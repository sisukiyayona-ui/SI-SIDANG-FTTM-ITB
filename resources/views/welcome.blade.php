<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Penjadwalan Sidang - FTTM ITB</title>
    <meta name="description" content="Jadwal Sidang Mahasiswa - FTTM ITB">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">
    <script nonce="{{ request()->attributes->get('csp_nonce') ?? '' }}">
        (function() {
            var saved = localStorage.getItem('darkMode');
            if (saved === 'true') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:     #f8fafc;
            --bg2:    #ffffff;
            --bg3:    #f1f5f9;
            --border: #e2e8f0;
            --text:   #1e293b;
            --muted:  #64748b;
            --accent: #2563eb;
            --indigo: #4f46e5;
            --green:  #16a34a;
            --yellow: #ca8a04;
            --purple: #9333ea;
            --pink:   #db2777;
            --cyan:   #0891b2;
            --orange: #ea580c;
            --link:   #2563eb;
        }

        html.dark-mode {
            --bg:     #0f172a;
            --bg2:    #1e293b;
            --bg3:    #334155;
            --border: #334155;
            --text:   #e2e8f0;
            --muted:  #94a3b8;
            --accent: #60a5fa;
            --indigo: #818cf8;
            --green:  #4ade80;
            --yellow: #facc15;
            --purple: #c084fc;
            --pink:   #f472b6;
            --cyan:   #22d3ee;
            --orange: #fb923c;
            --link:   #60a5fa;
        }
        html.dark-mode body {
            background: var(--bg) !important;
            color: var(--text) !important;
        }
        html.dark-mode .top-nav {
            background: var(--bg2) !important;
            border-bottom-color: var(--border) !important;
        }
        html.dark-mode .nav-brand-name {
            color: var(--text) !important;
        }
        html.dark-mode .nav-brand img {
            background: var(--accent) !important;
        }
        html.dark-mode .main-card {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
        }
        html.dark-mode .card-topbar {
            background: var(--bg3) !important;
            border-bottom-color: var(--border) !important;
        }
        html.dark-mode .card-title {
            color: var(--text) !important;
        }
        html.dark-mode thead tr.thead-labels {
            background: var(--bg3) !important;
        }
        html.dark-mode thead tr.thead-filters {
            background: #1e293b !important;
        }
        html.dark-mode tbody tr {
            border-bottom-color: var(--border) !important;
        }
        html.dark-mode tbody tr:hover {
            background: #1e293b !important;
        }
        html.dark-mode th {
            color: var(--muted) !important;
        }
        html.dark-mode .td-name {
            color: var(--text) !important;
        }
        html.dark-mode .search-wrap input {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        html.dark-mode .search-wrap input::placeholder {
            color: var(--muted) !important;
        }
        html.dark-mode .search-wrap input:focus {
            border-color: var(--accent) !important;
        }
        html.dark-mode .col-filter-input {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        html.dark-mode .col-filter-input::placeholder {
            color: var(--muted) !important;
        }
        html.dark-mode .col-filter-input:focus {
            border-color: var(--accent) !important;
            background: rgba(96,165,250,0.06) !important;
        }
        html.dark-mode .col-filter-select {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        html.dark-mode .col-filter-select:focus {
            border-color: var(--accent) !important;
        }
        html.dark-mode .col-filter-select option {
            background: var(--bg2) !important;
            color: var(--text) !important;
        }
        html.dark-mode .filter-select {
            background: var(--bg2) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }
        html.dark-mode .col-filter-wrap .fi-icon {
            color: var(--muted) !important;
        }
        html.dark-mode .footer {
            color: var(--muted) !important;
            border-top-color: var(--border) !important;
        }
        html.dark-mode .cta-banner {
            border-color: rgba(96,165,250,0.25) !important;
        }
        html.dark-mode .btn-ghost {
            color: var(--muted) !important;
        }
        html.dark-mode .btn-ghost:hover {
            color: var(--text) !important;
            background: rgba(255,255,255,0.06) !important;
        }
        html.dark-mode .td-prodi {
            color: var(--muted) !important;
        }
        html.dark-mode .td-no {
            color: var(--muted) !important;
        }
        html.dark-mode .col-filter-input.has-value,
        html.dark-mode .col-filter-select.has-value {
            border-color: var(--accent) !important;
            background: rgba(96,165,250,0.08) !important;
        }
        html.dark-mode .col-filter-wrap .fi-icon.has-value {
            color: var(--accent) !important;
        }
        html, body { height: 100%; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        .top-nav {
            background: var(--bg2);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 200;
            flex-shrink: 0;
        }
        .nav-inner {
            max-width: 1400px; margin: 0 auto;
            padding: 0 24px; height: 54px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .nav-left { display: flex; align-items: center; gap: 14px; }
        .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-brand img { width: 34px; height: 34px; background:#1e3a8a; border-radius:50%; padding:4px; flex-shrink: 0; }
        .nav-brand-text { display: flex; flex-direction: column; gap: 1px; }
        .nav-brand-name { font-size: 0.95rem; font-weight: 800; color: var(--text); letter-spacing: 0.3px; line-height: 1.1; }
        .nav-brand-sub  { font-size: 0.68rem; font-weight: 500; color: var(--muted); letter-spacing: 0.2px; line-height: 1.1; }
        .nav-divider { width: 1px; height: 18px; background: var(--border); }
        .nav-subtitle { font-size: 0.72rem; color: var(--muted); white-space: nowrap; }
        .nav-right { display: flex; align-items: center; gap: 8px; }
        .btn-nav {
            padding: 6px 16px; border-radius: 7px; font-size: 0.82rem; font-weight: 500;
            cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
            gap: 6px; border: 1px solid transparent; transition: all 0.15s;
        }
        .btn-ghost { color: var(--muted); background: transparent; }
        .btn-ghost:hover { color: var(--text); background: var(--bg3); }
        .btn-primary { color: #fff; background: var(--accent); border-color: var(--accent); font-weight: 600; }
        .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }

        /* ── PAGE WRAP ── */
        .page-wrap { max-width: 1400px; width: 100%; margin: 0 auto; padding: 24px 24px 56px; display: flex; flex-direction: column; gap: 24px; flex: 1; }

        /* ── CTA BANNER ── */
        .cta-banner {
            background: linear-gradient(135deg, #1e3a5f, #1a1f6e);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 12px; padding: 18px 24px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        }
        .cta-text h3 { font-size: 0.92rem; font-weight: 700; color: #fff; margin-bottom: 3px; }
        .cta-text p { font-size: 0.78rem; color: rgba(255,255,255,0.6); }
        .cta-btn {
            padding: 9px 22px; border-radius: 9px; background: var(--accent);
            color: #fff; font-weight: 600; font-size: 0.82rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; transition: all 0.15s;
        }
        .cta-btn:hover { background: #2563eb; transform: translateY(-1px); }
        .cta-btn-secondary {
            padding: 9px 22px; border-radius: 9px; background: transparent;
            color: #fff; font-weight: 600; font-size: 0.82rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0; transition: all 0.15s;
            border: 1px solid var(--accent);
        }
        .cta-btn-secondary:hover { background: rgba(59,130,246,0.1); }

        /* ── SINGLE BIG CARD ── */
        .main-card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        /* Card top bar */
        .card-topbar {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            background: var(--bg3);
        }
        .card-title-row { display: flex; align-items: center; gap: 10px; }
        .card-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: rgba(37,99,235,0.1); color: var(--accent);
            display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
        }
        .card-title { font-size: 1rem; font-weight: 700; color: var(--text); }
        .card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 1px; }
        .card-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 0.75rem; pointer-events: none; }
        .search-wrap input {
            background: var(--bg2); border: 1px solid var(--border); border-radius: 8px;
            color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.78rem;
            padding: 7px 12px 7px 30px; width: 200px; outline: none; transition: border-color 0.15s;
        }
        .search-wrap input::placeholder { color: #94a3b8; }
        .search-wrap input:focus { border-color: var(--accent); }

        .filter-select {
            background: var(--bg2); border: 1px solid var(--border); border-radius: 8px;
            color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.78rem;
            padding: 7px 28px 7px 10px; appearance: none; outline: none; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 9px center;
            transition: border-color 0.15s;
        }
        .filter-select:focus { border-color: var(--accent); }

        /* Table */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        thead tr.thead-labels { background: var(--bg3); border-bottom: 1px solid var(--border); }
        thead tr.thead-filters { background: #f1f5f9; border-bottom: 2px solid var(--border); }
        th {
            padding: 11px 14px;
            font-size: 0.7rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.7px;
            white-space: nowrap; text-align: left;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        td { padding: 11px 14px; font-size: 0.82rem; color: var(--text); vertical-align: middle; }
        .td-no { color: var(--muted); font-size: 0.72rem; text-align: center; width: 42px; padding: 11px 8px; }
        .td-name { font-weight: 600; color: var(--text); font-size: 0.83rem; }
        .nim-badge {
            display: inline-block;
            background: rgba(79,70,229,0.1); color: var(--indigo);
            border-radius: 5px; padding: 2px 7px; font-size: 0.68rem; font-weight: 700;
            margin-top: 3px;
        }
        .td-prodi { color: var(--muted); font-size: 0.78rem; }
        .td-extra { color: var(--text); font-size: 0.79rem; max-width: 200px; line-height: 1.4; }
        .td-date { color: var(--accent); font-weight: 600; font-size: 0.79rem; white-space: nowrap; }
        .td-room { font-size: 0.79rem; white-space: nowrap; }
        .bab-chip {
            background: rgba(202,138,4,0.1); color: var(--yellow);
            border-radius: 5px; padding: 3px 9px; font-size: 0.7rem; font-weight: 700;
        }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 0.68rem; font-weight: 700;
            white-space: nowrap;
        }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .badge-terjadwal { background: rgba(37,99,235,0.08); color: var(--accent); }
        .badge-terjadwal::before { background: var(--accent); }
        .badge-selesai { background: rgba(22,163,74,0.08); color: var(--green); }
        .badge-selesai::before { background: var(--green); }

        /* Jenis sidang tag (right side) */
        .jenis-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 600;
            white-space: nowrap;
        }
        .j-uk      { background: rgba(37,99,235,0.08);  color: var(--accent); border: 1px solid rgba(37,99,235,0.2); }
        .j-sp      { background: rgba(79,70,229,0.08);  color: var(--indigo); border: 1px solid rgba(79,70,229,0.2); }
        .j-sk1     { background: rgba(22,163,74,0.08);  color: var(--green);  border: 1px solid rgba(22,163,74,0.2); }
        .j-sk2     { background: rgba(202,138,4,0.08);  color: var(--yellow); border: 1px solid rgba(202,138,4,0.2); }
        .j-sk3     { background: rgba(147,51,234,0.08); color: var(--purple); border: 1px solid rgba(147,51,234,0.2); }
        .j-sk4     { background: rgba(219,39,119,0.08); color: var(--pink);   border: 1px solid rgba(219,39,119,0.2); }
        .j-sa      { background: rgba(8,145,178,0.08);  color: var(--cyan);   border: 1px solid rgba(8,145,178,0.2); }

        /* ── Inline column filter row ── */
        .col-filter-input {
            width: 100%;
            min-width: 70px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            padding: 4px 8px 4px 26px;
            outline: none;
            transition: border-color 0.15s, background 0.15s;
        }
        .col-filter-input::placeholder { color: #94a3b8; }
        .col-filter-input:focus {
            border-color: var(--accent);
            background: rgba(37,99,235,0.04);
        }
        .col-filter-select {
            width: 100%;
            min-width: 80px;
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            padding: 4px 6px;
            outline: none;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 6px center;
            padding-right: 20px;
        }
        .col-filter-select option { background: #fff; color: var(--text); }
        .col-filter-select:focus { border-color: var(--accent); background-color: rgba(37,99,235,0.04); }
        .col-filter-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .col-filter-wrap .fi-icon {
            position: absolute;
            left: 7px;
            color: var(--muted);
            font-size: 0.62rem;
            pointer-events: none;
            z-index: 1;
        }
        th.th-filter { padding: 6px 8px; vertical-align: middle; }
        .col-filter-input.has-value,
        .col-filter-select.has-value {
            border-color: var(--accent);
            background-color: rgba(37,99,235,0.06);
            color: var(--text);
        }
        .col-filter-wrap .fi-icon.has-value { color: var(--accent); }
        .clear-filters-btn {
            display: none;
            align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px;
            background: rgba(220,38,38,0.08); border: 1px solid rgba(220,38,38,0.2);
            color: #dc2626; font-size: 0.72rem; font-weight: 600;
            cursor: pointer; transition: all 0.15s;
            white-space: nowrap;
        }
        .clear-filters-btn:hover { background: rgba(220,38,38,0.15); }
        .clear-filters-btn.visible { display: inline-flex; }

        /* Empty row */
        .empty-row td {
            text-align: center; padding: 24px;
            color: var(--muted); font-size: 0.8rem;
        }

        /* Footer */
        .footer {
            text-align: center; padding: 20px;
            color: var(--muted); font-size: 0.72rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
            margin-top: auto;
        }

        @media (max-width: 640px) {
            .nav-subtitle, .nav-divider { display: none; }
            .page-wrap { padding: 14px 12px 40px; }
            .search-wrap input { width: 140px; }
        }
    </style>
</head>
<body>

    <!-- ══ NAVBAR ══ -->
    <nav class="top-nav">
        <div class="nav-inner">
            <div class="nav-left">
                <a href="{{ url('/') }}" class="nav-brand">
                    <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB Logo">
                    <div class="nav-brand-text">
                        <span class="nav-brand-name">SI SIDANG</span>
                        <span class="nav-brand-sub">FTTM ITB</span>
                    </div>
                </a>
            </div>
            <div class="nav-right">
                <button class="btn-nav btn-ghost" id="darkModeToggle" onclick="toggleDarkMode()" title="Toggle Dark Mode" style="border:none; font-size:1rem; padding:6px 10px;">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>
                <a href="{{ url('/') }}" class="btn-nav btn-ghost"><i class="fas fa-home"></i> Beranda</a>
                <a href="{{ route('login') }}" class="btn-nav btn-primary"><i class="fas fa-sign-in-alt"></i> Masuk</a>
            </div>
        </div>
    </nav>

    <!-- ══ PAGE ══ -->
    <div class="page-wrap">

        <!-- CTA Banner -->
        <div class="cta-banner">
            <div class="cta-text">
                <h3><i class="fas fa-calendar-alt"></i> &nbsp;Jadwal Sidang Mahasiswa – FTTM ITB</h3>
                <p>Halaman publik. Login untuk mengajukan sidang atau mengelola jadwal Anda.</p>
            </div>

        </div>

        <!-- ══ ONE BIG CARD ══ -->
        <div class="main-card">

            <!-- Top bar with search + filter -->
            <div class="card-topbar">
                <div class="card-title-row">
                    <div class="card-icon"><i class="fas fa-table"></i></div>
                    <div>
                        <div class="card-title">Daftar Jadwal Sidang</div>
                        <div class="card-subtitle">Semua jenis sidang mahasiswa FTTM ITB</div>
                    </div>
                </div>
                <div class="card-controls">
                    <div class="search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="globalSearch" placeholder="Cari semua kolom..." oninput="doFilter()">
                    </div>
                    <button class="clear-filters-btn" id="clearFiltersBtn" onclick="clearAllFilters()">
                        <i class="fas fa-times"></i> Reset Filter
                    </button>
                </div>
            </div>

            <!-- Single table -->
            <div class="table-scroll">
                <table id="mainTable">
                    <thead>
                        <tr class="thead-labels">
                            <th class="td-no">#</th>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Prodi</th>
                            <th>Judul / Bab</th>
                            <th>Tanggal</th>
                            <th>Ruang</th>
                            <th>Status</th>
                            <th>Jenis Sidang</th>
                        </tr>
                        <tr class="thead-filters">
                            <th class="th-filter td-no"></th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-mhs"></i>
                                    <input type="text" class="col-filter-input" id="fMahasiswa" placeholder="Cari..." oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-nim"></i>
                                    <input type="text" class="col-filter-input" id="fNIM" placeholder="Cari..." oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-prodi"></i>
                                    <input type="text" class="col-filter-input" id="fProdi" placeholder="Cari..." oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-judul"></i>
                                    <input type="text" class="col-filter-input" id="fJudul" placeholder="Cari..." oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-tgl"></i>
                                    <input type="text" class="col-filter-input" id="fTanggal" placeholder="YYYY-MM-DD" oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <div class="col-filter-wrap">
                                    <i class="fas fa-filter fi-icon" id="fi-ruang"></i>
                                    <input type="text" class="col-filter-input" id="fRuang" placeholder="Cari..." oninput="doFilter()">
                                </div>
                            </th>
                            <th class="th-filter">
                                <select class="col-filter-select" id="fStatus" onchange="doFilter()">
                                    <option value="">Semua</option>
                                    <option value="Terjadwal">Terjadwal</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </th>
                            <th class="th-filter">
                                <select class="col-filter-select" id="fJenis" onchange="doFilter()">
                                    <option value="">Semua</option>
                                    @foreach($jenisSidangList as $jenis)
                                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                                    @endforeach
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                            $jenisColors = [
                                'tahap I'  => ['class' => 'j-uk',  'icon' => 'fas fa-file-signature'],
                                'tahap II' => ['class' => 'j-sp',  'icon' => 'fas fa-file-export'],
                                'SK I'     => ['class' => 'j-sk1', 'icon' => 'fas fa-chart-line'],
                                'SK II'    => ['class' => 'j-sk2', 'icon' => 'fas fa-chart-line'],
                                'SK III'   => ['class' => 'j-sk3', 'icon' => 'fas fa-chart-line'],
                                'SK IV'    => ['class' => 'j-sk4', 'icon' => 'fas fa-chart-line'],
                                'Sidang Akhir' => ['class' => 'j-sa', 'icon' => 'fas fa-graduation-cap'],
                            ];
                            $fallbackList = ['j-uk','j-sp','j-sk1','j-sk2','j-sk3','j-sk4','j-sa'];
                        @endphp
                        @forelse($jadwalSidang as $i => $item)
                            @php
                                $jc = $jenisColors[$item->tahapan_sidang]['class'] ?? $fallbackList[$i % count($fallbackList)];
                                $ji = $jenisColors[$item->tahapan_sidang]['icon']  ?? 'fas fa-file-alt';
                            @endphp
                            <tr data-jenis="{{ $item->tahapan_sidang }}" data-status="Terjadwal">
                                <td class="td-no">{{ $i + 1 }}</td>
                                <td><div class="td-name">{{ $item->nama_mhs }}</div></td>
                                <td><span class="nim-badge">{{ $item->Nim }}</span></td>
                                <td class="td-prodi">{{ $item->nama_prodi }}</td>
                                <td class="td-extra">{{ $item->Judul ?? '—' }}</td>
                                <td class="td-date">{{ $item->tgl_sidang ? \Carbon\Carbon::parse($item->tgl_sidang)->translatedFormat('l, d F Y') : '—' }}</td>
                                <td class="td-room">{{ $item->ruang_sidang ?? '—' }}</td>
                                <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                                <td><span class="jenis-tag {{ $jc }}"><i class="{{ $ji }}"></i> {{ $item->tahapan_sidang }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center; padding:36px; color:var(--muted); font-size:0.82rem;">
                                    <i class="fas fa-inbox" style="font-size:1.4rem; opacity:0.25; display:block; margin-bottom:8px;"></i>
                                    Belum ada jadwal sidang yang terjadwal
                                </td>
                            </tr>
                        @endforelse

                        <!-- empty row shown when filter returns no result -->
                        <tr id="emptyRow" style="display:none;">
                            <td colspan="9" style="text-align:center; padding:36px; color:var(--muted); font-size:0.82rem;">
                                <i class="fas fa-search" style="font-size:1.4rem; opacity:0.25; display:block; margin-bottom:8px;"></i>
                                Tidak ada data yang cocok dengan pencarian
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div><!-- /table-scroll -->

            <!-- bottom summary -->
            <div style="padding: 12px 20px; border-top: 1px solid var(--border); display:flex; align-items:center; gap:16px; flex-wrap:wrap; background:var(--bg3); color:var(--text);">
                <span style="font-size:0.75rem; color:var(--muted);" id="rowCount">Menampilkan {{ $jadwalSidang->count() }} data</span>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-left:auto;">
                    @foreach($jenisSidangList as $j)
                        @php $ljc = $jenisColors[$j]['class'] ?? 'j-uk'; @endphp
                        <span class="jenis-tag {{ $ljc }}" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> {{ $j }}</span>
                    @endforeach
                </div>
            </div>
        </div><!-- /main-card -->

    </div><!-- /page-wrap -->

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Informasi Penjadwalan Sidang · FTTM Institut Teknologi Bandung · Halaman publik, login diperlukan untuk pengajuan sidang.
    </div>

    <script>
        function toggleDarkMode() {
            var html = document.documentElement;
            var icon = document.getElementById('darkModeIcon');
            var isDark = html.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', isDark ? 'true' : 'false');
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        }

        (function initDarkMode() {
            var html = document.documentElement;
            var icon = document.getElementById('darkModeIcon');
            var saved = localStorage.getItem('darkMode');
            if (saved === 'true') {
                html.classList.add('dark-mode');
                if (icon) icon.className = 'fas fa-sun';
            } else {
                if (icon) icon.className = 'fas fa-moon';
            }
        })();

        // Column indices (0-based): #=0, Mahasiswa=1, NIM=2, Prodi=3, Judul/Bab=4, Tanggal=5, Ruang=6, Status=7, Jenis=8
        function getCellText(row, colIndex) {
            const cells = row.querySelectorAll('td');
            return cells[colIndex] ? cells[colIndex].textContent.trim().toLowerCase() : '';
        }

        function doFilter() {
            const q        = document.getElementById('globalSearch').value.toLowerCase();
            const fMhs     = document.getElementById('fMahasiswa').value.toLowerCase();
            const fNIM     = document.getElementById('fNIM').value.toLowerCase();
            const fProdi   = document.getElementById('fProdi').value.toLowerCase();
            const fJudul   = document.getElementById('fJudul').value.toLowerCase();
            const fTanggal = document.getElementById('fTanggal').value.toLowerCase();
            const fRuang   = document.getElementById('fRuang').value.toLowerCase();
            const fStatus  = document.getElementById('fStatus').value;
            const fJenis   = document.getElementById('fJenis').value;

            const rows = document.querySelectorAll('#tableBody tr[data-jenis]');
            let visible = 0;

            rows.forEach(r => {
                const txt     = r.textContent.toLowerCase();
                const rStatus = r.dataset.status || '';
                const rJenis  = r.dataset.jenis  || '';

                const matchQ      = !q        || txt.includes(q);
                const matchMhs    = !fMhs     || getCellText(r, 1).includes(fMhs);
                const matchNIM    = !fNIM     || getCellText(r, 2).includes(fNIM);
                const matchProdi  = !fProdi   || getCellText(r, 3).includes(fProdi);
                const matchJudul  = !fJudul   || getCellText(r, 4).includes(fJudul);
                const matchTgl    = !fTanggal || getCellText(r, 5).includes(fTanggal);
                const matchRuang  = !fRuang   || getCellText(r, 6).includes(fRuang);
                const matchStatus = !fStatus  || rStatus === fStatus;
                const matchJenis  = !fJenis   || rJenis  === fJenis;

                const show = matchQ && matchMhs && matchNIM && matchProdi &&
                             matchJudul && matchTgl && matchRuang && matchStatus && matchJenis;
                r.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            document.getElementById('emptyRow').style.display = visible === 0 ? '' : 'none';
            document.getElementById('rowCount').textContent = 'Menampilkan ' + visible + ' data';

            updateFilterStyles();
        }

        function updateFilterStyles() {
            const colInputs = document.querySelectorAll('.col-filter-input, .col-filter-select');
            let anyActive = false;
            colInputs.forEach(el => {
                const hasVal = el.value.trim() !== '';
                el.classList.toggle('has-value', hasVal);
                if (hasVal) anyActive = true;
            });
            const globalSearch = document.getElementById('globalSearch');
            if (globalSearch.value.trim() !== '') anyActive = true;

            // update filter icon colours
            const icons = ['fi-mhs','fi-nim','fi-prodi','fi-judul','fi-tgl','fi-ruang'];
            const inputs = ['fMahasiswa','fNIM','fProdi','fJudul','fTanggal','fRuang'];
            icons.forEach((id, i) => {
                const icon = document.getElementById(id);
                if (icon) icon.classList.toggle('has-value', document.getElementById(inputs[i]).value.trim() !== '');
            });

            const btn = document.getElementById('clearFiltersBtn');
            btn.classList.toggle('visible', anyActive);
        }

        function clearAllFilters() {
            document.getElementById('globalSearch').value = '';
            ['fMahasiswa','fNIM','fProdi','fJudul','fTanggal','fRuang'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('fStatus').value = '';
            document.getElementById('fJenis').value = '';
            doFilter();
        }
    </script>
</body>
</html>
