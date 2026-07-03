@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Daftar Pegawai Teknisi</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active">Daftar Teknisi</li>
        </ol>
    </div>
    <div class="ms-auto">
        <a class="btn btn-primary" data-bs-toggle="modal" href="#modalTambahTeknisi">
            <i class="fe fe-user-plus me-1"></i> Tambah Teknisi
        </a>
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

<!-- TABEL TEKNISI -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Data Pegawai Teknisi</h3>
                <small class="text-muted ms-2">Owner dapat menambah, mengedit, dan menghapus data teknisi</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableTeknisi" class="table table-bordered text-nowrap border-bottom">
                        <thead>
                            <tr>
                                <th width="1%">No</th>
                                <th width="5%">Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Gender</th>
                                <th>Tgl Lahir</th>
                                <th>ID Teknisi</th>
                                <th width="1%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH TEKNISI -->
<div class="modal fade" id="modalTambahTeknisi" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-user-plus me-1"></i> Tambah Pegawai Teknisi</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="add_nmlengkap" class="form-control" placeholder="Nama lengkap teknisi">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="add_email" class="form-control" placeholder="Alamat email (opsional)">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" id="add_phone" class="form-control" placeholder="Nomor telepon (opsional)">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select id="add_jenis_kelamin" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="M">Laki-laki (M)</option>
                                <option value="F">Perempuan (F)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" id="add_tanggal_lahir" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Foto Teknisi (Opsional)</label>
                    <div class="input-group">
                        <input type="file" id="add_foto" class="form-control" accept="image/*" onclick="this.value=null;" onchange="previewFotoTeknisi(this, 'imgViewTeknisiTemp')">
                        <button type="button" class="btn btn-primary" onclick="openWebcamModal('add_foto', 'imgViewTeknisiTemp', true)">
                            <i class="fe fe-camera"></i> Buka Kamera
                        </button>
                    </div>
                    <img id="imgViewTeknisiTemp" class="mt-2 d-none" style="max-height: 150px; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnTambahTeknisi" onclick="submitTambahTeknisi()">
                    <i class="fe fe-check me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT TEKNISI -->
<div class="modal fade" id="modalEditTeknisi" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-edit me-1"></i> Edit Pegawai Teknisi</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_user_id">
                <div class="form-group mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" id="edit_nmlengkap" class="form-control">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" id="edit_email" class="form-control" placeholder="Alamat email (opsional)">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" id="edit_phone" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select id="edit_jenis_kelamin" class="form-control">
                                <option value="M">Laki-laki (M)</option>
                                <option value="F">Perempuan (F)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" id="edit_tanggal_lahir" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Foto Teknisi (Opsional)</label>
                    <div class="input-group">
                        <input type="file" id="edit_foto" class="form-control" accept="image/*" onclick="this.value=null;" onchange="previewFotoTeknisi(this, 'imgViewTeknisiTempU')">
                        <button type="button" class="btn btn-primary" onclick="openWebcamModal('edit_foto', 'imgViewTeknisiTempU', true)">
                            <i class="fe fe-camera"></i> Buka Kamera
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah foto</small>
                    <img id="imgViewTeknisiTempU" class="mt-2 d-none" style="max-height: 150px; object-fit: contain;">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-success" onclick="submitEditTeknisi()">
                    <i class="fe fe-check me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL HAPUS TEKNISI -->
<div class="modal fade" id="modalHapusTeknisi" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="fe fe-trash-2 me-1"></i> Hapus Teknisi</h6>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <input type="hidden" id="hapus_user_id">
                <p class="mb-1">Yakin hapus data teknisi ini?</p>
                <strong id="hapus_nama" class="text-danger"></strong>
                <p class="text-muted small mt-2">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger btn-sm" onclick="submitHapusTeknisi()">
                    <i class="fe fe-trash-2 me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FOTO TEKNISI -->
<div class="modal fade" id="modalFotoTeknisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-image me-1"></i> Foto Profil Teknisi</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="imgViewTeknisi" src="" alt="Foto Teknisi" class="img-fluid shadow-sm" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    function lihatFotoTeknisi(url) {
        $('#imgViewTeknisi').attr('src', url);
        $('#modalFotoTeknisi').modal('show');
    }

    function previewFotoTeknisi(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + imgId).attr('src', e.target.result).removeClass('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    var table;
    $(document).ready(function () {
        table = $('#tableTeknisi').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: "{{ route('user-mgmt.teknisi.show') }}" },
            columns: [
                { data: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'foto', searchable: false, orderable: false },
                { data: 'user_nmlengkap' },
                { data: 'user_email', defaultContent: '-' },
                { data: 'user_phone', defaultContent: '-' },
                { data: 'jenis_kelamin', defaultContent: '-' },
                { data: 'tanggal_lahir', defaultContent: '-' },
                { data: 'teknisi_sn', defaultContent: '-' },
                { data: 'action', searchable: false, orderable: false }
            ]
        });
    });

    function editTeknisi(data) {
        $('#edit_user_id').val(data.user_id);
        $('#edit_nmlengkap').val((data.user_nmlengkap || '').replace(/_/g, ' '));
        $('#edit_email').val(data.user_email);
        $('#edit_phone').val(data.user_phone || '');
        $('#edit_jenis_kelamin').val(data.jenis_kelamin || 'M');
        $('#edit_tanggal_lahir').val(data.tanggal_lahir || '');
        
        // Load existing photo
        if (data.user_foto && data.user_foto !== 'undraw_profile.svg') {
            $('#imgViewTeknisiTempU').attr('src', '/storage/users/' + data.user_foto).removeClass('d-none');
        } else {
            $('#imgViewTeknisiTempU').attr('src', '/assets/default/users/undraw_profile.svg').removeClass('d-none');
        }
        // Clear file input
        $('#edit_foto').val('');
    }

    function hapusTeknisi(data) {
        $('#hapus_user_id').val(data.user_id);
        $('#hapus_nama').text((data.user_nmlengkap || '').replace(/_/g, ' '));
    }

    function submitTambahTeknisi() {
        const nmlengkap     = $('#add_nmlengkap').val().trim();
        const email         = $('#add_email').val().trim();
        const jenis_kelamin = $('#add_jenis_kelamin').val();
        const tanggal_lahir = $('#add_tanggal_lahir').val();

        if (!nmlengkap || !jenis_kelamin || !tanggal_lahir) {
            swal({ title: 'Form tidak lengkap!', type: 'warning' });
            return;
        }

        // Cegah double submit
        const btn = $('#btnTambahTeknisi');
        if (btn.prop('disabled')) return;
        btn.prop('disabled', true).text('Menyimpan...');

        let formData = new FormData();
        formData.append('_token', "{{ csrf_token() }}");
        formData.append('nmlengkap', nmlengkap);
        formData.append('email', email);
        formData.append('phone', $('#add_phone').val());
        formData.append('jenis_kelamin', jenis_kelamin);
        formData.append('tanggal_lahir', tanggal_lahir);
        
        let file = $('#add_foto')[0].files[0];
        if (file) {
            formData.append('foto', file);
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('user-mgmt.teknisi.store') }}",
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalTambahTeknisi').modal('hide');
                swal({ title: res.success, type: 'success' });
                table.ajax.reload(null, false);
                $('#add_nmlengkap, #add_email, #add_phone').val('');
                $('#add_jenis_kelamin').val('');
                $('#add_tanggal_lahir').val('');
                $('#add_foto').val('');
                $('#imgViewTeknisiTemp').addClass('d-none').attr('src', '');
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0];
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                swal({ title: msg, type: 'error' });
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fe fe-check me-1"></i> Simpan');
            }
        });
    }

    function submitEditTeknisi() {
        const userId = $('#edit_user_id').val();
        let formData = new FormData();
        formData.append('nmlengkap', $('#edit_nmlengkap').val());
        formData.append('email', $('#edit_email').val());
        formData.append('phone', $('#edit_phone').val());
        formData.append('jenis_kelamin', $('#edit_jenis_kelamin').val());
        formData.append('tanggal_lahir', $('#edit_tanggal_lahir').val());
        
        let file = $('#edit_foto')[0].files[0];
        if (file) {
            formData.append('foto', file);
        }

        $.ajax({
            type: 'POST',
            url: "{{ url('/admin/user-management/teknisi/update') }}/" + userId,
            data: formData,
            contentType: false,
            processData: false,
            success: function (res) {
                $('#modalEditTeknisi').modal('hide');
                swal({ title: res.success, type: 'success' });
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                let msg = 'Gagal memperbarui!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors)[0][0];
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                swal({ title: msg, type: 'error' });
            }
        });
    }

    function submitHapusTeknisi() {
        const userId = $('#hapus_user_id').val();
        $.ajax({
            type: 'POST',
            url: "{{ url('/admin/user-management/teknisi/destroy') }}/" + userId,
            success: function (res) {
                $('#modalHapusTeknisi').modal('hide');
                swal({ title: res.success, type: 'success' });
                table.ajax.reload(null, false);
            },
            error: function () {
                swal({ title: 'Gagal menghapus!', type: 'error' });
            }
        });
    }
</script>
@endsection
