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
        // Gunakan select agar kolom serial_number dari tbl_barangkeluar tidak tertimpa
        $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
                ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama', 'tbl_user.user_nmlengkap as user_nmlengkap');

        if ($request->tglawal) {
            $query->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir]);
        }

        $data['data'] = $query->orderBy('bk_id', 'DESC')->get();
        $data['title']    = 'Print Laporan Barang Keluar';
        $data['tglawal']  = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        return view('Admin.Laporan.BarangKeluar.print', $data);
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            // Tambahkan select('tbl_barangkeluar.*') untuk memastikan SN ditarik dari tabel transaksi
            $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
                    ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama', 'tbl_barang.barang_id', 'tbl_user.user_nmlengkap as user_nmlengkap');

            if ($request->tglawal != '') {
                $query->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir]);
            }

            $data = $query->orderBy('bk_id', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    return $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('tujuan', function ($row) {
                    return $row->bk_tujuan ?? '-';
                })
                ->addColumn('teknisi', function ($row) {
                    $nama = $row->user_nmlengkap ?? $row->teknisi_nama;
                    return $nama ? htmlspecialchars($nama) . ' (' . htmlspecialchars($row->teknisi) . ')' : ($row->teknisi ?? '-');
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_nama ?? '-';
                })
                ->addColumn('serial_number', function ($row) {
                    // Pastikan nama kolom di DB memang 'serial_number'
                    return $row->serial_number ?? '-';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->bk_status == 'Dipinjam') {
                        return '<span class="badge bg-warning">Dipinjam</span>';
                    }
                    return '<span class="badge bg-success">Selesai</span>';
                })
                // Tambahkan 'serial_number' ke rawColumns jika mengandung karakter khusus
                ->rawColumns(['tgl', 'tujuan', 'teknisi', 'barang', 'status_badge', 'serial_number'])
                ->make(true);
        }
    }

    public function pdf(Request $request)
    {
        $query = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
                ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama', 'tbl_user.user_nmlengkap as user_nmlengkap');

        if ($request->tglawal) {
            $query->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir]);
        }

        $data['data'] = $query->orderBy('bk_id', 'DESC')->get();
        $data['title']    = 'PDF Laporan Barang Keluar';
        $data['web']      = WebModel::first();
        $data['tglawal']  = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        
        $pdf = PDF::loadView('Admin.Laporan.BarangKeluar.pdf', $data);
        
        if ($request->tglawal) {
            return $pdf->download('lap-bk-'.$request->tglawal.'-'.$request->tglakhir.'.pdf');
        } else {
            return $pdf->download('lap-bk-semua-tanggal.pdf');
        }
    }
}