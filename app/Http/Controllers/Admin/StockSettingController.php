<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StockSettingController extends Controller
{
    public function index()
    {
        // Baca file konfigurasi
        $settings = config('stock_threshold');

        // Jika file tidak ada, gunakan default
        if (is_null($settings)) {
            $settings = [
                'default_threshold' => 100,
                'unit_thresholds' => [],
                'product_thresholds' => [],
            ];
        }

        return view('admin.settings.stock', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'default_threshold' => 'required|integer|min:0',
            'unit_thresholds' => 'array',
            'unit_thresholds.*' => 'nullable|integer|min:0',
            // Tambahkan validasi untuk product_thresholds jika diperlukan
        ]);

        // Simpan data ke file konfigurasi
        $path = config_path('stock_threshold.php');
        $content = "<?php\n\nreturn " . var_export($validatedData, true) . ";";

        // Pastikan file bisa ditulis
        try {
            File::put($path, $content);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan. Pastikan folder "config" bisa ditulis.');
        }

        return redirect()->back()->with('success', 'Pengaturan stok berhasil diperbarui!');
    }
}