<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Tambahkan kolom created_at dan updated_at
            // Jika tabel sudah ada, pastikan posisinya logis, misalnya setelah 'keterangan'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Hapus kolom created_at dan updated_at jika di-rollback
            $table->dropTimestamps();
        });
    }
}