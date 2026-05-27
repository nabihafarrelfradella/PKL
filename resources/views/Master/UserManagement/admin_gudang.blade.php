@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Staff Gudang</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active">Staff Gudang</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

@if(Session::has('status'))
<div class="alert alert-{{ Session::get('status') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
    <i class="fe fe-{{ Session::get('status') == 'success' ? 'check-circle' : 'alert-circle' }} me-1"></i>
    {{ Session::get('msg') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row row-sm">
    <!-- Kartu Akun Staff Gudang -->
    <div class="col-md-7 col-lg-5">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fe fe-shield fs-18 text-primary"></i>
                <h3 class="card-title mb-0">Akun Staff Gudang</h3>
            </div>
            <div class="card-body">
                @if($adminGudang)
                {{-- Foto & Info Utama --}}
                <div class="d-flex align-items-center mb-4 gap-3">
                    <span class="avatar avatar-xl cover-image"
                        style="background: url('{{ $adminGudang->user_foto == "undraw_profile.svg" ? url("/assets/default/users/" . $adminGudang->user_foto) : asset("storage/users/" . $adminGudang->user_foto) }}') center center; min-width:60px; min-height:60px; border-radius:50%; border:3px solid #4a90d9;">
                    </span>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $adminGudang->user_nmlengkap }}</h5>
                        <span class="badge bg-primary">{{ $adminGudang->role_title }}</span>
                        <p class="text-muted mb-0 small mt-1">{{ $adminGudang->user_email }}</p>
                    </div>
                </div>

                {{-- Detail Akun --}}
                <table class="table table-sm table-borderless mb-4">
                    <tr>
                        <td class="text-muted fw-semibold" style="width:40%">Username</td>
                        <td><code>{{ $adminGudang->user_nama }}</code></td>
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

                <div class="alert alert-info py-2 mb-3" style="font-size:13px;">
                    <i class="fe fe-info me-1"></i>
                    Sistem hanya mengizinkan <strong>1 akun Staff Gudang</strong>. Owner hanya bisa mengedit akun ini.
                </div>

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditAdminGudang">
                    <i class="fe fe-edit me-1"></i> Edit Akun Staff Gudang
                </button>

                @else
                <div class="alert alert-warning">
                    <i class="fe fe-alert-triangle me-1"></i>
                    Belum ada akun Staff Gudang. Hubungi developer untuk membuat akun awal.
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Keterangan Role -->
    <div class="col-md-5 col-lg-7">
        <div class="card bg-primary-transparent h-100">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><i class="fe fe-info me-1"></i> Tentang Staff Gudang</h5>
                <ul class="list-unstyled mb-3">
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Master Barang (Jenis, Merk, Data)</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Transaksi (Masuk &amp; Keluar)</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Lihat &amp; cetak Laporan</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Konfirmasi pengembalian barang</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa akses User Management</li>
                    <li class="mb-2"><i class="fe fe-info text-warning me-2"></i> Hanya <strong>1 akun</strong> — tidak dapat ditambah</li>
                </ul>
                <hr>
                <p class="small text-muted mb-0">
                    <i class="fe fe-settings me-1"></i>
                    Konfigurasi hak akses detail via halaman
                    <a href="{{ route('user-mgmt.access-control') }}">Access Control</a>.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT (hanya jika akun ada) --}}
@if($adminGudang)
<div class="modal fade" id="modalEditAdminGudang" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-edit me-1"></i> Edit Akun Staff Gudang</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 mb-3">
                    <i class="fe fe-lock me-1"></i> Role <strong>Staff Gudang</strong> tidak dapat diubah.
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
    const nmlengkap = $('#ag_nmlengkap').val().trim();
    const username  = $('#ag_username').val().trim();
    const email     = $('#ag_email').val().trim();

    if (!nmlengkap || !username || !email) {
        swal({ title: 'Form tidak lengkap!', text: 'Nama, username, dan email wajib diisi.', type: 'warning' });
        return;
    }

    $.ajax({
        type: 'POST',
        url: '{{ url("/admin/user-management/admin-gudang/update") }}/' + userId,
        data: {
            nmlengkap: nmlengkap,
            username:  username,
            email:     email,
            pwd:       $('#ag_pwd').val()
        },
        success: function (res) {
            $('#modalEditAdminGudang').modal('hide');
            swal({ title: res.success, type: 'success' }, function () { location.reload(); });
        },
        error: function (xhr) {
            let msg = 'Gagal memperbarui!';
            if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON.errors)[0][0];
            else if (xhr.responseJSON?.error) msg = xhr.responseJSON.error;
            swal({ title: msg, type: 'error' });
        }
    });
}
</script>
@endsection
