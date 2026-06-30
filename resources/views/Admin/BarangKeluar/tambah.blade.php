<!-- MODAL TAMBAH BARANG KELUAR / FORM PEMINJAMAN TEKNISI -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
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
                <style>
                    #modaldemo8 .form-group { margin-bottom: 0.5rem; }
                    #modaldemo8 .my-3 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
                    #modaldemo8 .form-label, #modaldemo8 label { margin-bottom: 0.25rem; font-size: 0.85rem; }
                </style>
                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="form-group d-none">
                            <label class="form-label">Kode Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="bkkode" readonly class="form-control" placeholder="Otomatis">
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tglkeluar" id="tglkeluar_input" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                @if(($roleId ?? 0) == 3)
                                {{-- TEKNISI: auto-fill nama & SN dari session --}}
                                <div class="form-group">
                                    <label class="form-label">Nama Teknisi</label>
                                    <input type="text" class="form-control" value="{{ $currentUser->user_nmlengkap ?? '' }}" readonly style="background:#f0f8ff;">
                                    <input type="hidden" name="tujuan" value="{{ $currentUser->user_nmlengkap ?? '' }}">
                                </div>
                                <div class="form-group d-none">
                                    <label class="form-label">ID Teknisi</label>
                                    <input type="text" name="teknisi" class="form-control" value="{{ $currentUser->teknisi_sn ?? '-' }}" readonly style="background:#f0f8ff;">
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
                                <div class="form-group d-none">
                                    <label class="form-label">ID Teknisi</label>
                                    <input type="text" name="teknisi" readonly class="form-control" placeholder="Otomatis">
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                @if(($roleId ?? 0) == 3)
                                <div class="form-group">
                                    <label class="form-label fw-semibold text-danger">
                                        <i class="fe fe-user me-1"></i>Nama Customer <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="customer" id="customerInput" class="form-control" placeholder="Contoh: Budi Santoso" autocomplete="off">
                                </div>
                                @else
                                <div class="form-group">
                                    <label class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                    <input type="text" name="customer" id="customerInput" class="form-control" placeholder="Nama customer / instansi">
                                </div>
                                @endif
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fe fe-map-pin me-1 text-danger"></i>Lokasi Instalasi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" name="lokasi" id="lokasiInput" class="form-control" placeholder="Lokasi..." autocomplete="off">
                                        <button class="btn btn-primary-light border" type="button" onclick="openLocationPicker()" title="Pilih di Peta"><i class="fe fe-map"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Opsional">
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="form-group">
                            <label>Kode Barang / Kode Unik / SN <span class="text-danger me-1">*</span>
                                <input type="hidden" id="status" value="false">
                                <input type="hidden" name="kode_barang_unik">
                                <div class="spinner-border spinner-border-sm d-none" id="loaderkd" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </label>
                            <div class="input-group flex-nowrap">
                                <input type="text" class="form-control" autocomplete="off" name="kdbarang" placeholder="Scan QR atau masukkan kode...">
                                <button class="btn btn-primary-light" onclick="searchBarang()" type="button"><i class="fe fe-search"></i></button>
                                <button class="btn btn-success-light" onclick="modalBarang()" type="button"><i class="fe fe-box"></i></button>
                            </div>
                            <small class="text-muted">Bisa scan QR Code atau input Serial Number</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-sm-8">
                                <div class="form-group">
                                    <label>Nama Barang</label>
                                    <input type="text" class="form-control" id="nmbarang" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Jumlah Keluar <span class="text-danger">*</span></label>
                                    <div class="input-group flex-nowrap">
                                        <button class="btn btn-light border" type="button" onclick="adjustQty(-1)" style="padding-top: 0; padding-bottom: 0;"><i class="fe fe-minus"></i></button>
                                        <input type="text" name="jml" value="1" class="form-control text-center font-weight-bold" oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateAndNotifyQty();" style="font-weight: 600;">
                                        <button class="btn btn-light border" type="button" onclick="adjustQty(1)" style="padding-top: 0; padding-bottom: 0;"><i class="fe fe-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="satuan">
                        <input type="hidden" id="jenis">
                        <div class="form-group">
                            <label>SN Barang</label>
                            <div class="select2-wrapper" style="position: relative;">
                                <select id="sn_select" name="serial_number[]" class="form-control sn-select2" style="width:100%;" multiple="multiple">
                                    <option value="">-- Pilih SN... --</option>
                                </select>
                            </div>
                            <small class="text-muted">Pilih SN dari daftar. Kosongkan jika tidak pakai SN.</small>
                        </div>
                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addToBatch()">
                                <i class="fe fe-plus me-1"></i>Tambah ke Daftar
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                {{-- ── DAFTAR BARANG YANG AKAN DISIMPAN ── --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        <i class="fe fe-list me-1 text-primary"></i>Daftar Barang Keluar
                        <span class="badge bg-primary ms-1" id="batchCount">0</span>
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm d-none" id="btnClearAll" onclick="clearBatch()">
                        <i class="fe fe-trash me-1"></i>Hapus Semua
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0" id="batchItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="1%">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>SN Terpilih</th>
                                <th width="8%">Jumlah</th>
                                <th width="1%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="batchItemsBody">
                            <tr id="emptyBatchRow">
                                <td colspan="6" class="text-center text-muted py-3">
                                    <i class="fe fe-inbox d-block mb-1" style="font-size:24px;"></i>
                                    Belum ada barang. Pilih barang, tentukan SN, dan klik <strong>"Tambah ke Daftar"</strong>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

<!-- MODAL LOCATION PICKER -->
<div class="modal fade" id="modalLocationPicker" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fe fe-map-pin me-2"></i>Pilih Lokasi Instalasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="position-relative">
                    <div class="input-group mb-2">
                        <input type="text" id="mapSearchInput" class="form-control" placeholder="Ketik nama jalan / daerah untuk mencari..." onkeyup="autocompleteMapSearch(this.value)">
                        <button class="btn btn-primary" type="button" onclick="searchLocationMap()"><i class="fe fe-search"></i> Cari</button>
                    </div>
                    <div id="autocompleteResults" class="list-group position-absolute w-100 shadow" style="z-index: 1080; display: none; max-height: 200px; overflow-y: auto; top: 100%;"></div>
                </div>
                <div id="mapPicker" style="height: 350px; width: 100%; border-radius: 5px; border: 1px solid #ddd; z-index: 1;"></div>
                <div class="mt-2 p-2 bg-light rounded border">
                    <small class="text-muted d-block">Alamat Terpilih:</small>
                    <strong id="selectedLocationText" class="text-dark">Silakan klik pada peta...</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmLocation()"><i class="fe fe-check me-1"></i>Gunakan Lokasi Ini</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Styling khusus untuk modal barang keluar */
    #modalLocationPicker {
        z-index: 1070 !important;
    }
    #mapPicker, #mapPicker .leaflet-grab, #mapPicker .leaflet-interactive {
        cursor: crosshair !important;
    }
    #autocompleteResults .list-group-item:hover {
        background-color: #f0f4f8 !important;
        color: #0d6efd !important;
    }
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
    let map, marker;
    let selectedAddress = '';
    let mapTargetInput = 'lokasiInput';

    function openLocationPicker(targetId = 'lokasiInput') {
        mapTargetInput = targetId;
        
        // Sembunyikan form utama terlebih dahulu agar tidak saling tindih
        let modalUtama = bootstrap.Modal.getInstance(document.getElementById('modaldemo8'));
        if (modalUtama) modalUtama.hide();
        let modalUbah = bootstrap.Modal.getInstance(document.getElementById('Umodaldemo8'));
        if (modalUbah) modalUbah.hide();
        
        let locModalEl = document.getElementById('modalLocationPicker');
        let modalPeta = bootstrap.Modal.getInstance(locModalEl) || new bootstrap.Modal(locModalEl);
        modalPeta.show();
        
        setTimeout(() => { 
            initMap(); 
        }, 400);
    }
    
    // Ketika modal peta ditutup, munculkan kembali form utama
    document.getElementById('modalLocationPicker').addEventListener('hidden.bs.modal', function () {
        if (mapTargetInput === 'lokasiInput') {
            let modalUtama = bootstrap.Modal.getInstance(document.getElementById('modaldemo8')) || new bootstrap.Modal(document.getElementById('modaldemo8'));
            modalUtama.show();
        } else {
            let modalUbah = bootstrap.Modal.getInstance(document.getElementById('Umodaldemo8')) || new bootstrap.Modal(document.getElementById('Umodaldemo8'));
            modalUbah.show();
        }
    });
    
    document.getElementById('modalLocationPicker').addEventListener('shown.bs.modal', function () {
        if (map) {
            map.invalidateSize();
        }
    });
    
    let autocompleteTimeout;
    
    function autocompleteMapSearch(query) {
        if(query.length < 3) {
            $('#autocompleteResults').hide().empty();
            return;
        }
        
        clearTimeout(autocompleteTimeout);
        autocompleteTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id`)
                .then(response => response.json())
                .then(data => {
                    let resultsHtml = '';
                    if(data && data.length > 0) {
                        data.forEach(item => {
                            let safeName = item.display_name.replace(/'/g, "\\'");
                            resultsHtml += `<button type="button" class="list-group-item list-group-item-action text-start p-2" onclick="selectAutocomplete('${item.lat}', '${item.lon}', '${safeName}')">
                                <small><i class="fe fe-map-pin me-1 text-primary"></i> ${item.display_name}</small>
                            </button>`;
                        });
                        $('#autocompleteResults').html(resultsHtml).show();
                    } else {
                        $('#autocompleteResults').hide();
                    }
                });
        }, 500);
    }

    function selectAutocomplete(lat, lon, name) {
        $('#autocompleteResults').hide();
        $('#mapSearchInput').val(name);
        
        map.setView([lat, lon], 16);
        marker.setLatLng([lat, lon]);
        selectedAddress = name;
        $('#selectedLocationText').text(selectedAddress);
    }
    
    var batchItems = [];

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
                    $("#jenis").val(data[0].tipe_barang);
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
                                $("#jenis").val(resp[0].tipe_barang);
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

        // Get all SNs already in batchItems
        var snInCart = [];
        batchItems.forEach(function(item) {
            if(item.sns) {
                snInCart = snInCart.concat(item.sns);
            }
        });

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
                // Add available SNs that are NOT already selected AND NOT in the cart
                data.forEach(function(item) {
                    if (!previousSelected.includes(item.serial_number) && !snInCart.includes(item.serial_number)) {
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
                    if (sn && !snInCart.includes(sn)) {
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
                    $sel.val(previousSelected.filter(sn => !snInCart.includes(sn)));
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

    function addToBatch() {
        const status = $("#status").val();
        const kdbarang = $("input[name='kdbarang']").val().trim();
        const nmbarang = $("#nmbarang").val();
        const jml = parseInt($("input[name='jml']").val()) || 0;
        
        var selectedSNs = $('#sn_select').val() || [];
        selectedSNs = selectedSNs.filter(function(s) { return s && s.trim() !== ''; });
        
        var hasSNOptions = $('#sn_select option').filter(function() { return $(this).val() !== ""; }).length > 0;

        resetValid();

        if (status == "false" || kdbarang == "") {
            validasi('Barang wajib dipilih terlebih dahulu!', 'warning');
            $("input[name='kdbarang']").addClass('is-invalid');
            return;
        }
        if (jml <= 0) {
            validasi('Jumlah harus lebih dari 0!', 'warning');
            $("input[name='jml']").addClass('is-invalid');
            return;
        }
        if (hasSNOptions && selectedSNs.length !== jml) {
            validasi('Jumlah SN yang terpilih (' + selectedSNs.length + ') harus sama dengan Jumlah Keluar (' + jml + ')!', 'warning');
            return;
        }

        // Cek apakah barang sudah ada di daftar
        const existingIndex = batchItems.findIndex(item => item.kode === kdbarang);
        if (existingIndex >= 0) {
            // Gabungkan jumlah dan SN jika sudah ada
            batchItems[existingIndex].jumlah += jml;
            batchItems[existingIndex].sns = batchItems[existingIndex].sns.concat(selectedSNs);
        } else {
            // Tambah item baru
            batchItems.push({
                kode: kdbarang,
                nama: nmbarang,
                jumlah: jml,
                sns: selectedSNs
            });
        }

        renderBatchTable();
        clearItemInput();
    }

    function renderBatchTable() {
        const tbody = $('#batchItemsBody');
        tbody.empty();

        if (batchItems.length === 0) {
            tbody.html(`
                <tr id="emptyBatchRow">
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="fe fe-inbox d-block mb-1" style="font-size:24px;"></i>
                        Belum ada barang. Pilih barang, tentukan SN, dan klik <strong>"Tambah ke Daftar"</strong>.
                    </td>
                </tr>
            `);
            $('#batchCount').text('0');
            $('#btnClearAll').addClass('d-none');
            return;
        }

        batchItems.forEach((item, index) => {
            let snText = item.sns.length > 0 ? item.sns.join(', ') : '<em>Tidak ada SN</em>';
            tbody.append(`
                <tr>
                    <td class="text-center align-middle">${index + 1}</td>
                    <td class="align-middle"><span class="badge bg-primary-transparent text-primary">${item.kode}</span></td>
                    <td class="align-middle">${item.nama}</td>
                    <td class="align-middle"><small>${snText}</small></td>
                    <td class="align-middle text-center"><strong>${item.jumlah}</strong></td>
                    <td class="align-middle text-center">
                        <button type="button" class="btn btn-sm btn-danger-light btn-icon" onclick="removeFromBatch(${index})" title="Hapus">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#batchCount').text(batchItems.length);
        $('#btnClearAll').removeClass('d-none');
    }

    function removeFromBatch(index) {
        batchItems.splice(index, 1);
        renderBatchTable();
        
        // Refresh dropdown SN jika barang yang sedang aktif sama
        var currentKdB = $("input[name='kdbarang']").val().trim();
        if(currentKdB) {
            fetchAvailableSNs(currentKdB);
        }
    }

    function clearBatch() {
        if (batchItems.length > 0) {
            swal({
                title: "Kosongkan Daftar?",
                text: "Semua barang di daftar akan dihapus.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }, function(isConfirm) {
                if (isConfirm) {
                    batchItems = [];
                    renderBatchTable();
                    
                    var currentKdB = $("input[name='kdbarang']").val().trim();
                    if(currentKdB) {
                        fetchAvailableSNs(currentKdB);
                    }
                }
            });
        }
    }

    function clearItemInput() {
        $("#status").val("false");
        $("input[name='kdbarang']").val('');
        $("#nmbarang").val('');
        $("#satuan").val('');
        $("#jenis").val('');
        $("input[name='kode_barang_unik']").val('');
        $("input[name='jml']").val('1');
        
        // Reset Select2 SN
        $('#sn_select').empty().append('<option value=""></option>');
        initSNSelect2();
        
        $("input[name='kdbarang']").focus();
    }

    function checkForm() {
        const tglkeluar = $("input[name='tglkeluar']").val();
        const customer  = $("input[name='customer']").val();
        const tujuan    = $("select[name='tujuan']").val() || $("input[name='tujuan']").val();
        
        setLoading(true);
        resetValid();

        if (tglkeluar == "") {
            validasi('Tanggal Keluar wajib di isi!', 'warning');
            $("input[name='tglkeluar']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (tujuan == "" || tujuan == null) {
            validasi('Nama Teknisi wajib di pilih!', 'warning');
            $("select[name='tujuan']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (customer == "") {
            validasi('Customer / Lokasi Instalasi wajib di isi!', 'warning');
            $("input[name='customer']").addClass('is-invalid');
            setLoading(false); return false;
        } else if (batchItems.length === 0) {
            validasi('Daftar barang masih kosong! Silakan tambah barang ke daftar terlebih dahulu.', 'warning');
            setLoading(false); return false;
        } else {
            submitForm();
        }
    }

    function submitForm() {
        const bkkode          = $("input[name='bkkode']").val();
        const tglkeluar       = $("input[name='tglkeluar']").val();
        const tujuan          = $("select[name='tujuan']").val() || $("input[name='tujuan']").val();
        const teknisi         = $("input[name='teknisi']").val();
        const keterangan      = $("input[name='keterangan']").val();
        const customer        = $("input[name='customer']").val();
        const lokasi          = $("input[name='lokasi']").val();

        $.ajax({
            type: 'POST',
            url: "{{ route('barang-keluar.store') }}",
            data: {
                bkkode: bkkode,
                tglkeluar: tglkeluar,
                tujuan: tujuan,
                teknisi: teknisi,
                keterangan: keterangan,
                customer: customer,
                lokasi: lokasi,
                items: batchItems
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
        
        batchItems = [];
        renderBatchTable();
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


    function initMap() {
        if (!map) {
            let defaultLat = -6.8732;
            let defaultLng = 107.5420;
            
            map = L.map('mapPicker').setView([defaultLat, defaultLng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                reverseGeocode(e.latlng.lat, e.latlng.lng);
            });

            marker.on('dragend', function(e) {
                let position = marker.getLatLng();
                reverseGeocode(position.lat, position.lng);
            });

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    let userLat = position.coords.latitude;
                    let userLng = position.coords.longitude;
                    map.setView([userLat, userLng], 15);
                    marker.setLatLng([userLat, userLng]);
                    reverseGeocode(userLat, userLng);
                });
            }
        } else {
            setTimeout(() => { map.invalidateSize(); }, 100);
        }
    }

    function reverseGeocode(lat, lng) {
        $('#selectedLocationText').text('Mencari alamat...');
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if(data && data.display_name) {
                    selectedAddress = data.display_name;
                    $('#selectedLocationText').text(selectedAddress);
                } else {
                    $('#selectedLocationText').text('Alamat tidak ditemukan');
                }
            }).catch(err => {
                $('#selectedLocationText').text('Gagal memuat alamat');
            });
    }

    function searchLocationMap() {
        let query = $('#mapSearchInput').val();
        if(!query) return;
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if(data && data.length > 0) {
                    let result = data[0];
                    let lat = result.lat;
                    let lon = result.lon;
                    map.setView([lat, lon], 16);
                    marker.setLatLng([lat, lon]);
                    selectedAddress = result.display_name;
                    $('#selectedLocationText').text(selectedAddress);
                } else {
                    alert('Lokasi tidak ditemukan');
                }
            });
    }

    function confirmLocation() {
        if(selectedAddress) {
            $('#' + mapTargetInput).val(selectedAddress);
            bootstrap.Modal.getInstance(document.getElementById('modalLocationPicker')).hide();
        } else {
            alert('Silakan pilih lokasi di peta terlebih dahulu.');
        }
    }
</script>
@endsection