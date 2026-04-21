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
        Schema::create('tbl_barangmasuk', function (Blueprint $table) {
            $table->increments('bm_id');
            $table->string('bm_kode')->unique(); // Tambahkan unique agar kode tidak kembar
            $table->string('barang_kode');
            $table->integer('customer_id'); // Biasanya ID itu integer
            $table->date('bm_tanggal');     // Gunakan date untuk validasi tanggal
            $table->integer('bm_jumlah');   // Gunakan integer untuk angka
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
        Schema::dropIfExists('tbl_barangmasuk');
    }
};
