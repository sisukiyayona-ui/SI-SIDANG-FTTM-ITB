<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SI SIDANG FTTM ITB')</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ionicons@2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css">

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
        }

        html.dark-mode body {
            background: var(--body-bg) !important;
        }
        html.dark-mode .wrapper {
            background: var(--body-bg) !important;
        }
        html.dark-mode html,
        html.dark-mode body {
            background: var(--body-bg) !important;
        }
        html.dark-mode .main-header {
            background: var(--navbar-bg) !important;
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
        
        /* SIDEBAR NAVIGATION - DARK MODE FIX */
        /* Parent Menu (Data Master) when expanded/opened - MUST BE BLUE! */
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link,
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-is-opening > .nav-link,
        html.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item.menu-open > .nav-link,
        html.dark-mode [class*="sidebar-dark"] .nav-sidebar > .nav-item.menu-open > .nav-link {
            background-color: transparent !important;
            color: #60a5fa !important;
        }
        
        /* Parent menu icons when expanded */
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link .nav-icon,
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link i,
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link .right {
            color: #60a5fa !important;
        }
        
        /* Parent menu hover when expanded */
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link:hover {
            background-color: rgba(96, 165, 250, 0.15) !important;
            color: #60a5fa !important;
        }
        
        /* Child menu items (Persyaratan, Penilaian, etc) */
        html.dark-mode .main-sidebar .nav-treeview > .nav-item > .nav-link,
        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
        }
        
        /* Child menu active state */
        html.dark-mode .main-sidebar .nav-treeview > .nav-item > .nav-link.active,
        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link.active {
            background-color: var(--body-bg) !important;
            color: #60a5fa !important;
        }
        
        /* Child menu hover */
        html.dark-mode .main-sidebar .nav-treeview > .nav-item > .nav-link:hover,
        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link:hover {
            background-color: rgba(96, 165, 250, 0.1) !important;
            color: #60a5fa !important;
        }
        
        /* Override any AdminLTE default that makes it black */
        html.dark-mode .main-sidebar .nav-sidebar .nav-item > .nav-link.active {
            background: var(--body-bg) !important;
            color: var(--accent) !important;
        }
        html.dark-mode .main-sidebar .nav-sidebar .nav-item > .nav-link.active::before {
            box-shadow: 15px 15px 0 15px var(--body-bg) !important;
        }
        html.dark-mode .main-sidebar .nav-sidebar .nav-item > .nav-link.active::after {
            box-shadow: 15px -15px 0 15px var(--body-bg) !important;
        }

        /* Fix Parent Menu (has-treeview) Active State in Dark Mode */
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link,
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-is-opening > .nav-link {
            background: transparent !important;
            color: var(--accent) !important;
        }

        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-open > .nav-link:hover,
        html.dark-mode .main-sidebar .nav-sidebar .nav-item.menu-is-opening > .nav-link:hover {
            background: rgba(96, 165, 250, 0.1) !important;
            color: var(--accent) !important;
        }

        /* Fix Child Menu Active State in Dark Mode */
        html.dark-mode .main-sidebar .nav-treeview > .nav-item > .nav-link.active {
            background: var(--body-bg) !important;
            color: var(--accent) !important;
        }

        /* Sidebar Child Menu in Dark Mode */
        html.dark-mode .sidebar .nav-treeview > .nav-item > .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
        }

        html.dark-mode .sidebar .nav-treeview > .nav-item > .nav-link:hover {
            color: var(--accent) !important;
            background: rgba(96, 165, 250, 0.1) !important;
        }

        /* More Specific Rules for Parent Menu with Dropdown (has-treeview) */
        html.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item.menu-open > .nav-link,
        html.dark-mode [class*="sidebar-dark"] .nav-sidebar > .nav-item.menu-open > .nav-link,
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link {
            background-color: transparent !important;
            color: #60a5fa !important;
        }

        html.dark-mode .sidebar-dark-primary .nav-sidebar > .nav-item.menu-is-opening > .nav-link,
        html.dark-mode [class*="sidebar-dark"] .nav-sidebar > .nav-item.menu-is-opening > .nav-link,
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-is-opening > .nav-link {
            background-color: transparent !important;
            color: #60a5fa !important;
        }

        /* Parent menu hover when expanded */
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link:hover {
            background-color: rgba(96, 165, 250, 0.15) !important;
            color: #60a5fa !important;
        }

        /* Icon color for parent menu when expanded */
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link .nav-icon,
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link i {
            color: #60a5fa !important;
        }

        /* Arrow icon for parent menu */
        html.dark-mode .sidebar .nav-sidebar > .nav-item.menu-open > .nav-link .right {
            color: #60a5fa !important;
        }

        /* Override AdminLTE default dark sidebar */
        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
        }

        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link.active {
            background-color: var(--body-bg) !important;
            color: #60a5fa !important;
        }

        html.dark-mode [class*="sidebar-dark"] .nav-treeview > .nav-item > .nav-link:hover {
            background-color: rgba(96, 165, 250, 0.1) !important;
            color: #60a5fa !important;
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
            background-color: var(--primary-blue) !important;
            min-height: 100vh;
        }

        .wrapper {
            background-color: var(--primary-blue) !important;
            min-height: 100vh;
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

        /* Sidebar Styling */
        .main-sidebar {
            background: var(--primary-blue) !important;
            box-shadow: none !important;
            border-right: none !important;
        }

        .brand-link {
            background: transparent !important;
            border-bottom: none !important;
            padding: 24px 20px !important;
        }

        .brand-text {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 1.2rem !important;
            letter-spacing: 0.5px;
        }

        .sidebar {
            padding-left: 12px !important;
            padding-right: 0px !important;
        }

        .sidebar .nav-sidebar {
            width: 100% !important;
        }

        .sidebar .nav-sidebar .nav-item {
            width: 100% !important;
            margin: 4px 0 !important;
            position: relative;
        }

        .sidebar .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            padding: 12px 20px !important;
            border-radius: 30px 0 0 30px !important;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-right: 0 !important;
        }

        .sidebar .nav-sidebar .nav-link i {
            margin-right: 12px !important;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .sidebar .nav-sidebar .nav-link:hover i {
            transform: scale(1.15);
        }

        /* Hover State */
        .main-sidebar .nav-sidebar .nav-item > .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08) !important;
        }

        /* Midone Curved Cutout Active State */
        .main-sidebar .nav-sidebar .nav-item > .nav-link.active {
            background: var(--bg-light) !important;
            color: var(--primary-blue) !important;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .main-sidebar .nav-sidebar .nav-item > .nav-link.active::before {
            content: "";
            position: absolute;
            top: -30px;
            right: 0;
            width: 30px;
            height: 30px;
            background: transparent;
            border-bottom-right-radius: 20px;
            box-shadow: 15px 15px 0 15px var(--bg-light);
            pointer-events: none;
        }

        .main-sidebar .nav-sidebar .nav-item > .nav-link.active::after {
            content: "";
            position: absolute;
            bottom: -30px;
            right: 0;
            width: 30px;
            height: 30px;
            background: transparent;
            border-top-right-radius: 20px;
            box-shadow: 15px -15px 0 15px var(--bg-light);
            pointer-events: none;
        }

        /* When submenu has active item, keep curved cutout effect on parent */
        .main-sidebar .nav-sidebar .nav-item.menu-open:has(.nav-treeview .nav-link.active) > .nav-link.active {
            background: var(--bg-light) !important;
            color: var(--primary-blue) !important;
            font-weight: 600;
            position: relative;
            z-index: 0;
        }

        /* Keep top curve */
        .main-sidebar .nav-sidebar .nav-item.menu-open:has(.nav-treeview .nav-link.active) > .nav-link.active::before {
            content: "";
            position: absolute;
            top: -30px;
            right: 0;
            width: 30px;
            height: 30px;
            background: transparent;
            border-bottom-right-radius: 20px;
            box-shadow: 15px 15px 0 15px var(--bg-light);
            pointer-events: none;
        }

        /* Hide bottom curve when submenu is open */
        .main-sidebar .nav-sidebar .nav-item.menu-open:has(.nav-treeview .nav-link.active) > .nav-link.active::after {
            display: none;
        }

        /* Ensure active state works for submenu items */
        .nav-treeview .nav-link.active {
            background: rgba(255, 255, 255, 0.3) !important;
            color: #ffffff !important;
            font-weight: 600;
            border-left: 3px solid #ffffff !important;
        }

        /* Submenu Styling */
        .nav-sidebar .nav-treeview {
            background: transparent !important;
            padding-left: 15px;
            position: relative;
            z-index: 100;
            margin-top: 0 !important;
        }

        .nav-flat.nav-sidebar>.nav-item .nav-treeview .nav-item>.nav-link,
        .nav-flat.nav-sidebar>.nav-item>.nav-treeview .nav-item>.nav-link {
            border-left: none !important;
        }

        .nav-sidebar .nav-treeview .nav-link {
            border-radius: 30px !important;
            margin-right: 15px !important;
            position: relative;
            z-index: 101;
            background: rgba(255, 255, 255, 0.08) !important;
        }

        .nav-sidebar .nav-treeview .nav-link:hover {
            background: rgba(255, 255, 255, 0.15) !important;
        }

        /* Unified Floating Content Area */
        @media (min-width: 992px) {
            .main-header {
                margin-top: 15px !important;
                margin-left: 270px !important;
                margin-right: 20px !important;
                border-radius: 20px 20px 0 0 !important;
                border-bottom: 1px solid var(--border-color) !important;
            }
            
            .content-wrapper {
                margin-left: 270px !important;
                margin-right: 20px !important;
                margin-bottom: 20px !important;
                border-radius: 0 0 20px 20px !important;
                margin-top: 0px !important;
            }

            .sidebar-collapse .main-header,
            .sidebar-collapse .content-wrapper {
                margin-left: 93px !important;
            }
        }

        @media (max-width: 991.98px) {
            .main-header {
                margin: 10px 10px 0 10px !important;
                border-radius: 15px 15px 0 0 !important;
            }
            
            .content-wrapper {
                margin: 0 10px 10px 10px !important;
                border-radius: 0 0 15px 15px !important;
                margin-top: 0px !important;
            }
        }

        .content-wrapper {
            background: var(--bg-light) !important;
            padding: 24px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
            transition: margin-left .3s ease-in-out;
            min-height: calc(100vh - 100px);
        }

        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding-bottom: 16px !important;
            margin-bottom: 16px !important;
        }

        .user-panel .info a {
            color: #ffffff !important;
            font-weight: 600;
        }

        /* Card Customization */
        .card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01) !important;
            background: #ffffff;
            margin-bottom: 24px;
        }

        .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid var(--border-color) !important;
            color: var(--text-dark) !important;
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
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
            margin-bottom: 15px !important;
            padding: 15px 20px !important;
            color: rgba(255, 255, 255, 0.75) !important;
            transition: margin-left .3s ease-in-out;
            font-size: 0.875rem;
        }

        @media (max-width: 991.98px) {
            .main-footer {
                margin-left: 10px !important;
                margin-right: 10px !important;
            }
        }

        .main-footer a {
            color: #ffffff !important;
            font-weight: 500;
        }

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
            color: #6b7280 !important;
        }
        body.dark-mode .notif-empty-message {
            color: #cbd5e1 !important;
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
           NAVBAR - Dark Theme (matching dashboard)
        ============================================ */
        .main-header.navbar {
            background: #1a1f2e !important;
            border-bottom: 1px solid rgba(255,255,255,0.07) !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3) !important;
            height: 60px !important;
            padding: 0 16px !important;
            z-index: 1040 !important;
        }

        /* Hamburger & Breadcrumb */
        .main-header .navbar-nav .nav-link {
            color: rgba(255,255,255,0.75) !important;
            border-radius: 8px;
            padding: 8px 10px !important;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .main-header .navbar-nav .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255,255,255,0.08) !important;
        }

        /* Breadcrumb text in navbar */
        .breadcrumb {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 0.875rem;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.4) !important;
            content: "/" !important;
        }
        .breadcrumb-item a {
            color: rgba(255,255,255,0.6) !important;
            font-weight: 400;
            transition: color 0.15s ease;
            text-decoration: none;
        }
        .breadcrumb-item a:hover {
            color: #ffffff !important;
        }
        .breadcrumb-item.active {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
        }

        /* ---- Search Bar ---- */
        .navbar-search-wrap {
            width: 260px;
            position: relative;
        }
        .navbar-search-wrap input {
            width: 100%;
            background: rgba(255,255,255,0.07) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            border-radius: 20px !important;
            color: rgba(255,255,255,0.85) !important;
            font-size: 0.85rem;
            padding: 7px 16px 7px 38px !important;
            transition: all 0.2s ease;
            outline: none;
        }
        .navbar-search-wrap input::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .navbar-search-wrap input:focus {
            background: rgba(255,255,255,0.11) !important;
            border-color: rgba(255,255,255,0.25) !important;
            box-shadow: none !important;
        }
        .navbar-search-wrap .search-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
            pointer-events: none;
        }

        /* ---- Bell Notification Button ---- */
        .navbar-icon-btn {
            position: relative;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: rgba(255,255,255,0.75);
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        .navbar-icon-btn:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .navbar-icon-btn .notif-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            background: #ef4444;
            color: #fff;
            border-radius: 50%;
            font-size: 9px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #1a1f2e;
            line-height: 1;
        }

        /* ---- Avatar Profile Button ---- */
        .navbar-avatar-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.2);
            cursor: pointer;
            display: block;
            transition: border-color 0.2s ease;
            flex-shrink: 0;
        }
        .navbar-avatar-btn:hover {
            border-color: rgba(255,255,255,0.5);
        }
        .navbar-avatar-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .navbar-avatar-fallback {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5b5fc7, #7479ed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            border: 2px solid rgba(255,255,255,0.2);
            cursor: pointer;
            transition: border-color 0.2s ease;
        }
        .navbar-avatar-fallback:hover {
            border-color: rgba(255,255,255,0.5);
        }
        /* Right nav item spacing */
        .navbar-right-items {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ---- Teams Notification Dropdown ---- */
        .dropdown-menu-teams {
            background-color: #1e2235 !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
            color: #fff !important;
            padding: 0 !important;
            width: 440px !important;
            max-height: 80vh;
            overflow-y: auto;
            margin-top: 8px !important;
        }
        .dropdown-menu-teams::-webkit-scrollbar { width: 6px; }
        .dropdown-menu-teams::-webkit-scrollbar-track { background: transparent; }
        .dropdown-menu-teams::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.15);
            border-radius: 4px;
        }
        .teams-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 16px 8px;
            position: sticky;
            top: 0;
            background-color: #1e2235;
            z-index: 10;
            border-radius: 12px 12px 0 0;
        }
        .teams-dropdown-header h5 {
            margin: 0;
            font-size: 17px;
            font-weight: 600;
            color: #fff;
        }
        .teams-header-actions {
            display: flex;
            gap: 4px;
        }
        .teams-header-actions button {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.5);
            width: 30px;
            height: 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s;
        }
        .teams-header-actions button:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .teams-time-group {
            display: flex;
            align-items: center;
            padding: 12px 16px 4px;
            color: rgba(255,255,255,0.5);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .teams-time-group::before, .teams-time-group::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .teams-time-group span { padding: 0 10px; }
        .teams-notif-item {
            display: flex;
            padding: 12px 16px;
            text-decoration: none !important;
            color: #fff !important;
            gap: 14px;
            transition: background 0.15s;
        }
        .teams-notif-item:hover { background-color: rgba(255,255,255,0.05); }
        .teams-avatar-wrapper {
            position: relative;
            flex-shrink: 0;
        }
        .teams-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #9370DB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
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
            width: 20px;
            height: 20px;
            background-color: #1e2235;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        .teams-app-icon .icon-bg {
            background-color: #5b5fc7;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .teams-app-icon i { font-size: 9px; color: #fff; }
        .teams-notif-content { flex: 1; min-width: 0; }
        .teams-notif-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 3px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }
        .teams-notif-title span.title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .teams-notif-time {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            font-weight: 400;
            white-space: nowrap;
            margin-left: 10px;
            flex-shrink: 0;
        }
        .teams-notif-desc {
            font-size: 12px;
            color: rgba(255,255,255,0.55);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .teams-notif-item.unread {
            position: relative;
        }
        .teams-notif-item.unread .teams-notif-title .title-text {
            color: #fff;
            font-weight: 700;
        }
        .teams-notif-item.unread .teams-notif-desc {
            color: rgba(255,255,255,0.75);
        }
        .teams-notif-item.unread::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%);
            flex-shrink: 0;
        }
        .teams-notif-item.read .teams-notif-title .title-text {
            color: rgba(255,255,255,0.6);
            font-weight: 400;
        }
        .teams-notif-item.read .teams-notif-desc {
            color: rgba(255,255,255,0.4);
        }

        /* ---- Teams Profile Dropdown ---- */
        .profile-dropdown-teams {
            background-color: #1e2235 !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
            color: #fff !important;
            padding: 0 !important;
            width: 300px !important;
            margin-top: 8px !important;
        }
        .profile-top {
            display: flex;
            justify-content: space-between;
            padding: 14px 16px;
            font-size: 12px;
            color: rgba(255,255,255,0.5);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .profile-top button {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            background: none;
            border: none;
            padding: 0;
            font-size: 12px;
            cursor: pointer;
            transition: color 0.15s;
        }
        .profile-top button:hover { color: #fff; text-decoration: underline; }
        .profile-middle {
            display: flex;
            padding: 18px 16px;
            gap: 14px;
            align-items: flex-start;
        }
        .profile-pic-large {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.15);
            flex-shrink: 0;
        }
        .profile-pic-fallback {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5b5fc7, #7479ed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 22px;
            flex-shrink: 0;
        }
        .profile-info { flex: 1; min-width: 0; }
        .profile-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
        }
        .profile-email {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .profile-link {
            display: block;
            color: #7479ed !important;
            text-decoration: none;
            font-size: 12px;
            margin-bottom: 4px;
            transition: color 0.15s;
        }
        .profile-link:hover { color: #9a9ef5 !important; text-decoration: underline; }
        .profile-bottom {
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .profile-bottom a {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.7) !important;
            text-decoration: none;
            font-size: 13px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: all 0.15s;
        }
        .profile-bottom a:hover {
            background: rgba(255,255,255,0.07);
            color: #fff !important;
        }
        .profile-bottom a i {
            font-size: 16px;
            margin-right: 10px;
            color: rgba(255,255,255,0.5);
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

        /* FORCE OVERRIDE - Notifications Dropdown Theme dengan specificity maksimal */
        html:not(.dark-mode) body .dropdown-menu.dropdown-menu-teams,
        html body:not(.dark-mode) .dropdown-menu.dropdown-menu-teams,
        body:not(.dark-mode) .dropdown-menu.dropdown-menu-teams {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
            color: #1f2937 !important;
        }
        
        html:not(.dark-mode) body .teams-dropdown-header,
        body:not(.dark-mode) .teams-dropdown-header {
            background-color: #ffffff !important;
            border-bottom: 2px solid #3b82f6 !important;
        }
        
        html:not(.dark-mode) body .teams-dropdown-header h5,
        body:not(.dark-mode) .teams-dropdown-header h5 {
            color: #111827 !important;
        }
        
        html:not(.dark-mode) body .teams-header-actions button,
        body:not(.dark-mode) .teams-header-actions button {
            color: #6b7280 !important;
        }
        
        html:not(.dark-mode) body .teams-notif-item,
        body:not(.dark-mode) .teams-notif-item {
            color: #1f2937 !important;
        }
        
        html:not(.dark-mode) body .notif-empty-message,
        body:not(.dark-mode) .notif-empty-message {
            color: #6b7280 !important;
        }
        
        html:not(.dark-mode) body .teams-time-group,
        body:not(.dark-mode) .teams-time-group {
            color: #6b7280 !important;
        }

        /* FORCE OVERRIDE - Dark Mode */
        html.dark-mode body .dropdown-menu.dropdown-menu-teams,
        body.dark-mode .dropdown-menu.dropdown-menu-teams {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        
        html.dark-mode body .teams-dropdown-header,
        body.dark-mode .teams-dropdown-header {
            background-color: #1e293b !important;
            border-bottom: 2px solid #3b82f6 !important;
        }
        
        html.dark-mode body .teams-dropdown-header h5,
        body.dark-mode .teams-dropdown-header h5 {
            color: #f1f5f9 !important;
        }
        
        html.dark-mode body .teams-header-actions button,
        body.dark-mode .teams-header-actions button {
            color: #cbd5e1 !important;
        }
        
        html.dark-mode body .teams-notif-item,
        body.dark-mode .teams-notif-item {
            color: #f1f5f9 !important;
        }
        
        html.dark-mode body .notif-empty-message,
        body.dark-mode .notif-empty-message {
            color: #cbd5e1 !important;
        }
        
        html.dark-mode body .teams-time-group,
        body.dark-mode .teams-time-group {
            color: #cbd5e1 !important;
        }

        /* Jadwal Sidang - Calendar & Card Dark Mode Styling */
        /* STRATEGI: Warna pastel tetap tampil, tapi text selalu kontras */
        
        /* Calendar - Keep colorful background, ensure text visibility */
        body.dark-mode .table-bordered {
            border-color: #475569 !important;
        }
        
        /* Calendar header row - keep pastel colors visible */
        body.dark-mode .table thead th {
            /* Warna background tetap dari inline style, tidak di-override */
            color: #0f172a !important; /* Text gelap agar kontras dengan pastel */
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
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
                    <span class="notif-badge" id="notifBadge">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-teams" id="notifDropdown">
                    <div class="teams-dropdown-header">
                        <h5>Notifications</h5>
                        <div class="teams-header-actions">
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
        <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center" style="gap: 10px; padding: 1.5rem 1.25rem !important;">
            <img src="{{ asset('images/itb-logo.svg') }}"
                 alt="ITB Logo" style="width: 38px; height: 38px; background: #fff; border-radius: 50%; padding: 6px; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <span class="brand-text" style="font-size: 1.15rem; line-height: 1.2; font-weight: 700; color: #fff; letter-spacing: 0.5px;">SI SIDANG<br><span style="font-size: 0.75rem; font-weight: 500; opacity: 0.8;">FTTM ITB</span><br><span style="font-size: 0.65rem; font-weight: 400; opacity: 0.6; text-transform: capitalize;">Role : {{ session('auth_user.role') }}</span></span>
        </a>

        <div class="sidebar">
            <nav class="mt-4">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard Home</p>
                        </a>
                    </li>

                    @if(session('auth_user.role') === 'Admin')
                    <!-- ADMIN NAVIGATION -->
                    <li class="nav-item has-treeview {{ request()->routeIs('master.persyaratan*', 'master.penilaian*', 'master.prodi*', 'master.fakultas*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('master.persyaratan*', 'master.penilaian*', 'master.prodi*', 'master.fakultas*') ? 'active' : '' }}">
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
                            <p>Report</p>
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
                            <p>Report</p>
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

                    @if(in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']))
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
                    <!-- KPPS NAVIGATION - Approval Ajuan Sidang -->
                    <li class="nav-item has-treeview {{ request()->routeIs('sidang.approve-ajuan*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('sidang.approve-ajuan*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>Approve Ajuan Sidang <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sidang.approve-ajuan.index', 'S1') }}" class="nav-link {{ request()->routeIs('sidang.approve-ajuan.index') && request()->route('strata') == 'S1' ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S1</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.approve-ajuan.index', 'S2') }}" class="nav-link {{ request()->routeIs('sidang.approve-ajuan.index') && request()->route('strata') == 'S2' ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>S2</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.approve-ajuan.index', 'S3') }}" class="nav-link {{ request()->routeIs('sidang.approve-ajuan.index') && request()->route('strata') == 'S3' ? 'active' : '' }}">
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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/dist/overlayscrollbars.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
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

    function toggleDarkMode() {
        var body = document.body;
        var html = document.documentElement;
        var icon = document.getElementById('darkModeIcon');
        var isDark = html.classList.toggle('dark-mode');
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', isDark ? 'true' : 'false');
        icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        
        // Force update notifications dropdown styling
        updateNotificationDropdownTheme(isDark);
    }

    (function initDarkMode() {
        var body = document.body;
        var html = document.documentElement;
        var icon = document.getElementById('darkModeIcon');
        var saved = localStorage.getItem('darkMode');
        var isDark;
        
        // Jika belum pernah di-set manual, deteksi dari system theme
        if (saved === null) {
            // Deteksi system theme dari browser/OS
            isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            console.log('Auto-detect system theme:', isDark ? 'dark' : 'light');
        } else {
            // Gunakan preference yang sudah disimpan
            isDark = saved === 'true';
        }
        
        if (isDark) {
            html.classList.add('dark-mode');
            body.classList.add('dark-mode');
            if (icon) icon.className = 'fas fa-sun';
        } else {
            html.classList.remove('dark-mode');
            body.classList.remove('dark-mode');
            if (icon) icon.className = 'fas fa-moon';
        }
        
        // Force update notifications dropdown styling on load
        setTimeout(function() {
            updateNotificationDropdownTheme(isDark);
        }, 100);
        
        // Listen untuk perubahan system theme (real-time)
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                // Hanya apply auto jika user belum set manual preference
                if (localStorage.getItem('darkMode') === null) {
                    var newIsDark = e.matches;
                    console.log('System theme changed:', newIsDark ? 'dark' : 'light');
                    
                    if (newIsDark) {
                        html.classList.add('dark-mode');
                        body.classList.add('dark-mode');
                        if (icon) icon.className = 'fas fa-sun';
                    } else {
                        html.classList.remove('dark-mode');
                        body.classList.remove('dark-mode');
                        if (icon) icon.className = 'fas fa-moon';
                    }
                    
                    updateNotificationDropdownTheme(newIsDark);
                }
            });
        }
    })();

    function updateNotificationDropdownTheme(isDark) {
        var dropdown = document.getElementById('notifDropdown');
        var header = document.querySelector('.teams-dropdown-header');
        
        if (dropdown) {
            if (isDark) {
                dropdown.style.backgroundColor = '#1e293b';
                dropdown.style.borderColor = '#334155';
                dropdown.style.color = '#f1f5f9';
            } else {
                dropdown.style.backgroundColor = '#ffffff';
                dropdown.style.borderColor = '#e5e7eb';
                dropdown.style.color = '#1f2937';
            }
        }
        
        if (header) {
            if (isDark) {
                header.style.backgroundColor = '#1e293b';
                header.style.borderBottomColor = '#3b82f6';
            } else {
                header.style.backgroundColor = '#ffffff';
                header.style.borderBottomColor = '#3b82f6';
            }
            
            var h5 = header.querySelector('h5');
            if (h5) {
                h5.style.color = isDark ? '#f1f5f9' : '#111827';
            }
            
            var buttons = header.querySelectorAll('button');
            buttons.forEach(function(btn) {
                btn.style.color = isDark ? '#cbd5e1' : '#6b7280';
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

            function timeAgo(dateStr) {
                var d = new Date(dateStr.replace(' ', 'T') + 'Z');
                var now = new Date();
                var diff = Math.floor((now - d) / 1000);
                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
                if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
                if (diff < 172800) return 'Yesterday';
                return Math.floor(diff / 86400) + 'd ago';
            }

            function groupLabel(d) {
                var date = new Date(d.created_at.replace(' ', 'T') + 'Z');
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
                    var color = colors[n.id % colors.length];
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

@stack('scripts')
</body>
</html>
