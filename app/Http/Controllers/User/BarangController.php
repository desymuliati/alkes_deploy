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
                    return $stok < 100
                        ? '<span class="font-bold text-red-600">' . $stok . '</span>'
                        : $stok;
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

        // Data untuk notifikasi dan info
        $stokRendahBarangs = Barang::where('jumlah_stok', '<', 100)->get();

        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where(function ($query) use ($now, $threeMonthsFromNow) {
                $query->where('expired', '<', $now)
                      ->orWhereBetween('expired', [$now, $threeMonthsFromNow]);
            })->get();

        // Jumlah masing-masing kategori
        $stokRendahCount = $stokRendahBarangs->count();
        $kadaluarsaCount = Barang::whereNotNull('expired')->where('expired', '<', $now)->count();
        $mendekatiKadaluarsaCount = Barang::whereNotNull('expired')
            ->whereBetween('expired', [$now, $threeMonthsFromNow])
            ->count();

        return view('user.barangs.index', compact(
            'stokRendahBarangs',
            'kadaluarsaBarangs',
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