<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use App\Models\Admin\JenisBarangModel;
use App\Models\Admin\MerkModel;
use App\Models\Admin\UserModel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data['title']    = 'Dashboard';

        // Total jenis barang
        $data['jenis']    = JenisBarangModel::count();

        // Total merk
        $data['merk']     = MerkModel::count();

        // Total item barang (jenis item)
        $data['barang']   = BarangModel::count();

        // Total barang masuk (semua transaksi masuk)
        $data['bm']       = BarangmasukModel::count();

        // Total barang keluar aktif berstatus Dipinjam
        $data['bk_dipinjam'] = BarangkeluarModel::where('bk_status', 'Dipinjam')->count();

        // Total semua transaksi keluar
        $data['bk']       = BarangkeluarModel::count();

        // Total user
        $data['user']     = UserModel::count();

        // Total teknisi (role_id = 3)
        $data['teknisi']  = UserModel::where('role_id', 3)->count();

        return view('Admin.Dashboard.index', $data);
    }

    public function cekResi(Request $request)
    {
        $resi = $request->resi;

        $masuk = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->where('kode_barang_unik', $resi)
            ->orWhere('tbl_barangmasuk.serial_number', $resi)
            ->orWhere('tbl_barangmasuk.bm_kode', $resi)
            ->select('tbl_barangmasuk.*', 'tbl_barang.barang_nama')
            ->get();

        $keluar = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->where('tbl_barangkeluar.serial_number', $resi)
            ->orWhere('tbl_barangkeluar.bk_kode', $resi)
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
            ->get();

        return response()->json([
            'masuk'  => $masuk,
            'keluar' => $keluar,
        ]);
    }
}
