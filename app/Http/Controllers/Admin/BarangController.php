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

class BarangController extends Controller
{
    // Fungsi utama index
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Barang::select('barangs.*', 'barangs.stok_masuk', 'barangs.stok_keluar', 'barangs.jumlah_stok');

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
                ->addColumn('formatted_stok', function ($barang) {
                    // Jika satuan galon dan stok < 1, warna merah
                    if (strtolower($barang->satuan) == 'galon') {
                        if ($barang->jumlah_stok < 1) {
                            return '<span class="text-red-600">' . $barang->jumlah_stok . '</span>';
                        } else {
                            return $barang->jumlah_stok;
                        }
                    } else {
                        // Untuk satuan lain, stok < 100 warna merah
                        if ($barang->jumlah_stok < 100) {
                            return '<span class="text-red-600">' . $barang->jumlah_stok . '</span>';
                        } else {
                            return $barang->jumlah_stok;
                        }
                    }
                })
                ->addColumn('formatted_masuk', function ($barang) {
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    return $barang->stok_keluar ?? 0;
                })
                ->addColumn('action', function ($barang) {
                    return '
                        <a class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2"
                            href="' . route('admin.barangs.show', $barang->id) . '">
                            Detail
                        </a>
                        <a class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2"
                            href="' . route('admin.barangs.edit', $barang->id) . '">
                            Sunting
                        </a>
                        <form class="inline-block" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ' . addslashes($barang->nama_produk) . '?\');" action="' . route('admin.barangs.destroy', $barang->id) . '" method="POST">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Hapus
                            </button>
                            ' . method_field('delete') . csrf_field() . '
                        </form>';
                })
                ->rawColumns(['action', 'formatted_expired', 'formatted_stok'])
                ->make(true);
        }

        // Ambil count dari cache, jika belum ada akan dihit dan disimpan
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

        $stokRendahCount = Cache::remember('stokRendahCount', 300, function () {
            return Barang::where('jumlah_stok', '<', 100)->count();
        });

        // Ambil daftar barang stok rendah
        $stokRendahBarangs = Barang::where('jumlah_stok', '<', 100)->get();

        return view('admin.barangs.index', compact(
            'kadaluarsaCount',
            'mendekatiKadaluarsaCount',
            'stokRendahCount',
            'stokRendahBarangs'
        ));
    }

    // Saat barang ditambah, diupdate, atau dihapus, cache akan di-refresh
    protected function refreshCounts()
    {
        Cache::forget('kadaluarsaCount');
        Cache::forget('mendekatiKadaluarsaCount');
        Cache::forget('stokRendahCount');

        // Hitung dan simpan kembali
        Cache::remember('kadaluarsaCount', 300, function () {
            return Barang::whereNotNull('expired')
                ->where('expired', '<', Carbon::now())
                ->count();
        });
        Cache::remember('mendekatiKadaluarsaCount', 300, function () {
            return Barang::whereNotNull('expired')
                ->whereBetween('expired', [Carbon::now(), Carbon::now()->addMonths(3)])
                ->count();
        });
        Cache::remember('stokRendahCount', 300, function () {
            return Barang::where('jumlah_stok', '<', 100)->count();
        });
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

        // Generate slug unik
        $originalSlug = Str::slug($data['nama_produk']);
        $slug = $originalSlug;
        $countSlug = 1;
        while (Barang::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $countSlug++;
        }
        $data['slug'] = $slug;

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        $data['stok_masuk'] = 0;
        $data['stok_keluar'] = 0;
        $data['jumlah_stok'] = $data['stok_awal'];

        // Simpan data
        $barang = Barang::create($data);

        // Refresh cache count setelah data baru disimpan
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

        // Jika nama produk berubah, update slug
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

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        // Hitung stok baru dan stok keluar
        $stok_awal = $barang->stok_awal ?? 0;
        $stok_masuk = $data['stok_masuk'] ?? 0;
        $stok_keluar = $barang->stok_keluar ?? 0;

        // Update jumlah stok
        $data['jumlah_stok'] = $stok_awal + $stok_masuk - $stok_keluar;

        // Update data
        $barang->update($data);

        // Refresh cache count setelah update
        $this->refreshCounts();

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        // Refresh cache count setelah delete
        $this->refreshCounts();

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}