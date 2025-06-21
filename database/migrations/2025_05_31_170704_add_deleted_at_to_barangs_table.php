<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->softDeletes(); // Ini akan menambahkan kolom 'deleted_at'
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
            $table->dropSoftDeletes(); // Ini akan menghapus kolom 'deleted_at' jika di-rollback
        });
    }
};
