<div class="card-body tahap-container">
    <style>
        .tahap-container {
            color: #1e293b;
        }
        .tahap-container .text-muted {
            color: #64748b;
        }
        
        /* Light mode global overrides */
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

        /* Dark mode global overrides */
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

    <!-- Modal Tambah No SK -->
    <div class="modal fade" id="skModal" tabindex="-1" role="dialog" aria-labelledby="skModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="skForm" method="POST" action="{{ route('sidang.sk.store') }}" onsubmit="event.preventDefault(); window.submitSkForm(event)">
                    @csrf
                    <input type="hidden" name="id_judul" id="skIdJudul" value="{{ $idJudul }}">
                    <input type="hidden" name="tahapan_sidang" id="skTahapan" value="{{ $tahapan }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="skModalLabel"><i class="fas fa-file-alt mr-2"></i>Tambah No SK</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>No SK</label>
                            <input type="text" name="no_sk" class="form-control" placeholder="Masukkan No SK" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <div style="padding: 30px;">
        @if($isTahap1)
            {{-- VIEW TAHAP I - WITH TABS FOR TU PRODI/ADMIN/FS --}}
            @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
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
                    <a class="nav-link font-weight-bold p-0 text-primary" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" style="text-decoration: underline;">Jadwal Sidang & Penilaian</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent" style="min-height: 250px;">
                {{-- TAB: PERSYARATAN --}}
                <div class="tab-pane fade show active" id="persyaratan" role="tabpanel">
                    <div class="text-muted font-weight-bold mb-2 ml-1">
                        Mengambil Mata Kuliah Ujian Kualifikasi
                    </div>
                    <table class="table table-bordered table-sm text-center mb-4">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th style="width: 10%;">No</th>
                                <th style="width: 50%;">Persyaratan</th>
                                <th style="width: 20%;">Cek Kelengkapan</th>
                                <th style="width: 20%;">Upload File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($persyaratan && $persyaratan->count() > 0)
                                @foreach($persyaratan as $idx => $item)
                                    @php $syaratId = $item->id_syarat_sidang ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span>{{ $item->Persyaratan ?? $item->nama_persyaratan }}</span></td>
                                        <td>
                                            <input type="checkbox" {{ isset($item->status_kelengkapan) && $item->status_kelengkapan ? 'checked' : '' }} onchange="window.updateKelengkapan({{ $syaratId }}, this.checked)">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center flex-column" id="upload-wrapper-{{ $syaratId }}">
                                                <div class="d-flex align-items-center mb-1">
                                                    <a href="{{ isset($item->link_file) && $item->link_file ? $item->link_file : '#' }}" target="_blank" class="btn btn-sm btn-info text-white mr-2 {{ isset($item->link_file) && $item->link_file ? '' : 'd-none' }}" id="link-{{ $syaratId }}" title="Lihat Dokumen">
                                                        <i class="fas fa-eye mr-1"></i> Lihat
                                                    </a>
                                                    <label for="file-{{ $syaratId }}" class="btn btn-sm {{ isset($item->link_file) && $item->link_file ? 'btn-outline-primary' : 'btn-primary' }} m-0" id="label-file-{{ $syaratId }}" style="cursor: pointer;">
                                                        <i class="fas {{ isset($item->link_file) && $item->link_file ? 'fa-edit' : 'fa-upload' }} mr-1" id="icon-file-{{ $syaratId }}"></i> 
                                                        <span id="btn-text-{{ $syaratId }}">{{ isset($item->link_file) && $item->link_file ? 'Ubah' : 'Upload' }}</span>
                                                    </label>
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" onchange="window.uploadFile(this, '{{ $syaratId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                                </div>
                                                <div id="file-name-{{ $syaratId }}" class="text-muted text-center" style="font-size: 11px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ isset($item->link_file) && $item->link_file ? basename($item->link_file) : '' }}
                                                </div>
                                                <div id="progress-container-{{ $syaratId }}" class="progress mt-1 d-none" style="height: 6px; width: 100%;">
                                                    <div id="progress-bar-{{ $syaratId }}" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
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
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-primary" onclick="window.savePersyaratan('{{ $tahapan }}')">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </div>

                {{-- TAB: TIM PEMBIMBING & PENGUJI --}}
                <div class="tab-pane fade" id="tim" role="tabpanel">
                    <div class="row">
                        {{-- KIRI: FORM --}}
                        <div class="col-md-6">
                            <div id="timForm" style="display: block;">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tim Pembimbing dan Penguji</span>
                                </div>
                                <form id="timSidangForm" method="POST" onsubmit="event.preventDefault(); window.submitTimSidang(event)">
                                    @csrf
                                    <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                                    <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                                    <input type="hidden" name="id" id="timId" value="">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">No SK</label>
                                        <div class="col-sm-8 px-2 sk-container">
                                            <div class="input-group">
                                                <select class="form-control form-control-sm border-dark rounded-0" name="id_sk">
                                                    <option value="">-- Pilih No SK --</option>
                                                    @if($skList && $skList->count() > 0)
                                                        @foreach($skList as $sk)
                                                            <option value="{{ $sk->id }}">{{ $sk->NO_SK }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#skModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Nama</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
                                                <option value="">Pilih Nama</option>
                                                @if(isset($users) && $users->count() > 0)
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->ID }}" data-nip="{{ $user->NIP_NIM }}">{{ $user->NAMA_LENGKAP }} ({{ $user->NIP_NIM }})</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">NIP</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="nip" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Status Tim</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="status_tim_sidang">
                                                <option value="">Pilih Status</option>
                                                <option value="Ketua Sidang">Ketua Sidang</option>
                                                <option value="Ketua Pembimbing">Ketua Pembimbing</option>
                                                <option value="Pembimbing I">Pembimbing I</option>
                                                <option value="Pembimbing II">Pembimbing II</option>
                                                <option value="Penguji I">Penguji I</option>
                                                <option value="Penguji II">Penguji II</option>
                                                <option value="Penguji III">Penguji III</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Urutan</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="urutan">
                                                <option value="">Pilih Urutan</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                        <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timForm').style.display='none'; document.getElementById('timAddBtn').style.display='block';">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtn" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="showAddTimForm('timForm', 'timAddBtn')"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            </div>
                        </div>
                        {{-- KANAN: REPORT --}}
                        <div class="col-md-6">
                            <div class="text-muted font-weight-bold mb-2 ml-1" style="font-size: 14px;">
                                Tim <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penguji</span>
                            </div>
                            <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                            <table class="table table-bordered table-sm text-center mb-0">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 20%;">NIP</th>
                                        <th style="width: 30%;">Nama</th>
                                        <th style="width: 20%;">Keterangan</th>
                                        <th style="width: 20%;">No SK</th><th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($timSidang) && $timSidang->count() > 0)
                                         @foreach($timSidang as $idx => $tim)
                                             <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};"
                                                 data-id="{{ $tim->id }}"
                                                 data-id-sk="{{ $tim->id_sk }}"
                                                 data-id-user-penilai="{{ $tim->id_user_penilai }}"
                                                 data-nip="{{ $tim->nip }}"
                                                 data-nama="{{ $tim->Nama }}"
                                                 data-status-tim-sidang="{{ $tim->status_tim_sidang }}"
                                                 data-urutan="{{ $tim->urutan }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                     <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                 </td>
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="6" class="text-center text-muted">Belum ada tim penguji</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: JADWAL SIDANG & PENILAIAN --}}
                <div class="tab-pane fade" id="jadwal" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row align-items-center mb-2">
                                <label class="col-sm-4 text-danger mb-0 px-1" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                                <div class="col-sm-8 px-1">
                                    <select class="form-control form-control-sm" id="penilaianSelect" onchange="filterPenilaian(); document.getElementById('selectedTimSidangTahap1').value = this.value">
                                        <option value="">-- Pilih Penilai --</option>
                                        @if(isset($timSidang) && $timSidang->count() > 0)
                                            @foreach($timSidang as $tim)
                                                <option value="{{ $tim->id }}">{{ $tim->Nama }} ({{ $tim->nip }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" id="selectedTimSidangTahap1" value="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row align-items-center mb-2">
                                <label class="col-sm-4 text-danger mb-0 px-1" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">No Form</label>
                                <div class="col-sm-8 px-1">
                                    <select class="form-control form-control-sm" id="formFilterSelect" onchange="filterPenilaian()">
                                        <option value="">-- Pilih No Form --</option>
                                        @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                            @foreach($pointPenilaian as $form)
                                                <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- REPORT PENILAIAN --}}
                    <div class="mt-4">
                        <table class="table table-bordered table-sm text-center">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 40%;"><span class="text-danger text-decoration-underline">Parameter Nilai</span></th>
                                    <th style="width: 15%;"><span class="text-danger text-decoration-underline">Nilai</span></th>
                                    <th style="width: 35%;"><span class="text-danger text-decoration-underline">Catatan</span></th>
                                </tr>
                            </thead>
                                 <tbody id="penilaianReportBody">
                                    <tr id="penilaianEmptyRow" style="background-color: #dbe5f1;">
                                        <td colspan="4" class="text-center text-muted">Pilih No Form untuk melihat parameter penilaian</td>
                                    </tr>
                                    @if(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                        @php $rowNum = 0; @endphp
                                        @foreach($allPointPenilaian as $point)
                                            @php
                                                $existingRecords = (isset($penilaian) && $penilaian->count() > 0) ? $penilaian->where('id_penilaian', $point->id) : collect();
                                            @endphp
                                            @php $rowNum++; @endphp
                                            <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-data-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                                <td>{{ $rowNum }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm" style="width: 60px; margin: auto;" value="" min="0" max="100">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" value="">
                                                </td>
                                            </tr>
                                            @foreach($existingRecords as $existing)
                                                @php $rowNum++; @endphp
                                                <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-data-row" data-id-penilai="{{ $existing->id_tim_sidang }}" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                                    <td>{{ $rowNum }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm" style="width: 60px; margin: auto;" value="{{ $existing->Nilai }}" min="0" max="100">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" value="{{ $existing->catatan }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- STATUS LULUS --}}
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="mr-4 font-weight-bold">Status Lulus</span>
                            <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusDisplay" style="width: 150px;">
                                <option value="lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                <option value="tidak lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                            </select>
                            <button type="button" id="lockNilaiBtn" class="btn btn-sm btn-outline-secondary ml-2 px-2 py-0" onclick="toggleLockNilai()" title="Kunci Nilai"><i class="fas fa-lock"></i> Kunci Nilai</button>
                        </div>
                        <button type="button" class="btn btn-outline-dark px-4 py-1 bg-white text-danger" style="font-size: 14px; text-decoration: underline; text-decoration-color: red;" onclick="savePenilaianTahap1()">Simpan</button>
                    </div>
                </div>
            </div>
            @else
            {{-- VIEW TAHAP I - MAHASISWA/PEMBIMBING/PENGUJI (ORIGINAL) --}}
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
                @if(isset($ajuan) && $ajuan->status_lulus)
                    <span class="font-weight-bold ml-2 text-uppercase" style="color: {{ $ajuan->status_lulus === 'lulus' ? '#28a745' : '#dc3545' }};">{{ $ajuan->status_lulus }}</span>
                @else
                    <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusDisplay2" style="width: 150px;">
                        <option value="lulus">Lulus</option>
                        <option value="tidak lulus">Tidak Lulus</option>
                    </select>
                    <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger ml-2" style="font-size: 13px; border-radius: 0;" onclick="saveStatusLulus2()">Simpan</button>
                @endif
            </div>
            @endif
            @else
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
                    <a class="nav-link font-weight-bold p-0 text-primary" id="jadwal-tab" data-toggle="tab" href="#jadwal" role="tab" style="text-decoration: underline;">Jadwal Sidang & Penilaian</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent" style="min-height: 250px;">
                <div class="tab-pane fade show active" id="persyaratan" role="tabpanel">
                    <div class="text-muted font-weight-bold mb-2 ml-1">
                        Persyaratan Sidang <span class="text-danger text-decoration-underline">{{ str_replace('tahap', 'Tahap', $tahapan) }}</span>
                    </div>
                    <table class="table table-bordered table-sm text-center mb-4">
                        <thead style="background-color: #6998d3; color: white;">
                            <tr>
                                <th style="width: 10%; color: #ffffff;">No</th>
                                <th style="width: 50%; color: #ffffff;"><span class="text-danger text-decoration-underline">Persyaratan</span></th>
                                <th style="width: 20%; color: #ffffff;"><span class="text-danger text-decoration-underline">Cek Kelengkapan</span></th>
                                <th style="width: 20%; color: #ffffff;">Upload Dokumen</th>
                            </tr>
                        </thead>
                        <tbody id="persyaratanTahapBody">
                            @if($persyaratan && $persyaratan->count() > 0)
                                @foreach($persyaratan as $idx => $item)
                                    @php $syaratId = $item->id_syarat_sidang ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">{{ $item->Persyaratan ?? $item->nama_persyaratan }}</span></td>
                                        <td>
                                            <input type="checkbox" {{ isset($item->status_kelengkapan) && $item->status_kelengkapan ? 'checked' : '' }} data-syarat-id="{{ $syaratId }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center flex-column" id="upload-wrapper-{{ $syaratId }}">
                                                <div class="d-flex align-items-center mb-1">
                                                    <a href="{{ isset($item->link_file) && $item->link_file ? $item->link_file : '#' }}" target="_blank" class="btn btn-sm btn-info text-white mr-2 {{ isset($item->link_file) && $item->link_file ? '' : 'd-none' }}" id="link-{{ $syaratId }}" title="Lihat Dokumen">
                                                        <i class="fas fa-eye mr-1"></i> Lihat
                                                    </a>
                                                    <label for="file-{{ $syaratId }}" class="btn btn-sm {{ isset($item->link_file) && $item->link_file ? 'btn-outline-primary' : 'btn-primary' }} m-0" id="label-file-{{ $syaratId }}" style="cursor: pointer;">
                                                        <i class="fas {{ isset($item->link_file) && $item->link_file ? 'fa-edit' : 'fa-upload' }} mr-1" id="icon-file-{{ $syaratId }}"></i> 
                                                        <span id="btn-text-{{ $syaratId }}">{{ isset($item->link_file) && $item->link_file ? 'Ubah' : 'Upload' }}</span>
                                                    </label>
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" onchange="window.uploadFile(this, '{{ $syaratId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                                </div>
                                                <div id="file-name-{{ $syaratId }}" class="text-muted text-center" style="font-size: 11px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ isset($item->link_file) && $item->link_file ? basename($item->link_file) : '' }}
                                                </div>
                                                <div id="progress-container-{{ $syaratId }}" class="progress mt-1 d-none" style="height: 6px; width: 100%;">
                                                    <div id="progress-bar-{{ $syaratId }}" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                @foreach([1 => 'Lembar pengesahan formulir yang sudah ditandatangi pembimbing', 2 => 'Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">bimbingan</span>', 3 => '<span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Dokumen proposal penelitian yang sudah ditandatangi pembimbing</span>'] as $hid => $hname)
                                @php $hfakerId = 'hf_' . $hid; @endphp
                                <tr style="background-color: {{ $hid % 2 == 0 ? '#e9eef6' : '#dbe5f1' }};">
                                    <td>{{ $hid }}</td>
                                    <td class="text-left">{!! $hname !!}</td>
                                    <td><input type="checkbox" data-syarat-id="{{ $hfakerId }}"></td>
                                     <td>
                                        <div class="d-flex align-items-center justify-content-center flex-column" id="upload-wrapper-{{ $hfakerId }}">
                                            <div class="d-flex align-items-center mb-1">
                                                <a href="#" target="_blank" class="btn btn-sm btn-info text-white mr-2 d-none" id="link-{{ $hfakerId }}" title="Lihat Dokumen">
                                                    <i class="fas fa-eye mr-1"></i> Lihat
                                                </a>
                                                <label for="file-{{ $hfakerId }}" class="btn btn-sm btn-primary m-0" id="label-file-{{ $hfakerId }}" style="cursor: pointer;">
                                                    <i class="fas fa-upload mr-1" id="icon-file-{{ $hfakerId }}"></i> 
                                                    <span id="btn-text-{{ $hfakerId }}">Upload</span>
                                                </label>
                                                <input type="file" class="d-none" id="file-{{ $hfakerId }}" onchange="window.uploadFile(this, '{{ $hfakerId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                            </div>
                                            <div id="file-name-{{ $hfakerId }}" class="text-muted text-center" style="font-size: 11px; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            </div>
                                            <div id="progress-container-{{ $hfakerId }}" class="progress mt-1 d-none" style="height: 6px; width: 100%;">
                                                <div id="progress-bar-{{ $hfakerId }}" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-primary" onclick="savePersyaratanTahap()">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </div>

                <div class="tab-pane fade" id="tim" role="tabpanel">
                    {{-- TIM: FORM LEFT, REPORT RIGHT --}}
                    <div class="row">
                        {{-- KIRI: FORM --}}
                        <div class="col-md-6">
                            <div id="timFormTahap2" style="display: block;">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tim Pembimbing dan Penguji</span>
                                </div>
                                <form id="timSidangFormTahap2" method="POST" onsubmit="event.preventDefault(); window.submitTimSidang(event)">
                                    @csrf
                                    <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                                    <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                                    <input type="hidden" name="id" id="timId" value="">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">No SK</label>
                                        <div class="col-sm-8 px-2 sk-container">
                                            <div class="input-group">
                                                <select class="form-control form-control-sm border-dark rounded-0" name="id_sk">
                                                    <option value="">-- Pilih No SK --</option>
                                                    @if($skList && $skList->count() > 0)
                                                        @foreach($skList as $sk)
                                                            <option value="{{ $sk->id }}">{{ $sk->NO_SK }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#skModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Nama</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
                                                <option value="">Pilih Nama</option>
                                                @if(isset($users) && $users->count() > 0)
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->ID }}" data-nip="{{ $user->NIP_NIM }}">{{ $user->NAMA_LENGKAP }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">NIP</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="nip" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Status Tim</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="status_tim_sidang">
                                                <option value="">Pilih Status</option>
                                                <option value="Ketua Sidang">Ketua Sidang</option>
                                                <option value="Ketua Pembimbing">Ketua Pembimbing</option>
                                                <option value="Pembimbing I">Pembimbing I</option>
                                                <option value="Pembimbing II">Pembimbing II</option>
                                                <option value="Penguji I">Penguji I</option>
                                                <option value="Penguji II">Penguji II</option>
                                                <option value="Penguji III">Penguji III</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Urutan</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="urutan">
                                                <option value="">Pilih Urutan</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                        <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timFormTahap2').style.display='none'; document.getElementById('timAddBtnTahap2').style.display='block';">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtnTahap2" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="showAddTimForm('timFormTahap2', 'timAddBtnTahap2')"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            </div>
                        </div>
                        {{-- KANAN: REPORT --}}
                        <div class="col-md-6">
                            <div class="text-muted font-weight-bold mb-2 ml-1" style="font-size: 14px;">
                                Tim <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penguji</span>
                            </div>
                            <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                            <table class="table table-bordered table-sm text-center mb-0">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 20%;">NIP</th>
                                        <th style="width: 30%;">Nama</th>
                                        <th style="width: 20%;">Keterangan</th>
                                        <th style="width: 20%;">No SK</th><th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($timSidang) && $timSidang->count() > 0)
                                         @foreach($timSidang as $idx => $tim)
                                             <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};"
                                                 data-id="{{ $tim->id }}"
                                                 data-id-sk="{{ $tim->id_sk }}"
                                                 data-id-user-penilai="{{ $tim->id_user_penilai }}"
                                                 data-nip="{{ $tim->nip }}"
                                                 data-nama="{{ $tim->Nama }}"
                                                 data-status-tim-sidang="{{ $tim->status_tim_sidang }}"
                                                 data-urutan="{{ $tim->urutan }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                     <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                 </td>
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="6" class="text-center text-muted">Belum ada tim penguji</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="jadwal" role="tabpanel">
                    {{-- JADWAL & PENILAIAN --}}
                    <div id="jadwalListTahap2">
                        <div class="d-flex justify-content-between align-items-center mb-2 mx-1">
                            <div class="text-muted font-weight-bold">
                                Jadwal Sidang {{ str_replace('tahap', 'Tahap', $tahapan) }}
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('jadwalListTahap2').style.display='none'; document.getElementById('jadwalFormTahap2').style.display='block';"><i class="fas fa-plus mr-1"></i> Tambah</button>
                        </div>
                        <table class="table table-bordered table-sm text-center">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 40%;">Jadwal</th>
                                    <th style="width: 20%;">Status Lulus</th>
                                    <th style="width: 30%;">Penilaian Seminar/Sidang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($ajuan) && $ajuan->tgl_sidang)
                                    <tr style="background-color: #dbe5f1;">
                                        <td>1</td>
                                        <td><span class="text-primary text-decoration-underline jadwal-date-link" style="cursor: pointer;">{{ \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') }}</span></td>
                                        <td>{{ $ajuan->status_lulus ?? 'Dalam Proses' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-link text-primary p-0" onclick="document.getElementById('jadwalListTahap2').style.display='none'; document.getElementById('penilaianFormTahap2').style.display='block';">Penilaian</button>
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

                    {{-- PENILAIAN FORM TAHAP II --}}
                    <div id="penilaianFormTahap2" style="display: none;">
                        <div class="text-muted mb-3" style="font-size: 13px;">
                            Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penilaian</span> Seminar/Sidang {{ str_replace('tahap', 'Tahap', $tahapan) }}
                        </div>
                        <form id="penilaianFormTahap2Form" onsubmit="return false">
                            @csrf
                            <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                            <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                            <input type="hidden" name="id_tim_sidang" id="selectedTimSidangTahap2">
                            <input type="hidden" name="no_form" id="selectedNoFormTahap2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Penilai</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" id="penilaiTahap2" name="id_user_penilai" onchange="filterPenilaianTahap2(); document.getElementById('selectedTimSidangTahap2').value = this.value">
                                                <option value="">Pilih Penilai</option>
                                                @if(isset($timSidang) && $timSidang->count() > 0)
                                                    @foreach($timSidang as $tim)
                                                        <option value="{{ $tim->id }}">{{ $tim->Nama }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Form</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" id="formTahap2" name="no_form_display" onchange="filterPenilaianTahap2(); document.getElementById('selectedNoFormTahap2').value = this.value">
                                                <option value="">Pilih Form</option>
                                                @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                                    @foreach($pointPenilaian as $form)
                                                        <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2 mb-4">
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0;">Cetak Form</button>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;">Ba Sidang</button>
                            </div>
                            {{-- REPORT TABLE --}}
                            <table class="table table-bordered table-sm text-center">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 40%;">Parameter Penilaian</th>
                                        <th style="width: 15%;">Nilai (Range 1 - 5)</th>
                                        <th style="width: 35%;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="penilaianTahap2Body">
                                    <tr id="penilaianTahap2EmptyRow" style="background-color: #dbe5f1;">
                                        <td colspan="4" class="text-center text-muted">Pilih Form untuk melihat parameter penilaian</td>
                                    </tr>
                                    @if(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                        @php $rowNum = 0; @endphp
                                        @foreach($allPointPenilaian as $point)
                                            @php
                                                $existingRecords = (isset($penilaian) && $penilaian->count() > 0) ? $penilaian->where('id_penilaian', $point->id) : collect();
                                            @endphp
                                            @php $rowNum++; @endphp
                                            <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-tahap2-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                                <td>{{ $rowNum }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                <td>
                                                    <input type="number" name="penilaian[{{ $rowNum }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                    <input type="number" class="form-control form-control-sm" style="width: 60px; margin: auto;" name="penilaian[{{ $rowNum }}][nilai]" value="" min="1" max="5">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="penilaian[{{ $rowNum }}][catatan]" value="">
                                                </td>
                                            </tr>
                                            @foreach($existingRecords as $existing)
                                                @php $rowNum++; @endphp
                                                <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-tahap2-row" data-id-penilai="{{ $existing->id_tim_sidang }}" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}">
                                                    <td>{{ $rowNum }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $rowNum }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                        <input type="number" class="form-control form-control-sm" style="width: 60px; margin: auto;" name="penilaian[{{ $rowNum }}][nilai]" value="{{ $existing->Nilai }}" min="1" max="5">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm" name="penilaian[{{ $rowNum }}][catatan]" value="{{ $existing->catatan }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <div class="text-left mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0" style="font-size: 12px; border-radius: 0;" onclick="document.getElementById('penilaianFormTahap2').style.display='none'; document.getElementById('jadwalListTahap2').style.display='block';">&larr; Kembali</button>
                            </div>
                        </form>
                    </div>
                    {{-- STATUS KELULUSAN --}}
                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="mr-4 font-weight-bold">Status Kelulusan</span>
                            <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusTahap2" style="width: 150px;">
                                <option value="">Pilih Status</option>
                                <option value="lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                <option value="tidak lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                            </select>
                            <button type="button" id="lockNilaiTahap2Btn" class="btn btn-sm btn-outline-secondary ml-2 px-2 py-0" onclick="toggleLockNilaiTahap2()" title="Kunci Nilai"><i class="fas fa-lock"></i> Kunci Nilai</button>
                        </div>
                        <button type="button" class="btn btn-outline-dark px-4 py-1 bg-white text-danger" style="font-size: 14px; text-decoration: underline; text-decoration-color: red;" onclick="savePenilaianTahap2()">Simpan</button>
                    </div>

                    <div id="jadwalFormTahap2" style="display: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="text-muted" style="font-size: 13px;">
                                Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Jadwal Sidang {{ str_replace('tahap', 'Tahap', $tahapan) }}</span>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0;">Cetak Surat</button>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;">Cetak Undangan</button>
                            </div>
                        </div>
                        <form id="jadwalFormTahap2Form" onsubmit="event.preventDefault(); submitJadwalTahap2(event)">
                            @csrf
                            <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                            <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Tgl Seminar/Sidang</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_sidang" value="{{ isset($ajuan) ? $ajuan->tgl_sidang : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Waktu</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="time" class="form-control form-control-sm border-dark rounded-0" name="waktu_sidang" value="{{ isset($ajuan) ? $ajuan->waktu_sidang : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Ruangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="ruang_sidang" value="{{ isset($ajuan) ? $ajuan->ruang_sidang : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Surat Undangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_surat_undangan" value="{{ isset($ajuan) ? $ajuan->tgl_surat_undangan : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No Surat Undangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_surat_undangan" value="{{ isset($ajuan) ? $ajuan->no_surat_undangan : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Surat Penelaah</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_surat_penelaah" value="{{ isset($ajuan) ? $ajuan->tgl_surat_penelaah : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No Surat Penelaah</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_surat_penelaah" value="{{ isset($ajuan) ? $ajuan->no_surat_penelaah : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Pemanggilan Penilai</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_pemanggilan_penilai" value="{{ isset($ajuan) ? $ajuan->tgl_pemanggilan_penilai : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Email Surat</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="email" class="form-control form-control-sm border-dark rounded-0" name="email_surat" value="{{ isset($ajuan) ? $ajuan->email_surat : '' }}">
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No SK Kelulusan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_sk_kelulusan" value="{{ isset($ajuan) ? $ajuan->no_sk_kelulusan : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0;">Ajukan ke FS</button>
                                <button type="submit" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('jadwalFormTahap2').style.display='none'; document.getElementById('jadwalListTahap2').style.display='block';">Batal</button>
                            </div>
                        </form>
                    </div>


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
                                @if($isKetuaPembimbing ?? false)
                                <div class="form-group row align-items-center mb-3 px-1">
                                    <label class="col-sm-4 mb-0" style="font-size: 13px; color: #555;">Status Lulus</label>
                                    <div class="col-sm-8 px-2">
                                        <select class="form-control form-control-sm border-dark rounded-0" id="statusLulus">
                                            <option value="">Pilih Status</option>
                                            <option value="lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                            <option value="tidak lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                                        </select>
                                    </div>
                                </div>
                                @else
                                <div class="form-group row align-items-center mb-3 px-1">
                                    <label class="col-sm-4 mb-0" style="font-size: 13px; color: #555;">Status Lulus</label>
                                    <div class="col-sm-8 px-2">
                                        <input type="text" class="form-control form-control-sm border-dark rounded-0 bg-light" value="{{ isset($ajuan) ? $ajuan->status_lulus : '' }}" disabled>
                                    </div>
                                </div>
                                @endif
                                <div class="text-center mt-4">
                                    @if($isKetuaPembimbing ?? false)
                                    <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger mr-2" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;" onclick="updateStatusLulus({{ $ajuan->id ?? 0 }})">Simpan Status</button>
                                    @endif
                                    <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                </div>
                            </div>
                            
                            <div class="col-md-7">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline;">Penilaian</span>
                                </div>
                                @php
                                    $user = session('auth_user');
                                    $isPembimbingOrPenguji = in_array($user['role'] ?? '', ['Pembimbing', 'Penguji']);
                                @endphp
                                @if($isPembimbingOrPenguji)
                                <form id="penilaianForm" onsubmit="submitPenilaian(event)">
                                    @csrf
                                    <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                                    <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                                    <input type="hidden" name="id_tim_sidang" id="selectedTimSidang">
                                    <input type="hidden" name="no_form" id="selectedNoForm">
                                    <div class="form-group row align-items-center mb-2">
                                        <label class="col-sm-2 text-danger mb-0 px-1 text-right" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                                        <div class="col-sm-4 px-1">
                                            <select class="form-control form-control-sm border-dark rounded-0" id="penilaiSelect" onchange="loadPenilaianForm()">
                                                <option value="">Pilih Penilai</option>
                                                @if(isset($timSidang) && $timSidang->count() > 0)
                                                    @foreach($timSidang as $tim)
                                                        <option value="{{ $tim->id }}" {{ $tim->id_user_penilai == $user['id'] ? 'selected' : '' }}>{{ $tim->Nama }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <label class="col-sm-2 mb-0 px-1 text-center" style="font-size: 13px; color: #555;">Form</label>
                                        <div class="col-sm-2 px-1">
                                            <select class="form-control form-control-sm border-dark rounded-0" id="formSelect" onchange="loadPenilaianForm()">
                                                <option value="">Pilih Form</option>
                                                @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                                    @foreach($pointPenilaian as $form)
                                                        <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-sm-2 pl-0 d-flex align-items-center">
                                            <button type="submit" class="btn btn-outline-dark py-0 px-1 bg-white" style="font-size: 12px; border-radius: 0;">Simpan</button>
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
                                        <tbody id="penilaianTableBody">
                                            <tr id="penilaianFormEmptyRow" style="background-color: #dbe5f1;">
                                                <td colspan="4" class="text-center text-muted">Pilih Penilai dan Form untuk melihat parameter penilaian</td>
                                            </tr>
                                            @if(isset($penilaian) && $penilaian->count() > 0)
                                                @foreach($penilaian as $idx => $nilai)
                                                <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }}; display: none;" data-id="{{ $nilai->id_penilaian }}" data-no-form="{{ $nilai->no_form }}">
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $nilai->nama_penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $idx }}][id_penilaian]" value="{{ $nilai->id_penilaian }}" hidden>
                                                        <input type="number" name="penilaian[{{ $idx }}][nilai]" value="{{ $nilai->Nilai }}" class="form-control form-control-sm" min="0" max="100" style="width: 60px; margin: auto;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="penilaian[{{ $idx }}][catatan]" value="{{ $nilai->catatan }}" class="form-control form-control-sm" style="font-size: 12px;">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @elseif(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                                @foreach($allPointPenilaian as $idx => $point)
                                                <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }}; display: none;" data-id="{{ $point->id }}" data-no-form="{{ $point->no_form }}">
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $idx }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                        <input type="number" name="penilaian[{{ $idx }}][nilai]" value="" class="form-control form-control-sm" min="0" max="100" style="width: 60px; margin: auto;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="penilaian[{{ $idx }}][catatan]" value="" class="form-control form-control-sm" style="font-size: 12px;">
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </form>
                                @else
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
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tim" role="tabpanel">
                    {{-- TIM: FORM LEFT, REPORT RIGHT --}}
                    <div class="row">
                        {{-- KIRI: FORM --}}
                        <div class="col-md-6">
                            <div id="timFormTahap2" style="display: block;">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tim Pembimbing dan Penguji</span>
                                </div>
                                <form id="timSidangFormTahap2" method="POST" onsubmit="event.preventDefault(); window.submitTimSidang(event)">
                                    @csrf
                                    <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                                    <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                                    <input type="hidden" name="id" id="timId" value="">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">No SK</label>
                                        <div class="col-sm-8 px-2 sk-container">
                                            <div class="input-group">
                                                <select class="form-control form-control-sm border-dark rounded-0" name="id_sk">
                                                    <option value="">-- Pilih No SK --</option>
                                                    @if($skList && $skList->count() > 0)
                                                        @foreach($skList as $sk)
                                                            <option value="{{ $sk->id }}">{{ $sk->NO_SK }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#skModal">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Nama</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
                                                <option value="">Pilih Nama</option>
                                                @if(isset($users) && $users->count() > 0)
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->ID }}" data-nip="{{ $user->NIP_NIM }}">{{ $user->NAMA_LENGKAP }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">NIP</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="nip" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Status Tim</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="status_tim_sidang">
                                                <option value="">Pilih Status</option>
                                                <option value="Ketua Sidang">Ketua Sidang</option>
                                                <option value="Ketua Pembimbing">Ketua Pembimbing</option>
                                                <option value="Pembimbing I">Pembimbing I</option>
                                                <option value="Pembimbing II">Pembimbing II</option>
                                                <option value="Penguji I">Penguji I</option>
                                                <option value="Penguji II">Penguji II</option>
                                                <option value="Penguji III">Penguji III</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Urutan</label>
                                        <div class="col-sm-8 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="urutan">
                                                <option value="">Pilih Urutan</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0; text-decoration: underline; text-decoration-color: red;">Simpan</button>
                                        <button type="button" class="btn btn-outline-dark px-3 py-0 bg-white text-danger" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timFormTahap2').style.display='none'; document.getElementById('timAddBtnTahap2').style.display='block';">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtnTahap2" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="showAddTimForm('timFormTahap2', 'timAddBtnTahap2')"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            </div>
                        </div>
                        {{-- KANAN: REPORT --}}
                        <div class="col-md-6">
                            <div class="text-muted font-weight-bold mb-2 ml-1" style="font-size: 14px;">
                                Tim <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Pembimbing</span> dan <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penguji</span>
                            </div>
                            <div class="table-responsive" style="max-height: 45vh; overflow-y: auto;">
                            <table class="table table-bordered table-sm text-center mb-0">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 10%;">No</th>
                                        <th style="width: 20%;">NIP</th>
                                        <th style="width: 30%;">Nama</th>
                                        <th style="width: 20%;">Keterangan</th>
                                        <th style="width: 20%;">No SK</th><th style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($timSidang) && $timSidang->count() > 0)
                                         @foreach($timSidang as $idx => $tim)
                                             <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};"
                                                 data-id="{{ $tim->id }}"
                                                 data-id-sk="{{ $tim->id_sk }}"
                                                 data-id-user-penilai="{{ $tim->id_user_penilai }}"
                                                 data-nip="{{ $tim->nip }}"
                                                 data-nama="{{ $tim->Nama }}"
                                                 data-status-tim-sidang="{{ $tim->status_tim_sidang }}"
                                                 data-urutan="{{ $tim->urutan }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                     <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                 </td>
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="6" class="text-center text-muted">Belum ada tim penguji</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
#penilaianTableBody tr.penilaian-data-row { display: none; }
#penilaianReportBody tr.penilaian-data-row { display: none; }
</style>
<script>
var persyaratanFiles = {};

document.addEventListener('change', function(e) {
    if (e.target.matches('input[type="file"][data-syarat-id]')) {
        var input = e.target;
        var id = input.getAttribute('data-syarat-id');
        var file = input.files[0];
        if (file) {
            persyaratanFiles[id] = file;
            var nameEl = document.getElementById('file-name-' + id);
            if (nameEl) nameEl.textContent = file.name;
        } else {
            delete persyaratanFiles[id];
            var nameEl = document.getElementById('file-name-' + id);
            if (nameEl) nameEl.textContent = '';
        }
    }
});

function savePersyaratanTahap() {
    var tbody = document.getElementById('persyaratanTahapBody');
    if (!tbody) return;
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('id_judul', '{{ $idJudul }}');
    formData.append('tahapan_sidang', '{{ $tahapan }}');

    var checkboxes = tbody.querySelectorAll('input[type="checkbox"][data-syarat-id]');
    checkboxes.forEach(function(cb) {
        formData.append('kelengkapan[' + cb.getAttribute('data-syarat-id') + ']', cb.checked ? 'y' : 't');
    });

    var fileInputs = tbody.querySelectorAll('input[type="file"][data-syarat-id]');
    fileInputs.forEach(function(input) {
        var id = input.getAttribute('data-syarat-id');
        if (persyaratanFiles[id]) {
            formData.append('files[' + id + ']', persyaratanFiles[id]);
        }
    });

    var btn = document.querySelector('.btn-primary[onclick*="savePersyaratanTahap"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...'; }

    fetch('{{ route('mahasiswa.save-all-persyaratan') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('Persyaratan berhasil disimpan', 'success');
            persyaratanFiles = {};
            if (typeof showTahapForm === 'function') {
                showTahapForm('{{ $tahapan }}', '{{ $idJudul }}', 'persyaratan');
            } else {
                location.reload();
            }
        } else {
            showToast(data.message || 'Gagal menyimpan persyaratan', 'error');
        }
    })
    .catch(function(error) {
        showToast('Terjadi kesalahan: ' + error, 'error');
    })
    .finally(function() {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan'; }
    });
}

function updateStatusLulus(idAjuan) {
    const statusLulus = document.getElementById('statusLulus').value;
    if (!statusLulus) {
        alert('Silakan pilih status kelulusan');
        return;
    }
    
    fetch(`/sidang/status-lulus/${idAjuan}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status_lulus: statusLulus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status kelulusan berhasil diperbarui');
            location.reload();
        } else {
            alert('Error: ' + (data.error || 'Terjadi kesalahan'));
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function submitPenilaian(event) {
    event.preventDefault();
    event.stopPropagation();
    const form = event.target;
    const formData = new FormData(form);

    // Convert to JSON
    const data = {};
    const penilaianArr = {};
    formData.forEach((value, key) => {
        if (key.startsWith('penilaian[')) {
            const matches = key.match(/penilaian\[(\d+)\]\[(.+)\]/);
            if (matches) {
                const index = matches[1];
                const field = matches[2];
                if (!penilaianArr[index]) penilaianArr[index] = {};
                penilaianArr[index][field] = value;
            }
        } else {
            data[key] = value;
        }
    });

    data.penilaian = Object.values(penilaianArr);
    fetch('/sidang/penilaian', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (!response.ok) {
                var msg = data.error || data.message || JSON.stringify(data.errors || data);
                throw new Error(msg);
            }
            return data;
        });
    })
    .then(function(data) {
        if (data.success) {
            showToast('Penilaian berhasil disimpan', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(function(error) {
        showToast('Error: ' + error.message, 'error');
    });
}

function filterPenilaianTahap2() {
    var isLocked = {{ isset($ajuan) && $ajuan->status_lulus ? 'true' : 'false' }};
    var penilaiId = document.getElementById('penilaiTahap2').value;
    var noForm = document.getElementById('formTahap2').value;
    document.getElementById('selectedTimSidangTahap2').value = penilaiId;
    document.getElementById('selectedNoFormTahap2').value = noForm;
    var tbody = document.getElementById('penilaianTahap2Body');
    var dataRows = tbody.querySelectorAll('tr.penilaian-tahap2-row');
    var emptyRow = document.getElementById('penilaianTahap2EmptyRow');
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
                var existingRow = tbody.querySelector('tr.penilaian-tahap2-row[data-id-penilai="' + penilaiId + '"][data-point-id="' + rowPointId + '"]');
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
        row.querySelectorAll('input, textarea, select').forEach(function(input) {
            input.disabled = !show || isLocked;
        });
        if (show) hasVisible = true;
    });

    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
}

function filterPenilaian() {
    var isLocked = {{ isset($ajuan) && $ajuan->status_lulus ? 'true' : 'false' }};
    var penilaiId = document.getElementById('penilaianSelect').value;
    var noForm = document.getElementById('formFilterSelect').value;
    var tbody = document.getElementById('penilaianReportBody');
    var dataRows = tbody.querySelectorAll('tr.penilaian-data-row');
    var emptyRow = document.getElementById('penilaianEmptyRow');
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
                var existingRow = tbody.querySelector('tr.penilaian-data-row[data-id-penilai="' + penilaiId + '"][data-point-id="' + rowPointId + '"]');
                if (existingRow) {
                    show = false;
                }
            }
        } else {
            if (!penilaiId || rowPenilai !== penilaiId) {
                show = false;
            }
        }

        row.style.display = show ? 'table-row' : 'none';
        row.querySelectorAll('input, textarea, select').forEach(function(input) {
            input.disabled = !show || isLocked;
        });
        if (show) hasVisible = true;
    });

    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
}

function loadPenilaianForm() {
    const timSidangId = document.getElementById('penilaiSelect').value;
    const noForm = document.getElementById('formSelect').value;
    
    document.getElementById('selectedTimSidang').value = timSidangId;
    document.getElementById('selectedNoForm').value = noForm;
    
    const tableBody = document.getElementById('penilaianTableBody');
    const dataRows = tableBody.querySelectorAll('tr:not(#penilaianFormEmptyRow)');
    var emptyRow = document.getElementById('penilaianFormEmptyRow');
    var hasVisible = false;
    
    dataRows.forEach(row => {
        var show = true;
        if (!timSidangId || !noForm) {
            show = false;
        } else {
            var rowNoForm = row.getAttribute('data-no-form');
            if (!rowNoForm || rowNoForm !== noForm) {
                show = false;
            }
        }
        row.style.display = show ? 'table-row' : 'none';
        if (show) hasVisible = true;
    });
    
    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
}

function updateKelengkapan(idSyarat, isChecked) {
    let formData = new FormData();
    formData.append('id_syarat_sidang', idSyarat);
    formData.append('status_lengkap', isChecked ? 'y' : 't');
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route('mahasiswa.update-kelengkapan') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Status kelengkapan updated');
        } else {
            alert('Gagal update status kelengkapan');
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

function uploadFile(input, idSyarat, tahapan) {
    if (!input.files || input.files.length === 0) return;

    let formData = new FormData();
    formData.append('file', input.files[0]);
    formData.append('id_syarat_sidang', idSyarat);
    formData.append('tahapan_sidang', tahapan);
    formData.append('_token', '{{ csrf_token() }}');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route('mahasiswa.upload-persyaratan') }}', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.onload = function () {
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
        alert('Upload error occurred');
    };

    xhr.send(formData);
}

function showAddTimForm(formId, btnId) {
    var form = document.getElementById(formId);
    var btn = document.getElementById(btnId);
    if (!form) return;
    form.style.display = 'block';
    if (btn) btn.style.display = 'none';
    form.querySelector('[name="id"]').value = '';
    form.querySelector('[name="id_sk"]').value = '';
    form.querySelector('[name="id_user_penilai"]').value = '';
    form.querySelector('[name="nip"]').value = '';
    form.querySelector('[name="status_tim_sidang"]').value = '';

    var timTable = form.closest('.tab-pane').querySelector('table tbody');
    if (timTable) {
        var rows = timTable.querySelectorAll('tr[data-id]');
        var nextUrutan = rows.length + 1;
        var urutanSelect = form.querySelector('[name="urutan"]');
        if (urutanSelect) {
            urutanSelect.value = nextUrutan <= 7 ? nextUrutan : '';
        }
    }
}

function editTimSidang(btn) {
    var row = btn.closest('tr');
    var form = row.closest('.tab-pane').querySelector('form[id^="timSidangForm"]');
    if (!form) return;

    var id = row.getAttribute('data-id');
    var idSk = row.getAttribute('data-id-sk');
    var idUserPenilai = row.getAttribute('data-id-user-penilai');
    var nip = row.getAttribute('data-nip');
    var statusTimSidang = row.getAttribute('data-status-tim-sidang');
    var urutan = row.getAttribute('data-urutan');

    form.querySelector('[name="id"]').value = id;
    form.querySelector('[name="id_sk"]').value = idSk || '';
    form.querySelector('[name="id_user_penilai"]').value = idUserPenilai || '';
    form.querySelector('[name="nip"]').value = nip || '';
    form.querySelector('[name="status_tim_sidang"]').value = statusTimSidang || '';
    form.querySelector('[name="urutan"]').value = urutan || '';

    var formEl = form.closest('[id^="timForm"]');
    if (formEl) formEl.style.display = 'block';
    var isTahap2 = form.id === 'timSidangFormTahap2';
    var btnEl = document.getElementById(isTahap2 ? 'timAddBtnTahap2' : 'timAddBtn');
    if (btnEl) btnEl.style.display = 'none';
}

function deleteTimSidang(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;

    fetch('/sidang/tim-sidang/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Data berhasil dihapus', 'success');
            var tahapan = document.querySelector('[name="tahapan_sidang"]').value;
            var idJudul = document.querySelector('[name="id_judul"]').value;
            if (typeof showTahapForm === 'function') {
                showTahapForm(tahapan, idJudul, 'tim');
            } else {
                location.reload();
            }
        } else {
            showToast(data.message || 'Gagal menghapus data', 'error');
        }
    })
    .catch(error => {
        showToast('Terjadi kesalahan: ' + error, 'error');
    });
}

function submitTimSidang(event) {
    event.preventDefault();
    event.stopPropagation();
    const form = event.target;
    const formData = new FormData(form);
    const timId = form.querySelector('[name="id"]').value;

    if (timId) {
        formData.append('_method', 'PUT');
    }

    fetch('/sidang/tim-sidang' + (timId ? '/' + timId : ''), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Tim Pembimbing berhasil disimpan', 'success');
            form.reset();
            var isTahap2 = form.id === 'timSidangFormTahap2';
            var formEl = document.getElementById(isTahap2 ? 'timFormTahap2' : 'timForm');
            var btnEl = document.getElementById(isTahap2 ? 'timAddBtnTahap2' : 'timAddBtn');
            if (formEl) formEl.style.display = 'none';
            if (btnEl) btnEl.style.display = 'block';
            // Reload the tahap content to show updated table
            const tahapan = formData.get('tahapan_sidang');
            const idJudul = formData.get('id_judul');
            if (typeof showTahapForm === 'function') {
                showTahapForm(tahapan, idJudul, 'tim');
            } else {
                location.reload();
            }
        } else {
            showToast(data.message || 'Gagal menyimpan tim pembimbing', 'error');
        }
    })
    .catch(error => {
        showToast('Terjadi kesalahan: ' + error, 'error');
    });
}

async function savePenilaianTahap1() {
    const idJudul = '{{ $idJudul }}';
    const tahapan = '{{ $tahapan }}';
    const idTimSidang = document.getElementById('selectedTimSidangTahap1').value;

    if (!idTimSidang) {
        showToast('Pilih penilai terlebih dahulu', 'error');
        return;
    }

    const tbody = document.getElementById('penilaianReportBody');
    const dataRows = tbody.querySelectorAll('tr.penilaian-data-row');
    const penilaian = [];

    dataRows.forEach(function(row) {
        if (row.style.display === 'none') return;
        if (row.id === 'penilaianEmptyRow') return;

        const idPenilaian = row.getAttribute('data-point-id');
        const inputs = row.querySelectorAll('input');
        const nilai = inputs[0] ? inputs[0].value : '';
        const catatan = inputs[1] ? inputs[1].value : '';

        penilaian.push({
            id_penilaian: idPenilaian,
            nilai: nilai,
            catatan: catatan
        });
    });

    if (penilaian.length === 0) {
        showToast('Tidak ada data penilaian untuk disimpan', 'error');
        return;
    }

    try {
        const body = {
            id_judul: idJudul,
            tahapan_sidang: tahapan,
            id_tim_sidang: idTimSidang,
            penilaian: penilaian
        };
        const statusLulusEl = document.getElementById('statusLulusDisplay');
        const statusLulus = statusLulusEl && statusLulusEl.tagName === 'SELECT' ? statusLulusEl.value : '';
        if (statusLulus) {
            body.status_lulus = statusLulus;
        }

        const res = await fetch('/sidang/penilaian', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            let msg = 'Penilaian berhasil disimpan';
            if (data.status_lulus_updated) {
                msg += ' & status lulus tersimpan';
            } else if (statusLulus) {
                msg += ' (status lulus tidak diupdate)';
            }
            showToast(msg, 'success');
        } else {
            showToast('Error: ' + (data.error || 'Gagal simpan'), 'error');
        }
    } catch (error) {
        showToast('Error: ' + error, 'error');
    }
}

function saveStatusLulus() {
    const statusLulus = document.getElementById('statusLulusDisplay').value;
    const idJudul = '{{ $idJudul }}';
    const tahapan = '{{ $tahapan }}';

    fetch('/sidang/status-lulus/' + idJudul, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status_lulus: statusLulus,
            tahapan_sidang: tahapan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status Lulus berhasil disimpan', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error, 'error');
    });
}

function saveStatusLulus2() {
    const statusLulus = document.getElementById('statusLulusDisplay2').value;
    const idJudul = '{{ $idJudul }}';
    const tahapan = '{{ $tahapan }}';

    fetch('/sidang/status-lulus/' + idJudul, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status_lulus: statusLulus,
            tahapan_sidang: tahapan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status Lulus berhasil disimpan', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error, 'error');
    });
}

function toggleLockNilaiTahap2() {
    var btn = document.getElementById('lockNilaiTahap2Btn');
    var inputs = document.querySelectorAll('#penilaianTahap2Body input');
    var isLocked = inputs.length > 0 && inputs[0].disabled;
    inputs.forEach(function(inp) { inp.disabled = !isLocked; });
    btn.innerHTML = isLocked ? '<i class="fas fa-lock"></i> Kunci Nilai' : '<i class="fas fa-unlock"></i> Buka Nilai';
}

function toggleLockNilai() {
    var btn = document.getElementById('lockNilaiBtn');
    var inputs = document.querySelectorAll('#penilaianReportBody input');
    var isLocked = inputs.length > 0 && inputs[0].disabled;
    inputs.forEach(function(inp) { inp.disabled = !isLocked; });
    btn.innerHTML = isLocked ? '<i class="fas fa-lock"></i> Kunci Nilai' : '<i class="fas fa-unlock"></i> Buka Nilai';
}

async function savePenilaianTahap2() {
    const idJudul = '{{ $idJudul }}';
    const tahapan = '{{ $tahapan }}';
    const idTimSidang = document.getElementById('selectedTimSidangTahap2').value;

    if (!idTimSidang) {
        showToast('Pilih penilai terlebih dahulu', 'error');
        return;
    }

    const tbody = document.getElementById('penilaianTahap2Body');
    const dataRows = tbody.querySelectorAll('tr.penilaian-tahap2-row');
    const penilaian = [];

    dataRows.forEach(function(row) {
        if (row.style.display === 'none') return;
        if (row.id === 'penilaianTahap2EmptyRow') return;

        const idPenilaianInput = row.querySelector('input[name$="[id_penilaian]"]');
        const nilaiInput = row.querySelector('input[name$="[nilai]"]');
        const catatanInput = row.querySelector('input[name$="[catatan]"]');
        if (!idPenilaianInput) return;

        penilaian.push({
            id_penilaian: idPenilaianInput.value,
            nilai: nilaiInput ? nilaiInput.value : '',
            catatan: catatanInput ? catatanInput.value : ''
        });
    });

    if (penilaian.length === 0) {
        showToast('Tidak ada data penilaian untuk disimpan', 'error');
        return;
    }

    try {
        const body = {
            id_judul: idJudul,
            tahapan_sidang: tahapan,
            id_tim_sidang: idTimSidang,
            penilaian: penilaian
        };
        const statusLulusEl = document.getElementById('statusLulusTahap2');
        const statusLulus = statusLulusEl && statusLulusEl.tagName === 'SELECT' ? statusLulusEl.value : '';
        if (statusLulus) {
            body.status_lulus = statusLulus;
        }

        const res = await fetch('/sidang/penilaian', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (data.success) {
            let msg = 'Penilaian berhasil disimpan';
            if (data.status_lulus_updated) {
                msg += ' & status lulus tersimpan';
            } else if (statusLulus) {
                msg += ' (status lulus tidak diupdate)';
            }
            showToast(msg, 'success');
        } else {
            showToast('Error: ' + (data.error || 'Gagal simpan'), 'error');
        }
    } catch (error) {
        showToast('Error: ' + error, 'error');
    }
}

function saveStatusLulusTahap2() {
    const statusLulus = document.getElementById('statusLulusTahap2').value;
    if (!statusLulus) {
        showToast('Pilih status kelulusan terlebih dahulu', 'error');
        return;
    }
    const idJudul = '{{ $idJudul }}';
    const tahapan = '{{ $tahapan }}';

    fetch('/sidang/status-lulus/' + idJudul, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status_lulus: statusLulus,
            tahapan_sidang: tahapan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status Lulus berhasil disimpan', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error, 'error');
    });
}

function submitJadwalTahap2(event) {
    event.preventDefault();
    event.stopPropagation();
    var form = document.getElementById('jadwalFormTahap2Form');
    var formData = new FormData(form);

    // Validate required fields
    var tglSidang = formData.get('tgl_sidang');
    var waktuSidang = formData.get('waktu_sidang');
    if (!tglSidang || !waktuSidang) {
        showToast('Tgl Seminar/Sidang dan Waktu harus diisi', 'error');
        return;
    }

    // Disable submit button
    var submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    }

    fetch('/sidang/jadwal-sidang', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'HTTP ' + response.status);
            }).catch(function() {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            });
        }
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showToast(data.message || 'Jadwal sidang berhasil disimpan', 'success');
            // Switch back to jadwal list view
            document.getElementById('jadwalFormTahap2').style.display = 'none';
            document.getElementById('jadwalListTahap2').style.display = 'block';
            // Reload the tahap content to show updated data
            var tahapan = formData.get('tahapan_sidang');
            var idJudul = formData.get('id_judul');
            if (typeof showTahapForm === 'function') {
                showTahapForm(tahapan, idJudul, 'jadwal');
            } else {
                location.reload();
            }
        } else {
            showToast(data.message || 'Gagal menyimpan jadwal sidang', 'error');
        }
    })
    .catch(function(error) {
        showToast('Error: ' + error.message, 'error');
    })
    .finally(function() {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan';
        }
    });
}

function showToast(message, type) {
    // Create toast element
    let toast = document.createElement('div');
    toast.className = 'toast show';
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '250px';
    toast.style.backgroundColor = type === 'success' ? '#28a745' : '#dc3545';
    toast.style.color = 'white';
    toast.style.padding = '15px';
    toast.style.borderRadius = '5px';
    toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    toast.textContent = message;

    document.body.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(function() {
        toast.remove();
    }, 3000);
}

// Tab switching for Tahap I
$(document).ready(function() {
    $('#myTab a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
});

$('#skModal').on('show.bs.modal', function () {
    var tahapan = $('#skTahapan').val();
    var idJudul = $('#skIdJudul').val();
    var noSkInput = $('#skForm input[name="no_sk"]');
    if (noSkInput.val() === '') {
        fetch('/sidang/sk/next/' + encodeURIComponent(tahapan) + '?id_judul=' + encodeURIComponent(idJudul))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    noSkInput.val(data.no_sk);
                }
            })
            .catch(function() {});
    }
});

// Initialize penilaian form filters to empty state
var penilaianForm = document.getElementById('penilaianForm');
if (penilaianForm) {
    var tb = document.getElementById('penilaianTableBody');
    if (tb) {
        Array.from(tb.children).forEach(function(row) {
            if (row.id !== 'penilaianFormEmptyRow') {
                row.style.display = 'none';
            }
        });
        var er = document.getElementById('penilaianFormEmptyRow');
        if (er) er.style.display = '';
    }
    loadPenilaianForm();
}
var reportBody = document.getElementById('penilaianReportBody');
if (reportBody) {
    Array.from(reportBody.children).forEach(function(row) {
        if (row.id !== 'penilaianEmptyRow') {
            row.style.display = 'none';
        }
    });
    var er2 = document.getElementById('penilaianEmptyRow');
    if (er2) er2.style.display = '';
    filterPenilaian();
}
var tahap2Body = document.getElementById('penilaianTahap2Body');
if (tahap2Body) {
    Array.from(tahap2Body.children).forEach(function(row) {
        if (row.id !== 'penilaianTahap2EmptyRow') {
            row.style.display = 'none';
        }
    });
    var er3 = document.getElementById('penilaianTahap2EmptyRow');
    if (er3) er3.style.display = '';
    filterPenilaianTahap2();
}

// Auto-lock inputs when status_lulus already exists
@if(isset($ajuan) && $ajuan->status_lulus)
(function() {
    var inputs = document.querySelectorAll('#penilaianReportBody input, #penilaianTahap2Body input');
    inputs.forEach(function(inp) { inp.disabled = true; });
    var btn = document.getElementById('lockNilaiBtn');
    if (btn) btn.innerHTML = '<i class="fas fa-lock"></i> Terkunci';
    var btn2 = document.getElementById('lockNilaiTahap2Btn');
    if (btn2) btn2.innerHTML = '<i class="fas fa-lock"></i> Terkunci';
})();
@endif

// Make jadwal date clickable to show edit form
document.querySelectorAll('#jadwalListTahap2 .jadwal-date-link').forEach(function(el) {
    el.addEventListener('click', function() {
        document.getElementById('jadwalListTahap2').style.display = 'none';
        document.getElementById('jadwalFormTahap2').style.display = 'block';
    });
});
</script>