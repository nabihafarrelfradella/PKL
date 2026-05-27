@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<style>
    .notif-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #e84c4c;
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 3px;
        border: 2px solid #fff;
    }
    .badge-dipinjam {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-selesai {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        font-weight: 600;
    }
    .teknisi-info-bar {
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        color: white;
        border-radius: 12px;
        padding: 14px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .teknisi-info-bar .ti-icon {
        width: 46px;
        height: 46px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
</style>

<!-- PAGE-HEADER -->
<div class="page-header">
    @if($roleId == 3)
        <h1 class="page-title">Peminjaman Barang</h1>
    @else
        <h1 class="page-title">Barang Keluar</h1>
    @endif
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Transaksi</li>
            <li class="breadcrumb-item active">{{ $roleId == 3 ? 'Peminjaman Barang' : 'Barang Keluar' }}</li>
        </ol>
    </div>
</div>

{{-- INFO BAR KHUSUS TEKNISI --}}
@if($roleId == 3)
<div class="teknisi-info-bar">
    <div class="ti-icon"><i class="fe fe-tool"></i></div>
    <div>
        <div class="fw-bold" style="font-size:1rem;">Halo, {{ Session::get('user')->user_nmlengkap }}!</div>
        <div style="font-size:0.82rem; opacity:0.85;">Anda hanya dapat melihat & membuat peminjaman atas nama sendiri. SN Teknisi: <strong>{{ Session::get('user')->teknisi_sn ?? '-' }}</strong></div>
    </div>
    <div class="ms-auto">
        <a class="btn btn-light btn-sm" data-bs-toggle="modal" href="#modaldemo8" onclick="generateID()">
            <i class="fe fe-plus me-1"></i>Pinjam Barang
        </a>
    </div>
</div>
@endif

<!-- ROW -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header justify-content-between">
                <h3 class="card-title">
                    @if($roleId == 3)
                        <i class="fe fe-clipboard me-1 text-primary"></i>Riwayat Peminjaman Saya
                    @else
                        <i class="fe fe-list me-1"></i>Data Barang Keluar
                    @endif
                </h3>
                @if ($hakTambah > 0 && $roleId != 3)
                <div>
                    <a class="modal-effect btn btn-primary-light" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">
                        Tambah Data <i class="fe fe-plus"></i>
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Tanggal & Jam Keluar</th>
                            <th class="border-bottom-0">Kode BK</th>
                            <th class="border-bottom-0">Barang</th>
                            <th class="border-bottom-0">Serial Number</th>
                            <th class="border-bottom-0">Customer / Lokasi</th>
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
        $("input[name='tujuanU']").val(data.bk_tujuan ? data.bk_tujuan.replace(/_/g, ' ') : '');
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

    // Fungsi kembali() ada di kembali.blade.php (di-include di bawah)

    function validasi(judul, status) {
        swal({ title: judul, type: status, confirmButtonText: "Iya." });
    }
</script>
@endsection

@section('scripts')
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
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
            "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
            "pageLength": 10,
            lengthChange: true,
            "ajax": { "url": "{{ route('barang-keluar.getbarang-keluar') }}" },
            "columns": [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                { data: 'tgl',          name: 'created_at' },
                { data: 'bk_kode',      name: 'bk_kode' },
                { data: 'barang',       name: 'barang_nama' },
                { data: 'serial_number',name: 'serial_number' },
                { data: 'tujuan',       name: 'bk_tujuan' },
                { data: 'teknisi',      name: 'teknisi' },
                { data: 'bk_jumlah',   name: 'bk_jumlah' },
                { data: 'status',       name: 'bk_status' },
                { data: 'action',       name: 'action', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endsection