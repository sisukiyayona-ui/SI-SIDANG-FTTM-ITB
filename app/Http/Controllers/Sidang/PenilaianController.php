<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TAjuanSidang;
use App\Models\TPenilaian;
use App\Models\TPointPenilaian;
use App\Models\TTimSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    private const STATUS_LULUS_ALLOWED = [
        'lulus',
        'tidak lulus',
        'Layak tanpa perbaikan',
        'Layak dengan perbaikan minor tanpa harus dibaca kembali',
        'Layak dengan perbaikan minor dan perbaikan harus dibaca kembali',
        'Layak dengan perbaikan major (substansial)',
        'Tidak layak',
        'Lulus Sidang Magister',
        'Tidak Lulus Sidang Magister',
        'Mengulangi Sidang Magister',
    ];

    private const STATUS_LULUS_S2 = [
        'Lulus Sidang Magister',
        'Tidak Lulus Sidang Magister',
    ];

    public function storeTuProdi(Request $request)
    {
        $user = session('auth_user');

        if (($user['role'] ?? '') !== 'TU Prodi') {
            return response()->json(['error' => 'Hanya TU Prodi yang dapat mengisi input ini'], 403);
        }

        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'ip' => 'nullable|numeric',
            'jml_jurnal_q1' => 'nullable|integer|min:0',
            'jml_jurnal_bereputasi1' => 'nullable|integer|min:0',
            'jml_jurnal_bereputasi2' => 'nullable|integer|min:0',
            'rekomendasi_yudisium' => 'nullable|string|max:250',
        ]);

        $ajuan = TAjuanSidang::where('ID_JUDUL', $request->id_judul)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->latest('id')
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan. Simpan jadwal terlebih dahulu.'], 404);
        }

        if ($this->strataAjuan($ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG) === 'S2') {
            return response()->json(['error' => 'TU Prodi hanya dapat mengoreksi status kelulusan S2 setelah nilai dikunci Ketua Sidang.'], 403);
        }

        $ajuan->IP = ($request->filled('ip')) ? $request->input('ip') : null;
        $ajuan->JML_JURNAL_Q1 = ($request->filled('jml_jurnal_q1')) ? $request->input('jml_jurnal_q1') : null;
        $ajuan->JML_JURNAL_BEREPUTASI1 = ($request->filled('jml_jurnal_bereputasi1')) ? $request->input('jml_jurnal_bereputasi1') : null;
        $ajuan->JML_JURNAL_BEREPUTASI2 = ($request->filled('jml_jurnal_bereputasi2')) ? $request->input('jml_jurnal_bereputasi2') : null;
        $ajuan->REKOMENDASI_YUDISIUM = ($request->filled('rekomendasi_yudisium')) ? $request->input('rekomendasi_yudisium') : null;
        $ajuan->TGL_UPDATE = now();
        $ajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Input TU Prodi berhasil disimpan',
        ]);
    }

    public function store(Request $request)
    {
        $user = session('auth_user');
        
        // Allowed status lulus values based on tahapan
        $allowedStatusLulus = self::STATUS_LULUS_ALLOWED;
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'id_tim_sidang' => 'required',
            'penilaian' => 'required|array',
            'penilaian.*.id_penilaian' => 'required',
            'penilaian.*.nilai' => 'nullable|numeric|min:1|max:5',
            'status_lulus' => 'nullable|in:' . implode(',', $allowedStatusLulus),
        ], [
            'penilaian.*.nilai.min' => 'Nilai minimal 1.',
            'penilaian.*.nilai.max' => 'Nilai maksimal 5.',
            'penilaian.*.nilai.numeric' => 'Nilai harus berupa angka.',
        ], [
            'id_judul' => 'ID Judul',
            'tahapan_sidang' => 'Tahapan Sidang',
            'id_tim_sidang' => 'Tim Sidang',
            'penilaian' => 'Penilaian',
            'penilaian.*.id_penilaian' => 'Parameter Penilaian',
            'penilaian.*.nilai' => 'Nilai',
            'status_lulus' => 'Status Kelulusan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan. ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $idJudul = $request->id_judul;
        $tahapanSidang = $request->tahapan_sidang;
        $idTimSidang = $request->id_tim_sidang;

        if ($this->strataAjuan($idJudul, $tahapanSidang) === 'S2') {
            return $this->storeS2($request, $user);
        }

        // Get ajuan sidang data
        $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapanSidang)
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
        }

        $fsBlock = $this->fsViewOnlyS2($user, $idJudul, $tahapanSidang);
        if ($fsBlock) {
            return response()->json(['success' => false, 'error' => $fsBlock], 403);
        }

        $gate = $this->kppsGateForRole($user, $idJudul, $tahapanSidang);
        if ($gate) {
            return response()->json([
                'success' => false,
                'error' => $gate,
            ], 403);
        }

        // Nilai yang sudah dikunci hanya boleh diubah oleh TU Prodi/Admin/FS.
        // Setelah Ketua Sidang mengunci, Pembimbing/Penguji tidak bisa menyimpan lagi.
        if (! in_array($user['role'] ?? '', ['TU Prodi', 'Admin', 'FS'], true)
            && \App\Services\PenilaianSelesai::terkunci($ajuan)) {
            return response()->json([
                'success' => false,
                'error' => 'Nilai sudah dikunci. Hubungi Ketua Sidang atau TU Prodi untuk membukanya kembali.',
            ], 403);
        }

        // Get tim sidang data
        $timSidang = TTimSidang::find($idTimSidang);
        if (!$timSidang) {
            return response()->json(['error' => 'Data tim sidang tidak ditemukan'], 404);
        }

        // Ketua Sidang tidak menginput nilai (hanya status lulus + kunci nilai).
        if (strtolower(trim((string) $timSidang->STATUS_TIM_SIDANG)) === 'ketua sidang') {
            return response()->json([
                'success' => false,
                'error' => 'Ketua Sidang tidak dapat menginput nilai.',
            ], 403);
        }

        // Allow TU Prodi/Admin to save on behalf of penilai
        $isAdmin = in_array($user['role'] ?? '', ['TU Prodi', 'Admin', 'FS']);
        if ($timSidang->ID_USER_PENILAI != $user['id'] && !$isAdmin) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk memberikan penilaian ini'], 403);
        }

        // Determine actual penilai identity
        $penilaiUserId = $timSidang->ID_USER_PENILAI;
        $penilaiUser = \App\Models\TUser::find($penilaiUserId);

        // Save each penilaian
        foreach ($request->penilaian as $item) {
            if (empty($item['nilai']) && empty($item['catatan'])) {
                continue;
            }
            $pointPenilaian = TPointPenilaian::find($item['id_penilaian']);
            if (!$pointPenilaian) {
                continue;
            }

            TPenilaian::updateOrCreate(
                [
                    'ID_AJUAN' => $ajuan->id,
                    'ID_JUDUL' => $idJudul,
                    'TAHAPAN_SIDANG' => $tahapanSidang,
                    'ID_TIM_SIDANG' => $idTimSidang,
                    'ID_USER_PENILAI' => $penilaiUserId,
                    'ID_PENILAIAN' => $item['id_penilaian'],
                ],
                [
                    'JUDUL' => $ajuan->Judul,
                    'NIM' => $ajuan->Nim,
                    'NAMA_MHS' => $ajuan->nama_mhs,
                    'STATUS_TIM_SIDANG' => $timSidang->STATUS_TIM_SIDANG,
                    'NIP' => $penilaiUser->NIP_NIM ?? $user['nip_nim'],
                    'NAMA' => $penilaiUser->NAMA_LENGKAP ?? $user['nama_lengkap'],
                    'NAMA_PENILAIAN' => $pointPenilaian->penilaian,
                    'NILAI' => $item['nilai'] !== '' ? $item['nilai'] : null,
                    'CATATAN' => $item['catatan'] ?? null,
                    'NO_FORM' => $pointPenilaian->no_form,
                    'STATUS_SUBMIT' => 't',
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                    'ID_USER_CREATE' => $user['id'],
                    'NAMA_USER_CREATE' => $user['nama_lengkap'],
                ]
            );
        }

        // Save status_lulus to t_penilaian only (not t_ajuan_sidang)
        if ($request->has('status_lulus') && $request->input('status_lulus') !== '') {
            TPenilaian::where('ID_JUDUL', $idJudul)
                ->where('TAHAPAN_SIDANG', $tahapanSidang)
                ->where('ID_TIM_SIDANG', $idTimSidang)
                ->update(['STATUS_LULUS' => $request->input('status_lulus')]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penilaian berhasil disimpan',
            'status_lulus_updated' => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = session('auth_user');
        
        $request->validate([
            'nilai' => 'required|numeric|min:1|max:5',
            'catatan' => 'nullable|string',
        ]);

        $penilaian = TPenilaian::find($id);
        if (!$penilaian) {
            return response()->json(['error' => 'Data penilaian tidak ditemukan'], 404);
        }

        if ($this->strataAjuan($penilaian->ID_JUDUL, $penilaian->TAHAPAN_SIDANG) === 'S2') {
            return $this->updateS2($request, $user, $penilaian);
        }

        // Validate that user owns this penilaian
        if ($penilaian->ID_USER_PENILAI != $user['id']) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk mengubah penilaian ini'], 403);
        }

        $gate = $this->kppsGateForRole($user, $penilaian->ID_JUDUL, $penilaian->TAHAPAN_SIDANG);
        if ($gate) {
            return response()->json([
                'success' => false,
                'error' => $gate,
            ], 403);
        }

        $penilaian->update([
            'NILAI' => $request->nilai,
            'CATATAN' => $request->catatan,
            'TGL_UPDATE' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Penilaian berhasil diperbarui']);
    }

    public function updateStatusLulus(Request $request, $id)
    {
        $user = session('auth_user');
        
        if ($this->strataAjuan($id, $request->tahapan_sidang) === 'S2') {
            return $this->updateStatusLulusS2($request, $user, $id);
        }

        // Allowed status lulus values
        $allowedStatusLulus = self::STATUS_LULUS_ALLOWED;
        
        $request->validate([
            'status_lulus' => 'required|in:' . implode(',', $allowedStatusLulus),
        ]);

        $ajuan = TAjuanSidang::where('ID_JUDUL', $id)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->first();
        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
        }

        $fsBlock = $this->fsViewOnlyS2($user, $ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG);
        if ($fsBlock) {
            return response()->json(['success' => false, 'error' => $fsBlock], 403);
        }

        // Check if user is Pembimbing (any type), Ketua Sidang, or TU Prodi
        $isPembimbing = TTimSidang::where('ID_JUDUL', $ajuan->ID_JUDUL)
            ->where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)
            ->where('ID_USER_PENILAI', $user['id'])
            ->where(function($query) {
                $query->where('STATUS_TIM_SIDANG', 'Ketua Pembimbing')
                    ->orWhere('STATUS_TIM_SIDANG', 'Pembimbing')
                    ->orWhere('STATUS_TIM_SIDANG', 'Pembimbing II')
                    ->orWhere('STATUS_TIM_SIDANG', 'like', '%Pembimbing%');
            })
            ->first();

        $isTUProdi = in_array($user['role'] ?? '', ['TU Prodi', 'Admin']);
        $isKetuaSidang = $this->isKetuaSidang($user, $ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG);

        if (!$isPembimbing && !$isTUProdi && !$isKetuaSidang) {
            return response()->json(['error' => 'Hanya Pembimbing, Ketua Sidang atau TU Prodi yang dapat mengubah status kelulusan'], 403);
        }

        DB::table('t_ajuan_sidang')
            ->where('ID_JUDUL', $id)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->update([
                'STATUS_LULUS' => $request->input('status_lulus'),
                'TGL_UPDATE' => now(),
            ]);

        // Status kelulusan adalah keputusan bersama seluruh tim, jadi seluruh
        // baris nilai penilai pada judul + tahapan ini ikut diisi.
        DB::table('t_penilaian')
            ->where('ID_JUDUL', $id)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->update([
                'STATUS_LULUS' => $request->input('status_lulus'),
                'TGL_UPDATE' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Status kelulusan berhasil diperbarui']);
    }

    public function lockNilai(Request $request, $id)
    {
        // Keep S2 validation outside the legacy catch-all (validation must return 422).
        if ($this->strataAjuan($id, $request->tahapan_sidang) === 'S2') {
            return $this->lockNilaiS2($request, session('auth_user'), $id);
        }

        try {
            $user = session('auth_user');

            $request->validate([
                'nilai_terkunci' => 'required|in:y,t',
                'tahapan_sidang' => 'required|string',
                'id_tim_sidang' => 'nullable|integer',
                'status_lulus' => 'nullable|in:' . implode(',', self::STATUS_LULUS_ALLOWED),
            ]);

            $idTimSidang = $request->filled('id_tim_sidang') ? $request->id_tim_sidang : null;

            \Log::info('Lock Nilai Request', [
                'id_judul' => $id,
                'nilai_terkunci' => $request->nilai_terkunci,
                'tahapan_sidang' => $request->tahapan_sidang,
                'id_tim_sidang' => $request->id_tim_sidang,
                'status_lulus' => $request->status_lulus,
                'user' => $user
            ]);

            $statusLulus = $request->filled('status_lulus') ? $request->status_lulus : null;

            $fsBlock = $this->fsViewOnlyS2($user, $id, $request->tahapan_sidang);
            if ($fsBlock) {
                return response()->json(['success' => false, 'error' => $fsBlock], 403);
            }

            // Gate approve KPPS prodi: Pembimbing/Penguji tidak boleh mengunci
            // nilai sebelum seluruh KPPS AKTIF prodi approve ajuan. TU Prodi/Admin/FS tidak terpengaruh.
            $gate = $this->kppsGateForRole($user, $id, $request->tahapan_sidang);
            if ($gate) {
                return response()->json([
                    'success' => false,
                    'error' => $gate,
                ], 403);
            }

            // 0. Hak mengunci. Hanya Ketua Sidang (role 'Ketua Sidang' atau tercatat
            //    sebagai Ketua Sidang pada tim sidang judul + tahapan ini) dan
            //    TU Prodi/Admin/FS. Pembimbing/Penguji hanya mengisi nilai.
            $isKetuaSidangRequest = $this->isKetuaSidang($user, $id, $request->tahapan_sidang);
            $isPengelola = in_array($user['role'] ?? null, ['TU Prodi', 'Admin'], true);

            if (! $isKetuaSidangRequest && ! $isPengelola) {
                \Log::info('Lock Nilai ditolak: bukan Ketua Sidang/TU Prodi', [
                    'id_judul' => $id,
                    'tahapan_sidang' => $request->tahapan_sidang,
                    'user' => $user,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'Hanya Ketua Sidang atau TU Prodi yang dapat mengunci nilai.',
                ], 403);
            }

            // 1. Tentukan peran penilai yang mengunci. Hak kunci nilai milik
            //    Ketua Sidang (dulu Ketua Pembimbing).
            $timSidang = $idTimSidang ? TTimSidang::find($idTimSidang) : null;
            $statusTimTerpilih = strtolower(trim((string) (
                $timSidang?->STATUS_TIM_SIDANG
                ?? $timSidang?->status_tim_sidang
                ?? $timSidang?->keterangan
                ?? ''
            )));
            $isKetuaSidangDipilih = str_contains($statusTimTerpilih, 'ketua sidang');

            // Ketua Sidang (atau TU Prodi yang memilih Ketua Sidang) wajib pilih
            // Status Kelulusan — mereka tidak mengisi parameter nilai.
            $kunciSebagaiKetua = $isKetuaSidangRequest || (
                ($user['role'] ?? null) === 'TU Prodi' && $isKetuaSidangDipilih
            );
            if ($kunciSebagaiKetua && $request->nilai_terkunci === 'y' && ! $statusLulus) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pilih Status Kelulusan terlebih dahulu sebelum mengunci nilai.',
                ], 422);
            }

            // 0b. Guard kelengkapan tim: Ketua Sidang baru boleh mengunci setelah
            //     SELURUH Pembimbing/Penguji mengisi nilainya (baris Ketua Sidang
            //     tidak dihitung, lihat KelengkapanNilaiSidang), begitu juga TU Prodi.
            $perluGate = ($user['role'] ?? null) === 'TU Prodi' || $isKetuaSidangRequest;

            if ($perluGate && $request->nilai_terkunci === 'y') {
                // Pakai KODE_PRODI mahasiswa (bukan ajuan yang bisa salah kode)
                $kodeProdi = DB::table('t_judul as j')
                    ->join('t_user as u', 'j.ID_USER_MHS', '=', 'u.id')
                    ->where('j.id', $id)
                    ->value('u.KODE_PRODI');
                if (! $kodeProdi) {
                    $kodeProdi = DB::table('t_ajuan_sidang')
                        ->where('ID_JUDUL', $id)
                        ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                        ->orderByDesc('id')
                        ->value('KODE_PRODI');
                }

                $kelengkapan = \App\Services\KelengkapanNilaiSidang::cek(
                    $id,
                    $request->tahapan_sidang,
                    $kodeProdi
                );

                if (! $kelengkapan['lengkap']) {
                    \Log::info('Lock Nilai ditolak: nilai tim belum lengkap', [
                        'id_judul' => $id,
                        'tahapan_sidang' => $request->tahapan_sidang,
                        'user' => $user,
                        'kode_prodi' => $kodeProdi,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => \App\Services\KelengkapanNilaiSidang::pesan($kelengkapan),
                    ], 422);
                }
            }

            // 2. Update t_penilaian.
            //    - Ketua Sidang mengunci -> SELURUH penilai pada judul+tahapan ini
            //      ikut terkunci: NILAI_TERKUNCI=1 dan STATUS_LULUS ikut terisi.
            //    - TU Prodi mengunci lewat baris Ketua Sidang ikut mengunci
            //      seluruh penilai; Admin/FS hanya baris penilai yang dipilih.
            $nilaiTerkunciInt = $request->nilai_terkunci === 'y' ? 1 : 0;
            $updatePenilaian = [
                'NILAI_TERKUNCI' => $nilaiTerkunciInt,
                'TGL_UPDATE' => now(),
            ];
            if ($statusLulus) {
                $updatePenilaian['STATUS_LULUS'] = $statusLulus;
            }

            $isTuProdi = ($user['role'] ?? null) === 'TU Prodi';
            $kunciSemuaPenilai = $isTuProdi || $isKetuaSidangRequest;

            $queryPenilaian = DB::table('t_penilaian')
                ->where('ID_JUDUL', $id)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang);

            if (! $kunciSemuaPenilai) {
                if (! $idTimSidang) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Penilai belum dipilih.',
                    ], 422);
                }

                $queryPenilaian->where('ID_TIM_SIDANG', $idTimSidang);
            }

            $jumlahPenilaian = $queryPenilaian->update($updatePenilaian);

            \Log::info('Lock Nilai: Updated t_penilaian', [
                'id_judul' => $id,
                'role' => $user['role'] ?? null,
                'tahapan_sidang' => $request->tahapan_sidang,
                'id_tim_sidang' => $idTimSidang,
                'peran' => $timSidang->STATUS_TIM_SIDANG ?? null,
                'is_ketua_sidang_dipilih' => $isKetuaSidangDipilih,
                'is_ketua_sidang' => $isKetuaSidangRequest,
                'scope' => $kunciSemuaPenilai ? 'semua penilai' : 'hanya penilai sendiri',
                'nilai_terkunci' => $request->nilai_terkunci,
                'status_lulus' => $statusLulus,
                'rows_affected' => $jumlahPenilaian,
            ]);

            // 3. Sync t_ajuan_sidang - ikut terisi setiap kali nilai dikunci
            //    atas nama Ketua Sidang: dia sendiri (role 'Ketua Sidang' atau
            //    tercatat sebagai Ketua Sidang di tim sidang) maupun TU Prodi
            //    yang memilih baris Ketua Sidang di dropdown Penilai.
            //    Penilai lain tidak boleh menyentuh t_ajuan_sidang.
            //    NILAI_TERKUNCI tidak lagi tertahan bila dropdown "Pilih status"
            //    kosong (dulu membuat t_ajuan_sidang tetap NILAI_TERKUNCI='t'
            //    padahal nilai sudah dikunci).
            if ($isKetuaSidangRequest || $isKetuaSidangDipilih || $isTuProdi) {
                $updateAjuan = [
                    'NILAI_TERKUNCI' => $request->nilai_terkunci,
                    'TGL_UPDATE' => now(),
                ];
                $statusLulusFinal = null;

                $statusLulusFinal = $statusLulus ?: DB::table('t_penilaian')
                    ->where('ID_JUDUL', $id)
                    ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                    ->whereNotNull('STATUS_LULUS')
                    ->where('STATUS_LULUS', '<>', '')
                    ->value('STATUS_LULUS');

                if ($statusLulusFinal) {
                    $updateAjuan['STATUS_LULUS'] = $statusLulusFinal;
                }

                $jumlahAjuan = DB::table('t_ajuan_sidang')
                    ->where('ID_JUDUL', $id)
                    ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                    ->update($updateAjuan);

                \Log::info('Lock Nilai: Updated t_ajuan_sidang', [
                    'id_judul' => $id,
                    'role' => $user['role'] ?? null,
                    'tahapan_sidang' => $request->tahapan_sidang,
                    'id_tim_sidang' => $request->id_tim_sidang,
                    'peran' => $timSidang->STATUS_TIM_SIDANG ?? null,
                    'is_ketua_sidang_dipilih' => $isKetuaSidangDipilih,
                    'is_ketua_sidang' => $isKetuaSidangRequest,
                    'nilai_terkunci' => $request->nilai_terkunci,
                    'status_lulus' => $statusLulusFinal,
                    'rows_affected' => $jumlahAjuan,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil dikunci'
            ]);
        } catch (\Exception $e) {
            \Log::error('Lock Nilai Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /** Serialize S2 writes against the latest attempt, including the approval/lock checks. */
    private function withS2Ajuan($user, $idJudul, $tahapan, callable $callback)
    {
        return DB::transaction(function () use ($user, $idJudul, $tahapan, $callback) {
            $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
                ->where('TAHAPAN_SIDANG', $tahapan)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (!$ajuan) {
                return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
            }
            if (($user['role'] ?? '') === 'FS') {
                return response()->json(['error' => 'Role FS hanya dapat melihat data sidang S2 (view-only), tanpa menyimpan.'], 403);
            }
            if (strtolower(trim((string) $ajuan->STATUS_AJUKAN_PRODI)) !== 'y') {
                return response()->json(['error' => 'Penilaian belum bisa dilakukan. Menunggu TU Prodi melakukan Approve jadwal sidang.'], 403);
            }

            return $callback($ajuan);
        });
    }

    private function s2Scores(TAjuanSidang $ajuan)
    {
        return TPenilaian::where('ID_AJUAN', $ajuan->id)
            ->where('ID_JUDUL', $ajuan->ID_JUDUL)
            ->where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG);
    }

    private function s2Tim(TAjuanSidang $ajuan, $idTim)
    {
        return TTimSidang::where('ID_JUDUL', $ajuan->ID_JUDUL)
            ->where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)
            ->where('id', $idTim)
            ->first();
    }

    private function s2Points(TAjuanSidang $ajuan)
    {
        $query = TPointPenilaian::where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)
            ->where('STRATA', 'S2')
            ->where('STATUS_AKTIF', 'AKTIF');

        // Never fall back to parameters belonging to another prodi.
        if ($ajuan->KODE_PRODI) {
            $query->where('KODE_PRODI', $ajuan->KODE_PRODI);
        }
        if ($ajuan->ID_PRODI) {
            $query->where('ID_PRODI', $ajuan->ID_PRODI);
        }
        if (!$ajuan->KODE_PRODI && !$ajuan->ID_PRODI) {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    private function isS2Evaluator(TTimSidang $tim): bool
    {
        $status = strtolower(trim((string) $tim->STATUS_TIM_SIDANG));
        return $status !== 'ketua sidang'
            && (str_contains($status, 'pembimbing') || str_contains($status, 'penguji'));
    }

    private function canScoreS2($user, TAjuanSidang $ajuan, TTimSidang $tim): bool
    {
        $role = $user['role'] ?? '';
        
        // FS is view-only, never scores
        if ($role === 'FS') return false;
        
        // Must be a valid evaluator (Pembimbing/Penguji, not Ketua Sidang)
        if (!$this->isS2Evaluator($tim)) return false;
        
        // TU Prodi/Admin can score for ANY penilai
        if (in_array($role, ['TU Prodi', 'Admin'], true)) {
            return true;
        }
        
        // Pembimbing/Penguji can only score for themselves
        return $tim->ID_USER_PENILAI == ($user['id'] ?? 0)
            && !$this->isKetuaSidang($user, $ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG);
    }

    private function s2ScoresLocked(TAjuanSidang $ajuan): bool
    {
        return \App\Services\PenilaianSelesai::terkunci($ajuan)
            || $this->s2Scores($ajuan)->where('NILAI_TERKUNCI', 1)->exists();
    }

    private function storeS2(Request $request, $user)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'id_judul' => 'required|integer',
            'tahapan_sidang' => 'required|string',
            'id_tim_sidang' => 'required|integer',
            'penilaian.*.id_penilaian' => 'required|integer|distinct',
            'penilaian.*.catatan' => 'nullable|string|max:500',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        return $this->withS2Ajuan($user, $request->id_judul, $request->tahapan_sidang, function ($ajuan) use ($request, $user) {
            $tim = $this->s2Tim($ajuan, $request->id_tim_sidang);
            if (!$tim) {
                return response()->json(['error' => 'Tim sidang tidak sesuai dengan judul dan tahapan ajuan.'], 422);
            }
            if (!$this->canScoreS2($user, $ajuan, $tim)) {
                return response()->json(['error' => 'Hanya Pembimbing/Penguji yang ditugaskan dapat mengisi nilainya sendiri. Ketua Sidang tidak menginput nilai.'], 403);
            }
            $isPengelola = in_array($user['role'] ?? '', ['TU Prodi', 'Admin'], true);
            if (!$isPengelola && $this->s2ScoresLocked($ajuan)) {
                return response()->json(['error' => 'Nilai sudah dikunci. Hanya Ketua Sidang yang ditugaskan dapat membukanya kembali.'], 403);
            }

            $points = $this->s2Points($ajuan)->whereIn('id', array_column($request->penilaian, 'id_penilaian'))->get()->keyBy('id');
            foreach ($request->penilaian as $item) {
                if (!$points->has($item['id_penilaian'])) {
                    return response()->json(['error' => 'Parameter penilaian tidak aktif atau tidak sesuai dengan prodi dan tahapan S2.'], 422);
                }
            }

            $penilai = $tim->userPenilai;
            foreach ($request->penilaian as $item) {
                $nilai = ($item['nilai'] ?? '') !== '' ? ($item['nilai'] ?? null) : null;
                $catatan = $item['catatan'] ?? null;
                if ($nilai === null && trim((string) $catatan) === '') {
                    continue;
                }
                $point = $points->get($item['id_penilaian']);
                TPenilaian::updateOrCreate([
                    'ID_AJUAN' => $ajuan->id,
                    'ID_JUDUL' => $ajuan->ID_JUDUL,
                    'TAHAPAN_SIDANG' => $ajuan->TAHAPAN_SIDANG,
                    'ID_TIM_SIDANG' => $tim->id,
                    'ID_USER_PENILAI' => $tim->ID_USER_PENILAI,
                    'ID_PENILAIAN' => $point->id,
                ], [
                    'JUDUL' => $ajuan->JUDUL,
                    'NIM' => $ajuan->NIM,
                    'NAMA_MHS' => $ajuan->NAMA_MHS,
                    'STATUS_TIM_SIDANG' => $tim->STATUS_TIM_SIDANG,
                    'NIP' => $penilai->NIP_NIM ?? $tim->NIP,
                    'NAMA' => $penilai->NAMA_LENGKAP ?? $tim->NAMA,
                    'NAMA_PENILAIAN' => $point->PENILAIAN,
                    'NILAI' => $nilai,
                    'CATATAN' => $catatan,
                    'NO_FORM' => $point->NO_FORM,
                    'STATUS_SUBMIT' => 't',
                    'TGL_CREATE' => now(),
                    'TGL_UPDATE' => now(),
                    'ID_USER_CREATE' => $user['id'],
                    'NAMA_USER_CREATE' => $user['nama_lengkap'] ?? $tim->NAMA,
                ]);
            }

            // A score submission must never write a draft or final graduation decision.
            return response()->json(['success' => true, 'message' => 'Penilaian berhasil disimpan', 'status_lulus_updated' => false]);
        });
    }

    private function updateS2(Request $request, $user, TPenilaian $penilaian)
    {
        return $this->withS2Ajuan($user, $penilaian->ID_JUDUL, $penilaian->TAHAPAN_SIDANG, function ($ajuan) use ($request, $user, $penilaian) {
            if ($penilaian->ID_AJUAN != $ajuan->id) {
                return response()->json(['error' => 'Penilaian bukan milik ajuan sidang terbaru.'], 422);
            }
            $tim = $this->s2Tim($ajuan, $penilaian->ID_TIM_SIDANG);
            if (!$tim) {
                return response()->json(['error' => 'Tim sidang tidak sesuai dengan judul dan tahapan ajuan.'], 422);
            }
            $role = $user['role'] ?? '';
            if (!in_array($role, ['TU Prodi', 'Admin'], true) && $penilaian->ID_USER_PENILAI != ($user['id'] ?? 0)) {
                return response()->json(['error' => 'Hanya Pembimbing/Penguji yang ditugaskan dapat mengubah nilainya sendiri.'], 403);
            }
            if (!$this->canScoreS2($user, $ajuan, $tim)) {
                return response()->json(['error' => 'Hanya Pembimbing/Penguji yang ditugaskan dapat mengubah nilainya sendiri.'], 403);
            }
            $isPengelola = in_array($user['role'] ?? '', ['TU Prodi', 'Admin'], true);
            if (!$isPengelola && $this->s2ScoresLocked($ajuan)) {
                return response()->json(['error' => 'Nilai sudah dikunci. Hanya Ketua Sidang yang ditugaskan dapat membukanya kembali.'], 403);
            }
            if (!$this->s2Points($ajuan)->where('id', $penilaian->ID_PENILAIAN)->exists()) {
                return response()->json(['error' => 'Parameter penilaian tidak aktif atau tidak sesuai dengan prodi dan tahapan S2.'], 422);
            }
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), ['catatan' => 'nullable|string|max:500']);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
            }

            $this->s2Scores($ajuan)->where('id', $penilaian->id)->update([
                'NILAI' => $request->nilai,
                'CATATAN' => $request->catatan,
                'TGL_UPDATE' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Penilaian berhasil diperbarui']);
        });
    }

    private function updateStatusLulusS2(Request $request, $user, $idJudul)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'tahapan_sidang' => 'required|string',
            'id_tim_sidang' => 'nullable|integer',
            'status_lulus' => 'required|in:' . implode(',', self::STATUS_LULUS_S2),
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        return $this->withS2Ajuan($user, $idJudul, $request->tahapan_sidang, function ($ajuan) use ($request, $user) {
            $locked = \App\Services\PenilaianSelesai::terkunci($ajuan);
            $isPengelola = in_array($user['role'] ?? '', ['TU Prodi', 'Admin'], true);
            if ($isPengelola ? !$locked : ($locked || !$this->isKetuaSidang($user, $ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG))) {
                return response()->json(['error' => 'Sebelum dikunci, hanya Ketua Sidang yang ditugaskan dapat menyimpan keputusan. Setelah dikunci, hanya TU Prodi/Admin dapat mengoreksi status kelulusan.'], 403);
            }
            if ($request->filled('id_tim_sidang') && !$this->s2Tim($ajuan, $request->id_tim_sidang)) {
                return response()->json(['error' => 'Tim sidang tidak sesuai dengan judul dan tahapan ajuan.'], 422);
            }
            if (!$this->s2Scores($ajuan)->exists()) {
                return response()->json(['error' => 'Belum ada penilaian pada ajuan terbaru untuk menyimpan keputusan.'], 422);
            }

            $this->s2Scores($ajuan)->update(['STATUS_LULUS' => $request->status_lulus, 'TGL_UPDATE' => now()]);
            if ($locked) {
                $ajuan->STATUS_LULUS = $request->status_lulus;
                $ajuan->TGL_UPDATE = now();
                $ajuan->save();
            }

            return response()->json(['success' => true, 'message' => $locked ? 'Status kelulusan berhasil diperbarui' : 'Draf keputusan berhasil disimpan; belum menjadi kelulusan final.']);
        });
    }

    /** Every assigned evaluator must complete every active parameter of this prodi/stage. */
    private function s2CompletenessError(TAjuanSidang $ajuan): ?string
    {
        $tim = TTimSidang::where('ID_JUDUL', $ajuan->ID_JUDUL)
            ->where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)->get();
        $chairIds = $tim->filter(fn ($member) => strtolower(trim((string) $member->STATUS_TIM_SIDANG)) === 'ketua sidang')
            ->pluck('ID_USER_PENILAI');
        $evaluators = $tim->filter(fn ($member) => $this->isS2Evaluator($member) && !$chairIds->contains($member->ID_USER_PENILAI));
        $points = $this->s2Points($ajuan)->get();
        if ($evaluators->isEmpty() || $points->isEmpty()) {
            return 'Tim Pembimbing/Penguji atau parameter penilaian S2 belum lengkap.';
        }

        $scores = $this->s2Scores($ajuan)->get()->groupBy('ID_TIM_SIDANG');
        foreach ($evaluators as $evaluator) {
            $own = $scores->get($evaluator->id, collect())->where('ID_USER_PENILAI', $evaluator->ID_USER_PENILAI)->keyBy('ID_PENILAIAN');
            foreach ($points as $point) {
                $score = $own->get($point->id);
                $complete = strtolower(trim((string) $point->STATUS_CATATAN)) === 'y'
                    ? trim((string) ($score->CATATAN ?? '')) !== ''
                    : is_numeric($score->NILAI ?? null) && $score->NILAI >= 1 && $score->NILAI <= 5;
                if (!$complete) {
                    return 'Nilai ajuan terbaru belum lengkap: ' . $evaluator->NAMA . ' (' . $evaluator->STATUS_TIM_SIDANG . ').';
                }
            }
        }

        return null;
    }

    private function lockNilaiS2(Request $request, $user, $idJudul)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nilai_terkunci' => 'required|in:y,t',
            'tahapan_sidang' => 'required|string',
            'id_tim_sidang' => 'nullable|integer',
            'status_lulus' => 'required_if:nilai_terkunci,y|nullable|in:' . implode(',', self::STATUS_LULUS_S2),
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
        }

        return $this->withS2Ajuan($user, $idJudul, $request->tahapan_sidang, function ($ajuan) use ($request, $user) {
            $role = $user['role'] ?? '';
            // Allow TU Prodi/Admin to lock/unlock; Ketua Sidang can also lock/unlock
            if (!in_array($role, ['TU Prodi', 'Admin', 'Ketua Sidang'], true) && !$this->isKetuaSidang($user, $ajuan->ID_JUDUL, $ajuan->TAHAPAN_SIDANG)) {
                return response()->json(['error' => 'Hanya TU Prodi, Admin, atau Ketua Sidang yang dapat mengunci atau membuka nilai S2.'], 403);
            }
            if ($request->filled('id_tim_sidang') && !$this->s2Tim($ajuan, $request->id_tim_sidang)) {
                return response()->json(['error' => 'Tim sidang tidak sesuai dengan judul dan tahapan ajuan.'], 422);
            }

            $locking = $request->nilai_terkunci === 'y';
            $isPengelola = in_array($role, ['TU Prodi', 'Admin'], true);
            if ($locking && \App\Services\PenilaianSelesai::terkunci($ajuan) && !$isPengelola) {
                return response()->json(['error' => 'Nilai sudah dikunci. Buka kunci terlebih dahulu sebelum menetapkan keputusan baru.'], 403);
            }
            if ($locking && ($error = $this->s2CompletenessError($ajuan)) && !$isPengelola) {
                return response()->json(['error' => $error, 'message' => $error], 422);
            }

            $values = ['NILAI_TERKUNCI' => $locking ? 1 : 0, 'TGL_UPDATE' => now()];
            if ($locking) {
                $values['STATUS_LULUS'] = $request->status_lulus;
            }
            $this->s2Scores($ajuan)->update($values);
            $ajuan->NILAI_TERKUNCI = $request->nilai_terkunci;
            // Unlocking leaves the score-row decision as a draft, never a final ajuan status.
            $ajuan->STATUS_LULUS = $locking ? $request->status_lulus : null;
            $ajuan->TGL_UPDATE = now();
            $ajuan->save();

            return response()->json(['success' => true, 'message' => $locking ? 'Nilai berhasil dikunci' : 'Kunci nilai berhasil dibuka']);
        });
    }

    /**
     * Gate input/kunci nilai untuk role Pembimbing/Penguji/Ketua Sidang.
     * S2: dibuka setelah TU Prodi approve jadwal (STATUS_AJUKAN_PRODI='y').
     * S3: dibuka setelah semua KPPS AKTIF prodi approve ajuan.
     * Mengembalikan pesan error bila harus diblokir, null bila tidak.
     */
    private function kppsGateForRole($user, $idJudul, $tahapanSidang)
    {
        $role = $user['role'] ?? '';
        $strata = $this->strataAjuan($idJudul, $tahapanSidang);

        if ($strata === 'S2') {
            $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
                ->where('TAHAPAN_SIDANG', $tahapanSidang)
                ->latest('id')
                ->first();

            if (strtolower(trim((string) ($ajuan->STATUS_AJUKAN_PRODI ?? ''))) !== 'y') {
                return 'Penilaian belum bisa dilakukan. Menunggu TU Prodi melakukan Approve jadwal sidang.';
            }

            return null;
        }

        if (!in_array($role, ['Pembimbing', 'Penguji', 'Ketua Sidang'], true) || $strata !== 'S3') {
            return null;
        }

        $gate = \App\Services\KppsApproveGate::cek($idJudul, $tahapanSidang);

        if (empty($gate['blocked'])) {
            return null;
        }

        return \App\Services\KppsApproveGate::pesan($gate);
    }

    /**
     * FS hanya view-only untuk S2: tidak boleh menyimpan nilai, status,
     * maupun mengunci nilai.
     */
    private function fsViewOnlyS2($user, $idJudul, $tahapanSidang): ?string
    {
        if (($user['role'] ?? '') !== 'FS') {
            return null;
        }

        if ($this->strataAjuan($idJudul, $tahapanSidang) === 'S2') {
            return 'Role FS hanya dapat melihat data sidang S2 (view-only), tanpa menyimpan.';
        }

        return null;
    }

    private function strataAjuan($idJudul, $tahapanSidang): string
    {
        $query = TAjuanSidang::where('ID_JUDUL', $idJudul);
        $ajuan = is_scalar($tahapanSidang)
            ? (clone $query)->where('TAHAPAN_SIDANG', $tahapanSidang)->latest('id')->first()
            : null;

        // Invalid/missing stages must still reach S2 validation, not the legacy catch-all.
        if (!$ajuan && (clone $query)->where('STRATA', 'S2')->exists()) {
            return 'S2';
        }

        $strata = strtoupper(trim((string) ($ajuan->STRATA ?? '')));
        if ($strata === '' && $ajuan) {
            // JUDUL is also a column; use the relationship explicitly to avoid reading the title string.
            $strata = strtoupper(trim((string) ($ajuan->judul()->first()?->user?->STRATA ?? '')));
        }

        return $strata !== '' ? $strata : 'S3';
    }

    /**
     * S2 wajib tercatat sebagai Ketua Sidang pada tim judul + tahapan ini.
     * S1/S3 tetap menerima role global 'Ketua Sidang'.
     */
    private function isKetuaSidang($user, $idJudul, $tahapanSidang)
    {
        if (($user['role'] ?? '') === 'Ketua Sidang' && $this->strataAjuan($idJudul, $tahapanSidang) !== 'S2') {
            return true;
        }

        return TTimSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapanSidang)
            ->where('ID_USER_PENILAI', $user['id'] ?? 0)
            ->whereRaw("LOWER(TRIM(STATUS_TIM_SIDANG)) = ?", ['ketua sidang'])
            ->exists();
    }
}
