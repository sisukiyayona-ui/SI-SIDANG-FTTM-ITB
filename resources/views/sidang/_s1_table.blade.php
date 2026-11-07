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
                return 'success';
            case 'tidak lulus':
                return 'danger';
            case 'rejected':
            case 'ditolak':
                return 'danger';
            default:
                return 'info';
        }
    }
}

if (!function_exists('s1StatusOptions')) {
    function s1StatusOptions($name) {
        $options = [
            'belum diajukan' => 'Belum diajukan',
            'diproses di TU Prodi' => 'Diproses di TU Prodi',
            'diproses di fakultas' => 'Diproses di fakultas',
            'terjadwal' => 'Terjadwal',
            'rejected' => 'Ditolak',
            'lulus' => 'Lulus',
            'tidak lulus' => 'Tidak Lulus',
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
            <th class="align-middle">TA I</th>
            <th class="align-middle">TA II</th>
        </tr>
        <tr class="tracking-filter-row">
            <th></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nim" placeholder="Cari..." value="{{ request('nim') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama" placeholder="Cari..." value="{{ request('nama') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="judul" placeholder="Cari..." value="{{ request('judul') }}"></th>
            <th><input type="text" class="form-control form-control-sm column-search" name="nama_prodi" placeholder="Cari Prodi..." value="{{ request('nama_prodi') }}"></th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap1">
                    @php s1StatusOptions('tahap1'); @endphp
                </select>
            </th>
            <th>
                <select class="form-control form-control-sm column-search" name="tahap2">
                    @php s1StatusOptions('tahap2'); @endphp
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
                        <a href="{{ route('sidang.s1.ubah-judul', $item->id_judul) }}" class="text-decoration-none">{{ $item->Judul }}</a>
                    @else
                        <a href="#" class="text-decoration-none">{{ $item->Judul }}</a>
                    @endif
                </td>
                <td class="align-middle">{{ $item->nama_prodi ?? '—' }}</td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap1 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('TA I', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap1 ?? 'belum diajukan') }}
                    </span>
                </td>
                <td class="align-middle">
                    <span class="badge bg-{{ getStatusColor($item->tahap2 ?? 'belum diajukan') }}" role="button" onclick="showTahapForm('TA II', {{ $item->id_judul }})">
                        {{ ucfirst($item->tahap2 ?? 'belum diajukan') }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">Tidak ada data progress sidang S1</td>
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
