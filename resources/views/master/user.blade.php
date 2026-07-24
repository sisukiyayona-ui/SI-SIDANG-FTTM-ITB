@extends('layouts.master')

@section('title', 'Data User - SI SIDANG FTTM ITB')
@section('page_title', 'Data User')

@section('breadcrumb')
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item active">Data User</li>
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


            <div class="table-responsive">
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
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="1"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="2"></th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="3"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="4">
                                    <option value="">Semua</option>
                                    <option value="Tendik">Tendik</option>
                                    <option value="Dosen">Dosen</option>
                                    <option value="Mahasiswa">Mahasiswa</option>
                                </select>
                            </th>
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="5">
                                    <option value="">Semua</option>
                                    <option value="Admin">Admin</option>
                                    <option value="TU Prodi">TU Prodi</option>
                                    <option value="FS">FS</option>
                                    <option value="Mahasiswa">Mahasiswa</option>
                                    <option value="Pembimbing">Pembimbing</option>
                                    <option value="Penguji">Penguji</option>
                                    <option value="Monev">Monev</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm column-search" placeholder="Cari..." data-col="6"></th>
                            <th>
                                <select class="form-control form-control-sm column-search" data-col="7">
                                    <option value="">Semua</option>
                                    <option value="AKTIF">AKTIF</option>
                                    <option value="NON AKTIF">NON AKTIF</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
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
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $users->links() }}
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
                        <input type="text" name="username" id="f_username" class="form-control" placeholder="Username login">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="f_password" class="form-control" placeholder="Password (kosongkan jika tidak diubah)">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jenis User <span class="text-danger">*</span></label>
                        <select name="jenis_user" id="f_jenis_user" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="Admin">Admin</option>
                            <option value="TU Prodi">TU Prodi</option>
                            <option value="FS">FS</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Pembimbing">Pembimbing</option>
                            <option value="Penguji">Penguji</option>
                            <option value="Monev">Monev</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status Pegawai <span class="text-danger">*</span></label>
                        <select name="status_pegawai" id="f_status_pegawai" class="form-control" required>
                            <option value="">-- Pilih --</option>
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
                            <option value="">-- Pilih --</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Angkatan</label>
                        <input type="number" name="thn_angkatan" id="f_thn_angkatan" class="form-control" placeholder="Contoh: 2026" min="2000" max="2099">
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
                        <small class="text-muted">Kode Prodi dan Nama Prodi akan diisi otomatis dari session user login</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kode FS / Nama FS</label>
                        <select name="id_fs_prodi" id="f_id_fs_prodi" class="form-control">
                            <option value="">-- Pilih --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}" data-kode="{{ $p->kode_prodi }}" data-nama="{{ $p->nama_prodi }}">
                                    {{ $p->kode_prodi }} - {{ $p->nama_prodi }}
                                </option>
                            @endforeach
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
    function openCreate() {
        document.getElementById('modalUserTitle').innerHTML = '<i class="fas fa-plus mr-2"></i>Tambah User';
        document.getElementById('formUser').action = '{{ route("master.user.store") }}';
        document.getElementById('methodUser').value = 'POST';
        document.getElementById('formUser').reset();
        document.getElementById('saAktif').checked = true;
        document.getElementById('spApprove').checked = true;

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

            document.getElementById('f_nip_nim').value = item.nip_nim ?? '';
            document.getElementById('f_nama_lengkap').value = item.nama_lengkap ?? '';
            document.getElementById('f_email').value = item.email ?? '';
            document.getElementById('f_akun_ina').value = item.akun_ina ?? '';
            document.getElementById('f_username').value = item.username ?? '';
            document.getElementById('f_password').value = '';
            document.getElementById('f_jenis_user').value = item.jenis_user ?? '';

            var prodiSel = document.getElementById('f_id_prodi');
            prodiSel.value = '';
            for (var i = 0; i < prodiSel.options.length; i++) {
                if (prodiSel.options[i].dataset.kode === item.kode_prodi) {
                    prodiSel.options[i].selected = true;
                    break;
                }
            }

            var fsSel = document.getElementById('f_id_fs_prodi');
            fsSel.value = '';
            for (var i = 0; i < fsSel.options.length; i++) {
                if (fsSel.options[i].dataset.kode === item.kode_fs) {
                    fsSel.options[i].selected = true;
                    break;
                }
            }

            document.getElementById('f_status_pegawai').value = item.status_pegawai ?? '';
            document.getElementById('f_strata').value = item.strata ?? '';
            document.getElementById('f_thn_angkatan').value = item.thn_angkatan ?? '';

            document.getElementById(item.status_aktif === 'AKTIF' ? 'saAktif' : 'saNonAktif').checked = true;
            document.getElementById(item.status_approve === 'y' ? 'spApprove' : 'spTolak').checked = true;

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

    document.querySelectorAll('.column-search').forEach(input => {
        input.addEventListener('input', filterTable);
        input.addEventListener('change', filterTable);
    });

    function filterTable() {
        const filters = Array.from(document.querySelectorAll('.column-search')).map(input => ({
            colIndex: parseInt(input.dataset.col),
            value: input.value.toLowerCase()
        }));

        document.querySelectorAll('#userTable tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (!cells || cells.length === 0) return;
            let isMatch = true;
            filters.forEach(filter => {
                if (filter.value && cells[filter.colIndex]) {
                    const cellText = cells[filter.colIndex].textContent.toLowerCase();
                    if (!cellText.includes(filter.value)) {
                        isMatch = false;
                    }
                }
            });
            row.style.display = isMatch ? '' : 'none';
        });
    }

</script>
@endpush
