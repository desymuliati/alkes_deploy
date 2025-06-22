<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Http\Requests\BarangRequest;
use Carbon\Carbon;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $threeMonthsFromNow = Carbon::now()->addMonths(3);

        if ($request->ajax()) {
            $query = Barang::select('barangs.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_expired', function ($barang) use ($currentYear, $threeMonthsFromNow) {
                    if (empty($barang->expired)) {
                        return '-';
                    }

                    $expiredDate = Carbon::parse($barang->expired);
                    $formattedDate = $expiredDate->format('d F Y');

                    if ($expiredDate->isPast()) {
                        return '<span class="font-bold text-red-600">' . $formattedDate . ' (Kadaluarsa)</span>';
                    } elseif ($expiredDate->year === $currentYear && $expiredDate->lte($threeMonthsFromNow)) {
                        return '<span class="font-bold text-orange-500">' . $formattedDate . ' (Mendekati Kadaluarsa)</span>';
                    }

                    return $formattedDate;
                })
                ->addColumn('formatted_stok', function ($barang) {
                    return $barang->jumlah_stok < 100
                        ? '<span class="font-bold text-red-600">' . $barang->jumlah_stok . '</span>'
                        : $barang->jumlah_stok;
                })
                ->addColumn('formatted_masuk', function ($barang) {
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    return $barang->stok_keluar ?? 0;
                })
                ->addColumn('action', function ($barang) {
                    return '
                        <a href="' . route('admin.barangs.show', $barang->id) . '" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md text-xs font-semibold hover:bg-blue-700 mr-2">Detail</a>
                        <a href="' . route('admin.barangs.edit', $barang->id) . '" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md text-xs font-semibold hover:bg-yellow-700 mr-2">Sunting</a>
                        <form action="' . route('admin.barangs.destroy', $barang->id) . '" method="POST" class="inline-block" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus ' . addslashes($barang->nama_produk) . '?\');">
                            ' . method_field('delete') . csrf_field() . '
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700">Hapus</button>
                        </form>';
                })
                ->rawColumns(['action', 'formatted_expired', 'formatted_stok'])
                ->make(true);
        }

        $stokRendahBarangs = Barang::where('jumlah_stok', '<', 100)->get();

        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where(function ($query) use ($currentYear, $threeMonthsFromNow) {
                $query->where('expired', '<', Carbon::now())
                      ->orWhere(function ($q) use ($currentYear, $threeMonthsFromNow) {
                          $q->whereYear('expired', $currentYear)
                            ->where('expired', '<=', $threeMonthsFromNow);
                      });
            })
            ->get();

        return view('admin.barangs.index', compact('stokRendahBarangs', 'kadaluarsaBarangs'));
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

        $originalSlug = Str::slug($data['nama_produk']);
        $slug = $originalSlug;
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
            $count = 1;
            while (Barang::where('slug', $slug)->where('id', '!=', $barang->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        } else {
            unset($data['slug']);
        }

        $data['expired'] = $data['expired'] ?? null;
        $newStokAwal = $data['stok_awal'] ?? $barang->stok_awal;
        $newStokMasuk = $data['stok_masuk'] ?? $barang->stok_masuk;
        $currentStokKeluar = $barang->stok_keluar;
        unset($data['stok_keluar']);

        $data['jumlah_stok'] = $newStokAwal + $newStokMasuk - $currentStokKeluar;

        $barang->update($data);

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}