<?php

namespace App\Http\Controllers\Admin; // Pastikan namespace ini sudah benar

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang; // Import model Barang
use App\Models\Penjualan; // <-- PENTING: Import model Penjualan Anda
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB; // Tetap diimpor karena kita pakai DB::raw()

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan tabel stok terbatas dan ringkasan penjualan.
     * Juga melayani permintaan AJAX untuk DataTables.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // --- Logika untuk DataTables (List Stock) ---
        // Bagian ini akan dieksekusi hanya jika permintaan datang dari DataTables (AJAX)
        if ($request->ajax()) {
            // Ambil hanya 5 barang terbaru berdasarkan updated_at
            $query = Barang::orderBy('updated_at', 'desc')->limit(5);

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom 'DT_RowIndex' untuk nomor urut
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_stok', function ($barang) {
                    // Hanya mengembalikan jumlah stok. Styling "stok rendah" akan dilakukan di sisi client/JS
                    return $barang->jumlah_stok;
                })
                // Menghapus 'formatted_expired' dan 'action' karena tidak akan ditampilkan di dashboard
                ->make(true);
        }

        // --- Logika untuk Pie Chart Penjualan (Mengambil data dari model Penjualan) ---

        $salesData = Penjualan::select(
                'barangs.nama_produk',
                DB::raw('SUM(penjualans.jumlahTerjual) as total_sold')
            )
            ->join('barangs', 'penjualans.id_barang', '=', 'barangs.id') // Join dengan tabel barangs
            ->groupBy('barangs.nama_produk')
            ->orderByDesc('total_sold')
            ->limit(5) // Ambil 5 barang terlaris
            ->pluck('total_sold', 'barangs.nama_produk') // Mengubah hasil menjadi array asosiatif (nama_produk => total_sold)
            ->toArray();


        // Mengembalikan view dashboard
        return view('admin.dashboard', compact('salesData'));
    }

    // Method-method CRUD barang lainnya tetap di BarangController atau dihapus dari sini
    // (create, store, show, edit, update, destroy)
}