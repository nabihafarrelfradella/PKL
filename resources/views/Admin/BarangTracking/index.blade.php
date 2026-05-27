@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<style>
    .filter-card { border-left: 3px solid #2f80ed; }
    .qr-label    { font-family: 'Courier New', monospace; font-size: 0.75rem; word-break: break-all; }
</style>

<div class="page-header">
    <h1 class="page-title">Barang Tracking</h1>
    <div class="ms-auto pageheader-btn">
        <small class="text-muted"><i class="fe fe-info me-1"></i>Klik ikon QR untuk generate &amp; print label barang</small>
    </div>
</div>

<!-- FILTER -->
<div class="row row-sm mb-3">
    <div class="col-12">
        <div class="card filter-card">
            <div class="card-body pb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Nama Barang</label>
                        <input type="text" id="filterNama" class="form-control form-control-sm" placeholder="Cari nama barang...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Kode Unik / Resi</label>
                        <input type="text" id="filterKode" class="form-control form-control-sm" placeholder="BRG-xxx-xx...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Serial Number</label>
                        <input type="text" id="filterSerial" class="form-control form-control-sm" placeholder="SN-xxxxx...">
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary btn-sm" onclick="doFilter()"><i class="fe fe-search me-1"></i>Cari</button>
                        <button class="btn btn-light btn-sm ms-1" onclick="resetFilter()"><i class="fe fe-x me-1"></i>Reset</button>
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
                <div class="table-responsive">
                    <table id="table-tracking" class="table table-bordered text-nowrap border-bottom">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th>Nama Barang</th>
                                <th>Serial Number</th>
                                <th>Satuan</th>
                                <th>Jml Masuk</th>
                                <th>Tersedia</th>
                                <th>Tgl Masuk</th>
                                <th>Tgl Keluar</th>
                                <th>Status</th>
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
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
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
            <div class="modal-footer py-2">
                <button class="btn btn-primary btn-sm" onclick="printQR()"><i class="fe fe-printer me-1"></i>Print Label</button>
                <button class="btn btn-success btn-sm" onclick="downloadQR()"><i class="fe fe-download me-1"></i>Download</button>
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
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
        var table = $('#table-tracking').DataTable({
            processing: true,
            serverSide: true,
            searching:  false,
            scrollX:    true,
            order:      [],
            pageLength: 25,
            ajax: {
                url: "{{ route('barang-tracking.show') }}",
                data: function(d) {
                    d.filter_nama   = $('#filterNama').val();
                    d.filter_kode   = $('#filterKode').val();
                    d.filter_serial = $('#filterSerial').val();
                }
            },
            columns: [
                { data:'DT_RowIndex',   orderable:false, searchable:false },
                { data:'barang_nama' },
                { data:'serial_number', defaultContent:'-' },
                { data:'satuan_id',     defaultContent:'-' },
                { data:'bm_jumlah' },
                {
                    data:'stok_real', orderable:false, searchable:false,
                    render: function(data) {
                        if(data == 1) return '<span class="badge bg-success-light text-success">Tersedia</span>';
                        return '<span class="badge bg-danger-light text-danger">Keluar/Habis</span>';
                    }
                },
                { data:'tgl_masuk',     orderable:false, searchable:false },
                { data:'tgl_keluar',    orderable:false, searchable:false },
                { data:'teknisi_ket',   orderable:false, searchable:false },
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
            try {
                new QRCode(el, {
                    text:         _s || _k,
                    width:        200, height: 200,
                    colorDark:    '#1e293b',
                    colorLight:   '#ffffff',
                    correctLevel: QRCode.CorrectLevel.M
                });
            } catch(e) {
                el.innerHTML = '<p class="text-danger">Gagal generate QR. Cek koneksi internet.</p>';
            }
            $('#modalQR').modal('show');
        });

        $('#filterNama,#filterKode,#filterSerial').on('keypress', function(e){
            if(e.which===13) table.ajax.reload();
        });

        window.doFilter    = function(){ table.ajax.reload(); };
        window.resetFilter = function(){ $('#filterNama,#filterKode,#filterSerial').val(''); table.ajax.reload(); };
    });

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
