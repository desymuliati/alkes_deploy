<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenjualanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan pengguna yang login memiliki izin; saat ini cukup cek login
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_barang' => ['required', 'exists:barangs,id'],
            'jumlahTerjual' => ['required', 'integer', 'min:1'], // Jumlah minimal 1
            'waktu_terjual' => ['required', 'date'],
            // 'hargaTotal' tidak perlu divalidasi karena otomatis dihitung di controller
        ];
    }

    /**
     * Get the error messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_barang.required' => 'Barang wajib dipilih.',
            'id_barang.exists' => 'Barang yang dipilih tidak valid.',
            'jumlahTerjual.required' => 'Jumlah terjual wajib diisi.',
            'jumlahTerjual.integer' => 'Jumlah terjual harus berupa angka bulat.',
            'jumlahTerjual.min' => 'Jumlah terjual minimal 1.',
            'waktu_terjual.required' => 'Waktu terjual wajib diisi.',
            'waktu_terjual.date' => 'Waktu terjual harus berupa tanggal yang valid.',
        ];
    }
}