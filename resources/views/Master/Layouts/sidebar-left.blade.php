<?php
    use Illuminate\Support\Facades\Session;

    $roleId = Session::get('user')->role_id;
?>
<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
    <div class="app-sidebar">
        <div class="side-header">
            <a class="header-brand1" href="{{url('/admin')}}">
                <img src="{{url('/assets/default/web/default.png')}}" height="120px" class="header-brand-img toggle-logo" alt="logo">
                <div class="header-brand-img desktop-logo">
                    <div class="d-flex align-items-center">
                        <img src="{{url('/assets/default/web/default.png')}}" height="180px" class="me-1" alt="logo">
                        <h4 class="fw-bold mt-4 text-white text-uppercase text-truncate">Manajemen Alfatindo</h4>
                    </div>
                </div>
                <img src="{{url('/assets/default/web/default.png')}}" height="120px" class="header-brand-img light-logo" alt="logo">
                <div class="header-brand-img light-logo1">
                    <div class="d-flex align-items-center">
                        <img src="{{url('/assets/default/web/default.png')}}" height="120px" class="me-1" alt="logo">
                    </div>
                </div>
            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">
                <li class="sub-category">
                    <h3>Menu</h3>
                </li>

                <!-- Dashboard -->
                <li class="slide">
                    <a class="side-menu__item {{$title == 'Dashboard' ? 'active' : ''}}" data-bs-toggle="slide" href="{{url('/admin/dashboard')}}">
                        <i class="side-menu__icon fe fe-home"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <li class="sub-category">
                    <h3>Master Data</h3>
                </li>

                <!-- Master Barang -->
                <li class="slide {{$title == 'Jenis Barang' || $title == 'Merk' || $title == 'Barang' ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{$title == 'Jenis Barang' || $title == 'Merk' || $title == 'Barang' ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-package"></i>
                        <span class="side-menu__label">Master Barang</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{url('/admin/jenisbarang')}}" class="slide-item {{$title == 'Jenis Barang' ? 'active' : ''}}">Jenis</a></li>
                        <li><a href="{{url('/admin/merk')}}" class="slide-item {{$title == 'Merk' ? 'active' : ''}}">Merk</a></li>
                        <li><a href="{{url('/admin/barang')}}" class="slide-item {{$title == 'Barang' ? 'active' : ''}}">Barang</a></li>
                    </ul>
                </li>

                <!-- User Management (Hanya muncul untuk Admin) -->
                @if($roleId == 1)
                <li class="slide {{$title == 'Role' || $title == 'User' || $title == 'Akses' ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{$title == 'Role' || $title == 'User' || $title == 'Akses' ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">User Management</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{url('/admin/role')}}" class="slide-item {{$title == 'Role' ? 'active' : ''}}">Role Management</a></li>
                        <li><a href="{{url('/admin/user')}}" class="slide-item {{$title == 'User' ? 'active' : ''}}">User List</a></li>
                        <li><a href="{{url('/admin/akses/role')}}" class="slide-item {{$title == 'Akses' ? 'active' : ''}}">Access Control</a></li>
                        <li><a href="{{url('/admin/audit')}}" class="slide-item {{$title == 'Audit Trail' ? 'active' : ''}}">Audit Trail</a></li>
                    </ul>
                </li>
                @endif

                <li class="sub-category">
                    <h3>Reports & Transactions</h3>
                </li>

                <!-- Laporan -->
                <li class="slide {{$title == 'Laporan Barang Masuk' || $title == 'Laporan Barang Keluar' || $title == 'Laporan Stok Barang' ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{$title == 'Laporan Barang Masuk' || $title == 'Laporan Barang Keluar' || $title == 'Laporan Stok Barang' ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-printer"></i>
                        <span class="side-menu__label">Laporan</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{url('/admin/lap-barang-masuk')}}" class="slide-item {{$title == 'Laporan Barang Masuk' ? 'active' : ''}}">Lap Barang Masuk</a></li>
                        <li><a href="{{url('/admin/lap-barang-keluar')}}" class="slide-item {{$title == 'Laporan Barang Keluar' ? 'active' : ''}}">Lap Barang Keluar</a></li>
                        <li><a href="{{url('/admin/lap-stok-barang')}}" class="slide-item {{$title == 'Laporan Stok Barang' ? 'active' : ''}}">Lap Stok Barang</a></li>
                    </ul>
                </li>

                <!-- Transaksi -->
                <li class="slide {{$title == 'Barang Masuk' || $title == 'Barang Keluar' ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{$title == 'Barang Masuk' || $title == 'Barang Keluar' ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-repeat"></i>
                        <span class="side-menu__label">Transaksi</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{url('/admin/barang-masuk')}}" class="slide-item {{$title == 'Barang Masuk' ? 'active' : ''}}">Barang Masuk</a></li>
                        <li><a href="{{url('/admin/barang-keluar')}}" class="slide-item {{$title == 'Barang Keluar' ? 'active' : ''}}">Barang Keluar</a></li>
                    </ul>
                </li>

                <li class="sub-category">
                    <h3>Other</h3>
                </li>

                <!-- Log Out -->
                <li class="slide">
                    <a class="side-menu__item" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modalLogout">
                        <i class="side-menu__icon fe fe-log-out"></i>
                        <span class="side-menu__label">Log Out</span>
                    </a>
                </li>
            </ul>
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg></div>
        </div>
    </div>
    <!--/APP-SIDEBAR-->
</div>
