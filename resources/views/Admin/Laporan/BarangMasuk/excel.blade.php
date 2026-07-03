<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=LapBarangMasuk_".date('Y-m-d').".xls");
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
            <th colspan="7" style="background-color: #002b5e; color: white; font-size: 18px; font-weight: bold; text-align: center;">LAPORAN BARANG MASUK</th>
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
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">TGL MASUK</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">KODE BM</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">KODE UNIK</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">SN</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">KODE BARANG</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">NAMA BARANG</th>
            <th style="background-color: #e0e0e0; color: black; font-weight: bold; text-align: center;">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($data as $d)
        <tr>
            <td style="text-align: center;">{{$no++}}</td>
            <td>
                @if($d->jam_masuk)
                    {{\Carbon\Carbon::parse($d->jam_masuk)->translatedFormat('d M Y H:i')}}
                @else
                    {{\Carbon\Carbon::parse($d->bm_tanggal)->translatedFormat('d M Y')}}
                @endif
            </td>
            <td>{{$d->bm_kode}}</td>
            <td>{{$d->kode_barang_unik ?: ($d->bm_kode ?: '-')}}</td>
            <td>{{ (!empty($d->serial_number) && $d->serial_number !== 'Tanpa SN' && $d->serial_number !== '-') ? strip_tags($d->serial_number) : '-' }}</td>
            <td>{{$d->barang_kode}}</td>
            <td>{{$d->barang_nama}}</td>
            <td>{{ $d->deleted_at ? 'Dihapus (' . \Carbon\Carbon::parse($d->deleted_at)->translatedFormat('d M Y H:i') . ')' : 'Aktif' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
