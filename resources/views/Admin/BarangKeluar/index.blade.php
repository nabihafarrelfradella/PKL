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
                if (currentSN && currentSN !== '-') {
                    var hasCurrent = data.some(item => item.serial_number === currentSN);
                    if (!hasCurrent) {
                        var unikText = currentKBU ? ` (Unik: ${currentKBU})` : '';
                        list.append(`<option value="${currentSN}">${currentSN}${unikText}</option>`);
                    }
                }
                data.forEach(function(item) {
                    var unikText = item.kode_barang_unik ? ` (Unik: ${item.kode_barang_unik})` : '';
                    list.append(`<option value="${item.serial_number}">${item.serial_number}${unikText}</option>`);
                });
                
                // Select the current SN and update Select2 display
                list.val(currentSN).trigger('change');
            }
        });
    }

    function hapus(data) {
        $("input[name='idbk']").val(data.bk_id);
        $("#vbk").html("Kode BK " + "<b>" + data.bk_kode + "</b>");
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
            "drawCallback": function(settings) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl, {
                        html: true,
                        sanitize: false
                    })
                });
            },
            "columns": [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'tgl',
                    name: 'created_at',
                },{ data: 'bk_kode',      name: 'bk_kode' },
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
</script>
@endsection