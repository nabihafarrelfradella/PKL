<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BarangkeluarModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PDF;

class LapBarangKeluarController extends Controller
{
    public function index()
    {
        $data['title'] = 'Lap Barang Keluar';
        $data['teknisi_list'] = \App\Models\Admin\UserModel::where('role_id', 3)->orderBy('user_nmlengkap')->get();
        return view('Admin.Laporan.BarangKeluar.index', $data);
    }

    private function buildQuery(Request $request)
    {
        $query = BarangkeluarModel::withTrashed()
            ->leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
            ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
            ->leftJoin('tbl_user', 'tbl_user.teknisi_sn', '=', 'tbl_barangkeluar.teknisi')
            ->select(
                'tbl_barangkeluar.*',
                'tbl_barang.barang_nama',
                'tbl_jenisbarang.jenisbarang_nama',
                'tbl_user.user_nmlengkap as user_nmlengkap'
            );

        if ($request->tglawal != '' && $request->tglakhir != '') {
            $query->whereBetween('bk_tanggal', [$request->tglawal, $request->tglakhir]);
        }
        if ($request->filter_nama) {
            $query->where('tbl_barang.barang_nama', 'LIKE', '%' . $request->filter_nama . '%');
        }
        if ($request->filter_kode) {
            $query->where('tbl_barangkeluar.bk_kode', 'LIKE', '%' . $request->filter_kode . '%');
        }
        if ($request->filter_teknisi) {
            $query->where(function ($q) use ($request) {
                $q->where('tbl_user.user_nmlengkap', 'LIKE', '%' . $request->filter_teknisi . '%')
                  ->orWhere('tbl_barangkeluar.teknisi_nama', 'LIKE', '%' . $request->filter_teknisi . '%');
            });
        }
        if ($request->filter_status && $request->filter_status !== '') {
            if ($request->filter_status === 'Dihapus') {
                $query->whereNotNull('tbl_barangkeluar.deleted_at');
            } else {
                $query->where('tbl_barangkeluar.bk_status', $request->filter_status);
            }
        }

        return $query->orderBy('bk_id', 'DESC');
    }

    public function show(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->buildQuery($request)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    if ($row->jam_keluar) {
                        return Carbon::parse($row->jam_keluar)->translatedFormat('d F Y H:i');
                    }
                    return $row->bk_tanggal == '' ? '-' : Carbon::parse($row->bk_tanggal)->translatedFormat('d F Y');
                })
                ->addColumn('tujuan', function ($row) {
                    return $row->bk_tujuan ?? '-';
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->bk_lokasi ?? '-';
                })
                ->addColumn('teknisi', function ($row) {
                    $nama = $row->user_nmlengkap ?? $row->teknisi_nama;
                    return $nama ? htmlspecialchars($nama) . ' (' . htmlspecialchars($row->teknisi) . ')' : ($row->teknisi ?? '-');
                })
                ->addColumn('barang', function ($row) {
                    return $row->barang_nama ?? '-';
                })
                ->addColumn('serial_number', function ($row) {
                    return $row->serial_number ?? '-';
                })
                ->addColumn('keterangan', function ($row) {
                    return $row->keterangan ?? '-';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->deleted_at) {
                        return '<span class="badge bg-danger">Dihapus (' . Carbon::parse($row->deleted_at)->translatedFormat('d M Y H:i') . ')</span>';
                    }
                    if ($row->bk_status == 'Dipinjam') {
                        return '<span class="badge bg-warning text-dark">Dipinjam</span>';
                    } elseif ($row->bk_status == 'Selesai') {
                        return '<span class="badge bg-success">Selesai</span>';
                    } elseif ($row->bk_status == 'Ditolak') {
                        return '<span class="badge bg-danger">Ditolak</span>';
                    } elseif ($row->bk_status == 'Menunggu Persetujuan Pinjam') {
                        return '<span class="badge bg-info">Menunggu Persetujuan</span>';
                    }
                    return '<span class="badge bg-secondary">' . htmlspecialchars($row->bk_status) . '</span>';
                })
                ->rawColumns(['tgl', 'tujuan', 'lokasi', 'teknisi', 'barang', 'status_badge', 'serial_number'])
                ->make(true);
        }
    }

    public function print(Request $request)
    {
        $data['data']    = $this->buildQuery($request)->get();
        $data['title']   = 'Print Laporan Barang Keluar';
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir']= $request->tglakhir;
        return view('Admin.Laporan.BarangKeluar.print', $data);
    }

    public function pdf(Request $request)
    {
        $data['data']    = $this->buildQuery($request)->get();
        $data['title']   = 'PDF Laporan Barang Keluar';
        $data['tglawal'] = $request->tglawal;
        $data['tglakhir']= $request->tglakhir;

        $pdf = PDF::loadView('Admin.Laporan.BarangKeluar.pdf', $data);

        if ($request->tglawal) {
            return $pdf->stream('lap-bk-'.$request->tglawal.'-'.$request->tglakhir.'.pdf');
        } else {
            return $pdf->stream('lap-bk-semua-tanggal.pdf');
        }
    }

    public function excel(Request $request)
    {
        $items      = $this->buildQuery($request)->get();
        $exportTime = now()->translatedFormat('d F Y H:i');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Keluar');

        // === TITLE ===
        $sheet->setCellValue('A1', 'LAPORAN BARANG KELUAR - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // === METADATA ===
        $sheet->setCellValue('A1', 'LAPORAN BARANG KELUAR - PT ALFATINDO TEKNOLOGI');
        $sheet->mergeCells('A1:L1');
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
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '475569']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EFF6FF']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // === HEADER ===
        $headers = ['No', 'Tgl Keluar', 'Kode BK', 'Nama Barang', 'Merk', 'Kode Unik', 'SN', 'Nama Customer', 'Lokasi Customer', 'Teknisi', 'Keterangan', 'Status'];
        foreach (range('A', 'L') as $i => $col) {
            $sheet->setCellValue($col . '3', $headers[$i]);
        }
        $sheet->getStyle('A3:L3')->applyFromArray([
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
            $tgl = $item->jam_keluar
                ? Carbon::parse($item->jam_keluar)->translatedFormat('d M Y H:i')
                : ($item->bk_tanggal ? Carbon::parse($item->bk_tanggal)->translatedFormat('d M Y') : '-');

            $teknisi = $item->user_nmlengkap ?? $item->teknisi_nama ?? $item->teknisi ?? '-';
            $kodeUnik = $item->kode_barang_unik ?? '-';

            if ($item->deleted_at) {
                $status = 'Dihapus';
            } else {
                $status = $item->bk_status ?? '-';
            }

            $parts = explode(' - ', $item->barang_nama ?? '-');
            $nama = $parts[0] ?? '-';
            $merk = $parts[1] ?? '-';

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

            $bg = ($no % 2 == 0) ? 'F0F7FF' : 'FFFFFF';
            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'fill'    => ['fillType' => 'solid', 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => 'center'],
            ]);

            // Color-code status
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

        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'LapBarangKeluar_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }
}