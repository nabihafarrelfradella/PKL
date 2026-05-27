<!-- MODAL TAMBAH BARANG KELUAR / FORM PEMINJAMAN TEKNISI -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                        <small class="text-muted">SN Teknisi: <strong>{{ $currentUser->teknisi_sn ?? '-' }}</strong> &nbsp;|&nbsp; Pegawai Teknisi</small>
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
                            <input type="datetime-local" name="tglkeluar" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                        </div>

                        @if(($roleId ?? 0) == 3)
                        {{-- TEKNISI: auto-fill nama & SN dari session --}}
                        <div class="form-group">
                            <label class="form-label">Nama Teknisi</label>
                            <input type="text" class="form-control" value="{{ $currentUser->user_nmlengkap ?? '' }}" readonly style="background:#f0f8ff;">
                            <input type="hidden" name="tujuan" value="{{ $currentUser->user_nmlengkap ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SN Teknisi</label>
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
                            <select name="tujuan" id="tujuan" class="form-control select2" style="width: 100%;" onchange="getTeknisiInfo(this.value)">
                                <option value="">-- Pilih Teknisi --</option>
                                @foreach($pegawai as $pgw)
                                    <option value="{{ $pgw->user_nmlengkap }}">{{ $pgw->user_nmlengkap }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">SN Teknisi</label>
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
                            <input type="text" name="serial_number" class="form-control" placeholder="Terisi otomatis saat scan">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="text" name="jml" value="1" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');">
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
<script>
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
        $('.select2').select2({
            dropdownParent: $('#modaldemo8')
        });
        @endif
    });

    function getTeknisiInfo(nama) {
        if (!nama) { $("input[name='teknisi']").val(''); return; }
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/user-management/teknisi/get') }}/" + nama,
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
        $("#loaderkd").removeClass('d-none');
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/barang/getunit') }}/" + id,
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
                    $("input[name='serial_number']").val(data[0].serial_number);
                    $("input[name='kode_barang_unik']").val(data[0].kode_barang_unik);
                    if (id == data[0].kode_barang_unik || id == data[0].serial_number) {
                        $('input[name="kdbarang"]').val(data[0].barang_kode);
                    }
                } else {
                    $.ajax({
                        type: 'GET',
                        url: "{{ url('admin/barang/getbarang') }}/" + id,
                        success: function(data2) {
                            var resp = JSON.parse(data2);
                            if (resp.length > 0) {
                                $("#loaderkd").addClass('d-none');
                                $("#status").val("true");
                                $("#nmbarang").val(resp[0].barang_nama);
                                $("#satuan").val(resp[0].satuan_id);
                                $("#jenis").val(resp[0].jenisbarang_nama);
                                $("input[name='serial_number']").val('');
                                $("input[name='kode_barang_unik']").val('');
                            } else {
                                $("#loaderkd").addClass('d-none');
                                $("#status").val("false");
                                $("#nmbarang").val('');
                                $("#satuan").val('');
                                $("#jenis").val('');
                                $("input[name='serial_number']").val('');
                                $("input[name='kode_barang_unik']").val('');
                            }
                        }
                    });
                }
            }
        });
    }

    function checkForm() {
        const tglkeluar = $("input[name='tglkeluar']").val();
        const status    = $("#status").val();
        const jml       = $("input[name='jml']").val();
        const customer  = $("input[name='customer']").val();
        setLoading(true);
        resetValid();

        if (tglkeluar == "") {
            validasi('Tanggal Keluar wajib di isi!', 'warning');
            $("input[name='tglkeluar']").addClass('is-invalid');
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
        const serial_number   = $("input[name='serial_number']").val();
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
        $("input[name='kdbarang']").removeClass('is-invalid');
        $("input[name='customer']").removeClass('is-invalid');
        $("input[name='jml']").removeClass('is-invalid');
    };

    function reset() {
        resetValid();
        $("input[name='bkkode']").val('');
        $("input[name='tglkeluar']").val('');
        $("input[name='kdbarang']").val('');
        $("input[name='customer']").val('');
        $("input[name='keterangan']").val('');
        $("input[name='serial_number']").val('');
        $("input[name='jml']").val('1');
        $("#nmbarang").val('');
        $("#satuan").val('');
        $("#jenis").val('');
        $("#status").val('false');
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