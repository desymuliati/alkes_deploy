<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan; // DITAMBAHKAN: Untuk memanggil Artisan commands
use Illuminate\Support\Facades\Config; // DITAMBAHKAN: Untuk membaca config yang sudah ada
use Illuminate\Support\Facades\Log; // DITAMBAHKAN: Untuk logging error

class StockSettingController extends Controller
{
    public function index()
    {
        // Baca file konfigurasi. Gunakan default array jika file belum ada atau kosong.
        $settings = Config::get('stock_threshold', [
            'default_threshold' => 100,
            'unit_thresholds' => [],
            'product_thresholds' => [],
        ]);

        return view('admin.settings.stock', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'default_threshold' => 'required|integer|min:0',
            'unit_thresholds' => 'array',
            'unit_thresholds.*' => 'nullable|integer|min:0',
            // Jika Anda ingin mengelola product_thresholds dari UI, tambahkan validasinya di sini.
            // Jika tidak, pastikan tidak ada input form dengan nama ini agar tidak divalidasi.
        ]);

        // PENTING: Ambil pengaturan product_thresholds yang sudah ada
        // agar tidak hilang saat file ditulis ulang, karena form tidak mengelolanya.
        $currentSettings = Config::get('stock_threshold', [
            'product_thresholds' => [], // Default jika tidak ada
        ]);
        $validatedData['product_thresholds'] = $currentSettings['product_thresholds'] ?? [];


        // Simpan data ke file konfigurasi
        $path = config_path('stock_threshold.php');
        $content = "<?php\n\nreturn " . var_export($validatedData, true) . ";";

        // Pastikan file bisa ditulis
        try {
            File::put($path, $content);

            // PENTING: Bersihkan cache konfigurasi setelah file diupdate
            // agar perubahan langsung terbaca oleh aplikasi.
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            Log::error('Gagal menulis pengaturan stok: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan pengaturan. Pastikan folder "config" bisa ditulis. Detail: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Pengaturan stok berhasil diperbarui!');
    }
}