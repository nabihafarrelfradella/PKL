<!DOCTYPE html>
<html lang="en">

<?php
use App\Models\Admin\BarangkeluarModel;
use App\Models\Admin\BarangmasukModel;
use Carbon\Carbon;
?>

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
        .text-success { color: #15803d; font-weight: 600; }
        .text-danger  { color: #b91c1c; font-weight: 600; }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{url('/assets/default/web/default.png')}}" alt="Alfatindo">
        <h1>Laporan Stok Barang</h1>
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
                <th>KODE BARANG</th>
                <th>NAMA BARANG</th>
                <th>SATUAN</th>
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
            if ($tglawal == '') {
                $jmlmasuk = BarangmasukModel::where('barang_kode', $d->barang_kode)->sum('bm_jumlah');
            } else {
                $jmlmasuk = BarangmasukModel::where('barang_kode', $d->barang_kode)->whereBetween('bm_tanggal', [$tglawal, $tglakhir])->sum('bm_jumlah');
            }
            if ($tglawal != '') {
                $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('tbl_barangkeluar.barang_kode', $d->barang_kode)
                    ->whereBetween('bk_tanggal', [$tglawal, $tglakhir]);
            } else {
                $baseQuery = BarangkeluarModel::leftJoin('tbl_barang', 'tbl_barang.barang_kode', '=', 'tbl_barangkeluar.barang_kode')
                    ->leftJoin('tbl_jenisbarang', 'tbl_jenisbarang.jenisbarang_id', '=', 'tbl_barang.jenisbarang_id')
                    ->where('tbl_barangkeluar.barang_kode', $d->barang_kode);
            }
            $jmlkeluar = (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Dipinjam')->sum('tbl_barangkeluar.bk_jumlah')
                       + (clone $baseQuery)->where('tbl_barangkeluar.bk_status', 'Selesai')
                           ->where('tbl_jenisbarang.jenisbarang_nama', 'LIKE', '%habis%')
                           ->sum('tbl_barangkeluar.bk_jumlah');
            $totalStok = $d->barang_stok + ($jmlmasuk - $jmlkeluar);
            $colorClass = $totalStok > 0 ? 'text-success' : ($totalStok == 0 ? '' : 'text-danger');
            ?>
            <tr>
                <td align="center">{{$no++}}</td>
                <td>{{$d->barang_kode}}</td>
                <td>{{$d->barang_nama}}</td>
                <td>{{$d->satuan_id ?? '-'}}</td>
                <td>{{$d->jenisbarang_keterangan ?? '-'}}</td>
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
