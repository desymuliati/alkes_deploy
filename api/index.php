<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Http\Request;

// Tangani request
$request = Request::capture();

// Periksa query parameter, misalnya ?action=data
if ($request->query('action') === 'data') {
    // Query data dari database
    // Pastikan kamu sudah menghubungkan database dan punya model 'Barang'
    $query = \App\Models\Barang::query();

    // Jika kamu pakai yajra datatables, bisa seperti ini:
    echo datatables()->of($query)
        ->addIndexColumn()
        ->addColumn('formatted_stok', function($row) {
            return number_format($row->jumlah_stok);
        })
        ->addColumn('formatted_harga', function($row) {
            return 'Rp ' . number_format($row->harga, 0, ',', '.');
        })
        ->addColumn('formatted_expired', function($row) {
            return \Carbon\Carbon::parse($row->expired)->format('d M Y');
        })
        ->addColumn('action', function($row) {
            return '<a href="/edit/'.$row->id.'" class="btn btn-sm btn-primary">Edit</a>';
        })
        ->rawColumns(['action'])
        ->toJson();
    exit;
}

// Jika bukan permintaan data, proses request normal
$response = $app->make(Illuminate\Contracts\Http\Kernel::class)->handle($request);
$response->send();
$kernel->terminate($request, $response);