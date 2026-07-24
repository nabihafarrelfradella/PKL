<!-- MODAL EDIT -->
<div class="modal fade" data-bs-backdrop="static" id="Umodaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Ubah Barang Keluar</h6><button aria-label="Close" onclick="resetU()" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <div class="select2-wrapper" style="position: relative;">
                                <select name="tujuanU" id="tujuanU" class="form-control select2U" style="width: 100%;" onchange="getTeknisiInfoU(this)">
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach($pegawai as $pgw)
                                        <option value="{{ $pgw->user_nmlengkap }}" data-sn="{{ $pgw->teknisi_sn }}">{{ $pgw->user_nmlengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="teknisiU" class="form-label">ID Teknisi</label>
                            <input type="text" name="teknisiU" readonly class="form-control" placeholder="Otomatis">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customerU" class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                    <input type="text" name="customerU" id="customerU" class="form-control" placeholder="Nama customer / instansi">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lokasiU" class="form-label">Lokasi Instalasi <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="lokasiU" id="lokasiU" class="form-control" placeholder="Lokasi..." autocomplete="off">
                                        <input type="hidden" name="latU" id="latInputU">
                                        <input type="hidden" name="lngU" id="lngInputU">
                                        <input type="hidden" name="map_urlU" id="mapUrlInputU">
                                        <button class="btn btn-primary-light border" type="button" onclick="openLocationPicker('lokasiU')" title="Pilih di Peta"><i class="fe fe-map"></i></button>
                                    </div>
                                </div>
                            </div>
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
                                    <input type="hidden" name="idbkU">
                                    <input type="hidden" name="batchCountU" id="batchCountU">
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
                        <div class="form-group" id="sn_select_groupU">
                            <label for="serial_numberU" class="form-label">Kode Unik</label>
                            <div class="select2-wrapper" id="sn_wrapperU" style="position: relative; display: none;">
                                <select name="serial_numberU" id="sn_listU" class="form-control select2U" style="width: 100%;">
                                    <option value="">-- Pilih Kode Unik... --</option>
                                </select>
                            </div>
                            <input type="text" id="serial_number_inputU" readonly class="form-control" style="background:#f0f8ff;">
                        </div>
                        <div class="form-group d-none">
                            <label for="jmlU" class="form-label">Jumlah Keluar <span class="text-danger">*</span></label>
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
<style>
    /* Ensure wrapper is relatively positioned for Select2 dropdown offset calculation */
    .select2-wrapper {
        position: relative !important;
    }
    
    .select2-wrapper .select2-container {
        z-index: 9999 !important;
    }
    
    /* Limit the height of Select2 selection container to prevent it from growing indefinitely */
    .select2-container .select2-selection--multiple {
        max-height: 110px !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Ensure modal-body scrolls correctly on mobile and doesn't freeze */
    .modal-dialog-scrollable .modal-body {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
    }
</style>
<script>
    $('input[name="kdbarangU"]').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            var kd = $('input[name="kdbarangU"]').val();
            getbarangbyidU(kd);
            if (typeof fetchAvailableSNsU === 'function') {
                fetchAvailableSNsU(kd, null, null);
            }
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
        var kd = $('input[name="kdbarangU"]').val();
        getbarangbyidU(kd);
        if (typeof fetchAvailableSNsU === 'function') {
            fetchAvailableSNsU(kd, null, null);
        }
        resetValidU();
    }

    $(document).ready(function() {
        $('.select2U').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });

        // Close Select2 dropdown on scrolling modal body to prevent floating misalignment
        $('#Umodaldemo8 .modal-body').on('scroll', function() {
            if ($('.select2U').hasClass('select2-hidden-accessible')) {
                $('.select2U').select2('close');
            }
        });

        // Toggle modal-body overflow when Select2 is opened/closed to prevent clipping inside scrollable container
        $(document).on('select2:open', function(e) {
            $(e.target).closest('.modal-body').css('overflow', 'visible');
        });
        $(document).on('select2:close', function(e) {
            $(e.target).closest('.modal-body').css('overflow', 'auto');
            
            // Clean up Select2's residual scroll events that lock/freeze modal scroll
            var evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
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
            validasi('Nama Customer wajib di isi!', 'warning');
            $("input[name='customerU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if ($("input[name='lokasiU']").val() == "") {
            validasi('Lokasi Instalasi wajib di isi!', 'warning');
            $("input[name='lokasiU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else if (jml == "" || jml == "0") {
            validasi('Jumlah Keluar wajib di isi!', 'warning');
            $("input[name='jmlU']").addClass('is-invalid');
            setLoadingU(false);
            return false;
        } else {
            const batchCount = parseInt($("#batchCountU").val()) || 1;
            if (batchCount > 1) {
                swal({
                    title: "Terapkan Perubahan ke Semua?",
                    text: "Terdapat " + batchCount + " barang dalam transaksi ini.\nApakah Anda ingin menerapkan perubahan teknisi, tujuan, dan lokasi ini untuk seluruh barang pada transaksi ini?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, Terapkan Semua",
                    cancelButtonText: "Hanya SN Ini",
                    closeOnConfirm: true,
                    closeOnCancel: true
                }, function(isConfirm) {
                    submitFormU(isConfirm ? 1 : 0);
                });
            } else {
                submitFormU(0);
            }
        }
    }

    function submitFormU(applyToAll = 0) {
        const id = $("input[name='idbkU']").val();
        const bkkode = $("input[name='bkkodeU']").val();
        const tglkeluar = $("input[name='tglkeluarU']").val();
        const kdbarang = $("input[name='kdbarangU']").val();
        const teknisi = $("input[name='teknisiU']").val();
        const customer = $("input[name='customerU']").val();
        const keterangan = $("input[name='keteranganU']").val();
        const serial_number = $("#sn_wrapperU").is(":visible") 
            ? $("#sn_listU").val() 
            : $("#serial_number_inputU").val();
        const jml = $("input[name='jmlU']").val();

        const lokasi = $("input[name='lokasiU']").val();

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/barang-keluar/proses_ubah') }}/" + id,
            data: {
                bkkode: bkkode,
                tglkeluar: tglkeluar,
                barang: kdbarang,
                tujuan: customer,
                lokasi: lokasi,
                teknisi: teknisi,
                keterangan: keterangan,
                serial_number: serial_number,
                jml: jml,
                lat: $('#latInputU').val(),
                lng: $('#lngInputU').val(),
                map_url: $('#mapUrlInputU').val(),
                applyToAll: applyToAll
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
        $("#merkbarangU").val('');
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
