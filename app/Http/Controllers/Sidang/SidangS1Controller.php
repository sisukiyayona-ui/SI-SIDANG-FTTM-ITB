<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidangS1Controller extends Controller
{
    public function index()
    {
        $user = session('auth_user');
        $strata = 'S1';

        $juduls = collect();
        $activeJudulId = null;

        $tahapSub = function ($tahapan) {
            return '(SELECT x.* FROM t_ajuan_sidang x INNER JOIN (SELECT id_judul, MAX(id) as max_id FROM t_ajuan_sidang WHERE tahapan_sidang = "' . $tahapan . '" GROUP BY id_judul) y ON x.id = y.max_id AND x.id_judul = y.id_judul)';
        };

        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id_judul',
                'a.Judul',
                'a.Nim',
                'a.nama_mhs',
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a1.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a1.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a1.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a1.status_lulus)
                    END as tahap1
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a2.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a2.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a2.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a2.status_lulus)
                    END as tahap2
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a3.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a3.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a3.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a3.status_lulus)
                    END as sk1
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a4.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a4.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a4.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a4.status_lulus)
                    END as sk2
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a5.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a5.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a5.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a5.status_lulus)
                    END as sk3
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a6.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a6.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a6.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a6.status_lulus)
                    END as sk4
                "),
                DB::raw("
                    CASE
                        WHEN COALESCE(MAX(a7.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a7.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a7.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a7.status_lulus)
                    END as tahap4
                ")
            )
            ->leftJoin(DB::raw($tahapSub('tahap I') . ' as a1'), 'a.id_judul', '=', 'a1.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap II') . ' as a2'), 'a.id_judul', '=', 'a2.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK I') . ' as a3'), 'a.id_judul', '=', 'a3.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK II') . ' as a4'), 'a.id_judul', '=', 'a4.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK III') . ' as a5'), 'a.id_judul', '=', 'a5.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK IV') . ' as a6'), 'a.id_judul', '=', 'a6.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap IV') . ' as a7'), 'a.id_judul', '=', 'a7.id_judul')
            ->where('a.Strata', $strata)
            ->groupBy('a.id_judul', 'a.Judul', 'a.Nim', 'a.nama_mhs');

        if ($user['role'] === 'TU Prodi') {
            $query->where('a.kode_prodi', $user['kode_prodi']);
        } elseif ($user['role'] === 'FS') {
            // FS sees all prodi, only status_ajukan_prodi = 'y'
            $query->where('a.status_ajukan_prodi', 'y');
        } elseif ($user['role'] === 'KPPS') {
            $query->where('a.status_ajukan_prodi', 'y');
        }

        $tracking = $query->paginate(10);

        return view('sidang.s1', compact('strata', 'tracking', 'juduls', 'activeJudulId'));
    }
    
    public function show($id)
    {
        $sidang = TAjuanSidang::with(['judul', 'user', 'timSidang', 'cekPersyaratan'])->find($id);
        
        if (!$sidang) {
            abort(404);
        }
        
        return view('sidang.s1-detail', compact('sidang'));
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
}
