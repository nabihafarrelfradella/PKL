<!-- MODAL EDIT -->
<div class="modal fade" data-bs-backdrop="static" id="Umodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Ubah Barang Keluar</h6><button aria-label="Close" onclick="resetU()" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="idbkU">
                        <div class="form-group">
                            <label for="bkkodeU" class="form-label">Kode Barang Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="bkkodeU" readonly class="form-control" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="tglkeluarU" class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="tglkeluarU" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tujuanU" class="form-label">Nama Teknisi <span class="text-danger">*</span></label>
                            <select name="tujuanU" id="tujuanU" class="form-control select2U" style="width: 100%;" onchange="getTeknisiInfoU(this)">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($pegawai as $pgw)
                                    <option value="{{ $pgw->user_nmlengkap }}" data-sn="{{ $pgw->teknisi_sn }}">{{ $pgw->user_nmlengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="teknisiU" class="form-label">ID Teknisi</label>
                            <input type="text" name="teknisiU" readonly class="form-control" placeholder="Otomatis">
                        </div>

                        <div class="form-group">
                            <label for="customerU" class="form-label">Customer / Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="customerU" id="customerU" class="form-control" placeholder="Nama customer atau lokasi instalasi">
                        </div>
                        <div class="form-group">
                            <label for="keteranganU" class="form-label">Keterangan</label>
                            <input type="text" name="keteranganU" class="form-control" placeholder="">
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
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" id="nmbarangU" readonly>
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
                        <div class="form-group">
                            <label for="serial_numberU" class="form-label">SN Barang</label>
                            <input type="text" name="serial_numberU" id="serial_number_inputU" readonly class="form-control" style="background:#f0f8ff;">
                        </div>
                        <div class="form-group">
                            <label for="jmlU" class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="jmlU" id="jmlU" readonly class="form-control" style="background:#f0f8ff;" placeholder="">
                            <small class="text-muted">Jumlah tidak dapat diubah (setiap baris = 1 unit)</small>
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
        $('#modalBarang').modal('show');
        $('#Umodaldemo8').addClass('d-none');
        $('input[name="param"]').val('ubah');
        resetValidU();
        table2.ajax.reload();
    }

    function searchBarangU() {
        getbarangbyidU($('input[name="kdbarangU"]').val());
        resetValidU();
    }

    $(document).ready(function() {
        $('.select2U').select2({
            dropdownParent: $('#Umodaldemo8')
        });
    });

    function getbarangbyidU(id) {
        if (!id || !id.trim()) {
            validasi('Masukkan kode barang terlebih dahulu!', 'warning');
            return;
        }
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
                    $("#nmbarangU").val(data[0].barang_nama);
                    $("#satuanU").val(data[0].satuan_id);
                    $("#jenisU").val(data[0].jenisbarang_nama);
                } else {
                    $("#loaderkdU").addClass('d-none');
                    $("#statusU").val("false");
                    $("#nmbarangU").val('');
                    $("#satuanU").val('');
                    $("#jenisU").val('');
                    validasi('Barang dengan kode "' + id + '" tidak ditemukan!', 'warning');
                }
            },
            error: function() {
                $("#loaderkdU").addClass('d-none');
                validasi('Terjadi kesalahan saat mencari barang. Coba lagi!', 'error');
            }
        });
    }

    function checkFormU() {
        const tglkeluar = $("input[name='tglkeluarU']").val();
        const status = $("#statusU").val();
        const kdbarang = $("input[name='kdbarangU']").val();
        const tujuan = $("select[name='tujuanU']").val();
        const customer = $("input[name='customerU']").val();
        const jml = $("input[name='jmlU']").val();
        setLoadingU(true);
        resetValidU();

        if (tglkeluar == "") {
            validasi('Tanggal Keluar wajib di isi!', 'warning');
            $("input[name='tglkeluarU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (tujuan == "" || tujuan == null) {
            validasi('Nama Teknisi wajib di pilih!', 'warning');
            $("select[name='tujuanU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (status == "false" || kdbarang == '') {
            validasi('Barang wajib di pilih!', 'warning');
            $("input[name='kdbarangU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (customer == "") {
            validasi('Customer / Lokasi wajib di isi!', 'warning');
            $("input[name='customerU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (jml == "" || jml == "0") {
            validasi('Jumlah Keluar wajib di isi!', 'warning');
            $("input[name='jmlU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else {
            submitFormU();
        }
    }

    function submitFormU() {
        const id = $("input[name='idbkU']").val();
        const bkkode = $("input[name='bkkodeU']").val();
        const tglkeluar = $("input[name='tglkeluarU']").val();
        const kdbarang = $("input[name='kdbarangU']").val();
        const teknisi = $("input[name='teknisiU']").val();
        const customer = $("input[name='customerU']").val();
        const keterangan = $("input[name='keteranganU']").val();
        const serial_number = $("#serial_number_inputU").val();
        const jml = $("input[name='jmlU']").val();

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/barang-keluar/proses_ubah') }}/" + id,
            data: {
                bkkode: bkkode,
                tglkeluar: tglkeluar,
                barang: kdbarang,
                tujuan: customer,
                teknisi: teknisi,
                keterangan: keterangan,
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
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.error ?? 'Terjadi kesalahan';
                swal({ title: "Gagal!", text: msg, type: "error" });
                setLoadingU(false);
            }
        });
    }

    function resetValidU() {
        $("input[name='tglkeluarU']").removeClass('is-invalid');
        $("input[name='kdbarangU']").removeClass('is-invalid');
        $("select[name='tujuanU']").removeClass('is-invalid');
        $("input[name='customerU']").removeClass('is-invalid');
        $("input[name='jmlU']").removeClass('is-invalid');
    };

    function resetU() {
        resetValidU();
        $("input[name='idbkU']").val('');
        $("input[name='bkkodeU']").val('');
        $("input[name='tglkeluarU']").val('');
        $("input[name='kdbarangU']").val('');
        $("select[name='tujuanU']").val('').trigger('change');
        $("input[name='teknisiU']").val('');
        $("input[name='customerU']").val('');
        $("input[name='keteranganU']").val('');
        $("#serial_number_inputU").val('');
        $("input[name='jmlU']").val('0');
        $("#nmbarangU").val('');
        $("#satuanU").val('');
        $("#jenisU").val('');
        $("#statusU").val('false');
        setLoadingU(false);
    }

    function getTeknisiInfoU(param) {
        var sn = '';
        if (typeof param === 'string') {
            sn = param;
        } else {
            var selectedOption = $(param).find('option:selected');
            sn = selectedOption.data('sn');
        }
        if (!sn) {
            $("input[name='teknisiU']").val('');
            return;
        }
        $.ajax({
            type: 'GET',
            url: "/admin/user-management/teknisi/get-by-sn/" + sn,
            success: function(data) {
                if (data) {
                    $("input[name='teknisiU']").val(data.teknisi_sn);
                    if ($("#tujuanU").val() !== data.user_nmlengkap) {
                        $("#tujuanU").val(data.user_nmlengkap).trigger('change.select2');
                    }
                } else {
                    $("input[name='teknisiU']").val('');
                }
            }
        });
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