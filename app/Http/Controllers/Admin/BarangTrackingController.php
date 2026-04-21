<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangkeluarModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

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
            // Menggunakan Query Builder agar lebih ringan
            $query = DB::table('tbl_barangmasuk')
                ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                // Join ke tabel keluar untuk mendapatkan data transaksi terakhir barang tersebut
                ->leftJoin('tbl_barangkeluar', function($join) {
                    $join->on('tbl_barangkeluar.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                         ->whereRaw('tbl_barangkeluar.bk_id = (SELECT MAX(bk_id) FROM tbl_barangkeluar WHERE tbl_barangkeluar.barang_kode = tbl_barangmasuk.barang_kode)');
                })
                ->select(
                    'tbl_barangmasuk.*',
                    'tbl_barang.barang_nama',
                    'tbl_barang.satuan_id',
                    'tbl_barang.barang_stok',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_barangkeluar.jam_keluar as tgl_keluar_jam',
                    'tbl_barangkeluar.bk_tanggal as tgl_keluar_tgl',
                    'tbl_barangkeluar.teknisi as teknisi_keluar',
                    'tbl_barangkeluar.keterangan as ket_keluar'
                )
                ->orderBy('tbl_barangmasuk.bm_id', 'DESC');

            // Logika Filter
            if ($request->filter_serial) {
                $query->where('tbl_barangmasuk.serial_number', 'LIKE', '%' . $request->filter_serial . '%');
            }
            if ($request->filter_kode) {
                $query->where('tbl_barangmasuk.kode_barang_unik', 'LIKE', '%' . $request->filter_kode . '%');
            }
            if ($request->filter_nama) {
                $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tgl_masuk', function ($row) {
                    if ($row->jam_masuk) {
                        return Carbon::parse($row->jam_masuk)->translatedFormat('d M Y H:i');
                    }
                    return $row->bm_tanggal ? Carbon::parse($row->bm_tanggal)->translatedFormat('d M Y') : '-';
                })
                ->addColumn('tgl_keluar', function ($row) {
                    if ($row->tgl_keluar_jam) {
                        return Carbon::parse($row->tgl_keluar_jam)->translatedFormat('d M Y H:i');
                    }
                    if ($row->tgl_keluar_tgl) {
                        return Carbon::parse($row->tgl_keluar_tgl)->translatedFormat('d M Y');
                    }
                    return '-';
                })
                ->addColumn('teknisi_ket', function ($row) {
                    $info = [];
                    if ($row->teknisi_keluar) $info[] = 'Teknisi: ' . $row->teknisi_keluar;
                    if ($row->ket_keluar) $info[] = $row->ket_keluar;
                    
                    return !empty($info) ? implode(' | ', $info) : '-';
                })
                ->addColumn('stok_real', function ($row) {
                    // Mengambil stok master barang
                    return $row->barang_stok ?? 0;
                })
                ->addColumn('qr_data', function ($row) {
                    $lines = [
                        'Barang: '   . $row->barang_nama,
                        'Kode: '     . ($row->kode_barang_unik ?? $row->bm_kode),
                        'Serial: '   . ($row->serial_number ?? '-'),
                        'Satuan: '   . ($row->satuan_id ?? '-'),
                        'Tgl Masuk: '. ($row->jam_masuk ?? $row->bm_tanggal ?? '-')
                    ];
                    return implode("\n", $lines);
                })
                ->rawColumns(['tgl_masuk', 'tgl_keluar'])
                ->make(true);
        }
    }
}