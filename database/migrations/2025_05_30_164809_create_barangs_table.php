<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id(); // id_barang
            $table->string('nama_produk'); //
            $table->string('nomor_produk_katalog');
            $table->string('satuan');
            $table->integer('jumlah_stok'); // Stok            
            $table->integer('harga'); // Harga
            $table->timestamp('expired');
            $table->string('status');
            $table->string('keterangan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barangs');
    }
};