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

    private function buildQuery(Request $request)
    {
        $query = BarangmasukModel::withTrashed()
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangmasuk.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_merk', 'tbl_merk.merk_id', '=', 'tbl_barang.merk_id')
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
                'tbl_merk.merk_nama',
                'tbl_barangmasuk.deleted_at'
            ]);

        if ($request->tglawal != '' && $request->tglakhir != '') {
            $query->whereBetween('tbl_barangmasuk.bm_tanggal', [$request->tglawal, $request->tglakhir]);
        }
        if ($request->filter_nama) {
            $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
        }
        if ($request->filter_kode) {
            $query->where(function($q) use ($request) {
                $q->where('tbl_barangmasuk.bm_kode', 'LIKE', '%' . $request->filter_kode . '%')
                  ->orWhere('tbl_barangmasuk.kode_barang_unik', 'LIKE', '%' . $request->filter_kode . '%');
            });
        }
        if ($request->filter_sn) {
            $query->where('tbl_barangmasuk.serial_number', 'LIKE', '%' . $request->filter_sn . '%');
        }
        if ($request->filter_status == 'Dihapus') {
            $query->whereNotNull('tbl_barangmasuk.deleted_at');
        } elseif ($request->filter_status == 'Aktif') {
            $query->whereNull('tbl_barangmasuk.deleted_at');
        }

        return $query->orderBy('tbl_barangmasuk.bm_id', 'ASC');
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->buildQuery($request)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    if ($row->jam_masuk) {
                        return \Carbon\Carbon::parse($row->jam_masuk)->translatedFormat('d F Y H:i');
                    }
                    return $row->bm_tanggal ? \Carbon\Carbon::parse($row->bm_tanggal)->translatedFormat('d F Y') : '-';
                })
                ->addColumn('barang', function ($row) {
                    return (explode(' - ', $row->barang_nama ?? '-')[0]) ?? '-';
                })
                ->addColumn('merk_nama', function ($row) {
                    return $row->merk_nama ?? (explode(' - ', $row->barang_nama ?? '-')[1] ?? '-');
                })
                ->addColumn('kode_unik', function ($row) {
                    return $row->kode_barang_unik ?: ($row->bm_kode ?: '-');
                })
                ->addColumn('sn', function ($row) {
                    $cleanSN = (!empty($row->serial_number) && $row->serial_number !== 'Tanpa SN' && $row->serial_number !== '-') ? strip_tags($row->serial_number) : null;
                    return $cleanSN ?: '-';
                })
                ->addColumn('jenis', function ($row) {
                    return $row->jenisbarang_nama ?? '-';
                })
                ->addColumn('satuan', function ($row) {
                    return $row->satuan_id ?? '-';
                })
                ->addColumn('status', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus (' . \Carbon\Carbon::parse($row->deleted_at)->translatedFormat('d M Y H:i') . ')</span>';
                    }
                    return '<span class="badge bg-success">Aktif</span>';
                })
                ->rawColumns(['tgl', 'barang', 'kode_unik', 'sn', 'status'])
                ->make(true);
        }
    }

    public function print(Request $request)
    {
        $data['data'] = $this->buildQuery($request)->get();
        $data["title"]   = "Print Barang Masuk";
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir'] = $request->tglakhir;
        return view('Admin.Laporan.BarangMasuk.print', $data);
    }

    public function pdf(Request $request)
    {
        $data['data'] = $this->buildQuery($request)->get();
        $data["title"]   = "PDF Barang Masuk";
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
        $items = $this->buildQuery($request)->get();
        $exportTime = now()->translatedFormat('d F Y H:i');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Masuk');

        // === TITLE ROW ===
        $sheet->setCellValue('A1', 'LAPORAN BARANG MASUK - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '002B5E']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // === PERIODE ROW ===
        // === METADATA ===
        $sheet->setCellValue('A1', 'LAPORAN BARANG MASUK - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $periode = ($request->tglawal && $request->tglakhir)
            ? 'Periode: ' . Carbon::parse($request->tglawal)->translatedFormat('d F Y') . ' s/d ' . Carbon::parse($request->tglakhir)->translatedFormat('d F Y')
            : 'Periode: Semua Tanggal';
        $sheet->setCellValue('A2', $periode . '   |   Diekspor: ' . $exportTime);
        $sheet->mergeCells('A2:J2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // === HEADER ROW ===
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

        // === DATA ROWS ===
        $row = 4;
        $no  = 1;
        foreach ($items as $item) {
            $tgl = $item->jam_masuk
                ? Carbon::parse($item->jam_masuk)->translatedFormat('d M Y H:i')
                : ($item->bm_tanggal ? Carbon::parse($item->bm_tanggal)->translatedFormat('d M Y') : '-');
            $kodeUnik = $item->kode_barang_unik ?: ($item->bm_kode ?: '-');
            $sn       = (!empty($item->serial_number) && $item->serial_number !== 'Tanpa SN' && $item->serial_number !== '-')
                        ? strip_tags($item->serial_number) : '-';
            $status   = $item->deleted_at ? 'Dihapus' : 'Aktif';

            $parts = explode(' - ', $item->barang_nama ?? '-');
            $nama = $parts[0] ?? '-';
            $merk = $item->merk_nama ?? ($parts[1] ?? '-');

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

            if ($status === 'Dihapus') {
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

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'LapBarangMasuk_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}