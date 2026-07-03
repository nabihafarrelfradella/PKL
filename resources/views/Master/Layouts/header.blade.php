<!-- app-Header -->
<div class="app-header header sticky">
    <div class="container-fluid main-container">
        <div class="d-flex align-items-center" style="height: 100%;">
            <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)" style="position: relative; z-index: 99;"></a>
            <!-- sidebar-toggle-->
            <!-- <a class="logo-horizontal d-flex justify-center" href="index.html">
                <img src="../assets/images/brand/logo.png" class="header-brand-img desktop-logo" alt="logo">
                <img src="../assets/images/brand/logo-3.png" class="header-brand-img light-logo1" alt="logo">
            </a> -->
            <a class="logo-horizontal" href="{{url('/')}}" style="pointer-events: none;">
                <img src="{{url('/assets/default/web/default.png')}}" class="header-brand-img desktop-logo" style="height: 40px !important; width: auto !important; max-width: 180px; object-fit: contain; pointer-events: auto;" alt="logo">
                <img src="{{url('/assets/default/web/default.png')}}" class="header-brand-img light-logo1" style="height: 40px !important; width: auto !important; max-width: 180px; object-fit: contain; pointer-events: auto;" alt="logo">
            </a>

            <!-- LOGO -->
            <div class="d-flex order-lg-2 ms-auto header-right-icons">
                <!-- SEARCH -->
                <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                </button>
                <div class="navbar navbar-collapse responsive-navbar p-0">
                    <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                        <div class="d-flex justify-content-between order-lg-2">
                            {{-- NOTIFIKASI BELL â€” hanya untuk Owner & Admin Gudang --}}
                            @if(in_array(Session::get('user')->role_id, [1, 2]))
                            <div class="dropdown d-flex notifications" id="notifDropdown">
                                <a class="nav-link icon position-relative" data-bs-toggle="dropdown" href="javascript:void(0)" id="notifBell" onclick="markAllRead()">
                                    <i class="fe fe-bell fs-18"></i>
                                    <span id="notifBadge" class="d-none" style="position:absolute;top:6px;right:6px;background:#e84c4c;color:#fff;border-radius:50%;font-size:10px;font-weight:700;min-width:17px;height:17px;display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid #fff;"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width:340px;max-height:480px;overflow-y:auto;">
                                    <div class="drop-heading border-bottom px-3 py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold fs-14">
                                            <i class="fe fe-bell me-1 text-primary"></i>Notifikasi
                                            <span id="notifCount" class="badge bg-danger ms-1 d-none">0</span>
                                        </h6>
                                        <small class="text-muted" id="notifTime">-</small>
                                    </div>
                                    <div class="notifications-menu" id="notifList">
                                        <div class="text-center text-muted py-4">
                                            <i class="fe fe-check-circle fs-24 d-block mb-1"></i>
                                            <small>Tidak ada notifikasi baru</small>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider m-0"></div>
                                    <a href="{{ url('admin/barang-keluar') }}" class="dropdown-item text-center p-2 text-primary fw-semibold">
                                        <i class="fe fe-eye me-1"></i>Lihat Semua Barang Keluar
                                    </a>
                                </div>
                            </div>
                            @endif


                            <!-- SIDE-MENU -->
                            <div class="dropdown d-flex profile-1">
                                <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex">
                                    <div class="text-end">
                                        <h5 class="text-dark mb-0 me-4 fs-14 fw-semibold">{{Session::get('user')->user_nmlengkap}}</h5>
                                        <small class="text-muted me-4">{{Session::get('user')->role_title}}</small>
                                    </div>
                                    @if(Session::get('user')->user_foto == 'undraw_profile.svg')
                                    <img src="{{url('/assets/default/users/undraw_profile.svg')}}" alt="profile-user" class="avatar profile-user brround cover-image" style="object-fit: cover;">
                                    @else
                                    <img class="avatar profile-user brround cover-image" src="{{asset('storage/users/'.Session::get('user')->user_foto)}}" alt="avatar" style="object-fit: cover;">
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <!-- <div class="drop-heading">
                                        <div class="text-center">
                                            <h5 class="text-dark mb-0 fs-14 fw-semibold">{{Session::get('user')->user_nmlengkap}}</h5>
                                            <small class="text-muted">{{Session::get('user')->role_title}}</small>
                                        </div>
                                    </div> -->
                                    <!-- <div class="dropdown-divider m-0"></div> -->
                                    <a class="dropdown-item" href="{{url('/admin/profile')}}/{{Session::get('user')->user_id}}">
                                        <i class="dropdown-icon fe fe-user"></i> Profile
                                    </a>
                                    <a class="dropdown-item" data-bs-effect="effect-super-scaled" data-bs-toggle="modal" href="#modalLogout">
                                        <i class="dropdown-icon fe fe-log-out"></i> Log out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /app-Header -->
