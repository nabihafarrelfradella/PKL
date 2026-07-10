@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <h1 class="page-title">Laporan Barang Masuk</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Laporan</li>
            <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
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
                <!-- FILTER -->
                <div class="row row-sm mb-4 g-2">
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Nama Barang</label>
                        <input type="text" id="filter_nama" class="form-control form-control-sm" placeholder="Cari nama...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Kode BM / Unik</label>
                        <input type="text" id="filter_kode" class="form-control form-control-sm" placeholder="Cari kode...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Serial Number (SN)</label>
                        <input type="text" id="filter_sn" class="form-control form-control-sm" placeholder="Cari SN...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Status</label>
                        <select id="filter_status" class="form-control form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Dihapus">Dihapus</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Dari Tanggal</label>
                        <input type="date" name="tglawal" id="tglawal" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Sampai Tanggal</label>
                        <input type="date" name="tglakhir" id="tglakhir" class="form-control form-control-sm">
                    </div>
                    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2">
                        <div class="d-flex align-items-center flex-shrink-0">
                            <span class="me-2 text-muted small">Show</span>
                            <select id="custom-length" class="form-select form-select-sm w-auto d-inline-block">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="-1">All</option>
                            </select>
                            <span class="ms-2 text-muted small">entries</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button class="btn btn-success-light btn-sm" onclick="filter()" title="Filter"><i class="fe fe-filter"></i><span class="d-none d-md-inline ms-1">Filter</span></button>
                            <button class="btn btn-secondary-light btn-sm" onclick="reset()" title="Reset"><i class="fe fe-refresh-ccw"></i><span class="d-none d-md-inline ms-1">Reset</span></button>
                            <button class="btn btn-success-light btn-sm" onclick="exportExcel()" title="Export Excel"><i class="fe fe-file-text"></i><span class="d-none d-md-inline ms-1">Excel</span></button>
                            <button class="btn btn-primary-light btn-sm" onclick="print()" title="Print"><i class="fe fe-printer"></i><span class="d-none d-md-inline ms-1">Print</span></button>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <tr>
                                <th class="border-bottom-0" width="1%">No</th>
                                <th class="border-bottom-0">Tanggal Masuk</th>
                                <th class="border-bottom-0">Kode BM</th>
                                <th class="border-bottom-0">Nama Barang</th>
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Jenis</th>
                                <th class="border-bottom-0">Satuan</th>
                                <th class="border-bottom-0">Kode Unik</th>
                                <th class="border-bottom-0">SN</th>
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
        // Leave date blank to show all data by default
        $('#tglawal').val('');
        $('#tglakhir').val('');
        
        // Init Select2 for smooth dropdowns
        $('#filter_status').select2({ minimumResultsForSearch: Infinity, width: '100%' });
        $('#custom-length').select2({ minimumResultsForSearch: Infinity, width: '65px' });
        
        getData();
    });

    function getData() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],

            "dom": "<'row'<'col-sm-12 table-responsive'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "stateSave": false,
            "pageLength": 10,
            "lengthChange": false,
            "ajax": {
                "url": "{{ route('lap-bm.getlap-bm') }}",
                "data": function(d) {
                    d.tglawal = $('#tglawal').val();
                    d.tglakhir = $('#tglakhir').val();
                    d.filter_nama = $('#filter_nama').val();
                    d.filter_kode = $('#filter_kode').val();
                    d.filter_sn = $('#filter_sn').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            "searching": false,
            "columns": [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false 
                },
                { 
                    data: 'tgl', 
                    name: 'tbl_barangmasuk.bm_tanggal' 
                },
                { 
                    data: 'bm_kode', 
                    name: 'tbl_barangmasuk.bm_kode' 
                },
                { 
                    data: 'barang', 
                    name: 'tbl_barang.barang_nama',
                    render: function(data) {
                        return (data || '-').split(' - ')[0];
                    }
                },
                { 
                    data: 'barang', 
                    name: 'tbl_barang.barang_nama',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let parts = (data || '-').split(' - ');
                        return parts[1] || '-';
                    }
                },
                { 
                    data: 'jenis', 
                    name: 'tbl_jenisbarang.jenisbarang_nama' 
                },
                { 
                    data: 'satuan', 
                    name: 'tbl_barang.satuan_id' 
                },
                { 
                    data: 'kode_unik', 
                    name: 'tbl_barangmasuk.kode_barang_unik' 
                },
                { 
                    data: 'sn', 
                    name: 'tbl_barangmasuk.serial_number' 
                },
                {
                    data: 'status',
                    name: 'tbl_barangmasuk.deleted_at',
                    orderable: false,
                    searchable: false 
                }
            ],
        });

        $('#custom-length').on('change', function() {
            table.page.len($(this).val()).draw();
        });
    }

    function filter() {
        table.ajax.reload(null, false);
    }

    function reset() {
        $('#tglawal').val('');
        $('#tglakhir').val('');
        $('#filter_nama').val('');
        $('#filter_kode').val('');
        $('#filter_sn').val('');
        $('#filter_status').val('');
        table.ajax.reload(null, false);
    }

    function exportExcel() {
        var params = new URLSearchParams({
            tglawal: $('#tglawal').val(),
            tglakhir: $('#tglakhir').val(),
            filter_nama: $('#filter_nama').val(),
            filter_kode: $('#filter_kode').val(),
            filter_sn: $('#filter_sn').val(),
            filter_status: $('#filter_status').val()
        });
        let url = "{{route('lap-bm.excel')}}";
        window.open(url + '?' + params.toString(), '_blank');
    }

    function print() {
        var tglawal = $('#tglawal').val();
        var tglakhir = $('#tglakhir').val();
        if (tglawal != '' && tglakhir != '') {
            window.open("{{route('lap-bm.print')}}?tglawal=" + tglawal + "&tglakhir=" + tglakhir, '_blank');
        } else {
            swal({
                title: "Yakin Print Semua Data?",
                type: "warning",
                buttons: true,
                dangerMode: true,
                confirmButtonText: "Yakin",
                cancelButtonText: 'Batal',
                showCancelButton: true,
                confirmButtonColor: '#09ad95',
            }, function(value) {
                if (value == true) {
                    window.open("{{route('lap-bm.print')}}", '_blank');
                }
            });
        }
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
