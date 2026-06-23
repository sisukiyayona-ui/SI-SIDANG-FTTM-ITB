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
        }

        /* Global Font & Body */
        body, .wrapper, .main-sidebar, .main-header, .content-wrapper, .main-footer {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        body {
            background-color: var(--primary-blue) !important;
            overflow-x: hidden;
        }

        .wrapper {
            background-color: var(--primary-blue) !important;
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

        /* Submenu Styling */
        .nav-sidebar .nav-treeview {
            background: transparent !important;
            padding-left: 15px;
        }

        .nav-sidebar .nav-treeview .nav-link {
            border-radius: 30px !important;
            margin-right: 15px !important;
        }

        .nav-sidebar .nav-treeview .nav-link.active {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
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
        .dropdown-menu-teams {
            background-color: #242424 !important;
            border: 1px solid #3b3b3b !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5) !important;
            color: #fff !important;
            padding: 0 !important;
            width: 440px !important;
            max-height: 80vh;
            overflow-y: auto;
        }
        .dropdown-menu-teams::-webkit-scrollbar {
            width: 8px;
        }
        .dropdown-menu-teams::-webkit-scrollbar-thumb {
            background-color: #4a4a4a;
            border-radius: 4px;
        }
        .teams-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 16px 8px;
            position: sticky;
            top: 0;
            background-color: #242424;
            z-index: 10;
        }
        .teams-dropdown-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }
        .teams-header-actions {
            display: flex;
            gap: 8px;
        }
        .teams-header-actions button {
            background: transparent;
            border: none;
            color: #c8c8c8;
            padding: 6px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .teams-header-actions button:hover {
            background: #3b3b3b;
            color: #fff;
        }
        .teams-nav-tabs {
            display: flex;
            padding: 0 16px;
            border-bottom: 1px solid #3b3b3b;
            margin-bottom: 8px;
        }
        .teams-nav-tabs .tab {
            padding: 8px 0;
            margin-right: 20px;
            color: #c8c8c8;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .teams-nav-tabs .tab.active {
            color: #fff;
            border-bottom: 2px solid #5b5fc7;
        }

        .teams-time-group {
            display: flex;
            align-items: center;
            padding: 12px 16px 4px;
            color: #c8c8c8;
            font-size: 12px;
            font-weight: 600;
        }
        .teams-time-group::before, .teams-time-group::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #3b3b3b;
        }
        .teams-time-group span {
            padding: 0 10px;
        }

        .teams-notif-item {
            display: flex;
            padding: 12px 16px;
            text-decoration: none !important;
            color: #fff !important;
            gap: 16px;
            transition: background 0.2s;
            margin: 0;
        }
        .teams-notif-item:hover {
            background-color: #333333;
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
            background-color: #242424;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
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
            color: #ffffff;
        }
        .teams-notif-title span.title-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .teams-notif-time {
            font-size: 12px;
            color: #c8c8c8;
            font-weight: 400;
            white-space: nowrap;
            margin-left: 10px;
            flex-shrink: 0;
        }
        .teams-notif-desc {
            font-size: 13px;
            color: #c8c8c8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

            {{-- Bell / Notifications --}}
            <li class="nav-item dropdown d-flex align-items-center">
                <a class="nav-link navbar-icon-btn" data-toggle="dropdown" href="#" role="button">
                    <i class="far fa-bell" style="font-size:1.05rem;"></i>
                    <span class="notif-badge">4</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-teams">
                    <div class="teams-dropdown-header">
                        <h5>Notifications</h5>
                        <div class="teams-header-actions">
                            <button title="Filter"><i class="fas fa-filter"></i></button>
                            <button title="Mark all read"><i class="fas fa-check-double"></i></button>
                            <button title="Settings"><i class="fas fa-cog"></i></button>
                            <button title="Close" onclick="$(this).closest('.dropdown-menu').parent().find('.nav-link').dropdown('toggle'); return false;"><i class="fas fa-times"></i></button>
                        </div>
                    </div>

                    <div class="teams-time-group"><span>Last seen</span></div>
                    <a href="#" class="teams-notif-item">
                        <div class="teams-avatar-wrapper">
                            <div class="teams-avatar" style="background:#9B870C;">A</div>
                            <div class="teams-app-icon"><div class="icon-bg"><i class="fas fa-comment-alt"></i></div></div>
                        </div>
                        <div class="teams-notif-content">
                            <div class="teams-notif-title">
                                <span class="title-text">Re: TAMBAH VENDOR</span>
                                <span class="teams-notif-time">4 hrs ago</span>
                            </div>
                            <div class="teams-notif-desc">Ade mentioned you</div>
                        </div>
                    </a>

                    <div class="teams-time-group"><span>Yesterday</span></div>
                    <a href="#" class="teams-notif-item">
                        <div class="teams-avatar-wrapper">
                            <div class="teams-avatar" style="background:#8A2BE2;">T</div>
                            <div class="teams-app-icon"><div class="icon-bg"><i class="fas fa-comment-alt"></i></div></div>
                        </div>
                        <div class="teams-notif-content">
                            <div class="teams-notif-title">
                                <span class="title-text">Re: DELETE MATERIA</span>
                                <span class="teams-notif-time">22 hrs ago</span>
                            </div>
                            <div class="teams-notif-desc">Tutus mentioned you</div>
                        </div>
                    </a>
                    <a href="#" class="teams-notif-item">
                        <div class="teams-avatar-wrapper">
                            <div class="teams-avatar" style="background:#9B870C;">A</div>
                            <div class="teams-app-icon"><div class="icon-bg"><i class="fas fa-comment-alt"></i></div></div>
                        </div>
                        <div class="teams-notif-content">
                            <div class="teams-notif-title">
                                <span class="title-text">Re: TAMBAH VENDO</span>
                                <span class="teams-notif-time">23 hrs ago</span>
                            </div>
                            <div class="teams-notif-desc">Ade mentioned you</div>
                        </div>
                    </a>

                    <div class="teams-time-group"><span>Last week</span></div>
                    <a href="#" class="teams-notif-item">
                        <div class="teams-avatar-wrapper">
                            <div class="teams-avatar" style="background:#E9967A;">KW</div>
                            <div class="teams-app-icon"><div class="icon-bg"><i class="fas fa-comment-alt"></i></div></div>
                        </div>
                        <div class="teams-notif-content">
                            <div class="teams-notif-title">
                                <span class="title-text">Re: Daftar Supplier</span>
                                <span class="teams-notif-time">6 days ago</span>
                            </div>
                            <div class="teams-notif-desc">Katarina mentioned you</div>
                        </div>
                    </a>
                </div>
            </li>

            {{-- Avatar / Profile --}}
            <li class="nav-item dropdown d-flex align-items-center ml-1">
                <a class="nav-link p-0" data-toggle="dropdown" href="#" role="button" style="display:flex;align-items:center;">
                    @php
                        $avatarUrl = session('auth_user.avatar');
                        $userName  = session('auth_user.name', 'User');
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
                            <div class="profile-name">{{ session('auth_user.name', 'Nama User') }}</div>
                            <div class="profile-email">{{ session('auth_user.email', 'email@example.com') }}</div>
                            <a href="{{ route('profile') }}" class="profile-link">View account</a>
                            <a href="#" class="profile-link">Open another mailbox</a>
                        </div>
                    </div>
                    <div class="profile-bottom">
                        <a href="#">
                            <i class="far fa-user-circle"></i> Sign in with a different account
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary">
        <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center" style="gap: 10px; padding: 1.5rem 1.25rem !important;">
            <img src="{{ asset('images/itb-logo.svg') }}"
                 alt="ITB Logo" style="width: 38px; height: 38px; background: #fff; border-radius: 50%; padding: 6px; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <span class="brand-text" style="font-size: 1.15rem; line-height: 1.2; font-weight: 700; color: #fff; letter-spacing: 0.5px;">SI SIDANG<br><span style="font-size: 0.75rem; font-weight: 500; opacity: 0.8;">FTTM ITB</span></span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ session('auth_user.avatar') }}" class="img-circle elevation-2" alt="User">
                </div>
                <div class="info">
                    <a href="{{ route('profile') }}" class="d-block">{{ session('auth_user.name') }}</a>
                    <small class="text-white-50">{{ session('auth_user.role') }}</small>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    @if(in_array(session('auth_user.role'), ['Admin', 'TU Prodi']))
                    <li class="nav-item has-treeview {{ request()->routeIs('master.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('master.*') ? 'active' : '' }}">
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
                            @if(session('auth_user.role') === 'Admin')
                            <li class="nav-item">
                                <a href="{{ route('master.prodi.index') }}" class="nav-link {{ request()->routeIs('master.prodi*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-university"></i>
                                    <p>Prodi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.user.index') }}" class="nav-link {{ request()->routeIs('master.user*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>User</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('approve.user') }}" class="nav-link {{ request()->routeIs('approve.user*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-check"></i>
                            <p>Approve User</p>
                        </a>
                    </li>
                    @endif



                    <li class="nav-item has-treeview {{ request()->routeIs('sidang.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('sidang.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gavel"></i>
                            <p>Sidang <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sidang.ujian-kualifikasi') }}" class="nav-link {{ request()->routeIs('sidang.ujian-kualifikasi') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-signature"></i>
                                    <p>Ujian Kualifikasi</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.sidang-proposal') }}" class="nav-link {{ request()->routeIs('sidang.sidang-proposal') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-file-export"></i>
                                    <p>Sidang Proposal</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.seminar-kemajuan-i') }}" class="nav-link {{ request()->routeIs('sidang.seminar-kemajuan-i') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Seminar Kemajuan I</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.seminar-kemajuan-ii') }}" class="nav-link {{ request()->routeIs('sidang.seminar-kemajuan-ii') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Seminar Kemajuan II</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.seminar-kemajuan-iii') }}" class="nav-link {{ request()->routeIs('sidang.seminar-kemajuan-iii') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Seminar Kemajuan III</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.seminar-kemajuan-iv') }}" class="nav-link {{ request()->routeIs('sidang.seminar-kemajuan-iv') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-chart-line"></i>
                                    <p>Seminar Kemajuan IV</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('sidang.sidang-akhir') }}" class="nav-link {{ request()->routeIs('sidang.sidang-akhir') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-graduation-cap"></i>
                                    <p>Sidang Akhir</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-report"></i>
                            <p>Report</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>Profile</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link" style="color: rgba(255,255,255,.8); width: 100%; text-align: left;">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </button>
                        </form>
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

<script>
    function showToast(type, message) {
        var id = type === 'success' ? '#toastSuccess' : '#toastError';
        var msgId = type === 'success' ? '#toastSuccessMsg' : '#toastErrorMsg';
        $(msgId).text(message);
        $(id).fadeIn(200);
        
        // Auto hide after 4 seconds
        setTimeout(function() {
            $(id).fadeOut(300);
        }, 4000);
    }

    function hideToast(type) {
        var id = type === 'success' ? '#toastSuccess' : '#toastError';
        $(id).fadeOut(300);
    }

    $(function () {
        @if(session('success'))
            showToast('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            showToast('error', '{{ session('error') }}');
        @endif
    });
</script>

@stack('scripts')
</body>
</html>
