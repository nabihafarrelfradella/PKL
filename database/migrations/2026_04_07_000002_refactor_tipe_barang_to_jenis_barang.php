<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove the column from tbl_barang if it exists
        if (Schema::hasColumn('tbl_barang', 'tipe_barang')) {
            Schema::table('tbl_barang', function (Blueprint $row) {
                $row->dropColumn('tipe_barang');
            });
        }

        // Update tbl_jenisbarang: change jenisbarang_ket to ENUM using raw SQL
        DB::statement("ALTER TABLE tbl_jenisbarang MODIFY COLUMN jenisbarang_ket ENUM('Barang Kembali', 'Barang Habis Pakai') DEFAULT 'Barang Kembali'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE tbl_jenisbarang MODIFY COLUMN jenisbarang_ket TEXT NULL");

        Schema::table('tbl_barang', function (Blueprint $row) {
            $row->enum('tipe_barang', ['Barang Kembali', 'Barang Habis Pakai'])->default('Barang Kembali')->after('barang_stok');
        });
    }
};
