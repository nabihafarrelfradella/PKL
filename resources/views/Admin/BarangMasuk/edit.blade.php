<!-- MODAL EDIT -->
<div class="modal fade" data-bs-backdrop="static" id="Umodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Ubah Barang Masuk</h6><button aria-label="Close" onclick="resetU()" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="idbmU">
                        <div class="form-group">
                            <label for="bmkodeU" class="form-label">Kode Barang Masuk <span class="text-danger">*</span></label>
                            <input type="text" name="bmkodeU" readonly class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="tglmasukU" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="text" name="tglmasukU" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="serial_numberU" class="form-label">Serial Number</label>
                            <input type="text" name="serial_numberU" id="serial_number_inputU" list="sn_listU" class="form-control" placeholder="Pilih SN atau isi manual...">
                            <datalist id="sn_listU"></datalist>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Barang <span class="text-danger me-1">*</span>
                                <input type="hidden" id="statusU" value="true">
                                <div class="spinner-border spinner-border-sm d-none" id="loaderkdU" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="kdbarangU" placeholder="">
                                <button class="btn btn-primary-light" onclick="searchBarangU()" type="button"><i class="fe fe-search"></i></button>
                                <button class="btn btn-success-light" onclick="modalBarangU()" type="button"><i class="fe fe-box"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label>Nama Barang</label>
                                    <input type="text" class="form-control" id="nmbarangU" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Merk</label>
                                    <input type="text" class="form-control" id="merkbarangU" readonly placeholder="-">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" class="form-control" id="satuanU" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis</label>
                                    <input type="text" class="form-control" id="jenisU" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-none">
                            <label for="jmlU" class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                            <input type="hidden" name="jmlU" id="jmlU" readonly class="form-control" style="background:#f0f8ff;" placeholder="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success d-none" id="btnLoaderU" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                <a href="javascript:void(0)" onclick="checkFormU()" id="btnSimpanU" class="btn btn-success">Simpan
                    Perubahan <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" onclick="resetU()" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formEditJS')
<script>
    $('input[name="kdbarangU"]').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            getbarangbyidU($('input[name="kdbarangU"]').val());
        }
    });

    function modalBarangU() {
        $('#Umodaldemo8').modal('hide');
        $('#modalBarang').modal('show');
        $('input[name="param"]').val('ubah');
        resetValidU();
        table2.ajax.reload();
    }

    function searchBarangU() {
        getbarangbyidU($('input[name="kdbarangU"]').val());
        resetValidU();
    }

    function getbarangbyidU(id) {
        $("#loaderkdU").removeClass('d-none');
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/barang/getbarang') }}/" + id,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $("#loaderkdU").addClass('d-none');
                    $("#statusU").val("true");
                    let partsU = data[0].barang_nama.split(' - ');
                    $("#nmbarangU").val(partsU[0] || data[0].barang_nama);
                    $("#merkbarangU").val(data[0].merk_nama || partsU[1] || '-');
                    $("#satuanU").val(data[0].satuan_id);
                    $("#jenisU").val(data[0].tipe_barang);
                } else {
                    $("#loaderkdU").addClass('d-none');
                    $("#statusU").val("false");
                    $("#nmbarangU").val('');
                    $("#merkbarangU").val('');
                    $("#satuanU").val('');
                    $("#jenisU").val('');
                }
            }
        });
    }

    function checkFormU() {
        const tglmasuk = $("input[name='tglmasukU']").val();
        const status = $("#statusU").val();
        const kdbarang = $("input[name='kdbarangU").val();
        const jml = $("input[name='jmlU']").val();
        setLoadingU(true);
        resetValidU();

        if (tglmasuk == "") {
            validasi('Tanggal Masuk wajib di isi!', 'warning');
            $("input[name='tglmasukU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (status == "false" || kdbarang == '') {
            validasi('Barang wajib di pilih!', 'warning');
            $("input[name='kdbarangU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (jml == "" || jml == "0") {
            validasi('Jumlah Masuk wajib di isi!', 'warning');
            $("input[name='jmlU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else {
            submitFormU();
        }
    }

    function submitFormU() {
        const id = $("input[name='idbmU']").val();
        const bmkode = $("input[name='bmkodeU']").val();
        const tglmasuk = $("input[name='tglmasukU']").val();
        const kdbarang = $("input[name='kdbarangU']").val();
        const serial_number = $("input[name='serial_numberU']").val();
        const jml = $("input[name='jmlU']").val();

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/barang-masuk/proses_ubah') }}/" + id,
            enctype: 'multipart/form-data',
            data: {
                bmkode: bmkode,
                tglmasuk: tglmasuk,
                barang: kdbarang,
                serial_number: serial_number,
                jml: jml
            },
            success: function(data) {
                swal({
                    title: "Berhasil diubah!",
                    type: "success"
                });
                $('#Umodaldemo8').modal('toggle');
                table.ajax.reload(null, false);
                resetU();
            }
        });
    }

    function resetValidU() {
        $("input[name='tglmasukU']").removeClass('is-invalid');
        $("input[name='kdbarangU']").removeClass('is-invalid');
        $("input[name='serial_numberU']").removeClass('is-invalid');
        $("input[name='jmlU']").removeClass('is-invalid');
    };

    function resetU() {
        resetValidU();
        $("input[name='idbmU']").val('');
        $("input[name='bmkodeU']").val('');
        $("input[name='tglmasukU']").val('');
        $("input[name='kdbarangU']").val('');
        $("input[name='serial_numberU']").val('');
        $("input[name='jmlU']").val('0');
        $("#nmbarangU").val('');
        $("#merkbarangU").val('');
        $("#satuanU").val('');
        $("#jenisU").val('');
        $("#statusU").val('false');
        
        setLoadingU(false);
    }

    function setLoadingU(bool) {
        if (bool == true) {
            $('#btnLoaderU').removeClass('d-none');
            $('#btnSimpanU').addClass('d-none');
        } else {
            $('#btnSimpanU').removeClass('d-none');
            $('#btnLoaderU').addClass('d-none');
        }
    }
</script>
@endsection
