<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Http\Requests\PenjualanRequest; // Pastikan ini diimpor jika Anda punya
use Illuminate\Support\Facades\DB; // Pastikan ini diimpor

class PenjualanController extends Controller
{
    /**
     * Menampilkan daftar semua penjualan.
     * Memuat relasi 'barang' untuk tampilan.
     */
    public function index()
    {
        // Memuat relasi 'barang' dan paginasi
        $penjualans = Penjualan::with(['barang'])->latest()->paginate(10);
        return view('admin.penjualans.index', compact('penjualans'));
    }

    /**
     * Menampilkan form untuk membuat penjualan baru.
     * Mengirimkan daftar barang untuk dropdown.
     */
    public function create()
    {
        $barangs = Barang::all();
        return view('admin.penjualans.create', compact('barangs'));
    }

    /**
     * Menyimpan data penjualan baru.
     * Akan mengurangi stok_keluar dan jumlah_stok barang yang terjual.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'waktu_terjual' => 'required|date',
            'id_barang' => 'required|array|max:5',
            'id_barang.*' => 'required|exists:barangs,id',
            'jumlahTerjual' => 'required|array|max:5',
            'jumlahTerjual.*' => 'required|integer|min:1',
        ]);

        // Gunakan transaksi database untuk memastikan konsistensi
        DB::transaction(function () use ($request) {
            foreach ($request->id_barang as $index => $id_barang) {
                $barang = Barang::find($id_barang);

                if (!$barang) {
                    continue; // Harusnya tidak terjadi karena sudah divalidasi
                }

                $jumlahTerjual = $request->jumlahTerjual[$index];

                // Periksa apakah stok cukup sebelum mengurangi
                if ($barang->jumlah_stok < $jumlahTerjual) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlahTerjual.' . $index => "Stok produk {$barang->nama_produk} tidak cukup. Stok tersedia: {$barang->jumlah_stok}."
                    ]);
                }

                // Hitung harga total untuk item penjualan ini
                $hargaTotalItem = $jumlahTerjual * $barang->harga;

                // *** MODIFIKASI: Perbarui stok_keluar dan jumlah_stok di tabel barangs ***
                $barang->stok_keluar += $jumlahTerjual; // Tambahkan jumlah terjual ke stok_keluar
                $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
                // *** END MODIFIKASI ***

                // Tambahan logika otomatis:
                $barang->status = 'Keluar';
                $barang->keterangan = 'Barang Terjual';
                $barang->updated_at = now(); // Agar urutannya berubah berdasarkan perubahan terbaru

                // Simpan perubahan pada model Barang
                $barang->save();

                // Buat record penjualan
                Penjualan::create([
                    'waktu_terjual' => $request->waktu_terjual,
                    'id_barang' => $id_barang,
                    'jumlahTerjual' => $jumlahTerjual,
                    'hargaTotal' => $hargaTotalItem,
                ]);
            }
        });

        return redirect()->route('admin.penjualans.index')->with('success', 'Penjualan berhasil disimpan, stok barang diperbarui.');
    }

    /**
     * Menampilkan detail penjualan.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['barang']);
        $barangs = Barang::all();
        return view('admin.penjualans.show', compact('penjualan', 'barangs'));
    }

    /**
     * Menampilkan form edit penjualan.
     */
    public function edit(Penjualan $penjualan)
    {
        $barangs = Barang::all();
        return view('admin.penjualans.edit', compact('penjualan', 'barangs'));
    }

    /**
     * Memperbarui data penjualan.
     * Akan menyesuaikan stok_keluar dan jumlah_stok barang.
     */
    public function update(PenjualanRequest $request, Penjualan $penjualan)
    {
        $validatedData = $request->validated();

        // Simpan jumlahTerjual lama dan id_barang lama sebelum update
        $oldJumlahTerjual = $penjualan->jumlahTerjual;
        $oldIdBarang = $penjualan->id_barang;

        DB::transaction(function () use ($validatedData, $penjualan, $oldJumlahTerjual, $oldIdBarang) {
            $newIdBarang = $validatedData['id_barang'];
            $newJumlahTerjual = $validatedData['jumlahTerjual'];

            // *** MODIFIKASI: Menyesuaikan stok_keluar dan jumlah_stok di tabel barangs ***
            // Cek apakah id_barang berubah
            if ($oldIdBarang != $newIdBarang) {
                // 1a. Kembalikan stok_keluar dan jumlah_stok ke barang lama
                $oldBarang = Barang::find($oldIdBarang);
                if ($oldBarang) {
                    $oldBarang->stok_keluar -= $oldJumlahTerjual; // Kurangi kembali dari stok_keluar
                    $oldBarang->jumlah_stok = $oldBarang->stok_awal + $oldBarang->stok_masuk - $oldBarang->stok_keluar;
                    $oldBarang->save();
                }

                // 1b. Kurangi stok_keluar dan jumlah_stok dari barang baru
                $newBarang = Barang::find($newIdBarang);
                if (!$newBarang) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'id_barang' => 'Barang baru tidak ditemukan.'
                    ]);
                }
                // Periksa stok barang baru sebelum mengurangi
                if ($newBarang->jumlah_stok < $newJumlahTerjual) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlahTerjual' => "Stok produk {$newBarang->nama_produk} tidak cukup. Stok tersedia: {$newBarang->jumlah_stok}."
                    ]);
                }
                $newBarang->stok_keluar += $newJumlahTerjual; // Tambahkan ke stok_keluar
                $newBarang->jumlah_stok = $newBarang->stok_awal + $newBarang->stok_masuk - $newBarang->stok_keluar;
                $newBarang->save();

            } else { // id_barang tidak berubah, sesuaikan stok pada barang yang sama
                $barang = Barang::find($newIdBarang);
                if (!$barang) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'id_barang' => 'Barang tidak ditemukan.'
                    ]);
                }
                // Hitung selisih jumlah: (jumlahTerjual_baru - jumlahTerjual_lama)
                // Jika positif, stok_keluar bertambah dan jumlah_stok berkurang.
                // Jika negatif, stok_keluar berkurang dan jumlah_stok bertambah.
                $stokDifference = $newJumlahTerjual - $oldJumlahTerjual;

                // Periksa stok jika perubahan menyebabkan stok menjadi negatif
                if ($barang->jumlah_stok < $stokDifference) { // Jika mengurangi stok lebih dari yang ada
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'jumlahTerjual' => "Stok produk {$barang->nama_produk} tidak cukup untuk perubahan ini. Stok tersedia: {$barang->jumlah_stok}."
                    ]);
                }

                $barang->stok_keluar += $stokDifference; // Sesuaikan stok_keluar
                $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
                $barang->save();
            }
            // *** END MODIFIKASI ***

            // Cari barang yang relevan untuk perhitungan hargaTotal
            $barangForPrice = Barang::find($validatedData['id_barang']);
            $newInitialHargaTotal = $validatedData['jumlahTerjual'] * $barangForPrice->harga;

            // Update data penjualan
            $penjualan->update([
                'id_barang' => $validatedData['id_barang'],
                'jumlahTerjual' => $validatedData['jumlahTerjual'],
                'hargaTotal' => $newInitialHargaTotal,
                'waktu_terjual' => $validatedData['waktu_terjual'],
            ]);
        });

        return redirect()->route('admin.penjualans.show', $penjualan->id)
                            ->with('success', 'Detail Penjualan berhasil diperbarui dan stok disesuaikan.');
    }

    /**
     * Menghapus penjualan.
     * Akan mengembalikan stok_keluar dan jumlah_stok barang yang terjual.
     */
    public function destroy(Penjualan $penjualan)
    {
        DB::transaction(function () use ($penjualan) {
            // Dapatkan barang terkait
            $barang = Barang::find($penjualan->id_barang);

            // *** MODIFIKASI: Mengembalikan stok_keluar dan jumlah_stok di tabel barangs ***
            if ($barang) {
                $barang->stok_keluar -= $penjualan->jumlahTerjual; // Kurangi kembali dari stok_keluar
                $barang->jumlah_stok = $barang->stok_awal + $barang->stok_masuk - $barang->stok_keluar; // Hitung ulang total stok
                $barang->save(); // Simpan perubahan stok
            }
            // *** END MODIFIKASI ***

            // Hapus penjualan
            $penjualan->delete();
        });

        return redirect()->route('admin.penjualans.index')->with('success', 'Penjualan berhasil dihapus dan stok dikembalikan.');
    }
}