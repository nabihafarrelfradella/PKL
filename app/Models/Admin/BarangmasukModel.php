<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangmasukModel extends Model
{
    use HasFactory;

    protected $table = "tbl_barangmasuk";
    protected $primaryKey = 'bm_id';

    // Isi fillable harus lengkap sesuai yang ada di proses_tambah controller
    protected $fillable = [
        'bm_kode',
        'barang_kode',
        'customer_id', // Pastikan di DB kolom ini bisa NULL atau ada default value
        'bm_tanggal',
        'bm_jumlah',
        'serial_number',
        'kode_barang_unik',
        'jam_masuk', 
    ];

    /**
     * Karena controller kamu menggunakan:
     * BarangmasukModel::whereDate('created_at', ...)
     * Maka timestamps HARUS bernilai true.
     */
    public $timestamps = true; 

    // Memastikan format tanggal dikenali sebagai objek Carbon
    protected $dates = ['bm_tanggal', 'jam_masuk', 'created_at', 'updated_at'];
}