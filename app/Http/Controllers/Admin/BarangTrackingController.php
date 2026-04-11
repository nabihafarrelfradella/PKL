<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class BarangTrackingController extends Controller
{
    public function index()
    {
        $data['title'] = 'Barang Tracking';
        return view('Admin.BarangTracking.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->select(
                    'tbl_barangmasuk.*',
                    'tbl_barang.barang_nama',
                    'tbl_barang.satuan_id',
                    'tbl_barang.barang_stok',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_jenisbarang.jenisbarang_ket'
                )
                ->orderBy('tbl_barangmasuk.bm_id', 'DESC');

            if ($request->filter_serial) {
                $query->where('tbl_barangmasuk.serial_number', 'LIKE', '%' . $request->filter_serial . '%');
            }
            if ($request->filter_kode) {
                $query->where('tbl_barangmasuk.kode_barang_unik', 'LIKE', '%' . $request->filter_kode . '%');
            }
            if ($request->filter_nama) {
                $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl_masuk', function ($row) {
                    return $row->jam_masuk
                        ? Carbon::parse($row->jam_masuk)->translatedFormat('d M Y H:i')
                        : ($row->bm_tanggal ? Carbon::parse($row->bm_tanggal)->translatedFormat('d M Y') : '-');
                })
                ->addColumn('tgl_keluar', function ($row) {
                    $keluar = BarangkeluarModel::where('barang_kode', $row->barang_kode)->latest('bk_id')->first();
                    if ($keluar) {
                        return $keluar->jam_keluar
                            ? Carbon::parse($keluar->jam_keluar)->translatedFormat('d M Y H:i')
                            : Carbon::parse($keluar->bk_tanggal)->translatedFormat('d M Y');
                    }
                    return '-';
                })
                ->addColumn('teknisi_ket', function ($row) {
                    $keluar = BarangkeluarModel::where('barang_kode', $row->barang_kode)->latest('bk_id')->first();
                    if ($keluar) {
                        $info = '';
                        if ($keluar->teknisi) $info .= 'Teknisi: ' . $keluar->teknisi;
                        if ($keluar->keterangan) $info .= ($info ? ' | ' : '') . $keluar->keterangan;
                        return $info ?: '-';
                    }
                    return '-';
                })
                ->addColumn('stok_real', function ($row) {
                    $masuk  = BarangmasukModel::where('barang_kode', $row->barang_kode)->sum('bm_jumlah');
                    $keluar = BarangkeluarModel::where('barang_kode', $row->barang_kode)->sum('bk_jumlah');
                    $stok   = ($row->barang_stok ?? 0) + $masuk - $keluar;
                    return $stok;
                })
                ->addColumn('qr_data', function ($row) {
                    // Build compact string to encode into QR
                    $lines = [];
                    $lines[] = 'Barang: '      . $row->barang_nama;
                    $lines[] = 'Kode: '        . ($row->kode_barang_unik ?? $row->bm_kode);
                    $lines[] = 'Serial: '      . ($row->serial_number ?? '-');
                    $lines[] = 'Satuan: '      . ($row->satuan_id ?? '-');
                    $lines[] = 'Tgl Masuk: '   . ($row->jam_masuk ?? $row->bm_tanggal ?? '-');
                    return implode("\n", $lines);
                })
                ->rawColumns(['tgl_masuk', 'tgl_keluar'])
                ->make(true);
        }
    }
}
