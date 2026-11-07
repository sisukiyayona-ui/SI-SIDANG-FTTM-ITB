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

        // S2 tidak memakai KPPS: setelah TU Prodi approve, status lanjut ke
        // fakultas dan menjadi terjadwal begitu tanggal sidang diisi.
        $caseSql = function ($alias, $tahapan) {
            return "
                CASE
                    WHEN MAX({$alias}.status_lulus) IS NOT NULL AND MAX({$alias}.status_lulus) != 'diajukan' THEN MAX({$alias}.status_lulus)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.status_ajukan_mhs), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_prodi), 't') != 'y' THEN 'diproses di TU Prodi'
                    WHEN EXISTS (
                        SELECT 1 FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = j.id AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE = 'f'
                    ) THEN 'rejected'
                    WHEN MAX({$alias}.tgl_sidang) IS NOT NULL THEN 'terjadwal'
                    ELSE 'diproses di fakultas'
                END";
        };

        $query = DB::table('t_judul as j')
            ->leftJoin('t_user as u', 'j.ID_USER_MHS', '=', 'u.id')
            ->select(
                'j.id as id_judul',
                'j.JUDUL as Judul',
                'j.NIM as Nim',
                'u.NAMA_LENGKAP as nama_mhs',
                'u.NAMA_PRODI as nama_prodi',
                DB::raw($caseSql('a1', 'Tesis I') . ' as tesis1'),
                DB::raw($caseSql('a2', 'Tesis II') . ' as tesis2')
            )
            ->leftJoin(DB::raw($tahapSub('Tesis I') . ' as a1'), 'j.id', '=', 'a1.id_judul')
            ->leftJoin(DB::raw($tahapSub('Tesis II') . ' as a2'), 'j.id', '=', 'a2.id_judul')
            ->where(function ($q) use ($strata) {
                $q->where('u.STRATA', $strata)
                  ->orWhereExists(function ($sub) use ($strata) {
                      $sub->selectRaw('1')
                          ->from('t_ajuan_sidang as xs')
                          ->whereColumn('xs.id_judul', 'j.id')
                          ->where('xs.STRATA', $strata);
                  });
            })
            ->groupBy('j.id', 'j.JUDUL', 'j.NIM', 'u.NAMA_LENGKAP', 'u.NAMA_PRODI');

        if ($user['role'] === 'TU Prodi') {
            $prodiIds = DB::table('t_user_prodi')
                ->where('ID_USER', $user['id'] ?? 0)
                ->pluck('ID_PRODI')
                ->all();

            if (!empty($prodiIds)) {
                $kodeProdiList = TProdi::whereIn('id', $prodiIds)->pluck('KODE_PRODI')->all();
                $query->where(function ($q) use ($prodiIds, $kodeProdiList) {
                    $q->whereExists(function ($sub) use ($prodiIds) {
                        $sub->selectRaw('1')
                            ->from('t_ajuan_sidang as xp')
                            ->whereColumn('xp.ID_JUDUL', 'j.id')
                            ->whereIn('xp.ID_PRODI', $prodiIds);
                    });
                    if (!empty($kodeProdiList)) {
                        $q->orWhereIn('u.KODE_PRODI', $kodeProdiList)
                          ->orWhereExists(function ($sub) use ($kodeProdiList) {
                              $sub->selectRaw('1')
                                  ->from('t_ajuan_sidang as xp')
                                  ->whereColumn('xp.ID_JUDUL', 'j.id')
                                  ->whereIn('xp.KODE_PRODI', $kodeProdiList);
                          });
                    }
                });
            } else {
                $query->where(function ($q) use ($user) {
                    $q->where('u.KODE_PRODI', $user['kode_prodi'])
                      ->orWhereExists(function ($sub) use ($user) {
                          $sub->selectRaw('1')
                              ->from('t_ajuan_sidang as xp')
                              ->whereColumn('xp.ID_JUDUL', 'j.id')
                              ->where('xp.KODE_PRODI', $user['kode_prodi']);
                      });
                });
            }
        } elseif ($user['role'] === 'FS') {
            // FS melihat semua
        } elseif ($user['role'] === 'KPPS') {
            // KPPS hanya menangani S3
            $query->whereRaw('1 = 0');
        } elseif (in_array($user['role'], ['Pembimbing', 'Penguji'])) {
            $query->join('t_tim_sidang as ts', 'j.id', '=', 'ts.ID_JUDUL')
                  ->where('ts.ID_USER_PENILAI', $user['id']);
        } elseif ($user['role'] === 'Ketua Sidang') {
            $query->whereExists(function ($sub) use ($user) {
                $sub->selectRaw('1')
                    ->from('t_tim_sidang as tk')
                    ->whereColumn('tk.ID_JUDUL', 'j.id')
                    ->where('tk.ID_USER_PENILAI', $user['id'] ?? 0)
                    ->whereRaw("UPPER(tk.STATUS_TIM_SIDANG) LIKE '%KETUA SIDANG%'");
            });
        }

        $tracking = DB::table(DB::raw("({$query->orderBy('j.id', 'desc')->toSql()}) as track"))
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
                foreach (['tesis1', 'tesis2'] as $col) {
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
        $sidang = TAjuanSidang::find($id);

        if (!$sidang) {
            abort(404);
        }

        return redirect()->route('sidang.s2', ['judul' => $sidang->ID_JUDUL]);
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

    public function ubahJudul($idJudul)
    {
        $judul = DB::table('t_judul')
            ->select('t_judul.ID as id_judul', 't_judul.JUDUL as Judul', 't_judul.ABSTRAK as ABSTRAK', 't_user.NAMA_LENGKAP as nama_mhs', 't_user.NIP_NIM as Nim')
            ->join('t_user', 't_judul.NIM', '=', 't_user.NIP_NIM')
            ->where('t_judul.ID', $idJudul)
            ->first();

        if (!$judul) {
            abort(404);
        }

        $history = DB::table('t_judul_temp')
            ->select('ID_JUDUL', 'JUDUL as judul_lama', 'JUDUL_BARU as judul_baru', 'TAHAP_PERUBAHAN as tahap', 'ALASAN_PERUBAHAN as alasan', 'TGL_CREATE as tgl_create')
            ->where('ID_JUDUL', $idJudul)
            ->orderBy('ID', 'desc')
            ->get();

        return view('sidang.ubah-judul', [
            'judul' => $judul,
            'history' => $history,
            'tahapOptions' => ['Tesis I', 'Tesis II'],
            'idJudul' => $idJudul,
            'indexRoute' => route('sidang.s2'),
            'storeRoute' => route('sidang.s2.store-ubah-judul', $idJudul),
            'indexLabel' => 'Sidang S2',
        ]);
    }

    public function storeUbahJudul(Request $request, $idJudul)
    {
        $request->validate([
            'judul_baru' => 'required|string',
            'abstrak' => 'required|string|max:1000',
            'tahap' => 'required|in:Tesis I,Tesis II',
            'alasan' => 'required|string',
        ]);

        $user = session('auth_user');
        $nim = DB::table('t_judul')->where('id', $idJudul)->value('NIM');

        DB::table('t_judul_temp')->insert([
            'ID_JUDUL' => $idJudul,
            'JUDUL' => $request->judul_lama,
            'JUDUL_BARU' => $request->judul_baru,
            'ABSTRAK' => $request->abstrak,
            'TAHAP_PERUBAHAN' => $request->tahap,
            'ALASAN_PERUBAHAN' => $request->alasan,
            'ID_USER_MHS' => $user['id'],
            'NIM' => $nim,
            'TGL_CREATE' => now(),
        ]);

        DB::table('t_judul')
            ->where('id', $idJudul)
            ->update(['Judul' => $request->judul_baru, 'ABSTRAK' => $request->abstrak, 'tgl_update' => now()]);

        DB::table('t_ajuan_sidang')
            ->where('id_judul', $idJudul)
            ->update(['Judul' => $request->judul_baru, 'tgl_update' => now()]);

        DB::table('t_penilaian')
            ->where('id_judul', $idJudul)
            ->update(['judul' => $request->judul_baru, 'tgl_update' => now()]);

        return redirect()->route('sidang.s2.ubah-judul', $idJudul)->with('success', 'Judul berhasil diubah');
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
                ->where('TAHAPAN_SIDANG', 'Tesis I')
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
            'TAHAPAN_SIDANG'      => 'Tesis I',
            'STRATA'              => 'S2',
            'STATUS_AJUKAN_MHS'   => 'y',
            'STATUS_AJUKAN_PRODI' => 't',
            'STATUS_LULUS'        => null,
            'TGL_SIDANG'          => null,
            'WAKTU_SIDANG'        => null,
            'RUANG_SIDANG'        => null,
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
