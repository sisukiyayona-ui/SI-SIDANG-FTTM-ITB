<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\TFs;
use App\Models\TProdi;
use App\Services\MasterExcelService;
use App\Services\SpsiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdiController extends Controller
{
    public function template()
    {
        return MasterExcelService::template('prodi');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        // File hanya dibaca dari temp PHP lalu dibuang otomatis — tidak disimpan ke folder Laravel.
        $parsed = MasterExcelService::parse('prodi', $request->file('file'));

        $seen = [];
        $rows = [];
        foreach ($parsed as $p) {
            $inputFs = trim((string) ($p['values'][0] ?? ''));
            $kode = trim((string) ($p['values'][1] ?? ''));
            $nama = trim((string) ($p['values'][2] ?? ''));

            // Kolom FAKULTAS diisi user dengan nama; hasil resolve dikembalikan
            // sebagai kode supaya dropdown preview langsung terpilih.
            $fs = MasterExcelService::resolveFakultas($inputFs);
            $kodeFs = $fs ? $fs->KODE_FS : $inputFs;

            $existsInDb = $kode !== '' && TProdi::where('KODE_PRODI', $kode)->exists();
            $dupInFile = $kode !== '' && isset($seen[$kode]);
            $fsValid = $inputFs === '' || $fs !== null;
            if ($kode !== '') {
                $seen[$kode] = true;
            }

            $rows[] = [
                'row' => $p['row'],
                'kode_fs' => $kodeFs,
                'kode' => $kode,
                'nama' => $nama,
                'exists' => $existsInDb,
                'dup_in_file' => $dupInFile,
                'fs_valid' => $fsValid,
            ];
        }

        return response()->json(['success' => true, 'rows' => $rows]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'rows' => 'required|array',
        ]);

        $results = [];
        $inserted = 0;
        $failed = 0;

        // Semua atau tidak sama sekali: satu baris gagal, seluruh import dibatalkan.
        DB::beginTransaction();

        try {
            foreach ($request->input('rows') as $r) {
                $kodeFs = trim((string) ($r['kode_fs'] ?? ''));
                $kode = trim((string) ($r['kode'] ?? ''));
                $nama = trim((string) ($r['nama'] ?? ''));

                if ($kode === '' || $nama === '') {
                    $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'failed', 'message' => 'Kode dan Nama prodi wajib diisi'];
                    $failed++;
                    continue;
                }

                $fs = MasterExcelService::resolveFakultas($kodeFs);
                if (!$fs) {
                    $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'failed', 'message' => 'Fakultas ' . ($kodeFs ?: '-') . ' tidak terdaftar'];
                    $failed++;
                    continue;
                }

                if (TProdi::where('KODE_PRODI', $kode)->exists()) {
                    $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'failed', 'message' => 'Kode prodi ' . $kode . ' sudah terdaftar (aktif)'];
                    $failed++;
                    continue;
                }

                TProdi::create([
                    'KODE_PRODI' => $kode,
                    'NAMA_PRODI' => $nama,
                    'STATUS_AKTIF' => 'AKTIF',
                    'KODE_FS' => $fs->KODE_FS,
                    'NAMA_FS' => $fs->NAMA_FS,
                    'TGL_CREATE' => now(),
                ]);

                $results[] = ['kode' => $kode, 'nama' => $nama, 'status' => 'success', 'message' => 'Berhasil ditambahkan'];
                $inserted++;
            }

            $rolledBack = $failed > 0;

            if ($rolledBack) {
                DB::rollBack();
                $results = $this->markRolledBack($results);
                $inserted = 0;
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'success' => true,
            'inserted' => $inserted,
            'failed' => $failed,
            'results' => $results,
            'rolled_back' => $rolledBack,
        ]);
    }

    /**
     * Setelah rollback tidak ada baris tersimpan, jadi status "success" pada
     * hasil harus diganti supaya tidak berbohong ke user.
     */
    private function markRolledBack(array $results): array
    {
        foreach ($results as &$r) {
            if (($r['status'] ?? '') === 'success') {
                $r['status'] = 'failed';
                $r['message'] = 'Dibatalkan — ada baris lain yang gagal, tidak ada data yang disimpan';
            }
        }
        unset($r);

        return $results;
    }

    public function index(Request $request)
    {
        $query = TProdi::query();

        if ($s = $request->get('kode_prodi')) {
            $query->where('KODE_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('nama_prodi')) {
            $query->where('NAMA_PRODI', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('strata')) {
            $query->where('STRATA', 'like', '%' . $s . '%');
        }
        if ($s = $request->get('status_aktif')) {
            $query->where('STATUS_AKTIF', $s);
        }

        $prodi = $query->orderBy('KODE_PRODI', 'asc')->paginate(10)->withQueryString()->through(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'strata' => $p->strata ?? '',
                'kode_fs' => $p->kode_fs,
                'nama_fs' => $p->nama_fs,
                'status' => $p->status_aktif,
            ];
        });

        $allProdi = TProdi::all()->map(function($p) {
            return [
                'id' => $p->id,
                'kode' => $p->kode_prodi,
                'nama' => $p->nama_prodi,
                'strata' => $p->strata ?? '',
                'kode_fs' => $p->kode_fs,
                'nama_fs' => $p->nama_fs,
                'status' => $p->status_aktif,
            ];
        });

        if ($request->ajax()) {
            $tableHtml = view('master._prodi_table', compact('prodi'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        $fakultas = TFs::all();
        return view('master.prodi', compact('prodi', 'allProdi', 'fakultas'));
    }

    public function create()
    {
        $fakultas = TFs::all();
        return view('master.prodi-form', ['prodi' => null, 'fakultas' => $fakultas]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_prodi' => ['required', 'unique:t_prodi,KODE_PRODI'],
            'nama_prodi' => 'required',
            'kode_fs' => 'required',
            'status_aktif' => 'required',
        ], [
            'kode_prodi.unique' => 'Kode prodi sudah terdaftar. Gunakan kode prodi lain.',
        ]);

        $fs = TFs::where('KODE_FS', $request->kode_fs)->first();

        TProdi::create([
            'KODE_PRODI' => $request->kode_prodi,
            'NAMA_PRODI' => $request->nama_prodi,
            'STATUS_AKTIF' => $request->status_aktif,
            'KODE_FS' => $request->kode_fs,
            'NAMA_FS' => $fs?->nama_fs ?? $request->kode_fs,
            'TGL_CREATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $p = TProdi::find((int) $id);
        if (!$p) abort(404);
        $prodi = [
            'id' => $p->id,
            'kode' => $p->kode_prodi,
            'nama' => $p->nama_prodi,
            'status' => $p->status_aktif,
            'kode_fs' => $p->kode_fs,
            'nama_fs' => $p->nama_fs,
        ];
        $fakultas = TFs::all();
        return view('master.prodi-form', compact('prodi', 'fakultas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_prodi' => ['required', 'unique:t_prodi,KODE_PRODI,' . $id . ',id'],
            'nama_prodi' => 'required',
            'kode_fs' => 'required',
            'status_aktif' => 'required',
        ], [
            'kode_prodi.unique' => 'Kode prodi sudah terdaftar. Gunakan kode prodi lain.',
        ]);

        $p = TProdi::find((int) $id);
        if ($p) {
            $fs = TFs::where('KODE_FS', $request->kode_fs)->first();
            $p->update([
                'KODE_PRODI' => $request->kode_prodi,
                'NAMA_PRODI' => $request->nama_prodi,
                'STATUS_AKTIF' => $request->status_aktif,
                'KODE_FS' => $request->kode_fs,
                'NAMA_FS' => $fs?->nama_fs ?? $request->kode_fs,
                'TGL_UPDATE' => now(),
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil diperbarui.');
    }

    public function show($id)
    {
        $p = TProdi::find((int) $id);
        if (!$p) abort(404);
        $prodi = [
            'id' => $p->id,
            'kode' => $p->kode_prodi,
            'nama' => $p->nama_prodi,
            'status' => $p->status_aktif,
        ];
        return view('master.prodi-detail', compact('prodi'));
    }

    public function destroy(Request $request, $id)
    {
        $p = TProdi::find((int) $id);
        if ($p) {
            $p->delete();
        }
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('master.prodi.index')->with('success', 'Prodi berhasil dihapus.');
    }

    public function syncSpsi(Request $request)
    {
        try {
            $items = SpsiService::fetch('mst_prodi');
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        $namaFakultas = TFs::pluck('NAMA_FS', 'KODE_FS');

        $inserted = 0;
        $updated = 0;

        foreach ($items as $item) {
            $kode = trim((string) ($item['no_ps'] ?? ''));
            $nama = trim((string) ($item['nama_id'] ?? ''));
            $kdFak = trim((string) ($item['kd_fak'] ?? ''));

            if ($kode === '' || $nama === '') {
                continue;
            }

            $namaFak = $namaFakultas[$kdFak] ?? $kdFak;
            $status = !empty($item['status_aktif_prodi']) ? 'AKTIF' : 'NON AKTIF';

            $prodi = TProdi::where('KODE_PRODI', $kode)->first();

            if ($prodi) {
                $prodi->update([
                    'NAMA_PRODI' => $nama,
                    'STATUS_AKTIF' => $status,
                    'KODE_FS' => $kdFak,
                    'NAMA_FS' => $namaFak,
                    'TGL_UPDATE' => now(),
                ]);
                $updated++;
            } else {
                TProdi::create([
                    'KODE_PRODI' => $kode,
                    'NAMA_PRODI' => $nama,
                    'STATUS_AKTIF' => $status,
                    'KODE_FS' => $kdFak,
                    'NAMA_FS' => $namaFak,
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                ]);
                $inserted++;
            }
        }

        $message = 'Tarik data SPSI selesai: ' . $inserted . ' prodi ditambahkan, ' . $updated . ' diperbarui.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->route('master.prodi.index')->with('success', $message);
    }
}
