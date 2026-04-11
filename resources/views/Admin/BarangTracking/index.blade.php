@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Barang Tracking</h1>
    <div class="ms-auto pageheader-btn">
        <small class="text-muted">Scan atau cari resi / serial number barang</small>
    </div>
</div>
<!-- PAGE-HEADER END -->

<!-- FILTER -->
<div class="row row-sm mb-3">
    <div class="col-12">
        <div class="card">
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
<!-- END FILTER -->

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
                                <th>Kode Unik / Resi</th>
                                <th>Nama Barang</th>
                                <th>Serial Number</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Stok Saat Ini</th>
                                <th>Tgl Masuk</th>
                                <th>Tgl Keluar</th>
                                <th>Teknisi / Ket</th>
                                <th width="1%">QR Code</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END TABLE -->

<!-- MODAL QR CODE -->
<div class="modal fade" id="modalQR" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">QR Code Barang</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="qrWrapper" class="mb-3">
                    <img id="qrImg" src="" alt="QR Code" style="width:220px;height:220px;">
                </div>
                <h5 id="qrKode" class="fw-bold mb-1"></h5>
                <p id="qrNama" class="text-muted mb-0 small"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" onclick="printQR()"><i class="fe fe-printer me-1"></i>Print</button>
                <button class="btn btn-success btn-sm" onclick="downloadQR()"><i class="fe fe-download me-1"></i>Download</button>
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL QR -->

@endsection

@section('scripts')
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table;
    $(document).ready(function () {
        table = $('#table-tracking').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [],
            pageLength: 25,
            ajax: {
                url: "{{ route('barang-tracking.show') }}",
                data: function (d) {
                    d.filter_nama   = $('#filterNama').val();
                    d.filter_kode   = $('#filterKode').val();
                    d.filter_serial = $('#filterSerial').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'kode_barang_unik', name: 'kode_barang_unik', defaultContent: '-' },
                { data: 'barang_nama', name: 'barang_nama' },
                { data: 'serial_number', name: 'serial_number', defaultContent: '-' },
                { data: 'satuan_id', name: 'satuan_id', defaultContent: '-' },
                { data: 'bm_jumlah', name: 'bm_jumlah' },
                { data: 'stok_real', name: 'stok_real', searchable: false, orderable: false },
                { data: 'tgl_masuk', name: 'tgl_masuk', searchable: false, orderable: false },
                { data: 'tgl_keluar', name: 'tgl_keluar', searchable: false, orderable: false },
                { data: 'teknisi_ket', name: 'teknisi_ket', searchable: false, orderable: false },
                {
                    data: null,
                    name: 'action',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-primary-light" onclick="showQR('${row.kode_barang_unik || row.bm_kode}', '${row.barang_nama}', '${row.qr_data}')">
                                    <i class="fe fe-grid"></i>
                                </button>`;
                    }
                }
            ],
            language: {
                processing: '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...'
            }
        });

        // Enter key filter
        $('#filterNama, #filterKode, #filterSerial').on('keypress', function (e) {
            if (e.which === 13) doFilter();
        });
    });

    function doFilter() {
        table.ajax.reload();
    }

    function resetFilter() {
        $('#filterNama, #filterKode, #filterSerial').val('');
        table.ajax.reload();
    }

    var currentQRUrl = '';

    function showQR(kode, nama, qrData) {
        const encoded  = encodeURIComponent(qrData || kode);
        const qrUrl    = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encoded}`;
        currentQRUrl   = qrUrl;

        $('#qrImg').attr('src', qrUrl);
        $('#qrKode').text(kode);
        $('#qrNama').text(nama);
        $('#modalQR').modal('show');
    }

    function printQR() {
        const kode = $('#qrKode').text();
        const nama = $('#qrNama').text();
        const win = window.open('', '_blank', 'width=400,height=500');
        win.document.write(`
            <html><head><title>Print QR - ${kode}</title>
            <style>body{font-family:sans-serif;text-align:center;padding:20px;}
            h3{margin:10px 0 4px;}p{margin:0;color:#666;font-size:13px;}</style>
            </head><body>
            <img src="${currentQRUrl}" style="width:200px;height:200px;"><br>
            <h3>${kode}</h3><p>${nama}</p>
            <script>window.onload=function(){window.print();setTimeout(()=>window.close(),500)}<\/script>
            </body></html>
        `);
        win.document.close();
    }

    function downloadQR() {
        const kode = $('#qrKode').text();
        const link = document.createElement('a');
        link.href = currentQRUrl.replace('size=220x220', 'size=500x500');
        link.download = `QR-${kode}.png`;
        link.target = '_blank';
        link.click();
    }
</script>
@endsection
