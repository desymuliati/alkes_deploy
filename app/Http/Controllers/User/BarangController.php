<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $threeMonthsFromNow = $now->copy()->addMonths(3);

        if ($request->ajax()) {
            $query = Barang::select('barangs.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_expired', function ($barang) use ($now, $threeMonthsFromNow) {
                    if (empty($barang->expired)) {
                        return '-';
                    }

                    $expiredDate = Carbon::parse($barang->expired);
                    $formattedDate = $expiredDate->format('d F Y');

                    if ($expiredDate->isPast()) {
                        return '<span class="font-bold text-red-600">' . $formattedDate . ' (Kadaluarsa)</span>';
                    } elseif ($expiredDate->between($now, $threeMonthsFromNow)) {
                        return '<span class="font-bold text-orange-500">' . $formattedDate . ' (Mendekati Kadaluarsa)</span>';
                    }

                    return $formattedDate;
                })
                ->addColumn('formatted_stok', function ($barang) {
                    $stok = $barang->jumlah_stok;
                    // Check for 'Galon' unit and specific low stock threshold
                    if (strtolower($barang->satuan) == 'galon') {
                        return $stok <= 100
                            ? '<span class="font-bold text-red-600">' . $stok . '</span>'
                            : $stok;
                    } else {
                        // General low stock threshold for other units
                        return $stok < 100
                            ? '<span class="font-bold text-red-600">' . $stok . '</span>'
                            : $stok;
                    }
                })
                ->editColumn('jumlah_stok', fn($barang) => $barang->jumlah_stok)
                ->editColumn('harga', fn($barang) => $barang->harga)
                ->editColumn('expired', fn($barang) => $barang->expired)
                ->addColumn('formatted_masuk', fn($barang) => $barang->stok_masuk ?? 0)
                ->addColumn('formatted_keluar', fn($barang) => $barang->stok_keluar ?? 0)
                ->addColumn('action', function ($barang) {
                    $showUrl = route('user.barangs.show', $barang->id);
                    return '<a href="' . $showUrl . '" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">Detail</a>';
                })
                ->rawColumns(['formatted_expired', 'formatted_stok', 'action'])
                ->make(true);
        }

        // Data untuk notifikasi dan info pada tampilan non-AJAX
        $stokRendahBarangs = Barang::where(function($query) {
            $query->where(function($q) {
                $q->where('satuan', 'Galon')
                  ->where('jumlah_stok', '<=', 100); // Galon <= 100
            })->orWhere(function($q) {
                $q->where('satuan', '<>', 'Galon')
                  ->where('jumlah_stok', '<', 100); // Others < 100
            });
        })->get();

        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where('expired', '<', $now) // Only truly expired items
            ->get();

        $mendekatiKadaluarsaBarangs = Barang::whereNotNull('expired')
            ->whereBetween('expired', [$now, $threeMonthsFromNow]) // Items approaching expiration
            ->get();


        // Jumlah masing-masing kategori
        $stokRendahCount = $stokRendahBarangs->count();
        $kadaluarsaCount = $kadaluarsaBarangs->count(); // Use count from the fetched collection
        $mendekatiKadaluarsaCount = $mendekatiKadaluarsaBarangs->count(); // Use count from the fetched collection

        return view('user.barangs.index', compact(
            'stokRendahBarangs',
            'kadaluarsaBarangs',
            'mendekatiKadaluarsaBarangs', // NEW: Pass the list of approaching expired items
            'stokRendahCount',
            'kadaluarsaCount',
            'mendekatiKadaluarsaCount'
        ));
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('user.barangs.show', compact('barang'));
    }
}