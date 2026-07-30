<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class JadwalSidangController extends Controller
{
    public function index()
    {
        $user = session('auth_user');
        
        $query = DB::table('t_ajuan_sidang as a')
            ->select(
                'a.id',
                'a.id_judul',
                'a.Judul',
                'a.Nim',
                'a.nama_mhs',
                'a.tahapan_sidang',
                'a.tgl_sidang',
                'a.waktu_sidang',
                'a.ruang_sidang',
                'a.status_lulus',
                'a.Strata'
            )
            ->whereNotNull('a.tgl_sidang');

        // Role-based filtering
        if ($user['role'] === 'Mahasiswa') {
            $query->where('a.id_user', $user['id']);
        } elseif ($user['role'] === 'TU Prodi') {
            $query->where('a.kode_prodi', $user['kode_prodi']);
        } elseif ($user['role'] === 'FS') {
            // FS sees all prodi, only status_ajukan_prodi = 'y'
            $query->where('a.status_ajukan_prodi', 'y');
        } elseif (in_array($user['role'], ['Pembimbing', 'Penguji'])) {
            // Filter by judul yang dibimbing/diuji
            $query->whereIn('a.id_judul', function($q) use ($user) {
                $q->select('id_judul')
                  ->from('t_tim_sidang')
                  ->where('id_user_penilai', $user['id']);
            });
        }

        $jadwalSidang = $query->orderBy('a.tgl_sidang', 'desc')->get();

        return view('sidang.jadwal-sidang', compact('jadwalSidang'));
    }
}
