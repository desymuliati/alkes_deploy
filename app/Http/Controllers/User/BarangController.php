<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
// No need for Illuminate\Support\Facades\DB if not performing complex joins/aggregations here

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // Get current year for expired checks
        $currentYear = Carbon::now()->year;
        $threeMonthsFromNow = Carbon::now()->addMonths(3);

        if ($request->ajax()) {
            // Select all columns from the 'barangs' table.
            // Yajra DataTables will handle pagination, filtering, and sorting automatically
            // based on the columns defined in your JavaScript.
            $query = Barang::select('barangs.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('formatted_harga', function ($barang) {
                    return 'Rp ' . number_format($barang->harga, 0, ',', '.');
                })
                ->addColumn('formatted_expired', function ($barang) use ($currentYear, $threeMonthsFromNow) {
                    if (empty($barang->expired)) {
                        return '-'; // Handle null/empty expired dates gracefully
                    }

                    $expiredDate = Carbon::parse($barang->expired);
                    $formattedDate = $expiredDate->format('d F Y'); // Format: 01 Januari 2025

                    if ($expiredDate->isPast()) {
                        return '<span class="font-bold text-red-600">' . $formattedDate . ' (Kadaluarsa)</span>';
                    } elseif ($expiredDate->year === $currentYear && $expiredDate->lte($threeMonthsFromNow)) {
                        // Check if it's in the current year and within 3 months from now
                        return '<span class="font-bold text-orange-500">' . $formattedDate . ' (Mendekati Kadaluarsa)</span>';
                    }

                    return $formattedDate;
                })
                ->addColumn('formatted_stok', function ($barang) {
                    $stok = $barang->jumlah_stok;
                    // Returning HTML directly from controller for DataTables to render
                    return $stok < 100
                        ? '<span class="font-bold text-red-600">' . $stok . '</span>'
                        : $stok;
                })
                // Use 'jumlah_stok' directly for sorting/searching if DataTables config matches
                ->editColumn('jumlah_stok', function ($barang) {
                    return $barang->jumlah_stok; // Ensure raw numeric value is available for sorting/filtering
                })
                ->editColumn('harga', function ($barang) {
                    return $barang->harga; // Ensure raw numeric value is available for sorting/filtering
                })
                ->editColumn('expired', function ($barang) {
                    return $barang->expired; // Ensure raw date value is available for sorting/filtering
                })
                // Use default values for stok_masuk and stok_keluar if they are null
                ->addColumn('formatted_masuk', function ($barang) {
                    return $barang->stok_masuk ?? 0;
                })
                ->addColumn('formatted_keluar', function ($barang) {
                    return $barang->stok_keluar ?? 0;
                })
                // Add action column with buttons (assuming you have routes for show/edit/delete)
                ->addColumn('action', function ($barang) {
                    $showUrl = route('user.barangs.show', $barang->id); // Assuming 'user.barangs.show' route
                    // You might need edit/delete actions if this is a management page
                    // $editUrl = route('user.barangs.edit', $barang->id);
                    // $deleteUrl = route('user.barangs.destroy', $barang->id);

                    $buttons = '<a href="' . $showUrl . '" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">Detail</a>';
                    // Example for edit/delete if needed:
                    // $buttons .= '<a href="'.$editUrl.'" class="inline-flex items-center px-4 py-2 bg-green-500 ...">Edit</a>';
                    // $buttons .= '<form action="'.$deleteUrl.'" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure?\');">'.csrf_field().method_field('DELETE').'<button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 ...">Delete</button></form>';

                    return $buttons;
                })
                // Specify columns that contain HTML and should not be escaped
                ->rawColumns(['formatted_expired', 'formatted_stok', 'action'])
                ->make(true);
        }

        // Data for low stock and expired notifications outside DataTables AJAX
        $stokRendahBarangs = Barang::where('jumlah_stok', '<', 100)->get();

        // Optimized query for expired/approaching expiry.
        // It now correctly checks for expired dates (past) OR dates in the current year that are within 3 months future.
        $kadaluarsaBarangs = Barang::whereNotNull('expired')
            ->where(function ($query) use ($currentYear, $threeMonthsFromNow) {
                // Already expired (any year)
                $query->where('expired', '<', Carbon::now())
                      // OR (in current year AND within next 3 months)
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