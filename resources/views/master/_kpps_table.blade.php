<table class="table table-striped table-hover" id="kppsTable">
    <thead>
        <tr>
            <th style="width:50px;">No</th>
            <th>NIP</th>
            <th>Nama Lengkap</th>
            <th>Status Tim</th>
            <th>Fakultas</th>
            <th>Program Studi</th>
            <th style="width:100px;">Status</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nip" placeholder="Cari..." data-col="1" value="{{ request('nip') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." data-col="2" value="{{ request('nama') }}"></th>
            <th></th>
            <th>
                <select class="form-control form-control-sm column-search" name="kode_fs" data-col="3">
                    <option value="">Semua</option>
                    @foreach($fakultas as $fs)
                        <option value="{{ $fs->KODE_FS }}" {{ request('kode_fs') == $fs->KODE_FS ? 'selected' : '' }}>{{ $fs->NAMA_FS }}</option>
                    @endforeach
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="kode_prodi" data-col="4">
                    <option value="">Semua</option>
                    @foreach($prodis as $p)
                        <option value="{{ $p->KODE_PRODI }}" {{ request('kode_prodi') == $p->KODE_PRODI ? 'selected' : '' }}>{{ $p->NAMA_PRODI }}</option>
                    @endforeach
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="status_aktif" data-col="5">
                    <option value="">Semua</option>
                    <option value="AKTIF" {{ request('status_aktif') == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                    <option value="NON AKTIF" {{ request('status_aktif') == 'NON AKTIF' ? 'selected' : '' }}>NON AKTIF</option>
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($kpps as $i => $item)
            <tr>
                <td>{{ $kpps->firstItem() + $i }}</td>
                <td>{{ $item->NIP ?? '-' }}</td>
                <td><a href="javascript:void(0)" onclick="openEdit({{ $item->id }})" class="text-decoration-none">{{ $item->NAMA }}</a></td>
                <td>
                    @if($item->STATUS_TIM)
                        <span class="badge bg-info">{{ $item->STATUS_TIM }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->NAMA_FS ?? '-' }}</td>
                <td>{{ $item->NAMA_PRODI ?? '-' }}</td>
                <td>
                    <span class="badge bg-{{ $item->STATUS_AKTIF === 'AKTIF' ? 'success' : 'danger' }}">
                        {{ $item->STATUS_AKTIF }}
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted">Tidak ada data KPPS</td></tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-center">
    {{ $kpps->links() }}
</div>
