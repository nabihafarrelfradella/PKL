<!-- MODAL TAMBAH -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Tambah Jenis Barang</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="jenisbarang" class="form-label">Jenis Barang (Nama) <span class="text-danger">*</span></label>
                    <select name="jenisbarang" class="form-control">
                        <option value="">-- Pilih Jenis Barang --</option>
                        <option value="Barang Habis Pakai">Barang Habis Pakai</option>
                        <option value="Barang Kembali">Barang Kembali</option>
                    </select>
                    <small class="text-muted">Jenis barang menentukan apakah barang perlu dikembalikan atau tidak.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary d-none" id="btnLoader" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="checkForm()" id="btnSimpan" class="btn btn-primary">Simpan <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" onclick="reset()" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formTambahJS')
<script>
    function checkForm() {
        const jenis = $("select[name='jenisbarang']").val();
        setLoading(true);
        resetValid();

        if (jenis == "") {
            validasi('Jenis Barang wajib di pilih!', 'warning');
            $("select[name='jenisbarang']").addClass('is-invalid');
            setLoading(false);
            return false;
        } else {
            submitForm();
        }
    }

    function submitForm() {
        const jenis = $("select[name='jenisbarang']").val();

        $.ajax({
            type: 'POST',
            url: "{{route('jenisbarang.store')}}",
            enctype: 'multipart/form-data',
            data: {
                jenisbarang: jenis
            },
            success: function(data) {
                $('#modaldemo8').modal('toggle');
                swal({
                    title: "Berhasil ditambah!",
                    type: "success"
                });
                table.ajax.reload(null, false);
                reset();
                
            }
        });
    }

    function resetValid() {
        $("select[name='jenisbarang']").removeClass('is-invalid');
    };

    function reset() {
        resetValid();
        $("select[name='jenisbarang']").val('');
        setLoading(false);
    }

    function setLoading(bool) {
        if (bool == true) {
            $('#btnLoader').removeClass('d-none');
            $('#btnSimpan').addClass('d-none');
        } else {
            $('#btnSimpan').removeClass('d-none');
            $('#btnLoader').addClass('d-none');
        }
    }
</script>
@endsection