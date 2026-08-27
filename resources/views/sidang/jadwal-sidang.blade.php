@extends('layouts.master')

@section('title', 'Jadwal Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Jadwal Sidang')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route(session('auth_user.role') === 'Mahasiswa' ? 'mahasiswa.dashboard' : 'dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Jadwal Sidang</li>
    </ol>
@endsection

@php
    $bulan = request('bulan', date('m'));
    $tahun = request('tahun', date('Y'));
    $firstDay = mktime(0, 0, 0, $bulan, 1, $tahun);
    $dayOfWeek = date('w', $firstDay);
    $totalDays = date('t', $firstDay);
    $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $namaBulanPendek = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $hariNama = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $dayColors = [
        '#e3f2fd', // Sen (Biru muda)
        '#fce4ec', // Sel (Pink muda)
        '#e8f5e9', // Rab (Hijau muda)
        '#fff8e1', // Kam (Kuning/Amber muda)
        '#f3e5f5', // Jum (Ungu muda)
        '#fff3e0', // Sab (Orange muda)
        '#fbe9e7'  // Min (Merah muda)
    ];

    $prevMonth = $bulan == 1 ? 12 : $bulan - 1;
    $prevYear = $bulan == 1 ? $tahun - 1 : $tahun;
    $nextMonth = $bulan == 12 ? 1 : $bulan + 1;
    $nextYear = $bulan == 12 ? $tahun + 1 : $tahun;

    $jadwalByDate = [];
    foreach ($jadwalSidang as $item) {
        $tgl = $item->tgl_sidang;
        if ($tgl) {
            $jadwalByDate[$tgl][] = $item;
        }
    }

    function statusBadge($status) {
        $s = strtolower($status ?? '');
        if (!$status) return '<span class="badge badge-warning">Dalam Proses</span>';
        if ($s === 'belum diajukan') return '<span class="badge badge-secondary">Belum Diajukan</span>';
        if ($s === 'diproses di tu prodi') return '<span class="badge badge-warning">Diproses di TU Prodi</span>';
        if ($s === 'diproses di fakultas') return '<span class="badge badge-orange" style="background-color: orange;">Diproses di Fakultas</span>';
        if ($s === 'menunggu pelaksanaan sidang') return '<span class="badge badge-purple" style="background-color: #6f42c1;">Menunggu Pelaksanaan Sidang</span>';
        if ($s === 'terjadwal') return '<span class="badge badge-primary">Terjadwal</span>';
        if ($s === 'lulus') return '<span class="badge badge-success">Lulus</span>';
        if ($s === 'tidak lulus') return '<span class="badge badge-danger">Tidak Lulus</span>';
        if ($s === 'dalam proses') return '<span class="badge badge-warning">Dalam Proses</span>';
        return '<span class="badge badge-warning">' . ucfirst($status) . '</span>';
    }

    function getAjuanDisplayStatus($ev) {
        $sl = $ev->status_lulus ?? $ev->STATUS_LULUS ?? null;
        if (!empty($sl) && strtolower($sl) !== 'diajukan') return ucfirst($sl);
        $mhs = $ev->status_ajukan_mhs ?? $ev->STATUS_AJUKAN_MHS ?? 't';
        if (empty($mhs) || $mhs === 't') return 'Belum Diajukan';
        $prodi = $ev->status_ajukan_prodi ?? $ev->STATUS_AJUKAN_PRODI ?? 't';
        if ($mhs === 'y' && (empty($prodi) || $prodi === 't')) return 'Diproses di TU Prodi';
        $kpps = $ev->status_ajukan_kpps ?? $ev->STATUS_AJUKAN_KPPS ?? 't';
        if ($prodi === 'y' && (empty($kpps) || $kpps === 't')) return 'Diproses di Fakultas';
        if (($kpps ?? null) === 'y') return 'Menunggu Pelaksanaan Sidang';
        return 'Menunggu Pelaksanaan Sidang';
    }

    function tahapLabel($tahapan) {
        $tahapanLower = strtolower($tahapan);
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
@endphp

@section('content')
    <div id="jadwalCard" class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs mb-4 justify-content-end" id="jadwalTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="card-tab" data-toggle="tab" href="#card" role="tab"><i class="fas fa-th-large mr-1"></i> Card</a>
                </li>
            </ul>

            <div class="tab-content" id="jadwalTabsContent">
                <div class="tab-pane fade show active" id="calendar" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="?bulan={{ $prevMonth }}&tahun={{ $prevYear }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-chevron-left mr-1"></i> {{ $namaBulan[$prevMonth - 1] }} {{ $prevYear }}
                        </a>
                        <h4 class="mb-0 font-weight-bold">{{ $namaBulan[$bulan - 1] }} {{ $tahun }}</h4>
                        <a href="?bulan={{ $nextMonth }}&tahun={{ $nextYear }}" class="btn btn-outline-primary btn-sm">
                            {{ $namaBulan[$nextMonth - 1] }} {{ $nextYear }} <i class="fas fa-chevron-right ml-1"></i>
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered text-center" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    @foreach($hariNama as $idx => $h)
                                        <th class="py-2 text-dark" style="width: 14.28%; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px; background-color: {{ $dayColors[$idx] }};">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $dayCount = 1;
                                    $startOffset = $dayOfWeek == 0 ? 6 : $dayOfWeek - 1;
                                @endphp

                                @while($dayCount <= $totalDays)
                                    <tr>
                                        @for($col = 0; $col < 7; $col++)
                                            @php
                                                $isCurrentDay = ($dayCount <= $totalDays && ($col >= $startOffset || $dayCount > 1));
                                                $displayDay = $dayCount;
                                                $isToday = false;
                                            @endphp

                                            @if(!$isCurrentDay)
                                                <td class="p-1" style="height: 140px; vertical-align: top; background-color: {{ $dayColors[$col] }}; opacity: 0.5;"></td>
                                            @elseif($dayCount > $totalDays)
                                                <td class="p-1" style="height: 140px; vertical-align: top; background-color: {{ $dayColors[$col] }}; opacity: 0.5;"></td>
                                            @else
                                                @php
                                                    $dateStr = sprintf('%s-%02s-%02s', $tahun, $bulan, $displayDay);
                                                    $todayStr = date('Y-m-d');
                                                    $isToday = ($dateStr === $todayStr);
                                                    $dayEvents = $jadwalByDate[$dateStr] ?? [];
                                                @endphp
                                                <td class="p-1" style="height: 140px; vertical-align: top; background-color: {{ $dayColors[$col] }}; {{ $isToday ? 'border: 3px solid #1e3a8a !important; box-shadow: inset 0 0 10px rgba(0,0,0,0.1);' : '' }}">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="font-weight-bold {{ $isToday ? 'text-primary' : 'text-dark' }}" style="font-size: 1.2rem;">
                                                            {{ $displayDay }} {!! $isToday ? '<small>(Hari Ini)</small>' : '' !!}
                                                        </span>
                                                        @if(count($dayEvents) > 0)
                                                            <span class="badge badge-primary" style="font-size: 0.9rem;">{{ count($dayEvents) }}</span>
                                                        @endif
                                                    </div>
                                                    <div style="max-height: 100px; overflow-y: auto;">
                                                        @foreach(array_slice($dayEvents, 0, 3) as $ev)
                                                            <div class="text-left mb-1 p-1 rounded shadow-sm text-dark" style="font-size: 0.9rem; line-height: 1.3; background: #ffffff; border-left: 3px solid #1e3a8a; cursor: pointer;"
                                                                 onclick="showDetail({{ json_encode([
                                                                     'tanggal' => \Carbon\Carbon::parse($ev->tgl_sidang)->translatedFormat('l, d F Y'),
                                                                     'nama' => $ev->nama_mhs,
                                                                     'nim' => $ev->Nim,
                                                                     'judul' => $ev->Judul,
                                                                     'waktu' => $ev->waktu_sidang ?? '-',
                                                                     'ruang' => $ev->ruang_sidang ?? '-',
                                                                     'tahapan' => tahapLabel($ev->tahapan_sidang),
                                                                      'status' => getAjuanDisplayStatus($ev),
                                                                     'strata' => $ev->Strata ?? '-',
                                                                 ]) }})">
                                                                <strong>{{ $ev->nama_mhs }}</strong><br>
                                                                <span class="text-muted"><i class="far fa-clock"></i> {{ $ev->waktu_sidang ?? '-' }}</span>
                                                            </div>
                                                        @endforeach
                                                        @if(count($dayEvents) > 3)
                                                            <div class="text-muted small text-center">+{{ count($dayEvents) - 3 }} lainnya</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                @php $dayCount++; @endphp
                                            @endif
                                        @endfor
                                    </tr>
                                    @php $startOffset = 0; @endphp
                                @endwhile
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 text-muted small text-right">
                        <i class="fas fa-circle text-primary mr-1" style="font-size: 0.5rem;"></i> Klik jadwal untuk detail
                    </div>
                </div>

                <div class="tab-pane fade" id="card" role="tabpanel">
                    @if($jadwalSidang && $jadwalSidang->count() > 0)
                        @php
                            $groupedJadwal = $jadwalSidang->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tgl_sidang)->format('Y-m-d');
                            });
                            // Warna background yang selang-seling seperti di gambar (biru, orange, hijau)
                            $rowColors = ['#c0d4ec', '#fac9a7', '#c2dfc1', '#f5c6cb', '#ffeeba']; 
                        @endphp
                        
                        @foreach($groupedJadwal as $tanggal => $items)
                            @php 
                                // Dapatkan indeks hari (0 = Senin, 6 = Minggu)
                                $dayOfWeekIdx = (\Carbon\Carbon::parse($tanggal)->dayOfWeek + 6) % 7; 
                                $headerColor = $dayColors[$dayOfWeekIdx];
                            @endphp
                            <div class="mb-4" style="border: 1px solid #dcdcdc; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <div style="background-color: {{ $headerColor }}; padding: 12px 25px; font-weight: bold; color: #333; font-size: 1.25rem; border-bottom: 2px solid rgba(0,0,0,0.1);">
                                    <i class="fas fa-calendar-day mr-2 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                </div>
                                
                                @php $c_idx = 0; @endphp
                                @foreach($items as $item)
                                    <div class="d-flex p-3 text-dark position-relative" style="background-color: {{ $rowColors[$c_idx % count($rowColors)] }}; {{ !$loop->last ? 'border-bottom: 1px solid #999;' : '' }}">
                                        <div style="width: 180px; flex-shrink: 0; padding-left: 10px;">
                                            <div style="margin-bottom: 3px; font-weight: bold; font-size: 1.15rem;">{{ $item->waktu_sidang ? substr($item->waktu_sidang, 0, 5) : '-' }}</div>
                                            <div style="font-size: 1.1rem; margin-bottom: 2px;">{{ $item->ruang_sidang ?? '-' }}</div>
                                            <div style="font-size: 1.1rem;">FTTM</div>
                                        </div>
                                        <div style="flex-grow: 1;">
                                            <table style="width: 100%; border: none; font-size: 1.1rem; line-height: 1.5;">
                                                <tr>
                                                    <td style="width: 90px; vertical-align: top;">Nama</td>
                                                    <td style="width: 15px; vertical-align: top;">:</td>
                                                    <td>{{ $item->nama_mhs }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align: top;">NIM</td>
                                                    <td style="vertical-align: top;">:</td>
                                                    <td>{{ $item->Nim }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align: top;">Tahapan</td>
                                                    <td style="vertical-align: top;">:</td>
                                                    <td><strong>{{ tahapLabel($item->tahapan_sidang) }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td style="vertical-align: top;">Judul</td>
                                                    <td style="vertical-align: top;">:</td>
                                                    <td>{{ $item->Judul }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div style="position: absolute; top: 10px; right: 10px;">
                                            <a href="{{ session('auth_user.role') === 'Mahasiswa' ? '/mahasiswa/dashboard' : '/sidang/' . strtolower($item->Strata ?? 's1') }}" 
                                               class="btn btn-sm btn-primary" 
                                               style="font-size: 0.85rem; padding: 6px 12px; white-space: nowrap;"
                                               title="Lihat Tabel Jadwal Sidang {{ tahapLabel($item->tahapan_sidang) }}">
                                                <i class="fas fa-table mr-1"></i>Tabel
                                            </a>
                                        </div>
                                    </div>
                                    @php $c_idx++; @endphp
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>Belum ada jadwal sidang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle mr-2"></i>Detail Jadwal Sidang</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="detailModalBody"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var currentUserRole = '{{ session('auth_user.role') }}';

    function showDetail(data) {
        var statusClass = 'badge-warning';
        if (data.status.toLowerCase() === 'lulus') {
            statusClass = 'badge-success';
        } else if (data.status.toLowerCase() === 'tidak lulus') {
            statusClass = 'badge-danger';
        }

        var html = '' +
            '<div class="jadwal-detail-container">' +
                // Header: Tanggal & Waktu
                '<div class="detail-datetime-header">' +
                    '<div class="datetime-item">' +
                        '<i class="far fa-calendar-alt"></i>' +
                        '<div class="datetime-text">' +
                            '<div class="datetime-label">Tanggal</div>' +
                            '<div class="datetime-value">' + data.tanggal + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="datetime-divider"></div>' +
                    '<div class="datetime-item">' +
                        '<i class="far fa-clock"></i>' +
                        '<div class="datetime-text">' +
                            '<div class="datetime-label">Waktu</div>' +
                            '<div class="datetime-value">' + data.waktu + '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="datetime-divider"></div>' +
                    '<div class="datetime-item">' +
                        '<i class="fas fa-door-open"></i>' +
                        '<div class="datetime-text">' +
                            '<div class="datetime-label">Ruangan</div>' +
                            '<div class="datetime-value">' + data.ruang + '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                
                // Mahasiswa Info
                '<div class="detail-mahasiswa-section">' +
                    '<div class="mhs-name">' + data.nama + '</div>' +
                    '<div class="mhs-info-row">' +
                        '<span class="mhs-badge">NIM: ' + data.nim + '</span>' +
                        '<span class="mhs-badge">Strata: ' + data.strata + '</span>' +
                    '</div>' +
                '</div>' +
                
                // Judul
                '<div class="detail-judul-section">' +
                    '<div class="section-label">Judul Penelitian</div>' +
                    '<div class="judul-content">' + data.judul + '</div>' +
                '</div>' +
                
                // Tahapan & Status
                '<div class="detail-info-grid">' +
                    '<div class="info-box">' +
                        '<div class="info-label">Tahapan</div>' +
                        '<div class="info-value">' + data.tahapan + '</div>' +
                    '</div>' +
                    '<div class="info-box">' +
                        '<div class="info-label">Status</div>' +
                        '<div class="info-value">' +
                            '<span class="badge ' + statusClass + '">' + data.status + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                
                // Link to Jadwal Table - Task #17
                '<div class="detail-action-section">' +
                    '<a href="' + (currentUserRole === 'Mahasiswa' ? '/mahasiswa/dashboard' : '/sidang/' + data.strata.toLowerCase()) + '" class="btn btn-primary btn-block btn-lg">' +
                        '<i class="fas fa-table mr-2"></i>Lihat Tabel Jadwal Sidang ' + data.tahapan +
                    '</a>' +
                '</div>' +
            '</div>';

        document.getElementById('detailModalBody').innerHTML = html;
        $('#detailModal').modal('show');
    }
</script>

<style>
    /* Detail Modal - Improved Readability with ITB Colors */
    #detailModal .modal-dialog {
        max-width: 650px;
    }
    
    #detailModal .modal-content {
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }
    
    body.dark-mode #detailModal .modal-content {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    }
    
    .jadwal-detail-container {
        padding: 0;
    }
    
    /* DateTime Header - Horizontal Layout with Icons */
    .detail-datetime-header {
        display: flex;
        align-items: stretch;
        background: #1e3a8a; /* ITB Blue */
        color: white;
        padding: 0;
        margin: -16px -16px 0 -16px;
    }
    
    body.dark-mode .detail-datetime-header {
        background: #1e40af;
    }
    
    .datetime-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 16px;
    }
    
    .datetime-item i {
        font-size: 1.8rem;
        color: rgba(255, 255, 255, 0.9);
        flex-shrink: 0;
    }
    
    .datetime-text {
        flex: 1;
        min-width: 0;
    }
    
    .datetime-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
        margin-bottom: 4px;
        font-weight: 600;
    }
    
    .datetime-value {
        font-size: 1rem;
        font-weight: 700;
        line-height: 1.3;
        word-break: break-word;
    }
    
    .datetime-divider {
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
        align-self: stretch;
    }
    
    /* Mahasiswa Section */
    .detail-mahasiswa-section {
        padding: 28px 28px 24px;
        border-bottom: 2px solid #e5e7eb;
        background: #ffffff;
    }
    
    body.dark-mode .detail-mahasiswa-section {
        border-bottom-color: #334155;
        background: #1e293b;
    }
    
    .mhs-name {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 14px;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    
    body.dark-mode .mhs-name {
        color: #f1f5f9;
    }
    
    .mhs-info-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .mhs-badge {
        display: inline-block;
        padding: 10px 18px;
        background: #f1f5f9;
        color: #1e293b;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    
    body.dark-mode .mhs-badge {
        background: #334155;
        color: #cbd5e1;
        border-color: #475569;
    }
    
    /* Judul Section */
    .detail-judul-section {
        padding: 28px 28px;
        background: #ffffff;
        border-bottom: 2px solid #e5e7eb;
    }
    
    body.dark-mode .detail-judul-section {
        background: #1e293b;
        border-bottom-color: #334155;
    }
    
    .section-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #1e3a8a; /* ITB Blue */
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 14px;
    }
    
    body.dark-mode .section-label {
        color: #60a5fa;
    }
    
    .judul-content {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #1e293b;
        padding: 20px;
        background: #f8fafc;
        border-radius: 10px;
        border-left: 4px solid #1e3a8a; /* ITB Blue */
    }
    
    body.dark-mode .judul-content {
        background: #0f172a;
        color: #e2e8f0;
        border-left-color: #3b82f6;
    }
    
    /* Info Grid */
    .detail-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        padding: 28px 28px 32px;
        background: #ffffff;
    }
    
    body.dark-mode .detail-info-grid {
        background: #1e293b;
    }
    
    .info-box {
        padding: 22px 20px;
        background: #f8fafc;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    body.dark-mode .info-box {
        background: #0f172a;
        border-color: #334155;
    }
    
    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.12);
        border-color: #3b82f6;
    }
    
    body.dark-mode .info-box:hover {
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
    }
    
    .info-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }
    
    body.dark-mode .info-label {
        color: #94a3b8;
    }
    
    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }
    
    body.dark-mode .info-value {
        color: #f1f5f9;
    }
    
    .info-value .badge {
        font-size: 1.05rem;
        padding: 8px 18px;
        font-weight: 700;
        border-radius: 8px;
    }
    
    /* Modal Header */
    #detailModal .modal-header {
        border-bottom: 2px solid #e2e8f0;
        padding: 20px 28px;
        background: #ffffff;
    }
    
    body.dark-mode #detailModal .modal-header {
        border-bottom-color: #334155;
        background: #1e293b;
    }
    
    #detailModal .modal-title {
        font-weight: 700;
        font-size: 1.35rem;
        color: #0f172a;
        letter-spacing: -0.3px;
    }
    
    body.dark-mode #detailModal .modal-title {
        color: #f1f5f9;
    }
    
    #detailModal .close {
        font-size: 2rem;
        font-weight: 300;
        opacity: 0.6;
        transition: opacity 0.2s;
        text-shadow: none;
    }
    
    #detailModal .close:hover {
        opacity: 1;
    }
    
    body.dark-mode #detailModal .close {
        color: #f1f5f9;
    }
    
    /* Badge colors */
    .badge-success {
        background-color: #10b981 !important;
        color: #ffffff !important;
    }
    
    .badge-danger {
        background-color: #ef4444 !important;
        color: #ffffff !important;
    }
    
    .badge-warning {
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
    
    /* Action Section - Task #17 */
    .detail-action-section {
        padding: 20px 28px 28px;
        background: #ffffff;
        border-top: 2px solid #e5e7eb;
    }
    
    body.dark-mode .detail-action-section {
        background: #1e293b;
        border-top-color: #334155;
    }
    
    .detail-action-section .btn-primary {
        background: #1e3a8a;
        border: none;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 14px 24px;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .detail-action-section .btn-primary:hover {
        background: #1e40af;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
    }
    
    body.dark-mode .detail-action-section .btn-primary {
        background: #3b82f6;
    }
    
    body.dark-mode .detail-action-section .btn-primary:hover {
        background: #60a5fa;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .detail-datetime-header {
            flex-direction: column;
        }
        
        .datetime-divider {
            width: 100%;
            height: 1px;
        }
        
        .datetime-item {
            padding: 16px 20px;
        }
        
        .datetime-value {
            font-size: 0.95rem;
        }
        
        .detail-info-grid {
            grid-template-columns: 1fr;
        }
        
        .mhs-name {
            font-size: 1.6rem;
        }
        
        .detail-mahasiswa-section,
        .detail-judul-section,
        .detail-info-grid {
            padding: 20px;
        }
    }
</style>
@endpush