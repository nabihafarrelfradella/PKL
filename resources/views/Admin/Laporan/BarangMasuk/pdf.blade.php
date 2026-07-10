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

        <h1>Laporan Barang Masuk<br><small style="font-size: 16px; font-weight: normal;">PT ALFATINDO TEKNOLOGI</small></h1>
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
                <th width="1%">No</th>
                <th>Tgl Masuk</th>
                <th>Kode BM</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Jenis</th>
                <th>Satuan</th>
                <th>Kode Unik</th>
                <th>Serial Number</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
            @foreach($data as $d)
                @php
                    $status   = $d->deleted_at ? 'Dihapus' : 'Aktif';
                    $statusColor = $d->deleted_at ? 'color: red;' : 'color: green;';

                    $parts = explode(' - ', $d->barang_nama ?? '-');
                    $nama = $parts[0] ?? '-';
                    $merk = $parts[1] ?? '-';
                    
                    $tgl = $d->jam_masuk ? \Carbon\Carbon::parse($d->jam_masuk)->translatedFormat('d M Y H:i') : ($d->bm_tanggal ? \Carbon\Carbon::parse($d->bm_tanggal)->translatedFormat('d M Y') : '-');
                    $kodeUnik = $d->kode_barang_unik ?: ($d->bm_kode ?: '-');
                    $sn = (!empty($d->serial_number) && $d->serial_number !== 'Tanpa SN' && $d->serial_number !== '-') ? strip_tags($d->serial_number) : '-';
                @endphp
                <tr>
                    <td align="center">{{ $no++ }}</td>
                    <td align="center">{{ $tgl }}</td>
                    <td align="center">{{ $d->bm_kode }}</td>
                    <td>{{ $nama }}</td>
                    <td>{{ $merk }}</td>
                    <td align="center">{{ $d->jenisbarang_nama ?? '-' }}</td>
                    <td align="center">{{ $d->satuan_id ?? 'Unit' }}</td>
                    <td align="center">{{ $kodeUnik }}</td>
                    <td align="center">{{ $sn }}</td>
                    <td align="center" style="font-weight: bold; {{ $statusColor }}">{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
