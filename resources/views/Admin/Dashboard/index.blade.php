@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">Admin</li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
    <div class="ms-auto pageheader-btn">
        <a href="#modalTracking" data-bs-toggle="modal" class="btn btn-primary btn-icon text-white me-2">
            <span>
                <i class="fe fe-search"></i>
            </span> Cek Resi / Tracking
        </a>
    </div>
</div>
<!-- PAGE-HEADER END -->

<!-- ROW 1 OPEN -->
<div class="row">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-primary img-card box-primary-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$jenis}}</h2>
                        <p class="text-white mb-0">Jenis Barang </p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-package text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card  bg-success img-card box-success-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$merk}}</h2>
                        <p class="text-white mb-0">Merk Barang</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-package text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-info img-card box-info-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$barang}}</h2>
                        <p class="text-white mb-0">Barang</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-package text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-success img-card box-success-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$bm}}</h2>
                        <p class="text-white mb-0">Barang Masuk</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-repeat text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-danger img-card box-danger-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$bk}}</h2>
                        <p class="text-white mb-0">Barang Keluar</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-repeat text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-purple img-card box-purple-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$customer}}</h2>
                        <p class="text-white mb-0">Customer</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-user text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
        <div class="card bg-warning img-card box-warning-shadow">
            <div class="card-body">
                <div class="d-flex">
                    <div class="text-white">
                        <h2 class="mb-0 number-font">{{$user}}</h2>
                        <p class="text-white mb-0">User</p>
                    </div>
                    <div class="ms-auto"> <i class="fe fe-user text-white fs-40 me-2 mt-2"></i> </div>
                </div>
            </div>
        </div>
    </div>
    <!-- COL END -->
</div>
<!-- ROW 1 CLOSED -->

<!-- MODAL TRACKING -->
<div class="modal fade" data-bs-backdrop="static" id="modalTracking">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Tracking Serial Number / Resi</h6>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group mb-4">
                    <label class="form-label">Masukkan Resi (Kode Unik / Serial Number)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="inputResi" placeholder="Scan QR atau ketik disini...">
                        <button class="btn btn-primary" onclick="cekResi()" type="button"><i class="fe fe-search"></i> Cek Tracking</button>
                    </div>
                </div>

                <div id="trackingContainer" class="d-none">
                    <h5 class="fw-bold mb-3">Histori Transaksi Barang Masuk</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered text-nowrap border-bottom">
                            <thead>
                                <tr>
                                    <th>Kode BM</th>
                                    <th>Kode Unik / Resi</th>
                                    <th>Serial Number</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Waktu Masuk</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyMasuk"></tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold mb-3">Histori Transaksi Barang Keluar</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap border-bottom">
                            <thead>
                                <tr>
                                    <th>Kode BK</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                    <th>Teknisi / Keterangan</th>
                                    <th>Waktu Keluar</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyKeluar"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Tutup <i class="fe fe-x"></i></button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('input#inputResi').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            cekResi();
        }
    });

    function cekResi() {
        const resi = $('#inputResi').val();
        if (resi === '') {
            swal({
                title: 'Peringatan',
                text: 'Silahkan isi/scan Resi terlebih dahulu',
                type: 'warning'
            });
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('dashboard.cekResi') }}",
            data: { resi: resi },
            success: function(response) {
                $('#trackingContainer').removeClass('d-none');
                
                // Render Masuk
                let htmlMasuk = '';
                if (response.masuk.length > 0) {
                    response.masuk.forEach(m => {
                        htmlMasuk += `
                        <tr>
                            <td>${m.bm_kode}</td>
                            <td>${m.kode_barang_unik || '-'}</td>
                            <td>${m.serial_number || '-'}</td>
                            <td>${m.barang_nama}</td>
                            <td>${m.bm_jumlah}</td>
                            <td>${m.jam_masuk || m.bm_tanggal}</td>
                        </tr>`;
                    });
                } else {
                    htmlMasuk = '<tr><td colspan="6" class="text-center">Data kosong</td></tr>';
                }
                $('#tbodyMasuk').html(htmlMasuk);

                // Render Keluar
                let htmlKeluar = '';
                if (response.keluar.length > 0) {
                    response.keluar.forEach(k => {
                        let note = '';
                        if (k.teknisi) note += 'Teknisi: ' + k.teknisi + ' ';
                        if (k.keterangan) note += 'Keterangan: ' + k.keterangan;
                        
                        htmlKeluar += `
                        <tr>
                            <td>${k.bk_kode}</td>
                            <td>${k.barang_nama}</td>
                            <td>${k.bk_jumlah}</td>
                            <td>${k.bk_tujuan || '-'}</td>
                            <td>${k.bk_status}</td>
                            <td>${note || '-'}</td>
                            <td>${k.jam_keluar || k.bk_tanggal}</td>
                        </tr>`;
                    });
                } else {
                    htmlKeluar = '<tr><td colspan="7" class="text-center">Data kosong</td></tr>';
                }
                $('#tbodyKeluar').html(htmlKeluar);
            }
        });
    }
</script>
@endsection