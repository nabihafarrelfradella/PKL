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
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/default/web/default.png') }}" />

    <!-- TITLE -->
    <title>{{ $title }} | Manajemen Alfatindo</title>

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dark-style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/transparent-style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/skin-modes.css') }}" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all"
        href="{{ asset('assets/colors/color1.css') }}" />
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

        /* ── Logo Hide/Show Logic when Toggled ── */
        .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .header-brand-img.desktop-logo,
        .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .header-brand-img.light-logo,
        .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .header-brand-img.light-logo1 {
            display: none !important;
        }
        
        /* Show toggle-logo and style it when sidebar is collapsed (not hovered) */
        .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .header-brand-img.toggle-logo {
            display: block !important;
            margin: 5px auto 0 auto; /* Tengah, jangan terlalu bawah */
            height: 40px !important; /* Shrink logo size */
        }
        
        /* When toggled but hovered, just hide the toggle logo. Let the template handle which desktop/light logo to show */
        .sidebar-mini.sidenav-toggled .app-sidebar:hover .header-brand-img.toggle-logo {
            display: none !important;
        }

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           RESPONSIVE FIXES â€” Mobile & Tablet
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */
           
        /* â”€â”€ Safe Area (iOS Notch/Dynamic Island) â”€â”€ */
        .page {
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }
        .app-header {
            padding-top: env(safe-area-inset-top);
        }

        /* â”€â”€ Page Header & Navbar â”€â”€ */
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

        /* â”€â”€ Card Headers â”€â”€ */
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

        /* â”€â”€ Stat Cards (Dashboard) â”€â”€ */
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

        /* â”€â”€ Tables â”€â”€ */
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

        /* â”€â”€ Modals â”€â”€ */
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

        /* â”€â”€ DataTables Controls â”€â”€ */
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

        /* â”€â”€ Header Profile Text â”€â”€ */
        @media (max-width: 991.98px) {
            .profile-1 .text-end h5,
            .profile-1 .text-end small {
                display: none;
            }
        }

        /* â”€â”€ Teknisi Info Bar â”€â”€ */
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

        /* â”€â”€ Form Groups inside modals â”€â”€ */
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

        /* â”€â”€ Batch Items Table â”€â”€ */
        @media (max-width: 575.98px) {
            #batchItemsTable {
                font-size: 11px;
            }
            #batchItemsTable th,
            #batchItemsTable td {
                padding: 5px 6px !important;
            }
        }

        /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
           COMPACT LAYOUT â€” Global 90% scale effect
           (reduce padding/whitespace without horizontal scroll)
           â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

        /* Base font scale â€” all rem units shrink proportionally */
        html {
            font-size: 14px; /* default is 16px; 14px â‰ˆ 87.5% */
        }

        /* Reduce main content padding */
        .main-container.container-fluid {
            padding-left: 18px !important;
            padding-right: 18px !important;
        }

        /* Tighter card header & body */
        .card-header {
            padding: 10px 16px !important;
        }
        .card-body {
            padding: 14px 16px !important;
        }
        .card-footer {
            padding: 8px 16px !important;
        }

        /* Tighter page header */
        .page-header {
            margin-bottom: 16px !important;
            margin-top: 16px !important;
        }

        /* Tighter table cells */
        .table th, .table td {
            padding: 6px 10px !important;
            font-size: 0.825rem;
        }

        /* Tighter form controls */
        .form-control:not(.select2-hidden-accessible), .form-select:not(.select2-hidden-accessible) {
            padding: 5px 10px !important;
            font-size: 0.825rem !important;
        }
        .form-label {
            font-size: 0.8rem;
            margin-bottom: 3px;
        }
        .form-group {
            margin-bottom: 10px !important;
        }

        /* Compact row spacing */
        .row.row-sm {
            margin-bottom: 14px !important;
        }

        /* Smaller buttons globally */
        .btn {
            font-size: 0.8rem;
        }

        /* Tighter modals */
        .modal-body {
            padding: 14px 18px !important;
        }
        .modal-header {
            padding: 10px 18px !important;
        }
        .modal-footer {
            padding: 8px 18px !important;
        }

        /* DataTables compact */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.8rem;
        }

        /* Reduce stat card padding on desktop */
        @media (min-width: 768px) {
            .stat-card {
                padding: 14px 18px !important;
                min-height: 80px;
            }
            .stat-card .stat-info h3 {
                font-size: 1.5rem;
            }
        }

        /* Eliminate unnecessary side-app padding top â€” matches 80px header */
        .side-app {
            padding-top: 80px !important;
        }

        /* â”€â”€ Fix DataTables: "Show 10 entries" select spacing â”€â”€ */
        .dataTables_wrapper .dataTables_length select {
            display: inline-block !important;
            width: auto !important;
            padding: 2px 24px 2px 8px !important;
            margin: 0 4px !important;
            font-size: 0.8rem;
            height: auto !important;
        }
        .dataTables_wrapper .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        /* â”€â”€ Force table to take full width and prevent false overflow â”€â”€ */
        table.table, table.dataTable {
            width: 100% !important;
        }

        /* â”€â”€ Table Responsive Scrollbar (only shows when truly needed) â”€â”€ */
        .table-responsive {
            overflow-x: auto !important;
        }
        /* Custom thin scrollbar for webkit */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        .table-responsive {
            scrollbar-width: thin;
            -ms-overflow-style: -ms-autohiding-scrollbar;
        }

        /* ── Main content: push content on sidebar hover ── */
        .main-content.app-content {
            transition: margin-left 0.3s ease !important;
        }

        /* ── Sidebar Width & Layout Adjustments ── */
        @media (min-width: 992px) {
            /* Reduce sidebar width to 240px */
            body:not(.sidenav-toggled) .app-sidebar,
            body:not(.sidenav-toggled) .app-sidebar .side-header {
                width: 240px !important;
            }
            body:not(.sidenav-toggled) .main-content.app-content {
                margin-left: 240px !important;
            }
            body:not(.sidenav-toggled) .app-header {
                padding-left: 255px !important; /* 240px + 15px gap */
            }

            /* Hover expanded state */
            body.sidebar-mini.sidenav-toggled:has(.app-sidebar:hover) .app-sidebar,
            body.sidebar-mini.sidenav-toggled:has(.app-sidebar:hover) .app-sidebar .side-header {
                width: 240px !important;
            }
            body.sidebar-mini.sidenav-toggled:has(.app-sidebar:hover) .main-content.app-content {
                margin-left: 240px !important;
            }
        }

        /* ── Sidebar & Table Font Size Adjustments ── */
        .app-sidebar .side-menu__label,
        .app-sidebar .slide-item {
            font-size: 13.5px !important;
            font-weight: 500 !important;
        }

        /* ── Sidebar logo & Header Alignment ── */
        .app-sidebar .side-header,
        .app-header {
            height: 74px !important;
            min-height: 74px !important;
            max-height: 74px !important;
            display: flex;
            align-items: center;
        }
        .app-sidebar .side-header {
            justify-content: center;
        }
        .app-header .main-container {
            height: 100%;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .app-header .main-container > .d-flex {
            height: 100%;
            width: 100%;
            align-items: center;
        }
        /* Make desktop-logo bigger */
        .app-sidebar .header-brand-img.desktop-logo,
        .app-sidebar .header-brand-img.light-logo1 {
            height: 75px !important;
            max-height: 80px !important;
            transform: scale(1.70); /* Adjusted slightly for 240px width */
        }
        .app-sidebar .header-brand-img.toggle-logo,
        .app-sidebar .header-brand-img.light-logo {
            height: 55px !important;
            transform: scale(1.25);
        }

        /* ── Angle chevron > push left ── */
        .app-sidebar .side-menu__item .angle {
            margin-right: 15px !important;
            margin-left: auto !important;
        }
        /* Make all menu items use full-width flex so angle sits on right */
        .app-sidebar .side-menu__item {
            display: flex !important;
            align-items: center !important;
            padding: 7px 12px 7px 16px !important;
        }
        
        /* ── Center icons when sidebar is collapsed ── */
        @media (min-width: 992px) {
            .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .side-menu__item {
                justify-content: center !important;
                padding: 12px 0 !important; /* Space per icon */
            }
            .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .side-menu__icon {
                margin-right: 0 !important;
            }
            /* Push icons down slightly to make them look neater */
            .sidebar-mini.sidenav-toggled .app-sidebar:not(:hover) .side-menu {
                margin-top: 30px !important; /* Jarak antara logo dan ikon */
            }
        }
        .app-sidebar .side-menu__label {
            flex: 1;
        }

        /* --- GLOBAL MOBILE UI TWEAKS (Compact Shopee-style & iPhone XR Friendly) --- */
        @media (max-width: 767.98px) {
            /* 1. LAYOUT & CONTAINERS */
            .main-container { padding-left: 10px !important; padding-right: 10px !important; }
            .app-content .main-container { overflow-x: hidden; }
            .app-header .main-container { overflow: visible !important; }
            .card-body { padding: 12px 10px !important; }
            .card-header { padding: 10px 12px !important; min-height: 40px !important; }
            .row-sm > .col, .row-sm > [class*="col-"] {
                padding-right: 5px !important;
                padding-left: 5px !important;
                margin-bottom: 8px !important; 
            }
            .page-title { font-size: 1.15rem !important; margin-bottom: 0.2rem !important; line-height: 1.3 !important; }
            .page-header { margin-bottom: 0.8rem !important; flex-direction: column; align-items: flex-start !important; }

            /* 2. FORM CONTROLS & LABELS */
            .form-control:not(.select2-hidden-accessible), .form-select:not(.select2-hidden-accessible) {
                font-size: 0.85rem !important; height: 34px !important; padding: 0.3rem 0.6rem !important; border-radius: 6px !important;
            }
            label, .form-label { font-size: 0.8rem !important; margin-bottom: 0.3rem !important; font-weight: 600 !important; }
            .form-group, .mb-3, .mb-4 { margin-bottom: 10px !important; }

            /* Fix date inputs on mobile to look like PC instead of iOS dropdowns */
            input[type="date"] {
                -webkit-appearance: none !important;
                appearance: none !important;
                position: relative;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E") !important;
                background-repeat: no-repeat !important;
                background-position: right 0.6rem center !important;
                background-size: 14px !important;
                padding-right: 2rem !important;
            }
            input[type="date"]::-webkit-calendar-picker-indicator {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor: pointer;
                background: transparent;
            }

            /* 3. BUTTONS & ICON BUTTONS */
            .btn:not(.d-none) {
                font-size: 0.85rem !important; padding: 0.35rem 0.75rem !important; height: auto !important; border-radius: 6px !important; margin-bottom: 5px;
            }
            /* Tombol yg hanya berisi icon (seperti Edit/Hapus di tabel) */
            .btn:not(.d-none) i { line-height: 1 !important; }
            .btn-icon, .btn.btn-sm { padding: 0.3rem 0.5rem !important; font-size: 0.8rem !important; }
            .d-flex.align-items-end > .btn:not(.d-none), .d-flex.align-items-center > .btn:not(.d-none) { margin-right: 5px !important; margin-bottom: 5px !important; }
            
            /* 4. MODALS & POP-UPS */
            .modal-dialog { margin: 1rem auto !important; max-width: calc(100% - 2rem) !important; }
            .modal-dialog.modal-sm { max-width: 300px !important; margin: 1.5rem auto !important; }
            .modal-content { border-radius: 10px !important; border: none !important; }
            .modal-header, .modal-footer { padding: 10px 15px !important; }
            .modal-body { padding: 15px !important; }
            .modal-title { font-size: 1.1rem !important; font-weight: 700 !important; }
            /* SweetAlert 1 & 2 */
            .sweet-overlay { touch-action: none !important; }
            .swal2-popup { width: 85% !important; max-width: 320px !important; padding: 1rem !important; border-radius: 12px !important; touch-action: none !important; }
            .sweet-alert { width: 85% !important; max-width: 320px !important; padding: 1rem !important; border-radius: 12px !important; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; touch-action: none !important; }
            .sweet-alert [style*="overflow-y: auto"], .sweet-alert [style*="overflow-y: scroll"] { touch-action: pan-y !important; overscroll-behavior: contain !important; }
            .swal2-title, .sweet-alert h2 { font-size: 1.15rem !important; margin-top: 10px !important; margin-bottom: 0.5rem !important; line-height: 1.3 !important; }
            .swal2-content, .sweet-alert p { font-size: 0.85rem !important; line-height: 1.4 !important; }
            .sweet-alert .sa-icon { transform: scale(0.75); margin: 0 auto -15px auto !important; }
            .swal2-actions, .sweet-alert .sa-button-container { display: flex !important; justify-content: center !important; gap: 10px; margin-top: 1rem !important; }
            .swal2-actions button, .sweet-alert button { font-size: 0.85rem !important; padding: 0.4rem 1rem !important; margin: 0 !important; }

            /* Modal Konfirmasi Hapus Global */
            #Hmodaldemo8 .modal-dialog { width: 85% !important; max-width: 320px !important; margin: auto !important; }
            #Hmodaldemo8 .modal-body { padding: 1.5rem !important; }
            #Hmodaldemo8 .icon-exclamation { font-size: 3.5rem !important; margin: 1rem 0 1.5rem 0 !important; }
            #Hmodaldemo8 h3 { font-size: 1.15rem !important; margin-bottom: 1.5rem !important; line-height: 1.4 !important; }
            #Hmodaldemo8 .btn { margin: 0 5px !important; }

            /* 5. TABLES & DATATABLES */
            .table-responsive { margin-bottom: 10px !important; border-radius: 6px; overflow-x: auto; }
            .table-responsive .table { font-size: 0.8rem !important; }
            .table th, .table td { padding: 0.5rem 0.4rem !important; white-space: nowrap; }
            .dataTables_wrapper .dataTables_filter input { height: 32px !important; font-size: 0.8rem !important; margin-left: 0.5em !important; width: 140px !important; }
            .dataTables_length label { display: flex; align-items: center; gap: 5px; margin-bottom: 0 !important; }
            /* Center and fit Pagination & Info on Mobile */
            div.dataTables_wrapper div.dataTables_info { 
                font-size: 0.75rem !important; 
                padding-top: 0.5rem !important; 
                text-align: center !important; 
                margin-bottom: 8px !important; 
            }
            div.dataTables_wrapper div.dataTables_paginate {
                display: flex !important;
                justify-content: center !important;
                margin-top: 5px !important;
            }
            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                justify-content: center !important;
                gap: 3px !important;
            }
            div.dataTables_wrapper div.dataTables_paginate .page-link {
                padding: 0.35rem 0.55rem !important;
                font-size: 0.75rem !important;
            }

            /* 6. MISC / IMAGES */
            img, .img-fluid, #photoPreview { max-width: 100% !important; height: auto !important; object-fit: contain; }
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
<!-- <img src="{{ asset('assets/images/loader.svg') }}" class="loader-img" alt="Loader"> -->
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
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-body text-center p-4 pb-5">
                <button type="reset" aria-label="Close" class="btn-close position-absolute"
                    data-bs-dismiss="modal"></button>
                <br>
                <i class="icon icon-exclamation fs-70 text-warning lh-1 my-5 d-inline-block"></i>
                <h3 class="mb-5">Yakin logout ?</h3>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ url('admin/logout') }}" class="btn btn-danger-light w-45">Iya</a>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-default border w-45">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

<!-- JQUERY JS -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<!-- BOOTSTRAP JS -->
<script src="{{ asset('assets/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- Sticky js -->
<script src="{{ asset('assets/js/sticky.js') }}"></script>

<!-- INPUT MASK JS-->
<script src="{{ asset('assets/plugins/input-mask/jquery.mask.min.js') }}"></script>

<!-- SIDE-MENU JS-->
<script src="{{ asset('assets/plugins/sidemenu/sidemenu.js') }}"></script>

<!-- SIDEBAR JS -->
<script src="{{ asset('assets/plugins/sidebar/sidebar.js') }}"></script>

<!-- Perfect SCROLLBAR JS-->
<script src="{{ asset('assets/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/plugins/p-scroll/pscroll.js') }}"></script>

<!-- FILE UPLOADES JS -->
<script src="{{ asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{ asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

<!-- INTERNAL Bootstrap-Datepicker js-->
<script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!-- SELECT2 JS -->
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/select2.js') }}"></script>

<!-- BOOTSTRAP-DATERANGEPICKER JS -->
<script src="{{ asset('assets/plugins/bootstrap-daterangepicker/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

<!-- INTERNAL Bootstrap-Datepicker js-->
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<!-- INTERNAL Sumoselect js-->
<script src="{{ asset('assets/plugins/sumoselect/jquery.sumoselect.js') }}"></script>

<!-- TIMEPICKER JS -->
<script src="{{ asset('assets/plugins/time-picker/jquery.timepicker.js') }}"></script>
<script src="{{ asset('assets/plugins/time-picker/toggles.min.js') }}"></script>

<!-- INTERNAL intlTelInput js-->

<!-- INTERNAL jquery transfer js-->
<script src="{{ asset('assets/plugins/jQuerytransfer/jquery.transfer.js') }}"></script>

<!-- INTERNAL multi js-->
<script src="{{ asset('assets/plugins/multi/multi.min.js') }}"></script>

<!-- DATEPICKER JS -->
<script src="{{ asset('assets/plugins/date-picker/date-picker.js') }}"></script>
<script src="{{ asset('assets/plugins/date-picker/jquery-ui.js') }}"></script>
<script src="{{ asset('assets/plugins/input-mask/jquery.maskedinput.js') }}"></script>

<!-- COLOR PICKER JS -->
<script src="{{ asset('assets/plugins/pickr-master/pickr.es5.min.js') }}"></script>
<!-- MULTI SELECT JS-->
<script src="{{ asset('assets/plugins/multipleselect/multiple-select.js') }}"></script>
<script src="{{ asset('assets/plugins/multipleselect/multi-select.js') }}"></script>

<!-- SWEET-ALERT JS -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ asset('assets/js/sweet-alert.js') }}"></script>

<!-- INTERNAL CHARTJS CHART JS-->
<script src="{{ asset('assets/plugins/chart/Chart.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/chart/rounded-barchart.js') }}"></script>
<script src="{{ asset('assets/plugins/chart/utils.js') }}"></script>

<!-- DATA TABLE JS-->
<script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
<script src="{{ asset('assets/js/table-data.js') }}"></script>


<!-- INTERNAL INDEX JS -->

<!-- Color Theme js -->
<script src="{{ asset('assets/js/themeColors.js') }}"></script>

<!-- CUSTOM JS -->
<script src="{{ asset('assets/js/custom.js') }}"></script>

<script>
    // Global mobile Datatables pagination tweaks
    if (window.innerWidth <= 767.98) {
        $.fn.dataTable.ext.pager.numbers_length = 5;
    }

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

{{-- â”€â”€ GLOBAL FIX: Select2 + Bootstrap Modal scroll freeze â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
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
    // â”€â”€ Notifikasi Bell Polling â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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

<script>
    // â”€â”€ Fix DataTables column misalignment when sidebar is toggled â”€â”€
    $(document).on('click', '.app-sidebar__toggle', function() {
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
            if ($.fn.dataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        }, 310); // Wait slightly longer than 300ms CSS transition
    });

    // â”€â”€ Responsive Logout â”€â”€
    function confirmLogout() {
        if (window.innerWidth <= 768) {
            $('#modalLogout').modal('show');
        } else {
            swal({
                title: "Yakin logout ?",
                text: "Anda akan keluar dari sesi ini.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Iya, Logout",
                cancelButtonText: "Batal",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    window.location.href = "{{ url('admin/logout') }}";
                }
            });
        }
    }
</script>

@include('Master.Layouts.webcam_modal')

</body>

</html>
