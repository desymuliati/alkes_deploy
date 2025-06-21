<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// app/Http/Requests/BarangRequest.php

class BarangRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'nomor_produk_katalog' => ['required', 'string', 'max:255'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', Rule::in(['Box', 'Pcs', 'Botol', 'Galon', 'Unit'])],
            'stok_awal' => ['required', 'integer', 'min:0'], // <-- UBAH INI: dari 'jumlah_stok' jadi 'stok_awal'
            'harga' => ['required', 'numeric', 'min:0'],
            'expired' => ['nullable', 'date'],
            // 'status' di form Anda sekarang adalah 'Masuk'/'Keluar'.
            // Apakah 'status' di model Barang memang untuk status 'Masuk'/'Keluar' (jenis transaksi)
            // atau status item itu sendiri (Aktif/Nonaktif/Habis/etc.)?
            // Jika untuk transaksi, ini kurang tepat disimpan di model Barang utama.
            // Jika untuk status Barang, maka pilihan di form harus Aktif/Nonaktif dst.
            // Untuk sementara, saya akan mengikuti pilihan 'Masuk'/'Keluar' di form Anda,
            // tetapi ini mungkin perlu dipertimbangkan ulang arsitektur datanya.
            'status' => ['required', 'string', Rule::in(['Masuk', 'Keluar'])],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];

        // Tambahkan aturan unique untuk nomor_produk_katalog hanya saat membuat
        if ($this->isMethod('POST')) {
            // Saat membuat (POST), nomor produk katalog harus unik di tabel 'barangs'.
            $rules['nomor_produk_katalog'][] = 'unique:barangs,nomor_produk_katalog';
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Saat memperbarui (PUT/PATCH), nomor produk katalog harus unik,
            // kecuali untuk barang yang sedang diedit (diabaikan berdasarkan ID barang).
            $rules['nomor_produk_katalog'][] = Rule::unique('barangs', 'nomor_produk_katalog')->ignore($this->route('barang'));
        }

        // Aturan untuk 'stok_masuk' saat memperbarui (PUT/PATCH).
        // Ini diasumsikan bahwa form edit 'Barang' akan mengirimkan
        // nilai TOTAL terbaru untuk stok_masuk (termasuk pembelian manual).
        // Jika tidak ada di request, controller akan menggunakan nilai lama dari DB.
        // Jika ada, akan divalidasi sebagai integer non-negatif.
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['stok_masuk'] = ['sometimes', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nomor_produk_katalog.required' => 'Nomor Produk Katalog wajib diisi.',
            'nomor_produk_katalog.unique' => 'Nomor Produk Katalog ini sudah ada.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Pilihan Satuan tidak valid.',
            'stok_awal.required' => 'Jumlah Stok Awal wajib diisi.', // <-- Tambahkan pesan ini
            'stok_awal.integer' => 'Jumlah Stok Awal harus berupa angka.', // <-- Tambahkan pesan ini
            'stok_awal.min' => 'Jumlah Stok Awal tidak boleh negatif.', // <-- Tambahkan pesan ini
            'stok_masuk.integer' => 'Jumlah Stok Masuk harus berupa angka.',
            'stok_masuk.min' => 'Jumlah Stok Masuk tidak boleh negatif.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'expired.date' => 'Format Tanggal Expired tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Pilihan Status tidak valid.',
            'slug.unique' => 'Slug sudah ada.', // <-- Tambahkan pesan ini
            // ... pesan lainnya
        ];
    }
}