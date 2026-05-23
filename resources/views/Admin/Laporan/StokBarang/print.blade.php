<!DOCTYPE html>
<html lang="en">

<?php
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use Carbon\Carbon;
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <style>
        * { font-family: Arial, Helvetica, sans-serif; }
        .header { text-align: center; margin-bottom: 8px; }
        .header img { width: 140px; }
        .header h1 { margin: 8px 0 4px; font-size: 22px; font-weight: 700; }
        .header h4 { margin: 0; font-size: 14px; font-weight: 400; color: #555; }
        .print-date { text-align: right; font-size: 11px; color: #777; margin-bottom: 4px; }
        hr { border: none; border-top: 2px solid #2d3c89; margin: 8px 0 16px; }
        #table1 { border-collapse: collapse; width: 100%; margin-top: 8px; }
        #table1 td, #table1 th { border: 1px solid #ccc; padding: 7px 8px; }
        #table1 thead tr { background: #2d3c89; }
        #table1 th { color: #fff; font-size: 11px; padding: 9px 8px; text-transform: uppercase; }
        #table1 td { font-size: 11px; }
        #table1 tbody tr:nth-child(even) { background: #f5f7ff; }
        
        /* Warna Stok Sesuai Index */
        .stok-aman { color: #09ad95; font-weight: bold; }
        .stok-warning { color: #f7b731; font-weight: bold; }
        .stok-kritis { color: #e82646; font-weight: bold; }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <img src="{{url('/assets/default/web/default.png')}}" alt="Alfatindo">
        <h1>Laporan Stok Barang</h1>
        @if($tglawal == '')
            <h4>Semua Periode</h4>
        @else
            <h4>Periode: {{Carbon::parse($tglawal)->translatedFormat('d F Y')}} &mdash; {{Carbon::parse($tglakhir)->translatedFormat('d F Y')}}</h4>
        @endif
    </div>
    <div class="print-date">Dicetak pada: {{Carbon::now()->translatedFormat('d F Y H:i')}}</div>
    <hr>

    <table id="table1">
        <thead>
            <tr>
                <th width="1%">NO</th>
                <th>KODE BARANG</th>
                <th>NAMA BARANG</th>
                <th align="center">STOK AWAL</th>
                <th align="center">JML MASUK</th>
                <th align="center">JML KELUAR</th>
                <th align="center">TOTAL STOK</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
            @foreach($data as $d)
            <?php
                // Hitung Jumlah Masuk
                if ($tglawal == '') {
                    $jmlmasuk = BarangmasukModel::where('barang_kode', $d->barang_kode)->sum('bm_jumlah');
                } else {
                    $jmlmasuk = BarangmasukModel::where('barang_kode', $d->barang_kode)
                        ->whereBetween('bm_tanggal', [$tglawal, $tglakhir])->sum('bm_jumlah');
                }

                // Hitung Jumlah Keluar
                if ($tglawal == '') {
                    $jmlkeluar = BarangkeluarModel::where('barang_kode', $d->barang_kode)->sum('bk_jumlah');
                } else {
                    $jmlkeluar = BarangkeluarModel::where('barang_kode', $d->barang_kode)
                        ->whereBetween('bk_tanggal', [$tglawal, $tglakhir])->sum('bk_jumlah');
                }

                $totalStok = $d->barang_stok + ($jmlmasuk - $jmlkeluar);

                // Penentuan Class Warna sesuai Logika Index
                $colorClass = "stok-aman"; 
                if ($totalStok < 5) {
                    $colorClass = "stok-kritis"; // Merah
                } elseif ($totalStok <= 10) {
                    $colorClass = "stok-warning"; // Oranye
                }
            ?>
            <tr>
                <td align="center">{{$no++}}</td>
                <td>{{$d->barang_kode}}</td>
                <td>{{str_replace('_', ' ', $d->barang_nama)}}</td>
                <td align="center">{{$d->barang_stok}}</td>
                <td align="center">{{$jmlmasuk}}</td>
                <td align="center">{{$jmlkeluar}}</td>
                <td align="center" class="{{$colorClass}}">{{$totalStok}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>