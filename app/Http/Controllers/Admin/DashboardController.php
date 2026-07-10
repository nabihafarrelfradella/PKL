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
use Illuminate\Support\Facades\Session;
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

        $user = Session::get('user');

        if ($user && $user->role_id == 3) {
            // Jika teknisi, hitung hanya transaksi miliknya
            $data['bk_dipinjam'] = BarangkeluarModel::whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Kembali'])
                                                    ->where('teknisi', $user->teknisi_sn)
                                                    ->count();

            $data['bk']          = BarangkeluarModel::whereIn('bk_status', ['Dipinjam', 'Selesai', 'Menunggu Persetujuan Kembali'])
                                                    ->where('teknisi', $user->teknisi_sn)
                                                    ->count();

            $data['bk_menunggu'] = BarangkeluarModel::whereIn('bk_status', ['Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])
                                                    ->where('teknisi', $user->teknisi_sn)
                                                    ->count();
        } else {
            // Total barang keluar aktif berstatus Dipinjam
            $data['bk_dipinjam'] = BarangkeluarModel::whereIn('bk_status', ['Dipinjam', 'Menunggu Persetujuan Kembali'])->count();

            // Total semua transaksi keluar yang sudah disetujui
            $data['bk']          = BarangkeluarModel::whereIn('bk_status', ['Dipinjam', 'Selesai', 'Menunggu Persetujuan Kembali'])->count();

            // Total persetujuan yang menunggu aksi admin
            $data['bk_menunggu'] = BarangkeluarModel::whereIn('bk_status', ['Menunggu Persetujuan Pinjam', 'Menunggu Persetujuan Kembali'])->count();
        }

        // Total user
        $data['user']     = UserModel::count();

        // Total teknisi (role_id = 3)
        $data['teknisi']  = UserModel::where('role_id', 3)->count();

        // CHART 1: Barang Sering Dipinjam (Top 10)
        // Dihitung dari semua transaksi (histori) untuk Barang Kembali (Asset/Inventaris)
        $data['chartDipinjam'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('COUNT(tbl_barangkeluar.bk_id) as total'))
            ->where('tbl_jenisbarang.jenisbarang_nama', 'NOT LIKE', '%habis%')
            ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // CHART 2: Barang Paling Sering Habis (Top 10)
        // Dihitung dari total unit yang keluar untuk Barang Habis Pakai
        $data['chartHabis'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('SUM(tbl_barangkeluar.bk_jumlah) as total'))
            ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
            ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // CHART 3: Barang Sering Rusak (Top 10)
        // Dihitung dari kondisi kembali "Rusak Ringan" atau "Rusak Berat"
        $data['chartRusak'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('COUNT(tbl_barangkeluar.bk_id) as total'))
            ->whereIn('tbl_barangkeluar.bk_kondisi_kembali', ['Rusak Ringan', 'Rusak Berat'])
            ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

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
            ->where('tbl_barangkeluar.kode_barang_unik', $resi)
            ->orWhere('tbl_barangkeluar.serial_number', $resi)
            ->orWhere('tbl_barangkeluar.bk_kode', $resi)
            ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama')
            ->get();

        return response()->json([
            'masuk'  => $masuk,
            'keluar' => $keluar,
        ]);
    }

    public function getChartData(Request $request)
    {
        $filter = $request->filter ?? 'semua';
        $type = $request->type ?? 'semua';
        $now = now();
        $response = [];

        // Helper function (closure) untuk filter
        $applyFilter = function($query) use ($filter, $now) {
            if ($filter == 'hari') {
                $query->whereDate('tbl_barangkeluar.created_at', $now);
            } elseif ($filter == 'minggu') {
                $query->whereBetween('tbl_barangkeluar.created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]);
            } elseif ($filter == 'bulan') {
                $query->whereMonth('tbl_barangkeluar.created_at', $now->month)->whereYear('tbl_barangkeluar.created_at', $now->year);
            } elseif ($filter == 'tahun') {
                $query->whereYear('tbl_barangkeluar.created_at', $now->year);
            }
        };

        if ($type == 'dipinjam' || $type == 'semua') {
            $qDipinjam = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_jenisbarang.jenisbarang_nama', 'NOT LIKE', '%habis%');
            $applyFilter($qDipinjam);
            $response['chartDipinjam'] = $qDipinjam->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('COUNT(tbl_barangkeluar.bk_id) as total'))
                ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
                ->orderByDesc('total')->limit(10)->get();
        }

        if ($type == 'habis' || $type == 'semua') {
            $qHabis = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%');
            $applyFilter($qHabis);
            $response['chartHabis'] = $qHabis->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('SUM(tbl_barangkeluar.bk_jumlah) as total'))
                ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
                ->orderByDesc('total')->limit(10)->get();
        }

        if ($type == 'rusak' || $type == 'semua') {
            $qRusak = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->whereIn('tbl_barangkeluar.bk_kondisi_kembali', ['Rusak Ringan', 'Rusak Berat']);
            $applyFilter($qRusak);
            $response['chartRusak'] = $qRusak->select('tbl_barang.barang_nama', \Illuminate\Support\Facades\DB::raw('COUNT(tbl_barangkeluar.bk_id) as total'))
                ->groupBy('tbl_barangkeluar.barang_kode', 'tbl_barang.barang_nama')
                ->orderByDesc('total')->limit(10)->get();
        }

        return response()->json($response);
    }
}
