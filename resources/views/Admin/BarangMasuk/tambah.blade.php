<!-- MODAL TAMBAH -->
<div class="modal fade" data-bs-backdrop="static" id="modaldemo8">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fe fe-plus-circle me-1"></i>Tambah Barang Masuk</h6><button onclick="reset()" aria-label="Close" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <style>
                    #modaldemo8 .form-group { margin-bottom: 0.5rem; }
                    #modaldemo8 .my-3 { margin-top: 0.5rem !important; margin-bottom: 0.5rem !important; }
                    #modaldemo8 .form-label, #modaldemo8 label { margin-bottom: 0.25rem; font-size: 0.85rem; }
                </style>
                {{-- â”€â”€ BAGIAN INPUT â”€â”€ --}}
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="form-group d-none">
                            <label for="bmkode" class="form-label">Kode Barang Masuk <span class="text-danger">*</span></label>
                            <input type="text" name="bmkode" readonly class="form-control" placeholder="Otomatis">
                        </div>
                        <div class="form-group">
                            <label for="tglmasuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="tglmasuk" id="tglmasuk_input" class="form-control">
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
                            <div class="input-group" style="position: relative;">
                                <input type="text" class="form-control" autocomplete="off" name="kdbarang" id="kdbarang" placeholder="">
                                <button class="btn btn-primary-light" onclick="searchBarang()" type="button"><i class="fe fe-search"></i></button>
                                <button class="btn btn-success-light" onclick="modalBarang()" type="button"><i class="fe fe-box"></i></button>

                                <ul id="autocomplete-list" class="list-group position-absolute w-100" style="top: 100%; z-index: 1050; display: none; max-height: 300px; overflow-y: auto; box-shadow: 0px 4px 12px rgba(0,0,0,0.15);"></ul>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Nama Barang</label>
                                    <input type="text" class="form-control" id="nmbarang" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jml" class="form-label">Jumlah <span class="text-danger">*</span></label>
                                    <input type="text" name="jml" value="1" class="form-control" readonly style="background-color: #f3f6f9; cursor: not-allowed;" title="Jumlah tidak bisa diubah" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Barang</label>
                                    <input type="text" class="form-control" id="jenis" readonly placeholder="Otomatis">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="serial_number" class="form-label">Serial Number</label>
                                    <input type="text" name="serial_number" class="form-control" placeholder="Isi manual (Opsional)">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="satuan">
                        <div class="text-end">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addToBatch()">
                                <i class="fe fe-plus me-1"></i>Tambah ke Daftar
                            </button>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                {{-- â”€â”€ DAFTAR BARANG YANG AKAN DISIMPAN â”€â”€ --}}
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
                                <th>Serial Number</th>
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
    // â”€â”€ Batch Items Storage â”€â”€
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

    // Enter pada field jumlah â†’ langsung tambah ke daftar
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

    // â”€â”€ Tambah item ke daftar batch â”€â”€
    function addToBatch() {
        const status = $("#status").val();
        const kdbarang = $("input[name='kdbarang']").val().trim();
        const nmbarang = $("#nmbarang").val();
        const satuan = $("#satuan").val();
        const jenis = $("#jenis").val();
        const sn = $("input[name='serial_number']").val().trim();
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

        // Cek apakah barang sudah ada di daftar (berdasarkan kode barang saja)
        const existingIndex = batchItems.findIndex(item => item.kode === kdbarang);
        const displaySn = sn === '' ? '-' : sn;

        if (existingIndex >= 0) {
            // Jika sudah ada, tambahkan totalJumlahnya
            batchItems[existingIndex].totalJumlah += jml;

            // Cek apakah SN tersebut sudah ada di daftar details untuk barang ini
            const detailIndex = batchItems[existingIndex].details.findIndex(d => d.sn === displaySn);
            if (detailIndex >= 0) {
                batchItems[existingIndex].details[detailIndex].jumlah += jml;
            } else {
                batchItems[existingIndex].details.push({ sn: displaySn, jumlah: jml });
            }
        } else {
            // Tambah item baru
            batchItems.push({
                kode: kdbarang,
                nama: nmbarang,
                satuan: satuan,
                jenis: jenis,
                totalJumlah: jml,
                details: [
                    { sn: displaySn, jumlah: jml }
                ]
            });
        }

        renderBatchTable();
        clearItemInput();
    }

    // â”€â”€ Render tabel batch â”€â”€
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
            totalItems += item.totalJumlah;

            let snHtml = item.details.map((d, dIdx) => {
                let badge = d.sn === '-' ? '<span class="text-muted">-</span>' : `<code>${d.sn}</code>`;
                return `<div class="d-flex align-items-center justify-content-between mb-1">
                            <div style="min-width: 100px;">${badge}</div>
                            <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="removeBatchDetail(${index}, ${dIdx})" title="Hapus SN">
                                <i class="fe fe-trash-2 fs-12"></i>
                            </button>
                        </div>`;
            }).join('');

            tbody.append(`
                <tr>
                    <td class="text-center align-middle">${index + 1}</td>
                    <td class="align-middle"><code>${item.kode}</code></td>
                    <td class="align-middle">${item.nama}</td>
                    <td class="align-middle">${item.satuan}</td>
                    <td class="align-middle">${item.jenis}</td>
                    <td class="align-middle">${snHtml}</td>
                    <td class="text-center fw-bold align-middle">${item.totalJumlah}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBatchItem(${index})" title="Hapus">
                            <i class="fe fe-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `);
        });

        $('#batchCount').text(batchItems.length + ' jenis, ' + totalItems + ' unit');
    }

    // â”€â”€ Hapus satu item dari batch â”€â”€
    function removeBatchItem(index) {
        batchItems.splice(index, 1);
        renderBatchTable();
    }

    // â”€â”€ Hapus detail SN dari batch â”€â”€
    function removeBatchDetail(itemIndex, detailIndex) {
        let item = batchItems[itemIndex];
        let detail = item.details[detailIndex];
        item.totalJumlah -= detail.jumlah;
        item.details.splice(detailIndex, 1);
        
        if (item.details.length === 0) {
            batchItems.splice(itemIndex, 1);
        }
        renderBatchTable();
    }

    // â”€â”€ Hapus semua item batch â”€â”€
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

    // â”€â”€ Clear form input barang (tanpa reset tanggal & daftar) â”€â”€
    function clearItemInput() {
        $("input[name='jml']").val('1');
        $("input[name='serial_number']").val('');
        resetValid();
        // Focus kembali ke field serial number agar cepat menginput SN berikutnya
        $("input[name='serial_number']").focus();
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

        // Flatten payload sebelum dikirim
        let payload = [];
        batchItems.forEach(item => {
            item.details.forEach(detail => {
                payload.push({
                    kode: item.kode,
                    jumlah: detail.jumlah,
                    sn: detail.sn === '-' ? '' : detail.sn
                });
            });
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('barang-masuk.store') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                tglmasuk: tglmasuk,
                items: payload
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

    // â”€â”€ Autocomplete Logic â”€â”€
    $(document).ready(function() {
        let timer;
        const inputKd = $('#kdbarang');
        const list = $('#autocomplete-list');

        // Add CSS for autocomplete hover
        $("<style>")
            .prop("type", "text/css")
            .html(`
                #autocomplete-list .list-group-item { background-color: #fff; transition: all 0.2s; }
                #autocomplete-list .list-group-item:hover { background-color: #f8f9fa !important; }
            `)
            .appendTo("head");

        inputKd.on('keyup focus', function() {
            clearTimeout(timer);
            let val = $(this).val();
            if (val.length < 1) {
                list.hide();
                return;
            }
            timer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('barang.autocomplete') }}",
                    data: { term: val },
                    success: function(data) {
                        list.empty();
                        if (data.length > 0) {
                            data.forEach(item => {
                                list.append(`
                                    <li class="list-group-item list-group-item-action d-flex align-items-center" style="cursor: pointer;" onclick='pilihAutocomplete(${JSON.stringify(item)})'>
                                        <img src="${item.foto}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 12px; border: 1px solid #ddd;">
                                        <div>
                                            <div class="fw-bold fs-13 mb-0" style="line-height: 1.2;">${item.nama}</div>
                                            <small class="text-muted" style="font-size: 11px;">${item.kode}</small>
                                        </div>
                                    </li>
                                `);
                            });
                            list.show();
                        } else {
                            list.hide();
                        }
                    }
                });
            }, 250);
        });

        // Hide list if clicked outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.input-group').length) {
                list.hide();
            }
        });
    });

    function pilihAutocomplete(item) {
        $("input[name='kdbarang']").val(item.kode);
        $("#nmbarang").val(item.nama);
        $("#satuan").val(item.satuan);
        $("#jenis").val(item.jenis);
        $("#status").val("true");
        $('#autocomplete-list').hide();
        $("input[name='serial_number']").focus();
    }
</script>
@endsection
