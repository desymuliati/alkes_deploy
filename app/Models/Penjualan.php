<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'id_barang',
        'jumlahTerjual',
        'hargaTotal',
        'waktu_terjual',
    ];

    protected $casts = [
        'waktu_terjual' => 'date',
    ];

    /**
     * Relasi ke Barang.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    /**
     * Menghitung hargaTotal otomatis saat menyimpan.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($penjualan) {
            // Pastikan relasi barang sudah dimuat untuk mengakses harga
            if (!$penjualan->relationLoaded('barang') && $penjualan->id_barang) {
                $penjualan->load('barang');
            }

            // Validasi bahwa jumlahTerjual adalah numeric dan positif
            if (!isset($penjualan->jumlahTerjual) || !is_numeric($penjualan->jumlahTerjual) || $penjualan->jumlahTerjual <= 0) {
                $penjualan->jumlahTerjual = 0;
            }

            // Pastikan relasi barang ada dan memiliki field 'harga'
            if ($penjualan->barang && isset($penjualan->barang->harga)) {
                $hargaSatuan = $penjualan->barang->harga; // Pastikan field 'harga' ada di tabel 'barangs'
                // Hitung hargaTotal sebagai harga satuan * jumlah terjual
                $penjualan->hargaTotal = $hargaSatuan * $penjualan->jumlahTerjual;
            } else {
                // Jika data barang tidak ditemukan atau tidak ada field 'harga'
                $penjualan->hargaTotal = 0;
            }
        });
    }
}