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
                    DB::raw('COALESCE((SELECT bk_kondisi_kembali FROM tbl_barangkeluar AS tbk WHERE tbk.kode_barang_unik = tbl_barangmasuk.kode_barang_unik AND tbk.bk_kondisi_kembali IS NOT NULL ORDER BY tbk.bk_id DESC LIMIT 1), "Baik") AS kondisi_terakhir')
                )
                ->orderBy('tbl_barangmasuk.bm_id', 'DESC');

            // Logika Filter
            if ($request->filter_serial) {
                $query->where('tbl_barangmasuk.serial_number', 'LIKE', '%' . $request->filter_serial . '%');
            }
            if ($request->filter_kode) {
                $query->where('tbl_barangmasuk.kode_barang_unik', 'LIKE', '%' . $request->filter_kode . '%');
            }
            if ($request->filter_bm_kode) {
                $query->where('tbl_barangmasuk.bm_kode', 'LIKE', '%' . $request->filter_bm_kode . '%');
            }
            if ($request->filter_bk_kode) {
                $query->where('tbl_barangkeluar.bk_kode', 'LIKE', '%' . $request->filter_bk_kode . '%');
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

                    $html = !empty($info) ? implode('<br>', $info) . $status : $status;
                    return '<div class="small text-muted" style="white-space: normal; min-width: 200px; max-width: 350px;">' . $html . '</div>';
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
                ->addColumn('keterangan_bk', function ($row) {
                    return $row->ket_keluar ? htmlspecialchars($row->ket_keluar) : '-';
                })
                ->addColumn('bm_kode_col', function ($row) {
                    return $row->bm_kode ?? '-';
                })
                ->addColumn('bk_kode_col', function ($row) {
                    return $row->bk_kode ?? '-';
                })
                ->rawColumns(['tgl_masuk', 'tgl_keluar', 'teknisi_ket', 'kode_unik'])
                ->make(true);
        }
    }

    public function export(Request $request)
    {
        $query = DB::table('tbl_barangmasuk')
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
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
                DB::raw('COALESCE((SELECT bk_kondisi_kembali FROM tbl_barangkeluar AS tbk WHERE tbk.kode_barang_unik = tbl_barangmasuk.kode_barang_unik AND tbk.bk_kondisi_kembali IS NOT NULL ORDER BY tbk.bk_id DESC LIMIT 1), "Baik") AS kondisi_terakhir')
            )
            ->orderBy('tbl_barangmasuk.bm_id', 'DESC');

        if ($request->filter_serial) {
            $query->where('tbl_barangmasuk.serial_number', 'LIKE', '%' . $request->filter_serial . '%');
        }
        if ($request->filter_kode) {
            $query->where('tbl_barangmasuk.kode_barang_unik', 'LIKE', '%' . $request->filter_kode . '%');
        }
        if ($request->filter_bm_kode) {
            $query->where('tbl_barangmasuk.bm_kode', 'LIKE', '%' . $request->filter_bm_kode . '%');
        }
        if ($request->filter_bk_kode) {
            $query->where('tbl_barangkeluar.bk_kode', 'LIKE', '%' . $request->filter_bk_kode . '%');
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

        $data = $query->get();

        $exportTime = now()->translatedFormat('d F Y H:i');
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Tracking');

        // ===== METADATA ROW =====
        $sheet->setCellValue('A1', 'LAPORAN BARANG TRACKING - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', 'Diekspor pada: ' . $exportTime);
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => 'center']
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ===== HEADER ROW =====
        $headers = ['No', 'Nama Barang', 'Merk', 'Kode Unik', 'Serial Number', 'Satuan', 'Kondisi Stok', 'Kondisi Fisik', 'Tgl Masuk', 'Tgl Keluar', 'Status Transaksi', 'Keterangan'];
        $headerCols = range('A', 'L');
        foreach ($headerCols as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:L3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'AAAAAA']]]
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ===== DATA ROWS =====
        $row = 4;
        $no = 1;
        foreach ($data as $item) {
            // Tgl Masuk
            $tglMasuk = '-';
            if ($item->bk_status == 'Selesai' && $item->bk_tgl_kembali && !str_contains(strtolower($item->jenisbarang_nama), 'habis')) {
                $tglMasuk = Carbon::parse($item->bk_tgl_kembali)->translatedFormat('d M Y H:i');
            } elseif ($item->jam_masuk) {
                $tglMasuk = Carbon::parse($item->jam_masuk)->translatedFormat('d M Y H:i');
            } elseif ($item->bm_tanggal) {
                $tglMasuk = Carbon::parse($item->bm_tanggal)->translatedFormat('d M Y');
            }

            // Tgl Keluar
            $tglKeluar = '-';
            if ($item->tgl_keluar_jam) {
                $tglKeluar = Carbon::parse($item->tgl_keluar_jam)->translatedFormat('d M Y H:i');
            } elseif ($item->tgl_keluar_tgl) {
                $tglKeluar = Carbon::parse($item->tgl_keluar_tgl)->translatedFormat('d M Y');
            }

            // Status Transaksi
            $statusTrans = '';
            if ($item->deleted_at) {
                $statusTrans = 'Dihapus';
            } else if ($item->bk_status == 'Dipinjam') {
                $statusTrans = 'Sedang Dipinjam';
            } else if ($item->bk_status == 'Selesai') {
                if (str_contains(strtolower($item->jenisbarang_nama), 'habis')) {
                    $statusTrans = 'Habis Pakai';
                } else {
                    $statusTrans = 'Sudah Kembali';
                }
            } else {
                $statusTrans = 'Tersedia (In Stock)';
            }

            // Kondisi Stok
            $stokReal = 'Tersedia';
            if ($item->deleted_at) {
                $stokReal = 'Nonaktif';
            } else if ($item->bk_status == 'Dipinjam') {
                $stokReal = 'Keluar/Habis';
            } else if ($item->bk_status == 'Selesai' && str_contains(strtolower($item->jenisbarang_nama), 'habis')) {
                $stokReal = 'Keluar/Habis';
            } else if ($item->kondisi_terakhir == 'Rusak Berat') {
                $stokReal = 'Nonaktif';
            }

            // Ket
            $ketTeknisi = $item->ket_keluar ?? '-';

            // Split Nama dan Merk
            $parts = explode(' - ', $item->barang_nama ?? '-');
            $nama = $parts[0] ?? '-';
            $merk = $parts[1] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $nama);
            $sheet->setCellValue('C' . $row, $merk);
            $sheet->setCellValue('D' . $row, $item->kode_barang_unik ?? $item->bm_kode ?? '-');
            $sheet->setCellValue('E' . $row, $item->serial_number ?? '-');
            $sheet->setCellValue('F' . $row, $item->satuan_id ?? '-');
            $sheet->setCellValue('G' . $row, $stokReal);
            $sheet->setCellValue('H' . $row, $item->kondisi_terakhir ?? '-');
            $sheet->setCellValue('I' . $row, $tglMasuk);
            $sheet->setCellValue('J' . $row, $tglKeluar);
            $sheet->setCellValue('K' . $row, $statusTrans);
            $sheet->setCellValue('L' . $row, $ketTeknisi);

            // Alternating row color
            $bgColor = ($no % 2 == 0) ? 'F0F7FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => 'center']
            ]);

            // Color status cells
            if ($stokReal == 'Keluar/Habis') {
                $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('DC2626');
            } elseif ($stokReal == 'Nonaktif') {
                $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('6B7280');
            } else {
                $sheet->getStyle('G' . $row)->getFont()->getColor()->setRGB('16A34A');
            }

            $row++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Freeze header rows
        $sheet->freezePane('A4');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Export_Barang_Tracking_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($fileName).'"');
        $writer->save('php://output');
        exit;
    }
}