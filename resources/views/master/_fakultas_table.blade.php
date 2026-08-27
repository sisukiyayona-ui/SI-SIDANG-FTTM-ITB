<table class="table table-striped table-hover" id="fakultasTable">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Kode Fakultas</th>
            <th>Nama Fakultas</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="kode_fs" placeholder="Cari..." value="{{ request('kode_fs') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_fs" placeholder="Cari..." value="{{ request('nama_fs') }}"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($fakultas as $i => $item)
            <tr>
                <td>{{ $fakultas->firstItem() + $i }}</td>
                <td><span class="badge bg-info">{{ $item['kode'] }}</span></td>
                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['nama'] }}</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-end">
    {{ $fakultas->links() }}
</div>
