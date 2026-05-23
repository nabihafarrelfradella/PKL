@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Barang Keluar</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Transaksi</li>
            <li class="breadcrumb-item active" aria-current="page">Barang Keluar</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->


<!-- ROW -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">Data</h3>
                @if ($hakTambah > 0)
                <div>
                    <a class="modal-effect btn btn-primary-light" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">Tambah Data
                        <i class="fe fe-plus"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Tanggal & Jam Keluar</th>
                            <th class="border-bottom-0">Kode Barang Keluar</th>
                            <th class="border-bottom-0">Barang</th>
                            <th class="border-bottom-0">Serial Number</th>
                            <th class="border-bottom-0">Tujuan</th>
                            <th class="border-bottom-0">Teknisi</th>
                            <th class="border-bottom-0">Jumlah</th>
                            <th class="border-bottom-0">Status</th>
                            <th class="border-bottom-0" width="1%">Action</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END ROW -->

@include('Admin.BarangKeluar.tambah')
@include('Admin.BarangKeluar.edit')
@include('Admin.BarangKeluar.hapus')
@include('Admin.BarangKeluar.barang')
@include('Admin.BarangKeluar.kembali')

<script>
    function generateID() {
        $("input[name='bkkode']").val("Otomatis");
    }

    function update(data) {
        $("input[name='idbkU']").val(data.bk_id);
        $("input[name='bkkodeU']").val(data.bk_kode);
        $("input[name='kdbarangU']").val(data.barang_kode);
        $("input[name='tujuanU']").val(data.bk_tujuan.replace(/_/g, ' '));
        $("input[name='jmlU']").val(data.bk_jumlah);
        $("input[name='serial_numberU']").val(data.serial_number);
        $("input[name='teknisiU']").val(data.teknisi);
        $("textarea[name='keteranganU']").val(data.keterangan);
        $("input[name='tglkeluarU']").val(data.created_at);
        
        getbarangbyidU(data.barang_kode);
    }

    function hapus(data) {
        $("input[name='idbk']").val(data.bk_id);
        $("#vbk").html("Kode BK " + "<b>" + data.bk_kode + "</b>");
    }

    function kembali(data) {
        $("input[name='idbkK']").val(data.bk_id);
        $("#vkodeK").html("Kode BK " + "<b>" + data.bk_kode + "</b>");
        $("input[name='jmlK']").val(data.bk_jumlah);
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

@section('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var table;
    $(document).ready(function() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],
            "scrollX": true,
            "stateSave": true,
            "lengthMenu": [
                [5, 10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ],
            "pageLength": 10,
            lengthChange: true,
            "ajax": {
                "url": "{{ route('barang-keluar.getbarang-keluar') }}",
            },
            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'tgl',
                    name: 'created_at',
                },
                {
                    data: 'bk_kode',
                    name: 'bk_kode',
                },
                {
                    data: 'barang',
                    name: 'barang_nama',
                },
                {
                    data: 'serial_number',
                    name: 'serial_number',
                },
                {
                    data: 'tujuan',
                    name: 'bk_tujuan',
                },
                {
                    data: 'teknisi',
                    name: 'teknisi',
                },
                {
                    data: 'bk_jumlah',
                    name: 'bk_jumlah',
                },
                {
                    data: 'status',
                    name: 'bk_status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });
    });
</script>
@endsection