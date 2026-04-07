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
        Schema::table('tbl_barang', function (Blueprint $row) {
            $row->enum('tipe_barang', ['Barang Kembali', 'Barang Habis Pakai'])->default('Barang Kembali')->after('barang_stok');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barang', function (Blueprint $row) {
            $row->dropColumn('tipe_barang');
        });
    }
};
