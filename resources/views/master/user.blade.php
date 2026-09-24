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
    /* Dark mode: prodi list box */
    html.dark-mode #prodiList {
        background-color: #1e293b !important;
        border-color: #475569 !important;
    }
    html.dark-mode #prodiList .form-check-label {
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
    .itb-lookup-wrap { position: relative; }
    .itb-lookup-list {
        position: absolute;
        z-index: 1080;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: 8px;
        box-shadow: 0 10px 28px rgba(15,23,42,.16), 0 2px 6px rgba(15,23,42,.08);
        max-height: 280px;
        overflow-y: auto;
        padding: 4px;
        margin: 0;
        list-style: none;
    }
    .itb-lookup-list::before {
        content: '';
        display: block;
        height: 4px;
    }
    html.dark-mode .itb-lookup-list {
        background: #1e293b;
        border-color: #475569;
        color: #f1f5f9;
        box-shadow: 0 12px 32px rgba(0,0,0,.45), 0 2px 8px rgba(0,0,0,.3);
    }
    .itb-lookup-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        cursor: pointer;
        border-radius: 6px;
        font-size: 0.9rem;
        line-height: 1.35;
        border: 1px solid transparent;
        transition: background .12s ease, border-color .12s ease;
    }
    .itb-lookup-item + .itb-lookup-item { margin-top: 2px; }
    .itb-lookup-item:hover,
    .itb-lookup-item.active {
        background: #eef2ff;
        border-color: #c7d2fe;
    }
    html.dark-mode .itb-lookup-item:hover,
    html.dark-mode .itb-lookup-item.active {
        background: #334155;
        border-color: #64748b;
    }
    .itb-lookup-item .avatar {
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: .02em;
    }
    .itb-lookup-item .body { min-width: 0; flex: 1; }
    .itb-lookup-item .title {
        font-weight: 600;
        color: #0f172a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    html.dark-mode .itb-lookup-item .title { color: #f1f5f9; }
    .itb-lookup-item .meta {
        color: #64748b;
        font-size: 0.78rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    html.dark-mode .itb-lookup-item .meta { color: #94a3b8; }
    .itb-lookup-item .badge-status {
        flex: 0 0 auto;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    html.dark-mode .itb-lookup-item .badge-status {
        background: #064e3b;
        color: #6ee7b7;
        border-color: #065f46;
    }
    .itb-lookup-empty {
        padding: 12px;
        text-align: center;
        color: #64748b;
        font-size: 0.85rem;
    }
    html.dark-mode .itb-lookup-empty { color: #94a3b8; }
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
            <div class="table-responsive" id="userTableContainer">
                @include('master._user_table')
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
                        <div class="itb-lookup-wrap">
                            <input type="text" name="nip_nim" id="f_nip_nim" class="form-control" placeholder="NIP/NIM atau akun INA" maxlength="32" autocomplete="off" required>
                            <div id="itbLookupList" class="itb-lookup-list" style="display: none;" role="listbox"></div>
                        </div>
                        <small class="text-muted">Ketik NIP/NIM atau akun INA — pilih dari hasil pencarian akun ITB untuk auto-fill.</small>
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
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        @if(session('auth_user.role') === 'TU Prodi')
                            <div id="prodiListTu" class="border rounded p-2" style="max-height: 160px; overflow-y: auto; background: #fff;">
                                @forelse($prodis as $p)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input prodi-check" name="id_prodi[]" id="tu_prodi_{{ $p->id }}" value="{{ $p->id }}">
                                        <label class="form-check-label" for="tu_prodi_{{ $p->id }}">{{ $p->kode_prodi }} - {{ $p->nama_prodi }}</label>
                                    </div>
                                @empty
                                    <small class="text-muted">Belum ada program studi untuk akun Anda.</small>
                                @endforelse
                            </div>
                            <small class="text-muted">Centang satu atau lebih program studi. Format: kode - nama prodi.</small>
                        @else
                            <div id="prodiList" class="border rounded p-2" style="max-height: 160px; overflow-y: auto; background: #fff;">
                                <small class="text-muted">Pilih fakultas terlebih dahulu agar daftar prodi muncul.</small>
                            </div>
                            <small class="text-muted">Centang satu atau lebih program studi. Format: kode - nama prodi.</small>
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
                    {{-- Status Approve disembunyikan (dikomentari) --}}
                    {{--
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
                    --}}
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
        fetchProdiByFs(select.value, null, true, pendingProdiName);
        pendingProdiName = null;
    }

    var pendingProdiName = null;

    function fetchProdiByFs(kodeFs, selectedProdiIds, autoSelectFirst, preferredProdiName) {
        var prodiList = document.getElementById('prodiList');
        if (!prodiList) return;
        prodiList.innerHTML = '<small class="text-muted">-- Pilih Program Studi --</small>';
        if (!kodeFs) {
            prodiList.innerHTML = '<small class="text-muted">Pilih fakultas terlebih dahulu agar daftar prodi muncul.</small>';
            return;
        }
        fetch('{{ route("master.user.prodi-by-fs") }}?kode_fs=' + encodeURIComponent(kodeFs), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(prodis) {
            prodis = prodis || [];
            if (prodis.length === 0) {
                prodiList.innerHTML = '<small class="text-muted">Tidak ada prodi aktif</small>';
                return;
            }
            prodiList.innerHTML = '';
            prodis.forEach(function(p) {
                var label = document.createElement('label');
                label.className = 'form-check d-block mb-1';
                label.style.cursor = 'pointer';
                var cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'form-check-input prodi-check';
                cb.name = 'id_prodi[]';
                cb.value = p.id;
                var nameLc = String(p.NAMA_PRODI || '').toLowerCase();
                var matchPreferred = preferredProdiName &&
                    nameLc === String(preferredProdiName).trim().toLowerCase();
                if (selectedProdiIds && selectedProdiIds.indexOf(String(p.id)) !== -1) {
                    cb.checked = true;
                } else if (matchPreferred) {
                    cb.checked = true;
                }
                var span = document.createElement('span');
                span.className = 'form-check-label';
                span.textContent = p.KODE_PRODI + ' - ' + p.NAMA_PRODI;
                label.appendChild(cb);
                label.appendChild(span);
                prodiList.appendChild(label);
            });
        });
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
        return '#fff';
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
        // Status Approve disembunyikan (dikomentari)
        // document.getElementById('spApprove').checked = true;
        document.getElementById('f_password').required = false;
        document.getElementById('f_password').placeholder = 'Password (kosongkan jika tidak diubah)';
        toggleKkRow();
        toggleUserFormFields();

        @if(session('auth_user.role') !== 'TU Prodi')
        document.getElementById('f_kode_fs').value = '';
        document.getElementById('f_nama_fs').value = '';
        var prodiList = document.getElementById('prodiList');
        if (prodiList) {
            prodiList.innerHTML = '<small class="text-muted">Pilih fakultas terlebih dahulu agar daftar prodi muncul.</small>';
        }
        @endif
        
        document.getElementById('f_asal_instansi').value = '';
        document.getElementById('f_status_dekan').value = '';
        document.getElementById('f_status_wda').value = '';
        document.getElementById('f_instansi').value = '';
        document.getElementById('f_instansi').disabled = false;
        document.getElementById('f_instansi').style.backgroundColor = '';

        var prodiListEl = document.getElementById('prodiList');
        if (prodiListEl) {
            prodiListEl.innerHTML = '<small class="text-muted">Pilih fakultas terlebih dahulu agar daftar prodi muncul.</small>';
        }

        clearSignature();
        document.getElementById('signaturePreview').style.display = 'none';
        hideItbLookup();

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

            @if(session('auth_user.role') === 'TU Prodi')
            document.querySelectorAll('#prodiListTu .prodi-check').forEach(function(cb) {
                cb.checked = (item.prodi_ids || []).indexOf(String(cb.value)) !== -1;
            });
            @else
            document.getElementById('f_kode_fs').value = item.kode_fs ?? '';
            document.getElementById('f_nama_fs').value = item.nama_fs ?? '';
            fetchProdiByFs(item.kode_fs, item.prodi_ids || [], false);
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

            document.getElementById(item.status_aktif === 'AKTIF' ? 'saAktif' : 'saNonAktif').checked = true;
            // Status Approve disembunyikan (dikomentari)
            // document.getElementById((item.status_approve === 't' || item.status_approve === 'y') ? 'spApprove' : 'spTolak').checked = true;

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


    function nipNimInputHandler(e) {
        var el = e.target;
        var pos = getCaretPosition(el);
        var raw = el.value.replace(/[^0-9A-Za-z._@-]/g, '');
        if (el.value !== raw) {
            el.value = raw;
            setCaretPosition(el, pos);
        }
    }

    function getCaretPosition(input) {
        if (document.selection && document.selection.createRange) {
            var sel = document.selection.createRange();
            var clone = sel.duplicate();
            clone.moveToElementText(input);
            clone.setEndPoint('EndToEnd', sel);
            return clone.text.length - sel.text.length;
        }
        return input.selectionStart ?? 0;
    }

    function setCaretPosition(input, pos) {
        if (input.setSelectionRange) {
            input.setSelectionRange(pos, pos);
        } else if (document.selection) {
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    }

    var nipInput = document.getElementById('f_nip_nim');
    var itbLookupList = document.getElementById('itbLookupList');
    var itbLookupTimer = null;
    var itbLookupSeq = 0;
    var itbLookupItems = [];
    var itbLookupActive = -1;

    function hideItbLookup() {
        if (itbLookupList) {
            itbLookupList.style.display = 'none';
            itbLookupList.innerHTML = '';
        }
        itbLookupItems = [];
        itbLookupActive = -1;
    }

    function renderItbLookup(items) {
        if (!itbLookupList) return;
        itbLookupItems = items || [];
        itbLookupActive = -1;
        if (!itbLookupItems.length) {
            hideItbLookup();
            return;
        }
        itbLookupList.innerHTML = itbLookupItems.map(function(item, idx) {
            var idLine = item.nip || item.nim || item.nip_nim || '-';
            var nameLine = item.nama_lengkap || item.cn || '-';
            var extra = [];
            if (item.email) extra.push(item.email);
            if (item.akun_ina || item.username) extra.push('@' + (item.akun_ina || item.username));
            var status = item.status_pegawai || item.status || '';
            var initials = String(nameLine).replace(/[^A-Za-z ]/g, '').trim().split(/\s+/).slice(0, 2)
                .map(function(w) { return w.charAt(0).toUpperCase(); }).join('') || '?';
            return '<div class="itb-lookup-item" data-idx="' + idx + '" role="option">' +
                '<div class="avatar">' + escapeHtml(initials) + '</div>' +
                '<div class="body">' +
                    '<div class="title">' + escapeHtml(nameLine) + ' <span class="meta">(' + escapeHtml(idLine) + ')</span></div>' +
                    (extra.length ? '<div class="meta">' + escapeHtml(extra.join(' · ')) + '</div>' : '') +
                '</div>' +
                (status ? '<span class="badge-status">' + escapeHtml(status) + '</span>' : '') +
                '</div>';
        }).join('');
        itbLookupList.style.display = 'block';

        itbLookupList.querySelectorAll('.itb-lookup-item').forEach(function(el) {
            el.addEventListener('mousedown', function(e) {
                e.preventDefault();
                applyItbLookupItem(itbLookupItems[parseInt(el.getAttribute('data-idx'), 10)]);
            });
        });
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function applyMahasiswaDetail(mhs) {
        if (!mhs) return;
        var fill = function(id, val) {
            var el = document.getElementById(id);
            if (el) el.value = val == null ? '' : val;
        };
        if (mhs.kd_strata) fill('f_strata', mhs.kd_strata);
        if (mhs.tahun_daftar) fill('f_thn_angkatan', mhs.tahun_daftar);

        var prodiName = mhs.prodi || '';
        var fsSel = document.getElementById('f_kode_fs');
        if (fsSel && fsSel.tagName === 'SELECT' && mhs.kd_fak) {
            var found = false;
            for (var i = 0; i < fsSel.options.length; i++) {
                if (fsSel.options[i].value === mhs.kd_fak) {
                    found = true;
                    break;
                }
            }
            if (found) {
                pendingProdiName = prodiName;
                fsSel.value = mhs.kd_fak;
                setNamaFs(fsSel);
                return;
            }
        }
        selectProdiCheckboxByName(prodiName);
    }

    function selectProdiCheckboxByName(name) {
        if (!name) return;
        var lower = String(name).trim().toLowerCase();
        document.querySelectorAll('#prodiList .prodi-check, #prodiListTu .prodi-check').forEach(function(cb) {
            var label = cb.parentElement ? cb.parentElement.querySelector('.form-check-label') : null;
            if (!label) return;
            var text = label.textContent || '';
            var dash = text.indexOf(' - ');
            var prodiLabel = dash >= 0 ? text.substring(dash + 3) : text;
            if (prodiLabel.trim().toLowerCase() === lower || text.trim().toLowerCase() === lower) {
                cb.checked = true;
            }
        });
    }

    function applyItbLookupItem(item) {
        if (!item) return;
        var fill = function(id, val) {
            var el = document.getElementById(id);
            if (el) el.value = val == null ? '' : val;
        };
        fill('f_nip_nim', item.nip_nim || item.nip || item.nim || '');
        fill('f_nama_lengkap', item.nama_lengkap || '');
        fill('f_email', item.email || '');
        fill('f_akun_ina', item.akun_ina || '');
        fill('f_username', item.username || item.akun_ina || '');
        if (item.status_pegawai) {
            fill('f_status_pegawai', item.status_pegawai);
            toggleKkRow();
            toggleUserFormFields();
        }
        fill('f_asal_instansi', 'ITB');
        handleAsalInstansiChange();
        hideItbLookup();
        var isMhs = item.status_pegawai === 'Mahasiswa';
        var nim = item.nim || (item.nip_nim && String(item.nip_nim).length <= 10 ? item.nip_nim : '');
        if (isMhs && item.mhs_detail) {
            applyMahasiswaDetail(item.mhs_detail);
            if (typeof showToast === 'function') {
                showToast('success', 'Data mahasiswa diisi otomatis (strata, tahun, prodi).');
            }
            return;
        }
        if (isMhs && nim) {
            if (typeof showToast === 'function') {
                showToast('success', 'Data akun ITB diisi. Mengambil detail mahasiswa…');
            }
            fetch('{{ route("master.user.mhs-detail") }}?nim=' + encodeURIComponent(nim), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json().catch(function() { return null; }); })
            .then(function(res) {
                if (res && res.success && res.data) {
                    applyMahasiswaDetail(res.data);
                    if (typeof showToast === 'function') {
                        showToast('success', 'Detail mahasiswa terisi (strata, tahun, prodi).');
                    }
                }
            })
            .catch(function() {});
            return;
        }
        if (typeof showToast === 'function') {
            showToast('success', 'Data akun ITB diisi otomatis. Asal Instansi diisi ITB.');
        }
    }

    function showItbLoading() {
        if (!itbLookupList) return;
        itbLookupList.innerHTML = '<div class="itb-lookup-empty"><span class="spinner-border spinner-border-sm mr-1" style="width:14px;height:14px;vertical-align:-2px;"></span> Mencari akun ITB…</div>';
        itbLookupList.style.display = 'block';
    }

    function scheduleItbLookup(raw) {
        clearTimeout(itbLookupTimer);
        var q = String(raw || '').trim();
        if (q.length < 3) {
            hideItbLookup();
            return;
        }
        var seq = ++itbLookupSeq;
        showItbLoading();
        itbLookupTimer = setTimeout(function() {
            if (seq !== itbLookupSeq) return;
            fetch('{{ route("master.user.itb-lookup") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json().catch(function() { return null; }); })
            .then(function(res) {
                if (seq !== itbLookupSeq) return;
                if (!res || !res.success || !res.data) {
                    hideItbLookup();
                    return;
                }
                renderItbLookup([res.data]);
            })
            .catch(function() {
                if (seq === itbLookupSeq) hideItbLookup();
            });
        }, 200);
    }

    if (nipInput) {
        nipInput.addEventListener('input', nipNimInputHandler);
        nipInput.addEventListener('keydown', function(e) {
            if (itbLookupList && itbLookupList.style.display !== 'none') {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    var items = itbLookupList.querySelectorAll('.itb-lookup-item');
                    if (!items.length) return;
                    if (e.key === 'ArrowDown') {
                        itbLookupActive = (itbLookupActive + 1) % items.length;
                    } else {
                        itbLookupActive = (itbLookupActive - 1 + items.length) % items.length;
                    }
                    items.forEach(function(el, i) {
                        el.classList.toggle('active', i === itbLookupActive);
                    });
                    return;
                }
                if (e.key === 'Enter' && itbLookupActive >= 0) {
                    e.preventDefault();
                    applyItbLookupItem(itbLookupItems[itbLookupActive]);
                    return;
                }
                if (e.key === 'Escape') {
                    hideItbLookup();
                    return;
                }
            }
        });
        nipInput.addEventListener('input', function() {
            scheduleItbLookup(nipInput.value);
        });
        nipInput.addEventListener('blur', function() {
            setTimeout(hideItbLookup, 150);
        });
        nipInput.addEventListener('paste', function() {
            setTimeout(nipNimInputHandler, 0);
            setTimeout(function() { scheduleItbLookup(nipInput.value); }, 10);
        });
    }
    if (document.addEventListener) {
        document.addEventListener('click', function(e) {
            if (!itbLookupList) return;
            if (e.target !== nipInput && !itbLookupList.contains(e.target)) {
                hideItbLookup();
            }
        });
    }

    document.getElementById('f_status_pegawai').addEventListener('change', function() {
        toggleKkRow();
        toggleUserFormFields();
    });

    document.getElementById('formUser').addEventListener('submit', function(e) {
        e.preventDefault();
        const kodeFs = document.getElementById('f_kode_fs');
        const prodiChecked = document.querySelectorAll('#formUser .prodi-check:checked');
        if (kodeFs && !String(kodeFs.value || '').trim()) {
            showToast('error', 'Fakultas wajib dipilih.');
            if (typeof kodeFs.focus === 'function') { try { kodeFs.focus(); } catch (err) {} }
            return;
        }
        if (prodiChecked.length < 1) {
            showToast('error', 'Program Studi wajib dipilih minimal satu.');
            return;
        }
        const fd = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: fd
        }).then(async r => {
            const data = await r.json().catch(() => ({}));
            if (r.ok && data.success) {
                closeForm();
                showToast('success', 'Data user berhasil disimpan.');
                setTimeout(() => location.reload(), 1200);
                return;
            }
            console.error('User store response', { status: r.status, ok: r.ok, data });
            if (data?.errors) {
                const friendly = {
                    kode_fs: 'Fakultas',
                    id_prodi: 'Program Studi',
                };
                const lines = Object.entries(data.errors).map(([k, v]) => {
                    const key = String(k).replace(/\[\]$/, '');
                    const label = friendly[key] || key;
                    return label + ': ' + (Array.isArray(v) ? v.join(', ') : v);
                }).join('\n');
                showToast('error', lines || 'Data tidak valid.');
                return;
            }
            if (data?.message) {
                showToast('error', data.message);
                return;
            }
            showToast('error', 'Gagal menyimpan user. Periksa data form dan response server.');
        }).catch(() => {
            showToast('error', 'Terjadi kesalahan, coba lagi.');
        });
    });

    window.__debugUserStoreResponse = null;

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

    function ajaxFilter(routeName, containerId) {
        var params = {};
        document.querySelectorAll('.column-search').forEach(function(input) {
            if (input.value) params[input.name] = input.value;
        });
        var qs = Object.keys(params).map(function(k) { return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]); }).join('&');
        var url = routeName + (qs ? '?' + qs : '');
        var container = document.getElementById(containerId);

        container.style.opacity = '0.5';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                container.innerHTML = data.html;
                container.style.opacity = '1';
                bindFilters(routeName, containerId);
            })
            .catch(function(err) {
                console.error('AJAX filter error:', err);
                container.style.opacity = '1';
            });
    }

    function bindFilters(routeName, containerId) {
        document.querySelectorAll('.column-search').forEach(function(input) {
            input.removeEventListener('input', input._ajaxHandler);
            input._ajaxHandler = function() {
                clearTimeout(window._filterTimeout);
                window._filterTimeout = setTimeout(function() { ajaxFilter(routeName, containerId); }, 400);
            };
            input.addEventListener('input', input._ajaxHandler);
            input.removeEventListener('change', input._changeHandler);
            input._changeHandler = function() {
                ajaxFilter(routeName, containerId);
            };
            input.addEventListener('change', input._changeHandler);
        });
        document.querySelectorAll('.pagination a').forEach(function(link) {
            link.removeEventListener('click', link._ajaxHandler);
            link._ajaxHandler = function(e) {
                e.preventDefault();
                var pagContainer = document.getElementById(containerId);
                pagContainer.style.opacity = '0.5';
                fetch(this.href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        pagContainer.innerHTML = data.html;
                        pagContainer.style.opacity = '1';
                        bindFilters(routeName, containerId);
                    })
                    .catch(function(err) {
                        console.error('AJAX pagination error:', err);
                        pagContainer.style.opacity = '1';
                    });
            };
            link.addEventListener('click', link._ajaxHandler);
        });
    }
    bindFilters('{{ route("master.user.index") }}', 'userTableContainer');

    // ─── Signature Canvas Init ─────────────────────────────────────────────
    var isDrawing = false;
    if (document.getElementById('signatureCanvas')) {
        initCanvas();
    }

</script>
@endpush
