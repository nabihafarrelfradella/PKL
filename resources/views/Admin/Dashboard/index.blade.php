@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="ms-auto pageheader-btn">
        <a href="#modalTracking" data-bs-toggle="modal" class="btn btn-primary btn-icon text-white me-2">
            <span><i class="fe fe-search"></i></span> Cek Resi / Tracking
        </a>
    </div>
</div>
<!-- PAGE-HEADER END -->

<style>
    .stat-card {
        border-radius: 14px;
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0;
        position: relative;
        overflow: hidden;
        min-height: 100px;
    }
    .stat-card .stat-info {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    .stat-card .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 2px 0;
        color: #fff;
        line-height: 1;
    }
    .stat-card .stat-info p {
        font-size: 0.82rem;
        margin: 0;
        color: rgba(255,255,255,0.85);
        font-weight: 400;
    }
    .stat-card .stat-icon {
        font-size: 2.2rem;
        color: rgba(255,255,255,0.35);
        line-height: 1;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        bottom: -18px;
        right: 60px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -30px;
        right: 10px;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
</style>

<!-- ROW STAT CARDS -->
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #6c63d5;">
                <div class="stat-info">
                    <h3>{{$jenis}}</h3>
                    <p>Jenis Barang</p>
                </div>
                <div class="stat-icon"><i class="fe fe-package"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #1fba8c;">
                <div class="stat-info">
                    <h3>{{$merk}}</h3>
                    <p>Merk Barang</p>
                </div>
                <div class="stat-icon"><i class="fe fe-package"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #2f80ed;">
                <div class="stat-info">
                    <h3>{{$barang}}</h3>
                    <p>Barang</p>
                </div>
                <div class="stat-icon"><i class="fe fe-package"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #0bbfaa;">
                <div class="stat-info">
                    <h3>{{$bm}}</h3>
                    <p>Barang Masuk</p>
                </div>
                <div class="stat-icon"><i class="fe fe-repeat"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #e84c4c;">
                <div class="stat-info">
                    <h3>{{$bk}}</h3>
                    <p>Barang Keluar</p>
                </div>
                <div class="stat-icon"><i class="fe fe-repeat"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #9b3fdb;">
                <div class="stat-info">
                    <h3>{{$customer}}</h3>
                    <p>Customer</p>
                </div>
                <div class="stat-icon"><i class="fe fe-user"></i></div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card mb-0" style="border-radius:14px; border:none;">
            <div class="stat-card" style="background: #f0a500;">
                <div class="stat-info">
                    <h3>{{$user}}</h3>
                    <p>User</p>
                </div>
                <div class="stat-icon"><i class="fe fe-user"></i></div>
            </div>
        </div>
    </div>
</div>
<!-- ROW CLOSED -->

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

@endsection

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