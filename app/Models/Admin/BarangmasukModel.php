<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangmasukModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_barangmasuk';
    protected $primaryKey = 'bm_id'; // Pastikan primary key-nya benar

    // Daftarkan semua kolom yang bisa diisi
    protected $fillable = [
        'bm_kode', 
        'barang_kode', 
        'bm_tanggal', 
        'bm_jumlah', 
        'serial_number', 
        'kode_barang_unik', 
        'jam_masuk',
        'customer_id' // <--- Tambahkan ini
    ];
}