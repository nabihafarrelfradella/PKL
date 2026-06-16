<!DOCTYPE html>
<html lang="en">

<?php use Carbon\Carbon; ?>

<head>
    <meta charset="UTF-8">
    <title>{{$title}}</title>
    <style>
        * { font-family: Arial, Helvetica, sans-serif; }
        .header { text-align: center; margin-bottom: 8px; }
        .header img { width: 140px; }
        .header h1 { margin: 8px 0 4px; font-size: 22px; font-weight: 700; }
        .header h4 { margin: 0; font-size: 14px; font-weight: 400; color: #555; }
        hr { border: none; border-top: 2px solid #2d3c89; margin: 8px 0 16px; }
        #table1 { border-collapse: collapse; width: 100%; margin-top: 8px; }
        #table1 td, #table1 th { border: 1px solid #ccc; padding: 7px 8px; }
        #table1 thead tr { background: #2d3c89; }
        #table1 th { color: #fff; font-size: 11px; padding: 9px 8px; }
        #table1 td { font-size: 11px; }
        #table1 tbody tr:nth-child(even) { background: #f5f7ff; }
    </style>
</head>

<body>

    <div class="header">

        <h1>Laporan Barang Masuk</h1>
        @if($tglawal == '')
        <h4>Semua Tanggal</h4>
        @else
        <h4>{{Carbon::parse($tglawal)->translatedFormat('d F Y')}} &mdash; {{Carbon::parse($tglakhir)->translatedFormat('d F Y')}}</h4>
        @endif
    </div>
    <hr>

    <table id="table1">
        <thead>
            <tr>
                <th align="center" width="1%">NO</th>
                <th>TGL MASUK</th>
                <th>KODE UNIK / RESI</th>
                <th>KODE BM</th>
                <th>KODE BARANG</th>
                <th>NAMA BARANG</th>
                <th>SERIAL NUMBER</th>
                <th align="center">JML</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
            @foreach($data as $d)
            <tr>
                <td align="center">{{$no++}}</td>
                <td>{{Carbon::parse($d->bm_tanggal)->translatedFormat('d M Y')}}</td>
                <td>{{$d->kode_barang_unik ?? '-'}}</td>
                <td>{{$d->bm_kode}}</td>
                <td>{{$d->barang_kode}}</td>
                <td>{{$d->barang_nama}}</td>
                <td>{{$d->serial_number ?? '-'}}</td>
                <td align="center">{{$d->bm_jumlah}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
