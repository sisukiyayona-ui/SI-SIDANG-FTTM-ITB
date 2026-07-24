@extends('layouts.master')

@section('title', 'Jadwal Sidang - SI SIDANG FTTM ITB')
@section('page_title', 'Jadwal Sidang')

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
        if (!$status) return '<span class="badge badge-warning">Dalam Proses</span>';
        if (strtolower($status) === 'lulus') return '<span class="badge badge-success">Lulus</span>';
        if (strtolower($status) === 'tidak lulus') return '<span class="badge badge-danger">Tidak Lulus</span>';
        return '<span class="badge badge-warning">' . ucfirst($status) . '</span>';
    }

    function tahapLabel($tahapan) {
        return str_replace('tahap', 'Tahap ', $tahapan);
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
                                                                     'nama' => $ev->nama_mhs,
                                                                     'nim' => $ev->Nim,
                                                                     'judul' => $ev->Judul,
                                                                     'waktu' => $ev->waktu_sidang ?? '-',
                                                                     'ruang' => $ev->ruang_sidang ?? '-',
                                                                     'tahapan' => tahapLabel($ev->tahapan_sidang),
                                                                     'status' => $ev->status_lulus ? ucfirst($ev->status_lulus) : 'Dalam Proses',
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
                                    <div class="d-flex p-3 text-dark" style="background-color: {{ $rowColors[$c_idx % count($rowColors)] }}; {{ !$loop->last ? 'border-bottom: 1px solid #999;' : '' }}">
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
                                                    <td style="vertical-align: top;">Judul</td>
                                                    <td style="vertical-align: top;">:</td>
                                                    <td>{{ $item->Judul }}</td>
                                                </tr>
                                            </table>
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
    function showDetail(data) {
        var statusClass = 'badge-warning';
        if (data.status.toLowerCase() === 'lulus') statusClass = 'badge-success';
        else if (data.status.toLowerCase() === 'tidak lulus') statusClass = 'badge-danger';

        var html = '' +
            '<div class="card" style="border-left: 4px solid #6998d3;">' +
                '<div class="card-body">' +
                    '<div class="d-flex align-items-start">' +
                        '<div class="mr-3 text-center" style="min-width:70px;">' +
                            '<div class="badge badge-primary p-2">' + data.waktu + '</div>' +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<h5 class="mb-1">' + data.nama + '</h5>' +
                            '<p class="mb-1 text-muted">NIM: ' + data.nim + '</p>' +
                            '<p class="mb-1 text-muted">Strata: ' + data.strata + '</p>' +
                            '<hr>' +
                            '<p class="mb-1"><strong>Judul:</strong><br>' + data.judul + '</p>' +
                            '<hr>' +
                            '<p class="mb-1"><i class="fas fa-map-marker-alt mr-1"></i> ' + data.ruang + '</p>' +
                            '<p class="mb-1"><strong>Tahapan:</strong> ' + data.tahapan + '</p>' +
                            '<p class="mb-0"><strong>Status:</strong> <span class="badge ' + statusClass + '">' + data.status + '</span></p>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        document.getElementById('detailModalBody').innerHTML = html;
        $('#detailModal').modal('show');
    }
</script>
@endpush