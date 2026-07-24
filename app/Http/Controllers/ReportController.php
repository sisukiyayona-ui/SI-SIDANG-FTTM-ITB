<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\TUser;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = session('auth_user');

        // Query untuk report progress sidang sesuai requirement
        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id_judul',
                'a.Judul',
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a1.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a1.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a1.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a1.status_lulus)
                    END as tahap1
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a2.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a2.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a2.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a2.status_lulus)
                    END as tahap2
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a3.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a3.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a3.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a3.status_lulus)
                    END as sk1
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a4.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a4.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a4.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a4.status_lulus)
                    END as sk2
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a5.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a5.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a5.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a5.status_lulus)
                    END as sk3
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a6.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a6.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a6.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a6.status_lulus)
                    END as sk4
                '),
                DB::raw('
                    CASE
                        WHEN COALESCE(MAX(a7.status_ajukan_mhs), \'t\') != \'y\' AND COALESCE(MAX(a7.status_ajukan_prodi), \'t\') != \'y\' THEN \'belum diajukan\'
                        WHEN MAX(a7.status_lulus) IS NULL THEN \'dalam proses\'
                        ELSE MAX(a7.status_lulus)
                    END as tahap4
                ')
            )
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "tahap I" ORDER BY id DESC LIMIT 1) as a1'), function($join) {
                $join->on('a.id_judul', '=', 'a1.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "tahap II" ORDER BY id DESC LIMIT 1) as a2'), function($join) {
                $join->on('a.id_judul', '=', 'a2.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "SK I" ORDER BY id DESC LIMIT 1) as a3'), function($join) {
                $join->on('a.id_judul', '=', 'a3.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "SK II" ORDER BY id DESC LIMIT 1) as a4'), function($join) {
                $join->on('a.id_judul', '=', 'a4.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "SK III" ORDER BY id DESC LIMIT 1) as a5'), function($join) {
                $join->on('a.id_judul', '=', 'a5.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "SK IV" ORDER BY id DESC LIMIT 1) as a6'), function($join) {
                $join->on('a.id_judul', '=', 'a6.id_judul');
            })
            ->leftJoin(DB::raw('(SELECT * FROM t_ajuan_sidang WHERE tahapan_sidang = "tahap IV" ORDER BY id DESC LIMIT 1) as a7'), function($join) {
                $join->on('a.id_judul', '=', 'a7.id_judul');
            })
            ->where('a.Strata', 'S3')
            ->groupBy('a.id', 'a.id_judul', 'a.Judul');

        // Filter berdasarkan role
        if ($user['role'] === 'TU Prodi') {
            $query->where('a.kode_prodi', $user['kode_prodi']);
        }

        $reports = $query->get();

        return view('report.index', compact('reports'));
    }
    
    public function showDetail($idJudul, $tahapan)
    {
        $user = session('auth_user');
        
        // Get judul info
        $judul = DB::table('t_judul')->where('id', $idJudul)->first();
        
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan'], 404);
        }
        
        // Get detail data untuk tahapan tertentu
        $detail = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->where('Strata', 'S3')
            ->with(['timSidang', 'cekPersyaratan'])
            ->first();
        
        if (!$detail) {
            // Return basic info if no detail exists
            return response()->json([
                'Judul' => $judul->Judul,
                'tahapan_sidang' => $tahapan,
                'tgl_sidang' => null,
                'waktu_sidang' => null,
                'ruang_sidang' => null,
                'status_lulus' => 'belum diajukan',
            ]);
        }
        
        // Filter berdasarkan role
        if ($user['role'] === 'TU Prodi' && $detail->kode_prodi !== $user['kode_prodi']) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json($detail);
    }
}
