@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Barang Masuk</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Transaksi</li>
            <li class="breadcrumb-item active" aria-current="page">Barang Masuk</li>
        </ol>
    </div>
    @if ($hakTambah > 0)
    <div class="ms-auto">
        <a class="modal-effect btn btn-primary" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">
            <i class="fe fe-plus me-1"></i> Tambah Data
        </a>
    </div>
    @endif
</div>
<!-- PAGE-HEADER END -->


<!-- ROW -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title"><i class="fe fe-box me-1"></i>Data Barang Masuk</h3>
                @if ($hakTambah > 0)
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-info-light btn-sm" id="btnBatchPrintAll" onclick="batchPrintAllChecked()" style="display:none;">
                        <i class="fe fe-printer me-1"></i>Print Terpilih
                    </button>
                </div>
                @endif
            </div>
            <div class="card-body">
                <!-- Search/Filter Bar Template (injected via DataTables DOM) -->
                <div id="custom-search-html" style="display: none;">
                    <div class="d-flex align-items-center w-100">
                        <div class="input-group input-group-sm w-100" style="min-width: 250px;">
                            <input type="text" id="bmSearchInput" class="form-control" placeholder="Pencarian...">
                            <button class="btn btn-primary" onclick="doSearchBM()"><i class="fe fe-search"></i></button>
                            <button class="btn btn-light border" onclick="resetSearchBM()"><i class="fe fe-x"></i></button>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%"><input type="checkbox" id="checkAllBMMain" title="Pilih/Hapus semua"></th>
                            <th class="border-bottom-0" width="1%"></th>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Tanggal &amp; Jam Masuk</th>
                            <th class="border-bottom-0">Kode Barang Masuk</th>
                            <th class="border-bottom-0">Jumlah Unit</th>
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
                <h6 class="modal-title">QR Code Resi</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal"></button>
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
        
        let identifier = "";
        if (data.serial_number && data.serial_number !== '-') {
            identifier = "SN <b>" + data.serial_number + "</b>";
        } else if (data.kode_barang_unik && data.kode_barang_unik !== '-') {
            identifier = "Kode Unik <b>" + data.kode_barang_unik + "</b>";
        } else {
            identifier = "Kode BM <b>" + data.bm_kode + "</b>";
        }
        
        $("#vbm").html(identifier);
    }

    function showQR(data) {
        // QR Code selalu menggunakan Kode Unik
        const kode = data.kode_barang_unik || data.barang_kode;
        const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" + encodeURIComponent(kode);
        $("#qrImage").attr("src", qrUrl);
        $("#qrKodeUnik").text(kode);
        $("#Qmodaldemo8").modal('show');
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

    function batchPrintQR(bmKode) {
        if (!bmKode) return;
        
        var selected = [];
        var btn = $('.btn-expand-bm[data-bm-kode="' + bmKode + '"]');
        var tr = btn.closest('tr');
        var row = table.row(tr);
        
        if (row.child.isShown() && row.child().find('.qr-checkbox-sn:checked').length > 0) {
            row.child().find('.qr-checkbox-sn:checked').each(function() {
                selected.push(JSON.parse(decodeURIComponent($(this).val())));
            });
            processBatchPrint(selected);
            return;
        }

        var checkedParents = $('.parent-checkbox:checked');
        var bmKodes = [];
        if (checkedParents.length > 0) {
            checkedParents.each(function() {
                bmKodes.push($(this).data('bm-kode'));
            });
        } else {
            bmKodes.push(bmKode);
        }

        swal({
            title: 'Sedang Memproses...',
            text: 'Mengambil data QR Code...',
            showConfirmButton: false
        });
        
        $.ajax({
            url: "{{ url('admin/barang-masuk/detail-sn/batch') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}", bm_kodes: bmKodes },
            success: function(data) {
                swal.close();
                if (!data || data.length === 0) {
                    swal("Gagal", "Tidak ada Serial Number untuk transaksi ini.", "error");
                    return;
                }
                data.forEach(function(item) {
                    selected.push({
                        kode_unik: item.kode_barang_unik || item.bm_kode,
                        barang_kode: item.barang_kode,
                        nama: item.barang_nama,
                        sn: item.serial_number
                    });
                });
                processBatchPrint(selected);
            },
            error: function() {
                swal("Gagal", "Gagal mengambil data Serial Number.", "error");
            }
        });
    }

    function processBatchPrint(selected) {
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Batch Print QR</title>');
        printWindow.document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></s' + 'cript>');
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
            // QR Code selalu menggunakan Kode Unik
            var printKode = item.kode_unik || item.kode_barang_unik || item.barang_kode;

            printWindow.document.write('<div class="qr-container">');
            printWindow.document.write('<div id="qrcode-' + index + '" class="qr-code"></div>');
            printWindow.document.write('<div class="qr-info">');
            printWindow.document.write('<p class="qr-kode">' + printKode + '</p>');
            printWindow.document.write('<p>' + (item.nama || item.barang_nama) + '</p>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');
        });

        printWindow.document.write('<script>');
        printWindow.document.write('window.onload = function() {');
        selected.forEach(function(item, index) {
            // QR Code selalu menggunakan Kode Unik
            var printKode = item.kode_unik || item.kode_barang_unik || item.barang_kode;
            
            printWindow.document.write('new QRCode(document.getElementById("qrcode-' + index + '"), { text: "' + printKode + '", width: 120, height: 120 });');
        });
        printWindow.document.write('setTimeout(function() { window.print(); }, 500);');
        printWindow.document.write('};');
        printWindow.document.write('</s' + 'cript>');
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
<style>
    .btn-expand-bm {
        width: 28px;
        height: 28px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn-expand-bm.expanded {
        transform: rotate(90deg);
        background-color: #e8f0fe;
        border-color: #4a6cf7;
        color: #4a6cf7;
    }
    .child-row-table {
        background: #f8faff;
        border-radius: 8px;
        padding: 10px 16px;
        margin: 4px 0;
    }
    .child-row-table table {
        width: 100%;
        font-size: 12.5px;
        border-collapse: collapse;
    }
    .child-row-table th {
        background: #e8f0fe;
        color: #3a5bd4;
        font-weight: 600;
        padding: 6px 10px;
        border: 1px solid #d0daf5;
    }
    .child-row-table td {
        padding: 6px 10px;
        border: 1px solid #e8ecf5;
        vertical-align: middle;
    }
    .child-row-table tr:hover td {
        background: #eef2ff;
    }
</style>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function formatChildBM(data, isParentChecked) {
        if (!data || data.length === 0) {
            return '<div class="child-row-table"><p class="text-muted mb-0 py-2 text-center">Tidak ada data Serial Number.</p></div>';
        }
        var checkStr = isParentChecked ? 'checked' : '';
        let html = '<div class="child-row-table"><table>';
        html += '<thead><tr><th width="1%"><input type="checkbox" class="checkAllSN" ' + checkStr + '></th><th width="1%">#</th><th>Kode Barang</th><th>Nama Barang</th><th>Merk</th><th>Serial Number</th><th>Kode Unik</th><th width="10%">Action</th></tr></thead><tbody>';
        data.forEach(function(row, i) {
            var val = encodeURIComponent(JSON.stringify({
                kode_unik: row.kode_barang_unik || row.bm_kode,
                barang_kode: row.barang_kode,
                nama: row.barang_nama,
                sn: row.serial_number
            }));
            html += '<tr>';
            html += '<td><input type="checkbox" class="qr-checkbox-sn" value="' + val + '" ' + checkStr + '></td>';
            html += '<td>' + (i + 1) + '</td>';
            html += '<td><span class="badge bg-secondary-light text-secondary">' + (row.barang_kode || '-') + '</span></td>';
            html += '<td>' + (row.barang_nama || '-') + '</td>';
            html += '<td>' + (row.merk_nama || '-') + '</td>';
            html += '<td><code>' + (row.serial_number || '-') + '</code></td>';
            html += '<td><span class="badge bg-info-light text-info">' + (row.kode_barang_unik || '-') + '</span></td>';
            html += '<td>' + (row.action || '-') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    var table;
    var bmSearchTerm = '';
    $(document).ready(function() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],
            "stateSave": false,
            "searching": false,
            "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            "pageLength": 10,
            lengthChange: true,
            "language": {
                "lengthMenu": "Show _MENU_"
            },
            "dom": "<'row mb-2'<'col-12 d-flex flex-nowrap justify-content-between align-items-center gap-2'l<'#custom-search-container.flex-grow-1.ms-auto'>>>" +
                   "<'row'<'col-sm-12 table-responsive'tr>>" +
                   "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "initComplete": function() {
                $('#custom-search-container').html($('#custom-search-html').html());
                
                // Initialize select2 for length menu so it looks like the second screenshot
                $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity, width: '55px' });
                
                // Re-bind enter key on the newly injected input
                $('#custom-search-container').find('#bmSearchInput').on('keypress', function(e) {
                    if (e.which === 13) doSearchBM();
                });
            },
            "ajax": {
                "url": "{{ route('barang-masuk.getbarang-masuk') }}",
                "data": function(d) {
                    d.search_term = bmSearchTerm;
                }
            },
            "columns": [
                { data: 'chk', name: 'chk', orderable: false, searchable: false },
                {
                    data: 'expand',
                    orderable: false,
                    searchable: false,
                    render: function(data) { return data; }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false , orderable: false },
                { data: 'tgl', name: 'jam_masuk' },
                { data: 'bm_kode', name: 'bm_kode' },
                { data: 'serial_number', name: 'serial_number' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            "drawCallback": function() {
                // Re-attach expand button events after each draw
                $('#table-1 tbody').off('click', '.btn-expand-bm').on('click', '.btn-expand-bm', function(e) {
                    e.stopPropagation();
                    var btn        = $(this);
                    var tr         = btn.closest('tr');
                    var row        = table.row(tr);
                    var barangKode = btn.data('barang-kode');
                    var bmKode     = btn.data('bm-kode');

                    if (row.child.isShown()) {
                        row.child.hide();
                        btn.removeClass('expanded');
                    } else {
                        btn.addClass('expanded');
                        row.child('<div class="text-center py-3"><i class="fe fe-loader"></i> Memuat...</div>').show();

                        var isParentChecked = tr.find('.parent-checkbox').prop('checked');
                        $.ajax({
                            url: "{{ url('admin/barang-masuk/detail-sn/all') }}/" + encodeURIComponent(bmKode),
                            method: 'GET',
                            success: function(data) {
                                row.child(formatChildBM(data, isParentChecked)).show();
                            },
                            error: function() {
                                row.child('<div class="text-center py-2 text-danger">Gagal memuat data.</div>').show();
                            }
                        });
                    }
                });
                // Show/hide batch print button
                updateBatchPrintBtn();
            }
        });

        $('#checkAllBM').on('click', function() {
            $('.qr-checkbox').prop('checked', this.checked);
        });
        $('#checkAllBMMain').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.parent-checkbox').prop('checked', isChecked);
            updateBatchPrintBtn();
        });

        // Enter key on search input is handled in initComplete
    });

    function doSearchBM() {
        bmSearchTerm = $('#custom-search-container').find('#bmSearchInput').val().trim();
        table.ajax.reload();
    }

    function resetSearchBM() {
        bmSearchTerm = '';
        $('#custom-search-container').find('#bmSearchInput').val('');
        table.ajax.reload();
    }

    function updateBatchPrintBtn() {
        var checked = $('.parent-checkbox:checked').length;
        if (checked > 0) {
            $('#btnBatchPrintAll').show();
        } else {
            $('#btnBatchPrintAll').hide();
        }
    }

    $(document).on('change', '.parent-checkbox', function() {
        updateBatchPrintBtn();
    });

    function batchPrintAllChecked() {
        var checked = $('.parent-checkbox:checked');
        if (checked.length === 0) {
            validasi('Pilih minimal 1 transaksi terlebih dahulu!', 'warning');
            return;
        }
        var bmKodes = [];
        checked.each(function() { bmKodes.push($(this).data('bm-kode')); });

        swal({ title: 'Memproses...', text: 'Mengambil data QR Code...', showConfirmButton: false });
        $.ajax({
            url: "{{ url('admin/barang-masuk/detail-sn/batch') }}",
            method: 'POST',
            data: { _token: "{{ csrf_token() }}", bm_kodes: bmKodes },
            success: function(data) {
                swal.close();
                if (!data || data.length === 0) {
                    swal('Info', 'Tidak ada data untuk dicetak.', 'warning');
                    return;
                }
                var selected = [];
                data.forEach(function(item) {
                    selected.push({
                        kode_unik: item.kode_barang_unik || item.bm_kode,
                        barang_kode: item.barang_kode,
                        nama: item.barang_nama,
                        sn: item.serial_number
                    });
                });
                processBatchPrint(selected);
            },
            error: function() { swal('Gagal', 'Gagal mengambil data.', 'error'); }
        });
    }


    function hapusSemuaBM(bmKode) {
        hapusKelompok(bmKode);
    }

    function hapusKelompok(bmKode) {
        var checkedParents = $('.parent-checkbox:checked');
        var bmKodes = [];
        if (checkedParents.length > 0) {
            checkedParents.each(function() {
                bmKodes.push($(this).data('bm-kode'));
            });
        } else {
            bmKodes.push(bmKode);
        }

        var textMsg = bmKodes.length > 1 
            ? 'Semua unit barang dalam ' + bmKodes.length + ' transaksi yang dipilih akan dihapus!'
            : 'Semua unit barang dalam transaksi ' + bmKodes[0] + ' akan dihapus!';

        swal({
            title: 'Hapus data?',
            text: textMsg,
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '/admin/barang-masuk/hapus-kelompok',
                    method: 'POST',
                    data: { _token: "{{ csrf_token() }}", bm_kodes: bmKodes },
                    success: function(res) {
                        swal('Terhapus!', res.success, 'success');
                        table.ajax.reload(null, false);
                        $('#checkAllBMMain').prop('checked', false);
                    },
                    error: function(xhr) {
                        swal('Gagal!', xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    }

    $(document).on('change', '.parent-checkbox', function() {
        var isChecked = $(this).prop('checked');
        
        if (!isChecked) {
            $('#checkAllBMMain').prop('checked', false);
        } else if ($('.parent-checkbox:checked').length === $('.parent-checkbox').length) {
            $('#checkAllBMMain').prop('checked', true);
        }
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        if (row.child.isShown()) {
            row.child().find('.checkAllSN').prop('checked', isChecked);
            row.child().find('.qr-checkbox-sn').prop('checked', isChecked);
        }
    });

    $(document).on('change', '.checkAllSN', function() {
        var isChecked = $(this).prop('checked');
        var tableChild = $(this).closest('table');
        tableChild.find('.qr-checkbox-sn').prop('checked', isChecked);
        
        var parentTr = $(this).closest('tr').closest('table').closest('tr').prev('tr');
        parentTr.find('.parent-checkbox').prop('checked', isChecked);
    });
    
    $(document).on('change', '.qr-checkbox-sn', function() {
        var tableChild = $(this).closest('table');
        var allChecked = tableChild.find('.qr-checkbox-sn').length === tableChild.find('.qr-checkbox-sn:checked').length;
        tableChild.find('.checkAllSN').prop('checked', allChecked);
        
        var parentTr = $(this).closest('tr').closest('table').closest('tr').prev('tr');
        parentTr.find('.parent-checkbox').prop('checked', allChecked);
    });
</script>
@endsection
