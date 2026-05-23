<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->string('kode_barang_unik')->nullable()->after('barang_kode');
        });
    }

    public function down()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $table) {
            $table->dropColumn('kode_barang_unik');
        });
    }
};
