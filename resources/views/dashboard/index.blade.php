@extends('layouts.master')

@section('title', 'Dashboard - SI SIDANG FTTM ITB')
@section('page_title', 'Dashboard')
@section('body_class', 'master-ui')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
@endsection

@section('content')
    <div class="row stat-grid">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label">Total Mahasiswa</p>
                        <p class="stat-value">{{ $totalMahasiswa }}</p>
                    </div>
                    <div class="icon-bg"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label">Total Sidang</p>
                        <p class="stat-value">{{ $totalSidang }}</p>
                    </div>
                    <div class="icon-bg"><i class="fas fa-graduation-cap"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label">Total Seminar</p>
                        <p class="stat-value">{{ $totalSeminar }}</p>
                    </div>
                    <div class="icon-bg"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="stat-label">Total Penguji</p>
                        <p class="stat-value">{{ $totalPenguji }}</p>
                    </div>
                    <div class="icon-bg"><i class="fas fa-user-tie"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card chart-card">
                <div class="card-header">
                    <div class="card-heading">
                        <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Grafik Sidang (Lulus) per Tahap</h5>
                        <p class="card-heading-sub">Jumlah sidang lulus per tahap dalam tiga tahun terakhir.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-canvas">
                        <canvas id="chartSidang"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Sidang --}}
    {{--<div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tasks mr-2"></i>Progress Sidang</h5>
                </div>
                <div class="card-body">
                    @foreach($progress as $item)
                        @php
                            $pct = $item['total'] > 0 ? round(($item['completed'] / $item['total']) * 100) : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>{{ $item['label'] }}</span>
                                <span>{{ $item['completed'] }}/{{ $item['total'] }} ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $pct }}%; background: var(--primary-blue);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>--}}

    {{-- Aktivitas Terbaru & Informasi --}}
    {{--<div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock mr-2"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentActivities) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentActivities as $activity)
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center"
                                                 style="width: 36px; height: 36px; color: #fff; font-size: .8rem;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $activity['user'] }}</strong>
                                            <p class="mb-0 text-muted" style="font-size: .85rem;">{{ $activity['activity'] }}</p>
                                            <small class="text-muted">{{ $activity['time'] }}</small>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox mb-2" style="font-size: 1.5rem; opacity: 0.3;"></i>
                            <p class="mb-0">Belum ada aktivitas</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Informasi</h5>
                </div>
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-calendar-check text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h6>{{ $mahasiswaAktif }} Mahasiswa Aktif</h6>
                    <p class="text-muted mb-0">{{ $sidangSelesai }} Sidang Selesai</p>
                    <p class="text-muted">{{ $seminarBerjalan }} Seminar Berjalan</p>
                </div>
            </div>
        </div>
    </div>--}}
@endsection

@push('scripts')
<script>
    $(function () {
        var colors = ['#1462ad', '#1f7ad4', '#3b82f6', '#8ec5f0'];
        var datasets = [];
        @foreach($chartTahapData as $tahap => $data)
        datasets.push({
            label: '{{ $tahap }}',
            data: @json($data),
            backgroundColor: colors[datasets.length],
            borderColor: colors[datasets.length],
            borderWidth: 0,
            borderRadius: 6,
            maxBarThickness: 54,
        });
        @endforeach

        const ctx = document.getElementById('chartSidang').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartYears),
                datasets: datasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            color: '#94a3b8',
                            font: { size: 11, family: "'Segoe UI', system-ui, sans-serif" },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                        grid: { color: 'rgba(148, 163, 184, .18)', drawBorder: false },
                    },
                    x: {
                        ticks: { color: '#94a3b8', font: { size: 11 } },
                        grid: { display: false },
                    },
                },
            },
        });
    });
</script>
@endpush
