@extends('Master.Layouts.app', ['title' => $title])

@section('content')
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
            min-height: 110px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-info {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            z-index: 2;
        }

        .stat-card .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: #fff;
            line-height: 1;
        }

        .stat-card .stat-info p {
            font-size: 0.85rem;
            margin: 5px 0 0 0;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.3);
            line-height: 1;
            z-index: 2;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            bottom: -15px;
            right: 50px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: -25px;
            right: -10px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }
    </style>

    <div class="page-header">
        <h1 class="page-title">Dashboard Statistik</h1>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('barang-tracking.index') }}" class="btn btn-secondary btn-icon text-white me-2">
                <i class="fe fe-activity me-1"></i> Barang Tracking
            </a>
            <a href="#modalTracking" data-bs-toggle="modal" class="btn btn-primary btn-icon text-white">
                <i class="fe fe-search me-1"></i> Cek Resi / Tracking
            </a>
        </div>
    </div>
    @if(Session::has('status'))
        <div class="alert alert-{{ Session::get('status') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
            role="alert">
            <i class="fe fe-{{ Session::get('status') == 'success' ? 'check-circle' : 'alert-circle' }} me-1"></i>
            {{ Session::get('msg') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-5">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card" style="background: #1fba8c;">
                <div class="stat-info">
                    <h3>{{$merk}}</h3>
                    <p>Merk Barang</p>
                </div>
                <div class="stat-icon"><i class="fe fe-tag"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="stat-card" style="background: #2f80ed;">
                <div class="stat-info">
                    <h3>{{$barang}}</h3>
                    <p>Total Barang</p>
                </div>
                <div class="stat-icon"><i class="fe fe-package"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="stat-card" style="background: #0bbfaa;">
                <div class="stat-info">
                    <h3>{{$bm}}</h3>
                    <p>Barang Masuk</p>
                </div>
                <div class="stat-icon"><i class="fe fe-arrow-down-circle"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background: #e84c4c;">
                <div class="stat-info">
                    <h3>{{$bk_dipinjam}}</h3>
                    <p>Sedang Dipinjam</p>
                </div>
                <div class="stat-icon"><i class="fe fe-arrow-up-circle"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background: #f0a500;">
                <div class="stat-info">
                    <h3>{{$bk}}</h3>
                    <p>Total Transaksi Keluar</p>
                </div>
                <div class="stat-icon"><i class="fe fe-repeat"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background: #9b3fdb;">
                <div class="stat-info">
                    <h3>{{$teknisi}}</h3>
                    <p>Pegawai Teknisi</p>
                </div>
                <div class="stat-icon"><i class="fe fe-users"></i></div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="stat-card" style="background: #17a2b8;">
                <div class="stat-info">
                    <h3>{{$user}}</h3>
                    <p>Total Akun User</p>
                </div>
                <div class="stat-icon"><i class="fe fe-user"></i></div>
            </div>
        </div>
    </div>

    {{-- PANEL BARANG DIPINJAM — hanya untuk Owner & Admin Gudang --}}
    @php $userRole = Session::get('user')->role_id ?? 0; @endphp
    @if(in_array($userRole, [1, 2]))
    <div class="row row-sm mb-4">
        <div class="col-lg-12">
            <div class="card" style="border-top: 3px solid #e84c4c;">
                <div class="card-header justify-content-between" style="background: linear-gradient(135deg,#fff5f5,#fff);">
                    <h3 class="card-title">
                        <i class="fe fe-alert-circle text-danger me-2"></i>Barang Sedang Dipinjam
                        <span class="badge bg-danger ms-2">{{ $bk_dipinjam }}</span>
                    </h3>
                    <a href="{{ url('admin/barang-keluar') }}" class="btn btn-sm btn-outline-danger">
                        <i class="fe fe-eye me-1"></i>Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($bk_dipinjam == 0)
                        <div class="text-center py-5 text-muted">
                            <i class="fe fe-check-circle d-block mb-2 text-success" style="font-size:2.5rem;"></i>
                            <p class="mb-0 fw-semibold">Semua barang sudah dikembalikan!</p>
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Kode BK</th>
                                    <th>Barang</th>
                                    <th>Teknisi</th>
                                    <th>Customer / Lokasi</th>
                                    <th>Jam Keluar</th>
                                    <th>Jml</th>
                                    <th>SN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dipinjamList = \App\Models\Admin\BarangkeluarModel::leftJoin('tbl_barang','tbl_barang.barang_kode','=','tbl_barangkeluar.barang_kode')
                                        ->leftJoin('tbl_user','tbl_user.teknisi_sn','=','tbl_barangkeluar.teknisi')
                                        ->where('tbl_barangkeluar.bk_status','Dipinjam')
                                        ->select('tbl_barangkeluar.*','tbl_barang.barang_nama','tbl_user.user_nmlengkap as nm_teknisi')
                                        ->orderBy('tbl_barangkeluar.jam_keluar','DESC')
                                        ->get();
                                @endphp
                                @foreach($dipinjamList as $dp)
                                @php
                                    $jamKeluar = $dp->jam_keluar ? \Carbon\Carbon::parse($dp->jam_keluar) : null;
                                    $durasi    = $jamKeluar ? $jamKeluar->diffForHumans() : '-';
                                    $rowBg     = ($jamKeluar && $jamKeluar->diffInHours() > 24) ? '#fff0f0' : '#fffef0';
                                @endphp
                                <tr style="background:{{ $rowBg }};">
                                    <td class="ps-3"><strong class="text-danger">{{ $dp->bk_kode }}</strong></td>
                                    <td>{{ $dp->barang_nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge" style="background:#e0e7ff;color:#3730a3;">
                                            <i class="fe fe-tool me-1"></i>{{ $dp->nm_teknisi ?? $dp->teknisi ?? '-' }}
                                        </span>
                                    </td>
                                    <td><i class="fe fe-map-pin text-muted me-1"></i><strong>{{ $dp->bk_tujuan ?? '-' }}</strong></td>
                                    <td><span title="{{ $jamKeluar ? $jamKeluar->format('d/m/Y H:i') : '-' }}">{{ $durasi }}</span></td>
                                    <td>{{ $dp->bk_jumlah }}</td>
                                    <td><small class="text-muted">{{ Str::limit($dp->serial_number ?? '-', 15) }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif


    <div class="modal fade" data-bs-backdrop="static" id="modalTracking">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title"><i class="fe fe-search me-1"></i> Tracking Serial Number / Resi</h6>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="form-label fw-bold">Masukkan Resi (Kode Unik / Serial Number / Kode BM/BK)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputResi"
                                placeholder="Scan QR atau ketik disini...">
                            <button class="btn btn-primary" onclick="cekResi()" type="button"><i class="fe fe-search"></i>
                                Cek Tracking</button>
                        </div>
                    </div>

                    <div id="trackingContainer" class="d-none">
                        <h5 class="fw-bold mb-3"><i class="fe fe-arrow-down-circle text-success me-1"></i> Histori Transaksi
                            Barang Masuk</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered text-nowrap border-bottom">
                                <thead class="table-light">
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

                        <h5 class="fw-bold mb-3"><i class="fe fe-arrow-up-circle text-danger me-1"></i> Histori Transaksi
                            Barang Keluar</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap border-bottom">
                                <thead class="table-light">
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

        $('input#inputResi').keypress(function (event) {
            if (event.keyCode == '13') {
                cekResi();
            }
        });

        $.ajax({
            type: 'GET',
            url: "{{route('barang.checkStok')}}",
            success: function (data) {
                if (data.length > 0) {
                    let htmlList = '<ul style="text-align: left;">';
                    data.forEach(item => {
                        htmlList += `<li><b>${item.nama}</b> (Stok: ${item.stok})</li>`;
                    });
                    htmlList += '</ul>';

                    swal({
                        title: "Peringatan Stok Menipis!",
                        html: true,
                        text: "Beberapa barang memiliki stok kurang dari 5:<br><br>" + htmlList,
                        type: "warning",
                        confirmButtonText: "Tutup"
                    });
                }
            }
        });

        function cekResi() {
            const resi = $('#inputResi').val().trim();
            if (resi === '') {
                swal({ title: 'Peringatan', text: 'Silahkan isi/scan Resi terlebih dahulu', type: 'warning' });
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('dashboard.cekResi') }}",
                data: { resi: resi },
                success: function (response) {
                    $('#trackingContainer').removeClass('d-none');

                    // Render Masuk
                    let htmlMasuk = '';
                    if (response.masuk && response.masuk.length > 0) {
                        response.masuk.forEach(m => {
                            htmlMasuk += `
                            <tr>
                                <td><strong>${m.bm_kode}</strong></td>
                                <td>${m.kode_barang_unik || '-'}</td>
                                <td>${m.serial_number || '-'}</td>
                                <td>${m.barang_nama || '-'}</td>
                                <td>${m.bm_jumlah}</td>
                                <td>${m.jam_masuk || m.bm_tanggal || '-'}</td>
                            </tr>`;
                        });
                    } else {
                        htmlMasuk = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data barang masuk</td></tr>';
                    }
                    $('#tbodyMasuk').html(htmlMasuk);

                    // Render Keluar
                    let htmlKeluar = '';
                    if (response.keluar && response.keluar.length > 0) {
                        response.keluar.forEach(k => {
                            let note = '';
                            if (k.teknisi) note += 'Teknisi: ' + k.teknisi + ' ';
                            if (k.keterangan) note += 'Keterangan: ' + k.keterangan;

                            const statusBadge = k.bk_status == 'Dipinjam'
                                ? '<span class="badge bg-warning">Dipinjam</span>'
                                : '<span class="badge bg-success">Selesai</span>';

                            htmlKeluar += `
                            <tr>
                                <td><strong>${k.bk_kode}</strong></td>
                                <td>${k.barang_nama || '-'}</td>
                                <td>${k.bk_jumlah}</td>
                                <td>${k.bk_tujuan || '-'}</td>
                                <td>${statusBadge}</td>
                                <td>${note || '-'}</td>
                                <td>${k.jam_keluar || k.bk_tanggal || '-'}</td>
                            </tr>`;
                        });
                    } else {
                        htmlKeluar = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data barang keluar</td></tr>';
                    }
                    $('#tbodyKeluar').html(htmlKeluar);
                },
                error: function () {
                    swal({ title: 'Terjadi kesalahan!', type: 'error' });
                }
            });
        }
    </script>
@endsection