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

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang dengan perhitungan stok real-time dari database.
     */
    public function index(Request $request)
    {
        $currentYear = Carbon::now()->year;

        if ($request->ajax()) {
            // Ambil data langsung dari kolom stok_masuk, stok_keluar, dan jumlah_stok
            // yang sudah diupdate oleh controller Laporan dan Penjualan, serta dari update manual di BarangController.
            $query = Barang::select('barangs.*', 'barangs.stok_masuk', 'barangs.stok_keluar', 'barangs.jumlah_stok');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_expired', function ($barang) use ($currentYear) {
                    if ($barang->expired) {
                        $expiredDate = Carbon::parse($barang->expired);
                        $formattedDate = $expiredDate->format('d F Y');

                        if ($expiredDate->isPast()) {
                            return '<span class="font-bold text-red-600">' . $formattedDate . ' (Kadaluarsa)</span>';
                        } elseif ($expiredDate->year === $currentYear && $expiredDate->isFuture()) {
                            return '<span class="font-bold text-orange-500">' . $formattedDate . ' (Mendekati Kadaluarsa)</span>';
                        }
                        return $formattedDate;
                    }
                    return '-';
                })
                ->addColumn('formatted_stok', function ($barang) {
                    // Menggunakan jumlah_stok yang sudah terupdate di database
                    return $barang->jumlah_stok < 100
                        ? '<span class="font-bold text-red-600">' . $barang->jumlah_stok . '</span>'
                        : $barang->jumlah_stok;
                })
                ->addColumn('formatted_masuk', function ($barang) {
                    // Menggunakan stok_masuk yang sudah terupdate di database
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    // Menggunakan stok_keluar yang sudah terupdate di database
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

        $stokRendahBarangs = Barang::where('jumlah_stok', '<', 100)->get();

        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where(function ($query) use ($currentYear) {
                $query->whereYear('expired', '=', $currentYear)
                      ->orWhere('expired', '<', Carbon::now());
            })
            ->get();

        return view('admin.barangs.index', compact('stokRendahBarangs', 'kadaluarsaBarangs'));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        $satuanOptions = ['Box', 'Pcs', 'Botol', 'Galon', 'Unit'];
        $statusOptions = ['Aktif', 'Nonaktif', 'Habis', 'Dalam Perjalanan'];
        return view('admin.barangs.create', compact('satuanOptions', 'statusOptions'));
    }

    /**
     * Menyimpan barang baru ke database.
     * Stok masuk dan keluar diinisialisasi 0.
     * Jumlah stok awal barang dari stok_awal.
     */
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

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        // Inisialisasi stok_masuk dan stok_keluar menjadi 0.
        // Nilai ini akan diakumulasi dari transaksi Laporan (retur), Penjualan,
        // dan pembelian manual melalui form edit (untuk stok_masuk).
        $data['stok_masuk'] = 0;
        $data['stok_keluar'] = 0;
        // Jumlah stok awal barang hanya berdasarkan stok_awal yang dimasukkan.
        $data['jumlah_stok'] = $data['stok_awal'];

        Barang::create($data);

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail barang.
     */
    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('admin.barangs.show', compact('barang'));
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        $satuanOptions = ['Box', 'Pcs', 'Botol', 'Galon', 'Unit'];
        $statusOptions = ['Aktif', 'Nonaktif', 'Habis', 'Dalam Perjalanan'];
        return view('admin.barangs.edit', compact('barang', 'satuanOptions', 'statusOptions'));
    }

    /**
     * Memperbarui data barang di database.
     * Stok awal dan stok masuk (pembelian/tambahan) bisa diubah di sini.
     * Stok keluar tidak boleh diubah langsung.
     * Jumlah stok akan dihitung ulang berdasarkan perubahan ini.
     */
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

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        // Ambil nilai stok_awal dan stok_masuk dari request.
        // Jika tidak ada di request (misal tidak diisi di form), gunakan nilai lama dari database.
        $newStokAwal = $data['stok_awal'] ?? $barang->stok_awal;
        $newStokMasuk = $data['stok_masuk'] ?? $barang->stok_masuk;

        // Stok_keluar tidak boleh diubah langsung dari form ini.
        // Nilainya hanya boleh diupdate melalui PenjualanController.
        $currentStokKeluar = $barang->stok_keluar;
        unset($data['stok_keluar']); // Pastikan ini tidak dimasukkan ke dalam update()

        // Hitung ulang jumlah_stok berdasarkan stok_awal, stok_masuk (dari form/lama), dan stok_keluar (dari DB).
        $data['jumlah_stok'] = $newStokAwal + $newStokMasuk - $currentStokKeluar;

        $barang->update($data);

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}