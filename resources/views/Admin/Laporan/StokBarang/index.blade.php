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
                <!-- FILTER -->
                <div class="row row-sm mb-4 g-2">
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Nama Barang</label>
                        <input type="text" id="filter_nama" class="form-control form-control-sm" placeholder="Cari nama...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label mb-1 small text-muted">Jenis Barang</label>
                        <select id="filter_jenis" class="form-control form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($jenis_list as $j)
                                <option value="{{ $j->jenisbarang_nama }}">{{ $j->jenisbarang_nama }}</option>
                            @endforeach
                        </select>
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
                                <th class="border-bottom-0">Kode Barang</th>
                                <th class="border-bottom-0">Barang</th>
                                <th class="border-bottom-0">Merk</th>
                                <th class="border-bottom-0">Jenis</th>
                                <th class="border-bottom-0">Satuan</th>
                                <th class="border-bottom-0">Stok Awal</th>
                                <th class="border-bottom-0">Jumlah Masuk</th>
                                <th class="border-bottom-0">Jumlah Keluar</th>
                                <th class="border-bottom-0">Total Stok</th>
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
        $('#filter_jenis').select2({ width: '100%' });
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
            "stateSave": true,
            "pageLength": 10,
            "lengthChange": false,
            "ajax": {
                "url": "{{ route('lap-sb.getlap-sb') }}",
                "data": function(d) {
                    d.tglawal = $('input[name="tglawal"]').val();
                    d.tglakhir = $('input[name="tglakhir"]').val();
                    d.filter_nama = $('#filter_nama').val();
                    d.filter_jenis = $('#filter_jenis').val();
                    d.filter_status = $('#filter_status').val();
                }
            },
            "searching": false,
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false , orderable: false },
                { data: 'barang_kode', name: 'barang_kode' },
                { 
                    data: 'barang_nama', 
                    name: 'barang_nama',
                    render: function(data) {
                        return (data || '-').split(' - ')[0];
                    }
                },
                { 
                    data: 'barang_nama', 
                    name: 'barang_nama',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let parts = (data || '-').split(' - ');
                        return parts[1] || '-';
                    }
                },
                { data: 'jenis', name: 'tbl_jenisbarang.jenisbarang_nama' },
                { data: 'satuan', name: 'tbl_barang.satuan_id' },
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
                { data: 'status', name: 'status', orderable: false, searchable: false }
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
        $('input[name="tglawal"]').val('');
        $('input[name="tglakhir"]').val('');
        $('#filter_nama').val('');
        $('#filter_jenis').val('');
        $('#filter_status').val('');
        table.ajax.reload(null, false);
    }

    function exportExcel() {
        var params = new URLSearchParams({
            tglawal: $('input[name="tglawal"]').val(),
            tglakhir: $('input[name="tglakhir"]').val(),
            filter_nama: $('#filter_nama').val(),
            filter_jenis: $('#filter_jenis').val(),
            filter_status: $('#filter_status').val()
        });

        let url = "{{ route('lap-sb.excel') }}";
        window.open(url + '?' + params.toString(), '_blank');
    }

    function print() {
        var tglawal = $('input[name="tglawal"]').val();
        var tglakhir = $('input[name="tglakhir"]').val();
        if (tglawal != '' && tglakhir != '') {
            window.open("{{route('lap-sb.print')}}?tglawal=" + tglawal + "&tglakhir=" + tglakhir, '_blank');
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
                    window.open("{{route('lap-sb.print')}}", '_blank');
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
