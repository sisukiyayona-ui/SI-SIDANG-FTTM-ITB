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
                        @if($timSidang && $timSidang->where('status_tim_sidang', 'Pembimbing')->count() > 0)
                            @foreach($timSidang->where('status_tim_sidang', 'Pembimbing') as $idx => $pembimbing)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $pembimbing->nip }}</td>
                                    <td>Prof. Dr. <span class="text-danger text-decoration-underline">{{ $pembimbing->Nama }}</span>.</td>
                                    <td><span class="text-danger text-decoration-underline">{{ $pembimbing->keterangan ?? 'Ketua Pembimbing' }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Dummy based on screenshot --}}
                            <tr>
                                <td>1</td>
                                <td>12345678</td>
                                <td>Prof. Dr. <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Satria Bijaksana</span>.</td>
                                <td><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Ketua Pembimbing</span></td>
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
                            <td>1</td>
                            <td><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Nilai MK Ujian Kualifikasi</span></td>
                            <td>4</td>
                            <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex align-items-center mb-2" style="margin-left: 10px;">
                <span class="mr-4">Status Lulus</span>
                <div class="border d-flex align-items-center justify-content-center" style="width: 150px; height: 30px; font-size: 14px; font-weight: 500;">
                    {{ $ajuan->status_lulus ?? 'Lulus' }}
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
                    <a class="nav-link font-weight-bold p-0 text-primary" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" style="text-decoration: underline;">Jadwal dan Penilaian</a>
                </li>
                <li class="nav-item mx-2 text-primary font-weight-bold p-0">|</li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold p-0 text-primary" id="tim-tab" data-toggle="tab" href="#tim" role="tab" style="text-decoration: underline;">Tim Pembimbing dan Penguji</a>
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
                                <th style="width: 10%;">No</th>
                                <th style="width: 60%;"><span class="text-danger text-decoration-underline">Persyaratan</span></th>
                                <th style="width: 30%;">Upload <span class="text-white">Dokumen</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($persyaratan && $persyaratan->count() > 0)
                                @foreach($persyaratan as $idx => $item)
                                    @php $syaratId = $item->id_syarat_sidang ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">{{ $item->Persyaratan ?? $item->nama_persyaratan }}</span></td>
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
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" onchange="uploadFile(this, {{ $syaratId }}, '{{ $tahapan }}')">
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
                                    <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Lembar pengesahan formulir yang sudah ditandatangi pembimbing</span></td>
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                                <tr style="background-color: #e9eef6;">
                                    <td>2</td>
                                    <td class="text-left">Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">bimbingan</span></td>
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                                <tr style="background-color: #dbe5f1;">
                                    <td>3</td>
                                    <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Dokumen proposal penelitian yang sudah ditandatangi pembimbing</span></td>
                                    <td><button type="button" class="btn btn-light bg-white border py-0 px-2 text-dark"><i class="fas fa-upload" style="font-size: 14px;"></i></button></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        <button type="button" class="btn btn-outline-dark px-4 py-1 mr-5 bg-white text-danger" style="font-size: 14px; text-decoration: underline; text-decoration-color: red;" onclick="alert('Data berhasil disimpan!')">Simpan</button>
                        <i class="fas fa-arrow-right text-primary fa-2x mr-4" style="cursor: pointer;" title="Ajukan" onclick="alert('Data berhasil diajukan!')"></i>
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
                            <i class="fas fa-plus fa-lg text-dark" style="cursor: pointer;" onclick="document.getElementById('jadwalList').style.display='none'; document.getElementById('jadwalForm').style.display='block';"></i>
                        </div>
                        <table class="table table-bordered table-sm text-center">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 60%;"><span class="text-danger text-decoration-underline">Jadwal</span></th>
                                    <th style="width: 30%;">Status Lulus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($ajuan) && $ajuan->tgl_sidang)
                                    <tr style="background-color: #dbe5f1;">
                                        <td>1</td>
                                        <td><span class="text-primary text-decoration-underline">{{ \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') }}</span></td>
                                        <td>{{ $ajuan->status_lulus ?? 'Dalam Proses' }}</td>
                                    </tr>
                                @else
                                    <tr style="background-color: #dbe5f1;">
                                        <td colspan="3" class="text-center text-muted">Belum ada jadwal</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Form Penjadwalan & Penilaian --}}
                    <div id="jadwalForm" style="display: none;">
                        <div class="row">
                            <div class="col-md-5" style="border-right: 1px solid #ddd;">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Jadwal Sidang</span> {{ str_replace('tahap', 'Tahap', $tahapan) }}
                                </div>
                                <div class="form-group row align-items-center mb-2 px-1">
                                    <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Tanggal</label>
                                    <div class="col-sm-8 px-2">
                                        <input type="text" class="form-control form-control-sm border-dark rounded-0" value="{{ isset($ajuan) ? $ajuan->tgl_sidang : '' }}">
                                    </div>
                                </div>
                                <div class="form-group row align-items-center mb-2 px-1">
                                    <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Waktu</label>
                                    <div class="col-sm-8 px-2">
                                        <input type="text" class="form-control form-control-sm border-dark rounded-0" value="{{ isset($ajuan) ? $ajuan->waktu_sidang : '' }}">
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
                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Ajukan</button>
                                    <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                </div>
                            </div>
                            
                            <div class="col-md-7">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline;">Penilaian</span>
                                </div>
                                <div class="form-group row align-items-center mb-2">
                                    <label class="col-sm-2 text-danger mb-0 px-1 text-right" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                                    <div class="col-sm-4 px-1">
                                        <select class="form-control form-control-sm border-dark rounded-0">
                                            @if(isset($timSidang) && $timSidang->count() > 0)
                                                @foreach($timSidang as $tim)
                                                    <option value="{{ $tim->nip }}">{{ $tim->Nama }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <label class="col-sm-2 mb-0 px-1 text-center" style="font-size: 13px; color: #555;">Form</label>
                                    <div class="col-sm-2 px-1">
                                        <input type="text" class="form-control form-control-sm border-dark rounded-0">
                                    </div>
                                    <div class="col-sm-2 pl-0 d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-dark py-0 px-1 bg-white" style="font-size: 12px; border-radius: 0;">Cari</button>
                                        <a href="#" class="text-dark ml-2" style="font-size: 11px; text-decoration: underline;">BA Sidang</a>
                                    </div>
                                </div>
                                
                                <table class="table table-bordered table-sm text-center mt-3 mb-0" style="table-layout: fixed; border-color: #fff;">
                                    <thead style="background-color: #6998d3; color: white;">
                                        <tr>
                                            <th style="width: 10%;">No</th>
                                            <th style="width: 45%;"><span class="text-danger text-decoration-underline" style="text-decoration-color: red;">Parameter Penilaian</span></th>
                                            <th style="width: 15%;">Nilai</th>
                                            <th style="width: 30%;">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($penilaian) && $penilaian->count() > 0)
                                            @foreach($penilaian as $idx => $nilai)
                                            <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                                <td>{{ $idx + 1 }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $nilai->nama_penilaian }}</span></td>
                                                <td>{{ $nilai->Nilai }}</td>
                                                <td>{{ $nilai->catatan }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                        <tr style="background-color: #dbe5f1; height: 30px;">
                                            <td></td><td></td><td></td><td></td>
                                        </tr>
                                        <tr style="background-color: #e9eef6; height: 30px;">
                                            <td></td><td></td><td></td><td></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: TIM PEMBIMBING & PENGUJI --}}
                <div class="tab-pane fade" id="tim" role="tabpanel">
                    <div class="text-muted font-weight-bold mb-2 ml-1" style="font-size: 14px;">
                        Tim <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penguji</span>
                    </div>
                    <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                    <table class="table table-bordered table-sm text-center mb-0">
                        <thead style="background-color: #6998d3; color: white;">
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 25%;">NIP</th>
                                <th style="width: 35%;">Nama</th>
                                <th style="width: 30%;"><span class="text-danger text-decoration-underline" style="text-decoration-color: red;">Keterangan</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($timSidang) && $timSidang->count() > 0)
                                @foreach($timSidang as $idx => $tim)
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td>{{ $tim->nip }}</td>
                                        <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                        <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr style="background-color: #dbe5f1;">
                                    <td colspan="4" class="text-center text-muted">Belum ada tim penguji</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Event delegation for dynamically loaded content
document.addEventListener('click', function(e) {
    if (e.target.closest('.upload-btn')) {
        const btn = e.target.closest('.upload-btn');
        const idSyarat = btn.getAttribute('data-id');
        const fileInput = document.getElementById('file-' + idSyarat);
        console.log('Upload button clicked, id:', idSyarat, 'file input:', fileInput);
        if (fileInput) {
            fileInput.click();
        } else {
            console.error('File input not found for id:', idSyarat);
        }
    }
});

function uploadFile(input, idSyarat, tahapan) {
    console.log('uploadFile called', idSyarat, tahapan);
    if (!input.files || input.files.length === 0) {
        console.log('No file selected');
        return;
    }

    console.log('File selected:', input.files[0].name);

    let formData = new FormData();
    formData.append('file', input.files[0]);
    formData.append('id_syarat_sidang', idSyarat);
    formData.append('tahapan_sidang', tahapan);
    formData.append('_token', '{{ csrf_token() }}');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route('mahasiswa.upload-persyaratan') }}', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function () {
        console.log('XHR response:', xhr.status, xhr.responseText);
        if (xhr.status === 200) {
            try {
                let data = JSON.parse(xhr.responseText);
                if (data.success) {
                    alert('File berhasil diupload');
                    location.reload();
                } else {
                    alert(data.message || 'Gagal upload file');
                }
            } catch (e) {
                alert('Error parsing response: ' + e.message);
            }
        } else {
            alert('Upload failed with status: ' + xhr.status);
        }
    };

    xhr.onerror = function () {
        console.log('XHR error occurred');
        alert('Upload error occurred');
    };

    xhr.send(formData);
}
</script>

@if(!request()->ajax() && !request()->has('id_judul'))
@endsection
@endif