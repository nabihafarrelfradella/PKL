<!doctype html>
<html lang="en" dir="ltr">

<?php
use App\Models\Admin\AppreanceModel;
use Illuminate\Support\Facades\Session;
$appreance = AppreanceModel::where('user_id', '=', Session::get('user')->user_id)->first();
?>

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Manajemen Alfatindo">
    <meta name="author" content="Manajemen Alfatindo">
    <meta name="keywords" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('/assets/default/web/default.png') }}" />

    <!-- TITLE -->
    <title>{{ $title }} | Manajemen Alfatindo</title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ url('/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="{{ url('/assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ url('/assets/css/dark-style.css') }}" rel="stylesheet" />
    <link href="{{ url('/assets/css/transparent-style.css') }}" rel="stylesheet">
    <link href="{{ url('/assets/css/skin-modes.css') }}" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="{{ url('/assets/css/icons.css') }}" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all"
        href="{{ url('/assets/colors/color1.css') }}" />
    <style>
        modal.fade {
            z-index: 1050 !important;
        }

        .datepicker {
            z-index: 20000000 !important
        }

        button.cancel {
            background-color: gray !important;
        }

        ::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            background-color: #F5F5F5;
            border-radius: 10px;
        }

        ::-webkit-scrollbar {
            width: 6px;
            background-color: #F5F5F5;
        }

        .dataTables_scrollBody::-webkit-scrollbar {
            width: 6px;
            background-color: #F5F5F5;
            height: 10px !important;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #777;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #777;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #777 !important;
        }

        /* Logo collapsed */
        .sidebar-mini.sidenav-toggled .header-brand-img.logo-collapsed {
            display: flex !important;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 6px 0 2px !important;
            transform: translateY(-4px);
        }

        .sidebar-mini.sidenav-toggled:hover .header-brand-img.logo-collapsed {
            transform: translateY(-8px);
        }

        .sidebar-mini.sidenav-toggled .header-brand-img.toggle-logo,
        .sidebar-mini.sidenav-toggled .header-brand-img.desktop-logo,
        .sidebar-mini.sidenav-toggled .header-brand-img.light-logo,
        .sidebar-mini.sidenav-toggled .header-brand-img.light-logo1 {
            display: none !important;
        }

        .sidebar-mini:not(.sidenav-toggled) .header-brand-img.logo-collapsed {
            display: none !important;
        }

        /* ═══════════════════════════════════════════
           RESPONSIVE FIXES — Mobile & Tablet
           ═══════════════════════════════════════════ */
           
        /* ── Safe Area (iOS Notch/Dynamic Island) ── */
        .page {
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }
        .app-header {
            padding-top: env(safe-area-inset-top);
        }

        /* ── Page Header & Navbar ── */
        @media (max-width: 767.98px) {
            .app-header .logo-horizontal {
                position: absolute;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                z-index: 10; /* Ensure it stays above other header elements */
            }
            .app-header .logo-horizontal img.desktop-logo,
            .app-header .logo-horizontal img.light-logo1 {
                height: 120px !important; /* Force a much larger height */
                max-height: none !important; /* Override bootstrap constraints */
                max-width: none !important;
                object-fit: contain;
                transform: scale(2.2); /* Enlarge visually */
                transform-origin: center center;
            }
            .full-screen-link {
                display: none !important;
            }
            .main-container {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .page-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px;
                margin-bottom: 20px !important;
                margin-top: 10px !important;
            }
            .page-header .ms-sm-auto,
            .page-header .pageheader-btn,
            .page-header .d-grid {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .page-header .btn {
                width: 100%;
                justify-content: center;
                padding: 10px 16px;
                font-size: 14px;
            }
            .page-header h1.page-title {
                font-size: 1.25rem;
            }
            .breadcrumb {
                font-size: 12px;
            }
        }

        /* ── Card Headers ── */
        @media (max-width: 767.98px) {
            .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px;
            }
            .card-header .btn,
            .card-header a.btn {
                width: 100%;
                text-align: center;
                font-size: 13px;
            }
        }

        /* ── Stat Cards (Dashboard) ── */
        @media (max-width: 575.98px) {
            .stat-card {
                min-height: 85px;
                padding: 14px 16px;
                border-radius: 10px;
            }
            .stat-card .stat-info h3 {
                font-size: 1.3rem;
            }
            .stat-card .stat-info p {
                font-size: 0.75rem;
            }
            .stat-card .stat-icon {
                font-size: 1.8rem;
            }
        }

        /* ── Tables ── */
        @media (max-width: 767.98px) {
            .table {
                font-size: 12px;
            }
            .table th, .table td {
                padding: 6px 8px !important;
            }
            .table .btn-sm {
                padding: 2px 5px;
                font-size: 11px;
            }
        }

        /* ── Modals ── */
        @media (max-width: 575.98px) {
            .modal-dialog.modal-lg,
            .modal-dialog.modal-xl {
                max-width: calc(100% - 20px) !important;
                margin: 10px auto !important;
            }
            .modal-body {
                padding: 12px !important;
            }
            .modal-body .row > [class*="col-md-"] {
                margin-bottom: 0;
            }
            .modal-footer {
                flex-wrap: wrap;
                gap: 6px;
                padding: 10px 12px !important;
            }
            .modal-footer .btn {
                flex: 1 1 auto;
                font-size: 13px;
            }
        }

        /* ── DataTables Controls ── */
        @media (max-width: 767.98px) {
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left !important;
                margin-bottom: 8px;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                margin-left: 0 !important;
            }
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                text-align: center !important;
                margin-top: 8px;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 4px 8px !important;
                font-size: 12px;
            }
        }

        /* ── Header Profile Text ── */
        @media (max-width: 991.98px) {
            .profile-1 .text-end h5,
            .profile-1 .text-end small {
                display: none;
            }
        }

        /* ── Teknisi Info Bar ── */
        @media (max-width: 575.98px) {
            .teknisi-info-bar {
                flex-wrap: wrap;
                padding: 12px 14px;
                gap: 10px;
            }
            .teknisi-info-bar .ms-auto {
                width: 100%;
                margin-left: 0 !important;
            }
            .teknisi-info-bar .ms-auto .btn {
                width: 100%;
                text-align: center;
            }
        }

        /* ── Form Groups inside modals ── */
        @media (max-width: 575.98px) {
            .form-group label {
                font-size: 12px;
            }
            .form-control {
                font-size: 13px;
            }
            .input-group .btn {
                padding: 6px 10px;
            }
        }

        /* ── Batch Items Table ── */
        @media (max-width: 575.98px) {
            #batchItemsTable {
                font-size: 11px;
            }
            #batchItemsTable th,
            #batchItemsTable td {
                padding: 5px 6px !important;
            }
        }
    </style>
</head>

@if ($appreance != '')

    <body
        class="app ltr {{ $appreance->appreance_layout }} {{ $appreance->appreance_theme }} {{ $appreance->appreance_menu }} {{ $appreance->appreance_header }} {{ $appreance->appreance_sidestyle }}">
    @else

        <body class="app sidebar-mini ltr light-mode">
@endif

<!-- GLOBAL-LOADER -->
@if ($appreance != '')
    <div id="global-loader" class="{{ $appreance->appreance_theme == 'dark-mode' ? 'bg-dark' : '' }}" style="display: none !important;">
@else
    <div id="global-loader" style="display: none !important;">
@endif
<!-- <img src="{{ url('/assets/images/loader.svg') }}" class="loader-img" alt="Loader"> -->
</div>
<!-- /GLOBAL-LOADER -->

<!-- PAGE -->
<div class="page">
    <div class="page-main">

        <!-- APP HEADER -->
        @include('Master.Layouts.header')
        <!-- END APP HEADER -->

        <!-- SIDEBAR -->
        @include('Master.Layouts.sidebar-left')
        <!-- END SIDEBAR -->

        <!--app-content open-->
        <div class="main-content app-content mt-0">
            <div class="side-app">

                <!-- CONTAINER -->
                <div class="main-container container-fluid">
                    @yield('content')
                </div>
                <!-- CONTAINER END -->
            </div>
        </div>
        <!--app-content close-->

    </div>

    <!-- SIDEBAR RIGHT -->
    <!-- (-) -->
    <!-- END SIDEBAR RIGHT -->

    <!-- FOOTER -->
    @include('Master.Layouts.footer')
    <!-- FOOTER END -->

</div>

<!-- MODAL LOGOUT -->
<div class="modal fade" data-bs-backdrop="static" id="modalLogout">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <form method="GET" action="{{ url('admin/logout') }}" name="myFormH" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-center p-4 pb-5">
                    <button type="reset" aria-label="Close" class="btn-close position-absolute"
                        data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
                    <br>
                    <i class="icon icon-exclamation fs-70 text-warning lh-1 my-5 d-inline-block"></i>
                    <h3 class="mb-5">Yakin logout ?</h3>
                    <button type="submit" class="btn btn-danger-light pd-x-25">Iya</button>
                    <button type="reset" data-bs-dismiss="modal" class="btn btn-default pd-x-25">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

<!-- JQUERY JS -->
<script src="{{ url('/assets/js/jquery.min.js') }}"></script>

<!-- BOOTSTRAP JS -->
<script src="{{ url('/assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ url('/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- Sticky js -->
<script src="{{ url('/assets/js/sticky.js') }}"></script>

<!-- INPUT MASK JS-->
<script src="{{ url('/assets/plugins/input-mask/jquery.mask.min.js') }}"></script>

<!-- SIDE-MENU JS-->
<script src="{{ url('/assets/plugins/sidemenu/sidemenu.js') }}"></script>

<!-- SIDEBAR JS -->
<script src="{{ url('/assets/plugins/sidebar/sidebar.js') }}"></script>

<!-- Perfect SCROLLBAR JS-->
<script src="{{ url('/assets/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ url('/assets/plugins/p-scroll/pscroll.js') }}"></script>
<script src="{{ url('/assets/plugins/p-scroll/pscroll-1.js') }}"></script>

<!-- FILE UPLOADES JS -->
<script src="{{ url('/assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{ url('/assets/plugins/fileuploads/js/file-upload.js') }}"></script>

<!-- INTERNAL Bootstrap-Datepicker js-->
<script src="{{ url('/assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!-- SELECT2 JS -->
<script src="{{ url('/assets/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ url('/assets/js/select2.js') }}"></script>

<!-- BOOTSTRAP-DATERANGEPICKER JS -->
<script src="{{ url('/assets/plugins/bootstrap-daterangepicker/moment.min.js') }}"></script>
<script src="{{ url('/assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!-- INTERNAL Bootstrap-Datepicker js-->
<script src="{{ url('/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<!-- INTERNAL Sumoselect js-->
<script src="{{ url('/assets/plugins/sumoselect/jquery.sumoselect.js') }}"></script>

<!-- TIMEPICKER JS -->
<script src="{{ url('/assets/plugins/time-picker/jquery.timepicker.js') }}"></script>
<script src="{{ url('/assets/plugins/time-picker/toggles.min.js') }}"></script>

<!-- INTERNAL intlTelInput js-->
<script src="{{ url('/assets/plugins/intl-tel-input-master/intlTelInput.js') }}"></script>
<script src="{{ url('/assets/plugins/intl-tel-input-master/country-select.js') }}"></script>
<script src="{{ url('/assets/plugins/intl-tel-input-master/utils.js') }}"></script>

<!-- INTERNAL jquery transfer js-->
<script src="{{ url('/assets/plugins/jQuerytransfer/jquery.transfer.js') }}"></script>

<!-- INTERNAL multi js-->
<script src="{{ url('/assets/plugins/multi/multi.min.js') }}"></script>

<!-- DATEPICKER JS -->
<script src="{{ url('/assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ url('/assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ url('/assets/plugins/input-mask/jquery.maskedinput.js') }}"></script>

<!-- COLOR PICKER JS -->
<script src="{{ url('/assets/plugins/pickr-master/pickr.es5.min.js') }}"></script>
<script src="{{ url('/assets/js/picker.js') }}"></script>

<!-- MULTI SELECT JS-->
<script src="{{ url('/assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ url('/assets/plugins/multipleselect/multi-select.js') }}"></script>

<!-- SWEET-ALERT JS -->
<script src="{{ url('/assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ url('/assets/js/sweet-alert.js') }}"></script>

<!-- INTERNAL CHARTJS CHART JS-->
<script src="{{ url('/assets/plugins/chart/Chart.bundle.js') }}"></script>
<script src="{{ url('/assets/plugins/chart/rounded-barchart.js') }}"></script>
<script src="{{ url('/assets/plugins/chart/utils.js') }}"></script>

<!-- DATA TABLE JS-->
<script src="{{ url('/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ url('/assets/js/table-data.js') }}"></script>


<!-- INTERNAL INDEX JS -->
<script src="{{ url('/assets/js/index1.js') }}"></script>

<!-- Color Theme js -->
<script src="{{ url('/assets/js/themeColors.js') }}"></script>

<!-- CUSTOM JS -->
<script src="{{ url('/assets/js/custom.js') }}"></script>

<script>
    $(document).ready(function() {
        // BOOTSTRAP DATEPICKER
        $('.datepicker-date').bootstrapdatepicker({
            format: "yyyy-mm-dd",
            viewMode: "date",
            autoclose: true,
        })
    })
</script>

@if (Session::get('status') == 'success')
    <script>
        $(document).ready(function() {
            swal({
                title: "{{ Session::get('msg') }}",
                type: "success"
            });
        });
    </script>
@elseif(Session::get('status') == 'error')
    <script>
        $(document).ready(function() {
            swal({
                title: "{{ Session::get('msg') }}",
                type: "error"
            });
        });
    </script>
@endif

{{-- ── GLOBAL FIX: Select2 + Bootstrap Modal scroll freeze ──────────────── --}}
<style>
    /* Fix Select2 dropdown z-index inside Bootstrap modals */
    .select2-container--open .select2-dropdown { z-index: 9999 !important; }
    /* Hide already-selected options in multi-select dropdowns */
    .select2-results__option[aria-selected="true"] { display: none !important; }
</style>
<script>
    // Auto-focus Select2 search field when dropdown opens (prevents focus trap)
    $(document).on('select2:open', function () {
        setTimeout(function () {
            var field = document.querySelector('.select2-container--open .select2-search__field');
            if (field) field.focus();
        }, 0);
    });

    // Restore modal scroll after Select2 closes (main freeze fix)
    $(document).on('select2:close', function () {
        setTimeout(function () {
            var modal = document.querySelector('.modal.show');
            if (modal) {
                modal.style.overflow  = '';
                modal.style.overflowY = 'auto';
            }
        }, 50);
    });

    // Restore modal scroll after any native select change (secondary freeze fix)
    $(document).on('change', 'select:not(.select2-hidden-accessible)', function () {
        setTimeout(function () {
            var modal = document.querySelector('.modal.show');
            if (modal) modal.style.overflowY = 'auto';
        }, 50);
    });
</script>

@yield('scripts')
@yield('formTambahJS')
@yield('formEditJS')
@yield('formHapusJS')
@yield('formKembaliJS')
@yield('formOtherJS')

@php $roleId = Session::get('user')->role_id ?? 0; @endphp
@if(in_array($roleId, [1, 2]))
<script>
    // ── Notifikasi Bell Polling ──────────────────────────────────────────────
    const NOTIF_URL       = "{{ route('notifikasi.get') }}";
    const NOTIF_READ_ALL  = "{{ route('notifikasi.read-all') }}";
    const CSRF_TOKEN      = "{{ csrf_token() }}";

    function fetchNotifikasi() {
        $.ajax({
            type: 'GET',
            url: NOTIF_URL,
            success: function(res) {
                const total = res.total || 0;
                const items = res.items || [];

                // Badge
                const badge = $('#notifBadge');
                const count = $('#notifCount');
                if (total > 0) {
                    badge.text(total > 99 ? '99+' : total).removeClass('d-none');
                    count.text(total).removeClass('d-none');
                    // Animasi bergetar
                    $('#notifBell i').addClass('text-warning');
                } else {
                    badge.addClass('d-none');
                    count.addClass('d-none');
                    $('#notifBell i').removeClass('text-warning');
                }

                // Waktu update
                const now = new Date();
                $('#notifTime').text(now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0'));

                // List
                const list = $('#notifList');
                if (items.length === 0) {
                    list.html('<div class="text-center text-muted py-4"><i class="fe fe-check-circle fs-24 d-block mb-1"></i><small>Tidak ada notifikasi baru</small></div>');
                    return;
                }

                let html = '';
                items.forEach(function(n) {
                    const typeLabel = n.notif_type === 'peminjaman' ? 'Peminjaman' : (n.notif_type === 'pengembalian' ? 'Dikembalikan' : 'Habis Pakai');
                    const bgColor  = n.notif_type === 'peminjaman' ? '#fff3cd' : (n.notif_type === 'pengembalian' ? '#d1fae5' : '#e0e7ff');
                    const iconColor= n.notif_type === 'peminjaman' ? '#f0a500' : (n.notif_type === 'pengembalian' ? '#10b981' : '#6366f1');
                    const feIcon   = n.notif_type === 'peminjaman' ? 'fe-arrow-up-circle' : (n.notif_type === 'pengembalian' ? 'fe-corner-up-left' : 'fe-package');
                    html += `
                    <div class="dropdown-item d-flex align-items-start py-2 px-3 border-bottom" style="white-space:normal;cursor:default;">
                        <div class="me-2 flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:${bgColor};">
                            <i class="fe ${feIcon}" style="color:${iconColor};font-size:16px;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="fw-semibold text-dark" style="font-size:12px;line-height:1.3;">${n.notif_pesan}</div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="badge" style="background:${bgColor};color:${iconColor};font-size:10px;">${typeLabel}</span>
                                <small class="text-muted">${n.waktu}</small>
                            </div>
                        </div>
                    </div>`;
                });
                list.html(html);
            }
        });
    }

    function markAllRead() {
        $.ajax({
            type: 'POST',
            url: NOTIF_READ_ALL,
            data: { _token: CSRF_TOKEN },
            success: function() {
                setTimeout(fetchNotifikasi, 400);
            }
        });
    }

    // Pertama kali load + polling setiap 30 detik
    $(document).ready(function() {
        fetchNotifikasi();
        setInterval(fetchNotifikasi, 30000);
    });
</script>
@endif

</body>

</html>
