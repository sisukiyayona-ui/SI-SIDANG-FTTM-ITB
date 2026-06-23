@extends('layouts.master')

@section('title', 'Data User - SI SIDANG FTTM ITB')
@section('page_title', 'Data User')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">User</li>
    </ol>
@endsection

@section('content')
    <div id="listContainer" class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Daftar User</h5>
            <button class="btn btn-sm btn-accent" onclick="openCreate()">
                <i class="fas fa-plus mr-1"></i> Tambah User
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari user..." id="searchInput" onkeyup="filterTable()">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="filterJenis" onchange="filterTable()">
                        <option value="">-- Semua Jenis User --</option>
                        <option>TU Prodi</option>
                        <option>FS</option>
                        <option>Mahasiswa</option>
                        <option>Pembimbing</option>
                        <option>Penguji</option>
                        <option>Monev</option>
                        <option>Admin</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="userTable">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th>NIP/NIM</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Jenis User</th>
                            <th>Status Pegawai</th>
                            <th>Program Studi</th>
                            <th>Status Aktif</th>
                            <th>Approve</th>
                            <th style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item['nip_nim'] }}</td>
                                <td>{{ $item['nama_lengkap'] }}</td>
                                <td>{{ $item['email'] }}</td>
                                <td><span class="badge bg-info">{{ $item['jenis_user'] }}</span></td>
                                <td>{{ $item['status_pegawai'] ?? '-' }}</td>
                                <td>{{ $item['nama_prodi'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item['status_aktif'] === 'AKTIF' ? 'success' : 'danger' }}">
                                        {{ $item['status_aktif'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item['status_approve'] === 'y' ? 'success' : 'warning' }}">
                                        {{ $item['status_approve'] === 'y' ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openEdit({{ $item['id'] }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="openDelete({{ $item['id'] }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Form Container (In-Page CRUD Form) --}}
    <div id="formContainer" class="card" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0" id="modalUserTitle"><i class="fas fa-plus mr-2"></i>Tambah User</h5>
        </div>
        <div class="card-body">
            <form id="formUser" method="POST">
                @csrf
                <input type="hidden" name="_method" id="methodUser" value="POST">
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
                        <label class="form-label">Username</label>
                        <input type="text" name="Username" id="f_username" class="form-control" placeholder="Username login">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="Password" id="f_password" class="form-control" placeholder="Password (kosongkan jika tidak diubah)">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Pegawai</label>
                        <select name="status_pegawai" id="f_status_pegawai" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="Tendik">Tendik</option>
                            <option value="Dosen">Dosen</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis User <span class="text-danger">*</span></label>
                        <select name="jenis_user" id="f_jenis_user" class="form-control" required onchange="toggleStrata()">
                            <option value="">-- Pilih --</option>
                            <option value="TU Prodi">TU Prodi</option>
                            <option value="FS">FS</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Pembimbing">Pembimbing</option>
                            <option value="Penguji">Penguji</option>
                            <option value="Monev">Monev</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Program Studi</label>
                        <select name="id_prodi" id="f_id_prodi" class="form-control">
                            <option value="">-- Tidak ada / Non Prodi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" data-kode="{{ $p->kode_prodi }}" data-nama="{{ $p->nama_prodi }}">
                                    {{ $p->nama_prodi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode FS / Nama FS</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="13321002" disabled>
                            <input type="text" class="form-control" value="FTTM" disabled>
                        </div>
                    </div>
                </div>
                <div class="row" id="rowStrata">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Strata</label>
                        <select name="strata" id="f_strata" class="form-control">
                            <option value="">-- Pilih --</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="number" name="thn_angkatan" id="f_thn_angkatan" class="form-control" placeholder="cth: 2022" min="2000" max="2100">
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
                                <input type="radio" name="status_approve" value="y" class="form-check-input" id="spApprove" checked>
                                <label class="form-check-label" for="spApprove">Approved (y)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="status_approve" value="t" class="form-check-input" id="spTolak">
                                <label class="form-check-label" for="spTolak">Pending (t)</label>
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
    const userData = @json($users);

    function toggleStrata() {
        const jenis = document.getElementById('f_jenis_user').value;
        document.getElementById('rowStrata').style.display = jenis === 'Mahasiswa' ? '' : 'none';
    }

    function openCreate() {
        document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah User';
        document.getElementById('formUser').action = '{{ route("master.user.store") }}';
        document.getElementById('methodUser').value = 'POST';
        document.getElementById('formUser').reset();
        document.getElementById('saAktif').checked = true;
        document.getElementById('spApprove').checked = true;
        toggleStrata();

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
    }

    function openEdit(id) {
        const item = userData.find(u => u.id === id);
        if (!item) return;

        document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-edit mr-2"></i>Edit User';
        document.getElementById('formUser').action = '{{ url("master/user") }}/' + id;
        document.getElementById('methodUser').value = 'PUT';

        document.getElementById('f_nip_nim').value = item.nip_nim ?? '';
        document.getElementById('f_nama_lengkap').value = item.nama_lengkap ?? '';
        document.getElementById('f_email').value = item.email ?? '';
        document.getElementById('f_akun_ina').value = item.akun_ina ?? '';
        document.getElementById('f_username').value = item.Username ?? '';
        document.getElementById('f_password').value = '';
        document.getElementById('f_status_pegawai').value = item.status_pegawai ?? '';
        document.getElementById('f_jenis_user').value = item.jenis_user ?? '';
        document.getElementById('f_strata').value = item.strata ?? '';
        document.getElementById('f_thn_angkatan').value = item.thn_angkatan ?? '';

        // Set prodi by kode_prodi
        const prodiSel = document.getElementById('f_id_prodi');
        prodiSel.value = '';
        for (let opt of prodiSel.options) {
            if (opt.dataset.kode === item.kode_prodi) {
                opt.selected = true;
                break;
            }
        }

        document.getElementById(item.status_aktif === 'AKTIF' ? 'saAktif' : 'saNonAktif').checked = true;
        document.getElementById(item.status_approve === 'y' ? 'spApprove' : 'spTolak').checked = true;

        toggleStrata();

        document.getElementById('listContainer').style.display = 'none';
        document.getElementById('formContainer').style.display = 'block';
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
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: new FormData(this)
        }).then(r => r.json()).then(data => {
            if (data.success) {
                closeForm();
                showToast('success', 'Data user berhasil disimpan.');
                setTimeout(() => location.reload(), 1200);
            }
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

    function filterTable() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const j = document.getElementById('filterJenis').value;
        document.querySelectorAll('#userTable tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchQ = text.includes(q);
            const matchJ = !j || row.textContent.includes(j);
            row.style.display = (matchQ && matchJ) ? '' : 'none';
        });
    }

    // Initialize strata visibility
    toggleStrata();
</script>
@endpush
