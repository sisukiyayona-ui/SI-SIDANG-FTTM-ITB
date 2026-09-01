<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use App\Models\TPenilaian;
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
        // Status CASE expression: consistent with SidangS1/S2/S3 controllers
        $statusCase = function ($alias) {
            return "CASE
                    WHEN MAX({$alias}.STATUS_LULUS) IS NOT NULL AND MAX({$alias}.STATUS_LULUS) != 'diajukan' THEN MAX({$alias}.STATUS_LULUS)
                    WHEN MAX({$alias}.id) IS NULL OR COALESCE(MAX({$alias}.STATUS_AJUKAN_MHS), 't') != 'y' THEN 'belum diajukan'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_PRODI), 't') != 'y' THEN 'Diproses di TU Prodi'
                    WHEN COALESCE(MAX({$alias}.STATUS_AJUKAN_KPPS), 't') != 'y' THEN 'Diproses di Fakultas'
                    WHEN MAX({$alias}.TGL_SIDANG) IS NULL THEN 'Menunggu Pelaksanaan Sidang'
                    ELSE 'Terjadwal'
                END";
        };

        $tracking = DB::select("
            SELECT DISTINCT
                j.id as id_judul,
                j.JUDUL as Judul,
                {$statusCase('a1')} as tahap1,
                {$statusCase('a2')} as tahap2,
                {$statusCase('a3')} as sk1,
                {$statusCase('a4')} as sk2,
                {$statusCase('a5')} as sk3,
                {$statusCase('a6')} as sk4,
                {$statusCase('a7')} as tahap4
            FROM t_judul j
            LEFT JOIN t_user u ON j.ID_USER_MHS = u.id
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'tahap I'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a1 ON j.id = a1.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'tahap II'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a2 ON j.id = a2.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'SK I'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a3 ON j.id = a3.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'SK II'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a4 ON j.id = a4.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'SK III'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a5 ON j.id = a5.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'SK IV'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a6 ON j.id = a6.ID_JUDUL
            LEFT JOIN (
                SELECT x.* FROM t_ajuan_sidang x
                INNER JOIN (
                    SELECT id_judul, MAX(id) as max_id
                    FROM t_ajuan_sidang
                    WHERE tahapan_sidang = 'tahap IV'
                    GROUP BY id_judul
                ) y ON x.id = y.max_id AND x.id_judul = y.id_judul
            ) a7 ON j.id = a7.ID_JUDUL
            WHERE j.ID_USER_MHS = ? AND u.STRATA = ?
            GROUP BY j.id, j.JUDUL
        ", [$user['id'], $strata]);
        
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

        // Validasi kepemilikan judul untuk role Mahasiswa
        if ($user['role'] === 'Mahasiswa') {
            $judulMhs = TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists();
            if (!$judulMhs) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        
        // Data nama, nim, judul, status lulus dari table t_ajuan_sidang
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->latest('id')
            ->first();

        // All ajuan records for this judul+tahapan (for jadwal history table)
        $allAjuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('id')
            ->get();

        $allAjuanJson = $allAjuan->map(function($a) {
            return [
                'id' => $a->id,
                'tgl_sidang' => $a->tgl_sidang,
                'waktu_sidang' => $a->waktu_sidang,
                'ruang_sidang' => $a->ruang_sidang,
                'tgl_surat_undangan' => $a->tgl_undangan,
                'no_surat_undangan' => $a->NO_UNDANGAN,
                'tgl_surat_penelaah' => $a->tgl_penelaah,
                'no_surat_penelaah' => $a->no_surat_penelaah,
                'tgl_pemanggilan_penilai' => $a->tgl_pemanggilan_penilai,
                'tgl_hasil_penelahan' => $a->TGL_HASIL_PENELAHAN,
                'email_surat' => $a->email_surat,
                'no_sk_kelulusan' => $a->SK_LULUS,
            ];
        })->values();

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
            
        // Get syarat IDs belonging to this prodi (to filter cek_persyaratan)
        $syaratIdsForProdi = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
            ->where('STATUS_AKTIF', 'AKTIF');
        if ($prodiId) {
            $syaratIdsForProdi->where('ID_PRODI', $prodiId);
        }
        $syaratIdsForProdi = $syaratIdsForProdi->pluck('id');

        // Table t_cek_persyaratan (Persyaratan) — filter by prodi syarat IDs
        $cekPersyaratan = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->whereIn('ID_SYARAT_SIDANG', $syaratIdsForProdi)
            ->get();

        // Jika data t_cek_persyaratan kosong, ambil dari t_syarat_sidang
        $persyaratan = collect();
        if ($cekPersyaratan->isEmpty()) {
            $persyaratan = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                ->where('STATUS_AKTIF', 'AKTIF');

            // Filter by prodi ID (integer) — berlaku untuk semua role
            if ($prodiId) {
                $persyaratan->where('ID_PRODI', $prodiId);
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
        
        // Get point penilaian for form (distinct no_form based on tahapan and prodi)
        // Get mahasiswa's prodi from judul->user relationship
        $mahasiswaProdi = null;
        if ($idJudul) {
            $judul = \App\Models\TJudul::find($idJudul);
            if ($judul && $judul->user) {
                $mahasiswaProdi = $judul->user->KODE_PRODI;
            }
        }
        
        $pointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        // Filter by prodi if mahasiswa prodi is available
        if ($mahasiswaProdi) {
            $pointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $pointPenilaian = $pointPenilaianQuery->select('no_form')
            ->distinct()
            ->orderBy('no_form')
            ->get();
        
        // Get all point penilaian details (with same filters)
        $allPointPenilaianQuery = \App\Models\TPointPenilaian::where('tahapan_sidang', $tahapan)
            ->where('status_aktif', 'AKTIF');
        
        if ($mahasiswaProdi) {
            $allPointPenilaianQuery->where('KODE_PRODI', $mahasiswaProdi);
        }
        
        $allPointPenilaian = $allPointPenilaianQuery->get();
        
        // Additional data for TU Prodi/Admin/FS Tahap I & Tahap II
        $users = collect();
        $skList = collect();
        
        if (in_array($tahapan, ['tahap I', 'tahap II', 'SK I', 'SK II', 'SK III', 'SK IV', 'tahap IV']) && in_array($user['role'], ['TU Prodi', 'Admin', 'FS'])) {
            // Get users for dropdown (dosen OR users with role Pembimbing/Penguji)
            $dosenUsers = \App\Models\TUser::where('STATUS_PEGAWAI', 'Dosen')
                ->select('ID', 'NAMA_LENGKAP', 'NIP_NIM')
                ->pluck('ID')
                ->toArray();
            $pembimbingPengujiIds = \App\Models\TUserRole::whereIn('ROLE', ['Pembimbing', 'Penguji'])
                ->pluck('ID_USER')
                ->unique()
                ->toArray();
            $allUserIds = array_unique(array_merge($dosenUsers, $pembimbingPengujiIds));
            $users = \App\Models\TUser::whereIn('ID', $allUserIds)
                ->select('ID', 'NAMA_LENGKAP', 'NIP_NIM')
                ->orderBy('NAMA_LENGKAP')
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
            $ketuaPembimbing = $timSidang->first(function($item) use ($user) {
                return str_contains($item->status_tim_sidang ?? '', 'Pembimbing')
                    && $item->id_user_penilai == $user['id'];
            });
            $isKetuaPembimbing = $ketuaPembimbing !== null;
        }
            
        // Return HTML fragment for AJAX request
        $viewName = (in_array($user['role'], ['Admin', 'TU Prodi', 'FS', 'Pembimbing', 'Penguji'])) ? 'sidang.tahap' : 'mahasiswa.tahap';

        if (request()->ajax()) {
            return view($viewName, compact('ajuan', 'allAjuan', 'allAjuanJson', 'timSidang', 'persyaratan', 'cekPersyaratan', 'penilaian', 'tahapan', 'idJudul', 'pointPenilaian', 'allPointPenilaian', 'isKetuaPembimbing', 'users', 'skList'))->render();
        }
        
        return view($viewName, compact('ajuan', 'allAjuan', 'allAjuanJson', 'timSidang', 'persyaratan', 'cekPersyaratan', 'penilaian', 'tahapan', 'idJudul', 'pointPenilaian', 'allPointPenilaian', 'isKetuaPembimbing', 'users', 'skList'));
    }
    
    public function uploadPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');

        if (!$idJudul) {
            \Log::error('ID Judul tidak ditemukan in uploadPersyaratan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'ID Judul tidak ditemukan'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id_syarat_sidang' => 'required',
            'tahapan_sidang' => 'required',
            'file' => 'required|file|max:10240|mimetypes:application/pdf',
        ]);

        $tahapan = $request->tahapan_sidang;
        $idSyarat = $request->id_syarat_sidang;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file->getRealPath());
            if ($mimeType !== 'application/pdf') {
                return response()->json(['success' => false, 'message' => 'Hanya file PDF yang diizinkan'], 422);
            }

            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.pdf';
            $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
            $linkFile = '/storage/' . $path;

            // Check if exist in t_cek_persyaratan
            $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                ->where('TAHAPAN_SIDANG', $tahapan)
                ->where('ID_SYARAT_SIDANG', $idSyarat)
                ->first();

            if ($cek) {
                // Hapus file lama jika ada
                if ($cek->LINK_FILE) {
                    $oldPath = str_replace('/storage/', '', $cek->LINK_FILE);
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                }

                // Update
                $cek->LINK_FILE = $linkFile;
                $cek->STATUS_LENGKAP = 'y';
                $cek->TGL_UPDATE = date('Y-m-d');
                $cek->save();
            } else {
                // Ambil info nama persyaratan dari TSyaratSidang
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);

                // Jika data kosong, insert semua persyaratan untuk tahapan ini terlebih dahulu agar berurutan
                $allSyarat = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapan)
                    ->where('STATUS_AKTIF', 'AKTIF')
                    ->get();
                    
                foreach($allSyarat as $s) {
                    $newCek = new \App\Models\TCekPersyaratan();
                    $newCek->ID_JUDUL = $idJudul;
                    $newCek->TAHAPAN_SIDANG = $tahapan;
                    $newCek->ID_SYARAT_SIDANG = $s->id;
                    $newCek->PERSYARATAN = $s->NAMA_PERSYARATAN;

                    if ($s->id == $idSyarat) {
                        $newCek->LINK_FILE = $linkFile;
                        $newCek->STATUS_LENGKAP = 'y';
                    } else {
                        $newCek->STATUS_LENGKAP = 't';
                    }

                    $newCek->TGL_BUAT = date('Y-m-d');
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
        $idJudul = $request->id_judul ?? session('active_judul_id');
        
        if (!$idJudul) {
            \Log::error('ID Judul tidak ditemukan in updateKelengkapan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'ID Judul tidak ditemukan'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'id_syarat_sidang' => 'required',
            'status_lengkap' => 'required',
        ]);
        
        $idSyarat = $request->id_syarat_sidang;
        $statusLengkap = $request->status_lengkap;
        
        // Get tahapan from the syarat
        $syarat = \App\Models\TSyaratSidang::find($idSyarat);
        if (!$syarat) {
            \Log::error('Persyaratan tidak ditemukan', ['id_syarat' => $idSyarat]);
            return response()->json(['success' => false, 'message' => 'Persyaratan tidak ditemukan'], 404);
        }
        
        $tahapan = $syarat->TAHAPAN_SIDANG;
        
        // Check if exist in t_cek_persyaratan
        $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->where('ID_SYARAT_SIDANG', $idSyarat)
            ->first();
            
        if ($cek) {
            // Update
            $cek->STATUS_LENGKAP = $statusLengkap;
            $cek->TGL_UPDATE = date('Y-m-d');
            $cek->save();
            \Log::info('Updated kelengkapan', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'id_syarat' => $idSyarat, 'status' => $statusLengkap]);
        } else {
            // Insert new record
            $newCek = new \App\Models\TCekPersyaratan();
            $newCek->ID_JUDUL = $idJudul;
            $newCek->TAHAPAN_SIDANG = $tahapan;
            $newCek->ID_SYARAT_SIDANG = $idSyarat;
            $newCek->PERSYARATAN = $syarat->NAMA_PERSYARATAN;
            $newCek->STATUS_LENGKAP = $statusLengkap;
            $newCek->TGL_BUAT = date('Y-m-d');
            $newCek->save();
            \Log::info('Inserted kelengkapan', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'id_syarat' => $idSyarat, 'status' => $statusLengkap]);
        }
        
        return response()->json(['success' => true, 'message' => 'Status kelengkapan berhasil diupdate']);
    }

    public function saveAllPersyaratan(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');
        $idJudul = $request->id_judul ?? session('active_judul_id');
        $tahapan = $request->tahapan_sidang;

        if (!$idJudul || !$tahapan) {
            \Log::error('Data tidak lengkap in saveAllPersyaratan', ['request' => $request->all(), 'session' => session()->all()]);
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
        }

        if ($user['role'] === 'Mahasiswa' && !TJudul::where('id', $idJudul)->where('id_user_mhs', $user['id'])->exists()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        \Log::info('saveAllPersyaratan called', ['id_judul' => $idJudul, 'tahapan' => $tahapan, 'kelengkapan' => $request->kelengkapan]);

        // Check if ajuan record exists for this tahapan, if not create it
        $ajuan = \App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->first();

        if (!$ajuan) {
            // Get judul information
            $judul = \App\Models\TJudul::find($idJudul);
            if ($judul) {
                $mahasiswa = \App\Models\TUser::find($judul->ID_USER_MHS);
                $prodi = \App\Models\TProdi::find($judul->ID_PRODI);

                $ajuan = new \App\Models\TAjuanSidang();
                $ajuan->ID_USER = $judul->ID_USER_MHS;
                $ajuan->NIM = $mahasiswa ? $mahasiswa->NIP_NIM : '';
                $ajuan->NAMA_MHS = $mahasiswa ? $mahasiswa->NAMA_LENGKAP : '';
                $ajuan->ANGKATAN = $mahasiswa && $mahasiswa->THN_ANGKATAN ? $mahasiswa->THN_ANGKATAN : (int)date('Y');
                $ajuan->ID_JUDUL = $idJudul;
                $ajuan->JUDUL = $judul->JUDUL;
                $ajuan->TAHAPAN_SIDANG = $tahapan;
                $ajuan->STRATA = $mahasiswa ? $mahasiswa->STRATA : 'S3';
                $ajuan->STATUS_LULUS = null; // 'dalam proses'
                $ajuan->STATUS_AJUKAN_MHS = 't';
                $ajuan->STATUS_AJUKAN_PRODI = 't';
                if (!$prodi && !empty($user['kode_prodi'])) {
                    $prodi = \App\Models\TProdi::where('KODE_PRODI', $user['kode_prodi'])->first();
                }
                $ajuan->ID_PRODI = $prodi ? $prodi->id : null;
                $ajuan->KODE_PRODI = $prodi ? $prodi->KODE_PRODI : ($user['kode_prodi'] ?? '');
                $ajuan->NAMA_PRODI = $prodi ? $prodi->NAMA_PRODI : ($user['nama_prodi'] ?? '');
                $ajuan->TGL_CREATE = date('Y-m-d');
                $ajuan->TGL_UPDATE = date('Y-m-d');
                $ajuan->ID_USER_CREATE = $user['id'];
                $ajuan->NAMA_USER_CREATE = $user['nama_lengkap'];
                $ajuan->THN_CREATE = date('Y');
                $ajuan->save();
                \Log::info('Created ajuan record for tahap', ['id_judul' => $idJudul, 'tahapan' => $tahapan]);
            }
        }

        // Simpan status kelengkapan checkbox
        if ($request->has('kelengkapan')) {
            foreach ($request->kelengkapan as $idSyarat => $status) {
                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->TAHAPAN_SIDANG : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapSyarat)
                    ->where('ID_SYARAT_SIDANG', $idSyarat)
                    ->first();

                if ($cek) {
                    $cek->STATUS_LENGKAP = $status;
                    $cek->TGL_UPDATE = date('Y-m-d');
                    $cek->save();
                    \Log::info('Updated kelengkapan in saveAll', ['id_judul' => $idJudul, 'tahapan' => $tahapSyarat, 'id_syarat' => $idSyarat, 'status' => $status]);
                } else {
                    $newCek = new \App\Models\TCekPersyaratan();
                    $newCek->ID_JUDUL = $idJudul;
                    $newCek->TAHAPAN_SIDANG = $tahapSyarat;
                    $newCek->ID_SYARAT_SIDANG = $idSyarat;
                    $newCek->PERSYARATAN = $syarat ? $syarat->NAMA_PERSYARATAN : ('Persyaratan #' . $idSyarat);
                    $newCek->STATUS_LENGKAP = $status;
                    $newCek->TGL_BUAT = date('Y-m-d');
                    $newCek->save();
                    \Log::info('Inserted kelengkapan in saveAll', ['id_judul' => $idJudul, 'tahapan' => $tahapSyarat, 'id_syarat' => $idSyarat, 'status' => $status]);
                }
            }
        } else {
            \Log::warning('No kelengkapan data in request', ['request' => $request->all()]);
        }

        // Simpan file upload
        if ($request->hasFile('files')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);

            foreach ($request->file('files') as $idSyarat => $file) {
                $mimeType = $finfo->file($file->getRealPath());
                if ($mimeType !== 'application/pdf') {
                    return response()->json(['success' => false, 'message' => 'Hanya file PDF yang diizinkan'], 422);
                }
                if ($file->getSize() > 2 * 1024 * 1024) {
                    return response()->json(['success' => false, 'message' => 'Maksimal ukuran file 2 MB'], 422);
                }

                $filename = time() . '_' . bin2hex(random_bytes(16)) . '.pdf';
                $path = $file->storeAs('uploads/persyaratan', $filename, 'public');
                $linkFile = '/storage/' . $path;

                $syarat = \App\Models\TSyaratSidang::find($idSyarat);
                $tahapSyarat = $syarat ? $syarat->TAHAPAN_SIDANG : $tahapan;

                $cek = \App\Models\TCekPersyaratan::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapSyarat)
                    ->where('ID_SYARAT_SIDANG', $idSyarat)
                    ->first();

                if ($cek) {
                    if ($cek->LINK_FILE) {
                        $oldPath = str_replace('/storage/', '', $cek->LINK_FILE);
                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $cek->LINK_FILE = $linkFile;
                    // JANGAN set STATUS_LENGKAP otomatis, biarkan sesuai dengan checkbox user
                    // $cek->STATUS_LENGKAP = 'y';
                    $cek->TGL_UPDATE = date('Y-m-d');
                    $cek->save();
                } else {
                    // Get prodi ID from judul's user for filtering syarat
                    $prodiIdForSave = null;
                    $judulForSave = \App\Models\TJudul::find($idJudul);
                    if ($judulForSave && $judulForSave->user) {
                        $prodiForSave = \App\Models\TProdi::where('kode_prodi', $judulForSave->user->KODE_PRODI)->first();
                        $prodiIdForSave = $prodiForSave?->id;
                    }

                    $allSyarat = \App\Models\TSyaratSidang::where('TAHAPAN_SIDANG', $tahapSyarat)
                        ->where('STATUS_AKTIF', 'AKTIF');
                    if ($prodiIdForSave) {
                        $allSyarat->where('ID_PRODI', $prodiIdForSave);
                    }
                    $allSyarat = $allSyarat->get();

                    foreach ($allSyarat as $s) {
                        $newCek = new \App\Models\TCekPersyaratan();
                        $newCek->ID_JUDUL = $idJudul;
                        $newCek->TAHAPAN_SIDANG = $tahapSyarat;
                        $newCek->ID_SYARAT_SIDANG = $s->id;
                        $newCek->PERSYARATAN = $s->NAMA_PERSYARATAN;
                        // JANGAN set STATUS_LENGKAP otomatis jadi 'y' hanya karena upload file
                        // Status lengkap hanya diset sesuai checkbox user atau default 't'
                        $newCek->STATUS_LENGKAP = 't';
                        if ($s->id == $idSyarat) {
                            $newCek->LINK_FILE = $linkFile;
                        }
                        $newCek->TGL_BUAT = date('Y-m-d');
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
            'status_tim_sidang' => 'required',
            'urutan' => 'nullable',
            'file_penelaah' => 'nullable|file|mimes:pdf',
        ]);

        // External penguji (manual input)
        $manualNama = $request->input('manual_nama');
        $manualNip = $request->input('manual_nip');

        if ($manualNama) {
            $timNama = $manualNama;
            $timNip = $manualNip ?: '';
            $idUserPenilai = null;
        } else {
            $request->validate([
                'id_user_penilai' => 'required',
            ]);
            $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
            if (!$userPenilai) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }
            $timNama = $userPenilai->NAMA_LENGKAP;
            $timNip = $userPenilai->NIP_NIM;
            $idUserPenilai = $request->id_user_penilai;
        }

        // Auto-calculate urutan if not provided
        $urutan = $request->urutan;
        if (!$urutan) {
            $maxUrutan = \App\Models\TTimSidang::where('ID_JUDUL', $request->id_judul)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                ->max('URUTAN');
            $urutan = ($maxUrutan ?: 0) + 1;
        }

        $timSidang = new \App\Models\TTimSidang();
        $timSidang->ID_JUDUL = $request->id_judul;
        $timSidang->TAHAPAN_SIDANG = $request->tahapan_sidang;
        $timSidang->ID_USER_PENILAI = $idUserPenilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $timNip;
        $timSidang->NAMA = $timNama;
        $timSidang->URUTAN = $urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_CREATE = date('Y-m-d');
        $timSidang->TGL_UPDATE = date('Y-m-d');

        // Handle penelaah file upload (stored per tim row in t_tim_sidang)
        if ($request->hasFile('file_penelaah')) {
            $file = $request->file('file_penelaah');
            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $path = 'penelaah/' . $filename;
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            $timSidang->FILE_PENELAAH = '/storage/' . $path;
        }

        $timSidang->save();

        return response()->json([
            'success' => true,
            'message' => 'Tim Pembimbing berhasil ditambahkan',
            'tim' => [
                'id' => $timSidang->id,
                'nip' => $timSidang->NIP,
                'nama' => $timSidang->NAMA,
                'status_tim_sidang' => $timSidang->STATUS_TIM_SIDANG,
                'urutan' => $timSidang->URUTAN,
                'id_sk' => $timSidang->ID_SK,
                'file_penelaah' => $timSidang->FILE_PENELAAH,
            ],
        ]);
    }

    public function updateTimSidang(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'status_tim_sidang' => 'required',
            'urutan' => 'nullable',
            'file_penelaah' => 'nullable|file|mimes:pdf',
        ]);

        $timSidang = \App\Models\TTimSidang::find($id);
        if (!$timSidang) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // External penguji (manual input)
        $manualNama = $request->input('manual_nama');
        $manualNip = $request->input('manual_nip');

        if ($manualNama) {
            $timNama = $manualNama;
            $timNip = $manualNip ?: '';
            $idUserPenilai = null;
        } else {
            $request->validate([
                'id_user_penilai' => 'required',
            ]);
            $userPenilai = \App\Models\TUser::find($request->id_user_penilai);
            if (!$userPenilai) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
            }
            $timNama = $userPenilai->NAMA_LENGKAP;
            $timNip = $userPenilai->NIP_NIM;
            $idUserPenilai = $request->id_user_penilai;
        }

        $urutan = $request->urutan;
        if (!$urutan) {
            $maxUrutan = \App\Models\TTimSidang::where('ID_JUDUL', $timSidang->ID_JUDUL)
                ->where('TAHAPAN_SIDANG', $timSidang->TAHAPAN_SIDANG)
                ->max('URUTAN');
            $urutan = ($maxUrutan ?: 0) + 1;
        }

        $timSidang->ID_USER_PENILAI = $idUserPenilai;
        $timSidang->STATUS_TIM_SIDANG = $request->status_tim_sidang;
        $timSidang->NIP = $timNip;
        $timSidang->NAMA = $timNama;
        $timSidang->URUTAN = $urutan;
        $timSidang->ID_SK = $request->id_sk;
        $timSidang->TGL_UPDATE = date('Y-m-d');

        // Handle penelaah file upload (stored per tim row in t_tim_sidang)
        if ($request->hasFile('file_penelaah')) {
            $file = $request->file('file_penelaah');
            $filename = time() . '_' . bin2hex(random_bytes(16)) . '.' . $file->getClientOriginalExtension();
            $path = 'penelaah/' . $filename;
            \Illuminate\Support\Facades\Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            $timSidang->FILE_PENELAAH = '/storage/' . $path;
        }

        $timSidang->save();

        return response()->json([
            'success' => true,
            'message' => 'Tim Pembimbing berhasil diupdate',
            'tim' => [
                'id' => $timSidang->id,
                'nip' => $timSidang->NIP,
                'nama' => $timSidang->NAMA,
                'status_tim_sidang' => $timSidang->STATUS_TIM_SIDANG,
                'urutan' => $timSidang->URUTAN,
                'id_sk' => $timSidang->ID_SK,
                'file_penelaah' => $timSidang->FILE_PENELAAH,
            ],
        ]);
    }

    public function deleteTimSidang($id)
    {
        if ((session('auth_user.role') ?? '') !== 'TU Prodi') {
            abort(403, 'Hanya TU Prodi yang dapat menghapus tim sidang.');
        }

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
                            ->from('t_tim_sidang')
                            ->whereColumn('t_tim_sidang.id_judul', 't_ajuan_sidang.id_judul')
                            ->whereColumn('t_tim_sidang.tahapan_sidang', 't_ajuan_sidang.tahapan_sidang')
                            ->whereIn('t_tim_sidang.id_user_penilai', $penilaiIds);
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
        // If id_ajuan provided, update that specific record
        if ($request->filled('id_ajuan')) {
            $ajuan = TAjuanSidang::find($request->id_ajuan);
            if (!$ajuan) {
                return response()->json(['success' => false, 'message' => 'Data jadwal tidak ditemukan'], 404);
            }
        } else {
            // If existing record has 'tidak lulus', create a new record (keep old for audit)
            $ajuan = TAjuanSidang::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->latest('id')
                ->first();

            $isTidakLulus = $ajuan && ($ajuan->STATUS_LULUS === 'tidak lulus');

            if (!$ajuan || $isTidakLulus) {
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
        }

        // Update jadwal fields
        $ajuan->TGL_SIDANG = $tglSidang;
        $ajuan->WAKTU_SIDANG = $waktuSidang;

        if ($request->filled('waktu_selesai')) {
            $ajuan->WAKTU_SELESAI = $request->waktu_selesai;
        }
        if ($request->filled('jenis_sidang')) {
            $ajuan->JENIS_SIDANG = $request->jenis_sidang;
        }

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
        if ($request->filled('tgl_hasil_penelahan')) {
            $ajuan->TGL_HASIL_PENELAHAN = $request->tgl_hasil_penelahan;
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
            $ajuan->STATUS_AJUKAN_PRODI = 'y';
            $ajuan->STATUS_AJUKAN_MHS = 'y';
            $ajuan->STATUS_LULUS = 'diajukan';
        }

        if ($request->is_ajukan_kpps) {
            $ajuan->STATUS_AJUKAN_KPPS = 'y';
            $ajuan->TGL_AJUKAN_KPPS = now()->toDateString();
            $ajuan->STATUS_AJUKAN_PRODI = 'y';
            $ajuan->STATUS_AJUKAN_MHS = 'y';
            $ajuan->STATUS_SUBMIT = 'y';
            $ajuan->STATUS_LULUS = 'diajukan';
        }

        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        if ($request->is_ajukan_fs) {
            return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke FS']);
        }

        if ($request->is_ajukan_kpps) {
            return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke KPPS']);
        }

        return response()->json(['success' => true, 'message' => 'Jadwal sidang berhasil disimpan']);
    }

    public function deleteJadwal(\Illuminate\Http\Request $request, $id)
    {
        if ((session('auth_user.role') ?? '') !== 'TU Prodi') {
            abort(403, 'Hanya TU Prodi yang dapat menghapus jadwal sidang.');
        }

        $ajuan = TAjuanSidang::find((int) $id);

        if (!$ajuan) {
            return response()->json(['success' => false, 'message' => 'Data jadwal tidak ditemukan'], 404);
        }

        TPenilaian::where('ID_AJUAN', $ajuan->id)->delete();

        $ajuan->delete();

        return response()->json(['success' => true, 'message' => 'Jadwal sidang beserta penilaian terkait berhasil dihapus.']);
    }

    public function storeJudul(\Illuminate\Http\Request $request)
    {
        $user = session('auth_user');

        $request->validate([
            'judul' => 'required|string|max:500',
            'abstrak' => 'required|string|max:1000',
        ]);

        $judul = TJudul::create([
            'JUDUL' => $request->judul,
            'ABSTRAK' => $request->abstrak,
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

    public function ajukanProdi(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required|string',
        ]);

        $user = session('auth_user');

        $ajuan = TAjuanSidang::where('ID_JUDUL', $request->id_judul)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->latest('id')
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Data jadwal tidak ditemukan'], 404);
        }

        $ajuan->STATUS_AJUKAN_MHS = 'y';
        if ($ajuan->STATUS_LULUS === 'tidak lulus') {
            $ajuan->STATUS_LULUS = null;
        }
        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        return response()->json(['success' => true, 'message' => 'Berhasil diajukan ke Program Studi']);
    }
}