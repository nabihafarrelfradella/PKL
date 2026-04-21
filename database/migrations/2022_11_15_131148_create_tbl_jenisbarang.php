<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan tabel dibuat dengan kolom ENUM sejak awal
        Schema::create('tbl_jenisbarang', function (Blueprint $table) {
            $table->increments('jenisbarang_id');
            $table->string('jenisbarang_nama');
            $table->string('jenisbarang_slug');
            // Langsung set sebagai ENUM di sini agar kodingan lain tidak error
            $table->enum('jenisbarang_keterangan', ['Barang Kembali', 'Barang Habis Pakai'])
                  ->default('Barang Kembali');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_jenisbarang');
    }
};