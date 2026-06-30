<!-- MODAL TAMBAH -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-plus-circle me-1"></i>Tambah Barang Masuk</h6><button onclick="reset()" aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <style>
                    #modaldemo8 .form-group { margin-bottom: 0.5rem; }
                    #modaldemo8 .my-3 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
                    #modaldemo8 .form-label, #modaldemo8 label { margin-bottom: 0.25rem; font-size: 0.85rem; }
                </style>
                {{-- ── BAGIAN INPUT ── --}}
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group d-none">
                            <label for="bmkode" class="form-label">Kode Barang Masuk <span class="text-danger">*</span></label>
                            <input type="text" name="bmkode" readonly class="form-control" placeholder="Otomatis">
                        </div>
                        <div class="form-group">
                            <label for="tglmasuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tglmasuk" id="tglmasuk_input" class="form-control">
                        </div>
                        <div class="form-group d-none">
                            <label for="serial_number" class="form-label">Serial Number</label>
                            <input type="text" name="serial_number" class="form-control" placeholder="Otomatis">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="form-group">
                            <label>Kode Barang <span class="text-danger me-1">*</span>
                                <input type="hidden" id="status" value="false">
                                <div class="spinner-border spinner-border-sm d-none" id="loaderkd" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" autocomplete="off" name="kdbarang" placeholder="">
                                <button class="btn btn-primary-light" onclick="searchBarang()" type="button"><i class="fe fe-search"></i></button>
                                <button class="btn btn-success-light" onclick="modalBarang()" type="button"><i class="fe fe-box"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Barang</label>
                                    <input type="text" class="form-control" id="nmbarang" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jml" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="text" name="jml" value="1" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^0[^.]/, '0');" placeholder="">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="satuan">
                        <input type="hidden" id="jenis">
                        <div class="text-end">
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
                        <i class="fe fe-list me-1 text-primary"></i>Daftar Barang
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
                                <th>Satuan</th>
                                <th>Jenis</th>
                                <th width="8%">Jumlah</th>
                                <th width="1%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="batchItemsBody">
                            <tr id="emptyBatchRow">
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="fe fe-inbox d-block mb-1" style="font-size:24px;"></i>
                                    Belum ada barang. Pilih barang dan klik <strong>"Tambah ke Daftar"</strong>.
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
                <a href="javascript:void(0)" onclick="checkForm()" id="btnSimpan" class="btn btn-primary">Simpan Semua <i class="fe fe-check"></i></a>
                <a href="javascript:void(0)" class="btn btn-light" onclick="reset()" data-bs-dismiss="modal">Batal <i class="fe fe-x"></i></a>
            </div>
        </div>
    </div>
</div>


@section('formTambahJS')
<script>
    // ── Batch Items Storage ──
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
        $("input[name='tglmasuk']").val(getLocalDateTimeString());
    });

    $('input[name="kdbarang"]').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            getbarangbyid($('input[name="kdbarang"]').val());
        }
    });

    // Enter pada field jumlah → langsung tambah ke daftar
    $('input[name="jml"]').keypress(function(event) {
        if (event.keyCode == '13') {
            addToBatch();
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

    function getbarangbyid(id) {
        $("#loaderkd").removeClass('d-none');
        $.ajax({
            type: 'GET',
            url: "/admin/barang/getbarang/" + id,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $("#loaderkd").addClass('d-none');
                    $("#status").val("true");
                    $("#nmbarang").val(data[0].barang_nama);
                    $("#satuan").val(data[0].satuan_id); // Mengambil nama satuan (sesuai field di DB)
                    $("#jenis").val(data[0].tipe_barang);
                } else {
                    $("#loaderkd").addClass('d-none');
                    $("#status").val("false");
                    $("#nmbarang").val('');
                    $("#satuan").val('');
                    $("#jenis").val('');
                }
            }
        });
    }

    // ── Tambah item ke daftar batch ──
    function addToBatch() {
        const status = $("#status").val();
        const kdbarang = $("input[name='kdbarang']").val().trim();
        const nmbarang = $("#nmbarang").val();
        const satuan = $("#satuan").val();
        const jenis = $("#jenis").val();
        const jml = parseInt($("input[name='jml']").val()) || 0;

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

        // Cek apakah barang sudah ada di daftar
        const existingIndex = batchItems.findIndex(item => item.kode === kdbarang);
        if (existingIndex >= 0) {
            // Jika sudah ada, tambahkan jumlahnya
            batchItems[existingIndex].jumlah += jml;
        } else {
            // Tambah item baru
            batchItems.push({
                kode: kdbarang,
                nama: nmbarang,
                satuan: satuan,
                jenis: jenis,
                jumlah: jml
            });
        }

        renderBatchTable();
        clearItemInput();
    }

    // ── Render tabel batch ──
    function renderBatchTable() {
        const tbody = $('#batchItemsBody');
        tbody.empty();

        if (batchItems.length === 0) {
            tbody.html(`
                <tr id="emptyBatchRow">
                    <td colspan="7" class="text-center text-muted py-3">
                        <i class="fe fe-inbox d-block mb-1" style="font-size:24px;"></i>
                        Belum ada barang. Pilih barang dan klik <strong>"Tambah ke Daftar"</strong>.
                    </td>
                </tr>
            `);
            $('#batchCount').text('0');
            $('#btnClearAll').addClass('d-none');
            return;
        }

        $('#btnClearAll').removeClass('d-none');

        let totalItems = 0;
        batchItems.forEach(function(item, index) {
            totalItems += item.jumlah;
            tbody.append(`
                <tr>
                    <td class="text-center">${index + 1}</td>
                    <td><code>${item.kode}</code></td>
                    <td>${item.nama}</td>
                    <td>${item.satuan}</td>
                    <td>${item.jenis}</td>
                    <td class="text-center fw-bold">${item.jumlah}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBatchItem(${index})" title="Hapus">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#batchCount').text(batchItems.length + ' jenis, ' + totalItems + ' unit');
    }

    // ── Hapus satu item dari batch ──
    function removeBatchItem(index) {
        batchItems.splice(index, 1);
        renderBatchTable();
    }

    // ── Hapus semua item batch ──
    function clearBatch() {
        swal({
            title: "Yakin hapus semua?",
            text: "Semua barang dalam daftar akan dihapus.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }, function(isConfirm) {
            if (isConfirm) {
                batchItems = [];
                renderBatchTable();
            }
        });
    }

    // ── Clear form input barang (tanpa reset tanggal & daftar) ──
    function clearItemInput() {
        $("input[name='kdbarang']").val('');
        $("input[name='jml']").val('1');
        $("#nmbarang").val('');
        $("#satuan").val('');
        $("#jenis").val('');
        $("#status").val('false');
        resetValid();
        // Focus kembali ke field kode barang
        $("input[name='kdbarang']").focus();
    }

    function checkForm() {
        const tglmasuk = $("input[name='tglmasuk']").val();
        resetValid();

        if (tglmasuk == "") {
            validasi('Tanggal Masuk wajib diisi!', 'warning');
            $("input[name='tglmasuk']").addClass('is-invalid');
            return false;
        }

        if (batchItems.length === 0) {
            validasi('Daftar barang masih kosong! Tambahkan minimal 1 barang.', 'warning');
            return false;
        }

        setLoading(true);
        submitForm();
    }

    function submitForm() {
        const tglmasuk = $("input[name='tglmasuk']").val();

        $.ajax({
            type: 'POST',
            url: "{{ route('barang-masuk.store') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tglmasuk: tglmasuk,
                items: batchItems
            },
            success: function(data) {
                $('#modaldemo8').modal('hide');
                swal({
                    title: "Berhasil!",
                    text: data.success || "Data barang masuk telah ditambahkan.",
                    type: "success"
                });
                table.ajax.reload(null, false);
                reset();
            },
            error: function(xhr) {
                const pesan = xhr.responseJSON ? xhr.responseJSON.error : "Terjadi kesalahan sistem";
                swal({
                    title: "Gagal Simpan!",
                    text: pesan,
                    type: "error"
                });
                setLoading(false);
            }
        });
    }

    function resetValid() {
        $("input[name='tglmasuk']").removeClass('is-invalid');
        $("input[name='kdbarang']").removeClass('is-invalid');
        $("input[name='serial_number']").removeClass('is-invalid');
        $("input[name='jml']").removeClass('is-invalid');
    };

    function reset() {
        resetValid();
        $("input[name='bmkode']").val('Otomatis');
        $("input[name='tglmasuk']").val(getLocalDateTimeString());
        $("input[name='kdbarang']").val('');
        $("input[name='serial_number']").val('');
        $("input[name='jml']").val('1');
        $("#nmbarang").val('');
        $("#satuan").val('');
        $("#jenis").val('');
        $("#status").val('false');
        batchItems = [];
        renderBatchTable();
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