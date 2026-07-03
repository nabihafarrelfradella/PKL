<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LapStokBarang_".date('Y-m-d').".xls");
?>
<html xmlns:m="urn:schemas-microsoft-com:office:excel">
<head>
    <meta charset="UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <m:ExcelWorkbook>
            <m:ExcelWorksheets>
                <m:ExcelWorksheet>
                    <m:Name>Laporan</m:Name>
                    <m:WorksheetOptions>
                        <m:DisplayGridlines/>
                    </m:WorksheetOptions>
                </m:ExcelWorksheet>
            </m:ExcelWorksheets>
        </m:ExcelWorkbook>
    </xml>
    <![endif]-->
</head>
<body>
<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th colspan="7" style="background-color: #002b5e; color: white; font-size: 18px; font-weight: bold; text-align: center;">LAPORAN STOK BARANG</th>
        </tr>
        <tr>
            <th colspan="7" style="background-color: #002b5e; color: white; font-size: 14px; text-align: center;">
                @if($tglawal == '')
                Periode: Semua Tanggal
                @else
                Periode: {{\Carbon\Carbon::parse($tglawal)->translatedFormat('d F Y')}} s/d {{\Carbon\Carbon::parse($tglakhir)->translatedFormat('d F Y')}}
                @endif
            </th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">NO</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">KODE BARANG</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">NAMA BARANG</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">STOK AWAL</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">JUMLAH MASUK</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">JUMLAH KELUAR</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">TOTAL STOK</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($data as $d)
            @php
                if ($tglawal == '') {
                    $jmlmasuk = \App\Models\Admin\BarangmasukModel::where('barang_kode', '=', $d->barang_kode)->sum('bm_jumlah');
                } else {
                    $jmlmasuk = \App\Models\Admin\BarangmasukModel::whereBetween('bm_tanggal', [$tglawal, $tglakhir])
                        ->where('barang_kode', '=', $d->barang_kode)
                        ->sum('bm_jumlah');
                }

                if ($tglawal) {
                    $baseQuery = \App\Models\Admin\BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                        ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                        ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])
                        ->where('tbl_barangkeluar.barang_kode', '=', $d->barang_kode);
                } else {
                    $baseQuery = \App\Models\Admin\BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                        ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                        ->where('tbl_barangkeluar.barang_kode', '=', $d->barang_kode);
                }
                
                $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                           + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                               ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                               ->sum('tbl_barangkeluar.bk_jumlah');

                $totalstok = $d->barang_stok + ($jmlmasuk - $jmlkeluar);
            @endphp
        <tr>
            <td style="text-align: center;">{{$no++}}</td>
            <td>{{$d->barang_kode}}</td>
            <td>{{$d->barang_nama}}</td>
            <td style="text-align: center;">{{$d->barang_stok}}</td>
            <td style="text-align: center;">{{$jmlmasuk}}</td>
            <td style="text-align: center;">{{$jmlkeluar}}</td>
            <td style="text-align: center;">{{$totalstok}}</td>
            <td style="text-align: center;">{{ $d->deleted_at ? 'Dihapus' : 'Aktif' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
