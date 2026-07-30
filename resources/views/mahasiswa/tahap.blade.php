@if(!request()->ajax() && !request()->has('id_judul'))
@extends('layouts.master')
@section('title', 'Detail Tahap - SI SIDANG FTTM ITB')
@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('mahasiswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">{{ $tahapan }}</li>
    </ol>
@endsection
@section('content')
@endif

<div class="card-body tahap-container">
    <style>
        .tahap-container {
            color: #1e293b;
        }
        .tahap-container .text-muted {
            color: #64748b;
        }

        /* Light mode overrides */
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
        html:not(.dark-mode) .tahap-container table tbody td span {
            color: #1e293b !important;
            text-decoration-color: transparent !important;
        }

        /* Tabs styling */
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

        /* Dark mode overrides */
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
        $isTahap1 = strtolower($tahapan) === 'tahap i';
        $nama = $ajuan->nama_mhs ?? session('auth_user.nama_lengkap');
        $nim = $ajuan->Nim ?? session('auth_user.nip_nim');
        $judulText = $ajuan->Judul ?? ($idJudul ? \App\Models\TJudul::find($idJudul)->Judul : '');
    @endphp

    {{-- Info Utama --}}
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

    {{-- Container bergaris lengkung --}}
    <div style="padding: 30px;">
        @if($isTahap1)
            {{-- VIEW TAHAP I --}}
            <div class="font-weight-bold mb-2 p-0 text-muted" style="margin-left: 10px;">Pembimbing</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center mb-4">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 25%;">NIP</th>
                            <th style="width: 45%;">Nama</th>
                            <th style="width: 25%;"><span class="text-danger text-decoration-underline">Keterangan</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $pembimbingList = $timSidang ? $timSidang->filter(fn($t) => str_contains($t->status_tim_sidang ?? '', 'Pembimbing')) : collect(); @endphp
                        @if($pembimbingList->count() > 0)
                            @foreach($pembimbingList as $idx => $pembimbing)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $pembimbing->nip }}</td>
                                    <td><span class="text-danger text-decoration-underline">{{ $pembimbing->Nama }}</span></td>
                                    <td><span class="text-danger text-decoration-underline">{{ $pembimbing->keterangan ?? 'Ketua Pembimbing' }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-muted">Belum ada data pembimbing</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="font-weight-bold mb-2 p-0 text-muted" style="margin-left: 10px;">Nilai</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center mb-4">
                    <thead style="background-color: #6998d3; color: white;">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="width: 45%;"><span class="text-danger text-decoration-underline">Parameter Penilaian</span></th>
                            <th style="width: 15%;"><span class="text-danger text-decoration-underline">Nilai</span></th>
                            <th style="width: 35%;"><span class="text-danger text-decoration-underline">Catatan</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($penilaian && $penilaian->count() > 0)
                            @foreach($penilaian as $idx => $nilai)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td><span class="text-danger text-decoration-underline">{{ $nilai->nama_penilaian }}</span></td>
                                <td>{{ $nilai->Nilai }}</td>
                                <td>{{ $nilai->catatan }}</td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-muted">Belum ada data penilaian</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex align-items-center mb-2" style="margin-left: 10px;">
                <span class="mr-4">Status Lulus</span>
                <div class="border d-flex align-items-center justify-content-center" style="width: 150px; height: 30px; font-size: 14px; font-weight: 500;">
                    {{ $ajuan->status_lulus ?? 'Belum diajukan' }}
                </div>
            </div>
            
        @else
            {{-- VIEW TAHAP II & LAINNYA --}}
            <!-- Nav Tabs -->
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
                    <a class="nav-link font-weight-bold p-0 text-primary" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" style="text-decoration: underline;">Jadwal dan Penilaian</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent" style="min-height: 250px;">
                {{-- TAB: PERSYARATAN --}}
                <div class="tab-pane fade show active" id="persyaratan" role="tabpanel">
                    <div class="text-muted font-weight-bold mb-2 ml-1">
                        Persyaratan Sidang <span class="text-danger text-decoration-underline">{{ str_replace('tahap', 'Tahap', $tahapan) }}</span>
                    </div>

                    <table class="table table-bordered table-sm text-center mb-4">
                        <thead style="background-color: #6998d3; color: white;">
                            <tr>
                                <th style="width: 10%; color: #ffffff;">No</th>
                                <th style="width: {{ $tahapan === 'tahap II' ? '70%' : '50%' }}; color: #ffffff;"><span class="text-danger text-decoration-underline">Persyaratan</span></th>
                                @if($tahapan !== 'tahap II')
                                <th style="width: 20%; color: #ffffff;"><span class="text-danger text-decoration-underline">Cek Kelengkapan</span></th>
                                @endif
                                <th style="width: 20%; color: #ffffff;">Upload Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($persyaratan && $persyaratan->count() > 0)
                                @foreach($persyaratan as $idx => $item)
                                    @php $syaratId = $item->id_syarat_sidang ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">{{ $item->Persyaratan ?? $item->nama_persyaratan }}</span></td>
                                        @if($tahapan !== 'tahap II')
                                        <td>
                                            <input type="checkbox" {{ (isset($item->status_lengkap) && $item->status_lengkap === 'y') || (isset($item->STATUS_LENGKAP) && $item->STATUS_LENGKAP === 'y') ? 'checked' : '' }} data-syarat-id="{{ $syaratId }}" disabled>
                                        </td>
                                        @endif
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                @if(isset($item->link_file) && $item->link_file)
                                                    <a href="{{ $item->link_file }}" target="_blank" class="mr-2 text-primary" style="font-size: 13px;" id="link-{{ $syaratId }}">Lihat file</a>
                                                    <span id="check-{{ $syaratId }}" class="mr-2 text-success"><i class="fas fa-check-circle"></i></span>
                                                @else
                                                    <a href="#" target="_blank" class="mr-2 d-none text-primary" style="font-size: 13px;" id="link-{{ $syaratId }}">Lihat file</a>
                                                    <span id="check-{{ $syaratId }}" class="mr-2 text-success d-none"><i class="fas fa-check-circle"></i></span>
                                                @endif
                                                <div class="upload-container" style="position: relative; width: 34px;">
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" accept=".pdf" onchange="uploadFile(this, {{ $syaratId }}, '{{ $tahapan }}')">
                                                    <button type="button" id="btn-{{ $syaratId }}" class="btn btn-light bg-white border py-0 px-2 text-dark upload-btn" data-id="{{ $syaratId }}"><i class="fas fa-upload" style="font-size: 14px;"></i></button>
                                                    <div id="progress-{{ $syaratId }}" class="progress mt-1 d-none" style="height: 4px; position: absolute; bottom: -8px; left: 0; right: 0;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr style="background-color: #dbe5f1;">
                                    <td>1</td>
                                    <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Lulus ujian persiapan (tahap I)</span></td>
                                    @if($tahapan !== 'tahap II')
                                    <td><input type="checkbox" disabled></td>
                                    @endif
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                                <tr style="background-color: #e9eef6;">
                                    <td>2</td>
                                    <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing</span></td>
                                    @if($tahapan !== 'tahap II')
                                    <td><input type="checkbox" disabled></td>
                                    @endif
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                                <tr style="background-color: #dbe5f1;">
                                    <td>3</td>
                                    <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing</span></td>
                                    @if($tahapan !== 'tahap II')
                                    <td><input type="checkbox" disabled></td>
                                    @endif
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-primary px-4 py-1" style="font-size: 13px; border-radius: 0;" onclick="simpanPersyaratan()">Simpan</button>
                    </div>
                </div>

                {{-- TAB: TIM PEMBIMBING DAN PENGUJI --}}
                <div class="tab-pane fade" id="tim" role="tabpanel">
                    <div class="row">
                        {{-- KANAN: TABEL TIM (form kiri disembunyikan untuk mahasiswa) --}}
                        <div class="col-md-12">
                            <div class="text-muted font-weight-bold mb-2">
                                Tim <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penguji</span>
                            </div>
                            <table class="table table-bordered table-sm text-center">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 10%; color: #ffffff;">No</th>
                                        <th style="width: 25%; color: #ffffff;">NIP</th>
                                        <th style="width: 30%; color: #ffffff;">Nama</th>
                                        <th style="width: 25%; color: #ffffff;">Keterangan</th>
                                        <th style="color: #ffffff;">No SK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($timSidang) && $timSidang->count() > 0)
                                        @foreach($timSidang as $idx => $tim)
                                        <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $tim->NIP }}</td>
                                            <td class="text-left">{{ $tim->NAMA }}</td>
                                            <td>{{ $tim->STATUS_TIM_SIDANG }}</td>
                                            <td>{{ $tim->sk->NO_SK ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr style="background-color: #dbe5f1;">
                                        <td colspan="5" class="text-center text-muted">Belum ada tim penguji</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- TAB: JADWAL & PENILAIAN --}}
                <div class="tab-pane fade" id="jadwal" role="tabpanel">
                    {{-- Jadwal List Table --}}
                    <div id="jadwalList">
                        <div class="d-flex justify-content-between align-items-center mb-2 mx-1">
                            <div class="text-muted font-weight-bold">
                                Jadwal Sidang {{ str_replace('tahap', 'Tahap', $tahapan) }}
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" {{ isset($ajuan) && $ajuan->status_lulus === 'lulus' ? 'disabled' : '' }} onclick="openJadwalForm()"><i class="fas fa-plus"></i> Tambah</button>
                        </div>
                        <table class="table table-bordered table-sm text-center">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 8%;">No</th>
                                    <th style="width: 35%;">Jadwal</th>
                                    <th style="width: 25%;">Status Lulus</th>
                                    <th style="width: 32%;">Penilaian Seminar/Sidang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($ajuan) && $ajuan->tgl_sidang)
                                    <tr style="background-color: #dbe5f1;">
                                        <td>1</td>
                                        <td>
                                            <span class="text-primary text-decoration-underline" style="cursor: pointer;"
                                                  onclick="document.getElementById('jadwalList').style.display='none'; document.getElementById('jadwalForm').style.display='block';">
                                                {{ \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') }}
                                            </span>
                                        </td>
                                        <td>{{ $ajuan->status_lulus ?? 'Dalam Proses' }}</td>
                                        <td>
                                            <a href="#" style="text-decoration: none; color: #0066cc;"
                                                onclick="event.preventDefault(); document.getElementById('jadwalList').style.display='none'; document.getElementById('penilaianView').style.display='block'; document.getElementById('penilaiViewSelect').value=''; document.getElementById('formViewSelect').value=''; filterPenilaianView();">Penilaian</a>
                                        </td>
                                    </tr>
                                @else
                                    <tr style="background-color: #dbe5f1;">
                                        <td colspan="4" class="text-center text-muted">Belum ada jadwal</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Form Penjadwalan --}}
                    <div id="jadwalForm" style="display: none;">
                        <div class="text-muted mb-3" style="font-size: 13px;">
                            Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Jadwal Sidang</span> {{ str_replace('tahap', 'Tahap', $tahapan) }}
                        </div>
                        <div class="form-group row align-items-center mb-2 px-1">
                            <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Tanggal</label>
                            <div class="col-sm-8 px-2">
                                <input type="date" id="f_tgl_sidang" class="form-control form-control-sm border-dark rounded-0" value="{{ isset($ajuan) ? $ajuan->tgl_sidang : '' }}">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-2 px-1">
                            <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Waktu</label>
                            <div class="col-sm-8 px-2">
                                <input type="time" id="f_waktu_sidang" class="form-control form-control-sm border-dark rounded-0" value="{{ isset($ajuan) ? $ajuan->waktu_sidang : '' }}">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-2 px-1">
                            <label class="col-sm-4 mb-0" style="font-size: 13px; color: #555;">Ruang</label>
                            <div class="col-sm-8 px-2">
                                <input type="text" class="form-control form-control-sm border-dark rounded-0 bg-light" value="{{ isset($ajuan) ? $ajuan->ruang_sidang : '' }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-3 px-1">
                            <label class="col-sm-4 mb-0" style="font-size: 13px; color: #555;">Status Lulus</label>
                            <div class="col-sm-8 px-2">
                                <input type="text" class="form-control form-control-sm border-dark rounded-0 bg-light" value="{{ isset($ajuan) ? $ajuan->status_lulus : '' }}" disabled>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0" style="font-size: 12px; border-radius: 0;" onclick="document.getElementById('jadwalList').style.display='block'; document.getElementById('jadwalForm').style.display='none';">&larr; Kembali</button>
                            <div>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;" onclick="ajukanProdi('{{ $idJudul }}', '{{ $tahapan }}')">Ajukan Prodi</button>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;" onclick="saveJadwal()">Simpan</button>
                            </div>
                        </div>
                    </div>

                    {{-- Penilaian View (read-only, with filter) --}}
                    <div id="penilaianView" style="display: none;">
                        <div class="text-muted font-weight-bold mb-3">
                            Form <span class="text-danger" style="text-decoration: underline;">Penilaian</span> Seminar/Sidang {{ str_replace('tahap', 'Tahap', $tahapan) }}
                        </div>
                        <div class="row align-items-center mb-2">
                            <div class="col-md-4">
                                <div class="form-group row align-items-center mb-0 px-1">
                                    <label class="col-sm-4 text-danger mb-0 pr-0" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                                    <div class="col-sm-8 px-1">
                                        <select class="form-control form-control-sm border-dark rounded-0" id="penilaiViewSelect" onchange="filterPenilaianView()">
                                            <option value="">-- Pilih Penilai --</option>
                                            @if(isset($timSidang) && $timSidang->count() > 0)
                                                @foreach($timSidang as $tim)
                                                    <option value="{{ $tim->id }}">{{ $tim->Nama }} - ({{ $tim->status_tim_sidang }})</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group row align-items-center mb-0 px-1">
                                    <label class="col-sm-4 text-danger mb-0 pr-0" style="font-size: 13px; text-decoration: underline;">Form</label>
                                    <div class="col-sm-8 px-1">
                                        <select class="form-control form-control-sm border-dark rounded-0" id="formViewSelect" onchange="filterPenilaianView()">
                                            <option value="">-- Pilih Form --</option>
                                            @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                                @foreach($pointPenilaian as $form)
                                                    <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 text-right">
                                <button type="button" class="btn btn-outline-dark px-3 py-1" style="font-size: 13px; border-radius: 0;" onclick="window.open('#'); return false;">BA Sidang</button>
                            </div>
                        </div>
                        
                        <table class="table table-bordered table-sm text-center mt-3 mb-3" style="table-layout: fixed; border-color: #fff;">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 8%;">No</th>
                                    <th style="width: 25%;">Parameter Penilaian</th>
                                    <th style="width: 20%;">Keterangan</th>
                                    <th style="width: 12%;">Nilai (skor 1-5)</th>
                                    <th style="width: 35%;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="penilaianViewBody">
                                <tr id="penilaianViewEmptyRow" style="background-color: #dbe5f1;">
                                    <td colspan="5" class="text-center text-muted">Pilih Form untuk melihat parameter penilaian</td>
                                </tr>
                                @if(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                    @php $rowNum = 0; @endphp
                                    @foreach($allPointPenilaian as $point)
                                        @php
                                            $existingRecords = (isset($penilaian) && $penilaian->count() > 0) ? $penilaian->where('id_penilaian', $point->id) : collect();
                                        @endphp
                                        @php $rowNum++; @endphp
                                        <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-view-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                            <td>{{ $rowNum }}</td>
                                            <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                            <td class="text-left"><span class="text-muted" style="font-size: 13px;">{{ $point->keterangan ?? '-' }}</span></td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        @foreach($existingRecords as $existing)
                                            @php $rowNum++; @endphp
                                            <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-view-row" data-id-penilai="{{ $existing->id_tim_sidang }}" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                                <td>{{ $loop->parent->index + 1 }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                <td class="text-left"><span class="text-muted" style="font-size: 13px;">{{ $point->keterangan ?? '-' }}</span></td>
                                                <td>{{ $existing->Nilai }}</td>
                                                <td>{{ $existing->catatan }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('penilaianView').style.display='none'; document.getElementById('jadwalList').style.display='block';"><i class="fas fa-arrow-left"></i> Kembali</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

<script>
var persyaratanFiles = {};

function uploadFile(input, idSyarat, tahapan) {
    if (!input.files || input.files.length === 0) return;
    var file = input.files[0];

    var ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'pdf') {
        showToast('error', 'Hanya file PDF yang diizinkan');
        input.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        showToast('error', 'Maksimal ukuran file 2 MB');
        input.value = '';
        return;
    }

    persyaratanFiles[idSyarat] = file;

    var nameEl = document.getElementById('file-name-' + idSyarat);
    if (nameEl) nameEl.textContent = file.name;

    var progressEl = document.getElementById('progress-' + idSyarat);
    if (progressEl) {
        progressEl.classList.remove('d-none');
        var bar = progressEl.querySelector('.progress-bar');
        if (bar) {
            bar.style.width = '0%';
            setTimeout(function() { bar.style.width = '60%'; }, 100);
            setTimeout(function() { bar.style.width = '100%'; }, 400);
            setTimeout(function() { progressEl.classList.add('d-none'); }, 900);
        }
    }

    var linkEl = document.getElementById('link-' + idSyarat);
    if (linkEl) {
        linkEl.href = URL.createObjectURL(file);
        linkEl.classList.remove('d-none');
    }
    var checkEl = document.getElementById('check-' + idSyarat);
    if (checkEl) checkEl.classList.remove('d-none');

    input.value = '';
}

function simpanPersyaratan() {
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('id_judul', '{{ $idJudul }}');
    formData.append('tahapan_sidang', '{{ $tahapan }}');

    Object.keys(persyaratanFiles).forEach(function(id) {
        formData.append('files[' + id + ']', persyaratanFiles[id]);
    });

    var btn = document.querySelector('.btn-primary[onclick*="simpanPersyaratan"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }

    fetch('{{ route('mahasiswa.save-all-persyaratan') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('success', 'Persyaratan berhasil disimpan');
            persyaratanFiles = {};
            if (typeof showTahapForm === 'function') {
                setTimeout(function() { showTahapForm('{{ $tahapan }}', '{{ $idJudul }}', 'persyaratan'); }, 800);
            } else {
                location.reload();
            }
        } else {
            showToast('error', data.message || 'Gagal menyimpan persyaratan');
        }
    })
    .catch(function(error) {
        showToast('error', 'Terjadi kesalahan');
    })
    .finally(function() {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan'; }
    });
}

function filterPenilaianView() {
    var penilaiId = document.getElementById('penilaiViewSelect').value;
    var noForm = document.getElementById('formViewSelect').value;
    var tbody = document.getElementById('penilaianViewBody');
    var dataRows = tbody.querySelectorAll('tr.penilaian-view-row');
    var emptyRow = document.getElementById('penilaianViewEmptyRow');
    var hasVisible = false;

    dataRows.forEach(function(row) {
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
                var existingRow = tbody.querySelector('tr.penilaian-view-row[data-id-penilai="' + penilaiId + '"][data-point-id="' + rowPointId + '"]');
                if (existingRow) {
                    show = false;
                }
            }
        } else {
            if (!penilaiId || rowPenilai !== penilaiId) {
                show = false;
            }
        }

        row.style.display = show ? '' : 'none';
        if (show) hasVisible = true;
    });

    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
}

function openJadwalForm() {
    document.getElementById('jadwalList').style.display = 'none';
    document.getElementById('jadwalForm').style.display = 'block';
    document.getElementById('f_tgl_sidang').value = '';
    document.getElementById('f_waktu_sidang').value = '';
}

function saveJadwal() {
    var tgl = document.getElementById('f_tgl_sidang').value;
    var waktu = document.getElementById('f_waktu_sidang').value;
    if (!tgl || !waktu) {
        showToast('error', 'Tanggal dan Waktu harus diisi');
        return;
    }
    fetch('{{ route("sidang.jadwal-sidang.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            id_judul: '{{ $idJudul }}',
            tahapan_sidang: '{{ $tahapan }}',
            tgl_sidang: tgl,
            waktu_sidang: waktu
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('success', data.message || 'Jadwal sidang berhasil disimpan');
        } else {
            showToast('error', data.error || 'Gagal menyimpan jadwal sidang');
        }
    })
    .catch(function(err) {
        showToast('error', 'Terjadi kesalahan: ' + err);
    });
}

document.addEventListener('click', function(e) {
    if (e.target.closest('.upload-btn')) {
        const btn = e.target.closest('.upload-btn');
        const idSyarat = btn.getAttribute('data-id');
        const fileInput = document.getElementById('file-' + idSyarat);
        if (fileInput) {
            fileInput.click();
        }
    }
});

function ajukanProdi(idJudul, tahapan) {
    if (!confirm('Ajukan jadwal sidang ke Program Studi?')) return;
    fetch('/mahasiswa/ajukan-prodi', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id_judul: idJudul, tahapan_sidang: tahapan })
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('success', data.message || 'Berhasil diajukan ke Prodi');
        } else {
            showToast('error', data.error || 'Gagal mengajukan');
        }
    })
    .catch(function(err) {
        showToast('error', 'Error: ' + err);
    });
}
</script>
</div>
@if(!request()->ajax() && !request()->has('id_judul'))
@endsection
@endif