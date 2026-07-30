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

        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id',
                'j.id as id_judul',
                DB::raw('YEAR(a.tgl_create) as tahun'),
                'j.NIM',
                'u.NAMA_LENGKAP as nama_mahasiswa',
                'j.JUDUL',
                'ts.NIP',
                'ts.NAMA as pembimbing_penguji',
                'ts.STATUS_TIM_SIDANG',
                'a.tahapan_sidang',
                'a.status_lulus'
            )
            ->join('t_judul as j', 'a.id_judul', '=', 'j.id')
            ->join('t_user as u', 'j.id_user_mhs', '=', 'u.id')
            ->join('t_tim_sidang as ts', function ($join) {
                $join->on('ts.id_judul', '=', 'j.id')
                     ->on('ts.tahapan_sidang', '=', 'a.tahapan_sidang');
            })
            ->where('a.strata', 'S3')
            ->orderBy('j.id')
            ->orderBy('a.tahapan_sidang')
            ->orderBy('ts.URUTAN');

        if ($user['role'] === 'TU Prodi') {
            $query->where('a.kode_prodi', $user['kode_prodi']);
        } elseif (in_array($user['role'], ['FS', 'Monev'])) {
            // sees all records
        }

        $reports = $query->get();

        return view('report.index', compact('reports'));
    }
    
    public function showDetail($idJudul, $tahapan)
    {
        $user = session('auth_user');
        
        $judul = DB::table('t_judul')->where('id', $idJudul)->first();
        
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan'], 404);
        }
        
        $detail = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->where('Strata', 'S3')
            ->with(['timSidang', 'cekPersyaratan'])
            ->first();
        
        if (!$detail) {
            return response()->json([
                'Judul' => $judul->Judul,
                'tahapan_sidang' => $tahapan,
                'tgl_sidang' => null,
                'waktu_sidang' => null,
                'ruang_sidang' => null,
                'status_lulus' => 'belum diajukan',
            ]);
        }
        
        if (in_array($user['role'], ['TU Prodi', 'FS'])) {
            // Filter by prodi/FS if needed
        }
        
        return response()->json($detail);
    }
}
