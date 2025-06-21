<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'laporans'; // Sesuaikan dengan nama tabel Anda

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'id_barang',
        'id_penjualan',
        'jumlah_retur',   // Kuantitas barang yang diretur
        'nilai_retur',    // Nilai moneter dari barang yang diretur
        'tanggal_retur',  // Tanggal terjadinya retur
    ];

    // Casting kolom ke tipe data tertentu
    protected $casts = [
        'tanggal_retur' => 'date',
        'nilai_retur' => 'decimal:2',
    ];

    /**
     * Relasi ke model Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    /**
     * Relasi ke model Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    /**
     * Event boot model untuk otomatisasi perhitungan
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($laporan) {
            // Jika nilai_retur belum diisi dan jumlah_retur ada
            if (is_null($laporan->nilai_retur) && $laporan->barang && $laporan->jumlah_retur !== null) {
                // Load relasi barang jika belum dimuat
                if (!$laporan->relationLoaded('barang')) {
                    $laporan->load('barang');
                }

                // Hitung nilai_retur berdasarkan harga barang dan jumlah_retur
                if ($laporan->barang && isset($laporan->barang->harga)) {
                    $laporan->nilai_retur = $laporan->jumlah_retur * $laporan->barang->harga;
                } else {
                    // Jika harga barang tidak ditemukan, set ke 0
                    $laporan->nilai_retur = 0;
                }
            }
        });
    }
}