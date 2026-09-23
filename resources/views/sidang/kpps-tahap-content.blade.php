<div class="card-body tahap-container" id="kpps-tahap-container">
    @php
    function getAjuanDisplayStatusKpps($a) {
        $sl = $a->status_lulus ?? $a->STATUS_LULUS ?? null;
        if (!empty($sl) && strtolower($sl) !== 'diajukan') return ucfirst($sl);
        $mhs = $a->status_ajukan_mhs ?? $a->STATUS_AJUKAN_MHS ?? 't';
        if (empty($mhs) || $mhs === 't') return 'Belum Diajukan';
        $prodi = $a->status_ajukan_prodi ?? $a->STATUS_AJUKAN_PRODI ?? 't';
        if ($mhs === 'y' && (empty($prodi) || $prodi === 't')) return 'Diproses di TU Prodi';
        $kpps = $a->status_ajukan_kpps ?? $a->STATUS_AJUKAN_KPPS ?? 't';
        if (($kpps ?? null) === 'y') {
            $ajuanId = $a->id ?? null;
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
        $ajuanId = $a->id ?? null;
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
    @endphp
    <style>
        .tahap-container {
            color: #1e293b;
        }
        .tahap-container .text-muted {
            color: #64748b;
        }
        html:not(.dark-mode) .tahap-container table thead th,
        html:not(.dark-mode) .tahap-container table thead th span {
            color: #1e293b !important;
        }
        html:not(.dark-mode) .tahap-container table thead {
            background-color: #f8fafc !important;
        }
        html:not(.dark-mode) .tahap-container .text-danger,
        html:not(.dark-mode) .tahap-container .text-primary {
            color: #1e293b !important;
            text-decoration-color: transparent !important;
        }
        html:not(.dark-mode) .tahap-container .jadwal-date-link.text-primary {
            color: #0066cc !important;
        }
        html:not(.dark-mode) .tahap-container .jadwal-date-link.text-primary:hover {
            color: #004499 !important;
        }
        html:not(.dark-mode) .tahap-container table tbody td span {
            color: #1e293b !important;
            text-decoration-color: transparent !important;
        }
        html:not(.dark-mode) .tahap-container .nav-link.active,
        html:not(.dark-mode) .tahap-container .nav-item.active .nav-link {
            color: #0066cc !important;
            text-decoration: underline !important;
        }
        html.dark-mode .tahap-container .nav-link.active,
        html.dark-mode .tahap-container .nav-item.active .nav-link {
            color: #4da6ff !important;
            text-decoration: underline !important;
        }
        html.dark-mode .tahap-container,
        html.dark-mode .tahap-container .text-muted,
        html.dark-mode .tahap-container .text-primary,
        html.dark-mode .tahap-container .text-danger,
        html.dark-mode .tahap-container table th,
        html.dark-mode .tahap-container table td {
            color: #ffffff !important;
        }
        html.dark-mode .tahap-container table tbody tr {
            background-color: #1e293b !important;
        }
        html.dark-mode .tahap-container table tbody tr:nth-of-type(even) {
            background-color: #334155 !important;
        }
        html.dark-mode .tahap-container table thead,
        html.dark-mode .tahap-container table thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }
    </style>
    @php
        $nama = $ajuan->nama_mhs ?? '-';
        $nim = $ajuan->Nim ?? '-';
        $judulText = $ajuan->Judul ?? '';
    @endphp

    <div class="mb-4" style="line-height: 1.2;">
        <div class="d-flex">
            <div style="width: 60px;" class="font-weight-bold font-sm">Nama</div>
            <div class="mr-1">:</div>
            <div><span>{{ $nama }}</span></div>
        </div>
        <div class="d-flex mt-1">
            <div style="width: 60px;" class="font-weight-bold font-sm">NIM</div>
            <div class="mr-1">:</div>
            <div><span>{{ $nim }}</span></div>
        </div>
        <div class="d-flex mt-1">
            <div style="width: 60px;" class="font-weight-bold font-sm">Judul</div>
            <div class="mr-1">:</div>
            <div class="flex-grow-1"><span>{{ $judulText }}</span></div>
        </div>
    </div>

    <ul class="nav text-center w-100 d-flex justify-content-center border-bottom-0 mb-4" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold p-0 text-primary" id="persyaratan-tab" data-toggle="tab" href="#persyaratan" role="tab" style="text-decoration: underline;">Persyaratan</a>
        </li>
        <li class="nav-item mx-2 text-primary font-weight-bold p-0">|</li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold p-0 text-primary" id="tim-tab" data-toggle="tab" href="#tim" role="tab" style="text-decoration: underline;">Tim Pembimbing dan Penguji</a>
        </li>
        <li class="nav-item mx-2 text-primary font-weight-bold p-0">|</li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold p-0 text-primary" id="kpps-tab" data-toggle="tab" href="#kpps-voting" role="tab" style="text-decoration: underline;">Hasil Voting Tim KPPS</a>
        </li>
        <li class="nav-item mx-2 text-primary font-weight-bold p-0">|</li>
        <li class="nav-item">
                <a class="nav-link font-weight-bold p-0 text-primary" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" style="text-decoration: underline;">Jadwal dan Penilaian</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent" style="min-height: 250px;">
        <div class="tab-pane fade show active" id="persyaratan" role="tabpanel">
            <div class="text-muted font-weight-bold mb-2 ml-1">
                {{ strtolower($tahapan) === 'tahap i' ? 'Mengambil Mata Kuliah Ujian Kualifikasi' : 'Persyaratan Sidang ' . str_replace('tahap', 'Tahap', $tahapan) }}
            </div>
            <table class="table table-bordered table-sm text-center mb-4">
                <thead style="background-color: #6998d3; color: white;">
                    <tr>
                        <th style="width: 10%; color: #ffffff;">No</th>
                        <th style="width: 50%; color: #ffffff;">Persyaratan</th>
                        <th style="width: 20%; color: #ffffff;">Cek Kelengkapan</th>
                        <th style="width: 20%; color: #ffffff;">Upload Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @if($persyaratan && $persyaratan->count() > 0)
                        @foreach($persyaratan as $idx => $item)
                            @php $link = $item->LINK_FILE ?? null; @endphp
                            <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                <td>{{ $idx + 1 }}</td>
                                <td class="text-left">
                                    <span>{{ $item->PERSYARATAN ?? $item->NAMA_PERSYARATAN }}</span>
                                </td>
                                <td>
                                    @if(isset($item->STATUS_LENGKAP) && $item->STATUS_LENGKAP === 'y')
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        @if($link)
                                            <a href="{{ $link }}" target="_blank" class="mr-2 text-primary" style="font-size: 13px;">Lihat file</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr style="background-color: #dbe5f1;">
                            <td colspan="4" class="text-center text-muted">Belum ada persyaratan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="tim" role="tabpanel">
            <div class="text-muted font-weight-bold mb-2 ml-1" style="font-size: 14px;">
                Tim <span class="text-danger" style="text-decoration: underline;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline;">Penguji</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center mb-0" style="min-width: 750px;">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th style="width: 40px; color: #ffffff;">No</th>
                            <th style="min-width: 190px; white-space: nowrap; color: #ffffff;">NIP</th>
                            <th style="min-width: 280px; white-space: nowrap; color: #ffffff;">Nama</th>
                            <th style="color: #ffffff;">Keterangan</th>
                            <th style="color: #ffffff;">No SK</th>
                            <th style="color: #ffffff;">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($timSidang && $timSidang->count() > 0)
                            @foreach($timSidang as $idx => $tim)
                                <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                    <td>{{ $idx + 1 }}</td>
                                    <td style="white-space: nowrap;">{{ $tim->nip }}</td>
                                    <td class="text-left text-primary" style="text-decoration: underline; white-space: nowrap;">{{ $tim->Nama }}</td>
                                    <td class="text-danger" style="text-decoration: underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                    <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                    <td>
                                        @if($tim->FILE_PENELAAH)
                                            <a href="{{ $tim->FILE_PENELAAH }}" target="_blank" class="text-primary" style="font-size: 12px; text-decoration: underline;">Lihat file</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="background-color: #dbe5f1;">
                                <td colspan="6" class="text-center text-muted">Belum ada tim pembimbing dan penguji</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>

        <div class="tab-pane fade" id="kpps-voting" role="tabpanel">
            <div class="text-muted font-weight-bold mb-3" style="font-size: 14px;">
                Hasil Voting Tim <span class="text-danger" style="text-decoration: underline;">KPPS</span>
            </div>
            @php
                $appAjuanKpps = \App\Models\TAjuanSidang::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->first();
                $ajuanKodeProdi = $appAjuanKpps->KODE_PRODI ?? $appAjuanKpps->kode_prodi
                    ?? ($ajuan->KODE_PRODI ?? $ajuan->kode_prodi ?? null);
                $ajuanIdProdi = $appAjuanKpps->ID_PRODI ?? $appAjuanKpps->id_prodi
                    ?? ($ajuan->ID_PRODI ?? $ajuan->id_prodi ?? null);
                if (empty($ajuanKodeProdi) && !empty($ajuanIdProdi)) {
                    $ajuanKodeProdi = \Illuminate\Support\Facades\DB::table('t_prodi')
                        ->where('ID', $ajuanIdProdi)
                        ->value('KODE_PRODI');
                }
                $kppsQuery = \Illuminate\Support\Facades\DB::table('t_kpps as k')
                    ->leftJoin('t_app_ajuan_sidang as app', function($join) use ($appAjuanKpps) {
                        $join->on('k.ID_USER', '=', 'app.ID_USER')
                             ->where('app.ID_AJUAN_SIDANG', '=', $appAjuanKpps ? $appAjuanKpps->id : 0);
                    })
                    ->leftJoin('t_user as u', 'k.ID_USER', '=', 'u.ID')
                    ->where('k.STATUS_AKTIF', 'AKTIF');
                if (!empty($ajuanKodeProdi)) {
                    $kppsQuery->where('k.KODE_PRODI', $ajuanKodeProdi);
                } else {
                    $kppsQuery->whereRaw('1 = 0');
                }
                $kppsList = $kppsQuery
                    ->select(
                        \Illuminate\Support\Facades\DB::raw('MAX(k.NIP) as NIP'),
                        \Illuminate\Support\Facades\DB::raw('MAX(k.NAMA) as NAMA'),
                        \Illuminate\Support\Facades\DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT k.STATUS_TIM ORDER BY CASE WHEN k.STATUS_TIM = 'Ketua' THEN 1 WHEN k.STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END SEPARATOR ','), ',', 1) as STATUS_TIM"),
                        \Illuminate\Support\Facades\DB::raw('MAX(app.STATUS_APPROVE) as STATUS_APPROVE'),
                        \Illuminate\Support\Facades\DB::raw('MAX(app.USULAN_PERBAIKAN) as USULAN_PERBAIKAN'),
                        \Illuminate\Support\Facades\DB::raw('MAX(app.ALASAN_REJECT) as ALASAN_REJECT'),
                        \Illuminate\Support\Facades\DB::raw('CASE WHEN MAX(app.ID) IS NOT NULL THEN "Sudah Diajukan" ELSE "Belum Diajukan" END as STATUS_AJUAN')
                    )
                    ->groupBy('k.ID_USER')
                    ->get();
                $statusTimPriority = ['Ketua' => 1, 'Sekretaris' => 2];
                $kppsList = $kppsList->sortBy(function ($item) use ($statusTimPriority) {
                    $priority = $statusTimPriority[trim($item->STATUS_TIM ?? '')] ?? 3;
                    return str_pad($priority, 2, '0', STR_PAD_LEFT) . '|' . strtolower($item->NAMA ?? '');
                })->values();
            @endphp
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center mb-0">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th style="width: 6%; color: #ffffff;">No</th>
                            <th style="width: 16%; color: #ffffff;">NIP</th>
                            <th style="width: 28%; color: #ffffff;">Nama KPPS</th>
                            <th style="width: 14%; color: #ffffff;">Status Tim</th>
                            <th style="width: 16%; color: #ffffff;">Aksi</th>
                            <th style="width: 15%; color: #ffffff;">Status Approve</th>
                            <th style="width: 18%; color: #ffffff;">Alasan Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($kppsList->count() > 0)
                            @foreach($kppsList as $idx => $kpps)
                                <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $kpps->NIP ?? '-' }}</td>
                                    <td class="text-left">{{ $kpps->NAMA ?? '-' }}</td>
                                    <td>{{ $kpps->STATUS_TIM ?? '-' }}</td>
                                    <td>
                                        @if(trim((string) ($kpps->USULAN_PERBAIKAN ?? '')) !== '')
                                            <button type="button" class="btn btn-sm px-3 py-1"
                                                    style="font-size: 11px; border-radius: 4px; color: #003366; border-color: #003366; background: transparent; white-space: nowrap;"
                                                    data-isi="{{ htmlspecialchars($kpps->USULAN_PERBAIKAN, ENT_QUOTES) }}"
                                                    onmouseover="this.style.background='#003366'; this.style.color='#fff';"
                                                    onmouseout="this.style.background='transparent'; this.style.color='#003366';"
                                                    onclick="showKppsUsulanPerbaikan(this)">
                                                Lihat Usulan Perbaikan
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @php
                                        $stApprove = strtolower(trim((string) ($kpps->STATUS_APPROVE ?? '')));
                                        $isApproved = in_array($stApprove, ['t', 'y'], true);
                                        $isRejected = $stApprove === 'f';
                                    @endphp
                                    <td>
                                        @if($isApproved)
                                            <span class="badge bg-success" style="white-space: nowrap;">Sudah Di Approve</span>
                                        @elseif($isRejected)
                                            <span class="badge bg-danger" style="white-space: nowrap;">Ditolak</span>
                                        @else
                                            <span class="badge bg-danger" style="white-space: nowrap;">Belum Di Approve</span>
                                        @endif
                                    </td>
                                    <td class="text-left" style="font-size: 12px;">{{ trim((string) ($kpps->ALASAN_REJECT ?? '')) !== '' ? $kpps->ALASAN_REJECT : '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="background-color: #dbe5f1;">
                                <td colspan="7" class="text-center text-muted">Belum ada data KPPS</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="jadwal" role="tabpanel">
            @php $tahapLabelHeading = str_replace('tahap', 'Tahap', $tahapan); @endphp

            <div id="kppsJadwalList">
                <div class="text-muted font-weight-bold mb-2 ml-1">Jadwal Sidang {{ $tahapLabelHeading }}</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center mb-0">
                        <thead style="background-color: #6998d3; color: white;">
                            <tr>
                                <th style="width: 10%; color: #ffffff;">No</th>
                                <th style="width: 35%; color: #ffffff;">Jadwal</th>
                                <th style="width: 20%; color: #ffffff;">Status Lulus</th>
                                <th style="width: 35%; color: #ffffff;">Penilaian Seminar/Sidang</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($allAjuan && $allAjuan->count() > 0)
                                @foreach($allAjuan as $idx => $a)
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            @if($a->tgl_sidang)
                                                {{ \Carbon\Carbon::parse($a->tgl_sidang)->locale('id')->translatedFormat('l, d F Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ getAjuanDisplayStatusKpps($a) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm px-3 py-1" style="font-size: 12px; border-radius: 4px; color: #003366; border-color: #003366; background: transparent;" onmouseover="this.style.background='#003366'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#003366';" onclick="showKppsPenilaian()">Penilaian</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr style="background-color: #dbe5f1;">
                                    <td colspan="4" class="text-center text-muted">Belum ada jadwal sidang</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="kppsPenilaianForm" style="display: none;">
                <div class="text-muted font-weight-bold mb-2 ml-1">Form Penilaian Seminar/Sidang {{ $tahapLabelHeading }}</div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group row align-items-center mb-2">
                            <label class="col-sm-4 text-danger mb-0 px-1" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                            <div class="col-sm-8 px-1">
                                <select class="form-control form-control-sm select2-search" id="kppsPenilai" onchange="filterKppsPenilaian()">
                                    <option value="">-- Pilih Penilai --</option>
                                    @if($timSidang && $timSidang->count() > 0)
                                        @foreach($timSidang as $tim)
                                            @if(strtolower(trim($tim->status_tim_sidang ?? '')) === 'ketua sidang') @continue @endif
                                            <option value="{{ $tim->id }}">{{ $tim->Nama }} ({{ $tim->nip }})</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row align-items-center mb-2">
                            <label class="col-sm-4 text-danger mb-0 px-1" style="font-size: 13px; text-decoration: underline;">Form</label>
                            <div class="col-sm-8 px-1">
                                <select class="form-control form-control-sm select2-search" id="kppsForm" onchange="filterKppsPenilaian()">
                                    <option value="">-- Pilih Form --</option>
                                    @if($pointPenilaian && $pointPenilaian->count() > 0)
                                        @foreach($pointPenilaian as $form)
                                            <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center mb-0">
                        <thead style="background-color: #6998d3; color: white;">
                            <tr>
                                <th style="width: 8%; color: #ffffff;">No</th>
                                <th style="width: 40%; color: #ffffff;">Parameter Penilaian</th>
                                <th style="width: 12%; color: #ffffff;">Keterangan</th>
                                <th style="width: 20%; color: #ffffff;">Nilai (skor 1-5)</th>
                                <th style="width: 20%; color: #ffffff;">Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="kppsPenilaianBody">
                            <tr id="kppsPenilaianEmptyRow" style="background-color: #dbe5f1;">
                                <td colspan="5" class="text-center text-muted">Pilih Form untuk melihat parameter penilaian</td>
                            </tr>
                            @if($allPointPenilaian && $allPointPenilaian->count() > 0)
                                @foreach($allPointPenilaian as $idx => $point)
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                        <td>{{ $point->status_catatan ?? '-' }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                            @if($penilaian && $penilaian->count() > 0)
                                @foreach($penilaian as $idx => $pv)
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-row" data-id-penilai="{{ $pv->id_tim_sidang }}" data-no-form="{{ $pv->no_form }}" data-point-id="{{ $pv->id_penilaian }}">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger text-decoration-underline">{{ $pv->nama_penilaian }}</span></td>
                                        <td>{{ optional($pv->pointPenilaian)->STATUS_CATATAN ?? '-' }}</td>
                                        <td>{{ $pv->Nilai ?? '' }}</td>
                                        <td>{{ $pv->catatan ?? '' }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                </div>

            <script>
                function showKppsPenilaian() {
                    document.getElementById('kppsJadwalList').style.display = 'none';
                    document.getElementById('kppsPenilaianForm').style.display = 'block';
                    filterKppsPenilaian();
                    var btn = document.getElementById('kppsKembaliBtn');
                    if (btn) { btn.style.display = 'inline-block'; }
                }
                function hideKppsPenilaian() {
                    document.getElementById('kppsPenilaianForm').style.display = 'none';
                    document.getElementById('kppsJadwalList').style.display = 'block';
                    var btn = document.getElementById('kppsKembaliBtn');
                    if (btn) { btn.style.display = 'none'; }
                }
                function filterKppsPenilaian() {
                    var penilaiId = document.getElementById('kppsPenilai').value;
                    var noForm = document.getElementById('kppsForm').value;
                    var tbody = document.getElementById('kppsPenilaianBody');
                    var rows = tbody.querySelectorAll('tr.penilaian-row');
                    var emptyRow = document.getElementById('kppsPenilaianEmptyRow');
                    var hasVisible = false;

                    rows.forEach(function (row) {
                        var show = true;
                        var rowPenilai = row.getAttribute('data-id-penilai');
                        var rowNoForm = row.getAttribute('data-no-form');
                        var isReference = !rowPenilai;

                        if (!noForm) {
                            show = false;
                        } else if (rowNoForm !== noForm) {
                            show = false;
                        } else if (isReference) {
                            if (penilaiId) {
                                var rowPointId = row.getAttribute('data-point-id');
                                var existingRow = tbody.querySelector('tr.penilaian-row[data-id-penilai="' + penilaiId + '"][data-point-id="' + rowPointId + '"]');
                                if (existingRow) { show = false; }
                            }
                        } else {
                            if (!penilaiId || rowPenilai !== penilaiId) { show = false; }
                        }

                        row.style.display = show ? '' : 'none';
                        if (show) hasVisible = true;
                    });

                if (emptyRow) { emptyRow.style.display = hasVisible ? 'none' : ''; }
                renumberKppsColumn();
                }

                function renumberKppsColumn() {
                    var tbody = document.getElementById('kppsPenilaianBody');
                    if (!tbody) return;
                    var n = 0;
                    tbody.querySelectorAll('tr.penilaian-row').forEach(function (row) {
                        if (row.style.display === 'none') return;
                        var firstCell = row.querySelector('td');
                        if (firstCell) {
                            n++;
                            firstCell.textContent = n;
                        }
                    });
                }

                // Select2 untuk dropdown Penilai / Form (bisa dicari)
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(document).ready(function() {
                        jQuery('.select2-search', '#kpps-tahap-container').each(function() {
                            var $el = jQuery(this);
                            if ($el.data('select2')) return;
                            var opts = {
                                theme: 'bootstrap',
                                allowClear: true,
                                width: '100%'
                            };
                            var $modal = $el.closest('.modal');
                            if ($modal.length) opts.dropdownParent = $modal;
                            $el.select2(opts);
                        });
                    });
                }
            </script>
            @push('scripts')
            <script>
                if (window.jQuery && jQuery.fn.select2) {
                    jQuery(document).ready(function() {
                        jQuery('.select2-search', '#kpps-tahap-container').each(function() {
                            var $el = jQuery(this);
                            if ($el.data('select2')) return;
                            var opts = {
                                theme: 'bootstrap',
                                allowClear: true,
                                width: '100%'
                            };
                            var $modal = $el.closest('.modal');
                            if ($modal.length) opts.dropdownParent = $modal;
                            $el.select2(opts);
                        });
                    });
                }
            </script>
            @endpush
        </div>
    </div>
</div>
