@extends('layouts.master')

@section('title', 'Report - SI SIDANG FTTM ITB')
@section('page_title', 'Report & Statistik')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Report</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Total Sidang</p>
                            <h3 class="mb-0">{{ $stats['total_sidang'] }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(47,85,151,.1); color: #2f5597;">
                            <i class="fas fa-gavel"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Sidang Selesai</p>
                            <h3 class="mb-0">{{ $stats['sidang_selesai'] }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(40,167,69,.1); color: #28a745;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Mahasiswa Aktif</p>
                            <h3 class="mb-0">{{ $stats['mahasiswa_aktif'] }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(255,193,7,.1); color: #ffc107;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1">Seminar Berjalan</p>
                            <h3 class="mb-0">{{ $stats['seminar_berjalan'] }}</h3>
                        </div>
                        <div class="icon-bg" style="background: rgba(79,129,189,.1); color: #4f81bd;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Rekap Sidang & Seminar</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartRekapSidang" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie mr-2"></i>Rekap Kelulusan per Prodi</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartKelulusan" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Rekap Mahasiswa per Prodi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Program Studi</th>
                                    <th>Aktif</th>
                                    <th>Cuti</th>
                                    <th>Lulus</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapMahasiswa as $i => $item)
                                    @php $total = $item['aktif'] + $item['cuti'] + $item['lulus']; @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $item['prodi'] }}</td>
                                        <td><span class="badge bg-success">{{ $item['aktif'] }}</span></td>
                                        <td><span class="badge bg-warning">{{ $item['cuti'] }}</span></td>
                                        <td><span class="badge bg-info">{{ $item['lulus'] }}</span></td>
                                        <td><strong>{{ $total }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        new Chart(document.getElementById('chartRekapSidang'), {
            type: 'bar',
            data: {
                labels: @json($rekapSidang->pluck('bulan')),
                datasets: [
                    { label: 'Sidang', data: @json($rekapSidang->pluck('sidang')), backgroundColor: '#2f5597' },
                    { label: 'Seminar', data: @json($rekapSidang->pluck('seminar')), backgroundColor: '#4f81bd' },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true } },
            },
        });

        new Chart(document.getElementById('chartKelulusan'), {
            type: 'doughnut',
            data: {
                labels: @json($rekapKelulusan->pluck('prodi')),
                datasets: [{
                    data: @json($rekapKelulusan->pluck('lulus')),
                    backgroundColor: ['#2f5597', '#4f81bd', '#28a745', '#ffc107'],
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });
    });
</script>
@endpush
