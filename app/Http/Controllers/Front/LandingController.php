<?php

namespace App\Http\Controllers\Front;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LandingController extends Controller
{
    public function index()
    {
        $barangs = Barang::with(['penjualans', 'laporans'])->latest()->take(4)->get()->reverse();

        return view('landing', [
            'barangs' => $barangs
        ]);
    }
}