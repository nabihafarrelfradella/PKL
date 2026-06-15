<!-- MODAL TAMBAH BARANG KELUAR / FORM PEMINJAMAN TEKNISI -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header @if(($roleId ?? 0) == 3) bg-primary @endif">
                @if(($roleId ?? 0) == 3)
                    <span class="modal-title text-white fw-bold">
                        <i class="fe fe-tool me-2"></i>Form Peminjaman Barang
                    </span>
                @else
                    <h6 class="modal-title">Tambah Barang Keluar</h6>
                @endif
                <button aria-label="Close" onclick="reset()" class="btn-close @if(($roleId ?? 0) == 3) btn-close-white @endif" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>

            @if(($roleId ?? 0) == 3)
            <!-- BANNER TEKNISI INFO -->
            <div class="px-3 pt-3">
                <div class="alert alert-primary d-flex align-items-center py-2 mb-0" style="background: linear-gradient(135deg,#e3f0ff,#cce3ff); border:1px solid #99caff; border-radius:10px;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;flex-shrink:0;">
                        <i class="fe fe-user text-white"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-primary">{{ $currentUser->user_nmlengkap ?? '' }}</div>
                        <small class="text-muted">ID Teknisi: <strong>{{ $currentUser->teknisi_sn ?? '-' }}</strong> &nbsp;|&nbsp; Pegawai Teknisi</small>
                    </div>
                </div>
            </div>
            @endif

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Kode Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="bkkode" readonly class="form-control" placeholder="Otomatis">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tglkeluar" id="tglkeluar_input" class="form-control">
                        </div>

                        @if(($roleId ?? 0) == 3)
                        {{-- TEKNISI: auto-fill nama & SN dari session --}}
                        <div class="form-group">
                            <label class="form-label">Nama Teknisi</label>
                            <input type="text" class="form-control" value="{{ $currentUser->user_nmlengkap ?? '' }}" readonly style="background:#f0f8ff;">
                            <input type="hidden" name="tujuan" value="{{ $currentUser->user_nmlengkap ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Teknisi</label>
                            <input type="text" name="teknisi" class="form-control" value="{{ $currentUser->teknisi_sn ?? '-' }}" readonly style="background:#f0f8ff;">
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold text-danger">
                                <i class="fe fe-user me-1"></i>Nama Customer / Lokasi Instalasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="customer" id="customerInput" class="form-control" placeholder="Contoh: Budi Santoso / RT 5 Kel. Cimahi" autocomplete="off">
                            <small class="text-muted">Wajib diisi — untuk apa / ke mana barang ini dibawa</small>
                        </div>
                        @else
                        {{-- OWNER / ADMIN: dropdown pilih teknisi --}}
                        <div class="form-group">
                            <label class="form-label">Nama Teknisi <span class="text-danger">*</span></label>
                            <div class="select2-wrapper" style="position: relative;">
                                <select name="tujuan" id="tujuan" class="form-control select2" style="width: 100%;" onchange="getTeknisiInfo(this)">
                                    <option value="">-- Pilih Teknisi --</option>
                                    @foreach($pegawai as $pgw)
                                        <option value="{{ $pgw->user_nmlengkap }}" data-sn="{{ $pgw->teknisi_sn }}">{{ $pgw->user_nmlengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">ID Teknisi</label>
                            <input type="text" name="teknisi" readonly class="form-control" placeholder="Otomatis">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Customer / Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="customer" id="customerInput" class="form-control" placeholder="Nama customer atau lokasi instalasi">
                        </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Kode Barang / Kode Unik / SN <span class="text-danger me-1">*</span>
                                <input type="hidden" id="status" value="false">
                                <input type="hidden" name="kode_barang_unik">
                                <div class="spinner-border spinner-border-sm d-none" id="loaderkd" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="kdbarang" placeholder="Scan QR atau masukkan kode...">
                                <button class="btn btn-primary-light" onclick="searchBarang()" type="button"><i class="fe fe-search"></i></button>
                                <button class="btn btn-success-light" onclick="modalBarang()" type="button"><i class="fe fe-box"></i></button>
                            </div>
                            <small class="text-muted">Bisa scan QR Code atau input Serial Number</small>
                        </div>
                        <div class="form-group">
                            <label>Nama Barang</label>
                            <input type="text" class="form-control" id="nmbarang" readonly>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Satuan</label>
                                    <input type="text" class="form-control" id="satuan" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis</label>
                                    <input type="text" class="form-control" id="jenis" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>SN Barang</label>
                            <div class="select2-wrapper" style="position: relative;">
                                <select id="sn_select" name="serial_number[]" class="form-control sn-select2" style="width:100%;" multiple="multiple">
                                    <option value="">-- Pilih SN... --</option>
                                </select>
                            </div>
                            <small class="text-muted">Pilih SN dari daftar. Kosongkan jika tidak pakai SN.</small>
                        </div>
                        <div class="form-group">
                            <label>Jumlah Keluar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button class="btn btn-light border" type="button" onclick="adjustQty(-1)"><i class="fe fe-minus"></i></button>
                                <input type="text" name="jml" value="1" class="form-control text-center font-weight-bold" oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateAndNotifyQty();" style="font-weight: 600;">
                                <button class="btn btn-light border" type="button" onclick="adjustQty(1)"><i class="fe fe-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-primary d-none" id="btnLoader" type="button" disabled="">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Loading...
                </button>
                @if(($roleId ?? 0) == 3)
                    <a href="javascript:void(0)" onclick="checkForm()" id="btnSimpan" class="btn btn-primary">
                        <i class="fe fe-send me-1"></i>Ajukan Peminjaman
                    </a>
                @else
                    <a href="javascript:void(0)" onclick="checkForm()" id="btnSimpan" class="btn btn-primary">
                        Simpan <i class="fe fe-check"></i>
                    </a>
                @endif
                <a href="javascript:void(0)" class="btn btn-light" onclick="reset()" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>

@section('formTambahJS')
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
    function getLocalDateTimeString() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    $(document).ready(function() {
        $("input[name='tglkeluar']").val(getLocalDateTimeString());
    });

    $('input[name="kdbarang"]').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            getbarangbyid($('input[name="kdbarang"]').val());
        }
    });

    function modalBarang() {
        $('#modalBarang').modal('show');
        $('#modaldemo8').addClass('d-none');
        $('input[name="param"]').val('tambah');
        resetValid();
        table2.ajax.reload();
    }

    function searchBarang() {
        getbarangbyid($('input[name="kdbarang"]').val());
        resetValid();
    }

    $(document).ready(function() {
        @if(($roleId ?? 0) != 3)
        $('.select2').each(function() {
            $(this).select2({
                dropdownParent: $(this).parent()
            });
        });
        @endif

        // Init SN Select2 dengan tags:true — bisa pilih list atau ketik bebas
        initSNSelect2();

        // Close Select2 dropdown on scrolling modal body to prevent floating misalignment
        $('#modaldemo8 .modal-body').on('scroll', function() {
            if ($('#sn_select').hasClass('select2-hidden-accessible')) {
                $('#sn_select').select2('close');
            }
            if ($('#tujuan').hasClass('select2-hidden-accessible')) {
                $('#tujuan').select2('close');
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

    function initSNSelect2(maxSelect) {
        // Always read current jml from DOM — never trust closure for dynamic value
        var maxSelection = maxSelect || parseInt($("input[name='jml']").val()) || 1;

        // Store on element so handler always has the freshest value
        $('#sn_select').attr('data-max', maxSelection);

        // Remove old event handlers FIRST before destroying to prevent accumulation
        $('#sn_select').off('select2:selecting').off('change');

        // Destroy existing Select2 instance
        if ($('#sn_select').hasClass('select2-hidden-accessible')) {
            $('#sn_select').select2('destroy');
        }

        $('#sn_select').select2({
            dropdownParent: $('#sn_select').parent(),
            placeholder: '-- Pilih SN... --',
            allowClear: false,
            hideSelectedOptions: true,
            language: {
                maximumSelected: function() { return ''; }
            }
        }).on('select2:selecting', function(e) {
            // Always read the freshest jml from both DOM and data attribute (take the max)
            var maxFromInput = parseInt($("input[name='jml']").val()) || 1;
            var maxFromAttr  = parseInt($(this).attr('data-max')) || 1;
            var maxSel = Math.max(maxFromInput, maxFromAttr);
            var currentCount = ($(this).val() || []).length;
            if (currentCount >= maxSel) {
                e.preventDefault();
                validasi('Maksimal ' + maxSel + ' SN sesuai jumlah barang keluar', 'warning');
            }
        }).on('change', function() {
            var selectedVals = $(this).val() || [];
            selectedVals = selectedVals.filter(function(s) { return s && s.trim() !== ''; });
            var kbuArray = [];
            selectedVals.forEach(function(val) {
                var kbu = $("#sn_select option[value='" + val + "']").data('kbu') || '';
                kbuArray.push(kbu);
            });
            $("input[name='kode_barang_unik']").val(kbuArray.join(','));
        });
    }

    function getTeknisiInfo(el) {
        var selectedOption = $(el).find('option:selected');
        var sn = selectedOption.data('sn');
        if (!sn) {
            $("input[name='teknisi']").val('');
            return;
        }
        $.ajax({
            type: 'GET',
            url: "/admin/user-management/teknisi/get-by-sn/" + sn,
            success: function(data) {
                if (data) {
                    $("input[name='teknisi']").val(data.teknisi_sn);
                } else {
                    $("input[name='teknisi']").val('');
                }
            }
        });
    }

    function getbarangbyid(id) {
        if (!id || !id.trim()) {
            validasi('Masukkan kode barang, kode unik, atau Serial Number terlebih dahulu!', 'warning');
            return;
        }
        $("#loaderkd").removeClass('d-none');
        $.ajax({
            type: 'GET',
            url: "/admin/barang/getunit/" + id,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $("#loaderkd").addClass('d-none');
                    $("#status").val("true");
                    $("#nmbarang").val(data[0].barang_nama);
                    $("#satuan").val(data[0].satuan_id);
                    $("#jenis").val(data[0].jenisbarang_nama);
                    // Set SN dari scan/QR ke Select2
                    var snVal = data[0].serial_number || '';
                    var kbuVal = data[0].kode_barang_unik || '';
                    setSNSelect2(snVal, kbuVal);
                    if (id == data[0].kode_barang_unik || id == data[0].serial_number) {
                        $('input[name="kdbarang"]').val(data[0].barang_kode);
                    }
                    fetchAvailableSNs(data[0].barang_kode);
                } else {
                    $.ajax({
                        type: 'GET',
                        url: "/admin/barang/getbarang/" + id,
                        success: function(data2) {
                            var resp = JSON.parse(data2);
                            if (resp.length > 0) {
                                $("#loaderkd").addClass('d-none');
                                $("#status").val("true");
                                $("#nmbarang").val(resp[0].barang_nama);
                                $("#satuan").val(resp[0].satuan_id);
                                $("#jenis").val(resp[0].jenisbarang_nama);
                                $("input[name='kode_barang_unik']").val('');
                                setSNSelect2('', '');
                                fetchAvailableSNs(resp[0].barang_kode);
                            } else {
                                $("#loaderkd").addClass('d-none');
                                $("#status").val("false");
                                $("#nmbarang").val('');
                                $("#satuan").val('');
                                $("#jenis").val('');
                                setSNSelect2('', '');
                                fetchAvailableSNs('');
                                validasi('Barang dengan kode "' + id + '" tidak ditemukan!', 'warning');
                            }
                        },
                        error: function() {
                            $("#loaderkd").addClass('d-none');
                            validasi('Terjadi kesalahan saat mencari barang. Coba lagi!', 'error');
                        }
                    });
                }
            },
            error: function() {
                $("#loaderkd").addClass('d-none');
                validasi('Terjadi kesalahan saat mencari barang. Coba lagi!', 'error');
            }
        });
    }

    function fetchAvailableSNs(barang_kode) {
        var $sel = $('#sn_select');
        // Save currently selected values before rebuilding — filter out empty/blank values
        var previousSelected = ($sel.val() || []).filter(function(s) { return s && s.trim() !== ''; });

        // Destroy Select2 dulu agar bisa rebuild options dengan bersih
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
        $sel.empty().append('<option value=""></option>');
        $("input[name='kode_barang_unik']").val('');

        if (!barang_kode) {
            initSNSelect2();
            return;
        }

        $.ajax({
            type: 'GET',
            url: "/admin/barang/get-available-sn/" + barang_kode,
            dataType: 'json',
            success: function(data) {
                // Add available SNs that are NOT already selected
                data.forEach(function(item) {
                    if (!previousSelected.includes(item.serial_number)) {
                        var unikText = item.kode_barang_unik ? ` (Unik: ${item.kode_barang_unik})` : '';
                        var $opt = $('<option></option>')
                            .val(item.serial_number)
                            .text(item.serial_number + unikText)
                            .attr('data-kbu', item.kode_barang_unik);
                        $sel.append($opt);
                    }
                });
                // Re-add previously selected as selected options (so they show as tags)
                previousSelected.forEach(function(sn) {
                    if (sn) {
                        var matched = data.find(function(d) { return d.serial_number === sn; });
                        var unikText = matched && matched.kode_barang_unik ? ` (Unik: ${matched.kode_barang_unik})` : '';
                        var $opt = $('<option selected></option>')
                            .val(sn)
                            .text(sn + unikText)
                            .attr('data-kbu', matched ? matched.kode_barang_unik : '');
                        $sel.append($opt);
                    }
                });
                if (previousSelected.length > 0) {
                    $sel.val(previousSelected);
                }
                initSNSelect2(); // Reinit setelah options siap
            },
            error: function() {
                initSNSelect2();
            }
        });
    }

    // Set nilai SN di Select2 (dari scan QR / auto-fill)
    function setSNSelect2(sn, kbu) {
        if (!sn) {
            if ($('#sn_select').hasClass('select2-hidden-accessible')) {
                $('#sn_select').val(null).trigger('change');
            }
            return;
        }
        // Tambah option jika belum ada, lalu select
        if ($('#sn_select option[value="' + sn + '"]').length === 0) {
            var $opt = $('<option selected></option>').val(sn).text(sn).attr('data-kbu', kbu || '');
            $('#sn_select').append($opt);
        }
        
        var selectedVals = $('#sn_select').val() || [];
        if (!selectedVals.includes(sn)) {
            selectedVals.push(sn);
        }
        
        var currentJml = parseInt($("input[name='jml']").val()) || 1;
        if (selectedVals.length > currentJml) {
            $("input[name='jml']").val(selectedVals.length);
        }
        
        $('#sn_select').val(selectedVals).trigger('change');
        validateAndNotifyQty();
    }

    function adjustQty(amount) {
        var $jmlInput = $("input[name='jml']");
        var val = parseInt($jmlInput.val()) || 1;
        val += amount;
        if (val < 1) val = 1;
        $jmlInput.val(val);
        // Update data-max attribute so the event handler always has the fresh limit
        $('#sn_select').attr('data-max', val);
        validateAndNotifyQty();
    }

    function validateAndNotifyQty() {
        var $jmlInput = $("input[name='jml']");
        var val = parseInt($jmlInput.val()) || 1;
        if (val < 1) {
            val = 1;
            $jmlInput.val(1);
        }
        
        var $sel = $('#sn_select');
        if ($sel.hasClass('select2-hidden-accessible')) {
            var selectedVals = $sel.val() || [];
            if (selectedVals.length > val) {
                selectedVals = selectedVals.slice(0, val);
                $sel.val(selectedVals).trigger('change');
            }
            $sel.select2('destroy');
        }
        initSNSelect2(val);
    }

    function checkForm() {
        const tglkeluar = $("input[name='tglkeluar']").val();
        const status    = $("#status").val();
        const jml       = $("input[name='jml']").val();
        const customer  = $("input[name='customer']").val();
        const tujuan    = $("select[name='tujuan']").val() || $("input[name='tujuan']").val();
        setLoading(true);
        resetValid();

        var hasSNOptions = $('#sn_select option').filter(function() {
            return $(this).val() !== "";
        }).length > 0;
        var selectedVals = $('#sn_select').val() || [];

        if (tglkeluar == "") {
            validasi('Tanggal Keluar wajib di isi!', 'warning');
            $("input[name='tglkeluar']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (tujuan == "" || tujuan == null) {
            validasi('Nama Teknisi wajib di pilih!', 'warning');
            $("select[name='tujuan']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (status == "false") {
            validasi('Barang wajib di pilih!', 'warning');
            $("input[name='kdbarang']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (customer == "") {
            validasi('Customer / Lokasi Instalasi wajib di isi!', 'warning');
            $("input[name='customer']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (jml == "" || jml == "0") {
            validasi('Jumlah Keluar wajib di isi!', 'warning');
            $("input[name='jml']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (hasSNOptions && selectedVals.length !== parseInt(jml)) {
            validasi('Jumlah SN yang terpilih (' + selectedVals.length + ') harus sama dengan Jumlah Keluar (' + jml + ')!', 'warning');
            setLoading(false); return false;
        } else {
            submitForm();
        }
    }

    function submitForm() {
        const bkkode          = $("input[name='bkkode']").val();
        const tglkeluar       = $("input[name='tglkeluar']").val();
        const kdbarang        = $("input[name='kdbarang']").val();
        const kode_barang_unik= $("input[name='kode_barang_unik']").val();
        const tujuan          = $("select[name='tujuan']").val() || $("input[name='tujuan']").val();
        const jml             = $("input[name='jml']").val();
        const serial_number   = $("#sn_select").val(); // send as array
        const teknisi         = $("input[name='teknisi']").val();
        const keterangan      = $("input[name='keterangan']").val();
        const customer        = $("input[name='customer']").val();

        $.ajax({
            type: 'POST',
            url: "{{ route('barang-keluar.store') }}",
            data: {
                bkkode, tglkeluar, barang: kdbarang, kode_barang_unik,
                tujuan, jml, serial_number, teknisi, keterangan, customer
            },
            success: function(data) {
                $('#modaldemo8').modal('toggle');
                swal({ title: "Berhasil!", text: "Data peminjaman telah disimpan.", type: "success" });
                table.ajax.reload(null, false);
                reset();
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.error ?? 'Terjadi kesalahan';
                swal({ title: "Gagal!", text: msg, type: "error" });
                setLoading(false);
            }
        });
    }

    function resetValid() {
        $("input[name='tglkeluar']").removeClass('is-invalid');
        $("select[name='tujuan']").removeClass('is-invalid');
        $("input[name='kdbarang']").removeClass('is-invalid');
        $("input[name='customer']").removeClass('is-invalid');
        $("input[name='jml']").removeClass('is-invalid');
    };

    function reset() {
        resetValid();
        $("input[name='bkkode']").val('');
        $("input[name='tglkeluar']").val(getLocalDateTimeString());
        $("input[name='kdbarang']").val('');
        $("input[name='customer']").val('');
        $("input[name='keterangan']").val('');
        $("input[name='kode_barang_unik']").val('');
        $("input[name='jml']").val('1');
        $("#nmbarang").val('');
        $("#satuan").val('');
        $("#jenis").val('');
        $("#status").val('false');
        // Reset Select2 SN: destroy → kosongkan → reinit
        if ($('#sn_select').hasClass('select2-hidden-accessible')) {
            $('#sn_select').select2('destroy');
        }
        $('#sn_select').empty().append('<option value=""></option>');
        initSNSelect2();
        @if(($roleId ?? 0) != 3)
        $("select[name='tujuan']").val('').trigger('change');
        $("input[name='teknisi']").val('');
        @endif
        setLoading(false);
    }

    function setLoading(bool) {
        if (bool) {
            $('#btnLoader').removeClass('d-none');
            $('#btnSimpan').addClass('d-none');
        } else {
            $('#btnSimpan').removeClass('d-none');
            $('#btnLoader').addClass('d-none');
        }
    }
</script>
@endsection