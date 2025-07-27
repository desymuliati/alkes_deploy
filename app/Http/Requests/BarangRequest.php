<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
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
            'nama_produk' => ['required', 'string', 'max:255'], // Aturan dasar untuk nama_produk
            'satuan' => ['required', 'string', Rule::in(['Box', 'Pcs', 'Botol', 'Galon', 'Unit'])],
            'stok_awal' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'numeric', 'min:0'],
            'expired' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(['Masuk', 'Keluar'])],
            'keterangan' => ['nullable', 'string', 'max:1000'], // Menambahkan max:1000 untuk keterangan
        ];

        // Tambahkan aturan unique secara kondisional berdasarkan metode HTTP
        if ($this->isMethod('POST')) {
            // Saat membuat (POST), nomor produk katalog dan nama produk harus unik.
            $rules['nomor_produk_katalog'][] = 'unique:barangs,nomor_produk_katalog';
            $rules['nama_produk'][] = 'unique:barangs,nama_produk'; // DITAMBAHKAN: Aturan unique untuk nama_produk saat CREATE
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Saat memperbarui (PUT/PATCH), nomor produk katalog dan nama produk harus unik,
            // kecuali untuk barang yang sedang diedit (diabaikan berdasarkan ID barang).
            $rules['nomor_produk_katalog'][] = Rule::unique('barangs', 'nomor_produk_katalog')->ignore($this->route('barang'));
            $rules['nama_produk'][] = Rule::unique('barangs', 'nama_produk')->ignore($this->route('barang')); // DITAMBAHKAN: Aturan unique untuk nama_produk saat UPDATE
            $rules['stok_masuk'] = ['sometimes', 'integer', 'min:0']; // Aturan ini untuk 'stok_masuk' pada form edit.
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'nomor_produk_katalog.required' => 'Nomor Produk Katalog wajib diisi.',
            'nomor_produk_katalog.unique' => 'Nomor Produk Katalog ini sudah ada. Mohon gunakan nomor yang berbeda.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'nama_produk.unique' => 'Nama Produk ini sudah ada. Mohon gunakan nama yang berbeda.', // DITAMBAHKAN: Pesan untuk nama_produk unique
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Pilihan Satuan tidak valid.',
            'stok_awal.required' => 'Jumlah Stok Awal wajib diisi.',
            'stok_awal.integer' => 'Jumlah Stok Awal harus berupa angka.',
            'stok_awal.min' => 'Jumlah Stok Awal tidak boleh negatif.',
            'stok_masuk.integer' => 'Jumlah Stok Masuk harus berupa angka.',
            'stok_masuk.min' => 'Jumlah Stok Masuk tidak boleh negatif.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'expired.date' => 'Format Tanggal Expired tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Pilihan Status tidak valid.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 1000 karakter.', // Pesan untuk max length keterangan
            // 'slug.unique' => 'Slug sudah ada.', // Pesan ini tidak terhubung langsung dengan validasi di request ini, tapi tidak masalah jika ada.
        ];
    }
}