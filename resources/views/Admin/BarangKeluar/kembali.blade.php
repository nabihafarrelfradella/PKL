<!-- MODAL PENGEMBALIAN -->
<div class="modal fade" data-bs-backdrop="static" id="Kmodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Pengembalian Barang</h6><button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="idbkK">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bkkodeK" class="form-label">Kode Barang Keluar</label>
                            <input type="text" name="bkkodeK" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="nmbarangK" class="form-label">Nama Barang</label>
                            <input type="text" id="nmbarangK" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="tglkembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tglkembali" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="jmlK" class="form-label">Jumlah Kembali <span class="text-danger">*</span></label>
                            <input type="number" name="jmlK" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="kondisi" class="form-label">Kondisi Barang <span class="text-danger">*</span></label>
                            <select name="kondisi" class="form-control">
                                <option value="">-- Pilih --</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-info d-none" id="btnLoaderK" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="checkFormK()" id="btnSimpanK" class="btn btn-info">Simpan Pengembalian <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formKembaliJS')
<script>
    function kembali(data) {
        $("input[name='idbkK']").val(data.bk_id);
        $("input[name='bkkodeK']").val(data.bk_kode);
        $("#nmbarangK").val(data.barang_nama);
        $("input[name='jmlK']").val(data.bk_jumlah);
    }

    function checkFormK() {
        const tgl = $("input[name='tglkembali']").val();
        const jml = $("input[name='jmlK']").val();
        const kondisi = $("select[name='kondisi']").val();
        setLoadingK(true);
        resetValidK();
        if (tgl == "") {
            validasi('Tanggal Kembali wajib di isi!', 'warning');
            $("input[name='tglkembali']").addClass('is-invalid');
            setLoadingK(false);
            return false;
        } else if (jml == "") {
            validasi('Jumlah Kembali wajib di isi!', 'warning');
            $("input[name='jmlK']").addClass('is-invalid');
            setLoadingK(false);
            return false;
        } else if (kondisi == "") {
            validasi('Kondisi Barang wajib di isi!', 'warning');
            $("select[name='kondisi']").addClass('is-invalid');
            setLoadingK(false);
            return false;
        } else {
            submitFormK();
        }
    }

    function submitFormK() {
        const id = $("input[name='idbkK']").val();
        const tgl = $("input[name='tglkembali']").val();
        const jml = $("input[name='jmlK']").val();
        const kondisi = $("select[name='kondisi']").val();

        $.ajax({
            type: 'POST',
            url: "{{url('admin/barang-keluar/proses_kembali')}}/" + id,
            data: {
                tglkembali: tgl,
                jml: jml,
                kondisi: kondisi,
                _token: "{{csrf_token()}}"
            },
            success: function(data) {
                swal({
                    title: "Berhasil dikembalikan!",
                    type: "success"
                });
                $('#Kmodaldemo8').modal('toggle');
                table.ajax.reload(null, false);
                resetK();
            }
        });
    }

    function resetValidK() {
        $("input[name='tglkembali']").removeClass('is-invalid');
        $("input[name='jmlK']").removeClass('is-invalid');
        $("select[name='kondisi']").removeClass('is-invalid');
    };

    function resetK() {
        resetValidK();
        $("input[name='idbkK']").val('');
        $("input[name='bkkodeK']").val('');
        $("#nmbarangK").val('');
        $("input[name='tglkembali']").val('');
        $("input[name='jmlK']").val('');
        $("select[name='kondisi']").val('');
        setLoadingK(false);
    }

    function setLoadingK(bool) {
        if (bool == true) {
            $('#btnLoaderK').removeClass('d-none');
            $('#btnSimpanK').addClass('d-none');
        } else {
            $('#btnSimpanK').removeClass('d-none');
            $('#btnLoaderK').addClass('d-none');
        }
    }
</script>
@endsection
