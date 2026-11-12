@php
if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        $s = strtolower($status ?? '');
        switch($s) {
            case 'belum diajukan':
                return 'secondary';
            case 'diproses di tu prodi':
                return 'warning';
            case 'diproses di fakultas':
                return 'orange';
            case 'terjadwal':
                return 'primary';
            case 'dalam proses':
                return 'warning';
            case 'lulus':
            case 'lulus sidang magister':
                return 'success';
            case 'tidak lulus':
            case 'tidak lulus sidang magister':
                return 'danger';
            case 'mengulangi sidang magister':
                return 'warning';
            case 'rejected':
            case 'ditolak':
                return 'danger';
            default:
                return 'info';
        }
    }
}

if (!function_exists('s2StatusLabel')) {
    function s2StatusLabel($status) {
        $s = strtolower(trim((string) $status));
        switch ($s) {
            case 'lulus sidang magister':
                return 'LULUS Sidang Magister';
            case 'tidak lulus sidang magister':
                return 'TIDAK LULUS Sidang Magister';
            case 'mengulangi sidang magister':
                return 'Mengulangi Sidang Magister';
            case '':
            case 'belum diajukan':
                return 'Belum Diajukan';
            case 'diproses di tu prodi':
                return 'Diproses di TU Prodi';
            case 'terjadwal':
                return 'Terjadwal';
            default:
                return ucfirst($s);
        }
    }
}

if (!function_exists('s2StatusOptions')) {
    function s2StatusOptions($name) {
        $options = [
            'belum diajukan' => 'Belum Diajukan',
            'diproses di TU Prodi' => 'Diproses di TU Prodi',
            'terjadwal' => 'Terjadwal',
            'Lulus Sidang Magister' => 'LULUS Sidang Magister',
            'Tidak Lulus Sidang Magister' => 'TIDAK LULUS Sidang Magister',
        ];
        echo '<option value="">Semua</option>';
        foreach ($options as $value => $label) {
            $selected = request($name) == $value ? 'selected' : '';
            echo '<option value="' . e($value) . '" ' . $selected . '>' . e($label) . '</option>';
        }
    }
}
@endphp
<table id="trackingTable" class="table table-bordered table-hover text-center" style="table-layout: fixed;">
    <colgroup>
        <col style="width: 60px;">
        <col style="width: 140px;">
        <col style="width: 160px;">
        <col style="width: 320px;">
        <col style="width: 160px;">
        <col style="width: 150px;">
        <col style="width: 150px;">
    </colgroup>
    <thead>
        <tr>
            <th class="align-middle" style="width: 50px;">No</th>
            <th class="align-middle">NIM</th>
            <th class="align-middle">Nama</th>
            <th class="align-middle">Judul</th>
            <th class="align-middle">Prodi</th>
            <th class="align-middle">Tesis I</th>
            <th class="align-middle">Tesis II</th>
        </tr>
        <tr class="tracking-filter-row">
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nim" placeholder="Cari..." value="{{ request('nim') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." value="{{ request('nama') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="judul" placeholder="Cari..." value="{{ request('judul') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari Prodi..." value="{{ request('nama_prodi') }}"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis1">
                    @php s2StatusOptions('tesis1'); @endphp
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tesis2">
                    @php s2StatusOptions('tesis2'); @endphp
                </select>
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($tracking as $item)
            <tr>
                <td class="align-middle">{{ $tracking->firstItem() + $loop->index }}</td>
                <td class="align-middle">{{ $item->Nim }}</td>
                <td class="align-middle">{{ $item->nama_mhs }}</td>
                <td class="text-left text-muted">
                    @if(session('auth_user.role') === 'TU Prodi')
                        <a href="{{ route('sidang.s2.ubah-judul', $item->id_judul) }}" class="text-decoration-none">{{ $item->Judul }}</a>
                    @else
                        <a href="#" class="text-decoration-none">{{ $item->Judul }}</a>
                    @endif
                </td>
                <td class="align-middle">{{ $item->nama_prodi ?? '—' }}</td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tesis1 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('Tesis I', {{ $item->id_judul }})">
                        {{ s2StatusLabel($item->tesis1 ?? 'belum diajukan') }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tesis2 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('Tesis II', {{ $item->id_judul }})">
                        {{ s2StatusLabel($item->tesis2 ?? 'belum diajukan') }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Tidak ada data progress sidang S2</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex align-items-center mb-2 mb-md-0">
        <label class="mb-0 mr-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">Tampilkan</label>
        <select class="form-control form-control-sm column-search" name="per_page" style="width: auto; min-width: 70px;">
            @foreach([10, 25, 50, 100] as $n)
                <option value="{{ $n }}" {{ (int) $tracking->perPage() === $n ? 'selected' : '' }}>{{ $n }}</option>
            @endforeach
        </select>
        <span class="ml-2 text-muted" style="font-size: 0.875rem; white-space: nowrap;">data per halaman</span>
    </div>
    <div>
        {{ $tracking->links() }}
    </div>
</div>
