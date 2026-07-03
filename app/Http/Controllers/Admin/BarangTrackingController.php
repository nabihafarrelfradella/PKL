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
                // Join ke subquery untuk mendapatkan transaksi terakhir saja beserta nama teknisi aslinya
                ->leftJoin(DB::raw('(SELECT tbl_barangkeluar.*, tbl_user.user_nmlengkap as user_nmlengkap_user FROM tbl_barangkeluar LEFT JOIN tbl_user ON tbl_user.teknisi_sn = tbl_barangkeluar.teknisi WHERE tbl_barangkeluar.bk_id IN (SELECT MAX(bk_id) FROM tbl_barangkeluar GROUP BY kode_barang_unik)) as tbl_barangkeluar'), function($join) {
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
                    'tbl_barangkeluar.user_nmlengkap_user',
                    'tbl_barangkeluar.bk_tujuan as customer_keluar',
                    'tbl_barangkeluar.keterangan as ket_keluar',
                    'tbl_barangkeluar.bk_status',
                    'tbl_barangkeluar.bk_tgl_kembali',
                    DB::raw('(SELECT bk_kondisi_kembali FROM tbl_barangkeluar AS tbk WHERE tbk.kode_barang_unik = tbl_barangmasuk.kode_barang_unik AND tbk.bk_kondisi_kembali IS NOT NULL ORDER BY tbk.bk_id DESC LIMIT 1) AS kondisi_terakhir')
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
            if ($request->filter_tglawal && $request->filter_tglakhir) {
                $query->whereBetween('tbl_barangmasuk.bm_tanggal', [$request->filter_tglawal, $request->filter_tglakhir]);
            }
            if ($request->filter_kondisi_barang) {
                $query->having('kondisi_terakhir', $request->filter_kondisi_barang);
            }
            if ($request->filter_status_transaksi) {
                $status = $request->filter_status_transaksi;
                if ($status == 'Tersedia') {
                    $query->whereNull('tbl_barangkeluar.bk_status')
                          ->whereNull('tbl_barangmasuk.deleted_at');
                } else if ($status == 'Habis Pakai') {
                    $query->where('tbl_barangkeluar.bk_status', 'Selesai')
                          ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%');
                } else if ($status == 'Selesai') {
                    $query->where('tbl_barangkeluar.bk_status', 'Selesai')
                          ->where(function($q) {
                              $q->whereNull('tbl_jenisbarang.jenisbarang_nama')
                                ->orWhere('tbl_jenisbarang.jenisbarang_nama', 'NOT LIKE', '%habis%');
                          });
                } else {
                    $query->where('tbl_barangkeluar.bk_status', $status);
                }
            }
            if ($request->filter_kondisi_stok) {
                $stok = $request->filter_kondisi_stok;
                if ($stok == 'Nonaktif') {
                    $query->havingRaw("kondisi_terakhir = 'Rusak Berat' OR tbl_barangmasuk.deleted_at IS NOT NULL");
                } else if ($stok == 'Keluar/Habis') {
                    $query->whereNull('tbl_barangmasuk.deleted_at')
                          ->havingRaw("kondisi_terakhir != 'Rusak Berat' OR kondisi_terakhir IS NULL")
                          ->where(function($q) {
                              $q->where('tbl_barangkeluar.bk_status', 'Dipinjam')
                                ->orWhere(function($q2) {
                                    $q2->where('tbl_barangkeluar.bk_status', 'Selesai')
                                       ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%');
                                });
                          });
                } else if ($stok == 'Tersedia') {
                    $query->whereNull('tbl_barangmasuk.deleted_at')
                          ->havingRaw("kondisi_terakhir != 'Rusak Berat' OR kondisi_terakhir IS NULL")
                          ->where(function($q) {
                              $q->whereNull('tbl_barangkeluar.bk_status')
                                ->orWhere(function($q2) {
                                    $q2->where('tbl_barangkeluar.bk_status', '!=', 'Dipinjam')
                                       ->where(function($q3) {
                                           $q3->where('tbl_barangkeluar.bk_status', '!=', 'Selesai')
                                              ->orWhereNull('tbl_jenisbarang.jenisbarang_nama')
                                              ->orWhere('tbl_jenisbarang.jenisbarang_nama', 'NOT LIKE', '%habis%');
                                       });
                                });
                          });
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tgl_masuk', function ($row) {
                    if ($row->bk_status == 'Selesai' && $row->bk_tgl_kembali && !str_contains(strtolower($row->jenisbarang_nama), 'habis')) {
                        return Carbon::parse($row->bk_tgl_kembali)->translatedFormat('d M Y H:i');
                    }
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
                    $nama = $row->nama_teknisi_keluar ?? $row->user_nmlengkap_user;
                    if ($nama) $info[] = 'Oleh: ' . htmlspecialchars($nama);
                    if ($row->teknisi_sn_keluar) $info[] = 'ID Teknisi: ' . htmlspecialchars($row->teknisi_sn_keluar);
                    if ($row->customer_keluar) $info[] = 'Tujuan: ' . htmlspecialchars($row->customer_keluar);
                    if ($row->ket_keluar) $info[] = 'Ket: ' . htmlspecialchars($row->ket_keluar);
                    
                    if ($row->deleted_at) {
                        $status = '<br><span class="badge bg-danger mt-1"><i class="fe fe-trash me-1"></i>Dihapus (' . \Carbon\Carbon::parse($row->deleted_at)->translatedFormat('d M Y H:i') . ')</span>';
                    } else if ($row->bk_status == 'Dipinjam') {
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
                    if ($row->deleted_at) return 'Nonaktif';
                    
                    if ($row->bk_status == 'Dipinjam') return 'Keluar/Habis';
                    if ($row->bk_status == 'Selesai' && str_contains(strtolower($row->jenisbarang_nama), 'habis')) return 'Keluar/Habis';
                    
                    if ($row->kondisi_terakhir == 'Rusak Berat') {
                        return 'Nonaktif';
                    }
                    
                    return 'Tersedia';
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
                ->addColumn('kode_unik', function ($row) {
                    return $row->kode_barang_unik ?? $row->bm_kode ?? '-';
                })
                ->rawColumns(['tgl_masuk', 'tgl_keluar', 'teknisi_ket', 'kode_unik'])
                ->make(true);
        }
    }
}