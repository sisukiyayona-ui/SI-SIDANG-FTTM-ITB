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
<div class="mt-3 d-flex justify-content-center" id="userPagination">
    {{ $users->links() }}
</div>
