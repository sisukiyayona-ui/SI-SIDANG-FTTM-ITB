@extends('layouts.master')

@section('title', 'Dashboard - SI SIDANG FTTM ITB')
@section('page_title', 'Dashboard')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Mahasiswa</p>
                            <h3 class="mb-0 fw-bold">{{ $totalMahasiswa }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Sidang</p>
                            <h3 class="mb-0 fw-bold">{{ $totalSidang }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Seminar</p>
                            <h3 class="mb-0 fw-bold">{{ $totalSeminar }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Penguji</p>
                            <h3 class="mb-0 fw-bold">{{ $totalPenguji }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Grafik Sidang & Seminar</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartSidang" height="280"></canvas>
                </div>
            </div>

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
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        const ctx = document.getElementById('chartSidang').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Sidang',
                        data: @json($chartSidang),
                        borderColor: '#1e3a8a',
                        backgroundColor: 'rgba(30, 58, 138, 0.1)',
                        tension: .3,
                        fill: true,
                    },
                    {
                        label: 'Seminar',
                        data: @json($chartSeminar),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: .3,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                },
                scales: {
                    y: { beginAtZero: true },
                },
            },
        });
    });
</script>
@endpush
