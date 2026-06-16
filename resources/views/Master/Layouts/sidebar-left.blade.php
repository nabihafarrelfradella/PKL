<?php
    use Illuminate\Support\Facades\Session;
    use App\Models\Admin\AksesModel;
    $user   = Session::get('user');
    $roleId = $user ? $user->role_id : 0;
    // role_id=1 → Owner, role_id=2 → Admin Gudang, role_id=3 → Pegawai Teknisi
    
    // Ambil daftar akses 'view' untuk role ini (selain Owner)
    $aksesMenus = [];
    $aksesMenusJudul = [];
    $aksesSubmenus = [];
    if ($roleId != 1) {
        $menus = AksesModel::leftJoin('tbl_menu', 'tbl_menu.menu_id', '=', 'tbl_akses.menu_id')
            ->where('tbl_akses.role_id', $roleId)
            ->where('tbl_akses.akses_type', 'view')
            ->get(['tbl_menu.menu_redirect', 'tbl_menu.menu_judul']);
            
        $aksesMenus = $menus->pluck('menu_redirect')->toArray();
        $aksesMenusJudul = $menus->pluck('menu_judul')->toArray();
            
        $aksesSubmenus = AksesModel::leftJoin('tbl_submenu', 'tbl_submenu.submenu_id', '=', 'tbl_akses.submenu_id')
            ->where('tbl_akses.role_id', $roleId)
            ->where('tbl_akses.akses_type', 'view')
            ->pluck('tbl_submenu.submenu_redirect')->toArray();
    }
    
    if (!function_exists('hasViewAccess')) {
        function hasViewAccess($roleId, $redirect, $type, $menus, $submenus) {
            if ($roleId == 1) return true; // Owner has all access
            if ($type == 'menu') {
                return in_array($redirect, $menus);
            } elseif ($type == 'judul') {
                return in_array($redirect, $menus);
            } else {
                return in_array($redirect, $submenus);
            }
        }
    }
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
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg></div>
            <ul class="side-menu">

                <li class="sub-category">
                    <h3>Menu</h3>
                </li>

                <!-- Dashboard (semua role, diasumsikan selalu ada) -->
                @if(hasViewAccess($roleId, '/dashboard', 'menu', $aksesMenus, $aksesSubmenus))
                <li class="slide">
                    <a class="side-menu__item {{$title == 'Dashboard' ? 'active' : ''}}" data-bs-toggle="slide" href="{{url('/admin/dashboard')}}">
                        <i class="side-menu__icon fe fe-home"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                @endif

                {{-- ============================================
                     DYNAMIC MENU (Based on RBAC)
                     ============================================ --}}

                @php
                    $isMasterBarangMenuAllowed = hasViewAccess($roleId, 'Master Barang', 'judul', $aksesMenusJudul, []);
                    $showMasterBarang = $isMasterBarangMenuAllowed && (hasViewAccess($roleId, '/merk', 'submenu', $aksesMenus, $aksesSubmenus) || hasViewAccess($roleId, '/barang', 'submenu', $aksesMenus, $aksesSubmenus));
                @endphp
                @if($showMasterBarang)
                <li class="sub-category">
                    <h3>Master Data</h3>
                </li>

                <!-- Master Barang -->
                <li class="slide {{in_array($title, ['Jenis', 'Merk', 'Barang']) ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{in_array($title, ['Jenis', 'Merk', 'Barang']) ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-package"></i>
                        <span class="side-menu__label">Master Barang</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        @if(hasViewAccess($roleId, '/merk', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/merk')}}" class="slide-item {{$title == 'Merk' ? 'active' : ''}}">Merk Barang</a></li>
                        @endif
                        @if(hasViewAccess($roleId, '/barang', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/barang')}}" class="slide-item {{$title == 'Barang' ? 'active' : ''}}">Data Barang</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @php
                    $isTransaksiMenuAllowed = hasViewAccess($roleId, 'Transaksi', 'judul', $aksesMenusJudul, []);
                    $showTransaksi = $isTransaksiMenuAllowed && (hasViewAccess($roleId, '/barang-masuk', 'submenu', $aksesMenus, $aksesSubmenus) || 
                                     hasViewAccess($roleId, '/barang-keluar', 'submenu', $aksesMenus, $aksesSubmenus) || 
                                     hasViewAccess($roleId, '/barang-tracking', 'submenu', $aksesMenus, $aksesSubmenus));
                @endphp
                @if($showTransaksi)
                <li class="sub-category">
                    <h3>Transaksi</h3>
                </li>

                <!-- Transaksi -->
                <li class="slide {{in_array($title, ['Barang Masuk', 'Barang Keluar', 'Barang Tracking']) ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{in_array($title, ['Barang Masuk', 'Barang Keluar', 'Barang Tracking']) ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-repeat"></i>
                        <span class="side-menu__label">Transaksi</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        @if(hasViewAccess($roleId, '/barang-masuk', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/barang-masuk')}}" class="slide-item {{$title == 'Barang Masuk' ? 'active' : ''}}">Barang Masuk</a></li>
                        @endif
                        @if(hasViewAccess($roleId, '/barang-keluar', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/barang-keluar')}}" class="slide-item {{$title == 'Barang Keluar' ? 'active' : ''}}">Barang Keluar</a></li>
                        @endif
                        @if(hasViewAccess($roleId, '/barang-tracking', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/barang-tracking')}}" class="slide-item {{$title == 'Barang Tracking' ? 'active' : ''}}">Barang Tracking</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @php
                    $isLaporanMenuAllowed = hasViewAccess($roleId, 'Laporan', 'judul', $aksesMenusJudul, []);
                    $showLaporan = $isLaporanMenuAllowed && (hasViewAccess($roleId, '/lap-barang-masuk', 'submenu', $aksesMenus, $aksesSubmenus) || 
                                   hasViewAccess($roleId, '/lap-barang-keluar', 'submenu', $aksesMenus, $aksesSubmenus) || 
                                   hasViewAccess($roleId, '/lap-stok-barang', 'submenu', $aksesMenus, $aksesSubmenus));
                @endphp
                @if($showLaporan)
                <li class="sub-category">
                    <h3>Laporan</h3>
                </li>

                <!-- Laporan -->
                <li class="slide {{in_array($title, ['Lap Barang Masuk', 'Lap Barang Keluar', 'Lap Stok Barang', 'Laporan Barang Masuk', 'Laporan Barang Keluar', 'Laporan Stok Barang']) ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{in_array($title, ['Lap Barang Masuk', 'Lap Barang Keluar', 'Lap Stok Barang', 'Laporan Barang Masuk', 'Laporan Barang Keluar', 'Laporan Stok Barang']) ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-printer"></i>
                        <span class="side-menu__label">Laporan</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        @if(hasViewAccess($roleId, '/lap-barang-masuk', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/lap-barang-masuk')}}" class="slide-item {{in_array($title, ['Lap Barang Masuk', 'Laporan Barang Masuk']) ? 'active' : ''}}">Lap. Barang Masuk</a></li>
                        @endif
                        @if(hasViewAccess($roleId, '/lap-barang-keluar', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/lap-barang-keluar')}}" class="slide-item {{in_array($title, ['Lap Barang Keluar', 'Laporan Barang Keluar']) ? 'active' : ''}}">Lap. Barang Keluar</a></li>
                        @endif
                        @if(hasViewAccess($roleId, '/lap-stok-barang', 'submenu', $aksesMenus, $aksesSubmenus))
                        <li><a href="{{url('/admin/lap-stok-barang')}}" class="slide-item {{in_array($title, ['Lap Stok Barang', 'Laporan Stok Barang']) ? 'active' : ''}}">Lap. Stok Barang</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($roleId == 1)
                {{-- ============================================
                     OWNER ONLY — User Management
                     ============================================ --}}
                <li class="sub-category">
                    <h3>Manajemen Pengguna</h3>
                </li>

                <li class="slide {{in_array($title, ['Daftar Teknisi', 'Admin Gudang', 'Access Control', 'Audit Trail', 'Role', 'User']) ? 'is-expanded' : ''}}">
                    <a class="side-menu__item {{in_array($title, ['Daftar Teknisi', 'Admin Gudang', 'Access Control', 'Audit Trail', 'Role', 'User']) ? 'active' : ''}}" data-bs-toggle="slide" href="javascript:void(0)">
                        <i class="side-menu__icon fe fe-users"></i>
                        <span class="side-menu__label">User Management</span><i class="angle fe fe-chevron-right"></i>
                    </a>
                    <ul class="slide-menu">
                        <li><a href="{{route('user-mgmt.teknisi')}}" class="slide-item {{$title == 'Daftar Teknisi' ? 'active' : ''}}">Daftar Teknisi</a></li>
                        <li><a href="{{route('user-mgmt.admin-gudang')}}" class="slide-item {{$title == 'Admin Gudang' ? 'active' : ''}}">Staff Gudang</a></li>
                        <li><a href="{{route('user-mgmt.access-control')}}" class="slide-item {{$title == 'Access Control' ? 'active' : ''}}">Access Control</a></li>
                    </ul>
                </li>
                @endif



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
