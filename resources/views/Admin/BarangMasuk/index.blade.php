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
                    <button class="btn btn-info-light me-2" onclick="batchPrintQR()"><i class="fe fe-printer"></i> Batch Print QR</button>
                    <a class="modal-effect btn btn-primary-light" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">Tambah Data
                        <i class="fe fe-plus"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%"><input type="checkbox" id="checkAllBM"></th>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Tanggal & Jam Masuk</th>
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
        // Pre-fill barang details immediately from action data (no AJAX wait)
        if (data.barang_nama) {
            $("#nmbarangU").val(data.barang_nama);
            $("#statusU").val("true");
        }
        // Also fire AJAX to fill satuan/jenis (async)
        getbarangbyidU(data.barang_kode);

        let datetime = data.bm_tanggal;
        if(data.jam_masuk) {
            datetime = data.jam_masuk;
        }
        $("input[name='tglmasukU']").val(datetime);
        fetchAvailableSNsU(data.barang_kode, data.serial_number);
    }

    function fetchAvailableSNsU(barang_kode, currentSN) {
        if (!barang_kode) {
            $("#sn_listU").empty();
            return;
        }
        $.ajax({
            type: 'GET',
            url: "/admin/barang/get-available-sn/" + barang_kode,
            dataType: 'json',
            success: function(data) {
                var list = $("#sn_listU");
                list.empty();
                if (currentSN && currentSN !== '-' && !data.find(item => item.serial_number === currentSN)) {
                    list.append(`<option value="${currentSN}">${currentSN} (Saat ini)</option>`);
                }
                data.forEach(function(item) {
                    list.append(`<option value="${item.serial_number}">${item.serial_number} (Unik: ${item.kode_barang_unik})</option>`);
                });
            }
        });
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

    function batchPrintQR() {
        var selected = [];
        $('.qr-checkbox:checked').each(function() {
            selected.push(JSON.parse(decodeURIComponent($(this).val())));
        });
        
        if (selected.length === 0) {
            swal("Pilih Data", "Silakan pilih setidaknya satu data untuk dicetak QR-nya.", "warning");
            return;
        }

        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Batch Print QR</title>');
        printWindow.document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>');
        printWindow.document.write('<style>');
        printWindow.document.write('@page { size: 58mm auto; margin: 0; }');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 0 auto; padding: 10px 0; text-align: center; width: 58mm; }');
        printWindow.document.write('.qr-container { display: block; margin: 0 auto; padding: 15px 0; border-bottom: 1px dashed #000; width: 100%; page-break-inside: avoid; }');
        printWindow.document.write('.qr-code { margin: 0 auto; display: flex; justify-content: center; }');
        printWindow.document.write('.qr-info { margin-top: 8px; font-size: 11px; }');
        printWindow.document.write('.qr-info p { margin: 2px 0; line-height: 1.2; }');
        printWindow.document.write('.qr-kode { font-weight: bold; font-size: 12px; }');
        printWindow.document.write('@media print { body { width: 100%; max-width: 58mm; margin: 0 auto; padding: 0; } .qr-container { border-bottom: 1px dashed #000; padding: 10px 0; margin: 0; border-left: none; border-right: none; border-top: none; border-radius: 0; } }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');
        
        selected.forEach(function(item, index) {
            printWindow.document.write('<div class="qr-container">');
            printWindow.document.write('<div id="qrcode-' + index + '" class="qr-code"></div>');
            printWindow.document.write('<div class="qr-info">');
            printWindow.document.write('<p class="qr-kode">' + (item.kode_unik || '-') + '</p>');
            printWindow.document.write('<p>' + item.nama + '</p>');
            printWindow.document.write('<p>SN: ' + (item.sn && item.sn !== '-' ? item.sn : 'N/A') + '</p>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
        });

        printWindow.document.write('<script>');
        printWindow.document.write('window.onload = function() {');
        selected.forEach(function(item, index) {
            printWindow.document.write('new QRCode(document.getElementById("qrcode-' + index + '"), { text: "' + (item.sn && item.sn !== '-' ? item.sn : item.kode_unik) + '", width: 120, height: 120 });');
        });
        printWindow.document.write('setTimeout(function() { window.print(); }, 500);');
        printWindow.document.write('};');
        printWindow.document.write('<\/script>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var val = encodeURIComponent(JSON.stringify({
                            kode_unik: row.kode_barang_unik || row.bm_kode,
                            nama: row.barang_nama,
                            sn: row.serial_number
                        }));
                        return '<input type="checkbox" class="qr-checkbox" value="' + val + '">';
                    }
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'tgl',
                    name: 'jam_masuk',
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

        $('#checkAllBM').on('click', function() {
            $('.qr-checkbox').prop('checked', this.checked);
        });
    });
</script>
@endsection