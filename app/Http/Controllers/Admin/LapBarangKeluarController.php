<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\WebModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;

class LapBarangKeluarController extends Controller
{
    public function index()
    {
        $data['title'] = 'Lap Barang Keluar';
        return view('Admin.Laporan.BarangKeluar.index', $data);
    }

    public function print(Request $request)
    {
        if ($request->tglawal) {
            $data['data'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                ->orderBy('bk_id', 'DESC')
                ->get();
        } else {
            $data['data'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->orderBy('bk_id', 'DESC')
                ->get();
        }

        $data['title']    = 'Print Laporan Barang Keluar';
        $data['tglawal']  = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        return view('Admin.Laporan.BarangKeluar.print', $data);
    }

    public function pdf(Request $request)
    {
        if ($request->tglawal) {
            $data['data'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                ->orderBy('bk_id', 'DESC')
                ->get();
        } else {
            $data['data'] = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->orderBy('bk_id', 'DESC')
                ->get();
        }

        $data['title']    = 'PDF Laporan Barang Keluar';
        $data['tglawal']  = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        $pdf = PDF::loadView('Admin.Laporan.BarangKeluar.pdf', $data);

        if ($request->tglawal) {
            return $pdf->download('lap-bk-' . $request->tglawal . '-' . $request->tglakhir . '.pdf');
        } else {
            return $pdf->download('lap-bk-semua-tanggal.pdf');
        }
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            if ($request->tglawal == '') {
                $data = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->orderBy('bk_id', 'DESC')
                    ->get();
            } else {
                $data = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                    ->orderBy('bk_id', 'DESC')
                    ->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('tujuan', function ($row) {
                    $tujuan = $row->bk_tujuan ?? '-';
                    if ($row->teknisi) {
                        $tujuan .= '<br><small class="text-muted">Teknisi: ' . $row->teknisi . '</small>';
                    }
                    return $tujuan;
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_id == '' ? '-' : $row->barang_nama;
                })
                ->addColumn('serial_number', function ($row) {
                    return $row->serial_number ?? '-';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->bk_status == 'Dipinjam') {
                        return '<span class="badge bg-warning">Dipinjam</span>';
                    }
                    return '<span class="badge bg-success">Selesai</span>';
                })
                ->rawColumns(['tgl', 'tujuan', 'barang', 'status_badge'])
                ->make(true);
        }
    }
}
