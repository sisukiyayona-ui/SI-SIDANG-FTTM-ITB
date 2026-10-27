<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidangS3Controller extends Controller
{
    public function index(Request $request)
    {
        $user = session('auth_user');
        $strata = 'S3';

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
                        WHERE x0.id_judul = j.id AND x0.tahapan_sidang = '{$tahapan}' AND k0.STATUS_AKTIF = 'AKTIF'
                    ) AND (
                        SELECT COUNT(DISTINCT app.ID_USER) FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = j.id AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE IN ('t', 'y')
                        AND EXISTS (SELECT 1 FROM t_kpps kk WHERE kk.ID_USER = app.ID_USER AND kk.STATUS_AKTIF = 'AKTIF')
                    ) >= (
                        SELECT COUNT(DISTINCT k.ID_USER) FROM t_kpps k
                        INNER JOIN t_ajuan_sidang x3 ON x3.KODE_PRODI = k.KODE_PRODI
                        WHERE x3.id_judul = j.id AND x3.tahapan_sidang = '{$tahapan}' AND k.STATUS_AKTIF = 'AKTIF'
                    ) THEN 'terjadwal'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_kpps), 't') = 'y' THEN 'menunggu approve kpps'
                    WHEN EXISTS (
                        SELECT 1 FROM t_app_ajuan_sidang app
                        INNER JOIN t_ajuan_sidang x2 ON x2.id = app.ID_AJUAN_SIDANG
                        WHERE x2.id_judul = j.id AND x2.tahapan_sidang = '{$tahapan}' AND app.STATUS_APPROVE = 'f'
                    ) THEN 'rejected'
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
                DB::raw($caseSql('a1', 'tahap I') . ' as tahap1'),
                DB::raw($caseSql('a2', 'tahap II') . ' as tahap2'),
                DB::raw($caseSql('a3', 'SK I') . ' as sk1'),
                DB::raw($caseSql('a4', 'SK II') . ' as sk2'),
                DB::raw($caseSql('a5', 'SK III') . ' as sk3'),
                DB::raw($caseSql('a6', 'SK IV') . ' as sk4'),
                DB::raw($caseSql('a7', 'tahap IV') . ' as tahap4')
            )
            ->leftJoin(DB::raw($tahapSub('tahap I') . ' as a1'), 'j.id', '=', 'a1.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap II') . ' as a2'), 'j.id', '=', 'a2.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK I') . ' as a3'), 'j.id', '=', 'a3.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK II') . ' as a4'), 'j.id', '=', 'a4.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK III') . ' as a5'), 'j.id', '=', 'a5.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK IV') . ' as a6'), 'j.id', '=', 'a6.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap IV') . ' as a7'), 'j.id', '=', 'a7.id_judul')
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
                $kodeProdiList = \App\Models\TProdi::whereIn('id', $prodiIds)->pluck('KODE_PRODI')->all();
                // Muncul jika ajuan punya ID_PRODI/KODE_PRODI prodi TU, ATAU mahasiswa prodinya cocok
                // (agar judul yang belum punya ajuan / ID_PRODI kosong tetap terlihat)
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
            // FS sees all records
        } elseif (in_array($user['role'], ['Pembimbing', 'Penguji', 'KPPS'])) {
            $query->join('t_tim_sidang as ts', 'j.id', '=', 'ts.ID_JUDUL')
                  ->where('ts.ID_USER_PENILAI', $user['id']);
        } elseif ($user['role'] === 'Ketua Sidang') {
            // Ketua Sidang hanya melihat judul tempat ia tercatat sebagai
            // Ketua Sidang pada tim sidang — bukan seluruh progress S3.
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
                foreach (['tahap1', 'tahap2', 'sk1', 'sk2', 'sk3', 'sk4', 'tahap4'] as $col) {
                    if ($val = $request->get($col)) {
                        $q->where($col, $val);
                    }
                }
            })
            ->paginate($this->resolvePerPage($request))->withQueryString();

        if ($request->ajax()) {
            $tableHtml = view('sidang._s3_table', compact('tracking', 'strata'))->render();
            return response()->json(['html' => $tableHtml]);
        }

        $mahasiswaList = collect();
        if (in_array($user['role'], ['TU Prodi', 'FS'])) {
            $existingMhsIds = DB::table('t_judul')->pluck('ID_USER_MHS');
            $mhsQuery = \App\Models\TUser::where('JENIS_USER', 'Mahasiswa')
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

        return view('sidang.s3', compact('strata', 'tracking', 'juduls', 'activeJudulId', 'mahasiswaList'));
    }
    
    public function show($id)
    {
        $sidang = TAjuanSidang::with(['judul', 'user', 'timSidang', 'cekPersyaratan'])->find($id);
        
        if (!$sidang) {
            abort(404);
        }
        
        return view('sidang.s3-detail', compact('sidang'));
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
        $user = session('auth_user');
        
        // Get current judul from t_judul
        $judul = DB::table('t_judul')
            ->select('t_judul.ID as id_judul', 't_judul.JUDUL as Judul', 't_judul.ABSTRAK as ABSTRAK', 't_user.NAMA_LENGKAP as nama_mhs', 't_user.NIP_NIM as Nim')
            ->join('t_user', 't_judul.NIM', '=', 't_user.NIP_NIM')
            ->where('t_judul.ID', $idJudul)
            ->first();
        
        if (!$judul) {
            abort(404);
        }

        // Get judul history from t_judul_temp
        $history = DB::table('t_judul_temp')
            ->select('ID_JUDUL', 'JUDUL as judul_lama', 'JUDUL_BARU as judul_baru', 'TAHAP_PERUBAHAN as tahap', 'ALASAN_PERUBAHAN as alasan', 'TGL_CREATE as tgl_create')
            ->where('ID_JUDUL', $idJudul)
            ->orderBy('ID', 'desc')
            ->get();

        // Get tahap options
        $tahapOptions = ['tahap I', 'tahap II', 'SK I', 'SK II', 'SK III', 'SK IV', 'tahap IV'];

        return view('sidang.ubah-judul', compact('judul', 'history', 'tahapOptions', 'idJudul'));
    }

    public function storeUbahJudul(Request $request, $idJudul)
    {
        $request->validate([
            'judul_baru' => 'required|string',
            'abstrak' => 'required|string|max:1000',
            'tahap' => 'required|in:tahap I,tahap II,SK I,SK II,SK III,SK IV,tahap IV',
            'alasan' => 'required|string',
        ]);

        $user = session('auth_user');

        // Get NIM from t_judul
        $nim = DB::table('t_judul')
            ->where('id', $idJudul)
            ->value('NIM');

        // Insert into t_judul_temp
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

        // Update judul & abstrak in t_judul
        DB::table('t_judul')
            ->where('id', $idJudul)
            ->update(['Judul' => $request->judul_baru, 'ABSTRAK' => $request->abstrak, 'tgl_update' => now()]);

        // Update judul in t_ajuan_sidang
        DB::table('t_ajuan_sidang')
            ->where('id_judul', $idJudul)
            ->update(['Judul' => $request->judul_baru, 'tgl_update' => now()]);

        // Update judul in t_penilaian
        DB::table('t_penilaian')
            ->where('id_judul', $idJudul)
            ->update(['judul' => $request->judul_baru, 'tgl_update' => now()]);

        return redirect()->route('sidang.s3.ubah-judul', $idJudul)->with('success', 'Judul berhasil diubah');
    }

    public function storeJudul(Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'id_user_mhs' => 'required|exists:t_user,id',
            'judul' => 'required|string|max:500',
            'abstrak' => 'required|string|max:1000',
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
                $q->where('NIM', $mhs->NIP_NIM)
                  ->orWhere('ID_USER_MHS', $mhs->id);
            })
            ->first();

        // Judul sudah ada + ajuan tahap I ada → tolak
        // Judul sudah ada tapi ajuan hilang → lengkapi ajuan (auto-ajukan karena dibuat dari TU Prodi)
        if ($existingJudul) {
            $judulId = $existingJudul->id ?? $existingJudul->ID;
            $hasAjuanTahap1 = DB::table('t_ajuan_sidang')
                ->where('ID_JUDUL', $judulId)
                ->where('TAHAPAN_SIDANG', 'tahap I')
                ->exists();

            if ($hasAjuanTahap1) {
                return redirect()->back()->with('error', 'Mahasiswa dengan NIM ' . $mhs->NIP_NIM . ' sudah memiliki judul.');
            }

            $this->insertAjuanTahap1FromTuProdi($judulId, $existingJudul->JUDUL ?? $request->judul, $mhs, $prodi, $user);

            return redirect()->back()->with('success', 'Judul sudah ada; pengajuan tahap I dilengkapi dan langsung Diproses di TU Prodi.');
        }

        try {
            DB::transaction(function () use ($request, $mhs, $prodi, $user) {
                $judulId = DB::table('t_judul')->insertGetId([
                    'JUDUL' => $request->judul,
                    'ABSTRAK' => $request->abstrak ?? null,
                    'ID_USER_MHS' => $request->id_user_mhs,
                    'NIM' => $mhs->NIP_NIM,
                    'THN_CREATE' => date('Y'),
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                ]);

                $this->insertAjuanTahap1FromTuProdi($judulId, $request->judul, $mhs, $prodi, $user);
            });
        } catch (\Throwable $e) {
            \Log::error('storeJudul S3 gagal', ['error' => $e->getMessage(), 'mhs' => $mhs->id]);
            return redirect()->back()->with('error', 'Gagal menambah judul: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Judul berhasil ditambahkan dan langsung Diproses di TU Prodi.');
    }

    /**
     * Insert ajuan tahap I saat judul dibuat oleh TU Prodi/FS.
     * STATUS_AJUKAN_MHS = 'y' agar status langsung "Diproses di TU Prodi"
     * (beda dengan mahasiswa yang harus ajukan dulu).
     */
    private function insertAjuanTahap1FromTuProdi($judulId, string $judul, $mhs, $prodi, array $user): void
    {
        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $mhs->id,
            'NIM' => $mhs->NIP_NIM,
            'NAMA_MHS' => $mhs->NAMA_LENGKAP,
            'ANGKATAN' => $mhs->THN_ANGKATAN ?? date('Y'),
            'ID_JUDUL' => $judulId,
            'JUDUL' => $judul,
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => $mhs->STRATA ?: 'S3',
            'TGL_SIDANG' => null,
            'WAKTU_SIDANG' => null,
            'RUANG_SIDANG' => null,
            'STATUS_LULUS' => null,
            'STATUS_AJUKAN_MHS' => 'y',
            'STATUS_AJUKAN_PRODI' => 't',
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
            'ID_USER_CREATE' => $user['id'],
            'NAMA_USER_CREATE' => $user['nama_lengkap'] ?? '-',
            'THN_CREATE' => date('Y'),
            'ID_PRODI' => $prodi->id,
            'KODE_PRODI' => $prodi->KODE_PRODI,
            'NAMA_PRODI' => $prodi->NAMA_PRODI,
        ]);
    }
}
