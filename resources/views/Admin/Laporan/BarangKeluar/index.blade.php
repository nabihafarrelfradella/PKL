@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <h1 class="page-title">Laporan Barang Keluar</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Laporan</li>
            <li class="breadcrumb-item active" aria-current="page">Barang Keluar</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Data Barang Keluar</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="" class="fw-bold">Filter Tanggal</label>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label small text-muted">Dari Tanggal</label>
                            <input type="date" name="tglawal" id="tglawal" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label class="form-label small text-muted">Sampai Tanggal</label>
                            <input type="date" name="tglakhir" id="tglakhir" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end pb-3 mt-3 mt-md-0">
                        <button class="btn btn-success-light me-1" onclick="filter()"><i class="fe fe-filter"></i> Filter</button>
                        <button class="btn btn-secondary-light me-1" onclick="reset()"><i class="fe fe-refresh-ccw"></i> Reset</button>
                        <button class="btn btn-success-light me-1" onclick="exportExcel()"><i class="fe fe-file-text"></i> Export Excel</button>
                        <button class="btn btn-primary-light" onclick="print()"><i class="fe fe-printer"></i> Print</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Tanggal Keluar</th>
                                <th class="border-bottom-0">Kode BK</th>
                                <th class="border-bottom-0">Nama Barang</th>
                                <th class="border-bottom-0">Kode Unik</th>
                                <th class="border-bottom-0">SN</th>
                                <th class="border-bottom-0">Tujuan</th>
                                <th class="border-bottom-0">Teknisi</th>
                                <th class="border-bottom-0">Status</th>
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
        $('#tglawal').val('');
        $('#tglakhir').val('');
        getData();
    });

    function getData() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],

            "stateSave": false,
            "lengthMenu": [
                [5, 10, 25, 50, 100, -1],
                [5, 10, 25, 50, 100, 'Semua']
            ],
            "pageLength": 10,
            "lengthChange": true,
            "ajax": {
                "url": "{{ route('lap-bk.getlap-bk') }}",
                "data": function(d) {
                    d.tglawal = $('#tglawal').val();
                    d.tglakhir = $('#tglakhir').val();
                }
            },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false , orderable: false },
                { data: 'tgl', name: 'bk_tanggal' },
                { data: 'bk_kode', name: 'bk_kode' },
                { data: 'barang', name: 'barang_nama' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.kode_barang_unik || '-';
                    }
                },
                { 
                    data: null, 
                    name: 'serial_number',
                    render: function(data, type, row) {
                        var cleanSN = (row.serial_number && typeof row.serial_number === 'string') ? row.serial_number.replace(/<[^>]*>?/gm, '') : row.serial_number;
                        if (cleanSN && cleanSN !== '-' && cleanSN !== 'Tanpa SN') {
                            return cleanSN;
                        }
                        return '-';
                    }
                },
                { data: 'tujuan', name: 'bk_tujuan' },
                { data: 'teknisi', name: 'teknisi' },
                { data: 'status_badge', name: 'bk_status', orderable: false, searchable: false }
            ],
        });
    }

    function filter() {
        table.ajax.reload(null, false);
    }

    function reset() {
        $('#tglawal').val('');
        $('#tglakhir').val('');
        table.ajax.reload(null, false);
    }

    function exportExcel() {
        var tglawal = $('#tglawal').val();
        var tglakhir = $('#tglakhir').val();
        
        let url = "{{ route('lap-bk.excel') }}";
        if (tglawal != '' && tglakhir != '') {
            url += "?tglawal=" + tglawal + "&tglakhir=" + tglakhir;
        }
        
        window.open(url, '_blank');
    }

    function print() {
        var tglawal = $('#tglawal').val();
        var tglakhir = $('#tglakhir').val();
        
        let url = "{{ route('lap-bk.print') }}";
        if (tglawal != '' && tglakhir != '') {
            url += "?tglawal=" + tglawal + "&tglakhir=" + tglakhir;
        }
        
        window.open(url, '_blank');
    }

    function validasi(judul, status) {
        swal({
            title: judul,
            type: status,
            confirmButtonText: "OK"
        });
    }
</script>
@endsection
