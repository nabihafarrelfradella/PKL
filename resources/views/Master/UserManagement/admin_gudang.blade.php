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

@if(Session::has('status'))
<div class="alert alert-{{ Session::get('status') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
    <i class="fe fe-{{ Session::get('status') == 'success' ? 'check-circle' : 'alert-circle' }} me-1"></i>
    {{ Session::get('msg') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row row-sm">
    <!-- Kartu Akun Admin Gudang -->
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom-0 pt-5 pb-2 d-flex justify-content-center">
                <h3 class="card-title mb-0 fw-bold"><i class="fe fe-shield text-primary me-2"></i>Akun Admin Gudang</h3>
            </div>
            <div class="card-body px-5 pb-5 pt-3">
                @if($adminGudang)
                {{-- Foto & Info Utama --}}
                <div class="d-flex flex-column align-items-center mb-5 text-center">
                    <span class="avatar avatar-xxl cover-image mb-3 shadow-sm"
                        onclick="lihatFotoAG('{{ $adminGudang->user_foto == "undraw_profile.svg" ? url("/assets/default/users/" . $adminGudang->user_foto) : asset("storage/users/" . $adminGudang->user_foto) }}')"
                        style="background: url('{{ $adminGudang->user_foto == "undraw_profile.svg" ? url("/assets/default/users/" . $adminGudang->user_foto) : asset("storage/users/" . $adminGudang->user_foto) }}') center center; width: 90px; height: 90px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important; cursor: pointer;">
                    </span>
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">{{ $adminGudang->user_nmlengkap }}</h4>
                        <p class="text-muted mb-2">{{ $adminGudang->user_email }}</p>
                        <span class="badge bg-primary-transparent text-primary px-3 py-1 rounded-pill fw-semibold">{{ $adminGudang->role_title }}</span>
                    </div>
                </div>

                {{-- Detail Akun --}}
                <div class="bg-light rounded-3 p-4 mb-4 border">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted fw-semibold py-2" style="width: 35%">Username</td>
                            <td class="py-2"><span class="fw-bold text-dark">{{ $adminGudang->user_nama }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold py-2">Role Akses</td>
                            <td class="py-2"><span class="fw-bold text-dark">{{ $adminGudang->role_title }}</span></td>
                        </tr>
                    </table>
                </div>

                <button class="btn btn-primary w-100 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEditAdminGudang">
                    <i class="fe fe-edit me-2"></i> Edit Akun Admin Gudang
                </button>

                @else
                <div class="alert alert-warning text-center">
                    <i class="fe fe-alert-triangle me-1"></i>
                    Belum ada akun Admin Gudang. Hubungi developer untuk membuat akun awal.
                </div>
                @endif
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
                <h6 class="modal-title"><i class="fe fe-edit me-1"></i> Edit Akun Admin Gudang</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 mb-3">
                    <i class="fe fe-lock me-1"></i> Role <strong>Admin Gudang</strong> tidak dapat diubah.
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
                <div class="form-group mb-0">
                    <label class="form-label">Foto Profil <span class="text-muted">(Opsional)</span></label>
                    <div class="input-group">
                        <input type="file" id="ag_foto" class="form-control" accept="image/*" onclick="this.value=null;" onchange="previewFotoAG(this)">
                        <button type="button" class="btn btn-primary" onclick="openWebcamModal('ag_foto', 'imgViewAGTemp', true)">
                            <i class="fe fe-camera"></i> Buka Kamera
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah foto</small>
                    <input type="hidden" id="ag_remove_photo" value="0">
                    <div class="mt-2 position-relative d-inline-block">
                        <img id="imgViewAGTemp" class="img-thumbnail {{ $adminGudang->user_foto != 'undraw_profile.svg' ? '' : 'd-none' }}" 
                             src="{{ $adminGudang->user_foto != 'undraw_profile.svg' ? asset('storage/users/' . $adminGudang->user_foto) : '' }}" 
                             onclick="lihatFotoAG(this.src)"
                             style="max-height: 150px; min-width: 120px; min-height: 120px; object-fit: contain; cursor: pointer;"
                             onerror="this.onerror=null; this.src='/assets/default/users/undraw_profile.svg';">
                        <button type="button" id="btnHapusFotoAG" class="btn btn-danger btn-sm position-absolute {{ $adminGudang->user_foto != 'undraw_profile.svg' ? '' : 'd-none' }}" style="top: -10px; right: -10px; border-radius: 50%; padding: 2px 6px; z-index: 10;" onclick="hapusFotoAG()"><i class="fe fe-x"></i></button>
                    </div>
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

<!-- MODAL PREVIEW FOTO -->
<div class="modal fade" id="modalFotoAG" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-image me-1"></i> Preview Foto</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="imgViewAGLarge" src="" alt="Preview Foto" class="img-fluid shadow-sm" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

function previewFotoAG(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#imgViewAGTemp').attr('src', e.target.result).removeClass('d-none');
            $('#ag_remove_photo').val('0'); // Batal hapus jika pilih baru
            $('#btnHapusFotoAG').removeClass('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function lihatFotoAG(url) {
    if(url && url !== '') {
        $('#imgViewAGLarge').attr('src', url);
        $('#modalFotoAG').modal('show');
    }
}

function hapusFotoAG() {
    $('#ag_foto').val('');
    $('#imgViewAGTemp').addClass('d-none').attr('src', '');
    $('#ag_remove_photo').val('1'); // Set flag hapus
    $('#btnHapusFotoAG').addClass('d-none');
}

// Reset modal state ketika ditutup tanpa menyimpan
$(document).ready(function() {
    $('#modalEditAdminGudang').on('hidden.bs.modal', function () {
        $('#ag_foto').val('');
        $('#ag_remove_photo').val('0');
        
        const originalFoto = '{{ $adminGudang->user_foto ?? "" }}';
        if (originalFoto && originalFoto !== 'undraw_profile.svg') {
            $('#imgViewAGTemp').removeClass('d-none').attr('src', '{{ asset("storage/users") }}/' + originalFoto);
            $('#btnHapusFotoAG').removeClass('d-none');
        } else {
            $('#imgViewAGTemp').addClass('d-none').attr('src', '');
            $('#btnHapusFotoAG').addClass('d-none');
        }
    });
});

function submitEditAdminGudang(userId) {
    const nmlengkap = $('#ag_nmlengkap').val().trim();
    const username  = $('#ag_username').val().trim();
    const email     = $('#ag_email').val().trim();

    if (!nmlengkap || !username || !email) {
        swal({ title: 'Form tidak lengkap!', text: 'Nama, username, dan email wajib diisi.', type: 'warning' });
        return;
    }

    const formData = new FormData();
    formData.append('nmlengkap', nmlengkap);
    formData.append('username',  username);
    formData.append('email',     email);
    formData.append('pwd',       $('#ag_pwd').val());
    formData.append('remove_photo', $('#ag_remove_photo').val());
    formData.append('_token',    $('meta[name="csrf-token"]').attr('content'));

    const fotoFile = $('#ag_foto')[0].files[0];
    if (fotoFile) {
        formData.append('foto', fotoFile);
    }

    $.ajax({
        type: 'POST',
        url: '{{ url("/admin/user-management/admin-gudang/update") }}/' + userId,
        data: formData,
        processData: false,
        contentType: false,
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
