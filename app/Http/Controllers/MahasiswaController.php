<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use App\Models\TProdi;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $user = session('auth_user');
        $strata = $user['strata'] ?? 'S3'; // Default ke S3 jika tidak ada
        
        // Ambil semua judul milik mahasiswa
        $juduls = TJudul::where('id_user_mhs', $user['id'])->get();
        
        // Set judul aktif jika belum ada
        $activeJudulId = session('active_judul_id');
        if (!$activeJudulId && $juduls->count() > 0) {
            $activeJudulId = $juduls->first()->id;
            session(['active_judul_id' => $activeJudulId]);
        }
        
        // Query tracking progress sidang — base table t_judul agar mahasiswa baru (belum punya ajuan) tetap muncul
        $tracking = DB::select("
            SELECT DISTINCT
                j.id as id_judul,
                j.JUDUL as Judul,
                MAX(CASE
                    WHEN a1.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a1.status_ajukan_mhs, 't') != 'y' AND COALESCE(a1.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a1.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a1.status_lulus
                END) as tahap1,
                MAX(CASE
                    WHEN a2.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a2.status_ajukan_mhs, 't') != 'y' AND COALESCE(a2.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a2.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a2.status_lulus
                END) as tahap2,
                MAX(CASE
                    WHEN a3.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a3.status_ajukan_mhs, 't') != 'y' AND COALESCE(a3.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a3.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a3.status_lulus
                END) as sk1,
                MAX(CASE
                    WHEN a4.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a4.status_ajukan_mhs, 't') != 'y' AND COALESCE(a4.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a4.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a4.status_lulus
                END) as sk2,
                MAX(CASE
                    WHEN a5.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a5.status_ajukan_mhs, 't') != 'y' AND COALESCE(a5.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a5.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a5.status_lulus
                END) as sk3,
                MAX(CASE
                    WHEN a6.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a6.status_ajukan_mhs, 't') != 'y' AND COALESCE(a6.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a6.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a6.status_lulus
                END) as sk4,
                MAX(CASE
                    WHEN a7.id IS NULL THEN 'belum diajukan'
                    WHEN COALESCE(a7.status_ajukan_mhs, 't') != 'y' AND COALESCE(a7.status_ajukan_prodi, 't') != 'y' THEN 'belum diajukan'
                    WHEN a7.status_lulus IS NULL THEN 'dalam proses'
                    ELSE a7.status_lulus
                END) as tahap4
            FROM t_judul j
            LEFT JOIN t_user u ON j.ID_USER_MHS = u.id
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'tahap I'
                ORDER BY id DESC LIMIT 1
            ) a1 ON j.id = a1.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'tahap II'
                ORDER BY id DESC LIMIT 1
            ) a2 ON j.id = a2.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'SK I'
                ORDER BY id DESC LIMIT 1
            ) a3 ON j.id = a3.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'SK II'
                ORDER BY id DESC LIMIT 1
            ) a4 ON j.id = a4.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'SK III'
                ORDER BY id DESC LIMIT 1
            ) a5 ON j.id = a5.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'SK IV'
                ORDER BY id DESC LIMIT 1
            ) a6 ON j.id = a6.id_judul
            LEFT JOIN (
                SELECT * FROM t_ajuan_sidang
                WHERE id_user = " . $user['id'] . " AND tahapan_sidang = 'tahap IV'
                ORDER BY id DESC LIMIT 1
            ) a7 ON j.id = a7.id_judul
            WHERE j.ID_USER_MHS = " . $user['id'] . " AND u.STRATA = '" . $strata . "'
            GROUP BY j.id, j.JUDUL
        ");
        
        return view('mahasiswa.dashboard', compact('tracking', 'juduls', 'activeJudulId', 'strata'));
    }
    
    public function setActiveJudul($idJudul)
    {
        $user = session('auth_user');
        
        // Validasi bahwa judul milik mahasiswa
        $judul = TJudul::where('id_user_mhs', $user['id'])->where('id', $idJudul)->first();
        if (!$judul) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Judul tidak ditemukan.');
        }
        
        session(['active_judul_id' => $idJudul]);
        
        return redirect()->route('mahasiswa.dashboard')->with('success', 'Judul aktif berhasil diubah.');
    }
    
    public function showTahap($tahapan)
    {
        $user = session('auth_user');
        
        $tahapan = explode('?', $tahapan)[0];
        
        $idJudul = request('id_judul') ?? session('active_judul_id');
        
        // For Pembimbing/Penguji roles, get id_judul from request
        if (in_array($user['role'], ['Pembimbing', 'Penguji']) && request('id_judul')) {
            $idJudul = request('id_judul');
        }
        
        if (!$idJudul) {
            if (in_array($user['role'], ['Pembimbing', 'Penguji'])) {
                return response()->json(['error' => 'ID Judul diperlukan.'], 400);
            }
            $judul = TJudul::where('id_user_mhs', $user['id'])->first();
            if (!$judul) {
                return response()->json(['error' => 'Anda belum memiliki judul tesis.'], 404);
            }
            $idJudul = $judul->id;
            session(['active_judul_id' => $idJudul]);
        }
        
        // Data nama, nim, judul, status lulus dari table t_ajuan_sidang
        $ajuanQuery = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan);

        // Jika tidak ada id_judul dari request (mahasiswa sendiri), filter by id_user
        if (!request('id_judul') && !in_array($user['role'], ['Pembimbing', 'Penguji'])) {
            $ajuanQuery->where('id_user', $user['id']);
        }

        $ajuan = $ajuanQuery->first();
        
        // Get kode_prodi for persyaratan filtering
        $kodeProdi = $ajuan->kode_prodi ?? session('auth_user.kode_prodi');

        // Resolve kode_prodi string to actual prodi ID (integer)
        $prodiId = null;
        if ($kodeProdi) {
            $prodi = TProdi::where('kode_prodi', $kodeProdi)->first();
            $prodiId = $prodi?->id;
        }
            
        // Table t_tim_sidang (Pembimbing / Penguji)
        $timSidangQuery = \App\Models\TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan);
        
        // For Pembimbing/Penguji, show all team members
        $timSidang = $timSidangQuery->get();
            
        // Table t_cek_persyaratan (Persyaratan)
        $cekPersyaratan = \App\Models\TCekPersyaratan::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();
            
        // Jika data t_cek_persyaratan kosong, ambil dari t_syarat_sidang
        $persyaratan = collect();
        if ($cekPersyaratan->isEmpty()) {
            $persyaratan = \App\Models\TSyaratSidang::where('tahapan_sidang', $tahapan)
                ->where('status_aktif', 'AKTIF');
            
            // Filter by prodi ID (integer) — berlaku untuk semua role
            if ($prodiId) {
                $persyaratan->where('id_prodi', $prodiId);
            }
            
            $persyaratan = $persyaratan->get();
        } else {
            $persyaratan = $cekPersyaratan;
        }

        // Table penilaian - filter by user for Pembimbing/Penguji
        $penilaianQuery = \App\Models\TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan);
        
        if (in_array($user['role'], ['Pembimbing', 'Penguji'])) {
            $penilaianQuery->where('id_user_penilai', $user['id']);
        }
        
        $penilaian = $penilaianQuery->get();
        
        // Get point penilaian for form (distinct no_form based on tahapan)
        $pointPenilaian = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF')
            ->select('no_form')
            ->distinct()
            ->orderBy('no_form')
            ->get();
        
        // Get all point penilaian details
        $allPointPenilaian = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF')
            ->get();
        
        // Additional data for TU Prodi/Admin/FS Tahap I & Tahap II
        $users = collect();
        $skList = collect();
        
        if ((strtolower($tahapan) === 'tahap i' || strtolower($tahapan) === 'tahap ii') && in_array($user['role'], ['TU Prodi', 'Admin', 'FS'])) {
            // Get users for dropdown (dosen only)
            $users = \App\Models\TUser::where('STATUS_PEGAWAI', 'Dosen')
                ->select('ID', 'NAMA_LENGKAP', 'NIP_NIM')
                ->get();
            
            // Get all SK records for this judul+tahapan
            $skList = DB::table('t_sk')
                ->where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->orderBy('id', 'desc')
                ->get();
        }
        
        // Check if user is Ketua Tim Pembimbing (for status_lulus field)
        $isKetuaPembimbing = false;
        if ($user['role'] === 'Pembimbing') {
            $ketuaPembimbing = $timSidang->where('status_tim_sidang', 'Pembimbing')
                ->where('id_user_penilai', $user['id'])
                ->where('keterangan', 'Ketua Pembimbing')
                ->first();
            $isKetuaPembimbing = $ketuaPembimbing !== null;
        }
            
        // Return HTML fragment for AJAX request
        $viewName = (in_array($user['role'], ['Admin', 'TU Prodi', 'FS', 'Pembimbing', 'Penguji'])) ? 'sidang.tahap' : 'mahasiswa.tahap';

        if (request()->ajax()) {
            return view($viewName, compact('ajuan', 'timSidang', 'persyaratan', 'cekPersyaratan', 'penilaian', 'tahapan', 'idJudul', 'pointPenilaian', 'allPointPenilaian', 'isKetuaPembimbing', 'users', 'skList'))->render();
        }
        
        return view($viewName, compact('ajuan', 'timSidang', 'persyaratan', 'cekPersyaratan', 'penilaian', 'tahapan', 'idJudul', 'pointPenilaian', 'allPointPenilaian', 'isKetuaPembimbing', 'users', 'skList'));
    }
    
    public function uploadPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');
        
        $request->validate([
            'id_syarat_sidang' => 'required',
            'tahapan_sidang' => 'required',
            'file' => 'required|file|max:10240', // Max 10MB
        ]);
        
        $tahapan = $request->tahapan_sidang;
        $idSyarat = $request->id_syarat_sidang;
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
            $linkFile = '/storage/' . $path;
            
            // Check if exist in t_cek_persyaratan
            $cek = \App\Models\TCekPersyaratan::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->where('id_syarat_sidang', $idSyarat)
                ->first();
                
            if ($cek) {
                // Hapus file lama jika ada
                if ($cek->link_file) {
                    $oldPath = str_replace('/storage/', '', $cek->link_file);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                }
                
                // Update
                $cek->link_file = $linkFile;
                $cek->status_lengkap = 'y';
                $cek->tgl_update = date('Y-m-d H:i:s');
                $cek->save();
            } else {
                // Ambil info nama persyaratan dari TSyaratSidang
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                
                // Jika data kosong, insert semua persyaratan untuk tahapan ini terlebih dahulu agar berurutan
                $allSyarat = \App\Models\TSyaratSidang::where('tahapan_sidang', $tahapan)
                    ->where('status_aktif', 'AKTIF')
                    ->get();
                    
                foreach($allSyarat as $s) {
                    $newCek = new \App\Models\TCekPersyaratan();
                    $newCek->id_judul = $idJudul;
                    $newCek->tahapan_sidang = $tahapan;
                    $newCek->id_syarat_sidang = $s->id;
                    $newCek->Persyaratan = $s->nama_persyaratan;
                    
                    if ($s->id == $idSyarat) {
                        $newCek->link_file = $linkFile;
                        $newCek->status_lengkap = 'y';
                    } else {
                        $newCek->status_lengkap = 't';
                    }
                    
                    $newCek->tgl_buat = date('Y-m-d H:i:s');
                    $newCek->save();
                }
            }

            return response()->json(['success' => true, 'message' => 'File berhasil diupload', 'file_url' => url($linkFile)]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal upload file'], 400);
    }

    public function updateKelengkapan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = session('active_judul_id');
        
        $request->validate([
            'id_syarat_sidang' => 'required',
            'status_lengkap' => 'required',
        ]);
        
        $idSyarat = $request->id_syarat_sidang;
        $statusLengkap = $request->status_lengkap;
        
        // Get tahapan from the syarat
        $syarat = \App\Models\TSyaratSidang::find($idSyarat);
        if (!$syarat) {
            return response()->json(['success' => false, 'message' => 'Persyaratan tidak ditemukan'], 404);
        }
        
        $tahapan = $syarat->tahapan_sidang;
        
        // Check if exist in t_cek_persyaratan
        $cek = \App\Models\TCekPersyaratan::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->where('id_syarat_sidang', $idSyarat)
            ->first();
            
        if ($cek) {
            // Update
            $cek->status_lengkap = $statusLengkap;
            $cek->tgl_update = date('Y-m-d H:i:s');
            $cek->save();
        } else {
            // Insert new record
            $newCek = new \App\Models\TCekPersyaratan();
            $newCek->id_judul = $idJudul;
            $newCek->tahapan_sidang = $tahapan;
            $newCek->id_syarat_sidang = $idSyarat;
            $newCek->Persyaratan = $syarat->nama_persyaratan;
            $newCek->status_lengkap = $statusLengkap;
            $newCek->tgl_buat = date('Y-m-d H:i:s');
            $newCek->save();
        }
        
        return response()->json(['success' => true, 'message' => 'Status kelengkapan berhasil diupdate']);
    }

    public function saveAllPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');
        $tahapan = $request->tahapan_sidang;

        if (!$idJudul || !$tahapan) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }

        // Simpan status kelengkapan checkbox
        if ($request->has('kelengkapan')) {
            foreach ($request->kelengkapan as $idSyarat => $status) {
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->tahapan_sidang : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapSyarat)
                    ->where('id_syarat_sidang', $idSyarat)
                    ->first();

                if ($cek) {
                    $cek->status_lengkap = $status;
                    $cek->tgl_update = now();
                    $cek->save();
                } else {
                    $newCek = new \App\Models\TCekPersyaratan();
                    $newCek->id_judul = $idJudul;
                    $newCek->tahapan_sidang = $tahapSyarat;
                    $newCek->id_syarat_sidang = $idSyarat;
                    $newCek->Persyaratan = $syarat ? $syarat->nama_persyaratan : ('Persyaratan #' . $idSyarat);
                    $newCek->status_lengkap = $status;
                    $newCek->tgl_buat = now();
                    $newCek->save();
                }
            }
        }

        // Simpan file upload
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $idSyarat => $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
                $linkFile = '/storage/' . $path;

                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->tahapan_sidang : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapSyarat)
                    ->where('id_syarat_sidang', $idSyarat)
                    ->first();

                if ($cek) {
                    if ($cek->link_file) {
                        $oldPath = str_replace('/storage/', '', $cek->link_file);
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $cek->link_file = $linkFile;
                    $cek->status_lengkap = 'y';
                    $cek->tgl_update = now();
                    $cek->save();
                } else {
                    $allSyarat = \App\Models\TSyaratSidang::where('tahapan_sidang', $tahapSyarat)
                        ->where('status_aktif', 'AKTIF')
                        ->get();

                    foreach ($allSyarat as $s) {
                        $newCek = new \App\Models\TCekPersyaratan();
                        $newCek->id_judul = $idJudul;
                        $newCek->tahapan_sidang = $tahapSyarat;
                        $newCek->id_syarat_sidang = $s->id;
                        $newCek->Persyaratan = $s->nama_persyaratan;
                        $newCek->status_lengkap = ($s->id == $idSyarat) ? 'y' : 't';
                        if ($s->id == $idSyarat) {
                            $newCek->link_file = $linkFile;
                        }
                        $newCek->tgl_buat = now();
                        $newCek->save();
                    }
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Persyaratan berhasil disimpan']);
    }

    public function storeTimSidang(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = session('active_judul_id');

        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'id_user_penilai' => 'required',
            'status_tim_sidang' => 'required',
            'urutan' => 'required',
        ]);

        // Get user details from t_user table
        $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
        if (!$userPenilai) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $timSidang = new \App\Models\TTimSidang();
        $timSidang->ID_JUDUL = $request->id_judul;
        $timSidang->TAHAPAN_SIDANG = $request->tahapan_sidang;
        $timSidang->ID_USER_PENILAI = $request->id_user_penilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $userPenilai->NIP_NIM;
        $timSidang->NAMA = $userPenilai->NAMA_LENGKAP;
        $timSidang->URUTAN = $request->urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_CREATE = date('Y-m-d');
        $timSidang->TGL_UPDATE = date('Y-m-d');
        $timSidang->save();

        return response()->json(['success' => true, 'message' => 'Tim Pembimbing berhasil ditambahkan']);
    }

    public function updateTimSidang(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'id_user_penilai' => 'required',
            'status_tim_sidang' => 'required',
            'urutan' => 'required',
        ]);

        $timSidang = \App\Models\TTimSidang::find($id);
        if (!$timSidang) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Get user details from t_user table
        $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
        if (!$userPenilai) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }

        $timSidang->ID_USER_PENILAI = $request->id_user_penilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $userPenilai->NIP_NIM;
        $timSidang->NAMA = $userPenilai->NAMA_LENGKAP;
        $timSidang->URUTAN = $request->urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_UPDATE = date('Y-m-d');
        $timSidang->save();

        return response()->json(['success' => true, 'message' => 'Tim Pembimbing berhasil diupdate']);
    }

    public function deleteTimSidang($id)
    {
        $timSidang = \App\Models\TTimSidang::find($id);
        if (!$timSidang) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $timSidang->delete();

        return response()->json(['success' => true, 'message' => 'Tim Pembimbing berhasil dihapus']);
    }

    public function getNextSkNumber($tahapan, \Illuminate\Http\Request $request)
    {
        $nextNumber = DB::table('t_sk')->where('TAHAPAN_SIDANG', $tahapan)->count() + 1;
        $idJudul = $request->query('id_judul');
        $prodi = '322';
        if ($idJudul) {
            $prodi = DB::table('t_judul')
                ->join('t_user', 't_judul.nim', '=', 't_user.nip_nim')
                ->where('t_judul.id', $idJudul)
                ->value('t_user.kode_prodi') ?? '322';
        }
        $tahun = date('Y');
        $noSk = 'SK/' . strtoupper(str_replace(' ', '', $tahapan)) . '/' . $prodi . $tahun . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'no_sk' => $noSk,
        ]);
    }

    public function storeSk(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id_judul' => 'required|exists:t_judul,id',
            'tahapan_sidang' => 'required',
        ]);

        $noSk = $request->no_sk;
        if (!$noSk) {
            $nextNumber = DB::table('t_sk')->where('TAHAPAN_SIDANG', $request->tahapan_sidang)->count() + 1;
            $prodi = DB::table('t_judul')
                ->join('t_user', 't_judul.nim', '=', 't_user.nip_nim')
                ->where('t_judul.id', $request->id_judul)
                ->value('t_user.kode_prodi') ?? '322';
            $tahun = date('Y');
            $noSk = 'SK/' . strtoupper(str_replace(' ', '', $request->tahapan_sidang)) . '/' . $prodi . $tahun . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        }

        $skId = DB::table('t_sk')->insertGetId([
            'NO_SK' => $noSk,
            'ID_JUDUL' => $request->id_judul,
            'TAHAPAN_SIDANG' => $request->tahapan_sidang,
            'TGL_BUAT' => now(),
            'TGL_UPDATE' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'No SK berhasil dibuat.',
                'sk' => ['id' => $skId, 'no_sk' => $noSk],
            ]);
        }

        return redirect()->back()->with('success', 'No SK berhasil dibuat.');
    }

    public function storeJadwal(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'tgl_sidang' => 'required|date',
            'waktu_sidang' => 'required',
        ]);

        $idJudul = $request->id_judul;
        $tahapan = $request->tahapan_sidang;
        $tglSidang = $request->tgl_sidang;
        $waktuSidang = $request->waktu_sidang;

        // Check schedule conflicts for pembimbing/penguji (2 hour gap)
        $timSidang = \App\Models\TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        if ($timSidang->isNotEmpty()) {
            $penilaiIds = $timSidang->pluck('id_user_penilai')->unique()->filter()->toArray();

            if (!empty($penilaiIds)) {
                $waktuMulai = date('H:i:s', strtotime($waktuSidang . ' -2 hours'));
                $waktuAkhir = date('H:i:s', strtotime($waktuSidang . ' +2 hours'));

                // Exclude current ajuan record(s) from conflict check
                $currentIds = \App\Models\TAjuanSidang::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->pluck('id')
                    ->toArray();

                $conflict = \App\Models\TAjuanSidang::where('TGL_SIDANG', $tglSidang)
                    ->whereNotIn('id', $currentIds)
                    ->whereExists(function ($q) use ($penilaiIds) {
                        $q->select(\Illuminate\Support\Facades\DB::raw(1))
                            ->from('T_TIM_SIDANG')
                            ->whereColumn('T_TIM_SIDANG.id_judul', 'T_AJUAN_SIDANG.id_judul')
                            ->whereColumn('T_TIM_SIDANG.tahapan_sidang', 'T_AJUAN_SIDANG.tahapan_sidang')
                            ->whereIn('T_TIM_SIDANG.id_user_penilai', $penilaiIds);
                    })
                    ->where('WAKTU_SIDANG', '>=', $waktuMulai)
                    ->where('WAKTU_SIDANG', '<=', $waktuAkhir)
                    ->first();

                if ($conflict) {
                    $conflictNama = $conflict->NAMA_MHS ?? 'mahasiswa lain';
                    return response()->json([
                        'success' => false,
                        'message' => "Jadwal bentrok! Terdapat jadwal sidang lain pada {$tglSidang} pukul {$conflict->WAKTU_SIDANG} untuk {$conflictNama} dengan pembimbing/penguji yang sama. Minimal jeda 2 jam."
                    ]);
                }
            }
        }

        // Find existing ajuan or create new one
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->first();

        if (!$ajuan) {
            // Get judul info
            $judul = TJudul::find($idJudul);
            if (!$judul) {
                return response()->json(['success' => false, 'message' => 'Judul tidak ditemukan'], 404);
            }

            // Get user mahasiswa from judul
            $mahasiswa = \App\Models\TUser::find($judul->id_user_mhs);
            $prodi = \App\Models\TProdi::where('KODE_PRODI', $mahasiswa->KODE_PRODI ?? $user['kode_prodi'])->first();

            $ajuan = new TAjuanSidang();
            $ajuan->ID_USER = $mahasiswa->ID ?? $user['id'];
            $ajuan->NIM = $mahasiswa->NIP_NIM ?? $user['nip_nim'];
            $ajuan->NAMA_MHS = $mahasiswa->NAMA_LENGKAP ?? $user['nama_lengkap'];
            $ajuan->ANGKATAN = $mahasiswa->THN_ANGKATAN ?? date('Y');
            $ajuan->ID_JUDUL = $idJudul;
            $ajuan->JUDUL = $judul->Judul;
            $ajuan->TAHAPAN_SIDANG = $tahapan;
            $ajuan->STRATA = $mahasiswa->STRATA ?? $user['strata'];
            $ajuan->STATUS_AJUKAN_MHS = 't';
            $ajuan->TGL_CREATE = now();
            $ajuan->ID_USER_CREATE = $user['id'];
            $ajuan->NAMA_USER_CREATE = $user['nama_lengkap'];
            $ajuan->THN_CREATE = date('Y');
            $ajuan->ID_PRODI = $prodi->id ?? null;
            $ajuan->KODE_PRODI = $mahasiswa->KODE_PRODI ?? $user['kode_prodi'];
            $ajuan->NAMA_PRODI = $prodi->NAMA_PRODI ?? $user['nama_prodi'];
        }

        // Update jadwal fields
        $ajuan->TGL_SIDANG = $tglSidang;
        $ajuan->WAKTU_SIDANG = $waktuSidang;

        if ($request->filled('ruang_sidang')) {
            $ajuan->RUANG_SIDANG = $request->ruang_sidang;
        }
        if ($request->filled('tgl_surat_undangan')) {
            $ajuan->TGL_UNDANGAN = $request->tgl_surat_undangan;
        }
        if ($request->filled('no_surat_undangan')) {
            $ajuan->NO_UNDANGAN = $request->no_surat_undangan;
        }
        if ($request->filled('tgl_surat_penelaah')) {
            $ajuan->TGL_PENELAAH = $request->tgl_surat_penelaah;
        }
        if ($request->filled('no_surat_penelaah')) {
            $ajuan->NO_SURAT_PENELAAH = $request->no_surat_penelaah;
        }
        if ($request->filled('tgl_pemanggilan_penilai')) {
            $ajuan->TGL_PENGUMPULAN = $request->tgl_pemanggilan_penilai;
        }
        if ($request->filled('email_surat')) {
            $ajuan->EMAIL_SURAT = $request->email_surat;
        }
        if ($request->filled('no_sk_kelulusan')) {
            $ajuan->SK_LULUS = $request->no_sk_kelulusan;
        }
        if ($request->filled('status_lulus')) {
            $ajuan->STATUS_LULUS = $request->status_lulus;
        }

        if ($request->is_ajukan_fs) {
            $ajuan->STATUS_AJUKAN_MHS = 'y';
        }

        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        if ($request->is_ajukan_fs) {
            return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke FS']);
        }

        return response()->json(['success' => true, 'message' => 'Jadwal sidang berhasil disimpan']);
    }

    public function storeJudul(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'judul' => 'required|string|max:500',
        ]);

        $judul = TJudul::create([
            'JUDUL' => $request->judul,
            'ID_USER_MHS' => $user['id'],
            'NIM' => $user['nip_nim'],
            'THN_CREATE' => date('Y'),
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
        ]);

        $prodi = \App\Models\TProdi::where('KODE_PRODI', $user['kode_prodi'])->first();

        DB::table('t_ajuan_sidang')->insert([
            'ID_USER' => $user['id'],
            'NIM' => $user['nip_nim'],
            'NAMA_MHS' => $user['nama_lengkap'],
            'ANGKATAN' => $user['thn_angkatan'] ?? date('Y'),
            'ID_JUDUL' => $judul->id,
            'JUDUL' => $request->judul,
            'TAHAPAN_SIDANG' => 'tahap I',
            'STRATA' => $user['strata'],
            'STATUS_AJUKAN_MHS' => 'f',
            'TGL_CREATE' => now(),
            'TGL_UPDATE' => now(),
            'ID_USER_CREATE' => $user['id'],
            'NAMA_USER_CREATE' => $user['nama_lengkap'],
            'THN_CREATE' => date('Y'),
            'ID_PRODI' => $prodi?->id,
            'KODE_PRODI' => $user['kode_prodi'],
            'NAMA_PRODI' => $user['nama_prodi'],
        ]);

        session(['active_judul_id' => $judul->id]);

        return redirect()->back()->with('success', 'Judul berhasil ditambahkan.');
    }

    public function ubahJudul($idJudul)
    {
        $user = session('auth_user');
        
        // Get current judul from t_judul
        $judul = DB::table('t_judul')
            ->select('t_judul.ID as id_judul', 't_judul.JUDUL as Judul', 't_user.NAMA_LENGKAP as nama_mhs', 't_user.NIP_NIM as Nim')
            ->join('t_user', 't_judul.NIM', '=', 't_user.NIP_NIM')
            ->where('t_judul.ID', $idJudul)
            ->where('t_judul.ID_USER_MHS', $user['id'])
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

        return view('mahasiswa.ubah-judul', compact('judul', 'history'));
    }
}