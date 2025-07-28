<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Http\Requests\BarangRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\AppSetting;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Barang::select('barangs.*', 'barangs.stok_masuk', 'barangs.stok_keluar', 'barangs.jumlah_stok');

            // Ambil setting limit stok dari app_settings
            $stokLimits = AppSetting::where('setting_key', 'like', 'limit_stok_%')
                ->where('is_active', true)
                ->pluck('setting_value', 'unit');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_expired', function ($barang) {
                    if ($barang->expired) {
                        $expiredDate = Carbon::parse($barang->expired);
                        $formattedDate = $expiredDate->format('d F Y');

                        if ($expiredDate->isPast()) {
                            return '<span class="font-bold text-red-600">' . $formattedDate . ' (Kadaluarsa)</span>';
                        } elseif ($expiredDate->between(Carbon::now(), Carbon::now()->addMonths(3))) {
                            return '<span class="font-bold text-orange-500">' . $formattedDate . ' (Mendekati Kadaluarsa)</span>';
                        }
                        return $formattedDate;
                    }
                    return '-';
                })
                ->addColumn('formatted_stok', function ($barang) use ($stokLimits) {
                    $unit = strtolower($barang->satuan);
                    $limit = $stokLimits->get($unit, ($unit === 'galon' ? 1 : 100));

                    if ($barang->jumlah_stok < $limit) {
                        return '<span class="text-red-600">' . $barang->jumlah_stok . '</span>';
                    }
                    return $barang->jumlah_stok;
                })
                ->addColumn('formatted_masuk', function ($barang) {
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    return $barang->stok_keluar ?? 0;
                })
                ->addColumn('action', function ($barang) {
                    return view('admin.barangs._action', compact('barang'))->render();
                })
                ->rawColumns(['action', 'formatted_expired', 'formatted_stok'])
                ->make(true);
        }

        $kadaluarsaCount = Cache::remember('kadaluarsaCount', 300, function () {
            return Barang::whereNotNull('expired')
                ->where('expired', '<', Carbon::now())
                ->count();
        });

        $mendekatiKadaluarsaCount = Cache::remember('mendekatiKadaluarsaCount', 300, function () {
            return Barang::whereNotNull('expired')
                ->whereBetween('expired', [Carbon::now(), Carbon::now()->addMonths(3)])
                ->count();
        });

        $stokLimits = AppSetting::where('setting_key', 'like', 'limit_stok_%')
            ->where('is_active', true)
            ->pluck('setting_value', 'unit');

        $stokRendahCount = Cache::remember('stokRendahCount', 300, function () use ($stokLimits) {
            return Barang::all()->filter(function ($barang) use ($stokLimits) {
                $unit = strtolower($barang->satuan);
                $limit = $stokLimits->get($unit, ($unit === 'galon' ? 1 : 100));
                return $barang->jumlah_stok < $limit;
            })->count();
        });

        $stokRendahBarangs = Barang::all()->filter(function ($barang) use ($stokLimits) {
            $unit = strtolower($barang->satuan);
            $limit = $stokLimits->get($unit, ($unit === 'galon' ? 1 : 100));
            return $barang->jumlah_stok < $limit;
        });

        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where('expired', '<', Carbon::now())
            ->get();

        $mendekatiKadaluarsaBarangs = Barang::whereNotNull('expired')
            ->whereBetween('expired', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get();

        return view('admin.barangs.index', compact(
            'kadaluarsaCount',
            'mendekatiKadaluarsaCount',
            'stokRendahCount',
            'stokRendahBarangs',
            'kadaluarsaBarangs',
            'mendekatiKadaluarsaBarangs'
        ));
    }

    protected function refreshCounts()
    {
        Cache::forget('kadaluarsaCount');
        Cache::forget('mendekatiKadaluarsaCount');
        Cache::forget('stokRendahCount');
    }

    public function create()
    {
        $satuanOptions = ['Box', 'Pcs', 'Botol', 'Galon', 'Unit'];
        $statusOptions = ['Aktif', 'Nonaktif', 'Habis', 'Dalam Perjalanan'];
        return view('admin.barangs.create', compact('satuanOptions', 'statusOptions'));
    }

    public function store(BarangRequest $request)
    {
        $data = $request->validated();
        $slug = Str::slug($data['nama_produk']);
        $originalSlug = $slug;
        $count = 1;
        while (Barang::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        $data['expired'] = $data['expired'] ?? null;
        $data['stok_masuk'] = 0;
        $data['stok_keluar'] = 0;
        $data['jumlah_stok'] = $data['stok_awal'];

        Barang::create($data);

        $this->refreshCounts();

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('admin.barangs.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $satuanOptions = ['Box', 'Pcs', 'Botol', 'Galon', 'Unit'];
        $statusOptions = ['Aktif', 'Nonaktif', 'Habis', 'Dalam Perjalanan'];
        return view('admin.barangs.edit', compact('barang', 'satuanOptions', 'statusOptions'));
    }

    public function update(BarangRequest $request, Barang $barang)
    {
        $data = $request->validated();

        if ($request->filled('nama_produk') && $request->nama_produk !== $barang->nama_produk) {
            $originalSlug = Str::slug($data['nama_produk']);
            $slug = $originalSlug;
            $countSlug = 1;
            while (Barang::where('slug', $slug)->where('id', '!=', $barang->id)->exists()) {
                $slug = $originalSlug . '-' . $countSlug++;
            }
            $data['slug'] = $slug;
        } else {
            unset($data['slug']);
        }

        $data['expired'] = $data['expired'] ?? null;
        $stok_awal = $barang->stok_awal ?? 0;
        $stok_masuk = $data['stok_masuk'] ?? 0;
        $stok_keluar = $barang->stok_keluar ?? 0;
        $data['jumlah_stok'] = $stok_awal + $stok_masuk - $stok_keluar;

        $barang->update($data);

        $this->refreshCounts();

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        $this->refreshCounts();

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}