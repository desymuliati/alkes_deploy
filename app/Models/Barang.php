<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barangs';

    protected $fillable = [
        'nomor_produk_katalog',
        'nama_produk',
        'slug',
        'satuan',
        'stok_awal',
        'jumlah_stok',
        'harga',
        'expired',
        'status',
        'keterangan',
    ];

    protected $dates = ['expired'];
    
    protected $casts = [
        'expired' => 'datetime',
        'harga' => 'integer',
        'stok_awal' => 'integer',
        'jumlah_stok' => 'integer',
    ];

    // Relasi ke Penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'id_barang');
    }

    // Relasi ke Laporan Retur
    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'id_barang');
    }

    // Akses stok akhir (jumlah_stok)
    public function getJumlahStokAttribute()
    {
        $stokAwal = $this->stok_awal ?? 0;
        $totalKeluar = $this->penjualans()->sum('jumlahTerjual');
        $totalMasuk = $this->laporans()->sum('jumlah_retur');

        return $stokAwal - $totalKeluar + $totalMasuk;
    }

    // Jika ingin menampilkan harga dalam format rupiah secara otomatis
    public function getFormattedHargaAttribute()
    {
        return 'Rp. ' . number_format($this->harga, 0, ',', '.');
    }

    // Jika ingin menampilkan tanggal expired dalam format tertentu
    public function getFormattedExpiredAttribute()
    {
        return $this->expired->format('j F Y'); // misal: 8 Januari 2028
    }
}