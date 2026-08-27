<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidangS1Controller extends Controller
{
    public function index(Request $request)
    {
        $user = session('auth_user');
        $strata = 'S1';

        $juduls = collect();
        $activeJudulId = null;

        $tahapSub = function ($tahapan) {
            return '(SELECT x.* FROM t_ajuan_sidang x INNER JOIN (SELECT id_judul, MAX(id) as max_id FROM t_ajuan_sidang WHERE tahapan_sidang = "' . $tahapan . '" GROUP BY id_judul) y ON x.id = y.max_id AND x.id_judul = y.id_judul)';
        };

        $caseSql = function ($alias) {
            return "
                CASE
                    WHEN MAX({$alias}.status_lulus) IS NOT NULL AND MAX({$alias}.status_lulus) != 'diajukan' THEN MAX({$alias}.status_lulus)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.status_ajukan_mhs), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_prodi), 't') != 'y' THEN 'diproses di TU Prodi'
                    WHEN COALESCE(MAX({$alias}.status_ajukan_kpps), 't') != 'y' THEN 'diproses di fakultas'
                    WHEN MAX({$alias}.tgl_sidang) IS NULL THEN 'menunggu pelaksanaan sidang'
                    ELSE 'terjadwal'
                END";
        };

        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id_judul',
                'a.Judul',
                'a.Nim',
                'a.nama_mhs',
                DB::raw($caseSql('a1') . ' as tahap1'),
                DB::raw($caseSql('a2') . ' as tahap2'),
                DB::raw($caseSql('a3') . ' as sk1'),
                DB::raw($caseSql('a4') . ' as sk2'),
                DB::raw($caseSql('a5') . ' as sk3'),
                DB::raw($caseSql('a6') . ' as sk4'),
                DB::raw($caseSql('a7') . ' as tahap4')
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
            $query->where('a.status_ajukan_prodi', 'y');
        } elseif ($user['role'] === 'KPPS') {
            $query->where('a.status_ajukan_prodi', 'y');
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
                foreach (['tahap1', 'tahap2', 'sk1', 'sk2', 'sk3', 'sk4', 'tahap4'] as $col) {
                    if ($val = $request->get($col)) {
                        $q->where($col, $val);
                    }
                }
            })
            ->paginate(10)->withQueryString();

        if ($request->ajax()) {
            $tableHtml = view('sidang._s1_table', compact('tracking', 'strata'))->render();
            return response()->json(['html' => $tableHtml]);
        }

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
