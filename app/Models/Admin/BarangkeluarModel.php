<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class BarangkeluarModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_barangkeluar';
    protected $primaryKey = 'bk_id';

    // Pastikan semua kolom ini ada agar tidak error 'Mass Assignment'
    protected $fillable = [
        'bk_kode',
        'barang_kode',
        'kode_barang_unik',
        'bk_tanggal',
        'bk_tujuan',
        'bk_lokasi',
        'bk_map_url',
        'bk_lat',
        'bk_lng',
        'bk_jumlah',
        'bk_status', // 'Dipinjam' atau 'Selesai'
        'serial_number',
        'teknisi',
        'teknisi_nama',
        'keterangan',
        'jam_keluar',
        // Kolom khusus pengembalian
        'bk_tgl_kembali',
        'bk_kondisi_kembali',
        'bk_jumlah_kembali'
    ];
}