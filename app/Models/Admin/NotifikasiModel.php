<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class NotifikasiModel extends Model
{
    protected $table      = 'tbl_notifikasi';
    protected $primaryKey = 'notif_id';
    protected $fillable   = [
        'notif_type',
        'notif_pesan',
        'notif_dari',
        'notif_nama_teknisi',
        'notif_barang',
        'notif_customer',
        'bk_id',
        'is_read_owner',
        'is_read_gudang',
    ];
}
