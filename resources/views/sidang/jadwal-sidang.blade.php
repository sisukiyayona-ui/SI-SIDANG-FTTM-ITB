@extends('layouts.master')

@section('title', 'Jadwal Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Jadwal Sidang')
@section('body_class', 'master-ui')
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

    function getAjuanDisplayStatus($ev) {
        $sl = $ev->status_lulus ?? $ev->STATUS_LULUS ?? null;
        if (!empty($sl) && strtolower($sl) !== 'diajukan') return ucfirst($sl);
        $mhs = $ev->status_ajukan_mhs ?? $ev->STATUS_AJUKAN_MHS ?? 't';
        if (empty($mhs) || $mhs === 't') return 'Belum Diajukan';
        $prodi = $ev->status_ajukan_prodi ?? $ev->STATUS_AJUKAN_PRODI ?? 't';
        if ($mhs === 'y' && (empty($prodi) || $prodi === 't')) return 'Diproses di TU Prodi';
        $kpps = $ev->status_ajukan_kpps ?? $ev->STATUS_AJUKAN_KPPS ?? 't';
        if (($kpps ?? null) === 'y') {
            $ajuanId = $ev->id ?? null;
            $approved = $ajuanId ? \Illuminate\Support\Facades\DB::table('t_app_ajuan_sidang as app')
                ->where('app.ID_AJUAN_SIDANG', $ajuanId)
                ->whereIn('app.STATUS_APPROVE', ['t', 'y'])
                ->whereExists(function ($q) {
                    $q->selectRaw('1')->from('t_kpps as kk')->whereColumn('kk.ID_USER', 'app.ID_USER')->where('kk.STATUS_AKTIF', 'AKTIF');
                })
                ->distinct()
                ->count('app.ID_USER') : 0;
            $totalKpps = $ajuanId ? \Illuminate\Support\Facades\DB::table('t_kpps as k')
                ->join('t_ajuan_sidang as x3', 'x3.KODE_PRODI', '=', 'k.KODE_PRODI')
                ->where('x3.id', $ajuanId)
                ->where('k.STATUS_AKTIF', 'AKTIF')
                ->distinct()
                ->count('k.ID_USER') : 0;
            if ($totalKpps > 0 && $approved >= $totalKpps) return 'Terjadwal';
            return 'Menunggu Approve KPPS';
        }
        $ajuanId = $ev->id ?? null;
        if ($ajuanId) {
            $rejected = \Illuminate\Support\Facades\DB::table('t_app_ajuan_sidang')
                ->where('ID_AJUAN_SIDANG', $ajuanId)
                ->where('STATUS_APPROVE', 'f')
                ->exists();
            if ($rejected) return 'Rejected';
        }
        if ($prodi === 'y' && (empty($kpps) || $kpps === 't')) return 'Diproses di Fakultas';
        return 'Menunggu Approve KPPS';
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

    function strataColor($strata) {
        $s = strtolower(trim($strata ?? ''));
        $map = [
            's1' => ['border' => '#3b82f6', 'bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'S1'],
            's2' => ['border' => '#10b981', 'bg' => '#ecfdf5', 'text' => '#047857', 'label' => 'S2'],
            's3' => ['border' => '#8b5cf6', 'bg' => '#f5f3ff', 'text' => '#6d28d9', 'label' => 'S3'],
        ];
        return $map[$s] ?? ['border' => '#6b7280', 'bg' => '#f9fafb', 'text' => '#374151', 'label' => $strata ?? '-'];
    }

    function strataColorDark($strata) {
        $s = strtolower(trim($strata ?? ''));
        $map = [
            's1' => ['border' => '#60a5fa', 'bg' => 'rgba(96,165,250,.12)', 'text' => '#93c5fd'],
            's2' => ['border' => '#34d399', 'bg' => 'rgba(52,211,153,.12)', 'text' => '#6ee7b7'],
            's3' => ['border' => '#a78bfa', 'bg' => 'rgba(167,139,250,.12)', 'text' => '#c4b5fd'],
        ];
        return $map[$s] ?? ['border' => '#9ca3af', 'bg' => 'rgba(156,163,175,.12)', 'text' => '#d1d5db'];
    }
@endphp

@php
    $activeTab = request()->has('page') || request()->has('per_page') ? 'card' : 'calendar';
@endphp

@section('content')
<div class="master-data-container">
    <div id="jadwalCard" class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs mb-4 justify-content-end" id="jadwalTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'calendar' ? 'active' : '' }}" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab"><i class="fas fa-calendar-alt mr-1"></i> Kalender</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'card' ? 'active' : '' }}" id="card-tab" data-toggle="tab" href="#card" role="tab"><i class="fas fa-th-large mr-1"></i> Card</a>
                </li>
            </ul>

            <div class="tab-content" id="jadwalTabsContent">
                <div class="tab-pane fade {{ $activeTab === 'calendar' ? 'show active' : '' }}" id="calendar" role="tabpanel">
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
                        <table class="table table-bordered text-center jadwal-calendar">
                            <thead>
                                <tr>
                                    @foreach($hariNama as $idx => $h)
                                        <th style="width: 14.28%;">{{ $h }}</th>
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
                                                <td class="p-1 is-empty"></td>
                                            @elseif($dayCount > $totalDays)
                                                <td class="p-1 is-empty"></td>
                                            @else
                                                @php
                                                    $dateStr = sprintf('%s-%02s-%02s', $tahun, $bulan, $displayDay);
                                                    $todayStr = date('Y-m-d');
                                                    $isToday = ($dateStr === $todayStr);
                                                    $dayEvents = $jadwalByDate[$dateStr] ?? [];
                                                @endphp
                                                <td class="p-1 {{ $isToday ? 'is-today' : '' }}">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <span class="jadwal-cell-day">
                                                            {{ $displayDay }} {!! $isToday ? '<small>(Hari Ini)</small>' : '' !!}
                                                        </span>
                                                        @if(count($dayEvents) > 0)
                                                            <span class="badge bg-primary">{{ count($dayEvents) }}</span>
                                                        @endif
                                                    </div>
                                                    <div style="max-height: 100px; overflow-y: auto;">
                                                        @foreach(array_slice($dayEvents, 0, 3) as $ev)
                                                            @php $sc = strataColor($ev->Strata ?? ''); @endphp
                                                            <div class="jadwal-event" data-strata="{{ strtolower($ev->Strata ?? '') }}"
                                                                 style="border-left-color: {{ $sc['border'] }};"
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
                                                                     'prodi' => $ev->NAMA_PRODI ?? '-',
                                                                 ]) }})">
                                                                <span class="jadwal-event-strata" style="background: {{ $sc['bg'] }}; color: {{ $sc['text'] }}; border: 1px solid {{ $sc['border'] }};">{{ $sc['label'] }}</span>
                                                                <strong>{{ $ev->nama_mhs }}</strong>
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

                <div class="tab-pane fade {{ $activeTab === 'card' ? 'show active' : '' }}" id="card" role="tabpanel">
                    @php
                        $cardsData = $jadwalCards ?? $jadwalSidang;
                        $itemsCollection = method_exists($cardsData, 'getCollection') ? $cardsData->getCollection() : $cardsData;
                    @endphp
                    @if($itemsCollection && $itemsCollection->count() > 0)
                        @php
                            $groupedJadwal = $itemsCollection->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tgl_sidang)->format('Y-m-d');
                            });
                        @endphp

                        @foreach($groupedJadwal as $tanggal => $items)
                            @php
                                $dayStratas = $items->pluck('Strata')->map(fn($s) => strtolower(trim($s ?? '')))->filter()->unique()->values();
                                $headClass = 'jadwal-day-head-default';
                                $strataLabel = '';
                                if ($dayStratas->count() === 1) {
                                    $onlyStrata = $dayStratas->first();
                                    if (in_array($onlyStrata, ['s1', 's2', 's3'])) {
                                        $headClass = 'jadwal-day-head-' . $onlyStrata;
                                        $strataLabel = strtoupper($onlyStrata);
                                    }
                                }
                            @endphp
                            <div class="jadwal-day-card">
                                <div class="jadwal-day-head {{ $headClass }} d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-calendar-day mr-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center" style="gap: 6px;">
                                        @if($strataLabel)
                                            <span class="badge" style="background: rgba(255,255,255,0.22); color: #ffffff; border: 1px solid rgba(255,255,255,0.35); font-size: 0.72rem; padding: 4px 10px; border-radius: 999px;">
                                                Program {{ $strataLabel }}
                                            </span>
                                        @endif
                                        <span class="badge" style="background: rgba(255,255,255,0.22); color: #ffffff; border: 1px solid rgba(255,255,255,0.35); font-size: 0.72rem; padding: 4px 10px; border-radius: 999px;">
                                            {{ $items->count() }} Jadwal
                                        </span>
                                    </div>
                                </div>

                                @foreach($items as $item)
                                    @php $sc = strataColor($item->Strata ?? ''); @endphp
                                    <div class="d-flex jadwal-row position-relative" data-strata="{{ strtolower($item->Strata ?? '') }}" style="border-left: 4px solid {{ $sc['border'] }};">
                                        <div class="jadwal-row-time">
                                            <div class="time">{{ $item->waktu_sidang ? substr($item->waktu_sidang, 0, 5) : '-' }}</div>
                                            <div class="room">{{ $item->ruang_sidang ?? '-' }}</div>
                                            <div class="unit">FTTM</div>
                                        </div>
                                        <div style="flex-grow: 1;">
                                            <table>
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
                                        <div style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
                                            @php $strataKey = strtolower(trim($item->Strata ?? '')); @endphp
                                            <span class="jadwal-strata-badge jadwal-strata-badge-{{ in_array($strataKey, ['s1','s2','s3']) ? $strataKey : 'default' }}">
                                                {{ $sc['label'] }}{{ ($item->NAMA_PRODI ?? '') ? ' · ' . $item->NAMA_PRODI : '' }}
                                            </span>
                                            <a href="{{ session('auth_user.role') === 'Mahasiswa' ? '/mahasiswa/dashboard' : '/sidang/' . strtolower($item->Strata ?? 's1') }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Tabel Jadwal Sidang {{ tahapLabel($item->tahapan_sidang) }}">
                                                <i class="fas fa-table mr-1"></i>Tabel
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        @if(method_exists($cardsData, 'links'))
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center mb-2 mb-md-0">
                                <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">Tampilkan</label>
                                <select class="form-control form-control-sm column-search" style="width: auto; min-width: 70px;" onchange="changePerPage(this.value)">
                                    @foreach([10, 25, 50, 100] as $n)
                                        <option value="{{ $n }}" {{ (int) $cardsData->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
                                    @endforeach
                                </select>
                                <span class="ml-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">jadwal per halaman (Menampilkan {{ $cardsData->firstItem() }} - {{ $cardsData->lastItem() }} dari {{ $cardsData->total() }} jadwal)</span>
                            </div>
                            <div>
                                {{ $cardsData->fragment('card')->links() }}
                            </div>
                        </div>
                        @endif
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

        var strataColors = {
            's1': { border: '#3b82f6', bg: '#eff6ff', text: '#1d4ed8' },
            's2': { border: '#10b981', bg: '#ecfdf5', text: '#047857' },
            's3': { border: '#8b5cf6', bg: '#f5f3ff', text: '#6d28d9' }
        };
        var sc = strataColors[(data.strata || '').toLowerCase()] || { border: '#6b7280', bg: '#f9fafb', text: '#374151' };

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
                        '<span class="mhs-badge" style="background:' + sc.bg + '; color:' + sc.text + '; border-color:' + sc.border + ';">Strata: ' + data.strata + '</span>' +
                        (data.prodi && data.prodi !== '-' ? '<span class="mhs-badge">' + data.prodi + '</span>' : '') +
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

    // Tab activation from URL hash
    function activateTabFromHash() {
        var hash = window.location.hash;
        if (hash === '#card') {
            $('#jadwalTabs a[href="#card"]').tab('show');
        } else if (hash === '#calendar') {
            $('#jadwalTabs a[href="#calendar"]').tab('show');
        }
    }

    $(document).ready(function() {
        activateTabFromHash();
        $('#jadwalTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var targetHash = $(e.target).attr('href');
            if (history.replaceState) {
                history.replaceState(null, null, targetHash);
            } else {
                window.location.hash = targetHash;
            }
        });
    });

    window.addEventListener('hashchange', activateTabFromHash);

    function changePerPage(val) {
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', val);
        url.searchParams.set('page', '1');
        url.hash = 'card';
        window.location.href = url.toString();
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
        background: var(--mui-primary);
        color: white;
        padding: 0;
        margin: -16px -16px 0 -16px;
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
        border-bottom: 2px solid var(--mui-line);
        background: var(--mui-surface);
    }

    .mhs-name {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--mui-text);
        margin-bottom: 14px;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    
    .mhs-info-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .mhs-badge {
        display: inline-block;
        padding: 10px 18px;
        background: var(--mui-surface-soft);
        color: var(--mui-text);
        font-size: 1rem;
        font-weight: 600;
        border-radius: 8px;
        border: 1px solid var(--mui-line);
    }

    /* Judul Section */
    .detail-judul-section {
        padding: 28px 28px;
        background: var(--mui-surface);
        border-bottom: 2px solid var(--mui-line);
    }

    .section-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--mui-primary);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 14px;
    }
    
    .judul-content {
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--mui-text);
        padding: 20px;
        background: var(--mui-surface-soft);
        border-radius: 10px;
        border-left: 4px solid var(--mui-primary);
    }

    /* Info Grid */
    .detail-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        padding: 28px 28px 32px;
        background: var(--mui-surface);
    }

    .info-box {
        padding: 22px 20px;
        background: var(--mui-surface-soft);
        border-radius: 10px;
        border: 2px solid var(--mui-line);
        transition: all 0.3s ease;
    }

    .info-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(20, 98, 173, 0.12);
        border-color: var(--mui-primary);
    }

    .info-label {
        font-size: 0.8rem;
        color: var(--mui-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 10px;
    }

    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--mui-text);
        line-height: 1.3;
    }

    .info-value .badge {
        font-size: 1.05rem;
        padding: 8px 18px;
        font-weight: 700;
        border-radius: 8px;
    }
    
    /* Modal Header */
    #detailModal .modal-header {
        border-bottom: 2px solid var(--mui-line);
        padding: 20px 28px;
        background: var(--mui-surface);
    }

    #detailModal .modal-title {
        font-weight: 700;
        font-size: 1.35rem;
        color: var(--mui-text);
        letter-spacing: -0.3px;
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
    
    /* Action Section - Task #17 */
    .detail-action-section {
        padding: 20px 28px 28px;
        background: var(--mui-surface);
        border-top: 2px solid var(--mui-line);
    }

    .detail-action-section .btn-primary {
        background: var(--mui-primary);
        border: none;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .detail-action-section .btn-primary:hover {
        background: var(--mui-primary-strong);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(20, 98, 173, 0.3);
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