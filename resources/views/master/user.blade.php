@extends('layouts.master')

@section('title', 'Data User - SI SIDANG FTTM ITB')
@section('page_title', 'Data User')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Data User</li>
    </ol>
@endsection

@push('styles')
<style>
    /* Master Data Table Styles - Professional Dark/Light Mode */
    .master-data-container {
        --table-header-bg-light: #f8f9fa;
        --table-header-text-light: #2d3748;
        --table-header-border-light: #dee2e6;
        --table-row-hover-light: #f8f9fa;
        --table-border-light: #dee2e6;
        
        --table-header-bg-dark: #334155;
        --table-header-text-dark: #f1f5f9;
        --table-header-border-dark: #475569;
        --table-row-hover-dark: #2d3748;
        --table-border-dark: #475569;
    }

    .master-data-container .table thead th {
        background-color: var(--table-header-bg-light) !important;
        color: var(--table-header-text-light) !important;
        border-color: var(--table-header-border-light) !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        padding: 14px 12px !important;
        vertical-align: middle !important;
    }

    html.dark-mode .master-data-container .table thead th {
        background-color: var(--table-header-bg-dark) !important;
        color: var(--table-header-text-dark) !important;
        border-color: var(--table-header-border-dark) !important;
    }

    .master-data-container .table tbody tr:hover {
        background-color: var(--table-row-hover-light) !important;
    }

    html.dark-mode .master-data-container .table tbody tr:hover {
        background-color: var(--table-row-hover-dark) !important;
    }

    .master-data-container .table {
        border-color: var(--table-border-light) !important;
    }

    html.dark-mode .master-data-container .table {
        border-color: var(--table-border-dark) !important;
    }

    .master-data-container .table td,
    .master-data-container .table th {
        border-color: var(--table-border-light) !important;
    }

    html.dark-mode .master-data-container .table td,
    html.dark-mode .master-data-container .table th {
        border-color: var(--table-border-dark) !important;
    }

    /* Card Header Styling */
    .master-data-container .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        border: none;
        padding: 16px 20px;
    }

    .master-data-container .card-header h5 {
        color: white !important;
        font-weight: 600;
        margin: 0;
    }

    /* Dark mode: card header adapt */
    html.dark-mode .master-data-container .card-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
        color: #f1f5f9 !important;
        border-bottom: 1px solid #334155 !important;
    }
    html.dark-mode .master-data-container .card-header h5 {
        color: #f1f5f9 !important;
    }

    html.dark-mode .master-data-container .card {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    html.dark-mode .master-data-container .card-body {
        background-color: #1e293b !important;
    }

    /* Dark mode: form container */
    html.dark-mode #formContainer .card-body {
        background-color: #1e293b !important;
    }
    html.dark-mode #formContainer .form-label {
        color: #e2e8f0 !important;
    }
    html.dark-mode #formContainer .form-control {
        background-color: #334155 !important;
        border-color: #475569 !important;
        color: #f1f5f9 !important;
    }
    html.dark-mode #formContainer select.form-control option {
        background-color: #334155 !important;
        color: #f1f5f9 !important;
    }
    html.dark-mode #formContainer .form-check-label {
        color: #e2e8f0 !important;
    }
    html.dark-mode #formContainer small.text-muted {
        color: #94a3b8 !important;
    }
    /* Dark mode: role list box */
    html.dark-mode #roleList {
        background-color: #1e293b !important;
        border-color: #475569 !important;
    }
    html.dark-mode #roleList .form-check-label {
        color: #e2e8f0 !important;
    }
    /* Dark mode: footer/main-footer */
    html.dark-mode .main-footer {
        background-color: #0f172a !important;
        color: #94a3b8 !important;
        border-top: 1px solid #1e293b !important;
    }
    html.dark-mode .main-footer a {
        color: #60a5fa !important;
    }
    /* Dark mode: signature canvas */
    html.dark-mode #signatureCanvas {
        background-color: #1e293b !important;
        border-color: #475569 !important;
    }
    html.dark-mode #signaturePreview {
        border-color: #475569 !important;
    }
</style>
@endpush

@section('content')
<div class="master-data-container">
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Daftar User</h5>
            <button class="btn btn-sm btn-primary" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah
            </button>
        </div>
        <div class="card-body">


            <div class="table-responsive">
                <form method="GET" action="{{ route('master.user.index') }}" id="filterForm" autocomplete="off">
                <table class="table table-striped table-hover" id="userTable">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>NIP/NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Status Pegawai</th>
                            <th>Program Studi</th>
                            <th>Status Aktif</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nip_nim" placeholder="Cari..." data-col="1" value="{{ request('nip_nim') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_lengkap" placeholder="Cari..." data-col="2" value="{{ request('nama_lengkap') }}"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="email" placeholder="Cari..." data-col="3" value="{{ request('email') }}"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="status_pegawai" data-col="4">
                                    <option value="">Semua</option>
                                    <option value="Tendik" {{ request('status_pegawai') == 'Tendik' ? 'selected' : '' }}>Tendik</option>
                                    <option value="Dosen" {{ request('status_pegawai') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                                    <option value="Mahasiswa" {{ request('status_pegawai') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." data-col="5" value="{{ request('nama_prodi') }}"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="status_aktif" data-col="6">
                                    <option value="">Semua</option>
                                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $item)
                            <tr>
                                <td>{{ $users->firstItem() + $i }}</td>
                                <td>{{ $item['nip_nim'] }}</td>
                                <td><a href="javascript:void(0)" onclick="openEdit('{{ $item['id'] }}')" class="text-decoration-none">{{ $item['nama_lengkap'] }}</a></td>
                                <td>{{ $item['email'] }}</td>
                                <td>{{ $item['status_pegawai'] ?? '-' }}</td>
                                <td>{{ $item['nama_prodi'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item['status_aktif'] === 'AKTIF' ? 'success' : 'danger' }}">
                                        {{ $item['status_aktif'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </form>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

@php $user = session('auth_user'); @endphp
    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0" id="modalUserTitle"><i class="fas fa-plus mr-2"></i>Tambah User</h5>
        </div>
        <div class="card-body">
            <form id="formUser" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodUser" value="POST">
                {{-- Fakultas selection --}}
                <input type="hidden" name="nama_fs" id="f_nama_fs" value="FTTM">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIP / NIM <span class="text-danger">*</span></label>
                        <input type="text" name="nip_nim" id="f_nip_nim" class="form-control" placeholder="NIP atau NIM" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" id="f_nama_lengkap" class="form-control" placeholder="Nama lengkap" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="f_email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Akun INA</label>
                        <input type="text" name="akun_ina" id="f_akun_ina" class="form-control" placeholder="Akun INA">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="f_username" class="form-control" placeholder="Username login" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="f_password" class="form-control" placeholder="Password (kosongkan jika tidak diubah)">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role User <span class="text-danger">*</span></label>
                        <div id="roleList" class="border rounded p-2" style="max-height: 180px; overflow-y: auto;" class="role-list-box">
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="Admin">
                                <span class="form-check-label">Admin</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="TU Prodi">
                                <span class="form-check-label">TU Prodi</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="FS">
                                <span class="form-check-label">Fakultas</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="Mahasiswa">
                                <span class="form-check-label">Mahasiswa</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="Pembimbing">
                                <span class="form-check-label">Pembimbing</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="Penguji">
                                <span class="form-check-label">Penguji</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="Monev">
                                <span class="form-check-label">Monev</span>
                            </label>
                            <label class="form-check d-block mb-1" style="cursor: pointer;">
                                <input type="checkbox" class="form-check-input role-check" name="jenis_user[]" value="KPPS">
                                <span class="form-check-label">KPPS</span>
                            </label>
                        </div>
                        <small class="text-muted">Klik role untuk memilih lebih dari satu. Role pertama yang dipilih menjadi role default.</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Pegawai</label>
                        <select name="status_pegawai" id="f_status_pegawai" class="form-control">
                            <option value="">-- Pilih Status Pegawai --</option>
                            <option value="Tendik">Tendik</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Strata</label>
                        <select name="strata" id="f_strata" class="form-control">
                            <option value="">-- Pilih Strata --</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                        <small class="text-muted">Isi jika Role User Mahasiswa</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="number" name="thn_angkatan" id="f_thn_angkatan" class="form-control" placeholder="Contoh: 2026" min="2000" max="2099">
                        <small class="text-muted">Isi jika Role User Mahasiswa</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fakultas *</label>
                        @if(session('auth_user.role') === 'TU Prodi')
                            {{-- TU Prodi: fakultas dari login, disable --}}
                            <input type="hidden" name="kode_fs" id="f_kode_fs" value="{{ session('auth_user.kode_fs') }}">
                            <input type="hidden" name="nama_fs" id="f_nama_fs" value="{{ session('auth_user.nama_fs') }}">
                            <input type="text" class="form-control" value="{{ session('auth_user.nama_fs') }}" disabled style="background-color:#e9ecef;">
                        @else
                            <select name="kode_fs" id="f_kode_fs" class="form-control" onchange="setNamaFs(this)">
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach($fakultas as $fs)
                                    <option value="{{ $fs->KODE_FS }}" data-nama="{{ $fs->NAMA_FS }}">{{ $fs->NAMA_FS }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi</label>
                        @if(session('auth_user.role') === 'TU Prodi')
                            {{-- TU Prodi: prodi dari login, disable --}}
                            @php
                                $loginProdi = \App\Models\TProdi::where('kode_prodi', session('auth_user.kode_prodi'))->first();
                            @endphp
                            <input type="hidden" name="id_prodi" value="{{ $loginProdi?->id }}">
                            <input type="text" class="form-control" value="{{ session('auth_user.kode_prodi') }} - {{ session('auth_user.nama_prodi') }}" disabled style="background-color:#e9ecef;">
                        @else
                            <select name="id_prodi" id="f_id_prodi" class="form-control">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" data-kode="{{ $p->kode_prodi }}" data-nama="{{ $p->nama_prodi }}">
                                        {{ $p->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
                <div class="row" id="kkRow" style="display:none;">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">KK</label>
                        <input type="text" name="kk" id="f_kk" class="form-control" placeholder="Contoh: 322.1" maxlength="250">
                        <small class="text-muted">Diisi jika Status Pegawai Dosen</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Asal Instansi *</label>
                        <select name="asal_instansi" id="f_asal_instansi" class="form-control" onchange="handleAsalInstansiChange()">
                            <option value="">-- Pilih Asal Instansi --</option>
                            <option value="ITB">ITB</option>
                            <option value="NON ITB">NON ITB</option>
                        </select>
                        <small class="text-muted">Isi jika role untuk Pembimbing atau Penguji</small>
                    </div>
                    <div class="col-md-6 mb-3" id="instansiContainer">
                        <label class="form-label">Instansi <span class="text-danger">*</span></label>
                        <input type="text" name="instansi" id="f_instansi" class="form-control" placeholder="Nama instansi (untuk pengguna luar ITB)">
                        <small class="text-muted">Isi jika role untuk Pembimbing atau Penguji</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Kaprodi</label>
                        <select name="status_kaprodi" id="f_status_kaprodi" class="form-control">
                            <option value="">-- Pilih Status Kaprodi --</option>
                            <option value="y">Ya (Kaprodi)</option>
                            <option value="t">Tidak</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Dekan</label>
                        <select name="status_dekan" id="f_status_dekan" class="form-control">
                            <option value="">-- Pilih Status Dekan --</option>
                            <option value="y">Ya</option>
                            <option value="t">Tidak</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status WDA</label>
                        <select name="status_wda" id="f_status_wda" class="form-control">
                            <option value="">-- Pilih Status WDA --</option>
                            <option value="y">Ya</option>
                            <option value="t">Tidak</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Aktif <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="status_aktif" value="AKTIF" class="form-check-input" id="saAktif" checked>
                                <label class="form-check-label" for="saAktif">AKTIF</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status_aktif" value="NON AKTIF" class="form-check-input" id="saNonAktif">
                                <label class="form-check-label" for="saNonAktif">NON AKTIF</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Approve <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="status_approve" value="t" class="form-check-input" id="spApprove" checked>
                                <label class="form-check-label" for="spApprove">Approved (t)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status_approve" value="f" class="form-check-input" id="spTolak">
                                <label class="form-check-label" for="spTolak">Pending (f)</label>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Signature Canvas --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Tanda Tangan</label>
                        <input type="hidden" name="signature_data" id="f_signature_data">
                        <div class="d-flex align-items-start" style="gap: 15px;">
                            <div>
                                <canvas id="signatureCanvas" width="300" height="100" class="signature-canvas" style="border: 1px dashed #ccc; border-radius: 4px; background: #fafafa;"></canvas>
                                <div class="d-flex justify-content-between mt-2">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="clearSignature()">Hapus</button>
                                    <input type="file" name="signature_file" id="f_signature_file" accept="image/*" style="display:none;">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('f_signature_file').click()">Upload File</button>
                                </div>
                            </div>
                            <div id="signaturePreview" style="display: none; max-width: 150px; max-height: 100px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px;">
                                <img id="signatureImg" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        </div>
                        <small class="text-muted">Gambar tanda tangan di canvas atau upload file gambar</small>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end" style="gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeForm()">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Delete --}}
    <div class="modal fade" id="modalDelete" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="formDelete" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h6>Yakin ingin menghapus user ini?</h6>
                        <p class="text-muted small mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function handleAsalInstansiChange() {
        var asalInstansi = document.getElementById('f_asal_instansi').value;
        var instansiField = document.getElementById('f_instansi');
        
        if (asalInstansi === 'ITB') {
            instansiField.value = 'ITB';
            instansiField.disabled = true;
            instansiField.style.backgroundColor = '#e9ecef';
        } else {
            if (instansiField.value === 'ITB') {
                instansiField.value = '';
            }
            instansiField.disabled = false;
            instansiField.style.backgroundColor = '';
        }
    }

    function setNamaFs(select) {
        var selected = select.options[select.selectedIndex];
        var namaFs = selected.dataset.nama || selected.textContent.trim();
        document.getElementById('f_nama_fs').value = namaFs;
    }

    function toggleKkRow() {
        var statusPegawai = document.getElementById('f_status_pegawai').value;
        document.getElementById('kkRow').style.display = statusPegawai === 'Dosen' ? 'block' : 'none';
    }

    // Item 14: Toggle field aktif berdasarkan status_pegawai
    function toggleUserFormFields() {
        var statusPegawai = document.getElementById('f_status_pegawai').value;
        var isMahasiswa = (statusPegawai === 'Mahasiswa');
        var isDosen = (statusPegawai === 'Dosen');

        // Strata & Tahun Angkatan: aktif jika Mahasiswa
        var fStrata = document.getElementById('f_strata');
        var fThnAngkatan = document.getElementById('f_thn_angkatan');
        if (fStrata) {
            fStrata.disabled = !isMahasiswa;
            fStrata.style.backgroundColor = isMahasiswa ? '' : '#e9ecef';
        }
        if (fThnAngkatan) {
            fThnAngkatan.disabled = !isMahasiswa;
            fThnAngkatan.style.backgroundColor = isMahasiswa ? '' : '#e9ecef';
        }

        // Status Kaprodi, Dekan, WDA: aktif jika Dosen
        var fKaprodi = document.getElementById('f_status_kaprodi');
        var fDekan = document.getElementById('f_status_dekan');
        var fWda = document.getElementById('f_status_wda');
        if (fKaprodi) {
            fKaprodi.disabled = !isDosen;
            fKaprodi.style.backgroundColor = isDosen ? '' : '#e9ecef';
        }
        if (fDekan) {
            fDekan.disabled = !isDosen;
            fDekan.style.backgroundColor = isDosen ? '' : '#e9ecef';
        }
        if (fWda) {
            fWda.disabled = !isDosen;
            fWda.style.backgroundColor = isDosen ? '' : '#e9ecef';
        }
    }

    var canvas, ctx;
    var signatureData = null;

    function getCanvasBg() {
        return document.documentElement.classList.contains('dark-mode') ? '#1e293b' : '#fff';
    }

    function initCanvas() {
        canvas = document.getElementById('signatureCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');
        ctx.fillStyle = getCanvasBg();
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        var isDrawing = false;
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseout', stopDraw);

        canvas.addEventListener('touchstart', function(e) { e.preventDefault(); startDraw(e); });
        canvas.addEventListener('touchmove', function(e) { e.preventDefault(); draw(e); }, { passive: false });
        canvas.addEventListener('touchend', function(e) { e.preventDefault(); stopDraw(e); });
    }

    function getPos(canvas, evt) {
        var rect = canvas.getBoundingClientRect();
        var clientX, clientY;
        if (evt.touches && evt.touches.length > 0) {
            clientX = evt.touches[0].clientX;
            clientY = evt.touches[0].clientY;
        } else {
            clientX = evt.clientX;
            clientY = evt.clientY;
        }
        return { x: clientX - rect.left, y: clientY - rect.top };
    }

    function startDraw(evt) {
        isDrawing = true;
        var pos = getPos(canvas, evt);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    }

    function draw(evt) {
        if (!isDrawing) return;
        var pos = getPos(canvas, evt);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function stopDraw() {
        isDrawing = false;
        signatureData = canvas.toDataURL('image/png');
        document.getElementById('f_signature_data').value = signatureData;
        document.getElementById('signatureImg').src = signatureData;
        document.getElementById('signaturePreview').style.display = 'block';
    }

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = getCanvasBg();
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        document.getElementById('f_signature_data').value = '';
        document.getElementById('signaturePreview').style.display = 'none';
    }

    function updateSignatureFromFile(imgSrc) {
        document.getElementById('f_signature_data').value = imgSrc;
        document.getElementById('signatureImg').src = imgSrc;
        document.getElementById('signaturePreview').style.display = 'block';
    }

    document.getElementById('f_signature_file').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(evt) {
            var img = new Image();
            img.onload = function() {
                var c = document.createElement('canvas');
                c.width = 300; c.height = 100;
                var cctx = c.getContext('2d');
                cctx.fillStyle = getCanvasBg();
                cctx.fillRect(0, 0, c.width, c.height);
                cctx.drawImage(img, 0, 0, c.width, c.height);
                updateSignatureFromFile(c.toDataURL('image/png'));
            };
            img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
        e.target.value = '';
    });

    function openCreate() {
        document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah User';
        document.getElementById('formUser').action = '{{ route("master.user.store") }}';
        document.getElementById('methodUser').value = 'POST';
        document.getElementById('formUser').reset();
        document.getElementById('saAktif').checked = true;
        document.getElementById('spApprove').checked = true;
        document.getElementById('f_password').required = false;
        document.getElementById('f_password').placeholder = 'Password (kosongkan jika tidak diubah)';
        toggleKkRow();
        toggleUserFormFields();

        @if(session('auth_user.role') !== 'TU Prodi')
        document.getElementById('f_kode_fs').value = '';
        document.getElementById('f_nama_fs').value = '';
        @endif
        
        document.getElementById('f_asal_instansi').value = '';
        document.getElementById('f_status_dekan').value = '';
        document.getElementById('f_status_wda').value = '';
        document.getElementById('f_instansi').value = '';
        document.getElementById('f_instansi').disabled = false;
        document.getElementById('f_instansi').style.backgroundColor = '';
        
        clearSignature();
        document.getElementById('signaturePreview').style.display = 'none';

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        fetch('{{ url("master/user") }}/' + id + '/edit', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value }
        })
        .then(function(r) { return r.json(); })
        .then(function(item) {
            if (!item) return;

            document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit User';
            document.getElementById('formUser').action = '{{ url("master/user") }}/' + id;
            document.getElementById('methodUser').value = 'PUT';

            document.getElementById('f_nip_nim').value      = item.nip_nim ?? '';
            document.getElementById('f_nama_lengkap').value = item.nama_lengkap ?? '';
            document.getElementById('f_email').value        = item.email ?? '';
            document.getElementById('f_akun_ina').value     = item.akun_ina ?? '';
            document.getElementById('f_username').value     = item.username ?? '';
            document.getElementById('f_password').value     = '';
            document.getElementById('f_password').required  = false;
            document.getElementById('f_password').placeholder = 'Password (kosongkan jika tidak diubah)';

            var roles = item.roles && item.roles.length ? item.roles : (item.jenis_user ? [item.jenis_user] : []);
            document.querySelectorAll('.role-check').forEach(function(cb) {
                cb.checked = roles.indexOf(cb.value) !== -1;
            });

            document.getElementById('f_status_pegawai').value = item.status_pegawai ?? '';
            document.getElementById('f_kk').value = item.kk ?? '';
            toggleKkRow();
            toggleUserFormFields();
            document.getElementById('f_strata').value         = item.strata ?? '';
            document.getElementById('f_thn_angkatan').value   = item.thn_angkatan ?? '';
            document.getElementById('f_status_dekan').value = item.status_dekan ?? '';
            document.getElementById('f_status_wda').value = item.status_wda ?? '';
            document.getElementById('f_status_kaprodi').value = item.status_kaprodi ?? '';

            @if(session('auth_user.role') !== 'TU Prodi')
            document.getElementById('f_kode_fs').value = item.kode_fs ?? '';
            document.getElementById('f_nama_fs').value = item.nama_fs ?? '';
            @endif
            
            document.getElementById('f_asal_instansi').value = item.asal_instansi ?? '';
            document.getElementById('f_instansi').value = item.instansi ?? '';
            handleAsalInstansiChange(); // Apply logic based on asal_instansi

            // Signature preview — data stored as raw base64, prepend data URI
            var sigData = item.signature ? item.signature.trim() : '';
            if (sigData) {
                var sigUri = sigData.startsWith('data:image') ? sigData : 'data:image/png;base64,' + sigData;
                document.getElementById('signatureImg').src = sigUri;
                document.getElementById('signaturePreview').style.display = 'block';
                document.getElementById('f_signature_data').value = sigUri;

                // Restore canvas
                if (canvas && ctx) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    var img = new Image();
                    img.onload = function() {
                        ctx.fillStyle = getCanvasBg();
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    };
                    img.src = sigUri;
                }
            } else {
                document.getElementById('signaturePreview').style.display = 'none';
                document.getElementById('f_signature_data').value = '';
            }

            @if(session('auth_user.role') !== 'TU Prodi')
            var prodiSel = document.getElementById('f_id_prodi');
            if (prodiSel) {
                prodiSel.value = '';
                for (var i = 0; i < prodiSel.options.length; i++) {
                    if (prodiSel.options[i].dataset.kode === item.kode_prodi) {
                        prodiSel.options[i].selected = true;
                        break;
                    }
                }
            }
            @endif

            document.getElementById(item.status_aktif === 'AKTIF' ? 'saAktif' : 'saNonAktif').checked = true;
            document.getElementById((item.status_approve === 't' || item.status_approve === 'y') ? 'spApprove' : 'spTolak').checked = true;

            document.getElementById('listContainer').style.display = 'none';
            document.getElementById('formContainer').style.display = 'block';
        });
    }

    function closeForm() {
        document.getElementById('formContainer').style.display = 'none';
        document.getElementById('listContainer').style.display = 'block';
    }

    function openDelete(id) {
        document.getElementById('formDelete').action = '{{ url("master/user") }}/' + id;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    document.getElementById('f_status_pegawai').addEventListener('change', function() {
        toggleKkRow();
        toggleUserFormFields();
    });

    document.getElementById('formUser').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: new FormData(this)
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (r.ok && data.success) {
                closeForm();
                showToast('success', 'Data user berhasil disimpan.');
                setTimeout(() => location.reload(), 1200);
                return;
            }
            if (r.status === 422 && data.errors) {
                const firstError = Object.values(data.errors)[0];
                showToast('error', Array.isArray(firstError) ? firstError[0] : firstError);
                return;
            }
            showToast('error', data.message || 'Gagal menyimpan user. Periksa data form.');
        }).catch(() => {
            showToast('error', 'Terjadi kesalahan, coba lagi.');
        });
    });

    document.getElementById('formDelete').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(this)
        }).then(r => r.json()).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
                showToast('success', 'Data user berhasil dihapus.');
                setTimeout(() => location.reload(), 1200);
            }
        });
    });

    var filterTimeout;
    document.querySelectorAll('.column-search').forEach(function(input) {
        input.addEventListener('input', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function() { document.getElementById('filterForm').submit(); }, 400);
        });
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // ─── Signature Canvas Init ─────────────────────────────────────────────
    var isDrawing = false;
    if (document.getElementById('signatureCanvas')) {
        initCanvas();
    }

</script>
@endpush
