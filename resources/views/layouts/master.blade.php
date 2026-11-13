<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SI SIDANG FTTM ITB')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">

    @include('layouts.partials.theme-head')

    <link rel="stylesheet" href="{{ asset('vendor/inter/300.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/400.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/500.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/600.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/inter/700.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap/css/select2-bootstrap.min.css') }}">

    <style>
        /* Select2 Custom Styling */
        .select2-container--bootstrap .select2-selection--single {
            height: calc(1.5em + 0.5rem + 2px) !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
            border-radius: 0 !important;
        }
        
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.5rem) !important;
            padding-left: 0 !important;
        }
        
        .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.5rem) !important;
        }
        
        .penilaian-toolbar,
        .penilaian-filters {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .penilaian-filters {
            flex: 1 1 580px;
            min-width: 0;
        }
        .penilaian-filter-field {
            display: flex;
            align-items: center;
            flex: 1 1 250px;
            min-width: 0;
            gap: 8px;
        }
        .penilaian-filter-field label {
            flex-shrink: 0;
            font-size: 14px !important;
        }
        .penilaian-filter-control {
            flex: 1;
            min-width: 0;
        }
        .tahap-container .penilaian-select,
        .penilaian-select + .select2-container {
            width: 100% !important;
            min-width: 0;
        }
        .tahap-container .penilaian-select,
        .penilaian-select + .select2-container--bootstrap .select2-selection--single {
            height: 38px !important;
            padding: 0 12px !important;
            font-size: 14px !important;
        }
        .penilaian-select + .select2-container--bootstrap .select2-selection--single {
            display: flex;
            align-items: center;
        }
        .penilaian-select + .select2-container--bootstrap .select2-selection__rendered {
            flex: 1;
            line-height: 36px !important;
        }
        .penilaian-select + .select2-container--bootstrap .select2-selection__arrow {
            height: 36px !important;
        }

        /* Select2 Dark Mode */
        html.dark-mode .select2-container--bootstrap .select2-selection--single {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        
        html.dark-mode .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }
        
        html.dark-mode .select2-container--bootstrap .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
        }
        
        html.dark-mode .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }
        
        html.dark-mode .select2-container--bootstrap .select2-results__option {
            color: #f1f5f9 !important;
        }
        
        html.dark-mode .select2-container--bootstrap .select2-results__option--highlighted {
            background-color: #334155 !important;
            color: #60a5fa !important;
        }
        
        html.dark-mode .select2-container--bootstrap .select2-results__option[aria-selected=true] {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
        }
        
        html.dark-mode .select2-search--dropdown .select2-search__field {
            background-color: #334155 !important;
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        
        html.dark-mode .select2-search--dropdown .select2-search__field:focus {
            border-color: #60a5fa !important;
        }
    </style>
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --primary-blue-dark: #1e3a8a;
            --sidebar-bg: #1e3a8a;
            --accent: #3b82f6;
            --bg-light: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --body-bg: #1e3a8a;
            --card-bg: #ffffff;
            --card-header-bg: #ffffff;
            --table-stripe: #f8fafc;
            --table-hover: #f1f5f9;
            --input-bg: #ffffff;
            --toast-bg: #ffffff;
            --navbar-bg: #ffffff;
            --content-bg: #ffffff;
            --nav-sidebar-from: #1462ad;
            --nav-sidebar-to: #1462ad;
            --nav-border: rgba(255, 255, 255, 0.14);
            --nav-text: rgba(255, 255, 255, 0.82);
            --nav-text-strong: #ffffff;
            --nav-hover: rgba(255, 255, 255, 0.12);
            --nav-active-bg: rgba(255, 255, 255, 0.18);
            --nav-accent: #ffffff;
            --nav-icon: rgba(224, 242, 254, 0.72);
            --nav-icon-active: #ffffff;
            --topbar-bg: #1462ad;
            --topbar-border: rgba(255, 255, 255, 0.22);
            --topbar-edge: rgba(4, 28, 55, 0.40);
            --topbar-text: rgba(255, 255, 255, 0.78);
            --topbar-text-strong: #ffffff;
            --topbar-hover: rgba(255, 255, 255, 0.14);
            --topbar-field-bg: rgba(255, 255, 255, 0.14);
            --topbar-field-border: rgba(255, 255, 255, 0.26);
            --topbar-accent: #ffffff;
            --topbar-ring: rgba(255, 255, 255, 0.22);
            --topbar-avatar-bg: #ffffff;
            --topbar-avatar-text: #1462ad;
            --topbar-avatar-border: rgba(255, 255, 255, 0.55);
            --frame-bg: #1462ad;
            --panel-bg: #ffffff;
            --panel-border: #e5eaf1;
            --dd-bg: #ffffff;
            --dd-border: #e5eaf1;
            --dd-text: #1e293b;
            --dd-muted: #64748b;
            --dd-hover: #f1f5f9;
        }

        html.dark-mode {
            --primary-blue: #0f172a;
            --primary-blue-dark: #0f172a;
            --sidebar-bg: #0f172a;
            --accent: #60a5fa;
            --bg-light: #1e293b;
            --text-dark: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --body-bg: #0f172a;
            --card-bg: #1e293b;
            --card-header-bg: #1e293b;
            --table-stripe: #1e293b;
            --table-hover: #334155;
            --input-bg: #334155;
            --toast-bg: #1e293b;
            --navbar-bg: #1e293b;
            --content-bg: #0f172a;
            --nav-sidebar-from: #13243e;
            --nav-sidebar-to: #0b1424;
            --nav-border: rgba(148, 163, 184, 0.12);
            --nav-text: rgba(203, 213, 225, 0.72);
            --nav-text-strong: #f8fafc;
            --nav-hover: rgba(148, 163, 184, 0.10);
            --nav-active-bg: rgba(96, 165, 250, 0.16);
            --nav-accent: #60a5fa;
            --nav-icon: rgba(191, 219, 254, 0.55);
            --nav-icon-active: #60a5fa;
            --topbar-bg: #0f172a;
            --topbar-border: rgba(148, 163, 184, 0.14);
            --topbar-edge: rgba(2, 6, 23, 0.55);
            --topbar-text: #94a3b8;
            --topbar-text-strong: #f8fafc;
            --topbar-hover: rgba(148, 163, 184, 0.10);
            --topbar-field-bg: rgba(148, 163, 184, 0.10);
            --topbar-field-border: rgba(148, 163, 184, 0.16);
            --topbar-accent: #60a5fa;
            --topbar-ring: rgba(96, 165, 250, 0.18);
            --topbar-avatar-bg: linear-gradient(135deg, #1d4ed8, #3b82f6);
            --topbar-avatar-text: #ffffff;
            --topbar-avatar-border: rgba(148, 163, 184, 0.24);
            --frame-bg: #0a1220;
            --panel-bg: #0f172a;
            --panel-border: rgba(148, 163, 184, 0.14);
            --dd-bg: #111c30;
            --dd-border: rgba(148, 163, 184, 0.16);
            --dd-text: #e2e8f0;
            --dd-muted: #94a3b8;
            --dd-hover: rgba(148, 163, 184, 0.10);
        }

        html.dark-mode body {
            background: var(--frame-bg) !important;
        }
        html.dark-mode .wrapper {
            background: var(--frame-bg) !important;
        }
        
        /* OVERRIDE AdminLTE .dark-mode hardcoded background */
        .dark-mode {
            background-color: transparent !important;
            color: inherit !important;
        }
        
        html.dark-mode,
        html.dark-mode body {
            background: var(--body-bg) !important;
        }
        html.dark-mode .content-wrapper {
            background: var(--content-bg) !important;
        }
        html.dark-mode .content-wrapper > .content {
            background: var(--content-bg) !important;
        }
        html.dark-mode .main-footer {
            background: var(--navbar-bg) !important;
            color: var(--text-muted) !important;
        }
        html.dark-mode .card {
            background: var(--card-bg) !important;
        }
        html.dark-mode .card-header {
            background: var(--card-header-bg) !important;
            border-bottom-color: var(--border-color) !important;
        }
        html.dark-mode .card-footer {
            background: var(--card-header-bg) !important;
            border-top-color: var(--border-color) !important;
        }
        html.dark-mode .table {
            color: var(--text-dark) !important;
        }
        html.dark-mode .table-striped tbody tr:nth-of-type(odd) {
            background: var(--table-stripe) !important;
        }
        html.dark-mode .table-hover tbody tr:hover {
            background: var(--table-hover) !important;
        }
        html.dark-mode .table td,
        html.dark-mode .table th {
            border-top-color: var(--border-color) !important;
        }
        html.dark-mode .table thead th {
            border-bottom-color: var(--border-color) !important;
        }
        /* Baris yang sudah di-approve (table-secondary) di dark mode tampil
           sama seperti baris biasa — tanpa highlight/petak tambahan. */
        html.dark-mode .table-secondary,
        html.dark-mode .table > tbody > tr.table-secondary,
        html.dark-mode .table > tbody > tr > td.table-secondary,
        html.dark-mode .table > tbody > tr.table-secondary > td,
        html.dark-mode .table > tbody > tr.table-secondary > th {
            background: transparent !important;
            color: var(--text-dark) !important;
            box-shadow: none !important;
        }
        /* Hover baris approved tetap sama seperti baris lain */
        html.dark-mode .table-hover > tbody > tr.table-secondary:hover {
            background: var(--table-hover) !important;
        }
        html.dark-mode .table-danger,
        html.dark-mode .table > tbody > tr.table-danger,
        html.dark-mode .table > tbody > tr > td.table-danger {
            background: #7f1d1d !important;
            color: #fecaca !important;
        }
        html.dark-mode .table > tbody > tr.table-danger > td,
        html.dark-mode .table > tbody > tr.table-danger > th {
            background-color: #7f1d1d !important;
            color: #fecaca !important;
        }
        html.dark-mode .badge.bg-success,
        html.dark-mode .badge.badge-success {
            background-color: #15803d !important;
            color: #fff !important;
        }
        html.dark-mode .badge.bg-danger,
        html.dark-mode .badge.badge-danger {
            background-color: #dc2626 !important;
            color: #fff !important;
        }
        html.dark-mode .badge.bg-warning,
        html.dark-mode .badge.badge-warning {
            background-color: #d97706 !important;
            color: #fff !important;
        }
        html.dark-mode .badge.bg-secondary,
        html.dark-mode .badge.badge-secondary {
            background-color: #475569 !important;
            color: #fff !important;
        }
        html.dark-mode .form-control {
            background: var(--input-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }
        html.dark-mode .form-control:focus {
            border-color: var(--accent) !important;
        }
        html.dark-mode h1, html.dark-mode h2, html.dark-mode h3,
        html.dark-mode h4, html.dark-mode h5, html.dark-mode h6,
        html.dark-mode p, html.dark-mode span:not(.badge):not(.brand-text),
        html.dark-mode div:not(.main-sidebar):not(.sidebar):not(.brand-link) {
            color: var(--text-dark);
        }
        html.dark-mode .text-muted {
            color: var(--text-muted) !important;
        }
        html.dark-mode .breadcrumb-item.active {
            color: var(--text-muted) !important;
        }
        html.dark-mode .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted) !important;
        }
        html.dark-mode a:not(.btn):not(.nav-link):not(.brand-link) {
            color: var(--accent) !important;
        }
        html.dark-mode .pagination .page-item .page-link {
            background: var(--card-bg) !important;
            border-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }
        html.dark-mode .pagination .page-item.active .page-link {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
        }
        html.dark-mode .pagination .page-item.disabled .page-link {
            background: var(--input-bg) !important;
            color: var(--text-muted) !important;
        }
        .modal-dialog.modal-tahapan-wide {
            width: calc(100% - 32px);
            max-width: 1680px;
            margin: 1.75rem auto;
        }
        @media (max-width: 575.98px) {
            .modal-dialog.modal-tahapan-wide {
                width: calc(100% - 16px);
                margin: 0.5rem auto;
            }
        }

        html.dark-mode .modal-content {
            background: var(--card-bg) !important;
            border-color: var(--border-color) !important;
        }
        html.dark-mode .modal-header {
            border-bottom-color: var(--border-color) !important;
        }
        html.dark-mode .modal-footer {
            border-top-color: var(--border-color) !important;
        }
        html.dark-mode .close {
            color: var(--text-dark) !important;
            text-shadow: none !important;
        }

        /* Header putih (seragam untuk semua modal & card header master) */
        .modal-header.header-white {
            background: #ffffff !important;
            color: #1e293b !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .modal-header.header-white .modal-title {
            color: #1e293b !important;
        }
        .modal-header.header-white .close {
            color: #1e293b !important;
            opacity: 0.7;
            text-shadow: none !important;
        }
        .modal-header.header-white .close:hover {
            opacity: 1;
        }

        /* Modal import master: lebih lebar supaya kolom select (Tahapan, Strata) tidak terpotong */
        @media (min-width: 576px) {
            .modal-dialog.modal-dialog-scrollable {
                max-width: min(1320px, calc(100vw - 2rem));
            }
        }
        html.dark-mode .table-bordered,
        html.dark-mode .table-bordered td,
        html.dark-mode .table-bordered th {
            border-color: var(--border-color) !important;
        }
        html.dark-mode .dropdown-menu {
            background: var(--card-bg) !important;
            border-color: var(--border-color) !important;
        }
        html.dark-mode .dropdown-item {
            color: var(--text-dark) !important;
        }
        html.dark-mode .dropdown-item:hover {
            background: var(--table-hover) !important;
        }
        html.dark-mode select option {
            background: var(--card-bg) !important;
            color: var(--text-dark) !important;
        }
        html.dark-mode .custom-toast {
            background: var(--toast-bg) !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4) !important;
        }
        
        .dark-mode-toggle {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        .dark-mode-toggle:hover {
            background: var(--table-hover);
            color: var(--text-dark);
        }

        /* Global Font & Body */
        body, .wrapper, .main-sidebar, .main-header, .content-wrapper, .main-footer {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        html {
            height: 100%;
        }

        body {
            background-color: var(--frame-bg) !important;
            min-height: 100vh;
        }

        .wrapper {
            background-color: var(--frame-bg) !important;
            min-height: 100vh;
            animation: fadeInPage 0.25s ease-in-out;
        }

        @keyframes fadeInPage {
            0% { opacity: 0; transform: translateY(5px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Main Header (Navbar) Styling */
        .main-header {
            background: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
            padding: 12px 24px !important;
            transition: margin-left .3s ease-in-out;
        }

        .main-header .navbar-nav .nav-link {
            color: var(--text-muted) !important;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .main-header .navbar-nav .nav-link:hover {
            color: var(--text-dark) !important;
        }

        /* ==========================================================
           SIDEBAR — professional navy rail (light & dark theme)
           ========================================================== */
        .main-sidebar {
            background: linear-gradient(180deg, var(--nav-sidebar-from) 0%, var(--nav-sidebar-to) 100%) !important;
            box-shadow: none !important;
            border-right: 1px solid var(--nav-border) !important;
        }

        .main-sidebar .brand-link {
            background: transparent !important;
            border-bottom: 1px solid var(--nav-border) !important;
            padding: 18px 18px !important;
            min-height: 80px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: opacity .15s ease;
        }

        .main-sidebar .brand-link:hover {
            opacity: .92;
        }

        .main-sidebar .brand-logo {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #ffffff;
            padding: 5px;
            box-shadow: 0 4px 12px rgba(2, 6, 23, .35);
        }

        .main-sidebar .brand-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            float: none;
            margin: 0;
            line-height: 1.15;
            font-size: 1rem !important;
            font-weight: 700 !important;
            letter-spacing: 0;
            color: #ffffff !important;
        }

        .main-sidebar .brand-title {
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .07em;
            color: #ffffff;
            white-space: nowrap;
        }

        .main-sidebar .brand-sub {
            margin-top: 3px;
            font-size: .66rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .72);
            white-space: nowrap;
        }

        .main-sidebar .brand-role {
            margin-top: 7px;
            align-self: flex-start;
            max-width: 100%;
            padding: 2px 8px;
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .03em;
            color: #ffffff;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 999px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .main-sidebar .sidebar {
            padding: 0 12px !important;
        }

        .main-sidebar .sidebar > nav {
            margin-top: 10px !important;
        }

        .main-sidebar .sidebar .nav-sidebar {
            width: 100% !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item {
            width: 100% !important;
            margin: 2px 0 !important;
            position: relative;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link {
            display: flex;
            align-items: center;
            margin: 0 !important;
            padding: 10px 14px !important;
            border-radius: 10px !important;
            color: var(--nav-text) !important;
            font-size: .82rem !important;
            font-weight: 500 !important;
            letter-spacing: .01em;
            transition: background .15s ease, color .15s ease;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link > p {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 0;
            font-size: inherit !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link:hover {
            background: var(--nav-hover) !important;
            color: var(--nav-text-strong) !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-link .nav-icon,
        .main-sidebar .sidebar .nav-sidebar .nav-link > i {
            width: 20px;
            margin-right: 12px !important;
            font-size: .92rem !important;
            text-align: center;
            color: var(--nav-icon);
            transition: color .15s ease;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-link:hover .nav-icon,
        .main-sidebar .sidebar .nav-sidebar .nav-link:hover > i {
            color: var(--nav-text-strong);
            transform: none;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active {
            background: var(--nav-active-bg) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active .nav-icon,
        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active > i,
        .main-sidebar .sidebar .nav-sidebar .nav-item.menu-open > .nav-link .nav-icon {
            color: var(--nav-icon-active) !important;
        }

        /* Retire the old curved-cutout effect */
        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active::before,
        .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active::after {
            content: none !important;
            display: none !important;
            box-shadow: none !important;
        }

        /* Accent rail on the active top-level item */
        .main-sidebar .sidebar .nav-sidebar > .nav-item > .nav-link.active::before {
            content: "" !important;
            display: block !important;
            position: absolute;
            left: -6px;
            top: 50%;
            width: 3px;
            height: 20px;
            transform: translateY(-50%);
            background: var(--nav-accent);
            border-radius: 0 3px 3px 0;
            box-shadow: none !important;
            pointer-events: none;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-link .right {
            font-size: .68rem;
            opacity: .45;
            transition: transform .2s ease, opacity .15s ease;
        }

        .main-sidebar .sidebar .nav-sidebar .menu-open > .nav-link .right {
            opacity: .85;
        }

        /* Submenu */
        .main-sidebar .sidebar .nav-sidebar .nav-treeview {
            background: transparent !important;
            padding: 2px 0 4px 0 !important;
            margin: 0 !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-item {
            margin: 1px 0 !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-link {
            padding: 8px 12px 8px 20px !important;
            margin: 0 0 0 8px !important;
            border-radius: 8px !important;
            border-left: none !important;
            background: transparent !important;
            color: var(--nav-text) !important;
            font-size: .78rem !important;
            font-weight: 500 !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-link:hover {
            background: var(--nav-hover) !important;
            color: var(--nav-text-strong) !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-link.active {
            background: var(--nav-active-bg) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-left: none !important;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-link .nav-icon {
            width: 16px;
            margin-right: 10px !important;
            font-size: .78rem !important;
            color: var(--nav-icon);
            opacity: .9;
        }

        .main-sidebar .sidebar .nav-sidebar .nav-treeview .nav-link.active .nav-icon {
            color: var(--nav-icon-active) !important;
            opacity: 1;
        }

        /* Collapsed (icon only) sidebar */
        .sidebar-mini.sidebar-collapse .main-sidebar .brand-link {
            justify-content: center;
            min-height: 80px;
            padding: 16px 8px !important;
            gap: 0;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .brand-text {
            display: none !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .brand-logo {
            width: 36px;
            height: 36px;
            padding: 4px;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar {
            padding: 0 10px !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar {
            margin: 0 !important;
            width: 100% !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link {
            width: 100% !important;
            justify-content: center;
            padding: 11px 0 !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link > p,
        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar .nav-link .right {
            display: none !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar .nav-link .nav-icon {
            margin: 0 !important;
            opacity: .85 !important;
            color: var(--nav-text-strong) !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar .nav-item > .nav-link.active .nav-icon {
            opacity: 1 !important;
            color: #ffffff !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .sidebar .nav-sidebar > .nav-item > .nav-link.active::before {
            display: none !important;
        }

        /* The rail stays fixed-width: the floating header would cover an expanded rail */
        .sidebar-mini.sidebar-collapse .main-sidebar:hover {
            width: 4.6rem !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .brand-link {
            width: 4.6rem !important;
        }

        /* Unified floating content area */
        @media (min-width: 992px) {
            .main-header {
                margin-top: 16px !important;
                margin-left: 270px !important;
                margin-right: 20px !important;
                border: 1px solid var(--topbar-edge) !important;
                border-radius: 14px 14px 0 0 !important;
                box-shadow: 0 12px 28px -22px rgba(2, 6, 23, .55) !important;
            }

            .content-wrapper {
                margin-left: 270px !important;
                margin-right: 20px !important;
                margin-bottom: 20px !important;
                border-radius: 0 0 14px 14px !important;
                margin-top: 0px !important;
            }

            .sidebar-collapse .main-header,
            .sidebar-collapse .content-wrapper,
            .sidebar-collapse .main-footer {
                margin-left: 93px !important;
            }
        }

        @media (max-width: 991.98px) {
            .main-header {
                position: sticky !important;
                top: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }

            .content-wrapper {
                margin: 0 !important;
                border-radius: 0 !important;
                border-left: none !important;
                border-right: none !important;
            }

            /* Drawer sits below the topbar so the brand block stays visible */
            .main-sidebar {
                top: 64px !important;
                height: calc(100vh - 64px) !important;
                min-height: 0 !important;
                z-index: 1039 !important;
            }
        }

        .content-wrapper {
            background: var(--panel-bg) !important;
            border: 1px solid var(--panel-border) !important;
            border-top: none !important;
            padding: 24px !important;
            box-shadow: 0 20px 40px -30px rgba(15, 23, 42, .35) !important;
            transition: margin-left .3s ease-in-out;
            min-height: calc(100vh - 96px);
        }

        .user-panel {
            border-bottom: 1px solid var(--nav-border) !important;
            padding-bottom: 16px !important;
            margin-bottom: 16px !important;
        }

        .user-panel .info a {
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Card Customization */
        .card {
            border: 1px solid var(--panel-border) !important;
            border-radius: 14px !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
            background: var(--panel-bg) !important;
            margin-bottom: 24px;
        }

        .card-header {
            background: var(--panel-bg) !important;
            border-bottom: 1px solid var(--panel-border) !important;
            color: var(--text-dark) !important;
            border-top-left-radius: 14px !important;
            border-top-right-radius: 14px !important;
            padding: 16px 20px !important;
        }

        .card-header h5, .card-header .card-title, .card-header h3 {
            font-weight: 600 !important;
            font-size: 1.1rem !important;
            color: var(--text-dark) !important;
            margin-bottom: 0 !important;
        }

        /* Ensure card header buttons stay on the right */
        .card-header.d-flex {
            justify-content: space-between !important;
        }
        .card-header.d-flex .btn {
            margin-left: auto !important;
        }

        .card-body {
            padding: 20px !important;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 16px !important;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .stat-card .icon-bg {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        /* Form Inputs */
        .form-control, select.form-control {
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            padding: 10px 14px !important;
            height: auto !important;
            color: var(--text-dark) !important;
        }

        .form-control:focus {
            border-color: var(--primary-blue) !important;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15) !important;
        }

        .input-group-text {
            border-radius: 8px 0 0 8px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #f1f5f9 !important;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0 !important;
        }

        /* Buttons */
        .btn {
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 8px 16px !important;
        }

        .btn-primary {
            background-color: var(--primary-blue) !important;
            border-color: var(--primary-blue) !important;
        }

        .btn-primary:hover {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
        }

        .btn-accent {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        .btn-accent:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
        }

        .btn-warning {
            background-color: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #ffffff !important;
        }

        .btn-warning:hover {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
        }

        .btn-danger {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
        }

        .btn-danger:hover {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
        }

        /* Simpan = black */
        .btn-primary {
            background-color: #212529 !important;
            border-color: #212529 !important;
        }

        .btn-primary:hover {
            background-color: #000000 !important;
            border-color: #000000 !important;
        }

        /* Batal = red */
        .btn-secondary {
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #ffffff !important;
        }

        .btn-secondary:hover {
            background-color: #dc2626 !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }

        /* Tombol hitam untuk Cetak */
        .btn-black {
            background-color: #212529 !important;
            border-color: #212529 !important;
            color: #ffffff !important;
        }

        .btn-black:hover {
            background-color: #000000 !important;
            border-color: #000000 !important;
        }

        /* Ajukan = green */
        .btn-success {
            background-color: #16a34a !important;
            border-color: #16a34a !important;
        }

        /* Tables */
        .table thead th {
            background-color: var(--bg-light) !important;
            color: var(--text-muted) !important;
            border-bottom: 1px solid var(--border-color) !important;
            font-size: 0.8rem !important;
            text-transform: uppercase;
            font-weight: 600;
            padding: 12px 16px !important;
        }

        .table tbody td {
            padding: 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: var(--text-dark) !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(248, 250, 252, 0.5) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(241, 245, 249, 0.5) !important;
        }

        /* Font size kecil di sidebar menu */
        .sidebar .nav-sidebar .nav-link,
        .nav-sidebar .nav-treeview .nav-link {
            font-size: 0.8rem !important;
        }

        .sidebar .nav-sidebar .nav-link p,
        .nav-sidebar .nav-treeview .nav-link p {
            font-size: 0.8rem !important;
        }

        /* Font size kecil di navbar */
        .main-header .navbar-nav .nav-link {
            font-size: 0.8rem !important;
        }

        /* Font size kecil di table */
        .table tbody td {
            font-size: 0.8rem !important;
        }

        /* Breadcrumbs in navbar styling */
        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 0.875rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: #94a3b8 !important;
            content: ">" !important;
        }

        .breadcrumb-item a {
            color: var(--text-muted) !important;
            font-weight: 400;
            transition: color 0.15s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--text-dark) !important;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--text-dark) !important;
            font-weight: 500;
        }

        /* Footer styling inside content area */
        .main-footer {
            background: transparent !important;
            border-top: none !important;
            margin-left: 270px !important;
            margin-right: 20px !important;
            margin-bottom: 14px !important;
            padding: 6px 2px !important;
            color: rgba(255, 255, 255, .78) !important;
            transition: margin-left .3s ease-in-out;
            font-size: 0.8rem !important;
        }

        @media (max-width: 991.98px) {
            .main-footer {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 10px 14px !important;
            }
        }

        .main-footer a,
        .main-footer strong {
            color: #ffffff !important;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar .nav-sidebar .nav-item .nav-treeview {
            display: none !important;
        }

        /* Custom Toast Notification styling */
        .custom-toast {
            background: #ffffff !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            border-left: 4px solid #10b981 !important; /* Green for success */
            padding: 16px !important;
            margin-bottom: 12px;
            animation: slideInRight 0.3s ease forwards;
            transition: all 0.3s ease;
        }
        #toastError {
            border-left: 4px solid #ef4444 !important; /* Red for error */
        }
        .toast-icon {
            font-size: 1.2rem;
            margin-top: 2px;
        }
        @keyframes slideInRight {
            from {
                transform: translateX(120%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Teams-like Notification Dropdown */
        /* Light Mode (default) - PRIORITAS TINGGI */
        .dropdown-menu-teams {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
            color: #1f2937 !important;
            padding: 0 !important;
            width: 440px !important;
            max-height: 80vh;
            overflow-y: auto;
        }
        .dropdown-menu-teams::-webkit-scrollbar {
            width: 8px;
        }
        .dropdown-menu-teams::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 4px;
        }
        .teams-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 16px 12px;
            position: sticky;
            top: 0;
            background-color: #ffffff !important;
            z-index: 10;
            border-bottom: 2px solid #3b82f6 !important;
        }
        .teams-dropdown-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827 !important;
        }
        .teams-header-actions {
            display: flex;
            gap: 8px;
        }
        .teams-header-actions button {
            background: transparent;
            border: none;
            color: #6b7280 !important;
            padding: 6px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .teams-header-actions button:hover {
            background: #f3f4f6;
            color: #111827 !important;
        }
        .teams-nav-tabs {
            display: flex;
            padding: 0 16px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 8px;
        }
        .teams-nav-tabs .tab {
            padding: 8px 0;
            margin-right: 20px;
            color: #6b7280;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .teams-nav-tabs .tab.active {
            color: #111827;
            border-bottom: 2px solid #3b82f6;
        }

        .teams-time-group {
            display: flex;
            align-items: center;
            padding: 12px 16px 4px;
            color: #6b7280 !important;
            font-size: 12px;
            font-weight: 600;
        }
        .teams-time-group::before, .teams-time-group::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        .teams-time-group span {
            padding: 0 10px;
        }

        .teams-notif-item {
            display: flex;
            padding: 12px 16px;
            text-decoration: none !important;
            color: #1f2937 !important;
            gap: 16px;
            transition: background 0.2s;
            margin: 0;
        }
        .teams-notif-item:hover {
            background-color: #f9fafb !important;
        }

        /* Dark Mode - override dengan specificity tinggi */
        body.dark-mode .dropdown-menu-teams {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.6) !important;
            color: #f1f5f9 !important;
        }
        body.dark-mode .dropdown-menu-teams::-webkit-scrollbar-thumb {
            background-color: #475569 !important;
        }
        body.dark-mode .teams-dropdown-header {
            background-color: #1e293b !important;
            border-bottom: 2px solid #3b82f6 !important;
        }
        body.dark-mode .teams-dropdown-header h5 {
            color: #f1f5f9 !important;
        }
        body.dark-mode .teams-header-actions button {
            color: #cbd5e1 !important;
        }
        body.dark-mode .teams-header-actions button:hover {
            background: #334155 !important;
            color: #f1f5f9 !important;
        }
        body.dark-mode .teams-nav-tabs {
            border-bottom: 1px solid #334155;
        }
        body.dark-mode .teams-nav-tabs .tab {
            color: #cbd5e1;
        }
        body.dark-mode .teams-nav-tabs .tab.active {
            color: #f1f5f9;
        }
        body.dark-mode .teams-time-group {
            color: #cbd5e1 !important;
        }
        body.dark-mode .teams-time-group::before, 
        body.dark-mode .teams-time-group::after {
            border-bottom: 1px solid #334155;
        }
        body.dark-mode .teams-notif-item {
            color: #f1f5f9 !important;
        }
        body.dark-mode .teams-notif-item:hover {
            background-color: #334155 !important;
        }
        .teams-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .teams-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #9370DB; /* Default purple */
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            color: #fff;
            text-transform: uppercase;
        }
        .teams-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .teams-app-icon {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 22px;
            height: 22px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        body.dark-mode .teams-app-icon {
            background-color: #1e293b;
        }
        .teams-app-icon .icon-bg {
            background-color: #7479ed;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .teams-app-icon i {
            font-size: 10px;
            color: #fff;
        }
        .teams-notif-content {
            flex: 1;
            min-width: 0;
        }
        .teams-notif-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #111827 !important;
        }
        body.dark-mode .teams-notif-title {
            color: #f1f5f9 !important;
        }
        .teams-notif-title span.title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .teams-notif-time {
            font-size: 12px;
            color: #6b7280 !important;
            font-weight: 400;
            white-space: nowrap;
            margin-left: 10px;
            flex-shrink: 0;
        }
        body.dark-mode .teams-notif-time {
            color: #cbd5e1 !important;
        }
        .teams-notif-desc {
            font-size: 13px;
            color: #6b7280 !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        body.dark-mode .teams-notif-desc {
            color: #cbd5e1 !important;
        }

        /* Notification empty message styling */
        .notif-empty-message {
            color: var(--dd-muted) !important;
        }

        /* Teams-like Profile Dropdown */
        .profile-dropdown-teams {
            background-color: #242424 !important;
            border: 1px solid #3b3b3b !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5) !important;
            color: #fff !important;
            padding: 0 !important;
            width: 340px !important;
        }
        .profile-top {
            display: flex;
            justify-content: space-between;
            padding: 16px;
            font-size: 13px;
            color: #c8c8c8;
        }
        .profile-top a, .profile-top button {
            color: #c8c8c8;
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            font-size: 13px;
            cursor: pointer;
        }
        .profile-top a:hover, .profile-top button:hover {
            color: #fff;
            text-decoration: underline;
        }
        .profile-middle {
            display: flex;
            padding: 16px;
            gap: 16px;
        }
        .profile-pic-large {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-info {
            flex: 1;
        }
        .profile-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }
        .profile-email {
            font-size: 12px;
            color: #c8c8c8;
            margin-bottom: 8px;
        }
        .profile-link {
            display: block;
            color: #7479ed !important;
            text-decoration: none;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .profile-link:hover {
            text-decoration: underline;
        }
        .profile-bottom {
            padding: 16px;
            border-top: 1px solid #3b3b3b;
        }
        .profile-bottom a {
            display: flex;
            align-items: center;
            color: #fff !important;
            text-decoration: none;
            font-size: 14px;
        }
        .profile-bottom a i {
            font-size: 20px;
            margin-right: 12px;
            color: #c8c8c8;
        }

        /* ============================================
           TOPBAR — light & dark aware
        ============================================ */
        .main-header.navbar {
            background: var(--topbar-bg) !important;
            border-bottom: 1px solid var(--topbar-edge) !important;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04) !important;
            height: 64px !important;
            padding: 0 20px !important;
            z-index: 1040 !important;
        }

        /* Hamburger & breadcrumb */
        .main-header .navbar-nav .nav-link {
            color: var(--topbar-text) !important;
            border-radius: 9px;
            padding: 8px 10px !important;
            font-size: .82rem !important;
            transition: background .15s ease, color .15s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .main-header .navbar-nav .nav-link:hover {
            color: var(--topbar-text-strong) !important;
            background: var(--topbar-hover) !important;
        }

        .main-header .navbar-nav > .nav-item > .nav-link[data-widget="pushmenu"] {
            width: 38px;
            height: 38px;
            justify-content: center;
            padding: 0 !important;
            font-size: .9rem !important;
        }

        /* Breadcrumb text in navbar */
        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: .84rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--topbar-text) !important;
            opacity: .6;
            content: "/" !important;
        }

        .breadcrumb-item a {
            color: var(--topbar-text) !important;
            font-weight: 400;
            transition: color 0.15s ease;
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--topbar-text-strong) !important;
        }

        .breadcrumb-item.active {
            color: var(--topbar-text-strong) !important;
            font-weight: 600;
        }

        /* ---- Search field ---- */
        .navbar-search-wrap {
            width: 250px;
            position: relative;
        }

        .navbar-search-wrap input {
            width: 100%;
            height: 38px;
            background: var(--topbar-field-bg) !important;
            border: 1px solid var(--topbar-field-border) !important;
            border-radius: 10px !important;
            color: var(--topbar-text-strong) !important;
            font-size: .82rem;
            padding: 8px 14px 8px 36px !important;
            transition: border-color .15s ease, background .15s ease;
            outline: none;
            box-shadow: none !important;
        }

        .navbar-search-wrap input::placeholder {
            color: var(--topbar-text);
            opacity: .8;
        }

        .navbar-search-wrap input:focus {
            background: var(--topbar-hover) !important;
            border-color: var(--topbar-accent) !important;
            box-shadow: 0 0 0 3px var(--topbar-ring) !important;
        }

        .navbar-search-wrap .search-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--topbar-text);
            font-size: .78rem;
            pointer-events: none;
        }

        /* ---- Icon buttons ---- */
        .navbar-icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            color: var(--topbar-text) !important;
            font-size: 1rem;
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
            text-decoration: none !important;
            padding: 0 !important;
        }

        .navbar-icon-btn:hover {
            background: var(--topbar-hover) !important;
            color: var(--topbar-text-strong) !important;
        }

        .navbar-icon-btn .notif-badge {
            position: absolute;
            top: 3px;
            right: 3px;
            min-width: 17px;
            height: 17px;
            padding: 0 4px;
            background: #ef4444;
            color: #fff;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--topbar-bg);
            line-height: 1;
        }

        .dark-mode-toggle {
            width: 38px;
            height: 38px;
            justify-content: center;
            padding: 0 !important;
            border-radius: 10px;
            color: var(--topbar-text);
            background: transparent;
        }

        .dark-mode-toggle:hover {
            background: var(--topbar-hover);
            color: var(--topbar-text-strong);
        }

        /* ---- Avatar button ---- */
        .navbar-avatar-btn,
        .navbar-avatar-fallback {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid var(--topbar-border);
            transition: border-color .15s ease, box-shadow .15s ease;
            flex-shrink: 0;
        }

        .navbar-avatar-btn {
            overflow: hidden;
            cursor: pointer;
            display: block;
        }

        .navbar-avatar-btn:hover,
        .navbar-avatar-fallback:hover {
            border-color: var(--topbar-accent);
            box-shadow: 0 0 0 3px var(--topbar-ring);
        }

        .navbar-avatar-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .navbar-avatar-fallback {
            background: var(--topbar-avatar-bg);
            border-color: var(--topbar-avatar-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--topbar-avatar-text);
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }

        /* Right nav item spacing */
        .navbar-right-items {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ---- Notification dropdown ---- */
        .dropdown-menu-teams {
            background-color: var(--dd-bg) !important;
            border: 1px solid var(--dd-border) !important;
            border-radius: 14px !important;
            box-shadow: 0 20px 45px -18px rgba(15, 23, 42, .45) !important;
            color: var(--dd-text) !important;
            padding: 0 !important;
            width: 420px !important;
            max-height: min(80vh, 560px);
            overflow-y: auto;
            margin-top: 10px !important;
        }
        .dropdown-menu-teams::-webkit-scrollbar { width: 6px; }
        .dropdown-menu-teams::-webkit-scrollbar-track { background: transparent; }
        .dropdown-menu-teams::-webkit-scrollbar-thumb {
            background-color: var(--dd-border);
            border-radius: 4px;
        }
        .teams-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 16px 12px;
            position: sticky;
            top: 0;
            background-color: var(--dd-bg);
            border-bottom: 1px solid var(--dd-border);
            z-index: 10;
            border-radius: 14px 14px 0 0;
        }
        .teams-dropdown-header h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--dd-text);
        }
        .teams-header-actions {
            display: flex;
            gap: 2px;
        }
        .teams-header-actions button {
            background: transparent;
            border: none;
            color: var(--dd-muted);
            width: 30px;
            height: 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s ease, color .15s ease;
        }
        .teams-header-actions button:hover {
            background: var(--dd-hover);
            color: var(--dd-text);
        }
        .teams-time-group {
            display: flex;
            align-items: center;
            padding: 14px 16px 6px;
            color: var(--dd-muted);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .teams-time-group::before, .teams-time-group::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--dd-border);
        }
        .teams-time-group span { padding: 0 10px; }
        .teams-notif-item {
            display: flex;
            padding: 12px 16px;
            text-decoration: none !important;
            color: var(--dd-text) !important;
            gap: 14px;
            border-left: 3px solid transparent;
            transition: background .15s ease, border-color .15s ease;
        }
        .teams-notif-item:hover {
            background-color: var(--dd-hover);
        }
        .teams-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .teams-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            color: #fff;
            text-transform: uppercase;
        }
        .teams-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .teams-app-icon {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            background-color: var(--dd-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        .teams-app-icon .icon-bg {
            background-color: #3b82f6;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .teams-app-icon i { font-size: 8px; color: #fff; }
        .teams-notif-content { flex: 1; min-width: 0; }
        .teams-notif-title {
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--dd-text);
        }
        .teams-notif-title span.title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .teams-notif-time {
            font-size: 10.5px;
            color: var(--dd-muted);
            font-weight: 400;
            white-space: nowrap;
            margin-left: 10px;
            flex-shrink: 0;
        }
        .teams-notif-desc {
            font-size: 11.5px;
            color: var(--dd-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .teams-notif-item.unread {
            position: relative;
            border-left-color: #3b82f6;
            background: rgba(96, 165, 250, .05);
        }
        .teams-notif-item.unread .teams-notif-title .title-text {
            color: var(--dd-text);
            font-weight: 700;
        }
        .teams-notif-item.unread .teams-notif-desc {
            color: var(--dd-muted);
        }
        .teams-notif-item.unread::before {
            display: none;
        }
        .teams-notif-item.read .teams-notif-title .title-text {
            color: var(--dd-muted);
            font-weight: 500;
        }
        .teams-notif-item.read .teams-notif-desc {
            color: var(--dd-muted);
            opacity: .8;
        }
        .notif-read-btn {
            background: transparent;
            border: 1px solid var(--dd-border);
            border-radius: 8px;
            width: 26px;
            height: 26px;
            flex-shrink: 0;
            align-self: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dd-muted);
            font-size: 10px;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            transition: all .15s ease;
        }
        .notif-read-btn:hover {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
        }

        /* ---- Profile dropdown ---- */
        .profile-dropdown-teams {
            background-color: var(--dd-bg) !important;
            border: 1px solid var(--dd-border) !important;
            border-radius: 14px !important;
            box-shadow: 0 20px 45px -18px rgba(15, 23, 42, .45) !important;
            color: var(--dd-text) !important;
            padding: 0 !important;
            width: 300px !important;
            margin-top: 10px !important;
        }
        .profile-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            font-size: 12px;
            color: var(--dd-muted);
            border-bottom: 1px solid var(--dd-border);
        }
        .profile-top button {
            color: var(--dd-muted);
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: color .15s ease;
        }
        .profile-top button:hover { color: var(--dd-text); text-decoration: underline; }
        .profile-middle {
            display: flex;
            padding: 18px 16px;
            gap: 14px;
            align-items: flex-start;
        }
        .profile-pic-large {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--dd-border);
            flex-shrink: 0;
        }
        .profile-pic-fallback {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1d4ed8, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
        }
        .profile-info { flex: 1; min-width: 0; }
        .profile-name {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--dd-text);
        }
        .profile-email {
            font-size: 11px;
            color: var(--dd-muted);
            margin-bottom: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-link {
            display: block;
            color: #3b82f6 !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: color .15s ease;
        }
        .profile-link:hover { color: #2563eb !important; text-decoration: underline; }
        .profile-bottom {
            padding: 10px 12px;
            border-top: 1px solid var(--dd-border);
        }
        .profile-bottom a {
            display: flex;
            align-items: center;
            color: var(--dd-text) !important;
            text-decoration: none;
            font-size: 13px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: background .15s ease;
        }
        .profile-bottom a:hover {
            background: var(--dd-hover);
        }
        .profile-bottom a i {
            font-size: 16px;
            margin-right: 10px;
            color: var(--dd-muted);
        }

        /* Modern badge overrides */
        .badge {
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            font-size: 0.7rem !important;
        }
        .badge.bg-success, .badge.badge-success {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #10b981 !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
        }
        .badge.bg-danger, .badge.badge-danger {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
        }
        .badge.bg-warning, .badge.badge-warning {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #f59e0b !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
        }
        .badge.bg-info, .badge.badge-info {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #3b82f6 !important;
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
        }

        /* Jadwal Sidang - Calendar & Card Dark Mode Styling */
        /* STRATEGI: Warna pastel tetap tampil, tapi text selalu kontras */
        
        /* Calendar - Keep colorful background, ensure text visibility */
        body.dark-mode .table-bordered {
            border-color: #475569 !important;
        }
        
        /* Calendar header row - jangan bocor ke tabel lain di dark mode */
        body.dark-mode .jadwal-calendar thead th {
            color: var(--text-muted) !important;
            font-weight: 700 !important;
            border-color: #475569 !important;
        }
        
        /* Calendar body cells - keep pastel colors */
        body.dark-mode .table tbody td {
            /* Background color tetap dari inline style (dayColors) */
            border-color: #475569 !important;
        }
        
        /* Day numbers - ensure visibility on pastel backgrounds */
        body.dark-mode .table tbody td > div > span.font-weight-bold {
            color: #0f172a !important; /* Gelap agar kontras dengan pastel */
            text-shadow: 0 0 2px rgba(255,255,255,0.5);
        }
        
        body.dark-mode .table tbody td > div > span.font-weight-bold.text-primary {
            color: #1e3a8a !important; /* Biru gelap untuk hari ini */
            text-shadow: 0 0 3px rgba(255,255,255,0.7);
        }
        
        /* Badge count on calendar cells */
        body.dark-mode .table tbody td .badge-primary {
            background-color: #1e3a8a !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        
        /* Event cards in calendar - keep white for readability */
        body.dark-mode .table tbody td .rounded.shadow-sm {
            background: #ffffff !important;
            border-left-color: #1e3a8a !important;
            color: #0f172a !important;
        }
        
        body.dark-mode .table tbody td .rounded.shadow-sm strong {
            color: #0f172a !important;
        }
        
        body.dark-mode .table tbody td .rounded.shadow-sm .text-muted {
            color: #475569 !important;
        }
        
        /* Card View - PENTING: Text harus gelap di atas background warna-warni */
        body.dark-mode #card .mb-4 {
            border-color: #475569 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.6) !important;
        }
        
        /* Card date headers - keep colorful, text dark for contrast */
        body.dark-mode #card .mb-4 > div[style*="background-color"] {
            /* Background tetap colorful dari inline style */
            color: #0f172a !important; /* Text GELAP */
            font-weight: 700 !important;
            border-bottom-color: rgba(0,0,0,0.2) !important;
        }
        
        body.dark-mode #card .mb-4 > div[style*="background-color"] i {
            color: #1e3a8a !important; /* Icon biru gelap */
        }
        
        /* Card content rows - keep colorful backgrounds, DARK text */
        body.dark-mode #card .d-flex.p-3[style*="background-color"] {
            /* Background tetap colorful dari inline style ($rowColors) */
            color: #0f172a !important; /* Text GELAP untuk kontras */
            font-weight: 600 !important;
        }
        
        /* PAKSA semua text di card rows jadi gelap */
        body.dark-mode #card .d-flex.p-3 div,
        body.dark-mode #card .d-flex.p-3 table,
        body.dark-mode #card .d-flex.p-3 td {
            color: #0f172a !important; /* GELAP agar terbaca di pastel */
        }
        
        /* Border antar rows */
        body.dark-mode #card .d-flex.p-3[style*="border-bottom"] {
            border-bottom-color: rgba(0,0,0,0.3) !important;
        }
        
        /* Empty state text */
        body.dark-mode #card .text-center.text-muted {
            color: #cbd5e1 !important;
        }
        
        /* General card styling */
        body.dark-mode .card {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        
        body.dark-mode .card-header {
            background-color: #334155 !important;
            border-bottom-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-mode .card-body {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        
        /* Tab navigation */
        body.dark-mode .nav-tabs {
            border-bottom-color: #475569 !important;
        }
        
        body.dark-mode .nav-tabs .nav-link {
            color: #cbd5e1 !important;
            border-color: transparent !important;
        }
        
        body.dark-mode .nav-tabs .nav-link.active {
            background-color: #334155 !important;
            border-color: #475569 #475569 #334155 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-mode .nav-tabs .nav-link:hover {
            border-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        
        /* Modal styling */
        body.dark-mode .modal-content {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-mode .modal-header {
            background-color: #334155 !important;
            border-bottom-color: #475569 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-mode .modal-body {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-mode .modal-body .card {
            background-color: #334155 !important;
            border-left-color: #3b82f6 !important;
        }
        
        body.dark-mode .modal-body .card .text-muted {
            color: #cbd5e1 !important;
        }
        
        body.dark-mode .modal-body h5 {
            color: #f1f5f9 !important;
        }
        
        /* Buttons */
        body.dark-mode .btn-outline-primary {
            color: #60a5fa !important;
            border-color: #3b82f6 !important;
        }
        
        body.dark-mode .btn-outline-primary:hover {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }
        
        /* Page title */
        body.dark-mode h4.font-weight-bold {
            color: #f1f5f9 !important;
        }
        
        /* Footer text */
        body.dark-mode .text-muted.small {
            color: #cbd5e1 !important;
        }
        
        /* Today border highlight */
        body.dark-mode .table tbody td[style*="border: 3px solid #1e3a8a"] {
            border-color: #3b82f6 !important;
            box-shadow: inset 0 0 10px rgba(59, 130, 246, 0.3) !important;
        }
    </style>
    @stack('styles')
    @include('master._ui_theme')
</head>
<body class="hold-transition sidebar-mini layout-fixed @yield('body_class')">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark">
        {{-- Left: hamburger + breadcrumb --}}
        <ul class="navbar-nav align-items-center">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-flex align-items-center ml-2">
                @yield('breadcrumb')
            </li>
        </ul>

        {{-- Right: search + bell + avatar --}}
        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-none d-lg-flex align-items-center mr-2">
                <div class="navbar-search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Cari...">
                </div>
            </li>

            {{-- Dark Mode Toggle --}}
            <li class="nav-item d-flex align-items-center mr-1">
                <button class="dark-mode-toggle" id="darkModeToggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>
            </li>

            {{-- Bell / Notifications --}}
            <li class="nav-item dropdown d-flex align-items-center">
                <a class="nav-link navbar-icon-btn" data-toggle="dropdown" href="#" role="button" id="notifBell">
                    <i class="far fa-bell" style="font-size:1.05rem;"></i>
                    <span class="notif-badge" id="notifBadge" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-teams" id="notifDropdown">
                    <div class="teams-dropdown-header">
                        <h5>Notifications</h5>
                        <div class="teams-header-actions">
                            <button title="Refresh" onclick="event.preventDefault(); event.stopPropagation(); loadNotifications();"><i class="fas fa-sync-alt"></i></button>
                            <button title="Mark all read" onclick="markAllRead()"><i class="fas fa-check-double"></i></button>
                            <button title="Close" onclick="$(this).closest('.dropdown-menu').parent().find('.nav-link').dropdown('toggle'); return false;"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                    <div id="notifList">
                        <div class="text-center py-4 notif-empty-message" style="font-size:0.85rem;">Memuat notifikasi...</div>
                    </div>
                </div>
            </li>

            {{-- Avatar / Profile --}}
            <li class="nav-item dropdown d-flex align-items-center ml-1">
                <a class="nav-link p-0" data-toggle="dropdown" href="#" role="button" style="display:flex;align-items:center;">
                    @php
                        $avatarUrl = session('auth_user.avatar');
                        $userName  = session('auth_user.nama_lengkap', 'User');
                        $initials  = strtoupper(substr($userName, 0, 1));
                    @endphp
                    @if($avatarUrl)
                        <div class="navbar-avatar-btn">
                            <img src="{{ $avatarUrl }}" alt="Avatar" onerror="this.parentElement.outerHTML='<div class=\'navbar-avatar-fallback\'>{{ $initials }}</div>'">
                        </div>
                    @else
                        <div class="navbar-avatar-fallback">{{ $initials }}</div>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown-teams">
                    <div class="profile-top">
                        <span>SI Sidang FTTM</span>
                        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit">Sign out</button>
                        </form>
                    </div>
                    <div class="profile-middle">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" class="profile-pic-large" alt="Profile" onerror="this.outerHTML='<div class=\'profile-pic-fallback\'>{{ $initials }}</div>'">
                        @else
                            <div class="profile-pic-fallback">{{ $initials }}</div>
                        @endif
                        <div class="profile-info">
                            <div class="profile-name">{{ session('auth_user.nama_lengkap', 'Nama User') }}</div>
                            <div class="profile-email">{{ session('auth_user.email', 'email@example.com') }}</div>
                            <a href="{{ route('profile') }}" class="profile-link">View account</a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB Logo" class="brand-logo">
            <span class="brand-text">
                <span class="brand-title">SI SIDANG</span>
                <span class="brand-sub">FTTM ITB</span>
                <span class="brand-role">{{ \App\Models\TUserRole::label(session('auth_user.role')) }}</span>
            </span>
        </a>

        <div class="sidebar">
            <nav class="mt-4">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">

                    @if(session('auth_user.role') !== 'Mahasiswa')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Home</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'Admin')
                    <!-- ADMIN NAVIGATION -->
                    <li class="nav-item has-treeview {{ request()->routeIs('master.persyaratan*', 'master.penilaian*', 'master.prodi*', 'master.fakultas*', 'master.mk-prodi*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('master.persyaratan*', 'master.penilaian*', 'master.prodi*', 'master.fakultas*', 'master.mk-prodi*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Data Master <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('master.persyaratan.index') }}" class="nav-link {{ request()->routeIs('master.persyaratan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-alt"></i>
                                    <p>Persyaratan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.penilaian.index') }}" class="nav-link {{ request()->routeIs('master.penilaian*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Penilaian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.fakultas.index') }}" class="nav-link {{ request()->routeIs('master.fakultas*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-landmark"></i>
                                    <p>Fakultas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.prodi.index') }}" class="nav-link {{ request()->routeIs('master.prodi*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Prodi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.mk-prodi.index') }}" class="nav-link {{ request()->routeIs('master.mk-prodi*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Mata Kuliah Prodi</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.kpps.index') }}" class="nav-link {{ request()->routeIs('master.kpps*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Data KPPS</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.user.index') }}" class="nav-link {{ request()->routeIs('master.user*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data User</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'TU Prodi')
                    <!-- TU PRODI NAVIGATION -->
                    <li class="nav-item has-treeview {{ request()->routeIs('master.persyaratan*', 'master.penilaian*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('master.persyaratan*', 'master.penilaian*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>Data Master <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('master.persyaratan.index') }}" class="nav-link {{ request()->routeIs('master.persyaratan*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-alt"></i>
                                    <p>Persyaratan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.penilaian.index') }}" class="nav-link {{ request()->routeIs('master.penilaian*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Penilaian</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.kpps.index') }}" class="nav-link {{ request()->routeIs('master.kpps*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Data KPPS</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('master.user.index') }}" class="nav-link {{ request()->routeIs('master.user*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Data User</p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'active' : '' }}">
                             <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Sidang <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sidang.s1') }}" class="nav-link {{ request()->routeIs('sidang.s1') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S1</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s2') }}" class="nav-link {{ request()->routeIs('sidang.s2') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S2</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s3') }}" class="nav-link {{ request()->routeIs('sidang.s3') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S3</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sidang.jadwal-sidang') }}" class="nav-link {{ request()->routeIs('sidang.jadwal-sidang') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Sidang</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Report Sidang S3</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'FS')
                    <!-- TU FS NAVIGATION - Tanpa Data Master dan Approve User -->
                    <li class="nav-item has-treeview {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Sidang <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sidang.s1') }}" class="nav-link {{ request()->routeIs('sidang.s1') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S1</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s2') }}" class="nav-link {{ request()->routeIs('sidang.s2') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S2</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s3') }}" class="nav-link {{ request()->routeIs('sidang.s3') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S3</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sidang.jadwal-sidang') }}" class="nav-link {{ request()->routeIs('sidang.jadwal-sidang') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Sidang</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Report Sidang S3</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'Monev')
                    <!-- MONEV NAVIGATION - Hanya Report -->
                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Report Sidang S3</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'Mahasiswa')
                    <!-- MAHASISWA NAVIGATION - Semua Strata -->
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Progress Sidang</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sidang.jadwal-sidang') }}" class="nav-link {{ request()->routeIs('sidang.jadwal-sidang') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Sidang</p>
                        </a>
                    </li>
                    @endif

                    @if(in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'Ketua Sidang']))
                    <!-- PEMBIMBING & PENGUJI NAVIGATION -->
                    <li class="nav-item has-treeview {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('sidang.s1', 'sidang.s2', 'sidang.s3') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>Penilaian Sidang <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sidang.s1') }}" class="nav-link {{ request()->routeIs('sidang.s1') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S1</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s2') }}" class="nav-link {{ request()->routeIs('sidang.s2') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S2</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.s3') }}" class="nav-link {{ request()->routeIs('sidang.s3') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S3</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sidang.jadwal-sidang') }}" class="nav-link {{ request()->routeIs('sidang.jadwal-sidang') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Sidang</p>
                        </a>
                    </li>
                    @endif

                    @if(session('auth_user.role') === 'KPPS')
                    <!-- KPPS NAVIGATION - Approval Ajuan Sidang (khusus S3) -->
                    <li class="nav-item">
                        <a href="{{ route('sidang.approve-ajuan.index', 'S3') }}" class="nav-link {{ request()->routeIs('sidang.approve-ajuan*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>Approve Ajuan Sidang (S3)</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sidang.jadwal-sidang') }}" class="nav-link {{ request()->routeIs('sidang.jadwal-sidang') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Jadwal Sidang</p>
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="color: rgba(255,255,255,.8); width: 100%; text-align: left;">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </button>
                        </form>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('ganti-role.index') }}" class="nav-link {{ request()->routeIs('ganti-role*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>Ganti Role</p>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header" style="padding: 0 0 15px 0 !important;">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12">
                        <h1 class="m-0" style="font-weight: 700; color: var(--text-dark); font-size: 1.5rem;">@yield('page_title')</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" style="padding: 0 !important;">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} SI SIDANG FTTM ITB.</strong> All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>

<div class="position-fixed" style="top: 24px; right: 24px; z-index: 9999; max-width: 350px; width: 100%;">
    <!-- Success Toast -->
    <div id="toastSuccess" class="custom-toast" style="display: none;">
        <div class="custom-toast-content d-flex align-items-start">
            <div class="toast-icon text-success mr-3">
                <i class="fas fa-check-circle fa-lg"></i>
            </div>
            <div class="toast-text flex-grow-1">
                <h6 class="toast-title mb-0 font-weight-bold" style="color: #1e293b; font-size: 0.95rem;">Berhasil</h6>
                <p id="toastSuccessMsg" class="toast-msg mb-0 text-muted mt-1" style="font-size: 0.85rem; line-height: 1.4; color: #64748b;"></p>
            </div>
            <button type="button" class="close toast-close-btn ml-3" style="background:none; border:none; font-size: 1.25rem; color:#94a3b8; cursor:pointer;" onclick="hideToast('success')">&times;</button>
        </div>
    </div>
    <!-- Error Toast -->
    <div id="toastError" class="custom-toast" style="display: none;">
        <div class="custom-toast-content d-flex align-items-start">
            <div class="toast-icon text-danger mr-3">
                <i class="fas fa-exclamation-circle fa-lg"></i>
            </div>
            <div class="toast-text flex-grow-1">
                <h6 class="toast-title mb-0 font-weight-bold" style="color: #1e293b; font-size: 0.95rem;">Gagal</h6>
                <p id="toastErrorMsg" class="toast-msg mb-0 text-muted mt-1" style="font-size: 0.85rem; line-height: 1.4; color: #64748b;"></p>
            </div>
            <button type="button" class="close toast-close-btn ml-3" style="background:none; border:none; font-size: 1.25rem; color:#94a3b8; cursor:pointer;" onclick="hideToast('error')">&times;</button>
        </div>
    </div>
</div>

<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('vendor/adminlte/js/adminlte.min.js') }}"></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
<script nonce="{{ request()->attributes->get('csp_nonce') }}" src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

<script nonce="{{ request()->attributes->get('csp_nonce') }}">
    var toastTimer = null;

    function showToast(type, message) {
        var id = type === 'success' ? '#toastSuccess' : '#toastError';
        var msgId = type === 'success' ? '#toastSuccessMsg' : '#toastErrorMsg';

        if (toastTimer) clearTimeout(toastTimer);
        $(id).fadeOut(100);

        $(msgId).text(message);
        $(id).fadeIn(200);

        toastTimer = setTimeout(function() {
            $(id).fadeOut(300);
            toastTimer = null;
        }, 3000);
    }

    function hideToast(type) {
        var id = type === 'success' ? '#toastSuccess' : '#toastError';
        $(id).fadeOut(300);
    }

    document.addEventListener('themechange', function (e) {
        updateNotificationDropdownTheme(e.detail.dark);
    });

    function updateNotificationDropdownTheme(isDark) {
        var dropdown = document.getElementById('notifDropdown');
        var header = document.querySelector('.teams-dropdown-header');
        
        if (dropdown) {
            if (isDark) {
                dropdown.style.backgroundColor = '#111c30';
                dropdown.style.borderColor = 'rgba(148, 163, 184, .16)';
                dropdown.style.color = '#e2e8f0';
            } else {
                dropdown.style.backgroundColor = '#ffffff';
                dropdown.style.borderColor = '#e5eaf1';
                dropdown.style.color = '#1e293b';
            }
        }
        
        if (header) {
            if (isDark) {
                header.style.backgroundColor = '#111c30';
                header.style.borderBottomColor = '#3b82f6';
            } else {
                header.style.backgroundColor = '#ffffff';
                header.style.borderBottomColor = '#3b82f6';
            }
            
            var h5 = header.querySelector('h5');
            if (h5) {
                h5.style.color = isDark ? '#e2e8f0' : '#0f172a';
            }
            
            var buttons = header.querySelectorAll('button');
            buttons.forEach(function(btn) {
                btn.style.color = isDark ? '#94a3b8' : '#64748b';
            });
        }
    }

    function loadNotifications() {
        $.get('{{ route("notifications.index") }}', function(res) {
            var badge = $('#notifBadge');
            var count = res.unread_count || 0;
            badge.text(count > 99 ? '99+' : count);
            badge.toggle(count > 0);

            var list = $('#notifList');
            if (!res.notifications || res.notifications.length === 0) {
                list.html('<div class="text-center py-4 notif-empty-message" style="font-size:0.85rem;">Tidak ada notifikasi</div>');
                return;
            }

            var html = '';
            var today = new Date();
            var yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            function parseNotifDate(dateStr) {
                if (!dateStr) return null;
                var d;
                if (String(dateStr).indexOf('T') !== -1) {
                    d = new Date(dateStr);
                } else {
                    d = new Date(String(dateStr).replace(' ', 'T') + 'Z');
                }
                return isNaN(d.getTime()) ? null : d;
            }

            function timeAgo(dateStr) {
                var d = parseNotifDate(dateStr);
                if (!d) return '';
                var now = new Date();
                var diff = Math.floor((now - d) / 1000);
                if (isNaN(diff)) return '';
                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                if (diff < 172800) return 'Yesterday';
                return Math.floor(diff / 86400) + 'd ago';
            }

            function groupLabel(d) {
                var date = parseNotifDate(d.created_at);
                if (!date) return 'Lainnya';
                if (date.toDateString() === today.toDateString()) return 'Today';
                if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
                return date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            }

            var groups = {};
            res.notifications.forEach(function(n) {
                var g = groupLabel(n);
                if (!groups[g]) groups[g] = [];
                groups[g].push(n);
            });

            Object.keys(groups).forEach(function(group) {
                html += '<div class="teams-time-group"><span>' + group + '</span></div>';
                groups[group].forEach(function(n) {
                    var initial = (n.title || 'N').charAt(0).toUpperCase();
                    var colors = ['#2f5597', '#9B870C', '#8A2BE2', '#E9967A', '#2563EB', '#059669', '#D97706'];
                    var color = colors[Math.abs(n.id) % colors.length];
                    var notifClass = n.is_read ? 'teams-notif-item read' : 'teams-notif-item unread';
                    var link = n.link || '#';
                    html += '<a href="' + link + '" class="' + notifClass + '" data-id="' + n.id + '" onclick="markRead(' + n.id + ')">';
                    html += '<div class="teams-avatar-wrapper">';
                    html += '<div class="teams-avatar" style="background:' + color + ';">' + initial + '</div>';
                    html += '<div class="teams-app-icon"><div class="icon-bg"><i class="fas fa-bell"></i></div></div>';
                    html += '</div>';
                    html += '<div class="teams-notif-content">';
                    html += '<div class="teams-notif-title">';
                    html += '<span class="title-text">' + n.title + '</span>';
                    html += '<span class="teams-notif-time">' + timeAgo(n.created_at) + '</span>';
                    html += '</div>';
                    if (n.message) {
                        html += '<div class="teams-notif-desc">' + n.message + '</div>';
                    }
                    html += '</div>';
                    html += '<button type="button" class="notif-read-btn" title="Tandai sudah dibaca" onclick="event.preventDefault(); event.stopPropagation(); markRead(' + n.id + ');"><i class="fas fa-check"></i></button>';
                    html += '</a>';
                });
            });

            list.html(html);
        }).fail(function() {
            $('#notifList').html('<div class="text-center py-4 notif-empty-message" style="font-size:0.85rem;">Gagal memuat notifikasi</div>');
        });
    }

    function markRead(id) {
        $.post('{{ route("notifications.mark-read", ":id") }}'.replace(':id', id), {
            _token: '{{ csrf_token() }}'
        }, function() {
            loadNotifications();
        });
    }

    function markAllRead() {
        $.post('{{ route("notifications.mark-all-read") }}', {
            _token: '{{ csrf_token() }}'
        }, function() {
            loadNotifications();
        });
    }

    $(function () {
        @if(session('success'))
            showToast('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            showToast('error', '{{ session('error') }}');
        @endif

        loadNotifications();
        $('#notifBell').on('click', function() {
            setTimeout(loadNotifications, 100);
        });
    });
</script>

<div class="modal fade" id="globalConfirmModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none; box-shadow:0 .5rem 1.5rem rgba(0,0,0,.2);">
            <div class="modal-body text-center pt-4 pb-2 px-4">
                <div id="gcmIcon" class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;border-radius:50%;background:#fdecea;">
                    <i class="fas fa-trash-alt" style="font-size:28px;color:#e74a3b;"></i>
                </div>
                <h5 id="gcmTitle" class="font-weight-bold mb-2">Hapus Data</h5>
                <p id="gcmMessage" class="text-muted mb-1" style="font-size:14px;">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center pb-4 pt-2">
                <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="button" id="gcmConfirmBtn" class="btn btn-danger px-4 font-weight-bold">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ request()->attributes->get('csp_nonce') }}">
function showConfirmDialog(opts) {
    opts = opts || {};
    var $modal = $('#globalConfirmModal');

    $('#gcmTitle').text(opts.title || 'Konfirmasi');
    $('#gcmMessage').text(opts.message || 'Apakah Anda yakin?');

    var themes = {
        danger:  { bg: '#fdecea', color: '#e74a3b', icon: 'fa-trash-alt',             btn: 'btn-danger'  },
        warning: { bg: '#fff8e1', color: '#f6c23e', icon: 'fa-exclamation-triangle', btn: 'btn-warning' },
        primary: { bg: '#e8f0fe', color: '#4e73df', icon: 'fa-check-circle',          btn: 'btn-primary' }
    };
    var t = themes[opts.type] || themes.danger;

    $('#gcmIcon').css('background', t.bg)
                 .find('i').attr('class', 'fas ' + t.icon).css('color', t.color);
    $('#gcmConfirmBtn').attr('class', 'btn px-4 font-weight-bold ' + t.btn)
                       .text(opts.confirmText || 'Ya, Lanjutkan');

    return new Promise(function (resolve) {
        var settled = false;
        var done = function (val) {
            if (settled) return;
            settled = true;
            resolve(val);
        };

        $('#gcmConfirmBtn').off('click').on('click', function () {
            done(true);
            $modal.modal('hide');
        });

        $modal.off('hidden.bs.modal.gcm').on('hidden.bs.modal.gcm', function () {
            done(false);
        });

        $modal.modal('show');
    });
}
</script>

<div class="modal fade" id="sessionRenewalModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none; box-shadow:0 .5rem 1.5rem rgba(0,0,0,.2);">
            <div class="modal-body text-center pt-4 pb-2 px-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:72px;height:72px;border-radius:50%;background:#fff8e1;">
                    <i class="fas fa-clock" style="font-size:28px;color:#f6c23e;"></i>
                </div>
                <h5 class="font-weight-bold mb-2">Perpanjangan Session</h5>
                <p class="text-muted mb-1" style="font-size:14px;">Session Anda akan habis dalam <strong id="sessionCountdown" class="text-danger">00:00</strong></p>
                <p class="text-muted mb-3" style="font-size:13px;">Apakah Anda ingin memperpanjang session?</p>
            </div>
            <div class="modal-footer border-top-0 justify-content-center pb-4 pt-2">
                <button type="button" id="sessionRenewBtn" class="btn btn-success px-4 font-weight-bold">Ya, Perpanjang</button>
                <button type="button" id="sessionLogoutBtn" class="btn btn-danger px-4 font-weight-bold">Tidak</button>
            </div>
        </div>
    </div>
</div>

<div id="sessionToast" class="position-fixed" style="bottom:20px;right:20px;z-index:1050;display:none;">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="min-width:300px;">
        <div class="toast-header bg-warning text-dark">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong class="mr-auto">Session</strong>
            <small>Baru saja</small>
        </div>
        <div class="toast-body">
            <span id="sessionToastMsg">Session telah habis. Silakan login kembali.</span>
        </div>
    </div>
</div>

@php
    $authUser = session('auth_user');
    $sessionRemaining = 0;
    if ($authUser) {
        $createdAt = $authUser['session_created_at'] ?? 0;
        $duration = $authUser['session_duration'] ?? 21600;
        $now = now()->timestamp;
        $remainingAtRender = $duration - ($now - $createdAt);
        $absoluteRemaining = 28800 - ($now - ($authUser['session_login_at'] ?? $createdAt));
        $sessionRemaining = max(0, min($remainingAtRender, $absoluteRemaining));
    }
@endphp
<script nonce="{{ request()->attributes->get('csp_nonce') }}">
(function() {
    var SESSION_RENEW_URL = '{{ route("session.renew") }}';
    var LOGOUT_URL = '{{ route("logout") }}';
    var csrfToken = '{{ csrf_token() }}';
    var remaining = {{ (int) $sessionRemaining }};
    var WARNING_SECONDS = 300;

    var logoutForm = document.createElement('form');
    logoutForm.method = 'POST';
    logoutForm.action = LOGOUT_URL;
    logoutForm.style.display = 'none';
    var csrfField = document.createElement('input');
    csrfField.type = 'hidden';
    csrfField.name = '_token';
    csrfField.value = csrfToken;
    logoutForm.appendChild(csrfField);
    document.body.appendChild(logoutForm);

    var timer = null;
    var warningShown = false;
    var modalOpen = false;
    var loggedOut = false;

    function formatTime(seconds) {
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    function showSessionToast(msg) {
        $('#sessionToastMsg').text(msg);
        $('#sessionToast').show();
    }

    function hideSessionToast() {
        $('#sessionToast').hide();
    }

    function doLogout() {
        if (loggedOut) return;
        loggedOut = true;
        showSessionToast('Session telah habis. Silakan login kembali.');
        setTimeout(function() { logoutForm.submit(); }, 2000);
    }

    function openRenewalModal() {
        warningShown = true;
        modalOpen = true;
        $('#sessionCountdown').text(formatTime(remaining));
        $('#sessionRenewalModal').modal('show');
    }

    function tick() {
        remaining--;

        if (remaining <= 0) {
            clearInterval(timer);
            if (modalOpen) $('#sessionRenewalModal').modal('hide');
            doLogout();
            return;
        }

        if (remaining <= WARNING_SECONDS && !warningShown) {
            openRenewalModal();
        }

        if (modalOpen) {
            $('#sessionCountdown').text(formatTime(remaining));
        }
    }

    if (remaining > 0) {
        if (remaining <= WARNING_SECONDS) {
            openRenewalModal();
        }
        timer = setInterval(tick, 1000);
    }

    $('#sessionRenewBtn').on('click', function() {
        $.ajax({
            url: SESSION_RENEW_URL,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    remaining = data.remaining;
                    warningShown = false;
                    modalOpen = false;
                    $('#sessionRenewalModal').modal('hide');
                    hideSessionToast();
                    showSessionToast('Session berhasil diperpanjang.');
                    setTimeout(hideSessionToast, 3000);
                } else {
                    doLogout();
                }
            },
            error: function() {
                showSessionToast('Gagal memperpanjang session.');
            }
        });
    });

    $('#sessionLogoutBtn').on('click', function() {
        logoutForm.submit();
    });
})();
</script>

@include('layouts.partials.theme-script')
@stack('scripts')
</body>
</html>
