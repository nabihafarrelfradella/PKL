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
        Schema::table('tbl_barangkeluar', function (Blueprint $row) {
            $row->enum('bk_status', ['Dipinjam', 'Selesai'])->default('Dipinjam')->after('bk_jumlah');
            $row->date('bk_tgl_kembali')->nullable()->after('bk_status');
            $row->string('bk_kondisi_kembali')->nullable()->after('bk_tgl_kembali');
            $row->integer('bk_jumlah_kembali')->nullable()->after('bk_kondisi_kembali');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_barangkeluar', function (Blueprint $row) {
            $row->dropColumn(['bk_status', 'bk_tgl_kembali', 'bk_kondisi_kembali', 'bk_jumlah_kembali']);
        });
    }
};
