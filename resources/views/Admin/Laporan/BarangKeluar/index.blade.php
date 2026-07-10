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
                <!-- FILTER -->
                <div class="row row-sm mb-4 g-2">
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Nama Barang</label>
                        <input type="text" id="filter_nama" class="form-control form-control-sm" placeholder="Cari nama...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Kode BK</label>
                        <input type="text" id="filter_kode" class="form-control form-control-sm" placeholder="Cari kode...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Teknisi</label>
                        <select id="filter_teknisi" class="form-control form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($teknisi_list as $t)
                                <option value="{{ $t->user_nmlengkap }}">{{ $t->user_nmlengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Status</label>
                        <select id="filter_status" class="form-control form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="Dipinjam">Dipinjam</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Ditolak">Ditolak</option>
                            <option value="Menunggu Persetujuan Pinjam">Menunggu Persetujuan</option>
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
                                <th class="border-bottom-0">Tanggal Keluar</th>
                                <th class="border-bottom-0">Kode BK</th>
                                <th class="border-bottom-0">Nama Barang</th>
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Kode Unik</th>
                                <th class="border-bottom-0">SN</th>
                                <th class="border-bottom-0">Nama Customer</th>
                                <th class="border-bottom-0">Lokasi Customer</th>
                                <th class="border-bottom-0">Teknisi</th>
                                <th class="border-bottom-0">Keterangan</th>
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
        
        // Init Select2 for smooth dropdowns
        $('#filter_teknisi').select2({ width: '100%' });
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
                "url": "{{ route('lap-bk.getlap-bk') }}",
                "data": function(d) {
                    d.tglawal = $('#tglawal').val();
                    d.tglakhir = $('#tglakhir').val();
                    d.filter_nama = $('#filter_nama').val();
                    d.filter_kode = $('#filter_kode').val();
                    d.filter_teknisi = $('#filter_teknisi').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            "searching": false,
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false , orderable: false },
                { data: 'tgl', name: 'bk_tanggal' },
                { data: 'bk_kode', name: 'bk_kode' },
                {
                    data: 'barang',
                    name: 'barang_nama',
                    render: function(data) {
                        return (data || '-').split(' - ')[0];
                    }
                },
                {
                    data: 'barang',
                    name: 'barang_nama',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let parts = (data || '-').split(' - ');
                        return parts[1] || '-';
                    }
                },
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
                { data: 'lokasi', name: 'bk_lokasi' },
                { data: 'teknisi', name: 'teknisi' },
                { data: 'keterangan', name: 'keterangan', orderable: false, searchable: false },
                { data: 'status_badge', name: 'bk_status', orderable: false, searchable: false }
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
        $('#filter_teknisi').val('');
        $('#filter_status').val('');
        table.ajax.reload(null, false);
    }

    function exportExcel() {
        var params = new URLSearchParams({
            tglawal: $('#tglawal').val(),
            tglakhir: $('#tglakhir').val(),
            filter_nama: $('#filter_nama').val(),
            filter_kode: $('#filter_kode').val(),
            filter_teknisi: $('#filter_teknisi').val(),
            filter_status: $('#filter_status').val()
        });
        
        let url = "{{ route('lap-bk.excel') }}";
        window.open(url + '?' + params.toString(), '_blank');
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
