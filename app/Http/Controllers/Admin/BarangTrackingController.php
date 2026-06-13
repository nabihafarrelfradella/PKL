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
                // Join ke subquery untuk mendapatkan transaksi terakhir saja
                ->leftJoin(DB::raw('(SELECT * FROM tbl_barangkeluar WHERE bk_id IN (SELECT MAX(bk_id) FROM tbl_barangkeluar GROUP BY kode_barang_unik)) as tbl_barangkeluar'), function($join) {
                    $join->on('tbl_barangkeluar.kode_barang_unik', '=', 'tbl_barangmasuk.kode_barang_unik');
                })
                ->select(
                    'tbl_barangmasuk.*',
                    'tbl_barang.barang_nama',
                    'tbl_barang.satuan_id',
                    'tbl_barang.barang_stok',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_barangkeluar.jam_keluar as tgl_keluar_jam',
                    'tbl_barangkeluar.bk_tanggal as tgl_keluar_tgl',
                    'tbl_barangkeluar.teknisi as teknisi_sn_keluar',
                    'tbl_barangkeluar.teknisi_nama as nama_teknisi_keluar',
                    'tbl_barangkeluar.bk_tujuan as customer_keluar',
                    'tbl_barangkeluar.keterangan as ket_keluar',
                    'tbl_barangkeluar.bk_status'
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
                    if ($row->nama_teknisi_keluar) $info[] = 'Oleh: ' . htmlspecialchars($row->nama_teknisi_keluar);
                    if ($row->teknisi_sn_keluar) $info[] = 'ID Teknisi: ' . htmlspecialchars($row->teknisi_sn_keluar);
                    if ($row->customer_keluar) $info[] = 'Tujuan: ' . htmlspecialchars($row->customer_keluar);
                    if ($row->ket_keluar) $info[] = 'Ket: ' . htmlspecialchars($row->ket_keluar);
                    
                    if ($row->bk_status == 'Dipinjam') {
                        $status = '<br><span class="badge bg-warning mt-1"><i class="fe fe-clock me-1"></i>Sedang Dipinjam</span>';
                    } else if ($row->bk_status == 'Selesai') {
                        // Cek apakah ini barang habis pakai atau kembali
                        if (str_contains(strtolower($row->jenisbarang_nama), 'habis')) {
                            $status = '<br><span class="badge bg-danger mt-1"><i class="fe fe-x-circle me-1"></i>Habis Pakai</span>';
                        } else {
                            $status = '<br><span class="badge bg-success mt-1"><i class="fe fe-check-circle me-1"></i>Sudah Kembali</span>';
                        }
                    } else {
                        $status = '<br><span class="badge bg-primary mt-1"><i class="fe fe-box me-1"></i>Tersedia (In Stock)</span>';
                    }

                    return !empty($info) ? implode('<br>', $info) . $status : $status;
                })
                ->addColumn('stok_real', function ($row) {
                    // Jika barang dipinjam atau habis pakai, stok 0. Jika sudah kembali atau belum pernah keluar, stok 1.
                    if ($row->bk_status == 'Dipinjam') return 0;
                    if ($row->bk_status == 'Selesai' && str_contains(strtolower($row->jenisbarang_nama), 'habis')) return 0;
                    return 1;
                })
                ->addColumn('qr_data', function ($row) {
                    $lines = [
                        'Barang: '   . $row->barang_nama,
                        'Kode Unik: '. ($row->kode_barang_unik ?? $row->bm_kode),
                        'SN Barang: '. ($row->serial_number ?? '-'),
                        'Jenis: '    . ($row->jenisbarang_nama ?? '-'),
                    ];
                    return implode("\n", $lines);
                })
                ->rawColumns(['tgl_masuk', 'tgl_keluar', 'teknisi_ket'])
                ->make(true);
        }
    }
}