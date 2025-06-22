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
                    $stok = $barang->jumlah_stok;
                    return $stok < 100
                        ? '<span class="font-bold text-red-600">' . $stok . '</span>'
                        : $stok;
                })
                ->editColumn('jumlah_stok', function ($barang) {
                    return $barang->jumlah_stok;
                })
                ->editColumn('harga', function ($barang) {
                    return $barang->harga;
                })
                ->editColumn('expired', function ($barang) {
                    return $barang->expired;
                })
                ->addColumn('formatted_masuk', function ($barang) {
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    return $barang->stok_keluar ?? 0;
                })
                ->addColumn('action', function ($barang) {
                    $showUrl = route('user.barangs.show', $barang->id);
                    return '<a href="' . $showUrl . '" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">Detail</a>';
                })
                ->rawColumns(['formatted_expired', 'formatted_stok', 'action'])
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

        return view('user.barangs.index', compact('stokRendahBarangs', 'kadaluarsaBarangs'));
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        return view('user.barangs.show', compact('barang'));
    }
}