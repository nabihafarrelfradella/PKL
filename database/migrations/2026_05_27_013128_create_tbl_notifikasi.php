<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_notifikasi', function (Blueprint $table) {
            $table->id('notif_id');
            $table->enum('notif_type', ['peminjaman', 'pengembalian', 'habis_pakai'])
                  ->default('peminjaman');
            $table->text('notif_pesan');
            $table->unsignedInteger('notif_dari');   // user_id teknisi
            $table->string('notif_nama_teknisi');     // nama lengkap teknisi
            $table->string('notif_barang');           // nama barang
            $table->string('notif_customer')->nullable(); // nama customer / lokasi
            $table->unsignedBigInteger('bk_id')->nullable(); // FK ke tbl_barangkeluar
            $table->boolean('is_read_owner')->default(false);
            $table->boolean('is_read_gudang')->default(false);
            $table->timestamps();

            $table->index('bk_id');
            $table->index(['is_read_owner', 'created_at']);
            $table->index(['is_read_gudang', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_notifikasi');
    }
};
