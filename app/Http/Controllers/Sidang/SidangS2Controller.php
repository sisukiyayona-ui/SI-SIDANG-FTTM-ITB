<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TProdi;
use App\Models\TUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidangS2Controller extends Controller
{
    public function index(Request $request)
    {
        $user = session('auth_user');
        $strata = 'S2';

        $juduls = collect();
        $activeJudulId = null;

        $tahapSub = function ($tahapan) {
            return '(SELECT x.* FROM t_ajuan_sidang x INNER JOIN (SELECT id_judul, MAX(id) as max_id FROM t_ajuan_sidang WHERE tahapan_sidang = "' . $tahapan . '" GROUP BY id_judul) y ON x.id = y.max_id AND x.id_judul = y.id_judul)';
        };

        $caseSql = function ($alias, $tahapan) {
            return "
                CASE
                    WHEN MAX({$alias}.status_lulus) IS NOT NULL AND MAX({$alias}.status_lulus) != 'diajukan' THEN MAX({$alias}.status_lulus)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.status_ajukan_mhs), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_prodi), 't') != 'y' THEN 'diproses di TU Prodi'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_kpps), 't') = 'y' AND EXISTS (
                        SELECT 1 FROM t_kpps k0
                        INNER JOIN t_ajuan_sidang x0 ON x0.KODE_PRODI = k0.KODE_PRODI
                        WHERE x0.id_judul = a.id_judul AND x0.tahapan_sidang = '{$tahapan}' AND k0.STATUS_AKTIF = 'AKTIF'
                    ) AND (
                        SELECT COUNT(DISTINCT app.ID_USER) FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = a.id_judul AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE IN ('t', 'y')
                        AND EXISTS (SELECT 1 FROM t_kpps kk WHERE kk.ID_USER = app.ID_USER AND kk.STATUS_AKTIF = 'AKTIF')
                    ) >= (
                        SELECT COUNT(DISTINCT k.ID_USER) FROM t_kpps k
                        INNER JOIN t_ajuan_sidang x3 ON x3.KODE_PRODI = k.KODE_PRODI
                        WHERE x3.id_judul = a.id_judul AND x3.tahapan_sidang = '{$tahapan}' AND k.STATUS_AKTIF = 'AKTIF'
                    ) THEN 'terjadwal'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_kpps), 't') = 'y' THEN 'menunggu approve kpps'
                    WHEN EXISTS (
                        SELECT 1 FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = a.id_judul AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE = 'f'
                    ) THEN 'rejected'
                    ELSE 'diproses di fakultas'
                END";
        };

        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id_judul',
                'a.Judul',
                'a.Nim',
                'a.nama_mhs',
                'a.NAMA_PRODI as nama_prodi',
                DB::raw($caseSql('a1', 'TS1') . ' as tesis1'),
                DB::raw($caseSql('a2', 'TS2') . ' as tesis2'),
                DB::raw($caseSql('a7', 'tahap IV') . ' as tahap4')
            )
            ->leftJoin(DB::raw($tahapSub('TS1') . ' as a1'), 'a.id_judul', '=', 'a1.id_judul')
            ->leftJoin(DB::raw($tahapSub('TS2') . ' as a2'), 'a.id_judul', '=', 'a2.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap IV') . ' as a7'), 'a.id_judul', '=', 'a7.id_judul')
            ->where('a.Strata', $strata)
            ->groupBy('a.id_judul', 'a.Judul', 'a.Nim', 'a.nama_mhs', 'a.NAMA_PRODI');

        if ($user['role'] === 'TU Prodi') {
            $prodiIds = DB::table('t_user_prodi')
                ->where('ID_USER', $user['id'] ?? 0)
                ->pluck('ID_PRODI')
                ->all();

            if (!empty($prodiIds)) {
                $query->whereIn('a.ID_PRODI', $prodiIds);
            } else {
                $query->where('a.kode_prodi', $user['kode_prodi']);
            }
        } elseif ($user['role'] === 'FS') {
            $query->where('a.status_ajukan_prodi', 'y');
        } elseif ($user['role'] === 'KPPS') {
            $query->where('a.status_ajukan_prodi', 'y');
        } elseif ($user['role'] === 'Ketua Sidang') {
            // Ketua Sidang hanya melihat judul tempat ia tercatat sebagai
            // Ketua Sidang pada tim sidang.
            $query->whereExists(function ($sub) use ($user) {
                $sub->selectRaw('1')
                    ->from('t_tim_sidang as tk')
                    ->whereColumn('tk.ID_JUDUL', 'a.id_judul')
                    ->where('tk.ID_USER_PENILAI', $user['id'] ?? 0)
                    ->whereRaw("UPPER(tk.STATUS_TIM_SIDANG) LIKE '%KETUA SIDANG%'");
            });
        }

        $tracking = DB::table(DB::raw("({$query->toSql()}) as track"))
            ->mergeBindings($query)
            ->where(function($q) use ($request) {
                if ($nim = $request->get('nim')) {
                    $q->where('Nim', 'like', '%' . $nim . '%');
                }
                if ($nama = $request->get('nama')) {
                    $q->where('nama_mhs', 'like', '%' . $nama . '%');
                }
                if ($judul = $request->get('judul')) {
                    $q->where('Judul', 'like', '%' . $judul . '%');
                }
                if ($namaProdi = $request->get('nama_prodi')) {
                    $q->where('nama_prodi', 'like', '%' . $namaProdi . '%');
                }
                foreach (['tesis1', 'tesis2', 'tahap4'] as $col) {
                    if ($val = $request->get($col)) {
                        $q->where($col, $val);
                    }
                }
            })
            ->paginate($this->resolvePerPage($request))->withQueryString();

        if ($request->ajax()) {
            $tableHtml = view('sidang._s2_table', compact('tracking', 'strata'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        $mahasiswaList = collect();
        if (in_array($user['role'], ['TU Prodi', 'FS'])) {
            $existingMhsIds = DB::table('t_judul')->pluck('ID_USER_MHS');
            $mhsQuery = \App\Models\TUser::where('JENIS_USER', 'Mahasiswa')
                ->where('STRATA', 'S2')
                ->whereNotIn('id', $existingMhsIds);
            if ($user['role'] === 'TU Prodi') {
                $prodiIds = DB::table('t_user_prodi')
                    ->where('id_user', $user['id'] ?? 0)
                    ->pluck('id_prodi')
                    ->all();
                if (!empty($prodiIds)) {
                    $kodeProdiList = \App\Models\TProdi::whereIn('id', $prodiIds)->pluck('KODE_PRODI');
                    $mhsQuery->whereIn('KODE_PRODI', $kodeProdiList);
                } else {
                    $mhsQuery->where('KODE_PRODI', $user['kode_prodi']);
                }
            }
            $mahasiswaList = $mhsQuery->orderBy('NAMA_LENGKAP')->get(['id', 'NIP_NIM', 'NAMA_LENGKAP']);
        }

        return view('sidang.s2', compact('strata', 'tracking', 'juduls', 'activeJudulId', 'mahasiswaList'));
    }
    
    public function show($id)
    {
        $sidang = TAjuanSidang::with(['judul', 'user', 'timSidang', 'cekPersyaratan'])->find($id);
        
        if (!$sidang) {
            abort(404);
        }
        
        return view('sidang.s2-detail', compact('sidang'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_sidang' => 'nullable|date',
            'waktu_sidang' => 'nullable',
            'ruang_sidang' => 'nullable|string|max:250',
            'status_ajukan_prodi' => 'nullable|in:y,t',
            'status_lulus' => 'nullable|string|max:50',
        ]);
        
        $sidang = TAjuanSidang::find($id);
        if ($sidang) {
            $sidang->update([
                'tgl_sidang' => $request->tgl_sidang,
                'waktu_sidang' => $request->waktu_sidang,
                'ruang_sidang' => $request->ruang_sidang,
                'status_ajukan_prodi' => $request->status_ajukan_prodi,
                'status_lulus' => $request->status_lulus,
                'tgl_update' => now(),
            ]);
        }
        
        return redirect()->back()->with('success', 'Data sidang berhasil diperbarui');
    }

    public function storeJudul(Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'id_user_mhs' => 'required|exists:t_user,id',
            'judul'       => 'required|string|max:500',
            'abstrak'     => 'required|string|max:1000',
        ]);

        $mhs = \App\Models\TUser::find($request->id_user_mhs);
        if (!$mhs) {
            return redirect()->back()->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $prodi = \App\Models\TProdi::where('KODE_PRODI', $mhs->KODE_PRODI)->first();
        if (!$prodi) {
            return redirect()->back()->with('error', 'Prodi mahasiswa tidak ditemukan. Periksa data prodi mahasiswa.');
        }

        $existingJudul = DB::table('t_judul')
            ->where(function ($q) use ($mhs) {
                $q->where('NIM', $mhs->NIP_NIM)->orWhere('ID_USER_MHS', $mhs->id);
            })
            ->first();

        if ($existingJudul) {
            $judulId = $existingJudul->id ?? $existingJudul->ID;
            $hasAjuanTS1 = DB::table('t_ajuan_sidang')
                ->where('ID_JUDUL', $judulId)
                ->where('TAHAPAN_SIDANG', 'TS1')
                ->exists();

            if ($hasAjuanTS1) {
                return redirect()->back()->with('error', 'Mahasiswa dengan NIM ' . $mhs->NIP_NIM . ' sudah memiliki judul.');
            }

            $this->insertAjuanTS1FromTuProdi($judulId, $existingJudul->JUDUL ?? $request->judul, $mhs, $prodi, $user);
            return redirect()->back()->with('success', 'Judul sudah ada; pengajuan Tesis I dilengkapi dan langsung Diproses di TU Prodi.');
        }

        try {
            DB::transaction(function () use ($request, $mhs, $prodi, $user) {
                $judulId = DB::table('t_judul')->insertGetId([
                    'JUDUL'       => $request->judul,
                    'ABSTRAK'     => $request->abstrak ?? null,
                    'ID_USER_MHS' => $request->id_user_mhs,
                    'NIM'         => $mhs->NIP_NIM,
                    'THN_CREATE'  => date('Y'),
                    'TGL_CREATE'  => now(),
                    'TGL_UPDATE'  => now(),
                ]);

                $this->insertAjuanTS1FromTuProdi($judulId, $request->judul, $mhs, $prodi, $user);
            });
        } catch (\Throwable $e) {
            \Log::error('storeJudul S2 gagal', ['error' => $e->getMessage(), 'mhs' => $mhs->id]);
            return redirect()->back()->with('error', 'Gagal menambah judul: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Judul berhasil ditambahkan dan langsung Diproses di TU Prodi.');
    }

    /**
     * Insert ajuan Tesis I saat judul S2 dibuat oleh TU Prodi / FS.
     * STATUS_AJUKAN_MHS = 'y' agar status langsung "Diproses di TU Prodi".
     */
    private function insertAjuanTS1FromTuProdi(int $judulId, string $judul, $mhs, $prodi, array $user): void
    {
        DB::table('t_ajuan_sidang')->insert([
            'ID_USER'           => $mhs->id,
            'NIM'               => $mhs->NIP_NIM,
            'NAMA_MHS'          => $mhs->NAMA_LENGKAP,
            'ANGKATAN'          => $mhs->THN_ANGKATAN ?? date('Y'),
            'ID_JUDUL'          => $judulId,
            'JUDUL'             => $judul,
            'TAHAPAN_SIDANG'    => 'TS1',
            'STRATA'            => 'S2',
            'STATUS_AJUKAN_MHS' => 'y',
            'STATUS_LULUS'      => 'diajukan',
            'ID_PRODI'          => $prodi->id ?? null,
            'KODE_PRODI'        => $mhs->KODE_PRODI,
            'NAMA_PRODI'        => $prodi->NAMA_PRODI ?? null,
            'ID_USER_CREATE'    => $user['id'],
            'NAMA_USER_CREATE'  => $user['nama_lengkap'],
            'THN_CREATE'        => date('Y'),
            'TGL_CREATE'        => now(),
            'TGL_UPDATE'        => now(),
        ]);
    }
}
