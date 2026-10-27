<table class="table table-striped table-hover" id="mkProdiTable">
    <thead>
        <tr>
            <th style="width: 50px;">No</th>
            <th>Kode Prodi</th>
            <th>Nama Prodi</th>
            <th>Kode MK</th>
            <th>Tahapan Sidang</th>
            {{-- Kolom Aksi (edit/hapus) dinonaktifkan sementara
            <th style="width: 150px; min-width: 150px;">Aksi</th>
            --}}
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="kode_prodi" placeholder="Cari..." value="{{ request('kode_prodi') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari..." value="{{ request('nama_prodi') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="kode_mk" placeholder="Cari..." value="{{ request('kode_mk') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="tahapan_sidang" placeholder="Cari..." value="{{ request('tahapan_sidang') }}"></th>
            {{--<th></th>--}}
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $item)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td><span class="badge bg-info">{{ $item['kode_prodi'] }}</span></td>
                <td>{{ $item['nama_prodi'] }}</td>
                <td><a href="javascript:void(0)" onclick="openEdit({{ $item['id'] }})" class="text-decoration-none">{{ $item['kode_mk'] }}</a></td>
                <td>{{ $item['tahapan_display'] ?? $item['tahapan_sidang'] }}</td>
                {{-- Kolom Aksi (edit/hapus) dinonaktifkan sementara
                <td style="white-space: nowrap; min-width: 150px;">
                    <button type="button" class="btn btn-sm btn-warning mr-1" onclick="openEdit({{ $item['id'] }})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="openDelete({{ $item['id'] }})" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                --}}
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Belum ada data Mata Kuliah Prodi. Klik <strong>Tambah</strong> untuk menambahkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">Tampilkan</label>
        <select class="form-control form-control-sm column-search" name="per_page" style="width: auto; min-width: 70px; color: #495057;">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ (int) $items->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
            @endforeach
        </select>
        <span class="ml-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">data per halaman</span>
    </div>
    <div>
        {{ $items->links() }}
    </div>
</div>
