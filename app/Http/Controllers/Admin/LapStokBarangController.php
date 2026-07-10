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
        $data["jenis_list"] = \App\Models\Admin\JenisBarangModel::orderBy('jenisbarang_nama')->get();
        return view('Admin.Laporan.StokBarang.index', $data);
    }

    public function print(Request $request)
    {
        // Query dibersihkan dari filter tipe
        $query = BarangModel::withTrashed()->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
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
        $query = BarangModel::withTrashed()->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
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
        $query = BarangModel::withTrashed()
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->orderBy('barang_id', 'DESC');

        $items      = $query->get();
        $exportTime = now()->translatedFormat('d F Y H:i');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stok Barang');

        // === TITLE ===
        $sheet->setCellValue('A1', 'LAPORAN STOK BARANG - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // === PERIODE ===
        $periode = ($request->tglawal && $request->tglakhir)
            ? 'Filter Tgl: ' . \Carbon\Carbon::parse($request->tglawal)->translatedFormat('d F Y') . ' s/d ' . \Carbon\Carbon::parse($request->tglakhir)->translatedFormat('d F Y')
            : 'Periode: Semua Tanggal';
        $sheet->setCellValue('A2', $periode . '   |   Diekspor: ' . $exportTime);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // === HEADER ===
        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Merk', 'Jenis', 'Satuan', 'Stok Awal', 'Jml Masuk', 'Jml Keluar', 'Total Stok', 'Status'];
        foreach (range('A', 'K') as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'AAAAAA']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // === DATA ===
        $row = 4;
        $no  = 1;
        foreach ($items as $item) {
            $satuan   = $item->satuan_id ?? 'Unit';

            if ($request->tglawal == '') {
                $jmlMasuk = BarangmasukModel::where('barang_kode', $item->barang_kode)->sum('bm_jumlah');
            } else {
                $jmlMasuk = BarangmasukModel::whereBetween('bm_tanggal', [$request->tglawal, $request->tglakhir])
                    ->where('barang_kode', $item->barang_kode)->sum('bm_jumlah');
            }

            if ($request->tglawal) {
                $baseQ = BarangkeluarModel::leftJoin('tbl_barang as b2', 'b2.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang as j2', 'j2.jenisbarang_id', '=', 'b2.jenisbarang_id')
                    ->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir])
                    ->where('tbl_barangkeluar.barang_kode', $item->barang_kode);
            } else {
                $baseQ = BarangkeluarModel::leftJoin('tbl_barang as b2', 'b2.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang as j2', 'j2.jenisbarang_id', '=', 'b2.jenisbarang_id')
                    ->where('tbl_barangkeluar.barang_kode', $item->barang_kode);
            }
            $jmlKeluar = (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('j2.jenisbarang_nama', 'LIKE', '%habis%')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')->sum('tbl_barangkeluar.bk_jumlah');

            $totalStok = $item->barang_stok + ($jmlMasuk - $jmlKeluar);
            $status = $item->deleted_at ? 'Dihapus' : 'Aktif';

            $parts = explode(' - ', $item->barang_nama ?? '-');
            $nama = $parts[0] ?? '-';
            $merk = $parts[1] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->barang_kode);
            $sheet->setCellValue('C' . $row, $nama);
            $sheet->setCellValue('D' . $row, $merk);
            $sheet->setCellValue('E' . $row, $item->jenisbarang_nama ?? '-');
            $sheet->setCellValue('F' . $row, $satuan);
            $sheet->setCellValue('G' . $row, $item->barang_stok . ' ' . $satuan);
            $sheet->setCellValue('H' . $row, $jmlMasuk . ' ' . $satuan);
            $sheet->setCellValue('I' . $row, $jmlKeluar . ' ' . $satuan);
            $sheet->setCellValue('J' . $row, $totalStok . ' ' . $satuan);
            $sheet->setCellValue('K' . $row, $status);

            $bg = ($no % 2 == 0) ? 'F0F7FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => 'center'],
            ]);

            // Color stok
            if ($totalStok <= 0) {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('DC2626');
            } elseif ($totalStok < 5) {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('D97706');
            } else {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('16A34A');
            }

            // Color status
            if ($status === 'Dihapus') {
                $sheet->getStyle('K' . $row)->getFont()->getColor()->setRGB('DC2626');
            } else {
                $sheet->getStyle('K' . $row)->getFont()->getColor()->setRGB('16A34A');
            }

            $row++;
        }

        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A4');

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'LapStokBarang_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $query = BarangModel::withTrashed()
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->orderBy('barang_id', 'DESC');

            if ($request->filter_nama) {
                $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
            }
            if ($request->filter_jenis) {
                $query->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%' . $request->filter_jenis . '%');
            }
            if ($request->filter_status == 'Dihapus') {
                $query->whereNotNull('tbl_barang.deleted_at');
            } elseif ($request->filter_status == 'Aktif') {
                $query->whereNull('tbl_barang.deleted_at');
            }

            $data = $query->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('jenis', function ($row) {
                    return $row->jenisbarang_nama ?? '-';
                })
                ->addColumn('satuan', function ($row) {
                    return $row->satuan_id ?? 'Unit';
                })
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
                                   ->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')
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
                                   ->sum('tbl_barangkeluar.bk_jumlah')
                               + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                                   ->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')
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
                ->addColumn('status', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus (' . \Carbon\Carbon::parse($row->deleted_at)->translatedFormat('d M Y') . ')</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->rawColumns(['stokawal', 'jmlmasuk', 'jmlkeluar', 'totalstok', 'status']) // 'tipe' dihapus dari sini
                ->make(true);
        }
    }
}