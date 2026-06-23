<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Penjadwalan Sidang - FTTM ITB</title>
    <meta name="description" content="Jadwal Sidang Mahasiswa - FTTM ITB">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/itb-logo.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg:     #0d1117;
            --bg2:    #161b22;
            --bg3:    #1c2230;
            --border: rgba(255,255,255,0.08);
            --text:   #c9d1d9;
            --muted:  rgba(201,209,217,0.5);
            --accent: #3b82f6;
            --indigo: #6366f1;
            --green:  #22c55e;
            --yellow: #eab308;
            --purple: #a855f7;
            --pink:   #ec4899;
            --cyan:   #06b6d4;
            --orange: #f97316;
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
        .nav-brand img { width: 34px; height: 34px; background:#fff; border-radius:50%; padding:4px; flex-shrink: 0; }
        .nav-brand-text { display: flex; flex-direction: column; gap: 1px; }
        .nav-brand-name { font-size: 0.95rem; font-weight: 800; color: #fff; letter-spacing: 0.3px; line-height: 1.1; }
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
        .btn-ghost:hover { color: #fff; background: rgba(255,255,255,0.06); }
        .btn-primary { color: #fff; background: var(--accent); border-color: var(--accent); font-weight: 600; }
        .btn-primary:hover { background: #2563eb; transform: translateY(-1px); }

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
            background: rgba(59,130,246,0.15); color: var(--accent);
            display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
        }
        .card-title { font-size: 1rem; font-weight: 700; color: #fff; }
        .card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 1px; }
        .card-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 0.75rem; pointer-events: none; }
        .search-wrap input {
            background: var(--bg2); border: 1px solid var(--border); border-radius: 8px;
            color: var(--text); font-family: 'Inter', sans-serif; font-size: 0.78rem;
            padding: 7px 12px 7px 30px; width: 200px; outline: none; transition: border-color 0.15s;
        }
        .search-wrap input::placeholder { color: var(--muted); }
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

        /* Section Divider inside table */
        .section-divider {
            background: var(--bg3);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }
        .section-divider td {
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .section-dot {
            width: 8px; height: 8px; border-radius: 50%;
        }
        .section-count {
            background: rgba(255,255,255,0.07);
            color: var(--muted);
            border-radius: 20px;
            padding: 2px 9px;
            font-size: 0.68rem;
            font-weight: 600;
        }

        /* Table */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        thead tr.thead-labels { background: var(--bg3); border-bottom: 1px solid var(--border); }
        thead tr.thead-filters { background: rgba(0,0,0,0.25); border-bottom: 2px solid var(--border); }
        th {
            padding: 11px 14px;
            font-size: 0.7rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.7px;
            white-space: nowrap; text-align: left;
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }

        td { padding: 11px 14px; font-size: 0.82rem; color: var(--text); vertical-align: middle; }
        .td-no { color: var(--muted); font-size: 0.72rem; text-align: center; width: 42px; padding: 11px 8px; }
        .td-name { font-weight: 600; color: #fff; font-size: 0.83rem; }
        .nim-badge {
            display: inline-block;
            background: rgba(99,102,241,0.15); color: var(--indigo);
            border-radius: 5px; padding: 2px 7px; font-size: 0.68rem; font-weight: 700;
            margin-top: 3px;
        }
        .td-prodi { color: var(--muted); font-size: 0.78rem; }
        .td-extra { color: var(--text); font-size: 0.79rem; max-width: 200px; line-height: 1.4; }
        .td-date { color: var(--accent); font-weight: 600; font-size: 0.79rem; white-space: nowrap; }
        .td-room { font-size: 0.79rem; white-space: nowrap; }
        .bab-chip {
            background: rgba(234,179,8,0.1); color: var(--yellow);
            border-radius: 5px; padding: 3px 9px; font-size: 0.7rem; font-weight: 700;
        }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 0.68rem; font-weight: 700;
            white-space: nowrap;
        }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .badge-terjadwal { background: rgba(59,130,246,0.12); color: #60a5fa; }
        .badge-terjadwal::before { background: #60a5fa; }
        .badge-selesai { background: rgba(34,197,94,0.1); color: #4ade80; }
        .badge-selesai::before { background: #4ade80; }

        /* Jenis sidang tag (right side) */
        .jenis-tag {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 20px; font-size: 0.68rem; font-weight: 600;
            white-space: nowrap;
        }
        .j-uk      { background: rgba(59,130,246,0.1);  color: var(--accent); border: 1px solid rgba(59,130,246,0.2); }
        .j-sp      { background: rgba(99,102,241,0.1);  color: var(--indigo); border: 1px solid rgba(99,102,241,0.2); }
        .j-sk1     { background: rgba(34,197,94,0.08);  color: var(--green);  border: 1px solid rgba(34,197,94,0.2); }
        .j-sk2     { background: rgba(234,179,8,0.1);   color: var(--yellow); border: 1px solid rgba(234,179,8,0.2); }
        .j-sk3     { background: rgba(168,85,247,0.1);  color: var(--purple); border: 1px solid rgba(168,85,247,0.2); }
        .j-sk4     { background: rgba(236,72,153,0.08); color: var(--pink);   border: 1px solid rgba(236,72,153,0.2); }
        .j-sa      { background: rgba(6,182,212,0.08);  color: var(--cyan);   border: 1px solid rgba(6,182,212,0.2); }

        /* ── Inline column filter row ── */
        .col-filter-input {
            width: 100%;
            min-width: 70px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            padding: 4px 8px 4px 26px;
            outline: none;
            transition: border-color 0.15s, background 0.15s;
        }
        .col-filter-input::placeholder { color: rgba(201,209,217,0.3); }
        .col-filter-input:focus {
            border-color: rgba(59,130,246,0.5);
            background: rgba(59,130,246,0.06);
        }
        .col-filter-select {
            width: 100%;
            min-width: 80px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.72rem;
            padding: 4px 6px;
            outline: none;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' fill='%23555' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 6px center;
            padding-right: 20px;
        }
        .col-filter-select option { background: #1c2230; color: var(--text); }
        .col-filter-select:focus { border-color: rgba(59,130,246,0.5); background-color: rgba(59,130,246,0.06); }
        .col-filter-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .col-filter-wrap .fi-icon {
            position: absolute;
            left: 7px;
            color: rgba(201,209,217,0.3);
            font-size: 0.62rem;
            pointer-events: none;
            z-index: 1;
        }
        th.th-filter { padding: 6px 8px; vertical-align: middle; }
        .col-filter-input.has-value,
        .col-filter-select.has-value {
            border-color: rgba(59,130,246,0.5);
            background-color: rgba(59,130,246,0.08);
            color: #fff;
        }
        .col-filter-wrap .fi-icon.has-value { color: var(--accent); }
        /* clear badge shown when any col filter active */
        .clear-filters-btn {
            display: none;
            align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px;
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2);
            color: #f87171; font-size: 0.72rem; font-weight: 600;
            cursor: pointer; transition: all 0.15s;
            white-space: nowrap;
        }
        .clear-filters-btn:hover { background: rgba(239,68,68,0.2); }
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
            <a href="{{ route('login') }}" class="cta-btn"><i class="fas fa-sign-in-alt"></i> Masuk / Login</a>
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
                                    <option value="Ujian Kualifikasi">Ujian Kualifikasi</option>
                                    <option value="Sidang Proposal">Sidang Proposal</option>
                                    <option value="Seminar Kemajuan I">Sem. Kemajuan I</option>
                                    <option value="Seminar Kemajuan II">Sem. Kemajuan II</option>
                                    <option value="Seminar Kemajuan III">Sem. Kemajuan III</option>
                                    <option value="Seminar Kemajuan IV">Sem. Kemajuan IV</option>
                                    <option value="Sidang Akhir">Sidang Akhir</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">

                        <!-- ── Ujian Kualifikasi ── -->
                        <tr data-jenis="Ujian Kualifikasi" data-status="Terjadwal">
                            <td class="td-no">1</td>
                            <td><div class="td-name">Muhammad Rizky</div></td>
                            <td><span class="nim-badge">12221001</span></td>
                            <td class="td-prodi">Teknik Perminyakan</td>
                            <td class="td-extra">—</td>
                            <td class="td-date">2026-07-15</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-uk"><i class="fas fa-file-signature"></i> Ujian Kualifikasi</span></td>
                        </tr>
                        <tr data-jenis="Ujian Kualifikasi" data-status="Terjadwal">
                            <td class="td-no">2</td>
                            <td><div class="td-name">Aulia Rahman</div></td>
                            <td><span class="nim-badge">12221002</span></td>
                            <td class="td-prodi">Teknik Metalurgi</td>
                            <td class="td-extra">—</td>
                            <td class="td-date">2026-07-16</td>
                            <td class="td-room">Ruang Sidang B</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-uk"><i class="fas fa-file-signature"></i> Ujian Kualifikasi</span></td>
                        </tr>
                        <tr data-jenis="Ujian Kualifikasi" data-status="Selesai">
                            <td class="td-no">3</td>
                            <td><div class="td-name">Rina Wijaya</div></td>
                            <td><span class="nim-badge">12221003</span></td>
                            <td class="td-prodi">Teknik Geologi</td>
                            <td class="td-extra">—</td>
                            <td class="td-date">2026-07-17</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-selesai">Selesai</span></td>
                            <td><span class="jenis-tag j-uk"><i class="fas fa-file-signature"></i> Ujian Kualifikasi</span></td>
                        </tr>
                        <tr data-jenis="Ujian Kualifikasi" data-status="Terjadwal">
                            <td class="td-no">4</td>
                            <td><div class="td-name">Bambang Susilo</div></td>
                            <td><span class="nim-badge">12221004</span></td>
                            <td class="td-prodi">Teknik Geofisika</td>
                            <td class="td-extra">—</td>
                            <td class="td-date">2026-07-18</td>
                            <td class="td-room">Ruang Sidang C</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-uk"><i class="fas fa-file-signature"></i> Ujian Kualifikasi</span></td>
                        </tr>

                        <!-- ── Sidang Proposal ── -->
                        <tr data-jenis="Sidang Proposal" data-status="Terjadwal">
                            <td class="td-no">5</td>
                            <td><div class="td-name">Muhammad Rizky</div></td>
                            <td><span class="nim-badge">12221001</span></td>
                            <td class="td-prodi">Teknik Perminyakan</td>
                            <td class="td-extra">Optimasi Produksi Minyak Bumi</td>
                            <td class="td-date">2026-08-10</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sp"><i class="fas fa-file-export"></i> Sidang Proposal</span></td>
                        </tr>
                        <tr data-jenis="Sidang Proposal" data-status="Terjadwal">
                            <td class="td-no">6</td>
                            <td><div class="td-name">Aulia Rahman</div></td>
                            <td><span class="nim-badge">12221002</span></td>
                            <td class="td-prodi">Teknik Metalurgi</td>
                            <td class="td-extra">Karakterisasi Bahan Logam</td>
                            <td class="td-date">2026-08-11</td>
                            <td class="td-room">Ruang Sidang B</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sp"><i class="fas fa-file-export"></i> Sidang Proposal</span></td>
                        </tr>
                        <tr data-jenis="Sidang Proposal" data-status="Selesai">
                            <td class="td-no">7</td>
                            <td><div class="td-name">Rina Wijaya</div></td>
                            <td><span class="nim-badge">12221003</span></td>
                            <td class="td-prodi">Teknik Geologi</td>
                            <td class="td-extra">Analisis Struktur Batuan</td>
                            <td class="td-date">2026-08-12</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-selesai">Selesai</span></td>
                            <td><span class="jenis-tag j-sp"><i class="fas fa-file-export"></i> Sidang Proposal</span></td>
                        </tr>
                        <tr data-jenis="Sidang Proposal" data-status="Terjadwal">
                            <td class="td-no">8</td>
                            <td><div class="td-name">Bambang Susilo</div></td>
                            <td><span class="nim-badge">12221004</span></td>
                            <td class="td-prodi">Teknik Geofisika</td>
                            <td class="td-extra">Interpretasi Data Seismik</td>
                            <td class="td-date">2026-08-13</td>
                            <td class="td-room">Ruang Sidang C</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sp"><i class="fas fa-file-export"></i> Sidang Proposal</span></td>
                        </tr>

                        <!-- ── Seminar Kemajuan I ── -->
                        <tr data-jenis="Seminar Kemajuan I" data-status="Terjadwal">
                            <td class="td-no">9</td>
                            <td><div class="td-name">Muhammad Rizky</div></td>
                            <td><span class="nim-badge">12221001</span></td>
                            <td class="td-prodi">Teknik Perminyakan</td>
                            <td><span class="bab-chip">Bab 1-2</span></td>
                            <td class="td-date">2026-09-05</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sk1"><i class="fas fa-chart-line"></i> Seminar Kemajuan I</span></td>
                        </tr>
                        <tr data-jenis="Seminar Kemajuan I" data-status="Selesai">
                            <td class="td-no">10</td>
                            <td><div class="td-name">Aulia Rahman</div></td>
                            <td><span class="nim-badge">12221002</span></td>
                            <td class="td-prodi">Teknik Metalurgi</td>
                            <td><span class="bab-chip">Bab 1-2</span></td>
                            <td class="td-date">2026-09-06</td>
                            <td class="td-room">Ruang Sidang B</td>
                            <td><span class="badge badge-selesai">Selesai</span></td>
                            <td><span class="jenis-tag j-sk1"><i class="fas fa-chart-line"></i> Seminar Kemajuan I</span></td>
                        </tr>
                        <tr data-jenis="Seminar Kemajuan I" data-status="Terjadwal">
                            <td class="td-no">11</td>
                            <td><div class="td-name">Bambang Susilo</div></td>
                            <td><span class="nim-badge">12221004</span></td>
                            <td class="td-prodi">Teknik Geofisika</td>
                            <td><span class="bab-chip">Bab 1-2</span></td>
                            <td class="td-date">2026-09-07</td>
                            <td class="td-room">Ruang Sidang C</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sk1"><i class="fas fa-chart-line"></i> Seminar Kemajuan I</span></td>
                        </tr>

                        <!-- ── Seminar Kemajuan II ── -->
                        <tr data-jenis="Seminar Kemajuan II" data-status="Terjadwal">
                            <td class="td-no">12</td>
                            <td><div class="td-name">Muhammad Rizky</div></td>
                            <td><span class="nim-badge">12221001</span></td>
                            <td class="td-prodi">Teknik Perminyakan</td>
                            <td><span class="bab-chip">Bab 3</span></td>
                            <td class="td-date">2026-10-10</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sk2"><i class="fas fa-chart-line"></i> Seminar Kemajuan II</span></td>
                        </tr>
                        <tr data-jenis="Seminar Kemajuan II" data-status="Terjadwal">
                            <td class="td-no">13</td>
                            <td><div class="td-name">Aulia Rahman</div></td>
                            <td><span class="nim-badge">12221002</span></td>
                            <td class="td-prodi">Teknik Metalurgi</td>
                            <td><span class="bab-chip">Bab 3</span></td>
                            <td class="td-date">2026-10-11</td>
                            <td class="td-room">Ruang Sidang B</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sk2"><i class="fas fa-chart-line"></i> Seminar Kemajuan II</span></td>
                        </tr>

                        <!-- ── Seminar Kemajuan III ── -->
                        <tr data-jenis="Seminar Kemajuan III" data-status="Terjadwal">
                            <td class="td-no">14</td>
                            <td><div class="td-name">Muhammad Rizky</div></td>
                            <td><span class="nim-badge">12221001</span></td>
                            <td class="td-prodi">Teknik Perminyakan</td>
                            <td><span class="bab-chip">Bab 4</span></td>
                            <td class="td-date">2026-11-15</td>
                            <td class="td-room">Ruang Sidang A</td>
                            <td><span class="badge badge-terjadwal">Terjadwal</span></td>
                            <td><span class="jenis-tag j-sk3"><i class="fas fa-chart-line"></i> Seminar Kemajuan III</span></td>
                        </tr>

                        <!-- empty row shown when no result -->
                        <tr id="emptyRow" style="display:none;">
                            <td colspan="10" style="text-align:center; padding:36px; color:var(--muted); font-size:0.82rem;">
                                <i class="fas fa-search" style="font-size:1.4rem; opacity:0.25; display:block; margin-bottom:8px;"></i>
                                Tidak ada data yang cocok dengan pencarian
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div><!-- /table-scroll -->

            <!-- bottom summary -->
            <div style="padding: 12px 20px; border-top: 1px solid var(--border); display:flex; align-items:center; gap:16px; flex-wrap:wrap; background:var(--bg3);">
                <span style="font-size:0.75rem; color:var(--muted);" id="rowCount">Menampilkan 14 data</span>
                <div style="display:flex; gap:8px; flex-wrap:wrap; margin-left:auto;">
                    <span class="jenis-tag j-uk" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> Ujian Kualifikasi</span>
                    <span class="jenis-tag j-sp" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> Sidang Proposal</span>
                    <span class="jenis-tag j-sk1" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> Seminar Kemajuan I</span>
                    <span class="jenis-tag j-sk2" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> Seminar Kemajuan II</span>
                    <span class="jenis-tag j-sk3" style="font-size:0.65rem;"><i class="fas fa-circle" style="font-size:6px;"></i> Seminar Kemajuan III</span>
                </div>
            </div>
        </div><!-- /main-card -->

    </div><!-- /page-wrap -->

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Informasi Penjadwalan Sidang · FTTM Institut Teknologi Bandung · Halaman publik, login diperlukan untuk pengajuan sidang.
    </div>

    <script>
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
