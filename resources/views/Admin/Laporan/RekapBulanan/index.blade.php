@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">
            Rekap Bulanan
            <span id="judul_periode" class="text-primary fw-bold" style="font-size: inherit;">
                {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
            </span>
        </h1>
    </div>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Laporan</li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Bulanan</li>
        </ol>
    </div>
</div>

{{-- Hidden selects untuk nilai filter (dikontrol JS) --}}
<select id="filter_bulan" style="display:none;">
    <option value="01" {{ $bulan == '01' ? 'selected' : '' }}>Januari</option>
    <option value="02" {{ $bulan == '02' ? 'selected' : '' }}>Februari</option>
    <option value="03" {{ $bulan == '03' ? 'selected' : '' }}>Maret</option>
    <option value="04" {{ $bulan == '04' ? 'selected' : '' }}>April</option>
    <option value="05" {{ $bulan == '05' ? 'selected' : '' }}>Mei</option>
    <option value="06" {{ $bulan == '06' ? 'selected' : '' }}>Juni</option>
    <option value="07" {{ $bulan == '07' ? 'selected' : '' }}>Juli</option>
    <option value="08" {{ $bulan == '08' ? 'selected' : '' }}>Agustus</option>
    <option value="09" {{ $bulan == '09' ? 'selected' : '' }}>September</option>
    <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
    <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
    <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
</select>
<select id="filter_tahun" style="display:none;">
    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
    @endfor
</select>

<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                {{-- ══ TAB NAVIGASI — Full Width, Rata ══════════════════════════ --}}
                <div class="rekap-tab-nav mb-3">
                    <button class="rekap-tab-btn active" id="tab-masuk-btn" onclick="switchTab('masuk', this)">
                        <span class="rekap-tab-icon"><i class="fe fe-arrow-down-circle"></i></span>
                        <span class="rekap-tab-label">Barang Masuk</span>
                        <span class="badge bg-primary ms-1" id="badge_masuk">0</span>
                    </button>
                    <button class="rekap-tab-btn" id="tab-keluar-btn" onclick="switchTab('keluar', this)">
                        <span class="rekap-tab-icon"><i class="fe fe-arrow-up-circle"></i></span>
                        <span class="rekap-tab-label">Barang Keluar</span>
                        <span class="badge bg-danger ms-1" id="badge_keluar">0</span>
                    </button>
                    <button class="rekap-tab-btn" id="tab-stok-btn" onclick="switchTab('stok', this)">
                        <span class="rekap-tab-icon"><i class="fe fe-box"></i></span>
                        <span class="rekap-tab-label">Stok Barang</span>
                        <span class="badge bg-success ms-1" id="badge_stok">0</span>
                    </button>
                </div>

                <style>
                    /* ── Tab Nav ─────────────────────────────────────────── */
                    .rekap-tab-nav {
                        display: flex;
                        width: 100%;
                        gap: 0;
                        border-radius: 10px;
                        overflow: hidden;
                        border: 1px solid #e8e8f7;
                        background: #f3f4f8;
                    }
                    .rekap-tab-btn {
                        flex: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 8px;
                        padding: 14px 10px;
                        border: none;
                        background: transparent;
                        color: #6c757d;
                        font-size: 0.93rem;
                        font-weight: 500;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        border-right: 1px solid #e8e8f7;
                        position: relative;
                    }
                    .rekap-tab-btn:last-child {
                        border-right: none;
                    }
                    .rekap-tab-btn:hover:not(.active) {
                        background: #eaeaf7;
                        color: #4a4a8a;
                    }
                    .rekap-tab-btn.active {
                        background: #fff;
                        color: #4a4ae8;
                        font-weight: 700;
                        box-shadow: inset 0 -3px 0 #4a4ae8;
                    }
                    .rekap-tab-btn.active-keluar {
                        background: #fff;
                        color: #dc3545;
                        font-weight: 700;
                        box-shadow: inset 0 -3px 0 #dc3545;
                    }
                    .rekap-tab-btn.active-stok {
                        background: #fff;
                        color: #09ad95;
                        font-weight: 700;
                        box-shadow: inset 0 -3px 0 #09ad95;
                    }
                    .rekap-tab-icon {
                        font-size: 1.1rem;
                        line-height: 1;
                    }

                    /* ── Table Responsive Wrapper ─────────────────────────── */
                    .rekap-table-wrap {
                        width: 100%;
                        overflow-x: auto;
                        -webkit-overflow-scrolling: touch;
                    }
                    .rekap-table-wrap table {
                        min-width: 700px;
                    }

                    /* ── Control Bar: selalu 1 baris ─────────────────────── */
                    .rekap-control-bar {
                        flex-wrap: nowrap !important;
                        align-items: center !important;
                    }

                    /* ── Mobile Overrides ─────────────────────────────────── */
                    @media (max-width: 767px) {
                        /* Hide tab labels, keep icons + badges */
                        .rekap-tab-label { display: none; }
                        .rekap-tab-btn {
                            padding: 12px 6px;
                            gap: 4px;
                            font-size: 0.8rem;
                        }
                        .rekap-tab-icon { font-size: 1rem; }

                        /* Filter row: 2-column grid on mobile */
                        .rekap-filter-row > [class*="col-"] {
                            flex: 0 0 50%;
                            max-width: 50%;
                        }
                        .rekap-filter-row .col-12 {
                            flex: 0 0 100%;
                            max-width: 100%;
                        }

                        /* Pagination on mobile */
                        .dataTables_paginate .paginate_button {
                            padding: 4px 8px !important;
                            font-size: 0.8rem !important;
                        }
                    }

                    @media (max-width: 420px) {
                        /* Tighten up even more on very small screens */
                        .rekap-tab-btn { padding: 10px 4px; }
                        .card-body { padding: 12px !important; }
                    }
                </style>

                <div class="tab-content" id="rekapTabContent">

                    <!-- FILTER -->
                    <div class="row row-sm mb-4 g-2 rekap-filter-row">
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1 small text-muted">Bulan</label>
                            <select id="vis_bulan" class="form-select form-select-sm">
                                <option value="01" {{ $bulan == '01' ? 'selected' : '' }}>Januari</option>
                                <option value="02" {{ $bulan == '02' ? 'selected' : '' }}>Februari</option>
                                <option value="03" {{ $bulan == '03' ? 'selected' : '' }}>Maret</option>
                                <option value="04" {{ $bulan == '04' ? 'selected' : '' }}>April</option>
                                <option value="05" {{ $bulan == '05' ? 'selected' : '' }}>Mei</option>
                                <option value="06" {{ $bulan == '06' ? 'selected' : '' }}>Juni</option>
                                <option value="07" {{ $bulan == '07' ? 'selected' : '' }}>Juli</option>
                                <option value="08" {{ $bulan == '08' ? 'selected' : '' }}>Agustus</option>
                                <option value="09" {{ $bulan == '09' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1 small text-muted">Tahun</label>
                            <select id="vis_tahun" class="form-select form-select-sm">
                                @for ($y = date('Y') + 5; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2 rekap-control-bar">
                            <div class="d-flex align-items-center flex-shrink-0">
                                <span class="me-2 text-muted small">Show</span>
                                <div id="len_wrap_masuk">
                                    <select id="length_masuk" class="form-select form-select-sm" style="width:70px;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="-1">All</option>
                                    </select>
                                </div>
                                <div id="len_wrap_keluar" style="display:none;">
                                    <select id="length_keluar" class="form-select form-select-sm" style="width:70px;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="-1">All</option>
                                    </select>
                                </div>
                                <div id="len_wrap_stok" style="display:none;">
                                    <select id="length_stok" class="form-select form-select-sm" style="width:70px;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="-1">All</option>
                                    </select>
                                </div>
                                <span class="ms-2 text-muted small">entries</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <button class="btn btn-success-light btn-sm" onclick="filterSemua()" title="Tampilkan">
                                    <i class="fe fe-filter"></i><span class="d-none d-md-inline ms-1">Tampilkan</span>
                                </button>
                                <button class="btn btn-secondary-light btn-sm" onclick="resetSemua()" title="Reset">
                                    <i class="fe fe-refresh-ccw"></i><span class="d-none d-md-inline ms-1">Reset</span>
                                </button>
                                <button class="btn btn-success-light btn-sm" onclick="exportExcelRekap()" title="Export Excel (3 Sheet)">
                                    <i class="fe fe-file-text"></i><span class="d-none d-md-inline ms-1">Excel</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ── TAB 1: Barang Masuk ───────────────────────────────── --}}
                    <div class="tab-pane-rekap" id="panel-masuk">
                        <div style="display:none;">{{-- Show entries pindah ke toolbar bersama --}}</div>
                        <div class="rekap-table-wrap">
                            <table id="table_masuk" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="1%">No</th>
                                        <th class="border-bottom-0">Tanggal Masuk</th>
                                        <th class="border-bottom-0">Kode BM</th>
                                        <th class="border-bottom-0">Nama Barang</th>
                                        <th class="border-bottom-0">Merk</th>
                                        <th class="border-bottom-0">Jenis</th>
                                        <th class="border-bottom-0">Satuan</th>
                                        <th class="border-bottom-0">Kode Unik</th>
                                        <th class="border-bottom-0">Serial Number</th>
                                        <th class="border-bottom-0">Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    {{-- ── TAB 2: Barang Keluar ─────────────────────────────── --}}
                    <div class="tab-pane-rekap" id="panel-keluar" style="display:none;">
                        <div style="display:none;">{{-- Show entries pindah ke toolbar bersama --}}</div>
                        <div class="rekap-table-wrap">
                            <table id="table_keluar" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="1%">No</th>
                                        <th class="border-bottom-0">Tanggal Keluar</th>
                                        <th class="border-bottom-0">Kode BK</th>
                                        <th class="border-bottom-0">Nama Barang</th>
                                        <th class="border-bottom-0">Merk</th>
                                        <th class="border-bottom-0">Kode Unik</th>
                                        <th class="border-bottom-0">Serial Number</th>
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

                    {{-- ── TAB 3: Stok Barang ───────────────────────────────── --}}
                    <div class="tab-pane-rekap" id="panel-stok" style="display:none;">
                        <div style="display:none;">{{-- Show entries pindah ke toolbar bersama --}}</div>
                        <div class="rekap-table-wrap">
                            <table id="table_stok" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                                <thead>
                                    <tr>
                                        <th class="border-bottom-0" width="1%">No</th>
                                        <th class="border-bottom-0">Kode Barang</th>
                                        <th class="border-bottom-0">Nama Barang</th>
                                        <th class="border-bottom-0">Merk</th>
                                        <th class="border-bottom-0">Jenis</th>
                                        <th class="border-bottom-0">Satuan</th>
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

                </div>{{-- /tab-content --}}
            </div>{{-- /card-body --}}
        </div>{{-- /card --}}
    </div>
</div>
@endsection

@section('scripts')
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var tableMasuk, tableKeluar, tableStok;
    var bulanAktif = '{{ $bulan }}';
    var tahunAktif = '{{ $tahun }}';
    var tabAktif   = 'masuk'; // tracking tab aktif saat ini

    const namaBulan = {
        '01':'Januari','02':'Februari','03':'Maret','04':'April',
        '05':'Mei','06':'Juni','07':'Juli','08':'Agustus',
        '09':'September','10':'Oktober','11':'November','12':'Desember'
    };

    // ── Inisialisasi Select2 ────────────────────────────────────────
    $(document).ready(function () {
        // Select visible (toolbar bersama)
        $('#vis_bulan').select2({ minimumResultsForSearch: Infinity, width: '100%' });
        $('#vis_tahun').select2({ minimumResultsForSearch: Infinity, width: '100%' });
        $('#length_masuk, #length_keluar, #length_stok').select2({ minimumResultsForSearch: Infinity, width: '70px' });

        initSemua();

        // Sinkron length per tabel
        $('#length_masuk').on('change', function() { if(tableMasuk) tableMasuk.page.len($(this).val()).draw(); });
        $('#length_keluar').on('change', function() { if(tableKeluar) tableKeluar.page.len($(this).val()).draw(); });
        $('#length_stok').on('change', function() { if(tableStok) tableStok.page.len($(this).val()).draw(); });
    });

    // ── Switch Tab Manual (tanpa Bootstrap Tab) ─────────────────────
    function switchTab(tab, btnEl) {
        // Sembunyikan semua panel
        $('.tab-pane-rekap').hide();
        // Reset semua tombol
        $('.rekap-tab-btn').removeClass('active active-keluar active-stok');
        // Sembunyikan semua len_wrap, tampilkan yang sesuai
        $('#len_wrap_masuk, #len_wrap_keluar, #len_wrap_stok').hide();
        $('#len_wrap_' + tab).show();

        // Tampilkan panel yang dipilih & beri class aktif
        $('#panel-' + tab).show();
        tabAktif = tab;

        if (tab === 'masuk') {
            $(btnEl).addClass('active');
        } else if (tab === 'keluar') {
            $(btnEl).addClass('active-keluar');
        } else if (tab === 'stok') {
            $(btnEl).addClass('active-stok');
        }

        // Paksa DataTables adjust kolom (karena tabel sebelumnya tersembunyi)
        if (tab === 'masuk' && tableMasuk)   tableMasuk.columns.adjust().draw(false);
        if (tab === 'keluar' && tableKeluar) tableKeluar.columns.adjust().draw(false);
        if (tab === 'stok' && tableStok)     tableStok.columns.adjust().draw(false);
    }

    // ── Inisialisasi DataTable Masuk ────────────────────────────────
    function initMasuk() {
        if (tableMasuk) {
            tableMasuk.ajax.url('{{ route("lap-rekap.showMasuk") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load(function() {
                $('#badge_masuk').text(tableMasuk.page.info().recordsTotal);
            });
            return;
        }
        tableMasuk = $('#table_masuk').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            order: [],
            dom: "<'row'<'col-sm-12 table-responsive'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            stateSave: false,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ajax: {
                url: '{{ route("lap-rekap.showMasuk") }}',
                data: function(d) {
                    d.bulan = bulanAktif;
                    d.tahun = tahunAktif;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tgl', name: 'tbl_barangmasuk.bm_tanggal' },
                { data: 'bm_kode', name: 'tbl_barangmasuk.bm_kode' },
                {
                    data: 'barang', name: 'tbl_barang.barang_nama',
                    render: function(data) { return (data || '-').split(' - ')[0]; }
                },
                {
                    data: 'barang', name: 'tbl_barang.barang_nama',
                    orderable: false, searchable: false,
                    render: function(data) { let p = (data||'-').split(' - '); return p[1]||'-'; }
                },
                { data: 'jenis', name: 'tbl_jenisbarang.jenisbarang_nama' },
                { data: 'satuan', name: 'tbl_barang.satuan_id' },
                { data: 'kode_unik', name: 'tbl_barangmasuk.kode_barang_unik' },
                { data: 'sn', name: 'tbl_barangmasuk.serial_number' },
                { data: 'status', name: 'tbl_barangmasuk.deleted_at', orderable: false, searchable: false },
            ],
            drawCallback: function() {
                $('#badge_masuk').text(this.api().page.info().recordsTotal);
            }
        });
    }

    // ── Inisialisasi DataTable Keluar ───────────────────────────────
    function initKeluar() {
        if (tableKeluar) {
            tableKeluar.ajax.url('{{ route("lap-rekap.showKeluar") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load(function() {
                $('#badge_keluar').text(tableKeluar.page.info().recordsTotal);
            });
            return;
        }
        tableKeluar = $('#table_keluar').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            order: [],
            dom: "<'row'<'col-sm-12 table-responsive'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            stateSave: false,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ajax: {
                url: '{{ route("lap-rekap.showKeluar") }}',
                data: function(d) {
                    d.bulan = bulanAktif;
                    d.tahun = tahunAktif;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tgl', name: 'bk_tanggal' },
                { data: 'bk_kode', name: 'bk_kode' },
                {
                    data: 'barang', name: 'barang_nama',
                    render: function(data) { return (data || '-').split(' - ')[0]; }
                },
                {
                    data: 'barang', name: 'barang_nama',
                    orderable: false, searchable: false,
                    render: function(data) { let p = (data||'-').split(' - '); return p[1]||'-'; }
                },
                {
                    data: null, orderable: false,
                    render: function(data, type, row) { return row.kode_barang_unik || '-'; }
                },
                {
                    data: null, name: 'serial_number', orderable: false,
                    render: function(data, type, row) {
                        var sn = (row.serial_number && typeof row.serial_number === 'string')
                            ? row.serial_number.replace(/<[^>]*>?/gm, '') : row.serial_number;
                        return (sn && sn !== '-' && sn !== 'Tanpa SN') ? sn : '-';
                    }
                },
                { data: 'tujuan', name: 'bk_tujuan' },
                { data: 'lokasi', name: 'bk_lokasi' },
                { data: 'teknisi', name: 'teknisi' },
                { data: 'keterangan', name: 'keterangan', orderable: false, searchable: false },
                { data: 'status_badge', name: 'bk_status', orderable: false, searchable: false },
            ],
            drawCallback: function() {
                $('#badge_keluar').text(this.api().page.info().recordsTotal);
            }
        });
    }

    // ── Inisialisasi DataTable Stok ─────────────────────────────────
    function initStok() {
        if (tableStok) {
            tableStok.ajax.url('{{ route("lap-rekap.showStok") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load(function() {
                $('#badge_stok').text(tableStok.page.info().recordsTotal);
            });
            return;
        }
        tableStok = $('#table_stok').DataTable({
            processing: true,
            serverSide: true,
            info: true,
            order: [],
            dom: "<'row'<'col-sm-12 table-responsive'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            stateSave: false,
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ajax: {
                url: '{{ route("lap-rekap.showStok") }}',
                data: function(d) {
                    d.bulan = bulanAktif;
                    d.tahun = tahunAktif;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'barang_kode', name: 'barang_kode' },
                {
                    data: 'barang_nama', name: 'barang_nama',
                    render: function(data) { return (data || '-').split(' - ')[0]; }
                },
                {
                    data: 'merk_nama', name: 'merk_nama',
                    orderable: false, searchable: false,
                    render: function(data) { return data || '-'; }
                },
                { data: 'jenis', name: 'tbl_jenisbarang.jenisbarang_nama' },
                { data: 'satuan', name: 'tbl_barang.satuan_id' },
                { data: 'jmlmasuk', name: 'jmlmasuk', orderable: false },
                { data: 'jmlkeluar', name: 'jmlkeluar', orderable: false, searchable: false },
                {
                    data: 'totalstok', name: 'totalstok', orderable: false, searchable: false,
                    render: function(data) {
                        let clean = String(data).replace(/<[^>]*>?/gm, '').trim();
                        let stok  = parseInt(clean);
                        let color = stok < 5 ? '#e82646' : (stok <= 10 ? '#f7b731' : '#09ad95');
                        return '<span style="color:' + color + ';font-weight:bold;">' + stok + '</span>';
                    }
                },
                { data: 'status', name: 'status', orderable: false, searchable: false },
            ],
            drawCallback: function() {
                $('#badge_stok').text(this.api().page.info().recordsTotal);
            }
        });
    }

    // ── Inisialisasi semua tabel ────────────────────────────────────
    function initSemua() {
        initMasuk();
        initKeluar();
        initStok();
        updateJudul();
    }

    // ── Update judul halaman ────────────────────────────────────────
    function updateJudul() {
        var teks = namaBulan[bulanAktif] + ' ' + tahunAktif;
        $('#judul_periode').text(teks);
    }

    // ── Tombol Tampilkan ────────────────────────────────────────────
    function filterSemua() {
        bulanAktif = $('#vis_bulan').val();
        tahunAktif = $('#vis_tahun').val();

        tableMasuk.ajax.url('{{ route("lap-rekap.showMasuk") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load();
        tableKeluar.ajax.url('{{ route("lap-rekap.showKeluar") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load();
        tableStok.ajax.url('{{ route("lap-rekap.showStok") }}?bulan=' + bulanAktif + '&tahun=' + tahunAktif).load();

        updateJudul();
    }

    // ── Tombol Reset ────────────────────────────────────────────────
    function resetSemua() {
        bulanAktif = '{{ date("m") }}';
        tahunAktif = '{{ date("Y") }}';
        $('#vis_bulan').val(bulanAktif).trigger('change');
        $('#vis_tahun').val(tahunAktif).trigger('change');
        filterSemua();
    }

    // ── Export Excel (1 file, 3 sheet) ──────────────────────────────
    function exportExcelRekap() {
        var b = $('#vis_bulan').val();
        var t = $('#vis_tahun').val();
        window.open('{{ route("lap-rekap.excel") }}?bulan=' + b + '&tahun=' + t, '_blank');
    }
</script>
@endsection
