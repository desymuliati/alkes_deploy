<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStokMasukKeluarToBarangsTable extends Migration
{
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('stok_masuk')->nullable()->default(0)->after('stok_awal');
            $table->integer('stok_keluar')->nullable()->default(0)->after('stok_masuk');
        });
    }

    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('stok_masuk');
            $table->dropColumn('stok_keluar');
        });
    }
}