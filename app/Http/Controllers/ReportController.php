<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\TUser;
use App\Models\VReportTipeI;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $user = session('auth_user');

        $query = VReportTipeI::query();

        if ($user['role'] === 'TU Prodi') {
            $query->where('kode_prodi', $user['kode_prodi']);
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
