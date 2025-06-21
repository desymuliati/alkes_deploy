<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Bagian ini akan dieksekusi jika permintaan adalah AJAX dari DataTables
        if ($request->ajax()) {
            // Mengambil semua barang dari database, diurutkan berdasarkan updated_at
            // Hapus ->limit(5); agar DataTables dapat menampilkan semua data dengan paging/sorting server-side.
            $query = Barang::orderBy('updated_at', 'desc');

            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom 'DT_RowIndex' untuk nomor urut
                // formatted_harga: Untuk menampilkan 'Rp. x.xxx'
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                // formatted_stok: Mengembalikan nilai stok mentah, agar JS bisa melakukan styling dan perbandingan
                // Kita juga perlu memastikan 'jumlah_stok' asli tersedia di row objek untuk kondisi JS
                ->addColumn('formatted_stok', function ($barang) {
                    return $barang->jumlah_stok; // Mengembalikan nilai mentah untuk di-handle di JS
                })
                // Jika ada kolom 'action' atau kolom lain yang menghasilkan HTML,
                // Anda harus menambahkan rawColumns di sini.
                // Contoh: ->rawColumns(['action'])
                ->make(true);
        }

        // Jika bukan permintaan AJAX, tampilkan view dashboard user
        return view('user.dashboard');
    }
}