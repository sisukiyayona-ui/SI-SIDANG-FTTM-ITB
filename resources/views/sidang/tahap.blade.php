<div class="card-body tahap-container">
    <style>
        .tahap-container {
            color: #1e293b;
        }

        /* Select2 full-width (mis. dropdown Nama di form Tim) */
        .tahap-container .form-group .select2-container {
            width: 100% !important;
        }
        .tahap-container .select2-container .select2-selection--single {
            height: calc(1.8125rem + 2px);
            display: flex;
            align-items: center;
        }
        .tahap-container .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            flex: 1;
            line-height: 1.5;
            padding-left: 12px;
        }
        .tahap-container .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
            height: calc(1.8125rem + 2px);
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
                            <input type="text" name="no_sk" class="form-control" placeholder="Masukkan No SK (kosongkan jika tidak ada)">
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
                    <a class="nav-link font-weight-bold p-0 text-primary" id="kpps-tab" data-toggle="tab" href="#kpps-voting" role="tab" style="text-decoration: underline;">Hasil Voting Tim KPPS</a>
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
                                    @php $syaratId = $item->ID_SYARAT_SIDANG ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span>{{ $item->PERSYARATAN ?? $item->NAMA_PERSYARATAN }}</span></td>
                                        <td>
                                            <input type="checkbox" {{ isset($item->STATUS_LENGKAP) && $item->STATUS_LENGKAP === 'y' ? 'checked' : '' }} data-syarat-id="{{ $syaratId }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ isset($item->LINK_FILE) && $item->LINK_FILE ? $item->LINK_FILE : '#' }}" target="_blank" class="mr-2 text-primary {{ isset($item->LINK_FILE) && $item->LINK_FILE ? '' : 'd-none' }}" style="font-size: 13px;" id="link-{{ $syaratId }}">Lihat file</a>
                                                <span id="check-{{ $syaratId }}" class="mr-2 text-success {{ isset($item->LINK_FILE) && $item->LINK_FILE ? '' : 'd-none' }}"><i class="fas fa-check-circle"></i></span>
                                                @if(!in_array(session('auth_user.role'), ['FS']))
                                                <div class="upload-container" style="position: relative; width: 34px;">
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" accept=".pdf" onchange="window.uploadFile(this, '{{ $syaratId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                                    <label for="file-{{ $syaratId }}" class="btn btn-light bg-white border py-0 px-2 text-dark upload-btn" style="cursor: pointer; margin-bottom: 0;"><i class="fas fa-upload" style="font-size: 14px;"></i></label>
                                                    <div id="progress-{{ $syaratId }}" class="progress mt-1 d-none" style="height: 4px; position: absolute; bottom: -8px; left: 0; right: 0;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted" style="font-size: 12px;">Tidak dapat mengunggah</span>
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
                    <div class="text-right mt-3">
                        @if(!in_array(session('auth_user.role'), ['FS']))
                        <button type="button" class="btn btn-primary" onclick="window.savePersyaratan('{{ $tahapan }}')">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        @endif
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
                                            <select class="form-control form-control-sm border-dark rounded-0 select2-search" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
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
                                    <input type="hidden" name="urutan" value="">
                                    @if(session('auth_user.role') !== 'Pembimbing' && session('auth_user.role') !== 'Penguji')
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">File Penelaah</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="file" name="file_penelaah" class="form-control form-control-sm border-dark rounded-0" accept="application/pdf">
                                        </div>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-4">
                                        <button type="button" class="btn btn-secondary" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timForm').style.display='none'; document.getElementById('timAddBtn').style.display='block';">Batal</button>
                                        <button type="submit" class="btn btn-primary" style="font-size: 13px; border-radius: 0;">Simpan</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtn" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" {{ isset($ajuan) && $ajuan->status_lulus === 'lulus' ? 'disabled' : '' }} onclick="showAddTimForm('timForm', 'timAddBtn')"><i class="fas fa-plus mr-1"></i> Tambah</button>
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
                                        <th style="width: 20%;">No SK</th>
                                        <th style="width: 15%;">File</th>@if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))<th style="width: 10%;">Aksi</th>@endif
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
                                                 data-urutan="{{ $tim->urutan }}"
                                                 data-file-penelaah="{{ $tim->FILE_PENELAAH }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     @if($tim->FILE_PENELAAH)
                                                         <a href="{{ $tim->FILE_PENELAAH }}" target="_blank" class="text-primary" style="font-size:12px; text-decoration:underline;">Lihat file</a>
                                                     @else
                                                         -
                                                     @endif
                                                 </td>
                                                 @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
                                                  <td>
                                                      <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                      @if(session('auth_user.role') === 'TU Prodi')
                                                      <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                      @endif
                                                  </td>
                                                 @endif
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="{{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? '7' : '6' }}" class="text-center text-muted">Belum ada tim penguji</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: HASIL VOTING TIM KPPS --}}
                <div class="tab-pane fade" id="kpps-voting" role="tabpanel">
                    <div class="text-muted font-weight-bold mb-3" style="font-size: 14px;">
                        Hasil Voting Tim <span class="text-danger" style="text-decoration: underline;">KPPS</span>
                    </div>
                    @php
                        $appAjuan = \App\Models\TAjuanSidang::where('id_judul', $idJudul)
                            ->where('tahapan_sidang', $tahapan)
                            ->where('status_ajukan_kpps', 'y')
                            ->first();
                        $kppsApps = collect();
                        if ($appAjuan) {
                            $kppsApps = \Illuminate\Support\Facades\DB::table('t_app_ajuan_sidang as app')
                                ->join('t_user as u', 'app.ID_USER', '=', 'u.ID')
                                ->where('app.ID_AJUAN_SIDANG', $appAjuan->id)
                                ->select('app.*', 'u.NIP_NIM', 'u.NAMA_LENGKAP')
                                ->get();
                        }
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 8%;">No</th>
                                    <th style="width: 20%;">NIP</th>
                                    <th style="width: 30%;">Nama KPPS</th>
                                    <th style="width: 20%;">Status Tim</th>
                                    <th style="width: 22%;">Status Approve</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($kppsApps->count() > 0)
                                    @foreach($kppsApps as $idx => $app)
                                        <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $app->NIP_NIM ?? '-' }}</td>
                                            <td class="text-left">{{ $app->NAMA_LENGKAP ?? '-' }}</td>
                                            <td>{{ $app->STATUS_TIM ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ ($app->STATUS_APPROVE ?? '') === 'y' ? 'success' : 'secondary' }}">
                                                    {{ ($app->STATUS_APPROVE ?? '') === 'y' ? 'Disetujui' : 'Menunggu' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr style="background-color: #dbe5f1;">
                                        <td colspan="5" class="text-center text-muted">Belum ada data voting KPPS</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB: JADWAL SIDANG & PENILAIAN --}}
                <div class="tab-pane fade" id="jadwal" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row align-items-center mb-2">
                                <label class="col-sm-4 text-danger mb-0 px-1" style="font-size: 13px; text-decoration: underline;">Penilai</label>
                                <div class="col-sm-8 px-1">
                                    <select class="form-control form-control-sm select2-search" id="penilaianSelect" onchange="document.getElementById('selectedTimSidangTahap1').value=this.value;clearReferenceRows('penilaianReportBody');filterPenilaian();">
                                        <option value="">-- Pilih Penilai --</option>
                                        @if(isset($timSidang) && $timSidang->count() > 0)
                                            @foreach($timSidang as $tim)
                                                <option value="{{ $tim->id }}" data-keterangan="{{ $tim->keterangan ?? $tim->status_tim_sidang }}">{{ $tim->Nama }} ({{ $tim->nip }})</option>
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
                                    <select class="form-control form-control-sm select2-search" id="formFilterSelect" onchange="filterPenilaian()">
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
                                    <th style="width: 15%;"><span class="text-danger text-decoration-underline">Nilai (Range 1 - 5)</span></th>
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
                                             <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-data-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}" data-status-catatan="{{ $point->status_catatan }}">
                                                <td>{{ $rowNum }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm nilai-input" style="width: 80px; margin: auto;" value="" min="1" max="5">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm catatan-input" value="">
                                                </td>
                                            </tr>
                                            @foreach($existingRecords as $existing)
                                                @php $rowNum++; @endphp
                                                <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-data-row" data-id-penilai="{{ $existing->id_tim_sidang }}" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}" data-status-catatan="{{ $point->status_catatan }}">
                                                    <td>{{ $rowNum }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm nilai-input" style="width: 80px; margin: auto;" value="{{ $existing->Nilai }}" min="1" max="5">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm catatan-input" value="{{ $existing->catatan }}">
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
                            @if(!in_array(session('auth_user.role'), ['FS']))
                            <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusDisplay" style="width: 150px;">
                                <option value="lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                <option value="tidak lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                            </select>
                            @else
                            <span class="font-weight-bold ml-2 text-uppercase" style="color: {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? '#28a745' : ((isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? '#dc3545' : '#6c757d') }};">{{ isset($ajuan) && $ajuan->status_lulus ? $ajuan->status_lulus : 'Belum ditentukan' }}</span>
                            @endif
                        </div>
                        @if(!in_array(session('auth_user.role'), ['FS']))
                        @php
                            // Item 16: Cek apakah penilai sudah mengunci nilai
                            $isTUKunci = in_array(session('auth_user.role'), ['TU Prodi', 'Admin']);
                            $isNilaiTerkunci = false;
                            if (!$isTUKunci && isset($penilaian) && $penilaian->count() > 0) {
                                $isNilaiTerkunci = $penilaian->where('NILAI_TERKUNCI', 1)->count() > 0;
                            }
                        @endphp
                        <div class="d-flex align-items-center">
                            <button type="button" id="lockNilaiBtn" class="btn btn-sm btn-success mr-2 px-2 py-0" onclick="lockNilai('{{ $tahapan }}', 'penilaianReportBody', 'statusLulusDisplay', 'lockNilaiBtn')" title="Kunci Nilai" {{ $isNilaiTerkunci ? 'disabled' : '' }}><i class="fas fa-lock"></i> Kunci Nilai</button>
                            <button type="button" id="savePenilaianBtn" class="btn btn-primary" style="font-size: 14px;" onclick="savePenilaianTahap1()" {{ $isNilaiTerkunci ? 'disabled' : '' }}>Simpan</button>
                        </div>
                        @endif
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
                @if(isset($ajuan) && $ajuan->status_lulus)
                    <span class="badge bg-{{ getStatusColor($ajuan->status_lulus ?? '') }}">{{ $ajuan->status_lulus ?? 'Belum ditentukan' }}</span>
                @else
                    <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusDisplay2" style="width: 150px;">
                        <option value="lulus">Lulus</option>
                        <option value="tidak lulus">Tidak Lulus</option>
                    </select>
                    <button type="button" class="btn btn-primary ml-2" style="font-size: 13px; border-radius: 0;" onclick="saveStatusLulus2()">Simpan</button>
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
                        Persyaratan Sidang <span class="text-danger text-decoration-underline">{{ getTahapLabel($tahapan) }}</span>
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
                                    @php $syaratId = $item->ID_SYARAT_SIDANG ?? $item->id; @endphp
                                    <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }};">
                                        <td>{{ $idx + 1 }}</td>
                                        <td class="text-left"><span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">{{ $item->PERSYARATAN ?? $item->NAMA_PERSYARATAN }}</span></td>
                                        <td>
                                            <input type="checkbox" {{ isset($item->STATUS_LENGKAP) && $item->STATUS_LENGKAP === 'y' ? 'checked' : '' }} data-syarat-id="{{ $syaratId }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-center">
                                                <a href="{{ isset($item->LINK_FILE) && $item->LINK_FILE ? $item->LINK_FILE : '#' }}" target="_blank" class="mr-2 text-primary {{ isset($item->LINK_FILE) && $item->LINK_FILE ? '' : 'd-none' }}" style="font-size: 13px;" id="link-{{ $syaratId }}">Lihat file</a>
                                                <span id="check-{{ $syaratId }}" class="mr-2 text-success {{ isset($item->LINK_FILE) && $item->LINK_FILE ? '' : 'd-none' }}"><i class="fas fa-check-circle"></i></span>
                                @if(!in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']))
                                                <div class="upload-container" style="position: relative; width: 34px;">
                                                    <input type="file" class="d-none" id="file-{{ $syaratId }}" accept=".pdf" onchange="window.uploadFile(this, '{{ $syaratId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                                    <label for="file-{{ $syaratId }}" class="btn btn-light bg-white border py-0 px-2 text-dark upload-btn" style="cursor: pointer; margin-bottom: 0;"><i class="fas fa-upload" style="font-size: 14px;"></i></label>
                                                    <div id="progress-{{ $syaratId }}" class="progress mt-1 d-none" style="height: 4px; position: absolute; bottom: -8px; left: 0; right: 0;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted" style="font-size: 12px;">Tidak dapat mengunggah</span>
                                                @endif
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
                                        <div class="d-flex align-items-center justify-content-center">
                                            <a href="#" target="_blank" class="mr-2 text-primary d-none" style="font-size: 13px;" id="link-{{ $hfakerId }}">Lihat file</a>
                                            <span id="check-{{ $hfakerId }}" class="mr-2 text-success d-none"><i class="fas fa-check-circle"></i></span>
                                                @if(!in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']))
                                                <div class="upload-container" style="position: relative; width: 34px;">
                                                    <input type="file" class="d-none" id="file-{{ $hfakerId }}" accept=".pdf" onchange="window.uploadFile(this, '{{ $hfakerId }}', '{{ $tahapan }}', '{{ $idJudul }}')">
                                                    <label for="file-{{ $hfakerId }}" class="btn btn-light bg-white border py-0 px-2 text-dark upload-btn" style="cursor: pointer; margin-bottom: 0;"><i class="fas fa-upload" style="font-size: 14px;"></i></label>
                                                    <div id="progress-{{ $hfakerId }}" class="progress mt-1 d-none" style="height: 4px; position: absolute; bottom: -8px; left: 0; right: 0;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                                @else
                                                <span class="text-muted" style="font-size: 12px;">Tidak dapat mengunggah</span>
                                                @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="text-right mt-3">
                        @if(!in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']))
                        <button type="button" class="btn btn-primary" onclick="savePersyaratanTahap()">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="tim" role="tabpanel">
                    {{-- TIM: FORM LEFT, REPORT RIGHT --}}
                    <div class="row">
                        @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
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
                                            <select class="form-control form-control-sm border-dark rounded-0 select2-search" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
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
                                    <input type="hidden" name="urutan" id="urutanHidden" value="">
                                    @if(session('auth_user.role') !== 'Pembimbing' && session('auth_user.role') !== 'Penguji')
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">File Penelaah</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="file" name="file_penelaah" class="form-control form-control-sm border-dark rounded-0" accept="application/pdf">
                                        </div>
                                    </div>
                                    @endif
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-primary" style="font-size: 13px; border-radius: 0;">Simpan</button>
                                        <button type="button" class="btn btn-secondary" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timFormTahap2').style.display='none'; document.getElementById('timAddBtnTahap2').style.display='block';">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtnTahap2" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" {{ isset($ajuan) && $ajuan->status_lulus === 'lulus' ? 'disabled' : '' }} onclick="showAddTimForm('timFormTahap2', 'timAddBtnTahap2')"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            </div>
                        </div>
                        @endif
                        {{-- KANAN: REPORT --}}
                        <div class="col-md-{{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? '6' : '12' }}">
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
                                        <th style="width: 20%;">No SK</th>
                                        <th style="width: 15%;">File</th>@if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))<th style="width: 10%;">Aksi</th>@endif
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
                                                 data-urutan="{{ $tim->urutan }}"
                                                 data-file-penelaah="{{ $tim->FILE_PENELAAH }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     @if($tim->FILE_PENELAAH)
                                                         <a href="{{ $tim->FILE_PENELAAH }}" target="_blank" class="text-primary" style="font-size:12px; text-decoration:underline;">Lihat file</a>
                                                     @else
                                                         -
                                                     @endif
                                                 </td>
                                                 @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
                                                  <td>
                                                      <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                      @if(session('auth_user.role') === 'TU Prodi')
                                                      <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                      @endif
                                                  </td>
                                                 @endif
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="{{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? '7' : '6' }}" class="text-center text-muted">Belum ada tim penguji</td>
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
                                Jadwal Sidang {{ getTahapLabel($tahapan) }}
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" {{ isset($ajuan) && $ajuan->status_lulus === 'lulus' ? 'disabled' : '' }} {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']) ? 'disabled' : '' }} onclick="openJadwalForm(null)"><i class="fas fa-plus mr-1"></i> Tambah</button>
                        </div>
                        <table class="table table-bordered table-sm text-center">
                            <thead style="background-color: #6998d3; color: white;">
                                <tr>
                                    <th style="width: 10%;">No</th>
                                    <th style="width: 35%;">Jadwal</th>
                                    <th style="width: 15%;">Status Lulus</th>
                                    <th style="width: 25%;">Penilaian Seminar/Sidang</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $visibleAjuan = isset($allAjuan) ? $allAjuan->filter(function($a) { return $a->tgl_sidang && (session('auth_user.role') !== 'FS' || ($a->STATUS_AJUKAN_PRODI ?? 't') === 'y'); }) : collect(); @endphp
                                @if($visibleAjuan->isNotEmpty())
                                    @foreach($visibleAjuan as $idx => $a)
                                    <tr style="background-color: #dbe5f1;">
                                        <td>{{ $idx + 1 }}</td>
                                        <td><span class="text-primary text-decoration-underline jadwal-date-link" style="cursor: {{ (in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) || ($a->status_lulus ?? '') === 'tidak lulus') ? 'default' : 'pointer' }}; white-space: nowrap;" {{ (in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) || ($a->status_lulus ?? '') === 'tidak lulus') ? '' : 'onclick="openJadwalForm(this)"' }} data-id="{{ $a->id }}" data-tgl-sidang="{{ $a->tgl_sidang }}" data-waktu-sidang="{{ $a->waktu_sidang }}" data-waktu-selesai="{{ $a->waktu_selesai }}" data-ruang-sidang="{{ $a->ruang_sidang }}" data-tgl-surat-undangan="{{ $a->tgl_undangan }}" data-no-surat-undangan="{{ $a->NO_UNDANGAN }}" data-tgl-surat-penelaah="{{ $a->tgl_penelaah }}" data-no-surat-penelaah="{{ $a->no_surat_penelaah }}" data-tgl-hasil-penelahan="{{ $a->TGL_HASIL_PENELAHAN }}" data-email-surat="{{ $a->email_surat }}" data-no-sk-kelulusan="{{ $a->SK_LULUS }}">{{ \Carbon\Carbon::parse($a->tgl_sidang)->translatedFormat('l, d F Y') }}</span></td>
                                        <td><span class="badge bg-{{ getStatusColor($a->status_lulus ?? 'Dalam Proses') }}">{{ $a->status_lulus ?? 'Dalam Proses' }}</span></td>
                                        <td>
                                            <button type="button" class="btn btn-sm px-3 py-1" style="font-size: 12px; border-radius: 4px; color: #003366; border-color: #003366; background: transparent;" onmouseover="this.style.background='#003366'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#003366';" onclick="document.getElementById('jadwalListTahap2').style.display='none'; document.getElementById('penilaianFormTahap2').style.display='block';" {{ ($a->status_lulus ?? '') === 'tidak lulus' ? 'disabled' : '' }}>Penilaian</button>
                                        </td>
                                        <td>
                                            @if(session('auth_user.role') === 'TU Prodi' && ($a->status_lulus ?? '') !== 'lulus')
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 12px; border-radius: 4px;" onclick="hapusJadwal({{ $a->id }}, '{{ $tahapan }}', {{ $idJudul }})"><i class="fas fa-trash mr-1"></i> Hapus</button>
                                            @else
                                            -
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr style="background-color: #dbe5f1;">
                                        <td colspan="5" class="text-center text-muted">Belum ada jadwal</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- PENILAIAN FORM TAHAP II --}}
                    <div id="penilaianFormTahap2" style="display: none;">
                        <div class="text-muted mb-3" style="font-size: 13px;">
                            Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Penilaian</span> Seminar/Sidang {{ getTahapLabel($tahapan) }}
                        </div>
                        <form id="penilaianFormTahap2Form" onsubmit="return false">
                            @csrf
                            <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                            <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                            <input type="hidden" name="id_tim_sidang" id="selectedTimSidangTahap2">
                            <input type="hidden" name="no_form" id="selectedNoFormTahap2">
                            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                                <div class="d-flex align-items-center">
                                    <label class="text-danger mb-0 text-nowrap mr-2" style="font-size: 12px; text-decoration: underline; text-decoration-color: red;">Penilai</label>
                                    <select class="form-control form-control-sm border-dark rounded-0 select2-search" id="penilaiTahap2" name="id_user_penilai" style="width: 150px;" onchange="document.getElementById('selectedTimSidangTahap2').value=this.value;document.getElementById('selectedNoFormTahap2').value=document.getElementById('formTahap2').value;clearReferenceRows('penilaianTahap2Body');filterPenilaianTahap2();" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'disabled' : '' }}>
                                        <option value="">Pilih Penilai</option>
                                        @if(isset($timSidang) && $timSidang->count() > 0)
                                            @foreach($timSidang as $tim)
                                                <option value="{{ $tim->id }}" {{ $tim->id_user_penilai == session('auth_user.id') ? 'selected' : '' }} data-keterangan="{{ $tim->keterangan ?? $tim->status_tim_sidang }}">{{ $tim->Nama }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <label class="text-danger mb-0 text-nowrap mr-2 ml-3" style="font-size: 12px; text-decoration: underline; text-decoration-color: red;">Form</label>
                                    <select class="form-control form-control-sm border-dark rounded-0 select2-search" id="formTahap2" name="no_form_display" style="width: 160px;" onchange="filterPenilaianTahap2(); document.getElementById('selectedNoFormTahap2').value = this.value">
                                        <option value="">Pilih Form</option>
                                        @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                            @foreach($pointPenilaian as $form)
                                                <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-black px-2 py-0" style="font-size: 12px; border-radius: 0;" onclick="cetakForm()">Cetak Penilaian</button>
                                    <button type="button" class="btn btn-outline-dark px-2 py-0 bg-white text-danger ml-1" style="font-size: 12px; border-radius: 0;" onclick="cetakBeritaAcara()">BA Sidang</button>
                                </div>
                            </div>
                            {{-- REPORT TABLE --}}
                            <table class="table table-bordered table-sm text-center">
                                <thead style="background-color: #6998d3; color: white;">
                                    <tr>
                                        <th style="width: 8%;">No</th>
                                        <th style="width: 30%;">Parameter Penilaian</th>
                                        <th style="width: 25%;">Keterangan</th>
                                        <th style="width: 12%;">Nilai (Range 1 - 5)</th>
                                        <th style="width: 25%;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody id="penilaianTahap2Body">
                                    <tr id="penilaianTahap2EmptyRow" style="background-color: #dbe5f1;">
                                        <td colspan="5" class="text-center text-muted">Pilih Form untuk melihat parameter penilaian</td>
                                    </tr>
                                    @if(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                        @php $rowNum = 0; @endphp
                                        @foreach($allPointPenilaian as $point)
                                            @php
                                                $existingRecords = (isset($penilaian) && $penilaian->count() > 0) ? $penilaian->where('id_penilaian', $point->id) : collect();
                                            @endphp
                                            @php $rowNum++; @endphp
                                             <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-tahap2-row" data-id-penilai="" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}" data-status-catatan="{{ $point->status_catatan }}">
                                                <td>{{ $rowNum }}</td>
                                                <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                <td class="text-left"><span class="text-muted" style="font-size: 13px;">{{ $point->keterangan ?? '-' }}</span></td>
                                                <td>
                                                    <input type="number" name="penilaian[{{ $rowNum }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                    <input type="number" class="form-control form-control-sm nilai-input" style="width: 80px; margin: auto;" name="penilaian[{{ $rowNum }}][nilai]" value="" min="1" max="5">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm catatan-input" name="penilaian[{{ $rowNum }}][catatan]" value="">
                                                </td>
                                            </tr>
                                            @foreach($existingRecords as $existing)
                                                @php $rowNum++; @endphp
                                                <tr style="background-color: {{ $rowNum % 2 == 0 ? '#e9eef6' : '#dbe5f1' }}; display: none;" class="penilaian-tahap2-row" data-id-penilai="{{ $existing->id_tim_sidang }}" data-no-form="{{ $point->no_form }}" data-point-id="{{ $point->id }}" data-status-catatan="{{ $point->status_catatan }}">
                                                    <td>{{ $rowNum }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td class="text-left"><span class="text-muted" style="font-size: 13px;">{{ $point->keterangan ?? '-' }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $rowNum }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                        <input type="number" class="form-control form-control-sm nilai-input" style="width: 80px; margin: auto;" name="penilaian[{{ $rowNum }}][nilai]" value="{{ $existing->Nilai }}" min="1" max="5">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm catatan-input" name="penilaian[{{ $rowNum }}][catatan]" value="{{ $existing->catatan }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            {{-- STATUS KELULUSAN --}}
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0 mr-3" style="font-size: 12px; border-radius: 0;" onclick="document.getElementById('penilaianFormTahap2').style.display='none'; document.getElementById('jadwalListTahap2').style.display='block';">&larr; Kembali</button>
                                    <span class="mr-4 font-weight-bold">Status Kelulusan</span>
                                    @if(!in_array(session('auth_user.role'), ['FS']))
                                    <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusTahap2" style="width: 340px;">
                                        <option value="">Pilih Status</option>
                                        @if(in_array(strtolower($tahapan), ['tahap iii', 'tahap iv', 'sk i', 'sk ii', 'sk iii', 'sk iv']))
                                            <option value="lulus" {{ (isset($ajuan) && strtolower($ajuan->status_lulus ?? '') === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                            <option value="tidak lulus" {{ (isset($ajuan) && strtolower($ajuan->status_lulus ?? '') === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                                        @else
                                            <option value="Layak tanpa perbaikan" {{ (isset($ajuan) && $ajuan->status_lulus === 'Layak tanpa perbaikan') ? 'selected' : '' }}>1. Layak tanpa perbaikan</option>
                                            <option value="Layak dengan perbaikan minor tanpa harus dibaca kembali" {{ (isset($ajuan) && $ajuan->status_lulus === 'Layak dengan perbaikan minor tanpa harus dibaca kembali') ? 'selected' : '' }}>2. Layak dengan perbaikan minor tanpa harus dibaca kembali</option>
                                            <option value="Layak dengan perbaikan minor dan perbaikan harus dibaca kembali" {{ (isset($ajuan) && $ajuan->status_lulus === 'Layak dengan perbaikan minor dan perbaikan harus dibaca kembali') ? 'selected' : '' }}>3. Layak dengan perbaikan minor dan perbaikan harus dibaca kembali</option>
                                            <option value="Layak dengan perbaikan major (substansial)" {{ (isset($ajuan) && $ajuan->status_lulus === 'Layak dengan perbaikan major (substansial)') ? 'selected' : '' }}>4. Layak dengan perbaikan major (substansial)</option>
                                            <option value="Tidak layak" {{ (isset($ajuan) && $ajuan->status_lulus === 'Tidak layak') ? 'selected' : '' }}>5. Tidak layak</option>
                                        @endif
                                    </select>
                                    @else
                            <span class="badge bg-{{ getStatusColor($ajuan->status_lulus ?? '') }}">{{ $ajuan->status_lulus ?? 'Belum ditentukan' }}</span>
                                    @endif
                                </div>
                                @if(!in_array(session('auth_user.role'), ['FS']))
                                <div class="d-flex align-items-center">
                                    <button type="button" id="lockNilaiTahap2Btn" class="btn btn-sm btn-success mr-2 px-2 py-0" onclick="lockNilai('{{ $tahapan }}', 'penilaianTahap2Body', 'statusLulusTahap2', 'lockNilaiTahap2Btn')" title="Kunci Nilai"><i class="fas fa-lock"></i> Kunci Nilai</button>
                                    <button type="button" class="btn btn-primary" style="font-size: 14px;" onclick="savePenilaianTahap2()">Simpan</button>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    @if(in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']))
                    <script>
                        (function() {
                            var sel = document.getElementById('penilaiTahap2');
                            if (sel && sel.value) {
                                document.getElementById('selectedTimSidangTahap2').value = sel.value;
                                filterPenilaianTahap2();
                            }
                        })();
                    </script>
                    @endif

                    <div id="jadwalFormTahap2" style="display: none;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="text-muted" style="font-size: 13px;">
                                Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Jadwal Sidang {{ getTahapLabel($tahapan) }}</span>
                            </div>
<div>
                                <button type="button" class="btn btn-black px-3 py-0 mr-2" style="font-size: 13px; border-radius: 0;" onclick="cetakSuratKesediaan()">Surat Kesediaan Penelaah</button>
                                <button type="button" class="btn btn-black px-3 py-0" style="font-size: 13px; border-radius: 0;" onclick="cetakUndangan()">Cetak Undangan</button>
                            </div>
                        </div>
                        <form id="jadwalFormTahap2Form" onsubmit="event.preventDefault(); submitJadwalTahap2(event)">
                            @csrf
                            <input type="hidden" name="id_judul" value="{{ $idJudul }}">
                            <input type="hidden" name="tahapan_sidang" value="{{ $tahapan }}">
                            <input type="hidden" id="id_ajuan_tahap2" name="id_ajuan" value="">
                            <div class="row">
                                <div class="col-md-6">
                                    @if(strtolower($tahapan) === 'tahap iv')
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Jenis Sidang</label>
                                        <div class="col-sm-6 px-2">
                                            <select class="form-control form-control-sm border-dark rounded-0" name="jenis_sidang" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'disabled' : '' }}>
                                                <option value="">Pilih Jenis</option>
                                                <option value="Terbuka" {{ (isset($ajuan) && strtolower($ajuan->JENIS_SIDANG ?? '') == 'terbuka') ? 'selected' : '' }}>Terbuka</option>
                                                <option value="Tertutup" {{ (isset($ajuan) && strtolower($ajuan->JENIS_SIDANG ?? '') == 'tertutup') ? 'selected' : '' }}>Tertutup</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Tgl Seminar/Sidang</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_sidang" value="{{ isset($ajuan) ? $ajuan->tgl_sidang : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Waktu Mulai</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="time" class="form-control form-control-sm border-dark rounded-0" name="waktu_sidang" value="{{ isset($ajuan) ? $ajuan->waktu_sidang : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Waktu Selesai</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="time" class="form-control form-control-sm border-dark rounded-0" name="waktu_selesai" value="{{ isset($ajuan) ? ($ajuan->waktu_selesai ?? '') : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Ruangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="ruang_sidang" value="{{ isset($ajuan) ? $ajuan->ruang_sidang : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No Surat Undangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_surat_undangan" value="{{ isset($ajuan) ? $ajuan->NO_UNDANGAN : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Surat Undangan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_surat_undangan" value="{{ isset($ajuan) ? $ajuan->tgl_undangan : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No Surat Penelaah</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_surat_penelaah" value="{{ isset($ajuan) ? $ajuan->no_surat_penelaah : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Surat Penelaah</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_surat_penelaah" value="{{ isset($ajuan) ? $ajuan->tgl_penelaah : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Tgl Hasil Penilaian</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="date" class="form-control form-control-sm border-dark rounded-0" name="tgl_hasil_penelahan" value="{{ isset($ajuan) ? $ajuan->TGL_HASIL_PENELAHAN : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">Email Surat</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="email" class="form-control form-control-sm border-dark rounded-0" name="email_surat" value="{{ isset($ajuan) ? $ajuan->email_surat : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <!-- <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-6 mb-0" style="font-size: 13px; color: #555;">No SK Kelulusan</label>
                                        <div class="col-sm-6 px-2">
                                            <input type="text" class="form-control form-control-sm border-dark rounded-0" name="no_sk_kelulusan" value="{{ isset($ajuan) ? $ajuan->SK_LULUS : '' }}" {{ in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']) ? 'readonly' : '' }}>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                             <div class="d-flex justify-content-between align-items-center mt-4">
                                 <div>
                                     <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-0" style="font-size: 12px; border-radius: 0;" onclick="kembaliKeJadwalListTahap2()">&larr; Kembali</button>
                                 </div>
                                 <div>
<input type="hidden" name="is_ajukan_fs" value="">
                                      @if(!in_array(session('auth_user.role'), ['Pembimbing', 'Penguji', 'FS']) && (!isset($ajuan) || $ajuan->STATUS_AJUKAN_PRODI !== 'y'))
                                       <button type="button" class="btn btn-success px-3 py-0" style="font-size: 13px; border-radius: 0;" onclick="this.form.is_ajukan_fs.value='1'; submitJadwalTahap2(event);">Ajukan ke FS</button>
                                      @endif
                                      @if(!in_array(session('auth_user.role'), ['Pembimbing', 'Penguji']))
                                       <button type="submit" class="btn btn-primary px-3 py-0" style="font-size: 13px; border-radius: 0;" onclick="submitJadwalTahap2(event)">Simpan</button>
                                      @endif
                                      @if(session('auth_user.role') === 'FS' && (!isset($ajuan) || !$ajuan->STATUS_AJUKAN_KPPS))
                                       <input type="hidden" name="is_ajukan_kpps" value="">
                                       <button type="button" class="btn btn-success px-3 py-0 ml-2" style="font-size: 13px; border-radius: 0;" onclick="this.form.is_ajukan_kpps.value='1'; submitJadwalTahap2(event);">Ajukan ke KPPS</button>
                                      @endif
                                 </div>
                             </div>
                        </form>
                    </div>


                    <div id="jadwalForm" style="display: none;">
                        <div class="row">
                            <div class="col-md-5" style="border-right: 1px solid #ddd;">
                                <div class="text-muted mb-3" style="font-size: 13px;">
                                    Form <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Tambah</span>/ Edit <span class="text-danger" style="text-decoration: underline; text-decoration-color: red;">Jadwal Sidang</span> {{ getTahapLabel($tahapan) }}
                                </div>
                                <div class="form-group row align-items-center mb-2 px-1">
                                    <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">Tanggal</label>
                                    <div class="col-sm-8 px-2">
                                        <input type="text" class="form-control form-control-sm border-dark rounded-0" value="{{ isset($ajuan) && $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') : '' }}">
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
                                            @php $color = getStatusColor($ajuan->status_lulus ?? ''); @endphp
                                            <span class="badge bg-{{ $color }}">{{ $ajuan->status_lulus ?? 'Belum ditentukan' }}</span>
                                        </div>
                                    </div>
                                    @endif
                                <div class="text-right mt-4">
                                    @if($isKetuaPembimbing ?? false)
                                    <button type="button" class="btn btn-primary mr-2" style="font-size: 13px; border-radius: 0;" onclick="updateStatusLulus({{ $ajuan->id ?? 0 }})">Simpan Status</button>
                                    @endif
                                    <button type="button" class="btn btn-primary" style="font-size: 13px; border-radius: 0;">Simpan</button>
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
                                            <select class="form-control form-control-sm border-dark rounded-0 select2-search" id="penilaiSelect" onchange="document.getElementById('formSelect').value='';loadPenilaianForm();">
                                                <option value="">Pilih Penilai</option>
                                                @if(isset($timSidang) && $timSidang->count() > 0)
                                                    @foreach($timSidang as $tim)
                                                        <option value="{{ $tim->id }}" {{ $tim->id_user_penilai == $user['id'] ? 'selected' : '' }} data-keterangan="{{ $tim->keterangan ?? $tim->status_tim_sidang }}">{{ $tim->Nama }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <label class="col-sm-2 mb-0 px-1 text-center" style="font-size: 13px; color: #555;">Form</label>
                                        <div class="col-sm-2 px-1">
                                            <select class="form-control form-control-sm border-dark rounded-0 select2-search" id="formSelect" onchange="loadPenilaianForm()">
                                                <option value="">Pilih Form</option>
                                                @if(isset($pointPenilaian) && $pointPenilaian->count() > 0)
                                                    @foreach($pointPenilaian as $form)
                                                        <option value="{{ $form->no_form }}">{{ $form->no_form }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-sm-2 pl-0 d-flex align-items-center">
                                            <button type="submit" class="btn btn-primary" style="font-size: 12px; border-radius: 0;">Simpan</button>
                                        </div>
                                        @if($isKetuaPembimbing ?? false)
                                        <div class="col-sm-2 pl-0 d-flex align-items-center ml-2">
                                            <button type="button" id="lockNilaiPembimbingBtn" class="btn btn-sm btn-success px-2 py-0" onclick="lockNilai('{{ $tahapan }}', 'penilaianTableBody', 'statusLulusPembimbing', 'lockNilaiPembimbingBtn')" title="Kunci Nilai" style="font-size: 12px;"><i class="fas fa-lock"></i> Kunci Nilai</button>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($isKetuaPembimbing ?? false)
                                    <div class="d-flex align-items-center mb-2 mt-3">
                                        <span class="mr-2 font-weight-bold" style="font-size: 13px;">Status Kelulusan</span>
                                        <select class="form-control form-control-sm border-dark rounded-0" id="statusLulusPembimbing" style="width: 150px;">
                                            <option value="">Pilih Status</option>
                                            <option value="lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'lulus') ? 'selected' : '' }}>Lulus</option>
                                            <option value="tidak lulus" {{ (isset($ajuan) && $ajuan->status_lulus === 'tidak lulus') ? 'selected' : '' }}>Tidak Lulus</option>
                                        </select>
                                    </div>
                                    @endif

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
                                                 <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }}; display: none;" data-id="{{ $nilai->id_penilaian }}" data-no-form="{{ $nilai->no_form }}" data-status-catatan="{{ $nilai->pointPenilaian->STATUS_CATATAN ?? null }}">
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $nilai->nama_penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $idx }}][id_penilaian]" value="{{ $nilai->id_penilaian }}" hidden>
                                                        <input type="number" name="penilaian[{{ $idx }}][nilai]" value="{{ $nilai->Nilai }}" class="form-control form-control-sm nilai-input" min="0" max="100" style="width: 60px; margin: auto;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="penilaian[{{ $idx }}][catatan]" value="{{ $nilai->catatan }}" class="form-control form-control-sm catatan-input" style="font-size: 12px;">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($allPointPenilaian) && $allPointPenilaian->count() > 0)
                                                @foreach($allPointPenilaian as $idx => $point)
                                                <tr style="background-color: {{ $idx % 2 == 0 ? '#dbe5f1' : '#e9eef6' }}; display: none;" data-id="{{ $point->id }}" data-no-form="{{ $point->no_form }}" data-status-catatan="{{ $point->status_catatan }}">
                                                    <td>{{ $idx + 1 }}</td>
                                                    <td><span class="text-danger text-decoration-underline">{{ $point->penilaian }}</span></td>
                                                    <td>
                                                        <input type="number" name="penilaian[{{ $idx }}][id_penilaian]" value="{{ $point->id }}" hidden>
                                                        <input type="number" name="penilaian[{{ $idx }}][nilai]" value="" class="form-control form-control-sm nilai-input" min="0" max="100" style="width: 60px; margin: auto;">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="penilaian[{{ $idx }}][catatan]" value="" class="form-control form-control-sm catatan-input" style="font-size: 12px;">
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
                                        <a href="#" onclick="cetakBeritaAcara()" class="text-dark ml-2" style="font-size: 11px; text-decoration: underline;">BA Sidang</a>
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
                        @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
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
                                            <select class="form-control form-control-sm border-dark rounded-0 select2-search" name="id_user_penilai" onchange="var f=this.closest('form');var n=f.querySelector('[name=nip]');if(n)n.value=this.options[this.selectedIndex]?this.options[this.selectedIndex].getAttribute('data-nip')||'':''">
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
                                    <input type="hidden" name="urutan" id="urutanHidden" value="">
                                    @if(session('auth_user.role') !== 'Pembimbing' && session('auth_user.role') !== 'Penguji')
                                    <div class="form-group row align-items-center mb-2 px-1">
                                        <label class="col-sm-4 text-danger mb-0" style="font-size: 13px; text-decoration: underline; text-decoration-color: red;">File Penelaah</label>
                                        <div class="col-sm-8 px-2">
                                            <input type="file" name="file_penelaah" class="form-control form-control-sm border-dark rounded-0" accept="application/pdf">
                                        </div>
                                    </div>
                                    @endif
                                    <div class="text-right mt-4">
                                        <button type="submit" class="btn btn-primary" style="font-size: 13px; border-radius: 0;">Simpan</button>
                                        <button type="button" class="btn btn-secondary" style="font-size: 13px; border-radius: 0;" onclick="document.getElementById('timFormTahap2').style.display='none'; document.getElementById('timAddBtnTahap2').style.display='block';">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <div id="timAddBtnTahap2" style="display: none;">
                                <button type="button" class="btn btn-primary btn-sm" {{ isset($ajuan) && $ajuan->status_lulus === 'lulus' ? 'disabled' : '' }} onclick="showAddTimForm('timFormTahap2', 'timAddBtnTahap2')"><i class="fas fa-plus mr-1"></i> Tambah</button>
                            </div>
                        </div>
                        @endif
                        {{-- KANAN: REPORT --}}
                        <div class="col-md-{{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? '6' : '12' }}">
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
                                        <th style="width: 20%;">No SK</th>
                                        <th style="width: 15%;">File</th>@if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))<th style="width: 10%;">Aksi</th>@endif
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
                                                 data-urutan="{{ $tim->urutan }}"
                                                 data-file-penelaah="{{ $tim->FILE_PENELAAH }}">
                                                 <td>{{ $idx + 1 }}</td>
                                                 <td>{{ $tim->nip }}</td>
                                                 <td class="text-left text-primary" style="text-decoration:underline;">{{ $tim->Nama }}</td>
                                                 <td class="text-danger" style="text-decoration:underline;">{{ $tim->keterangan ?? $tim->status_tim_sidang }}</td>
                                                 <td>{{ optional($tim->sk)->no_sk ?? '-' }}</td>
                                                 <td>
                                                     @if($tim->FILE_PENELAAH)
                                                         <a href="{{ $tim->FILE_PENELAAH }}" target="_blank" class="text-primary" style="font-size:12px; text-decoration:underline;">Lihat file</a>
                                                     @else
                                                         -
                                                     @endif
                                                 </td>
                                                 @if(in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']))
                                                  <td>
                                                      <button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button>
                                                      @if(session('auth_user.role') === 'TU Prodi')
                                                      <button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang({{ $tim->id }})" title="Hapus"><i class="fas fa-trash"></i></button>
                                                      @endif
                                                  </td>
                                                 @endif
                                             </tr>
                                         @endforeach
                                    @else
                                        <tr style="background-color: #dbe5f1;">
                                            <td colspan="{{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? '7' : '6' }}" class="text-center text-muted">Belum ada tim penguji</td>
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

    if (typeof persyaratanFiles !== 'undefined') {
        Object.keys(persyaratanFiles).forEach(function(id) {
            formData.append('files[' + id + ']', persyaratanFiles[id]);
        });
    }

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
        showToast('Silakan pilih status kelulusan', 'error');
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
            showToast('Status kelulusan berhasil diperbarui', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Terjadi kesalahan'), 'error');
        }
    })
    .catch(error => {
        showToast('Error: ' + error, 'error');
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

/**
 * Kosongkan isian pada baris referensi (baris tanpa data-id-penilai).
 * Dipanggil saat GANTI penilai supaya tidak ada sisa isian penilai sebelumnya.
 * Baris existing (milik penilai tertentu dari database) tidak disentuh.
 */
function clearReferenceRows(tbodyId) {
    var tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    tbody.querySelectorAll('tr.penilaian-tahap2-row:not([data-id-penilai]), tr.penilaian-data-row:not([data-id-penilai])').forEach(function (row) {
        row.querySelectorAll('.nilai-input, .catatan-input').forEach(function (inp) {
            inp.value = '';
        });
    });
}

function filterPenilaianTahap2() {
    var isLocked = false;
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
        if (!show) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else if (isLocked) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = false;
            });
            applyStatusCatatan(row);
        }
        if (show) hasVisible = true;
    });

    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
    toggleLockButton('penilaianTahap2Body', 'lockNilaiTahap2Btn');
}

function applyStatusCatatan(row) {
    var sc = row.getAttribute('data-status-catatan');
    var catatanInput = row.querySelector('input.catatan-input');
    var nilaiInput = row.querySelector('input.nilai-input');
    if (sc === 'y') {
        if (catatanInput) catatanInput.disabled = false;
        if (nilaiInput) nilaiInput.disabled = true;
    } else {
        if (catatanInput) catatanInput.disabled = true;
        if (nilaiInput) nilaiInput.disabled = false;
    }
}

function filterPenilaian() {
    var isLocked = false;
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
        if (!show) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else if (isLocked) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = false;
            });
            applyStatusCatatan(row);
        }
        if (show) hasVisible = true;
    });

    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
    toggleLockButton('penilaianReportBody', 'lockNilaiBtn');
}

function loadPenilaianForm() {
    const timSidangId = document.getElementById('penilaiSelect').value;
    const noForm = document.getElementById('formSelect').value;
    var isLocked = false;
    
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
        if (!show) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else if (isLocked) {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = true;
            });
        } else {
            row.querySelectorAll('input, textarea, select').forEach(function(inp) {
                inp.disabled = false;
            });
            applyStatusCatatan(row);
        }
        if (show) hasVisible = true;
    });
    
    if (emptyRow) {
        emptyRow.style.display = hasVisible ? 'none' : '';
    }
    toggleLockButton('penilaianTableBody', 'lockNilaiPembimbingBtn');
}

function updateKelengkapan(idSyarat, isChecked) {
    let formData = new FormData();
    formData.append('id_syarat_sidang', idSyarat);
    formData.append('status_lengkap', isChecked ? 'y' : 't');
    formData.append('id_judul', '{{ $idJudul }}');
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route('mahasiswa.update-kelengkapan') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status kelengkapan diperbarui', 'success');
        } else {
            showToast('Gagal update status kelengkapan', 'error');
        }
    }).catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan', 'error');
    });
}

function savePersyaratan(tahapan) {
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('id_judul', '{{ $idJudul }}');
    formData.append('tahapan_sidang', tahapan);

    var checkboxes = document.querySelectorAll('#persyaratan input[type="checkbox"][data-syarat-id]');
    checkboxes.forEach(function(cb) {
        formData.append('kelengkapan[' + cb.getAttribute('data-syarat-id') + ']', cb.checked ? 'y' : 't');
    });

    if (typeof persyaratanFiles !== 'undefined') {
        Object.keys(persyaratanFiles).forEach(function(id) {
            formData.append('files[' + id + ']', persyaratanFiles[id]);
        });
    }

    var btn = document.querySelector('.btn-primary[onclick*="savePersyaratan"]');
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

function uploadFile(input, idSyarat, tahapan, idJudul) {
    if (!input.files || input.files.length === 0) return;

    let file = input.files[0];

    let ext = file.name.split('.').pop().toLowerCase();
    if (ext !== 'pdf') {
        showToast('Hanya file PDF yang diizinkan', 'error');
        input.value = '';
        return;
    }
    if (file.size > 2 * 1024 * 1024) {
        showToast('Maksimal ukuran file 2 MB', 'error');
        input.value = '';
        return;
    }

    if (typeof persyaratanFiles === 'undefined') {
        window.persyaratanFiles = {};
    }
    persyaratanFiles[idSyarat] = file;

    let progressEl = document.getElementById('progress-' + idSyarat);
    if (progressEl) {
        progressEl.classList.remove('d-none');
        let bar = progressEl.querySelector('.progress-bar');
        if (bar) {
            bar.style.width = '0%';
            setTimeout(function() { bar.style.width = '60%'; }, 100);
            setTimeout(function() { bar.style.width = '100%'; }, 400);
            setTimeout(function() { progressEl.classList.add('d-none'); }, 900);
        }
    }

    let linkEl = document.getElementById('link-' + idSyarat);
    if (linkEl) {
        linkEl.href = URL.createObjectURL(file);
        linkEl.classList.remove('d-none');
    }

    let checkIcon = document.getElementById('check-' + idSyarat);
    if (checkIcon) checkIcon.classList.remove('d-none');

    input.value = '';
}

function showAddTimForm(formId, btnId) {
    var form = document.getElementById(formId);
    var btn = document.getElementById(btnId);
    if (!form) return;
    form.style.display = 'block';
    if (btn) btn.style.display = 'none';
    form.querySelector('[name="id"]').value = '';
    form.querySelector('[name="id_sk"]').value = '';
    var penilaiSel = form.querySelector('[name="id_user_penilai"]');
    penilaiSel.value = '';
    if (window.jQuery && jQuery.fn.select2) jQuery(penilaiSel).val('').trigger('change');
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
    var penilaiSel = form.querySelector('[name="id_user_penilai"]');
    penilaiSel.value = idUserPenilai || '';
    if (window.jQuery && jQuery.fn.select2) jQuery(penilaiSel).val(idUserPenilai || '').trigger('change');
    form.querySelector('[name="nip"]').value = nip || '';
    form.querySelector('[name="status_tim_sidang"]').value = statusTimSidang || '';
    form.querySelector('[name="urutan"]').value = urutan || '';

    var formEl = form.closest('[id^="timForm"]');
    if (formEl) formEl.style.display = 'block';
    var isTahap2 = form.id === 'timSidangFormTahap2';
    var btnEl = document.getElementById(isTahap2 ? 'timAddBtnTahap2' : 'timAddBtn');
    if (btnEl) btnEl.style.display = 'none';
}

async function deleteTimSidang(id) {
    var ok = await showConfirmDialog({
        title: 'Hapus Data',
        message: 'Apakah Anda yakin ingin menghapus data ini?',
        confirmText: 'Ya, Hapus',
        type: 'danger'
    });
    if (!ok) return;

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
            // Hapus baris secara lokal — tanpa reload konten modal
            var row = document.querySelector('tr[data-id="' + id + '"]');
            var pane = row ? row.closest('.tab-pane') : null;
            if (row) row.remove();
            if (pane) renumberTimRows(pane);
            // Hapus juga option penilai di dropdown tab penilaian
            removePenilaiOption(id);
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
            // Update baris tabel secara lokal — tanpa reload konten modal
            upsertTimSidangRow(form, formData, data.tim || null);
            // Sinkronkan dropdown Penilai di tab penilaian
            if (data.tim) {
                syncPenilaiDropdowns(data.tim);
            }
            // Form tetap terlihat — hanya bersihkan isian agar siap input berikutnya
            form.reset();
            form.querySelector('[name="id"]').value = '';
            var urutanInput = form.querySelector('[name="urutan"]');
            if (urutanInput) urutanInput.value = '';
            var nipInput = form.querySelector('[name="nip"]');
            if (nipInput) nipInput.value = '';
            if (window.jQuery && jQuery.fn.select2) {
                jQuery(form).find('select[name="id_user_penilai"]').val('').trigger('change');
            }
        } else {
            showToast(data.message || 'Gagal menyimpan tim pembimbing', 'error');
        }
    })
    .catch(error => {
        showToast('Terjadi kesalahan: ' + error, 'error');
    });
}

/**
 * Tambah/update baris tabel Tim Pembimbing & Penguji secara lokal
 * (tanpa fetch ulang konten modal).
 */
function upsertTimSidangRow(form, formData, tim) {
    var pane = form.closest('.tab-pane');
    if (!pane) return;
    var tbody = pane.querySelector('table tbody');
    if (!tbody) return;

    var id          = (tim && tim.id) || formData.get('id') || '';
    var nip         = (tim && tim.nip) || formData.get('nip') || '';
    var statusTim   = (tim && tim.status_tim_sidang) || formData.get('status_tim_sidang') || '';
    var urutan      = (tim && tim.urutan) || formData.get('urutan') || '';
    var idSk        = (tim && tim.id_sk) || formData.get('id_sk') || '';
    var idPenilai   = formData.get('id_user_penilai') || '';

    // Nama penilai dari option select yang terpilih
    var nama = (tim && tim.nama) || '';
    if (!nama) {
        var selPenilai = form.querySelector('[name="id_user_penilai"]');
        if (selPenilai && selPenilai.selectedOptions[0]) nama = selPenilai.selectedOptions[0].textContent.trim();
    }

    // No SK dari option select yang terpilih
    var noSk = '-';
    var selSk = form.querySelector('[name="id_sk"]');
    if (selSk && selSk.value && selSk.selectedOptions[0]) noSk = selSk.selectedOptions[0].textContent.trim();

    var canAksi  = {{ in_array(session('auth_user.role'), ['TU Prodi', 'Admin', 'FS']) ? 'true' : 'false' }};
    var isTuProdi = {{ session('auth_user.role') === 'TU Prodi' ? 'true' : 'false' }};

    var row = id ? tbody.querySelector('tr[data-id="' + id + '"]') : null;

    if (!row) {
        // Hapus baris "Belum ada tim penguji" bila ada
        var emptyTd = tbody.querySelector('td.text-center.text-muted');
        if (emptyTd) emptyTd.closest('tr').remove();

        row = document.createElement('tr');
        row.style.backgroundColor = '#dbe5f1';
        var html =
            '<td></td>' +
            '<td></td>' +
            '<td class="text-left text-primary" style="text-decoration:underline;"></td>' +
            '<td class="text-danger" style="text-decoration:underline;"></td>' +
            '<td></td>' +
            '<td>-</td>';
        if (canAksi) {
            html += '<td>' +
                '<button type="button" class="btn btn-sm btn-warning py-0 px-1" onclick="editTimSidang(this)" title="Edit"><i class="fas fa-edit"></i></button> ' +
                (isTuProdi ? '<button type="button" class="btn btn-sm btn-danger py-0 px-1" onclick="deleteTimSidang(this.closest(\'tr\').dataset.id)" title="Hapus"><i class="fas fa-trash"></i></button>' : '') +
                '</td>';
        }
        row.innerHTML = html;
        tbody.appendChild(row);
    }

    row.setAttribute('data-id', id);
    row.setAttribute('data-id-sk', idSk);
    row.setAttribute('data-id-user-penilai', idPenilai);
    row.setAttribute('data-nip', nip);
    row.setAttribute('data-nama', nama);
    row.setAttribute('data-status-tim-sidang', statusTim);
    row.setAttribute('data-urutan', urutan);

    var tds = row.querySelectorAll('td');
    if (tds.length >= 5) {
        tds[1].textContent = nip;
        tds[2].textContent = nama;
        tds[3].textContent = statusTim;
        tds[4].textContent = noSk;
    }

    renumberTimRows(pane);
}

/**
 * Rapikan ulang nomor urut baris tabel tim pada satu tab.
 */
function renumberTimRows(pane) {
    if (!pane) return;
    pane.querySelectorAll('table tbody tr[data-id]').forEach(function (tr, i) {
        var td = tr.querySelector('td');
        if (td) td.textContent = i + 1;
    });
}

/**
 * Sinkronkan dropdown Penilai (tab penilaian semua tahapan) dengan data tim
 * — dipanggil setelah simpan/edit tim tanpa reload konten modal.
 */
function syncPenilaiDropdowns(tim) {
    if (!tim || !tim.id) return;
    var container = document.querySelector('.tahap-container');
    if (!container) return;

    ['penilaianSelect', 'penilaiSelect', 'penilaiTahap2'].forEach(function (sid) {
        var sel = container.querySelector('select#' + sid);
        if (!sel) return;

        var opt = sel.querySelector('option[value="' + tim.id + '"]');
        if (!opt) {
            opt = document.createElement('option');
            opt.value = tim.id;
            sel.appendChild(opt);
        }
        opt.textContent = (sid === 'penilaianSelect')
            ? ((tim.nama || '') + ' (' + (tim.nip || '') + ')')
            : (tim.nama || '');
        opt.setAttribute('data-keterangan', tim.status_tim_sidang || '');
    });
}

/**
 * Hapus option penilai dari semua dropdown saat tim dihapus.
 */
function removePenilaiOption(id) {
    var container = document.querySelector('.tahap-container');
    if (!container) return;

    ['penilaianSelect', 'penilaiSelect', 'penilaiTahap2'].forEach(function (sid) {
        var sel = container.querySelector('select#' + sid);
        if (!sel) return;
        var opt = sel.querySelector('option[value="' + id + '"]');
        if (opt) {
            // Bila option terpilih, reset dulu
            if (String(sel.value) === String(id)) {
                sel.value = '';
                if (window.jQuery && jQuery.fn.select2 && jQuery(sel).data('select2')) {
                    jQuery(sel).val('').trigger('change');
                }
                // Bersihkan filter penilaian terkait bila ada
                ['formFilterSelect', 'formSelect', 'formTahap2'].forEach(function (fid) {
                    var fs = container.querySelector('select#' + fid);
                    if (fs) {
                        fs.value = '';
                        if (window.jQuery && jQuery.fn.select2 && jQuery(fs).data('select2')) {
                            jQuery(fs).val('').trigger('change');
                        }
                    }
                });
            }
            opt.remove();
        }
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

function toggleLockButton(tbodyId, btnId) {
    var btn = document.getElementById(btnId);
    if (!btn) return;
    var tbody = document.getElementById(tbodyId);
    if (!tbody) { btn.disabled = true; return; }

    var statusLulusId = '';
    if (tbodyId === 'penilaianReportBody') statusLulusId = 'statusLulusDisplay';
    else if (tbodyId === 'penilaianTahap2Body') statusLulusId = 'statusLulusTahap2';
    else if (tbodyId === 'penilaianTableBody') statusLulusId = 'statusLulusPembimbing';
    var statusEl = document.getElementById(statusLulusId);

    var rows = tbody.querySelectorAll('tr[data-status-catatan]');
    var allFilled = true;
    var hasVisible = false;
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        if (row.style.display === 'none') continue;
        if (row.id && row.id.indexOf('EmptyRow') !== -1) continue;
        hasVisible = true;
        var sc = row.getAttribute('data-status-catatan');
        if (sc === 'y') {
            var catatanInput = row.querySelector('input.catatan-input');
            if (catatanInput && !catatanInput.value.trim()) {
                allFilled = false;
                break;
            }
        } else {
            var nilaiInput = row.querySelector('input.nilai-input');
            if (nilaiInput && !nilaiInput.value.trim()) {
                allFilled = false;
                break;
            }
        }
    }
    var enabled = hasVisible && allFilled;
    btn.disabled = !enabled;
    if (statusEl) statusEl.disabled = !enabled;
}

async function lockNilai(tahapan, tbodyId, statusLulusId, btnId) {
    var ok = await showConfirmDialog({
        title: 'Kunci Nilai',
        message: 'Apakah Anda yakin ingin mengunci nilai? Setelah dikunci, nilai tidak dapat diubah.',
        confirmText: 'Ya, Kunci',
        type: 'warning'
    });
    if (!ok) return;
    var statusLulus = document.getElementById(statusLulusId);
    var statusLulusVal = statusLulus ? statusLulus.value : '';
    var idJudul = '{{ $idJudul }}';

    var idTimSidang = '';
    var keterangan = '';
    if (tbodyId === 'penilaianReportBody') {
        idTimSidang = document.getElementById('selectedTimSidangTahap1') ? document.getElementById('selectedTimSidangTahap1').value : '';
        var sel = document.getElementById('penilaianSelect');
        if (sel) {
            var opt = sel.options[sel.selectedIndex];
            if (opt) keterangan = opt.getAttribute('data-keterangan') || '';
        }
    } else if (tbodyId === 'penilaianTahap2Body') {
        idTimSidang = document.getElementById('selectedTimSidangTahap2') ? document.getElementById('selectedTimSidangTahap2').value : '';
        var sel = document.getElementById('penilaiTahap2');
        if (sel) {
            var opt = sel.options[sel.selectedIndex];
            if (opt) keterangan = opt.getAttribute('data-keterangan') || '';
        }
    } else if (tbodyId === 'penilaianTableBody') {
        idTimSidang = document.getElementById('selectedTimSidang') ? document.getElementById('selectedTimSidang').value : '';
        var sel = document.getElementById('penilaiSelect');
        if (sel) {
            var opt = sel.options[sel.selectedIndex];
            if (opt) keterangan = opt.getAttribute('data-keterangan') || '';
        }
    }

    fetch('/sidang/lock-nilai/' + idJudul, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            nilai_terkunci: 'y',
            tahapan_sidang: tahapan,
            status_lulus: statusLulusVal,
            id_tim_sidang: idTimSidang,
            keterangan: keterangan
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showToast(data.message || 'Nilai berhasil dikunci', 'success');
        } else {
            showToast('Error: ' + (data.error || 'Gagal mengunci nilai'), 'error');
        }
    })
    .catch(function(error) {
        showToast('Error: ' + error, 'error');
    });
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
        if (!res.ok) {
            var msg = data.error || data.message || JSON.stringify(data.errors || data);
            showToast('Error: ' + msg, 'error');
            return;
        }
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

    // Check if this is "Ajukan" action (is_ajukan_fs or is_ajukan_kpps)
    var isAjukan = formData.get('is_ajukan_fs') === '1' || formData.get('is_ajukan_kpps') === '1';

    // Disable submit button
    var submitBtn = event.target;
    var originalText = submitBtn.innerHTML;
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
            
            // Jika "Ajukan", kembali ke list. Jika hanya "Simpan", tetap di form
            if (isAjukan) {
                // Switch back to jadwal list view — instan, refresh senyap di belakang
                setTimeout(function() {
                    document.getElementById('jadwalFormTahap2').style.display = 'none';
                    document.getElementById('jadwalListTahap2').style.display = 'block';
                    refreshJadwalListQuiet();
                }, 500);
            } else {
                // Tetap di form, hanya re-enable tombol
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        } else {
            showToast(data.message || 'Gagal menyimpan jadwal sidang', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    })
    .catch(function(error) {
        showToast('Error: ' + error.message, 'error');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

/**
 * Refresh isi list jadwal secara senyap di belakang layar — tanpa spinner.
 * Konten lama tetap tampil sampai data baru siap, lalu ditukar instan.
 */
function refreshJadwalListQuiet() {
    var tahapanEl = document.querySelector('[name="tahapan_sidang"]');
    var tahapan = (tahapanEl && tahapanEl.value) || '{{ $tahapan }}';
    var idJudulEl = document.querySelector('[name="id_judul"]');
    var idJudul = (idJudulEl && idJudulEl.value) || '{{ $idJudul }}';

    fetch('/sidang/tahap/' + encodeURIComponent(tahapan) + '?id_judul=' + encodeURIComponent(idJudul) + '&_=' + new Date().getTime(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.text(); })
    .then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var src = doc.getElementById('jadwalListTahap2');
        var dst = document.getElementById('jadwalListTahap2');
        if (src && dst && !dst.contains(src)) {
            src.style.display = 'block';
            dst.replaceWith(src);
        }
    })
    .catch(function () {});
}

function kembaliKeJadwalListTahap2() {
    // Tampilkan list langsung — transisi instan tanpa loading
    var formEl = document.getElementById('jadwalFormTahap2');
    var listEl = document.getElementById('jadwalListTahap2');
    if (formEl) formEl.style.display = 'none';
    if (listEl) listEl.style.display = 'block';

    // Data terbaru diambil senyap di belakang layar
    refreshJadwalListQuiet();
}

async function hapusJadwal(id, tahapan, idJudul) {
    var ok = await showConfirmDialog({
        title: 'Hapus Jadwal Sidang',
        message: 'Yakin ingin menghapus jadwal sidang ini beserta semua penilaiannya?',
        confirmText: 'Ya, Hapus',
        type: 'danger'
    });
    if (!ok) return;

    fetch('/sidang/jadwal/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: new URLSearchParams({ _method: 'DELETE' })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            showToast(data.message || 'Jadwal sidang berhasil dihapus', 'success');
            // Hapus baris jadwal secara lokal — tanpa reload konten modal
            var btnEl = document.querySelector('button[onclick*="hapusJadwal(' + id + ',"]');
            if (btnEl) {
                var tr = btnEl.closest('tr');
                if (tr) tr.remove();
            }
        } else {
            showToast(data.message || 'Gagal menghapus jadwal sidang', 'error');
        }
    })
    .catch(function(error) {
        showToast('Error: ' + error.message, 'error');
    });
}

function showToast(message, type) {
    var isSuccess = type === 'success';
    var toast = document.createElement('div');
    toast.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;max-width:350px;width:100%;animation:slideInRight 0.3s ease forwards;';
    toast.innerHTML =
        '<div style="background:#fff;border-radius:12px;box-shadow:0 10px 25px -5px rgba(0,0,0,0.08),0 8px 10px -6px rgba(0,0,0,0.05);border-left:4px solid ' + (isSuccess ? '#10b981' : '#ef4444') + ';padding:16px;margin-bottom:12px;">' +
            '<div style="display:flex;align-items:flex-start;">' +
                '<div style="font-size:1.2rem;margin-top:2px;margin-right:12px;color:' + (isSuccess ? '#10b981' : '#ef4444') + '">' +
                    '<i class="fas ' + (isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle') + ' fa-lg"></i>' +
                '</div>' +
                '<div style="flex-grow:1;">' +
                    '<h6 style="margin:0;font-weight:700;color:#1e293b;font-size:0.95rem;">' + (isSuccess ? 'Berhasil' : 'Gagal') + '</h6>' +
                    '<p style="margin:4px 0 0;font-size:0.85rem;line-height:1.4;color:#64748b;">' + message + '</p>' +
                '</div>' +
                '<button type="button" style="background:none;border:none;font-size:1.25rem;color:#94a3b8;cursor:pointer;margin-left:12px;padding:0;line-height:1;" onclick="this.parentElement.parentElement.parentElement.remove()">&times;</button>' +
            '</div>' +
        '</div>';
    document.body.appendChild(toast);
    setTimeout(function() { toast.remove(); }, 3000);
}

// Tab switching for Tahap I
$(document).ready(function() {
    $('#myTab a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // Cegah interaksi select2 dianggap klik-luar oleh Bootstrap modal
    // (mencegah modal tertutup sendiri saat memilih penilai/form)
    if (!window.__select2ModalGuard) {
        window.__select2ModalGuard = true;
        jQuery(document).on('mousedown mouseup click touchend',
            '.select2-dropdown, .select2-selection, .select2-search__field, li.select2-results__option',
            function (e) { e.stopPropagation(); }
        );
    }

    if (window.jQuery && jQuery.fn.select2) {
        jQuery('.select2-search').each(function() {
            var $el = jQuery(this);
            if ($el.data('select2')) return;
            var hasInlineWidth = $el.attr('style') && /\bwidth\s*:/.test($el.attr('style'));
            var opts = {
                theme: 'bootstrap',
                allowClear: true,
                width: hasInlineWidth ? 'resolve' : '100%'
            };
            var $modal = $el.closest('.modal');
            if ($modal.length) opts.dropdownParent = $modal;
            $el.select2(opts);
        });
    }
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

// Input listeners for dynamic lock button validation
document.addEventListener('input', function(e) {
    if (e.target.closest('#penilaianReportBody')) {
        toggleLockButton('penilaianReportBody', 'lockNilaiBtn');
    } else if (e.target.closest('#penilaianTahap2Body')) {
        toggleLockButton('penilaianTahap2Body', 'lockNilaiTahap2Btn');
    } else if (e.target.closest('#penilaianTableBody')) {
        toggleLockButton('penilaianTableBody', 'lockNilaiPembimbingBtn');
    }
});

// All ajuan data for jadwal edit
var _allAjuanData = @json($allAjuanJson);

function openJadwalForm(el) {
    var form = document.getElementById('jadwalFormTahap2Form');
    if (!form) return;
    var fields = ['tgl_sidang','waktu_sidang','waktu_selesai','ruang_sidang','tgl_surat_undangan','no_surat_undangan','tgl_surat_penelaah','no_surat_penelaah','tgl_hasil_penelahan','email_surat','no_sk_kelulusan'];
    var idInput = document.getElementById('id_ajuan_tahap2');

    // Find record by id
    var data = null;
    if (typeof el === 'object' && el !== null && el.dataset) {
        data = {
            'id': el.dataset.id || '',
            'tgl_sidang': el.dataset.tglSidang || null,
            'waktu_sidang': el.dataset.waktuSidang || null,
            'waktu_selesai': el.dataset.waktuSelesai || null,
            'ruang_sidang': el.dataset.ruangSidang || null,
            'tgl_surat_undangan': el.dataset.tglSuratUndangan || null,
            'no_surat_undangan': el.dataset.noSuratUndangan || null,
            'tgl_surat_penelaah': el.dataset.tglSuratPenelaah || null,
            'no_surat_penelaah': el.dataset.noSuratPenelaah || null,
            'tgl_hasil_penelahan': el.dataset.tglHasilPenelahan || null,
            'email_surat': el.dataset.emailSurat || null,
            'no_sk_kelulusan': el.dataset.noSkKelulusan || null
        };
    } else if (el) {
        if (typeof _allAjuanData !== 'undefined' && _allAjuanData) {
            for (var i = 0; i < _allAjuanData.length; i++) {
                if (_allAjuanData[i].id == el) { data = _allAjuanData[i]; break; }
            }
        }
    }

    if (data) {
        fields.forEach(function(name) {
            var fieldEl = form.querySelector('[name="' + name + '"]');
            if (fieldEl && data[name] !== undefined && data[name] !== null && data[name] !== '') fieldEl.value = data[name];
        });
        if (idInput && data.id) idInput.value = data.id;
    } else {
        // New entry (no record clicked): reset form to blank
        fields.forEach(function(name) {
            var fieldEl = form.querySelector('[name="' + name + '"]');
            if (fieldEl) fieldEl.value = '';
        });
        if (idInput) idInput.value = '';
    }

    document.getElementById('jadwalListTahap2').style.display = 'none';
    document.getElementById('jadwalFormTahap2').style.display = 'block';
}

function cetakSuratKesediaan() {
    var idJudul = document.querySelector('#jadwalFormTahap2Form [name="id_judul"]').value;
    var tahapan = document.querySelector('#jadwalFormTahap2Form [name="tahapan_sidang"]').value;
    if (!idJudul) { alert('Data jadwal belum tersedia.'); return; }
    var url = '{{ route("sidang.surat-kesediaan", ["idJudul" => ":idJudul", "tahapan" => ":tahapan"]) }}';
    url = url.replace(':idJudul', encodeURIComponent(idJudul)).replace(':tahapan', encodeURIComponent(tahapan));
    window.open(url, '_blank');
}

function cetakUndangan() {
    var idJudul = document.querySelector('#jadwalFormTahap2Form [name="id_judul"]').value;
    var tahapan = document.querySelector('#jadwalFormTahap2Form [name="tahapan_sidang"]').value;
    if (!idJudul) { alert('Data jadwal belum tersedia.'); return; }
    var url = '{{ route("sidang.cetak-undangan", ["idJudul" => ":idJudul", "tahapan" => ":tahapan"]) }}';
    url = url.replace(':idJudul', encodeURIComponent(idJudul)).replace(':tahapan', encodeURIComponent(tahapan));
    window.open(url, '_blank');
}

function cetakForm() {
    var idTimSidang = document.getElementById('selectedTimSidangTahap2').value;
    if (!idTimSidang) { alert('Pilih penilai terlebih dahulu.'); return; }
    var noForm = document.getElementById('selectedNoFormTahap2').value;
    if (!noForm) { alert('Pilih Form terlebih dahulu.'); return; }
    var idJudul = document.querySelector('#penilaianFormTahap2Form [name="id_judul"]').value;
    var tahapan = document.querySelector('#penilaianFormTahap2Form [name="tahapan_sidang"]').value;
    var url = '{{ route("sidang.cetak-form", ["idJudul" => ":idJudul", "tahapan" => ":tahapan"]) }}';
    url = url.replace(':idJudul', idJudul).replace(':tahapan', encodeURIComponent(tahapan));
    url += '?id_tim_sidang=' + encodeURIComponent(idTimSidang);
    url += '&no_form=' + encodeURIComponent(noForm);
    window.open(url, '_blank');
}

function cetakBeritaAcara() {
    var idJudul = document.querySelector('#penilaianFormTahap2Form [name="id_judul"]').value;
    var tahapan = document.querySelector('#penilaianFormTahap2Form [name="tahapan_sidang"]').value;
    if (!idJudul) { alert('ID Judul tidak ditemukan.'); return; }
    var url = '{{ route("sidang.cetak-berita-acara", ["idJudul" => ":idJudul", "tahapan" => ":tahapan"]) }}';
    url = url.replace(':idJudul', idJudul).replace(':tahapan', encodeURIComponent(tahapan));
    window.open(url, '_blank');
}
</script>

@php
function getTahapLabel($tahapan) {
    $labels = [
        'tahap I'  => 'Ujian Kualifikasi',
        'tahap II' => 'Ujian Proposal',
        'tahap IV' => 'Sidang Terbuka / Tertutup',
    ];
    return $labels[strtolower($tahapan)] ?? str_replace('tahap', 'Tahap', $tahapan);
}

function getStatusColor($status) {
    $s = strtolower($status ?? '');
    switch($s) {
        case 'belum diajukan':
            return 'secondary';
        case 'diproses di tu prodi':
        case 'dalam proses':
            return 'warning';
        case 'diproses di fakultas':
            return 'orange';
        case 'menunggu pelaksanaan sidang':
            return 'purple';
        case 'terjadwal':
            return 'primary';
        case 'lulus':
            return 'success';
        case 'tidak lulus':
            return 'danger';
        case 'diajukan':
        case 'diajukan ke fs':
            return 'info';
        default:
            return 'secondary';
    }
}
@endphp
