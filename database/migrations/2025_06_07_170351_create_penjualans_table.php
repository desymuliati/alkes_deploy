<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenjualansTable extends Migration
{
    public function up()
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlahTerjual');
            $table->integer('hargaTotal')->nullable(); // Bisa null, dihitung otomatis
            $table->date('waktu_terjual');
            $table->timestamps();

            $table->foreign('id_barang')->references('id')->on('barangs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penjualans');
    }
}