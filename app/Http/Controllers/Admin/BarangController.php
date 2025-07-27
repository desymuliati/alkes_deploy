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
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class BarangController extends Controller
{
    /**
     * Helper untuk mendapatkan ambang batas stok dinamis per barang.
     * Prioritas:
     * 1. product_thresholds (berdasarkan slug produk)
     * 2. unit_thresholds (berdasarkan satuan produk)
     * 3. default_threshold
     *
     * @param \App\Models\Barang $barang
     * @return int
     */
    private function getStockThreshold($barang)
    {
        // Ambil semua pengaturan dari file konfigurasi stock_threshold.php
        // Pastikan untuk memberikan array kosong sebagai default jika file config tidak ada
        $settings = Config::get('stock_threshold', [
            'default_threshold' => 100,
            'unit_thresholds' => [],
            'product_thresholds' => [],
        ]);

        // 1. Cek ambang batas spesifik per produk berdasarkan slug
        if (isset($settings['product_thresholds'][$barang->slug])) {
            return (int) $settings['product_thresholds'][$barang->slug];
        }

        // 2. Cek ambang batas berdasarkan satuan produk (case-insensitive)
        // Pastikan 'unit_thresholds' ada dan bukan null
        if (isset($settings['unit_thresholds']) && isset($settings['unit_thresholds'][strtolower($barang->satuan)])) {
            return (int) $settings['unit_thresholds'][strtolower($barang->satuan)];
        }

        // 3. Gunakan ambang batas default
        return (int) ($settings['default_threshold'] ?? 100);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Untuk DataTables, kita perlu semua kolom yang relevan untuk ditampilkan
            // dan juga 'satuan' dan 'slug' untuk perhitungan formatted_stok
            $query = Barang::select('barangs.*', 'barangs.stok_masuk', 'barangs.stok_keluar', 'barangs.jumlah_stok', 'barangs.satuan', 'barangs.slug');

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
                    // Menggunakan helper getStockThreshold untuk ambang batas dinamis
                    $threshold = $this->getStockThreshold($barang);
                    return $barang->jumlah_stok < $threshold
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

        // DITAMBAHKAN: Caching untuk statistik dashboard
        // Data akan di-cache selama 5 menit (300 detik)
        $cacheDuration = 300; // Durasi cache dalam detik (5 menit)

        // Cache untuk Barang Stok Rendah
        $stokRendahData = Cache::remember('admin_stok_rendah_data', $cacheDuration, function () {
            $allBarangsForDashboard = Barang::select('id', 'nama_produk', 'jumlah_stok', 'satuan', 'slug')->get();
            $stokRendahBarangs = $allBarangsForDashboard->filter(function ($barang) {
                return $barang->jumlah_stok < $this->getStockThreshold($barang);
            });
            return [
                'barangs' => $stokRendahBarangs,
                'count' => $stokRendahBarangs->count(),
            ];
        });
        $stokRendahBarangs = $stokRendahData['barangs'];
        $stokRendahCount = $stokRendahData['count'];

        // Cache untuk Barang Kadaluarsa
        $kadaluarsaData = Cache::remember('admin_kadaluarsa_data', $cacheDuration, function () {
            $kadaluarsaBarangs = Barang::select('id', 'nama_produk', 'expired')
                ->whereNotNull('expired')
                ->where('expired', '<', Carbon::now()->addMonths(3))
                ->get();
            return [
                'barangs' => $kadaluarsaBarangs,
                'count' => Barang::whereNotNull('expired')->where('expired', '<', Carbon::now())->count(),
                'mendekati_count' => Barang::whereNotNull('expired')->whereBetween('expired', [Carbon::now(), Carbon::now()->addMonths(3)])->count(),
            ];
        });
        $kadaluarsaBarangs = $kadaluarsaData['barangs'];
        $kadaluarsaCount = $kadaluarsaData['count'];
        $mendekatiKadaluarsaCount = $kadaluarsaData['mendekati_count'];

        return view('admin.barangs.index', compact(
            'stokRendahBarangs',
            'kadaluarsaBarangs',
            'stokRendahCount',
            'kadaluarsaCount',
            'mendekatiKadaluarsaCount'
        ));
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

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        $data['stok_masuk'] = 0;
        $data['stok_keluar'] = 0;
        $data['jumlah_stok'] = $data['stok_awal'];

        Barang::create($data);

        // DITAMBAHKAN: Hapus cache terkait setelah data barang diubah
        Cache::forget('admin_stok_rendah_data');
        Cache::forget('admin_kadaluarsa_data');

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

        if (empty($data['expired'])) {
            $data['expired'] = null;
        }

        $newStokAwal = $data['stok_awal'] ?? $barang->stok_awal;
        $newStokMasuk = $data['stok_masuk'] ?? $barang->stok_masuk;

        $currentStokKeluar = $barang->stok_keluar;
        unset($data['stok_keluar']);

        $data['jumlah_stok'] = $newStokAwal + $newStokMasuk - $currentStokKeluar;

        $barang->update($data);

        // DITAMBAHKAN: Hapus cache terkait setelah data barang diubah
        Cache::forget('admin_stok_rendah_data');
        Cache::forget('admin_kadaluarsa_data');

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil diperbarui!');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        // DITAMBAHKAN: Hapus cache terkait setelah data barang diubah
        Cache::forget('admin_stok_rendah_data');
        Cache::forget('admin_kadaluarsa_data');

        return redirect()->route('admin.barangs.index')->with('success', 'Barang berhasil dihapus.');
    }
}