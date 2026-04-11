<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // tbl_barang updates
        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->text('serial_number')->nullable();
        });

        // Set invalid satuan to 'Unit'
        DB::table('tbl_barang')->whereNotIn('satuan_id', ['Meter', 'Pcs', 'Roll', 'Unit'])->update(['satuan_id' => 'Unit']);
        DB::statement("ALTER TABLE tbl_barang MODIFY COLUMN satuan_id ENUM('Meter', 'Pcs', 'Roll', 'Unit') DEFAULT 'Unit'");

        // tbl_barangmasuk updates
        Schema::table('tbl_barangmasuk', function (Blueprint $table) {
            $table->text('serial_number')->nullable();
            $table->text('kode_barang_unik')->nullable();
            $table->dateTime('jam_masuk')->nullable();
        });

        // tbl_barangkeluar updates
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->text('serial_number')->nullable();
            $table->dateTime('jam_keluar')->nullable();
            $table->string('teknisi')->nullable();
            $table->text('keterangan')->nullable();
        });
    }

    public function down()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'jam_keluar', 'teknisi', 'keterangan']);
        });

        Schema::table('tbl_barangmasuk', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'kode_barang_unik', 'jam_masuk']);
        });

        DB::statement("ALTER TABLE tbl_barang MODIFY COLUMN satuan_id ENUM('Meter', 'Qty', 'Pcs', 'Kg') DEFAULT 'Qty'");

        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->dropColumn('serial_number');
        });
    }
};
