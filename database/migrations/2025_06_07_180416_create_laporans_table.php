<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporansTable extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id(); // Kolom ID utama untuk setiap transaksi retur
            $table->unsignedBigInteger('id_barang'); // Foreign key ke barang spesifik yang diretur (dari penjualan tersebut)
            $table->unsignedBigInteger('id_penjualan'); // Foreign key ke transaksi penjualan yang diretur
            $table->integer('jumlah_retur'); // Kuantitas barang yang diretur
            $table->decimal('nilai_retur', 10, 2)->default(0); // Nilai moneter dari barang yang diretur. Ini akan dihitung di aplikasi.
            $table->date('tanggal_retur'); // Tanggal terjadinya retur
            $table->timestamps(); // Kolom created_at dan updated_at

            $table->foreign('id_barang')->references('id')->on('barangs')->onDelete('cascade');

            $table->foreign('id_penjualan')->references('id')->on('penjualans')->onDelete('cascade');

            $table->unique(['id_barang', 'id_penjualan', 'tanggal_retur'], 'unique_return_per_sale_item_date');
        });
    }

    /**
     * Batalkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporans');
    }
}