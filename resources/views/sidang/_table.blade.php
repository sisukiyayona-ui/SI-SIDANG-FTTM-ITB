<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-table mr-2"></i>Data {{ $page_title ?? 'Sidang' }}</h5>
        <div>
            <a href="#" class="btn btn-sm btn-primary" onclick="window.print()">
                <i class="fas fa-print mr-1"></i> Cetak
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option value="">Semua Status</option>
                    <option>Terjadwal</option>
                    <option>Selesai</option>
                    <option>Dibatalkan</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        @foreach($columns as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            @foreach($columns as $col)
                                @php $key = strtolower(str_replace(' ', '_', $col)); @endphp
                                <td>
                                    @if($col === 'Status')
                                        <span class="badge bg-{{ $item['status'] === 'Selesai' ? 'success' : ($item['status'] === 'Terjadwal' ? 'primary' : 'danger') }}">
                                            {{ $item[$key] ?? $item[$col] ?? '-' }}
                                        </span>
                                    @elseif($col === 'NIM' || $col === 'Kode' || $col === 'Bobot')
                                        <span class="badge bg-info">{{ $item[$key] ?? $item[$col] ?? '-' }}</span>
                                    @else
                                        {{ $item[$key] ?? $item[$col] ?? '-' }}
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <a href="#" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-secondary" title="Cetak">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 2 }}" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <nav>
            <ul class="pagination pagination-dummy justify-content-center mb-0">
                <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>
