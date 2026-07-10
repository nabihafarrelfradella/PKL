@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<style>
    .filter-card { border-left: 3px solid #2f80ed; }
    .qr-label    { font-family: 'Courier New', monospace; font-size: 0.75rem; word-break: break-all; }
</style>

<div class="page-header">
    <h1 class="page-title">Barang Tracking</h1>
</div>

<!-- FILTER -->
<div class="row row-sm mb-3">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body pb-3">
                <div class="row g-3">
                    <!-- ROW 1 -->
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Nama Barang</label>
                        <div class="position-relative">
                            <input type="text" id="filterNama" class="form-control form-control-sm" placeholder="Cari nama barang..." style="padding-right: 30px;">
                            <i class="fe fe-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #a8b1c7; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Serial Number</label>
                        <div class="position-relative">
                            <input type="text" id="filterSerial" class="form-control form-control-sm" placeholder="SN-xxxxx..." style="padding-right: 30px;">
                            <i class="fe fe-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #a8b1c7; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Kode BM</label>
                        <div class="position-relative">
                            <input type="text" id="filterBmKode" class="form-control form-control-sm" placeholder="BM-xxxxx..." style="padding-right: 30px;">
                            <i class="fe fe-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #a8b1c7; pointer-events: none;"></i>
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Status Transaksi</label>
                        <div class="position-relative">
                            <select id="filterStatusTransaksi" class="form-control form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="Tersedia">Tersedia / Masuk</option>
                                <option value="Dipinjam">Sedang Dipinjam</option>
                                <option value="Selesai">Sudah Kembali</option>
                                <option value="Habis Pakai">Habis Pakai</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Kondisi Stok</label>
                        <div class="position-relative">
                            <select id="filterKondisiStok" class="form-control form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Keluar/Habis">Keluar / Habis</option>
                                <option value="Nonaktif">Nonaktif / Dihapus</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Kondisi Barang</label>
                        <div class="position-relative">
                            <select id="filterKondisiBarang" class="form-control form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <!-- ROW 3 -->
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Kode BK</label>
                        <div class="position-relative">
                            <input type="text" id="filterBkKode" class="form-control form-control-sm" placeholder="BK-xxxxx..." style="padding-right: 30px;">
                            <i class="fe fe-search position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #a8b1c7; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Tgl Masuk/Kembali (Awal)</label>
                        <div class="position-relative">
                            <input type="date" id="filterTglAwal" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <label class="form-label mb-1">Tgl Masuk/Kembali (Akhir)</label>
                        <div class="position-relative">
                            <input type="date" id="filterTglAkhir" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="row row-sm">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Barang Masuk &amp; Tracking</h3>
            </div>
            <div class="card-body">
                <!-- Action Buttons Template (injected via DataTables DOM) -->
                <div id="custom-buttons-html" style="display: none;">
                    <div class="d-flex align-items-center flex-shrink-0 gap-2">
                        <button class="btn btn-success-light btn-sm" onclick="exportExcel()"><i class="fe fe-download"></i><span class="d-none d-md-inline ms-1">Excel</span></button>
                        <button class="btn btn-primary-light btn-sm" onclick="batchPrintQR()"><i class="fe fe-printer"></i><span class="d-none d-md-inline ms-1">Print QR</span></button>
                    </div>
                </div>
                <div class="w-100">
                    <table id="table-tracking" class="table table-bordered text-nowrap border-bottom">
                        <thead>
                            <tr>
                                <th width="1%"><input type="checkbox" id="checkAllTracking"></th>
                                <th width="1%">No</th>
                                <th>Nama Barang</th>
                                <th>Merk</th>
                                <th>Kode Unik</th>
                                <th>Serial Number</th>
                                <th>Satuan</th>
                                <th>Kondisi Stok</th>
                                <th>Kondisi Fisik</th>
                                <th>Tgl Masuk</th>
                                <th>Tgl Keluar</th>
                                <th>Status Transaksi</th>
                                <th>Keterangan</th>
                                <th width="1%">QR</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL QR -->
<div class="modal fade" id="modalQR" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2" style="background:linear-gradient(135deg,#1e40af,#3b82f6);">
                <h6 class="modal-title text-white fw-bold"><i class="fe fe-grid me-1"></i>QR Code Barang</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="qrcode" class="d-inline-block mb-3"
                     style="padding:12px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                </div>
                <div class="mt-1">
                    <span class="badge bg-primary-light text-primary qr-label" id="qrKode" style="padding:5px 8px;"></span>
                </div>
                <p id="qrNama" class="fw-semibold mt-2 mb-0"></p>
                <small id="qrSN" class="text-muted qr-label"></small>
            </div>
            <div class="modal-footer py-2 justify-content-center gap-2">
                <button class="btn btn-primary btn-sm px-3" onclick="printQR()"><i class="fe fe-printer"></i><span class="d-none d-md-inline ms-1">Print Label</span></button>
                <button class="btn btn-success btn-sm px-3" onclick="downloadQR()"><i class="fe fe-download"></i><span class="d-none d-md-inline ms-1">Download</span></button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var _k='', _n='', _s='';
    $(document).ready(function () {
        // Initialize Select2 for filter dropdowns to use web UI instead of native OS UI
        $('#filterStatusTransaksi, #filterKondisiStok, #filterKondisiBarang').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        var table = $('#table-tracking').DataTable({
            processing: true,
            serverSide: true,
            searching:  false,
            scrollX:    true,
            order:      [],
            pageLength: 25,
            dom: "<'row mb-2'<'col-12 d-flex flex-wrap justify-content-between align-items-center gap-2'l<'#custom-buttons-container'>>>" +
                 "<'row'<'col-sm-12 table-responsive'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            initComplete: function() {
                $('#custom-buttons-container').html($('#custom-buttons-html').html());
                
                // Initialize select2 for length menu so it looks consistent
                $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity, width: '65px' });
            },
            ajax: {
                url: "{{ route('barang-tracking.show') }}",
                data: function(d) {
                    d.filter_nama   = $('#filterNama').val();
                    d.filter_serial = $('#filterSerial').val();
                    d.filter_bm_kode = $('#filterBmKode').val();
                    d.filter_bk_kode = $('#filterBkKode').val();
                    d.filter_kondisi_stok = $('#filterKondisiStok').val();
                    d.filter_kondisi_barang = $('#filterKondisiBarang').val();
                    d.filter_status_transaksi = $('#filterStatusTransaksi').val();
                    d.filter_tglawal = $('#filterTglAwal').val();
                    d.filter_tglakhir = $('#filterTglAkhir').val();
                }
            },
            columns: [
                {
                    data: null, orderable:false, searchable:false,
                    render: function(data, type, row) {
                        var val = encodeURIComponent(JSON.stringify({
                            kode_unik: row.kode_barang_unik || row.bm_kode,
                            nama: row.barang_nama,
                            sn: row.serial_number
                        }));
                        return '<input type="checkbox" class="qr-checkbox" value="' + val + '">';
                    }
                },
                { data:'DT_RowIndex',   orderable:false, searchable:false },
                {
                    data:'barang_nama',
                    render: function(data) {
                        return (data || '-').split(' - ')[0];
                    }
                },
                {
                    data:'barang_nama',
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        let parts = (data || '-').split(' - ');
                        return parts[1] || '-';
                    }
                },
                { data:'kode_unik', defaultContent:'-' },
                { data:'serial_number', defaultContent:'-' },
                { data:'satuan_id',     defaultContent:'-' },
                {
                    data:'stok_real', orderable:false, searchable:false,
                    render: function(data) {
                        if(data == 'Tersedia') return '<span class="badge bg-success-light text-success">Tersedia</span>';
                        if(data == 'Keluar/Habis') return '<span class="badge bg-danger-light text-danger">Keluar/Habis</span>';
                        if(data == 'Nonaktif') return '<span class="badge bg-dark-light text-dark">Nonaktif</span>';
                        return '<span class="badge bg-secondary-light text-secondary">' + data + '</span>';
                    }
                },
                {
                    data: 'kondisi_terakhir', orderable: false, searchable: false,
                    render: function(data) {
                        if(data == 'Rusak Ringan') {
                            return '<span class="text-info fw-bold">' + data + '</span><br><span class="badge bg-warning-light text-warning mt-1">Butuh Perbaikan</span>';
                        } else if(data) {
                            return '<span class="text-info fw-bold">' + data + '</span>';
                        }
                        return '<span class="text-muted">-</span>';
                    }
                },
                { data:'tgl_masuk',     orderable:false, searchable:false },
                { data:'tgl_keluar',    orderable:false, searchable:false },
                { data:'teknisi_ket',   orderable:false, searchable:false },
                {
                    data: 'keterangan_bk', orderable:false, searchable:false,
                    render: function(data) {
                        return data && data !== '-' ? '<div class="text-muted small" style="white-space: normal; min-width: 200px; max-width: 350px;">' + data + '</div>' : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null, orderable:false, searchable:false,
                    render: function(data, type, row) {
                        // Simpan di data-attribute agar aman dari newline/quote
                        return '<button class="btn btn-sm btn-primary-light btn-qr" title="Lihat QR Code"'
                             + ' data-k="' + (row.kode_barang_unik||row.bm_kode||'').replace(/"/g,'&quot;') + '"'
                             + ' data-n="' + (row.barang_nama||'').replace(/"/g,'&quot;') + '"'
                             + ' data-s="' + (row.serial_number||'').replace(/"/g,'&quot;') + '">'
                             + '<i class="fe fe-grid"></i></button>';
                    }
                }
            ],
            language: { processing:'<span class="spinner-border spinner-border-sm me-2"></span>Memuat...' }
        });

        // Delegated click — aman untuk DataTables yang re-render
        $('#table-tracking').on('click', '.btn-qr', function() {
            _k = $(this).data('k');
            _n = $(this).data('n');
            _s = $(this).data('s');

            $('#qrKode').text(_k);
            $('#qrNama').text(_n);
            $('#qrSN').text(_s ? 'SN: '+_s : '');

            var el = document.getElementById('qrcode');
            el.innerHTML = '';
            var qrSize = window.innerWidth <= 767.98 ? 150 : 200;
            try {
                new QRCode(el, {
                    text:         _s || _k,
                    width:        qrSize, height: qrSize,
                    colorDark:    '#1e293b',
                    colorLight:   '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch(e) {
                el.innerHTML = '<p class="text-danger">Gagal generate QR. Cek koneksi internet.</p>';
            }
            $('#modalQR').modal('show');
        });

        // Auto-filter logic
        var filterTimer;
        $('#filterNama, #filterSerial, #filterBmKode, #filterBkKode').on('keyup', function() {
            clearTimeout(filterTimer);
            filterTimer = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });

        $('#filterKondisiStok, #filterKondisiBarang, #filterStatusTransaksi, #filterTglAwal, #filterTglAkhir').on('change', function() {
            table.ajax.reload();
        });
        
        $('#checkAllTracking').on('click', function() {
            $('.qr-checkbox').prop('checked', this.checked);
        });
    });

    function batchPrintQR() {
        var selected = [];
        $('.qr-checkbox:checked').each(function() {
            selected.push(JSON.parse(decodeURIComponent($(this).val())));
        });
        
        if (selected.length === 0) {
            swal({
                title: "Perhatian",
                text: "Silakan pilih setidaknya satu data untuk dicetak QR-nya.",
                type: "warning"
            });
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

    function exportExcel() {
        var url = "{{ route('barang-tracking.export') }}";
        var params = new URLSearchParams({
            filter_nama: $('#filterNama').val(),
            filter_serial: $('#filterSerial').val(),
            filter_bm_kode: $('#filterBmKode').val(),
            filter_bk_kode: $('#filterBkKode').val(),
            filter_kondisi_stok: $('#filterKondisiStok').val(),
            filter_kondisi_barang: $('#filterKondisiBarang').val(),
            filter_status_transaksi: $('#filterStatusTransaksi').val(),
            filter_tglawal: $('#filterTglAwal').val(),
            filter_tglakhir: $('#filterTglAkhir').val()
        });
        window.open(url + '?' + params.toString(), '_blank');
    }

    function printQR() {
        setTimeout(function(){
            var c = document.querySelector('#qrcode canvas');
            if(!c){ alert('QR belum siap.'); return; }
            var w = window.open('','_blank','width=420,height=520');
            w.document.write('<html><head><title>Label QR</title>'
                +'<style>body{font-family:Arial;text-align:center;padding:20px}'
                +'img{width:200px;height:200px;border:1px solid #ccc;border-radius:8px}'
                +'h3{margin:10px 0 3px;font-size:14px;word-break:break-all}'
                +'p{margin:2px;font-size:12px;color:#555}'
                +'</style></head><body>'
                +'<img src="'+c.toDataURL('image/png')+'">'
                +'<h3>'+_k+'</h3><p>'+_n+'</p>'
                +(_s?'<p style="font-family:monospace;font-size:11px;background:#f3f4f6;padding:2px 6px;border-radius:4px">'+_s+'</p>':'')
                +'<script>window.onload=function(){window.print();setTimeout(function(){window.close()},600)}<\/script>'
                +'</body></html>');
            w.document.close();
        }, 200);
    }

    function downloadQR() {
        setTimeout(function(){
            var c = document.querySelector('#qrcode canvas');
            if(!c){ alert('QR belum siap.'); return; }
            var b = document.createElement('canvas');
            b.width=500; b.height=540;
            var x = b.getContext('2d');
            x.fillStyle='#fff'; x.fillRect(0,0,500,540);
            x.drawImage(c,50,20,400,400);
            x.fillStyle='#1e293b'; x.font='bold 16px Arial'; x.textAlign='center';
            x.fillText(_k,250,445);
            x.font='13px Arial'; x.fillStyle='#475569';
            x.fillText(_n.substring(0,48),250,468);
            if(_s){ x.font='11px Courier New'; x.fillStyle='#64748b'; x.fillText(_s,250,490); }
            var a=document.createElement('a');
            a.download='QR-'+_k+'.png';
            a.href=b.toDataURL('image/png');
            a.click();
        }, 200);
    }
</script>
@endsection
