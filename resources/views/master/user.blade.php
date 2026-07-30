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

    html.dark-mode .master-data-container .card {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    html.dark-mode .master-data-container .card-body {
        background-color: #1e293b !important;
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
                            <th>Jenis User</th>
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
                            <th>
                                <select class="form-control form-control-sm column-search" name="jenis_user" data-col="5">
                                    <option value="">Semua</option>
                                    <option value="Admin" {{ request('jenis_user') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="TU Prodi" {{ request('jenis_user') == 'TU Prodi' ? 'selected' : '' }}>TU Prodi</option>
                                    <option value="FS" {{ request('jenis_user') == 'FS' ? 'selected' : '' }}>FS</option>
                                    <option value="Mahasiswa" {{ request('jenis_user') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                    <option value="Pembimbing" {{ request('jenis_user') == 'Pembimbing' ? 'selected' : '' }}>Pembimbing</option>
                                    <option value="Penguji" {{ request('jenis_user') == 'Penguji' ? 'selected' : '' }}>Penguji</option>
                                    <option value="Monev" {{ request('jenis_user') == 'Monev' ? 'selected' : '' }}>Monev</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." data-col="6" value="{{ request('nama_prodi') }}"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" name="status_aktif" data-col="7">
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
                                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama_lengkap'] }}</a></td>
                                <td>{{ $item['email'] }}</td>
                                <td>{{ $item['status_pegawai'] ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ $item['jenis_user'] }}</span></td>
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
                {{-- Hidden fields untuk FS (selalu 164 / FTTM) --}}
                <input type="hidden" name="kode_fs" value="164">
                <input type="hidden" name="nama_fs" value="FTTM">
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
                        <label class="form-label">Jenis User <span class="text-danger">*</span></label>
                        <select name="jenis_user" id="f_jenis_user" class="form-control" required onchange="onJenisUserChange()">
                            <option value="">-- Pilih --</option>
                            <option value="Admin">Admin</option>
                            <option value="TU Prodi">TU Prodi</option>
                            <option value="FS">FS</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Pembimbing">Pembimbing</option>
                            <option value="Penguji">Penguji</option>
                            <option value="Monev">Monev</option>
                            <option value="Dosen">Dosen</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Pegawai</label>
                        <select name="status_pegawai" id="f_status_pegawai" class="form-control" disabled onchange="onStatusPegawaiChange()">
                            <option value="">-- Pilih --</option>
                            <option value="Tendik">Tendik</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                        <small class="text-muted">Aktif jika Jenis User bukan Mahasiswa</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Strata</label>
                        <select name="strata" id="f_strata" class="form-control" disabled>
                            <option value="">-- Pilih --</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                        <small class="text-muted">Aktif jika Jenis User = Mahasiswa</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="number" name="thn_angkatan" id="f_thn_angkatan" class="form-control" placeholder="Contoh: 2026" min="2000" max="2099" disabled>
                        <small class="text-muted">Aktif jika Jenis User = Mahasiswa</small>
                    </div>
                </div>
                <div class="row">
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
                                <option value="">-- Tidak ada / Non Prodi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}" data-kode="{{ $p->kode_prodi }}" data-nama="{{ $p->nama_prodi }}">
                                        {{ $p->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Kaprodi</label>
                        <select name="status_kaprodi" id="f_status_kaprodi" class="form-control" disabled>
                            <option value="">-- Pilih --</option>
                            <option value="y">Ya (Kaprodi)</option>
                            <option value="t">Tidak</option>
                        </select>
                        <small class="text-muted">Aktif jika Status Pegawai = Dosen</small>
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
    // ─── Logika disable/enable berdasar Jenis User ───────────────────────────
    function onJenisUserChange() {
        var jenis = document.getElementById('f_jenis_user').value;
        var isMhs   = (jenis === 'Mahasiswa');
        var isDosen = (jenis === 'Dosen');
        var isBukanMhs = (jenis !== '' && jenis !== 'Mahasiswa');

        var strataField = document.getElementById('f_strata');
        var thnField = document.getElementById('f_thn_angkatan');
        var statusPegawaiField = document.getElementById('f_status_pegawai');

        // Strata & thn angkatan: aktif hanya jika Mahasiswa
        strataField.disabled = !isMhs;
        thnField.disabled = !isMhs;

        // Status pegawai: aktif jika BUKAN Mahasiswa
        statusPegawaiField.disabled = !isBukanMhs;
        if (!isBukanMhs) {
            statusPegawaiField.value = '';
        }

        // Reset nilai kolom yg tidak aktif
        if (!isMhs) {
            strataField.value = '';
            thnField.value = '';
        }

        // Trigger status pegawai change untuk update status kaprodi
        onStatusPegawaiChange();
    }

    // ─── Logika disable/enable Status Kaprodi berdasar Status Pegawai ─────
    function onStatusPegawaiChange() {
        var statusPegawai = document.getElementById('f_status_pegawai').value;
        var isDosen = (statusPegawai === 'Dosen');
        var statusKaprodiField = document.getElementById('f_status_kaprodi');

        statusKaprodiField.disabled = !isDosen;
        if (!isDosen) {
            statusKaprodiField.value = '';
        }
    }

    function openCreate() {
        document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah User';
        document.getElementById('formUser').action = '{{ route("master.user.store") }}';
        document.getElementById('methodUser').value = 'POST';
        document.getElementById('formUser').reset();
        document.getElementById('saAktif').checked = true;
        document.getElementById('spApprove').checked = true;
        document.getElementById('f_password').required = false;
        document.getElementById('f_password').placeholder = 'Password (kosongkan jika tidak diubah)';

        // Reset semua disable sesuai state awal (belum ada jenis_user)
        document.getElementById('f_strata').disabled         = true;
        document.getElementById('f_thn_angkatan').disabled   = true;
        document.getElementById('f_status_pegawai').disabled = true;
        document.getElementById('f_status_kaprodi').disabled = true;

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

            document.getElementById('f_jenis_user').value = item.jenis_user ?? '';
            // Trigger disable/enable logic setelah set jenis_user
            onJenisUserChange();

            document.getElementById('f_status_pegawai').value = item.status_pegawai ?? '';
            onStatusPegawaiChange();
            document.getElementById('f_strata').value         = item.strata ?? '';
            document.getElementById('f_thn_angkatan').value   = item.thn_angkatan ?? '';
            document.getElementById('f_status_kaprodi').value = item.status_kaprodi ?? '';

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

</script>
@endpush
