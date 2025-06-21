<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diotorisasi untuk membuat request ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Pastikan pengguna yang login memiliki izin yang sesuai untuk membuat/mengupdate retur.
        // auth()->check() hanya memastikan pengguna telah login.
        // Untuk kontrol akses yang lebih ketat, pertimbangkan untuk menggunakan Gates atau Policies.
        return auth()->check();
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_barang' => ['required', 'exists:barangs,id'], // ID barang yang diretur
            'id_penjualan' => ['required', 'exists:penjualans,id'], // ID penjualan asal retur
            'jumlah_retur' => ['required', 'integer', 'min:1'], // Kuantitas barang yang diretur, minimal 1
            'nilai_retur' => ['nullable', 'numeric', 'min:0'], // Nilai moneter retur (bisa dihitung otomatis oleh model)
            'tanggal_retur' => ['required', 'date'], // Tanggal terjadinya retur
        ];
    }

    /**
     * Dapatkan pesan error untuk aturan validasi yang telah ditentukan.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_barang.required' => 'Barang wajib dipilih untuk retur.',
            'id_barang.exists' => 'Barang yang dipilih untuk retur tidak valid.',
            'id_penjualan.required' => 'Penjualan asal retur wajib diisi.',
            'id_penjualan.exists' => 'Penjualan asal retur tidak valid.',
            'jumlah_retur.required' => 'Jumlah barang yang diretur wajib diisi.',
            'jumlah_retur.integer' => 'Jumlah retur harus berupa angka bulat.',
            'jumlah_retur.min' => 'Jumlah retur harus setidaknya 1.',
            'nilai_retur.numeric' => 'Nilai retur harus berupa angka.',
            'nilai_retur.min' => 'Nilai retur tidak boleh kurang dari 0.',
            'tanggal_retur.required' => 'Tanggal retur wajib diisi.',
            'tanggal_retur.date' => 'Tanggal retur harus berupa tanggal yang valid.',
        ];
    }
}