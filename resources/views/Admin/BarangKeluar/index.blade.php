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
    <div>
        @if($roleId == 3)
            <h1 class="page-title">Peminjaman Barang</h1>
        @else
            <h1 class="page-title">Barang Keluar</h1>
        @endif
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Transaksi</li>
            <li class="breadcrumb-item active">{{ $roleId == 3 ? 'Peminjaman Barang' : 'Barang Keluar' }}</li>
        </ol>
    </div>
    @if ($hakTambah > 0 && $roleId != 3)
    <div class="ms-auto">
        <a class="modal-effect btn btn-primary" onclick="generateID()" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modaldemo8">
            <i class="fe fe-plus me-1"></i> Tambah Data 
        </a>
    </div>
    @endif
</div>

{{-- INFO BAR KHUSUS TEKNISI --}}
@if($roleId == 3)
<div class="teknisi-info-bar">
    <div class="ti-icon"><i class="fe fe-user text-white"></i></div>
    <div>
        <div class="fw-bold" style="font-size:1rem;">Halo, {{ Session::get('user')->user_nmlengkap }}!</div>
        <div style="font-size:0.82rem; opacity:0.85;">Anda hanya dapat melihat & membuat peminjaman atas nama sendiri. ID Teknisi: <strong>{{ Session::get('user')->teknisi_sn ?? '-' }}</strong></div>
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
            </div>
            <div class="card-body">
                <!-- Search/Filter Bar Template (injected via DataTables DOM) -->
                <div id="custom-search-html" style="display: none;">
                    <div class="d-flex align-items-center w-100">
                        <div class="input-group input-group-sm w-100" style="min-width: 250px;">
                            <input type="text" id="bkSearchInput" class="form-control" placeholder="Pencarian...">
                            <button class="btn btn-primary" onclick="doSearchBK()"><i class="fe fe-search"></i></button>
                            <button class="btn btn-light border" onclick="resetSearchBK()"><i class="fe fe-x"></i></button>
                        </div>
                    </div>
                </div>
                <div class="w-100">
                    <table id="table-1" class="table table-bordered text-nowrap border-bottom dataTable no-footer dtr-inline collapsed">
                        <thead>
                            <th class="border-bottom-0" width="1%"></th>
                            <th class="border-bottom-0" width="1%">No</th>
                            <th class="border-bottom-0">Tanggal &amp; Jam Keluar</th>
                            <th class="border-bottom-0">Kode BK</th>
                            <th class="border-bottom-0">Teknisi</th>
                            <th class="border-bottom-0">Tujuan &amp; Lokasi</th>
                            <th class="border-bottom-0">Total Unit</th>
                            <th class="border-bottom-0">Keterangan</th>
                            <th class="border-bottom-0">Status</th>
                            <th class="border-bottom-0" width="1%">Aksi</th>
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
        // Default lokasi PT Alfatindo Teknologi
        if (!$("input[name='lokasi']").val()) {
            $("input[name='lokasi'], #lokasiInput").val('PT Alfatindo Teknologi');
            $("input[name='map_url'], #mapUrlInput").val('https://maps.app.goo.gl/iaQ52BrTGEfoVEP37');
        }
    }

    function update(data) {
        $("input[name='idbkU']").val(data.bk_id);
        $("input[name='bkkodeU']").val(data.bk_kode);
        $("input[name='batchCountU']").val(data.batch_count);
        $("input[name='kdbarangU']").val(data.barang_kode);
        $("select[name='tujuanU']").val(data.teknisi_nama || '').trigger('change');
        $("input[name='teknisiU']").val(data.teknisi);
        $("input[name='jmlU']").val(data.bk_jumlah);
        
        // Show/hide select2 wrapper dynamically depending on serial number usage
        if (data.serial_number && data.serial_number !== '-') {
            $("#sn_wrapperU").show();
            $("#serial_number_inputU").hide().val('');
        } else {
            $("#sn_wrapperU").hide();
            $("#serial_number_inputU").show().val('-');
        }

        $("input[name='keteranganU']").val(data.keterangan);
        $("input[name='customerU']").val(data.bk_tujuan || '');
        $("input[name='lokasiU']").val(data.bk_lokasi || '');
        $("input[name='tglkeluarU']").val(data.created_at);
        // Pre-fill barang nama immediately from action data (no AJAX wait)
        if (data.barang_nama) {
            $("#nmbarangU").val(data.barang_nama);
            $("#statusU").val("true");
        }
        // Async fill satuan/jenis
        getbarangbyidU(data.barang_kode);
        fetchAvailableSNsU(data.barang_kode, data.serial_number, data.kode_barang_unik);
        if (data.teknisi) {
            if (typeof getTeknisiInfoU === 'function') {
                getTeknisiInfoU(data.teknisi);
            }
        }
    }

    function fetchAvailableSNsU(barang_kode, currentSN, currentKBU) {
        if (!barang_kode) {
            $("#sn_listU").empty().trigger('change');
            return;
        }
        $.ajax({
            type: 'GET',
            url: "/admin/barang/get-available-sn/" + barang_kode,
            dataType: 'json',
            success: function(data) {
                var list = $("#sn_listU");
                list.empty();
                
                var displayKBU = (currentKBU && currentKBU !== '-') ? currentKBU : currentSN;
                
                if (displayKBU && displayKBU !== '-') {
                    var hasCurrent = data.some(item => item.serial_number === displayKBU || item.kode_barang_unik === displayKBU);
                    if (!hasCurrent) {
                        list.append(`<option value="${displayKBU}">${displayKBU}</option>`);
                    }
                }
                
                data.forEach(function(item) {
                    var optionValue = item.kode_barang_unik || item.serial_number;
                    var labelText = optionValue;
                    if (item.kondisi) {
                        labelText += ' - ' + item.kondisi;
                    }
                    list.append(`<option value="${optionValue}">${labelText}</option>`);
                });
                
                // Select the current SN and update Select2 display
                list.val(displayKBU).trigger('change');
            }
        });
    }

    function hapus(data) {
        $("input[name='idbk']").val(data.bk_id);
        
        let identifier = "";
        if (data.serial_number && data.serial_number !== '-') {
            // hapus html span atau em yang mungkin terbawa dari controller
            let cleanSN = data.serial_number.replace(/<[^>]*>?/gm, ''); 
            if(cleanSN === 'Tanpa SN' || cleanSN === '-') {
                identifier = "Kode Unik <b>" + data.kode_barang_unik + "</b>";
            } else {
                identifier = "SN <b>" + cleanSN + "</b>";
            }
        } else if (data.kode_barang_unik && data.kode_barang_unik !== '-') {
            identifier = "Kode Unik <b>" + data.kode_barang_unik + "</b>";
        } else {
            identifier = "Kode BK <b>" + data.bk_kode + "</b>";
        }

        $("#vbk").html(identifier);
    }

    // Fungsi kembali() ada di kembali.blade.php (di-include di bawah)

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
    .btn-expand-bk {
        width: 28px;
        height: 28px;
        padding: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn-expand-bk.expanded {
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
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    function formatChildBK(data, isParentChecked) {
        if (!data || data.length === 0) {
            return '<div class="child-row-table"><p class="text-muted mb-0 py-2 text-center">Tidak ada data Barang/SN.</p></div>';
        }
        let html = '<div class="child-row-table"><table>';
        html += '<thead><tr><th width="1%">#</th><th>Kode Barang</th><th>Nama Barang</th><th>Merk</th><th>Serial Number</th><th>Kode Unik</th><th>Status</th><th width="10%">Action</th></tr></thead><tbody>';
        data.forEach(function(row, i) {
            let parts = (row.barang_nama || '-').split(' - ');
            let nama = parts[0];
            let merk = parts[1] || '-';
            html += '<tr>';
            html += '<td class="text-center">' + (i + 1) + '</td>';
            html += '<td><span class="badge bg-secondary-light text-secondary">' + (row.barang_kode || '-') + '</span></td>';
            html += '<td>' + nama + '</td>';
            html += '<td>' + merk + '</td>';
            html += '<td><code>' + (row.serial_number || '-') + '</code></td>';
            html += '<td><span class="badge bg-info-light text-info">' + (row.kode_barang_unik || '-') + '</span></td>';
            html += '<td>' + (row.status || '-') + '</td>';
            html += '<td class="text-center">' + (row.action || '-') + '</td>';
            html += '</tr>';
        });
        html += '</tbody></table></div>';
        return html;
    }

    var table;
    $(document).ready(function() {
        table = $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "order": [],

            "stateSave": true,
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
                $('#custom-search-container').find('#bkSearchInput').on('keypress', function(e) {
                    if (e.which === 13) doSearchBK();
                });
            },
            "ajax": {
                "url": "{{ route('barang-keluar.getbarang-keluar') }}",
                "data": function(d) {
                    d.search = d.search || {};
                    d.search.value = typeof bkSearchTerm !== 'undefined' ? bkSearchTerm : '';
                }
            },
            "drawCallback": function(settings) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl, { html: true, sanitize: false })
                });

                // Re-attach expand button events after each draw
                $('#table-1 tbody').off('click', '.btn-expand-bk').on('click', '.btn-expand-bk', function(e) {
                    e.stopPropagation();
                    var btn        = $(this);
                    var tr         = btn.closest('tr');
                    var row        = table.row(tr);
                    var bkKode = btn.data('bk-kode');

                    if (row.child.isShown()) {
                        row.child.hide();
                        btn.removeClass('expanded');
                    } else {
                        btn.addClass('expanded');
                        row.child('<div class="text-center py-3"><i class="fe fe-loader"></i> Memuat...</div>').show();

                        $.ajax({
                            url: "{{ url('admin/barang-keluar/detail-sn/all') }}/" + encodeURIComponent(bkKode),
                            method: 'GET',
                            success: function(data) {
                                row.child(formatChildBK(data)).show();
                                // Re-init popovers in child row
                                var pops = [].slice.call(row.child().find('[data-bs-toggle="popover"]'));
                                pops.forEach(function(el) { new bootstrap.Popover(el, { html: true, sanitize: false }); });
                            },
                            error: function() {
                                row.child('<div class="text-center py-2 text-danger">Gagal memuat data.</div>').show();
                            }
                        });
                    }
                });
            },
            "columns": [
                {
                    data: 'expand',
                    orderable: false,
                    searchable: false,
                    render: function(data) { return data || ''; }
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false , orderable: false },
                { data: 'tgl',        name: 'created_at' },
                { data: 'bk_kode',    name: 'bk_kode' },
                { data: 'teknisi',    name: 'teknisi_nama' },
                { data: 'tujuan',     name: 'bk_tujuan' },
                { data: 'total_unit', name: 'total_unit', searchable: false },
                { data: 'keterangan', name: 'keterangan', orderable: false, searchable: false },
                { data: 'status',     name: 'bk_status' },
                { data: 'action',     name: 'action', orderable: false, searchable: false },
            ],
        });
    });

    function terimaPinjam(id) {
        swal({
            title: "Setujui Pinjaman?",
            text: "Barang akan resmi dipinjam oleh Teknisi.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ya, Setujui!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.post(`/admin/barang-keluar/terima_pinjam/${id}`, function(res) {
                    swal("Disetujui!", res.success, "success");
                    table.ajax.reload();
                }).fail(function(err) {
                    swal("Gagal!", err.responseJSON.error || "Terjadi kesalahan", "error");
                });
            }
        });
    }

    function tolakPinjam(id) {
        swal({
            title: "Tolak Pinjaman?",
            text: "Pengajuan pinjaman ini akan ditolak.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Tolak!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.post(`/admin/barang-keluar/tolak_pinjam/${id}`, function(res) {
                    swal("Ditolak!", res.success, "success");
                    table.ajax.reload();
                }).fail(function(err) {
                    swal("Gagal!", err.responseJSON.error || "Terjadi kesalahan", "error");
                });
            }
        });
    }

    function terimaKembali(id) {
        swal({
            title: "Setujui Pengembalian?",
            text: "Barang akan dinyatakan selesai dikembalikan dan stok diperbarui.",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ya, Setujui!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.post(`/admin/barang-keluar/terima_kembali/${id}`, function(res) {
                    swal("Berhasil!", res.success, "success");
                    table.ajax.reload();
                }).fail(function(err) {
                    swal("Gagal!", err.responseJSON.error || "Terjadi kesalahan", "error");
                });
            }
        });
    }

    function tolakKembali(id) {
        swal({
            title: "Tolak Pengembalian?",
            text: "Status barang akan dikembalikan menjadi 'Dipinjam'.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Tolak!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.post(`/admin/barang-keluar/tolak_kembali/${id}`, function(res) {
                    swal("Ditolak!", res.success, "success");
                    table.ajax.reload();
                }).fail(function(err) {
                    swal("Gagal!", err.responseJSON.error || "Terjadi kesalahan", "error");
                });
            }
        });
    }

    function hapusTransaksi(bkKode) {
        swal({
            title: "Hapus Transaksi?",
            text: "Semua barang (unit) pada transaksi " + bkKode + " ini akan dihapus permanen dan stok akan dikembalikan.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Ya, Hapus Semua!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{ url('admin/barang-keluar/hapus-transaksi') }}/" + encodeURIComponent(bkKode),
                    type: "POST",
                    success: function(res) {
                        swal("Berhasil!", res.success, "success");
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        swal("Gagal!", err.responseJSON?.error || "Terjadi kesalahan", "error");
                    }
                });
            }
        });
    }

    function batchKembaliPerBK(bkKode) {
        swal({
            title: "Batch Pengembalian?",
            text: "Semua barang pada transaksi " + bkKode + " akan ditandai sebagai 'Selesai' (kembali). Lanjutkan?",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            confirmButtonText: "Ya, Kembalikan Semua!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{ url('admin/barang-keluar/batch-kembali') }}/" + encodeURIComponent(bkKode),
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        swal("Berhasil!", res.success || "Semua barang berhasil dikembalikan.", "success");
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        swal("Gagal!", err.responseJSON?.error || "Terjadi kesalahan", "error");
                    }
                });
            }
        });
    }

    var bkSearchTerm = '';
    function doSearchBK() {
        bkSearchTerm = $('#custom-search-container').find('#bkSearchInput').val().trim();
        table.ajax.reload();
    }

    function resetSearchBK() {
        bkSearchTerm = '';
        $('#custom-search-container').find('#bkSearchInput').val('');
        table.ajax.reload();
    }
</script>
@endsection
