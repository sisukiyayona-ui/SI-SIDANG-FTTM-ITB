@extends('layouts.master')

@section('title', 'Report Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Report Sidang')
@section('body_class', 'master-ui')

@php
if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        $s = strtolower($status ?? '');
        switch($s) {
            case 'belum diajukan':
            case null:
                return 'secondary';
            case 'diproses di tu prodi':
            case 'dalam proses':
                return 'warning';
            case 'diproses di fakultas':
                return 'orange';
            case 'menunggu pelaksanaan sidang':
            case 'menunggu approve kpps':
                return 'purple';
            case 'terjadwal':
                return 'primary';
            case 'lulus':
                return 'success';
            case 'tidak lulus':
                return 'danger';
            case 'rejected':
            case 'ditolak':
                return 'danger';
            default:
                return 'info';
        }
    }
}

if (!function_exists('formatTahapan')) {
    function formatTahapan($tahapan) {
        $tahapanLower = strtolower($tahapan ?? '');
        $labels = [
            'tahap i'   => 'Ujian Kualifikasi',
            'tahap ii'  => 'Ujian Proposal',
            'tahap iii' => 'Tahap III',
            'tahap iv'  => 'Sidang Terbuka / Tertutup',
            'sk i'      => 'SK I',
            'sk ii'     => 'SK II',
            'sk iii'    => 'SK III',
            'sk iv'     => 'SK IV',
        ];
        return $labels[$tahapanLower] ?? $tahapan;
    }
}
@endphp

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Report Sidang</li>
    </ol>
@endsection

@section('content')
    <div class="card card-report-sidang">
        <div class="card-header">
            <div class="report-card-heading">
                <div class="report-heading-icon"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <h5 class="mb-0">Report Sidang</h5>
                    <p class="card-heading-sub mb-0">Rekap data sidang, tim penguji, dan status kelulusan mahasiswa.</p>
                </div>
            </div>
            <div class="card-actions">
                <a href="{{ route('report.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            {{-- Filter --}}
            <div class="report-filter-bar mb-3">
                <div class="report-filter-group">
                    <label for="filterStrata"><i class="fas fa-layer-group mr-1"></i> Strata</label>
                    <select id="filterStrata" class="form-control form-control-sm" onchange="changeReportStrata(this.value)">
                        <option value="" {{ $strata === '' ? 'selected' : '' }}>Semua Strata (S1, S2, S3)</option>
                        <option value="S1" {{ $strata === 'S1' ? 'selected' : '' }}>S1</option>
                        <option value="S2" {{ $strata === 'S2' ? 'selected' : '' }}>S2</option>
                        <option value="S3" {{ $strata === 'S3' ? 'selected' : '' }}>S3</option>
                    </select>
                </div>
                <div class="report-filter-summary">
                    <span class="badge badge-light border">
                        <i class="fas fa-list mr-1"></i>
                        {{ $strata === '' ? 'Semua Strata' : 'Strata ' . $strata }}
                        <span class="badge badge-primary ml-1">{{ $reports->total() }} data</span>
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm report-table" id="reportTable">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th style="width:36px;">No</th>
                            <th style="width:60px;">Strata</th>
                            <th style="width:60px;">Tahun</th>
                            <th style="width:96px;">NIM</th>
                            <th style="width:150px;">Nama Mahasiswa</th>
                            <th>Judul</th>
                            <th style="width:120px;">NIP</th>
                            <th style="width:170px;">Nama Dosen</th>
                            <th style="width:130px;">Status Tim Sidang</th>
                            <th style="width:140px;">Tahapan</th>
                            <th style="width:90px;">Tanggal Sidang</th>
                            <th style="width:150px;">Status Lulus</th>
                            <th style="width:70px;">Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $item)
                            <tr>
                                <td class="text-center">{{ $reports->firstItem() + $loop->index }}</td>
                                <td class="text-center">
                                    <span class="badge badge-strata badge-strata-{{ strtolower($item->strata ?? '') }}">
                                        {{ $item->strata ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->tahun }}</td>
                                <td>{{ $item->NIM }}</td>
                                <td>{{ $item->nama_mahasiswa }}</td>
                                <td>{{ $item->JUDUL }}</td>
                                <td>{{ $item->NIP }}</td>
                                <td>{{ $item->pembimbing_penguji }}</td>
                                <td>{{ $item->STATUS_TIM_SIDANG }}</td>
                                <td>{{ formatTahapan($item->tahapan_sidang) }}</td>
                                <td class="text-center">
                                    @if(isset($item->tgl_sidang) && $item->tgl_sidang)
                                        {{ \Carbon\Carbon::parse($item->tgl_sidang)->translatedFormat('d M Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-status bg-{{ getStatusColor($item->status_lulus) }} badge-block">
                                        {{ $item->status_lulus ?? 'belum diajukan' }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->nilai_rata2 ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted py-4">Tidak ada data untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap report-pagination">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <label class="mb-0 mr-2 text-muted" for="perPage">Tampilkan</label>
                    <select class="form-control form-control-sm" id="perPage" name="per_page" style="width:auto; min-width:70px;" onchange="changeReportPerPage(this.value)">
                        @foreach([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int) $reports->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                    <span class="ml-2 text-muted" style="font-size:0.875rem;">data per halaman</span>
                </div>
                <div class="d-flex flex-column align-items-end">
                    <span class="text-muted small mb-1">
                        Menampilkan {{ $reports->total() > 0 ? $reports->firstItem() . '-' . $reports->lastItem() : 0 }} dari {{ $reports->total() }} data
                    </span>
                    {{ $reports->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-report-sidang .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e8ef;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .report-card-heading { display:flex; align-items:center; gap:0.85rem; }
        .report-heading-icon {
            width:42px; height:42px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            background:#e8f0fe; color:#2c5f9e; font-size:1.1rem;
        }
        .card-report-sidang .card-heading h5 { font-weight:600; color:#2c3a4b; }
        .card-report-sidang .card-heading-sub { color:#7b8794; font-size:0.82rem; }
        .report-filter-bar {
            display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
            background:#f7f9fc; border:1px solid #e8edf4; border-radius:8px;
            padding:0.7rem 0.9rem;
        }
        .report-filter-group { display:flex; align-items:center; gap:0.6rem; }
        .report-filter-group label { margin:0; font-weight:600; font-size:0.85rem; color:#3c4a5a; white-space:nowrap; }
        .report-filter-group select { min-width:230px; }
        .report-filter-summary { margin-left:auto; }
        .report-table { font-size:0.82rem; margin-bottom:0; }
        .report-table thead th { vertical-align:middle; font-weight:600; white-space:nowrap; }
        .report-table tbody td { vertical-align:middle; }
        .badge-strata { font-weight:700; letter-spacing:0.02em; }
        .badge-strata-s1 { background:#e3f2fd; color:#1565c0; }
        .badge-strata-s2 { background:#e8f5e9; color:#2e7d32; }
        .badge-strata-s3 { background:#fff3e0; color:#ef6c00; }
        .badge-status { font-weight:600; white-space:normal; text-align:left; line-height:1.25; }
        .badge-block { display:block; }
        .report-pagination .pagination { margin-bottom:0; }
        html.dark-mode .card-report-sidang .card-header { background:#232b36; border-bottom-color:#3a4454; }
        html.dark-mode .card-report-sidang .card-heading h5 { color:#e9ecef; }
        html.dark-mode .report-filter-bar { background:#232b36; border-color:#3a4454; }
        html.dark-mode .report-filter-group label { color:#e9ecef; }
    </style>

    <script>
        function changeReportStrata(value) {
            var url = new URL(window.location.href);
            if (value) { url.searchParams.set('strata', value); }
            else { url.searchParams.delete('strata'); }
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function changeReportPerPage(value) {
            var url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>
@endsection
