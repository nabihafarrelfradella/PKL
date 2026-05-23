@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Barang Masuk</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Transaksi</li>
            <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
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
                            <th class="border-bottom-0">Jam Masuk</th>
                            <th class="border-bottom-0">Tanggal Masuk</th>
                            <th class="border-bottom-0">Kode Barang Masuk</th>
                            <th class="border-bottom-0">Kode Barang</th>
                            <th class="border-bottom-0">Barang</th>
                            <th class="border-bottom-0">Serial Number</th>
                            <th class="border-bottom-0">Jumlah Masuk</th>
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

@include('Admin.BarangMasuk.tambah')
@include('Admin.BarangMasuk.edit')
@include('Admin.BarangMasuk.hapus')
@include('Admin.BarangMasuk.barang')

<!-- MODAL QR -->
<div class="modal fade" data-bs-backdrop="static" id="Qmodaldemo8">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">QR Code Resi</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer" class="mb-3">
                    <img id="qrImage" src="" alt="QR Code">
                </div>
                <h5 id="qrKodeUnik" class="font-weight-bold"></h5>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="printQR()">Print <i class="fe fe-printer"></i></button>
                <button class="btn btn-light" data-bs-dismiss="modal">Tutup <i class="fe fe-x"></i></button>
            </div>
        </div>
    </div>
</div>

<script>
    function generateID() {
        $("input[name='bmkode']").val("Otomatis");
    }

    function update(data) {
        $("input[name='idbmU']").val(data.bm_id);
        $("input[name='bmkodeU']").val(data.bm_kode);
        $("input[name='kdbarangU']").val(data.barang_kode);
        $("input[name='serial_numberU']").val(data.serial_number);
        $("input[name='jmlU']").val(data.bm_jumlah);

        getbarangbyidU(data.barang_kode);

        let datetime = data.bm_tanggal;
        if(data.jam_masuk) {
            datetime = data.jam_masuk;
        }
        $("input[name='tglmasukU']").val(datetime);
    }

    function hapus(data) {
        $("input[name='idbm']").val(data.bm_id);
        $("#vbm").html("Kode BM " + "<b>" + data.bm_kode + "</b>");
    }

    function showQR(data) {
        const kode = data.kode_barang_unik;
        const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(kode);
        $("#qrImage").attr("src", qrUrl);
        $("#qrKodeUnik").text(kode);
    }

    function printQR() {
        var printWindow = window.open('', '', 'height=400,width=600');
        printWindow.document.write('<html><head><title>Print QR</title>');
        printWindow.document.write('</head><body style="text-align:center;">');
        printWindow.document.write('<img src="' + $("#qrImage").attr("src") + '" />');
        printWindow.document.write('<h2>' + $("#qrKodeUnik").text() + '</h2>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
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
        //datatables
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
                "url": "{{ route('barang-masuk.getbarang-masuk') }}",
            },

            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'jam_masuk',
                    name: 'jam_masuk',
                },
                {
                    data: 'tgl',
                    name: 'bm_tanggal',
                },
                {
                    data: 'bm_kode',
                    name: 'bm_kode',
                },
                {
                    data: 'barang_kode',
                    name: 'barang_kode',
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
                    data: 'bm_jumlah',
                    name: 'bm_jumlah',
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