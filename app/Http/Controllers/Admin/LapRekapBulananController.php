<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use App\Models\Admin\BarangModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LapRekapBulananController extends Controller
{
    public function index(Request $request)
    {
        $data['title']  = 'Lap Rekap Bulanan';
        $data['bulan']  = $request->bulan ?? date('m');
        $data['tahun']  = $request->tahun ?? date('Y');
        return view('Admin.Laporan.RekapBulanan.index', $data);
    }

    /**
     * Hitung tanggal awal dan akhir dari bulan & tahun request.
     */
    private function getTglRange(Request $request): array
    {
        $bulan    = $request->bulan ?? date('m');
        $tahun    = $request->tahun ?? date('Y');
        $tglawal  = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d');
        $tglakhir = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d');
        return [$tglawal, $tglakhir, $bulan, $tahun];
    }

    // =========================================================
    //  AJAX — Tab Barang Masuk (serverSide: true)
    // =========================================================
    public function showMasuk(Request $request)
    {
        if ($request->ajax()) {
            [$tglawal, $tglakhir] = $this->getTglRange($request);

            $query = BarangmasukModel::withTrashed()
                ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->select([
                    'tbl_barangmasuk.bm_id',
                    'tbl_barangmasuk.bm_kode',
                    'tbl_barangmasuk.bm_tanggal',
                    'tbl_barangmasuk.jam_masuk',
                    'tbl_barangmasuk.bm_jumlah',
                    'tbl_barangmasuk.serial_number',
                    'tbl_barangmasuk.kode_barang_unik',
                    'tbl_barangmasuk.barang_kode',
                    'tbl_barang.barang_nama',
                    'tbl_barang.satuan_id',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_barangmasuk.deleted_at',
                ])
                ->whereBetween('tbl_barangmasuk.bm_tanggal', [$tglawal, $tglakhir])
                ->orderBy('tbl_barangmasuk.bm_id', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    if ($row->jam_masuk) {
                        return Carbon::parse($row->jam_masuk)->translatedFormat('d F Y H:i');
                    }
                    return $row->bm_tanggal ? Carbon::parse($row->bm_tanggal)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('barang', fn($row) => $row->barang_nama ?? '-')
                ->addColumn('kode_unik', fn($row) => $row->kode_barang_unik ?: ($row->bm_kode ?: '-'))
                ->addColumn('sn', function ($row) {
                    $clean = (!empty($row->serial_number) && $row->serial_number !== 'Tanpa SN' && $row->serial_number !== '-')
                        ? strip_tags($row->serial_number) : null;
                    return $clean ?: '-';
                })
                ->addColumn('jenis', fn($row) => $row->jenisbarang_nama ?? '-')
                ->addColumn('satuan', fn($row) => $row->satuan_id ?? '-')
                ->addColumn('status', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    // =========================================================
    //  AJAX — Tab Barang Keluar (serverSide: true)
    // =========================================================
    public function showKeluar(Request $request)
    {
        if ($request->ajax()) {
            [$tglawal, $tglakhir] = $this->getTglRange($request);

            $query = BarangkeluarModel::withTrashed()
                ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
                ->select([
                    'tbl_barangkeluar.bk_id',
                    'tbl_barangkeluar.bk_kode',
                    'tbl_barangkeluar.bk_tanggal',
                    'tbl_barangkeluar.jam_keluar',
                    'tbl_barangkeluar.bk_jumlah',
                    'tbl_barangkeluar.bk_tujuan',
                    'tbl_barangkeluar.bk_lokasi',
                    'tbl_barangkeluar.bk_status',
                    'tbl_barangkeluar.bk_kondisi_kembali',
                    'tbl_barangkeluar.kode_barang_unik',
                    'tbl_barangkeluar.serial_number',
                    'tbl_barangkeluar.teknisi',
                    'tbl_barangkeluar.keterangan',
                    'tbl_barangkeluar.deleted_at',
                    'tbl_barang.barang_nama',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_user.user_nmlengkap as user_nmlengkap',
                ])
                ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                ->orderBy('bk_id', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    if ($row->jam_keluar) {
                        return Carbon::parse($row->jam_keluar)->translatedFormat('d F Y H:i');
                    }
                    return $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('teknisi', function ($row) {
                    $nama = $row->user_nmlengkap ?? null;
                    return $nama ? htmlspecialchars($nama) . ' (' . htmlspecialchars($row->teknisi) . ')' : ($row->teknisi ?? '-');
                })
                ->addColumn('barang', fn($row) => $row->barang_nama ?? '-')
                ->addColumn('tujuan', fn($row) => $row->bk_tujuan ?? '-')
                ->addColumn('lokasi', fn($row) => $row->bk_lokasi ?? '-')
                ->addColumn('keterangan', fn($row) => $row->keterangan ?? '-')
                ->addColumn('status_badge', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus</span>';
                    }
                    if ($row->bk_status == 'Dipinjam') {
                        return '<span class="badge bg-warning text-dark">Dipinjam</span>';
                    } elseif ($row->bk_status == 'Selesai') {
                        return '<span class="badge bg-success">Selesai</span>';
                    } elseif ($row->bk_status == 'Ditolak') {
                        return '<span class="badge bg-danger">Ditolak</span>';
                    }
                    return '<span class="badge bg-secondary">' . htmlspecialchars($row->bk_status) . '</span>';
                })
                ->rawColumns(['status_badge'])
                ->make(true);
        }
    }

    // =========================================================
    //  AJAX — Tab Stok Barang (serverSide: true)
    // =========================================================
    public function showStok(Request $request)
    {
        if ($request->ajax()) {
            [$tglawal, $tglakhir] = $this->getTglRange($request);

            $query = BarangModel::withTrashed()
                ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
                ->select([
                    'tbl_barang.barang_id',
                    'tbl_barang.barang_kode',
                    'tbl_barang.barang_nama',
                    'tbl_barang.barang_stok',
                    'tbl_barang.satuan_id',
                    'tbl_barang.deleted_at',
                    'tbl_jenisbarang.jenisbarang_nama',
                    'tbl_merk.merk_nama',
                ])
                ->orderBy('barang_id', 'ASC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('jenis', fn($row) => $row->jenisbarang_nama ?? '-')
                ->addColumn('satuan', fn($row) => $row->satuan_id ?? 'Unit')
                ->addColumn('stokawal', fn($row) => $row->barang_stok)
                ->addColumn('jmlmasuk', function ($row) use ($tglawal, $tglakhir) {
                    return BarangmasukModel::whereBetween('bm_tanggal', [$tglawal, $tglakhir])
                        ->where('barang_kode', $row->barang_kode)->sum('bm_jumlah');
                })
                ->addColumn('jmlkeluar', function ($row) use ($tglawal, $tglakhir) {
                    $baseQ = BarangkeluarModel::leftJoin('tbl_barang as b2', 'b2.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                        ->leftJoin('tbl_jenisbarang as j2', 'j2.jenisbarang_id', '=', 'b2.jenisbarang_id')
                        ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                        ->where('tbl_barangkeluar.barang_kode', $row->barang_kode);
                    return (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                         + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('j2.jenisbarang_nama', 'LIKE', '%habis%')->sum('tbl_barangkeluar.bk_jumlah')
                         + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')->sum('tbl_barangkeluar.bk_jumlah');
                })
                ->addColumn('totalstok', function ($row) use ($tglawal, $tglakhir) {
                    $masuk = BarangmasukModel::whereBetween('bm_tanggal', [$tglawal, $tglakhir])
                        ->where('barang_kode', $row->barang_kode)->sum('bm_jumlah');
                    $baseQ = BarangkeluarModel::leftJoin('tbl_barang as b2', 'b2.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                        ->leftJoin('tbl_jenisbarang as j2', 'j2.jenisbarang_id', '=', 'b2.jenisbarang_id')
                        ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                        ->where('tbl_barangkeluar.barang_kode', $row->barang_kode);
                    $keluar = (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                            + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('j2.jenisbarang_nama', 'LIKE', '%habis%')->sum('tbl_barangkeluar.bk_jumlah')
                            + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')->sum('tbl_barangkeluar.bk_jumlah');
                    return $row->barang_stok + ($masuk - $keluar);
                })
                ->addColumn('status', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    // =========================================================
    //  EXPORT EXCEL — 3 Sheet dalam 1 file
    // =========================================================
    public function excelRekap(Request $request)
    {
        [$tglawal, $tglakhir, $bulan, $tahun] = $this->getTglRange($request);
        $exportTime   = now()->translatedFormat('d F Y H:i');
        $periodeLabel = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y');
        $periodeStr   = 'Periode: ' . Carbon::parse($tglawal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($tglakhir)->translatedFormat('d F Y');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // ── SHEET 1: Barang Masuk ──────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Barang Masuk');
        $items_masuk = BarangmasukModel::withTrashed()
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->select([
                'tbl_barangmasuk.bm_kode', 'tbl_barangmasuk.bm_tanggal', 'tbl_barangmasuk.jam_masuk',
                'tbl_barangmasuk.bm_jumlah', 'tbl_barangmasuk.serial_number', 'tbl_barangmasuk.kode_barang_unik',
                'tbl_barang.barang_nama', 'tbl_barang.satuan_id', 'tbl_jenisbarang.jenisbarang_nama',
                'tbl_barangmasuk.deleted_at',
            ])
            ->whereBetween('tbl_barangmasuk.bm_tanggal', [$tglawal, $tglakhir])
            ->orderBy('tbl_barangmasuk.bm_id', 'ASC')->get();
        $this->buildSheetMasuk($sheet1, $items_masuk, $periodeStr, $exportTime);

        // ── SHEET 2: Barang Keluar ─────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Barang Keluar');
        $items_keluar = BarangkeluarModel::withTrashed()
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
            ->select([
                'tbl_barangkeluar.*', 'tbl_barang.barang_nama',
                'tbl_jenisbarang.jenisbarang_nama', 'tbl_user.user_nmlengkap as user_nmlengkap',
            ])
            ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
            ->orderBy('bk_id', 'ASC')->get();
        $this->buildSheetKeluar($sheet2, $items_keluar, $periodeStr, $exportTime);

        // ── SHEET 3: Stok Barang ───────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Stok Barang');
        $items_stok = BarangModel::withTrashed()
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
            ->orderBy('barang_id', 'ASC')->get();
        $this->buildSheetStok($sheet3, $items_stok, $tglawal, $tglakhir, $periodeStr, $exportTime);

        // ── OUTPUT ─────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'RekapBulanan_' . str_replace(' ', '_', $periodeLabel) . '_' . date('His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    // ─── HELPER: Sheet Barang Masuk ───────────────────────────
    private function buildSheetMasuk($sheet, $items, string $periodeStr, string $exportTime)
    {
        $sheet->setCellValue('A1', 'LAPORAN BARANG MASUK - REKAP BULANAN');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', $periodeStr . '   |   Diekspor: ' . $exportTime);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['No', 'Tgl Masuk', 'Kode BM', 'Nama Barang', 'Merk', 'Jenis', 'Satuan', 'Kode Unik', 'SN', 'Status'];
        foreach (range('A', 'J') as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'AAAAAA']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $row = 4;
        $no  = 1;
        foreach ($items as $item) {
            $tgl      = $item->jam_masuk ? Carbon::parse($item->jam_masuk)->translatedFormat('d M Y H:i')
                        : ($item->bm_tanggal ? Carbon::parse($item->bm_tanggal)->translatedFormat('d M Y') : '-');
            $kodeUnik = $item->kode_barang_unik ?: ($item->bm_kode ?: '-');
            $sn       = (!empty($item->serial_number) && $item->serial_number !== 'Tanpa SN' && $item->serial_number !== '-')
                        ? strip_tags($item->serial_number) : '-';
            $status   = $item->deleted_at ? 'Dihapus' : 'Aktif';
            $parts    = explode(' - ', $item->barang_nama ?? '-');
            $nama     = $parts[0] ?? '-';
            $merk     = $parts[1] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $tgl);
            $sheet->setCellValue('C' . $row, $item->bm_kode);
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('E' . $row, $merk);
            $sheet->setCellValue('F' . $row, $item->jenisbarang_nama ?? '-');
            $sheet->setCellValue('G' . $row, $item->satuan_id ?? 'Unit');
            $sheet->setCellValue('H' . $row, $kodeUnik);
            $sheet->setCellValue('I' . $row, $sn);
            $sheet->setCellValue('J' . $row, $status);

            $bg = ($no % 2 == 0) ? 'F0F7FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => 'center'],
            ]);
            $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB($status === 'Dihapus' ? 'DC2626' : '16A34A');
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }

    // ─── HELPER: Sheet Barang Keluar ──────────────────────────
    private function buildSheetKeluar($sheet, $items, string $periodeStr, string $exportTime)
    {
        $sheet->setCellValue('A1', 'LAPORAN BARANG KELUAR - REKAP BULANAN');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', $periodeStr . '   |   Diekspor: ' . $exportTime);
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F5F3FF']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['No', 'Tgl Keluar', 'Kode BK', 'Nama Barang', 'Merk', 'Kode Unik', 'SN', 'Customer', 'Lokasi', 'Teknisi', 'Keterangan', 'Status'];
        foreach (range('A', 'L') as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:L3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '7C3AED']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'AAAAAA']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $row = 4;
        $no  = 1;
        foreach ($items as $item) {
            $tgl      = $item->jam_keluar ? Carbon::parse($item->jam_keluar)->translatedFormat('d M Y H:i')
                        : ($item->bk_tanggal ? Carbon::parse($item->bk_tanggal)->translatedFormat('d M Y') : '-');
            $teknisi  = $item->user_nmlengkap ?? $item->teknisi ?? '-';
            $kodeUnik = $item->kode_barang_unik ?? '-';
            $status   = $item->deleted_at ? 'Dihapus' : ($item->bk_status ?? '-');
            $parts    = explode(' - ', $item->barang_nama ?? '-');
            $nama     = $parts[0] ?? '-';
            $merk     = $parts[1] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $tgl);
            $sheet->setCellValue('C' . $row, $item->bk_kode ?? '-');
            $sheet->setCellValue('D' . $row, $nama);
            $sheet->setCellValue('E' . $row, $merk);
            $sheet->setCellValue('F' . $row, $kodeUnik);
            $sheet->setCellValue('G' . $row, $item->serial_number ?? '-');
            $sheet->setCellValue('H' . $row, $item->bk_tujuan ?? '-');
            $sheet->setCellValue('I' . $row, $item->bk_lokasi ?? '-');
            $sheet->setCellValue('J' . $row, $teknisi);
            $sheet->setCellValue('K' . $row, $item->keterangan ?? '-');
            $sheet->setCellValue('L' . $row, $status);

            $bg = ($no % 2 == 0) ? 'F5F3FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => 'center'],
            ]);

            if ($status === 'Dipinjam') {
                $sheet->getStyle('L' . $row)->getFont()->getColor()->setRGB('D97706');
            } elseif ($status === 'Selesai') {
                $sheet->getStyle('L' . $row)->getFont()->getColor()->setRGB('16A34A');
            } elseif (in_array($status, ['Dihapus', 'Ditolak'])) {
                $sheet->getStyle('L' . $row)->getFont()->getColor()->setRGB('DC2626');
            }
            $row++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }

    // ─── HELPER: Sheet Stok Barang ────────────────────────────
    private function buildSheetStok($sheet, $items, string $tglawal, string $tglakhir, string $periodeStr, string $exportTime)
    {
        $sheet->setCellValue('A1', 'LAPORAN STOK BARANG - REKAP BULANAN');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->setCellValue('A2', $periodeStr . '   |   Diekspor: ' . $exportTime);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ECFDF5']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        $headers = ['No', 'Kode Barang', 'Nama Barang', 'Merk', 'Jenis', 'Satuan', 'Stok Awal', 'Jml Masuk', 'Jml Keluar', 'Total Stok', 'Status'];
        foreach (range('A', 'K') as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'AAAAAA']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $row = 4;
        $no  = 1;
        foreach ($items as $item) {
            $satuan = $item->satuan_id ?? 'Unit';

            $jmlMasuk = BarangmasukModel::whereBetween('bm_tanggal', [$tglawal, $tglakhir])
                ->where('barang_kode', $item->barang_kode)->sum('bm_jumlah');

            $baseQ = BarangkeluarModel::leftJoin('tbl_barang as b2', 'b2.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                ->leftJoin('tbl_jenisbarang as j2', 'j2.jenisbarang_id', '=', 'b2.jenisbarang_id')
                ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                ->where('tbl_barangkeluar.barang_kode', $item->barang_kode);
            $jmlKeluar = (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('j2.jenisbarang_nama', 'LIKE', '%habis%')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQ)->where('tbl_barangkeluar.bk_status', 'Selesai')->where('tbl_barangkeluar.bk_kondisi_kembali', 'Rusak Berat')->sum('tbl_barangkeluar.bk_jumlah');

            $totalStok = $item->barang_stok + ($jmlMasuk - $jmlKeluar);
            $status    = $item->deleted_at ? 'Dihapus' : 'Aktif';
            $parts     = explode(' - ', $item->barang_nama ?? '-');
            $nama      = $parts[0] ?? '-';
            $merk      = $item->merk_nama ?? ($parts[1] ?? '-');

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->barang_kode);
            $sheet->setCellValue('C' . $row, $nama);
            $sheet->setCellValue('D' . $row, $merk);
            $sheet->setCellValue('E' . $row, $item->jenisbarang_nama ?? '-');
            $sheet->setCellValue('F' . $row, $satuan);
            $sheet->setCellValue('G' . $row, $jmlMasuk . ' ' . $satuan);
            $sheet->setCellValue('H' . $row, $jmlKeluar . ' ' . $satuan);
            $sheet->setCellValue('I' . $row, $totalStok . ' ' . $satuan);
            $sheet->setCellValue('J' . $row, $status);

            $bg = ($no % 2 == 0) ? 'F0F7FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                'alignment' => ['vertical' => 'center'],
            ]);

            if ($totalStok <= 0) {
                $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('DC2626');
            } elseif ($totalStok < 5) {
                $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('D97706');
            } else {
                $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('16A34A');
            }

            if ($status == 'Dihapus') {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('DC2626');
            } else {
                $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('16A34A');
            }
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }
}
