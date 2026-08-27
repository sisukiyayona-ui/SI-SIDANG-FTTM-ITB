<table class="table table-striped table-hover" id="prodiTable">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Kode Prodi</th>
            <th>Nama Prodi</th>
            <th>Strata</th>
            <th>Status</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="kode_prodi" placeholder="Cari..." value="{{ request('kode_prodi') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." value="{{ request('nama_prodi') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="strata" placeholder="Cari..." value="{{ request('strata') }}"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="status_aktif">
                    <option value="">Semua</option>
                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($prodi as $i => $item)
            <tr>
                <td>{{ $prodi->firstItem() + $i }}</td>
                <td><span class="badge bg-info">{{ $item['kode'] }}</span></td>
                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
                <td>{{ $item['strata'] ?? '-' }}</td>
                <td>
                    <span class="badge bg-{{ $item['status'] === 'AKTIF' ? 'success' : 'danger' }}">
                        {{ $item['status'] }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-end">
    {{ $prodi->links() }}
</div>
