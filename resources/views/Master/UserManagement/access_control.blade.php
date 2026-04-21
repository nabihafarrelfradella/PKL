@extends('Master.Layouts.app', ['title' => $title])

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <div>
        <h1 class="page-title">Access Control</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item text-gray">User Management</li>
            <li class="breadcrumb-item active">Access Control</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<div class="alert alert-info">
    <i class="fe fe-lock me-1"></i>
    <strong>Hak akses di-hardcode dalam sistem</strong> — tidak dapat diubah melalui antarmuka ini. Halaman ini bersifat informatif.
</div>

<!-- TABEL PERBANDINGAN ROLE -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fe fe-shield me-1"></i> Tabel Hak Akses per Role</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-start">Fitur / Modul</th>
                                <th><span class="badge bg-danger fs-13">OWNER</span></th>
                                <th><span class="badge bg-primary fs-13">ADMIN GUDANG</span></th>
                                <th><span class="badge bg-success fs-13">PEGAWAI TEKNISI</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $check = '<i class="fe fe-check-circle text-success fs-18"></i>';
                            $cross = '<i class="fe fe-x-circle text-danger fs-18"></i>';
                            $partial = '<i class="fe fe-eye text-warning fs-18"></i>';
                            $features = [
                                ['Dashboard', true, true, true],
                                ['Dashboard - Cek Resi', true, true, true],
                                ['Master Barang - Jenis Barang', true, true, false],
                                ['Master Barang - Merk Barang', true, true, false],
                                ['Master Barang - Data Barang', true, true, false],
                                ['Transaksi - Barang Masuk', true, true, 'view'],
                                ['Transaksi - Barang Keluar', true, true, 'view+form'],
                                ['Transaksi - Barang Tracking', true, true, false],
                                ['Laporan Barang Masuk', true, true, false],
                                ['Laporan Barang Keluar', true, true, false],
                                ['User Management', true, false, false],
                                ['User Management - Daftar Teknisi (CRUD)', true, false, false],
                                ['User Management - Admin Gudang (Edit)', true, false, false],
                                ['User Management - Audit Trail', true, false, false],
                                ['Konfirmasi Pengembalian Barang', true, true, false],
                                ['Logout', true, true, true],
                            ];
                            @endphp
                            @foreach($features as $f)
                            <tr>
                                <td class="text-start">{{ $f[0] }}</td>
                                <td>{!! $f[1] === true ? $check : ($f[1] === 'view' ? $partial : $cross) !!}</td>
                                <td>{!! $f[2] === true ? $check : ($f[2] === 'view' ? $partial : $cross) !!}</td>
                                <td>
                                    @if($f[3] === true)
                                        {!! $check !!}
                                    @elseif($f[3] === 'view')
                                        <i class="fe fe-eye text-warning fs-18"></i>
                                        <small class="d-block text-muted" style="font-size:10px">View Only</small>
                                    @elseif($f[3] === 'view+form')
                                        <i class="fe fe-edit-2 text-info fs-18"></i>
                                        <small class="d-block text-muted" style="font-size:10px">View + Ajukan Peminjaman</small>
                                    @else
                                        {!! $cross !!}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KARTU DETAIL ROLE -->
<div class="row row-sm">
    <!-- OWNER -->
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fe fe-crown me-1"></i> OWNER</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Akses penuh ke seluruh fitur</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Satu-satunya yang bisa buka User Management</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> CRUD akun Pegawai Teknisi</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Edit akun Admin Gudang (tidak bisa hapus/tambah)</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Lihat Audit Trail</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ADMIN GUDANG -->
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fe fe-shield me-1"></i> ADMIN GUDANG</h5>
                <small>Hanya 1 akun — tidak bisa ditambah/dihapus</small>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Akses hampir semua fitur</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Master Barang</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Kelola Transaksi</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Lihat & cetak Laporan</li>
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Konfirmasi pengembalian barang</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa akses User Management</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- PEGAWAI TEKNISI -->
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fe fe-user me-1"></i> PEGAWAI TEKNISI</h5>
                <small>Bisa banyak akun — CRUD oleh Owner</small>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fe fe-check text-success me-2"></i> Dashboard (tampilan terbatas)</li>
                    <li class="mb-2"><i class="fe fe-eye text-warning me-2"></i> Barang Masuk (view only)</li>
                    <li class="mb-2"><i class="fe fe-edit-2 text-info me-2"></i> Barang Keluar (view + ajukan peminjaman)</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa akses Master Barang</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa akses Laporan</li>
                    <li class="mb-2"><i class="fe fe-x text-danger me-2"></i> Tidak bisa konfirmasi pengembalian</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
