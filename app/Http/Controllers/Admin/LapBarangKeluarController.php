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
                ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama');

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
                    ->select('tbl_barangkeluar.*', 'tbl_barang.barang_nama', 'tbl_barang.barang_id');

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
                    $tujuan = $row->bk_tujuan ?? '-';
                    if ($row->teknisi) {
                        $tujuan .= '<br><small class="text-muted">Teknisi: ' . $row->teknisi . '</small>';
                    }
                    return $tujuan;
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
                ->rawColumns(['tgl', 'tujuan', 'barang', 'status_badge', 'serial_number'])
                ->make(true);
        }
    }
}