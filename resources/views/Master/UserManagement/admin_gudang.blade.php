@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Admin Gudang</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active">Admin Gudang</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<div class="row row-sm">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fe fe-shield me-1 text-primary"></i> Akun Admin Gudang
                </h3>
                <small class="text-muted">Hanya 1 akun Admin Gudang yang diperbolehkan dalam sistem</small>
            </div>
            <div class="card-body">
                @if($adminGudang)
                <!-- Tampilan data Admin Gudang -->
                <div class="d-flex align-items-center mb-4">
                    <span class="avatar avatar-xl cover-image me-3"
                          style="background: url('{{ $adminGudang->user_foto == "undraw_profile.svg" ? url("/assets/default/users/" . $adminGudang->user_foto) : asset("storage/users/" . $adminGudang->user_foto) }}') center center;">
                    </span>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $adminGudang->user_nmlengkap }}</h5>
                        <span class="badge bg-info">{{ $adminGudang->role_title }}</span>
                        <p class="text-muted mb-0 small mt-1">{{ $adminGudang->user_email }}</p>
                    </div>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted fw-semibold" width="40%">Username</td>
                        <td>{{ $adminGudang->user_nama }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Email</td>
                        <td>{{ $adminGudang->user_email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Role</td>
                        <td><span class="badge bg-primary">{{ $adminGudang->role_title }}</span></td>
                    </tr>
                </table>

                <hr>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" href="#modalEditAdminGudang">
                    <i class="fe fe-edit me-1"></i> Edit Akun Admin Gudang
                </button>
                <small class="text-muted d-block mt-2">
                    <i class="fe fe-info me-1"></i> Tidak tersedia tombol Hapus — akun ini bersifat tetap dalam sistem.
                </small>
                @else
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-1"></i>
                    Belum ada akun Admin Gudang yang terdaftar di sistem. Hubungi developer untuk membuat akun awal.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Keterangan Role -->
    <div class="col-md-4 col-lg-6">
        <div class="card bg-primary-transparent">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fe fe-info me-1"></i> Tentang Admin Gudang</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Akses hampir semua fitur</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Master Barang</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Transaksi (Masuk & Keluar)</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Lihat & cetak Laporan</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Konfirmasi pengembalian barang</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa akses User Management</li>
                    <li class="mb-2"><i class="fe fe-info text-warning me-2"></i> Hanya 1 akun diperbolehkan</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT ADMIN GUDANG -->
@if($adminGudang)
<div class="modal fade" id="modalEditAdminGudang" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-edit me-1"></i> Edit Akun Admin Gudang</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fe fe-info me-1"></i> Role Admin Gudang tidak dapat diubah.
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="ag_nmlengkap" class="form-control" value="{{ $adminGudang->user_nmlengkap }}">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" id="ag_username" class="form-control" value="{{ $adminGudang->user_nama }}">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" id="ag_email" class="form-control" value="{{ $adminGudang->user_email }}">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Password Baru <span class="text-muted">(kosongkan jika tidak diganti)</span></label>
                    <input type="password" id="ag_pwd" class="form-control" placeholder="Password baru">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="submitEditAdminGudang({{ $adminGudang->user_id }})">
                    <i class="fe fe-check me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    function submitEditAdminGudang(userId) {
        $.ajax({
            type: 'POST',
            url: "{{ url('/admin/user-management/admin-gudang/update') }}/" + userId,
            data: {
                nmlengkap: $('#ag_nmlengkap').val(),
                username:  $('#ag_username').val(),
                email:     $('#ag_email').val(),
                pwd:       $('#ag_pwd').val()
            },
            success: function (res) {
                $('#modalEditAdminGudang').modal('hide');
                swal({ title: res.success, type: 'success' }, function () {
                    location.reload();
                });
            },
            error: function () {
                swal({ title: 'Gagal memperbarui!', type: 'error' });
            }
        });
    }
</script>
@endsection
