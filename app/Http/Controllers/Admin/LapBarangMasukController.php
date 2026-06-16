<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\WebModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;

class LapBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $data["title"] = "Lap Barang Masuk";
        return view('Admin.Laporan.BarangMasuk.index', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->select([
                    'tbl_barangmasuk.bm_id',
                    'tbl_barangmasuk.bm_kode',
                    'tbl_barangmasuk.bm_tanggal',
                    'tbl_barangmasuk.bm_jumlah',
                    'tbl_barangmasuk.serial_number',
                    'tbl_barangmasuk.kode_barang_unik', // Sesuai SS database anda
                    'tbl_barangmasuk.barang_kode',
                    'tbl_barang.barang_nama'
                ]);

            if ($request->tglawal != '' && $request->tglakhir != '') {
                $query->whereBetween('tbl_barangmasuk.bm_tanggal', [$request->tglawal, $request->tglakhir]);
            }

            $data = $query->orderBy('tbl_barangmasuk.bm_id', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->bm_tanggal ? \Carbon\Carbon::parse($row->bm_tanggal)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_nama ?? '-';
                })
                ->addColumn('kode_unik', function ($row) {
                    return $row->kode_barang_unik ?? '-';
                })
                ->rawColumns(['tgl', 'barang'])
                ->make(true);
        }
    }

    public function print(Request $request)
    {
        $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->select([
                'tbl_barangmasuk.bm_id',
                'tbl_barangmasuk.bm_kode',
                'tbl_barangmasuk.bm_tanggal',
                'tbl_barangmasuk.bm_jumlah',
                'tbl_barangmasuk.serial_number',
                'tbl_barangmasuk.kode_barang_unik',
                'tbl_barangmasuk.barang_kode',
                'tbl_barang.barang_nama',
            ]);
        
        if ($request->tglawal) {
            $query->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir]);
        }
        
        $data['data'] = $query->orderBy('bm_id', 'DESC')->get();
        $data["title"] = "Print Barang Masuk";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        
        return view('Admin.Laporan.BarangMasuk.print', $data);
    }

    public function pdf(Request $request)
    {
        $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->select([
                'tbl_barangmasuk.bm_id',
                'tbl_barangmasuk.bm_kode',
                'tbl_barangmasuk.bm_tanggal',
                'tbl_barangmasuk.bm_jumlah',
                'tbl_barangmasuk.serial_number',
                'tbl_barangmasuk.kode_barang_unik',
                'tbl_barangmasuk.barang_kode',
                'tbl_barang.barang_nama',
            ]);
        
        if ($request->tglawal) {
            $query->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir]);
        }
        
        $data['data'] = $query->orderBy('bm_id', 'DESC')->get();
        $data["title"] = "PDF Barang Masuk";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        
        $pdf = PDF::loadView('Admin.Laporan.BarangMasuk.pdf', $data);
        
        if ($request->tglawal) {
            return $pdf->stream('lap-bm-'.$request->tglawal.'-'.$request->tglakhir.'.pdf');
        } else {
            return $pdf->stream('lap-bm-semua-tanggal.pdf');
        }
    }

    public function excel(Request $request)
    {
        $query = BarangmasukModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->select([
                'tbl_barangmasuk.bm_id',
                'tbl_barangmasuk.bm_kode',
                'tbl_barangmasuk.bm_tanggal',
                'tbl_barangmasuk.bm_jumlah',
                'tbl_barangmasuk.serial_number',
                'tbl_barangmasuk.kode_barang_unik',
                'tbl_barangmasuk.barang_kode',
                'tbl_barang.barang_nama',
            ]);
        
        if ($request->tglawal) {
            $query->whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir]);
        }
        
        $data['data'] = $query->orderBy('bm_id', 'DESC')->get();
        $data["title"] = "Excel Barang Masuk";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        
        return view('Admin.Laporan.BarangMasuk.excel', $data);
    }
}