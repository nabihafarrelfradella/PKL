@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <h1 class="page-title">Laporan Stok Barang</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Laporan</li>
            <li class="breadcrumb-item active" aria-current="page">Stok Barang</li>
        </ol>
    </div>
</div>
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Data</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="" class="fw-bold">Filter Tanggal</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="tglawal" class="form-control datepicker-date" placeholder="Tanggal Awal">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="tglakhir" class="form-control datepicker-date" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-5">
                        <button class="btn btn-success-light" onclick="filter()"><i class="fe fe-filter"></i> Filter</button>
                        <button class="btn btn-secondary-light" onclick="reset()"><i class="fe fe-refresh-ccw"></i> Reset</button>
                        <button class="btn btn-primary-light" onclick="print()"><i class="fe fe-printer"></i> Print</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Kode Barang</th>
                                <th class="border-bottom-0">Barang</th>
                                <th class="border-bottom-0">Stok Awal</th>
                                <th class="border-bottom-0">Jumlah Masuk</th>
                                <th class="border-bottom-0">Jumlah Keluar</th>
                                <th class="border-bottom-0">Total Stok</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table;
    $(document).ready(function() {
        getData();
    });

    function getData() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],
            "scrollX": true,
            "stateSave": true,
            "lengthMenu": [
                [5, 10, 25, 50, 100, -1],
                [5, 10, 25, 50, 100, 'Semua']
            ],
            "pageLength": 10,
            "lengthChange": true,
            "ajax": {
                "url": "{{ route('lap-sb.getlap-sb') }}",
                "data": function(d) {
                    d.tglawal = $('input[name="tglawal"]').val();
                    d.tglakhir = $('input[name="tglakhir"]').val();
                }
            },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                { data: 'barang_kode', name: 'barang_kode' },
                { data: 'barang_nama', name: 'barang_nama' },
                { data: 'stokawal', name: 'barang_stok' },
                { data: 'jmlmasuk', name: 'jmlmasuk', orderable: false },
                { data: 'jmlkeluar', name: 'jmlkeluar', searchable: false, orderable: false },
                { 
                    data: 'totalstok', 
                    name: 'totalstok', 
                    searchable: false, 
                    orderable: false,
                    render: function (data, type, row) {
                        // Bersihkan tag HTML (menghapus class text-success bawaan backend)
                        let cleanNumber = String(data).replace(/<[^>]*>?/gm, '').trim();
                        let stok = parseInt(cleanNumber);
                        
                        let color = "";
                        if (stok < 5) {
                            color = "#e82646"; // Merah
                        } else if (stok <= 10) {
                            color = "#f7b731"; // Oranye
                        } else {
                            color = "#09ad95"; // Hijau Default
                        }

                        return `<span style="color: ${color} !important; font-weight: bold;">${stok}</span>`;
                    }
                },
            ],
        });
    }

    function filter() {
        table.ajax.reload(null, false);
    }

    function reset() {
        $('input[name="tglawal"]').val('');
        $('input[name="tglakhir"]').val('');
        table.ajax.reload(null, false);
    }

    function print() {
        var tglawal = $('input[name="tglawal"]').val();
        var tglakhir = $('input[name="tglakhir"]').val();
        
        let url = "{{ route('lap-sb.print') }}";
        
        // Jika filter tanggal diisi, tambahkan parameter ke URL
        if (tglawal != '' && tglakhir != '') {
            url += "?tglawal=" + tglawal + "&tglakhir=" + tglakhir;
        }

        window.open(url, '_blank');
    }

    function validasi(judul, status) {
        swal({
            title: judul,
            type: status,
            confirmButtonText: "Iya."
        });
    }
</script>
@endsection