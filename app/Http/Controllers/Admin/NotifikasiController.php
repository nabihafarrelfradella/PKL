<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\NotifikasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class NotifikasiController extends Controller
{
    /**
     * Ambil notifikasi yang belum dibaca untuk user yang sedang login.
     * Owner (role 1) → is_read_owner = 0
     * Admin Gudang (role 2) → is_read_gudang = 0
     */
    public function getNotifikasi(Request $request)
    {
        $user   = Session::get('user');
        $roleId = $user->role_id ?? 0;

        $query = NotifikasiModel::orderBy('created_at', 'DESC');

        if ($roleId == 1) {
            $query->where('is_read_owner', 0);
        } elseif ($roleId == 2) {
            $query->where('is_read_gudang', 0);
        } else {
            return response()->json(['total' => 0, 'items' => []]);
        }

        $notifs = $query->limit(20)->get();
        $total  = $notifs->count();

        $items = $notifs->map(function ($n) {
            $icon  = $n->notif_type === 'peminjaman' ? 'fe-arrow-up-circle' : 'fe-corner-up-left';
            $color = $n->notif_type === 'peminjaman' ? 'warning' : 'success';
            return [
                'notif_id'       => $n->notif_id,
                'notif_type'     => $n->notif_type,
                'notif_pesan'    => $n->notif_pesan,
                'notif_barang'   => $n->notif_barang,
                'notif_customer' => $n->notif_customer,
                'teknisi'        => $n->notif_nama_teknisi,
                'bk_id'          => $n->bk_id,
                'waktu'          => $n->created_at ? \Carbon\Carbon::parse($n->created_at)->diffForHumans() : '-',
                'icon'           => $icon,
                'color'          => $color,
            ];
        });

        return response()->json(['total' => $total, 'items' => $items]);
    }

    /**
     * Tandai 1 notifikasi sudah dibaca.
     */
    public function markRead($id)
    {
        $user   = Session::get('user');
        $roleId = $user->role_id ?? 0;
        $notif  = NotifikasiModel::find($id);

        if (!$notif) {
            return response()->json(['error' => 'Tidak ditemukan'], 404);
        }

        if ($roleId == 1) {
            $notif->update(['is_read_owner' => 1]);
        } elseif ($roleId == 2) {
            $notif->update(['is_read_gudang' => 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sudah dibaca.
     */
    public function markAllRead()
    {
        $user   = Session::get('user');
        $roleId = $user->role_id ?? 0;

        if ($roleId == 1) {
            NotifikasiModel::where('is_read_owner', 0)->update(['is_read_owner' => 1]);
        } elseif ($roleId == 2) {
            NotifikasiModel::where('is_read_gudang', 0)->update(['is_read_gudang' => 1]);
        }

        return response()->json(['success' => true]);
    }
}
