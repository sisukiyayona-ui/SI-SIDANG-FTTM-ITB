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
<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">Tampilkan</label>
        <select class="form-control form-control-sm column-search" name="per_page" style="width: auto; min-width: 70px; color: #495057;">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ (int) $fakultas->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
            @endforeach
        </select>
        <span class="ml-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">data per halaman</span>
    </div>
    <div>
        {{ $fakultas->links() }}
    </div>
</div>
