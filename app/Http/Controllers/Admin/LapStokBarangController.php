<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class LapStokBarangController extends Controller
{
    public function index(Request $request)
    {
        $data["title"] = "Lap Stok Barang";
        return view('Admin.Laporan.StokBarang.index', $data);
    }

    public function print(Request $request)
    {
        // Query dibersihkan dari filter tipe
        $query = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->orderBy('barang_id', 'DESC');
        
        $data['data'] = $query->get();

        $data["title"] = "Print Stok Barang";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        return view('Admin.Laporan.StokBarang.print', $data);
    }

    public function pdf(Request $request)
    {
        // Query dibersihkan dari filter tipe
        $query = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->orderBy('barang_id', 'DESC');
        
        $data['data'] = $query->get();

        $data["title"] = "PDF Stok Barang";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        $pdf = PDF::loadView('Admin.Laporan.StokBarang.pdf', $data);
        
        if($request->tglawal){
            return $pdf->stream('lap-stok-'.$request->tglawal.'-'.$request->tglakhir.'.pdf');
        } else {
            return $pdf->stream('lap-stok-semua-tanggal.pdf');
        }
    }

    public function excel(Request $request)
    {
        $query = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->orderBy('barang_id', 'DESC');
        
        $data['data'] = $query->get();
        $data["title"] = "Excel Stok Barang";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        return view('Admin.Laporan.StokBarang.excel', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangModel::leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->orderBy('barang_id', 'DESC');
            
            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                // Column 'tipe' sudah dihapus
                ->addColumn('stokawal', function ($row) {
                    return '<span>'.$row->barang_stok.'</span>';
                })
                ->addColumn('jmlmasuk', function ($row) use ($request) {
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::where('barang_kode', '=', $row->barang_kode)->sum('bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('barang_kode', '=', $row->barang_kode)
                            ->sum('bm_jumlah');
                    }
                    return '<span>'.$jmlmasuk.'</span>';
                })
                ->addColumn('jmlkeluar', function ($row) use ($request) {
                    if ($request->tglawal) {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    } else {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    }
                    $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                                   ->sum('tbl_barangkeluar.bk_jumlah');
                    return '<span>'.$jmlkeluar.'</span>';
                })
                ->addColumn('totalstok', function ($row) use ($request) {
                    // Hitung jmlmasuk untuk kalkulasi total
                    if ($request->tglawal == '') {
                        $jmlmasuk = BarangmasukModel::where('barang_kode', '=', $row->barang_kode)->sum('bm_jumlah');
                    } else {
                        $jmlmasuk = BarangmasukModel::whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('barang_kode', '=', $row->barang_kode)
                            ->sum('bm_jumlah');
                    }

                    // Hitung jmlkeluar untuk kalkulasi total
                    if ($request->tglawal) {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    } else {
                        $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                            ->where('tbl_barangkeluar.barang_kode', '=', $row->barang_kode);
                    }
                    $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                                   ->sum('tbl_barangkeluar.bk_jumlah');

                    $totalstok = $row->barang_stok + ($jmlmasuk - $jmlkeluar);
                    
                    if($totalstok == 0){
                        $result = '<span>'.$totalstok.'</span>';
                    }else if($totalstok > 0){
                        $result = '<span class="text-success">'.$totalstok.'</span>';
                    }else{
                        $result = '<span class="text-danger">'.$totalstok.'</span>';
                    }
                    
                    return $result;
                })
                ->rawColumns(['stokawal', 'jmlmasuk', 'jmlkeluar', 'totalstok']) // 'tipe' dihapus dari sini
                ->make(true);
        }
    }
}