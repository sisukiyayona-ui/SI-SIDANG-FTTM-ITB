<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SidangS3Controller extends Controller
{
    public function index()
    {
        $user = session('auth_user');
        $strata = 'S3';

        $juduls = collect();
        $activeJudulId = null;

        $tahapSub = function ($tahapan) {
            return '(SELECT x.* FROM t_ajuan_sidang x INNER JOIN (SELECT id_judul, MAX(id) as max_id FROM t_ajuan_sidang WHERE tahapan_sidang = "' . $tahapan . '" GROUP BY id_judul) y ON x.id = y.max_id AND x.id_judul = y.id_judul)';
        };

        $query = DB::table('t_judul as j')
            ->leftJoin('t_user as u', 'j.ID_USER_MHS', '=', 'u.id')
            ->select(
                'j.id as id_judul',
                'j.JUDUL as Judul',
                'j.NIM as Nim',
                'u.NAMA_LENGKAP as nama_mhs',
                DB::raw("
                    CASE
                        WHEN MAX(a1.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a1.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a1.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a1.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a1.status_lulus)
                    END as tahap1
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a2.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a2.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a2.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a2.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a2.status_lulus)
                    END as tahap2
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a3.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a3.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a3.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a3.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a3.status_lulus)
                    END as sk1
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a4.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a4.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a4.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a4.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a4.status_lulus)
                    END as sk2
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a5.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a5.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a5.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a5.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a5.status_lulus)
                    END as sk3
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a6.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a6.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a6.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a6.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a6.status_lulus)
                    END as sk4
                "),
                DB::raw("
                    CASE
                        WHEN MAX(a7.id) IS NULL THEN 'belum diajukan'
                        WHEN COALESCE(MAX(a7.status_ajukan_mhs), 't') != 'y' AND COALESCE(MAX(a7.status_ajukan_prodi), 't') != 'y' THEN 'belum diajukan'
                        WHEN MAX(a7.status_lulus) IS NULL THEN 'dalam proses'
                        ELSE MAX(a7.status_lulus)
                    END as tahap4
                ")
            )
            ->leftJoin(DB::raw($tahapSub('tahap I') . ' as a1'), 'j.id', '=', 'a1.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap II') . ' as a2'), 'j.id', '=', 'a2.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK I') . ' as a3'), 'j.id', '=', 'a3.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK II') . ' as a4'), 'j.id', '=', 'a4.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK III') . ' as a5'), 'j.id', '=', 'a5.id_judul')
            ->leftJoin(DB::raw($tahapSub('SK IV') . ' as a6'), 'j.id', '=', 'a6.id_judul')
            ->leftJoin(DB::raw($tahapSub('tahap IV') . ' as a7'), 'j.id', '=', 'a7.id_judul')
            ->where('u.STRATA', $strata)
            ->groupBy('j.id', 'j.JUDUL', 'j.NIM', 'u.NAMA_LENGKAP');

        // Role-based filtering
        if ($user['role'] === 'TU Prodi') {
            $query->where('u.KODE_PRODI', $user['kode_prodi']);
        } elseif ($user['role'] === 'FS') {
            // FS sees all records
        } elseif (in_array($user['role'], ['Pembimbing', 'Penguji', 'KPPS'])) {
            $query->join('t_tim_sidang as ts', 'j.id', '=', 'ts.ID_JUDUL')
                  ->where('ts.ID_USER_PENILAI', $user['id']);
        }

        $tracking = $query->orderBy('j.id', 'desc')->paginate(10);

        // Fetch mahasiswa list for Tambah Judul modal (TU Prodi/FS) — only those without a title
        $mahasiswaList = collect();
        if (in_array($user['role'], ['TU Prodi', 'FS'])) {
            $existingMhsIds = DB::table('t_judul')->pluck('ID_USER_MHS');
            $mhsQuery = \App\Models\TUser::where('JENIS_USER', 'Mahasiswa')
                ->whereNotIn('id', $existingMhsIds);
            if ($user['role'] === 'TU Prodi') {
                $mhsQuery->where('KODE_PRODI', $user['kode_prodi']);
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
            ->select('t_judul.ID as id_judul', 't_judul.JUDUL as Judul', 't_user.NAMA_LENGKAP as nama_mhs', 't_user.NIP_NIM as Nim')
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
            'TAHAP_PERUBAHAN' => $request->tahap,
            'ALASAN_PERUBAHAN' => $request->alasan,
            'ID_USER_MHS' => $user['id'],
            'NIM' => $nim,
            'TGL_CREATE' => now(),
        ]);

        // Update judul in t_judul
        DB::table('t_judul')
            ->where('id', $idJudul)
            ->update(['Judul' => $request->judul_baru, 'tgl_update' => now()]);

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
        ]);

        $mhs = \App\Models\TUser::find($request->id_user_mhs);
        if (!$mhs) {
            return redirect()->back()->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $existingJudul = DB::table('t_judul')->where('NIM', $mhs->NIP_NIM)->first();
        if ($existingJudul) {
            return redirect()->back()->with('error', 'Mahasiswa dengan NIM ' . $mhs->NIP_NIM . ' sudah memiliki judul.');
        }

        $judulId = DB::table('t_judul')->insertGetId([
            'JUDUL' => $request->judul,
            'ID_USER_MHS' => $request->id_user_mhs,
            'NIM' => $mhs->NIP_NIM,
            'THN_CREATE' => date('Y'),
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
        ]);

        $prodi = \App\Models\TProdi::where('KODE_PRODI', $mhs->KODE_PRODI)->first();

        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $request->id_user_mhs,
            'NIM' => $mhs->NIP_NIM,
            'NAMA_MHS' => $mhs->NAMA_LENGKAP,
            'ANGKATAN' => $mhs->THN_ANGKATAN ?? date('Y'),
            'ID_JUDUL' => $judulId,
            'JUDUL' => $request->judul,
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => 'S3',
            'TGL_SIDANG' => null,
            'WAKTU_SIDANG' => null,
            'RUANG_SIDANG' => null,
            'STATUS_LULUS' => null,
            'STATUS_AJUKAN_MHS' => 'y',
            'STATUS_AJUKAN_PRODI' => 'y',
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
            'ID_USER_CREATE' => $user['id'],
            'NAMA_USER_CREATE' => $user['nama_lengkap'],
            'THN_CREATE' => date('Y'),
            'ID_PRODI' => $prodi?->id,
            'KODE_PRODI' => $mhs->KODE_PRODI,
            'NAMA_PRODI' => $mhs->NAMA_PRODI,
        ]);

        return redirect()->back()->with('success', 'Judul berhasil ditambahkan.');
    }
}