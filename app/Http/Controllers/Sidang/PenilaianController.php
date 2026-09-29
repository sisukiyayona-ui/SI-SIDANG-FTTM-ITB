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
        $allowedStatusLulus = [
            'lulus',
            'tidak lulus',
            'Layak tanpa perbaikan',
            'Layak dengan perbaikan minor tanpa harus dibaca kembali',
            'Layak dengan perbaikan minor dan perbaikan harus dibaca kembali',
            'Layak dengan perbaikan major (substansial)',
            'Tidak layak'
        ];
        
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

        // Get ajuan sidang data
        $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapanSidang)
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
        }

        // Get tim sidang data
        $timSidang = TTimSidang::find($idTimSidang);
        if (!$timSidang) {
            return response()->json(['error' => 'Data tim sidang tidak ditemukan'], 404);
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

        // Validate that user owns this penilaian
        if ($penilaian->ID_USER_PENILAI != $user['id']) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk mengubah penilaian ini'], 403);
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
        
        // Allowed status lulus values
        $allowedStatusLulus = [
            'lulus',
            'tidak lulus',
            'Layak tanpa perbaikan',
            'Layak dengan perbaikan minor tanpa harus dibaca kembali',
            'Layak dengan perbaikan minor dan perbaikan harus dibaca kembali',
            'Layak dengan perbaikan major (substansial)',
            'Tidak layak'
        ];
        
        $request->validate([
            'status_lulus' => 'required|in:' . implode(',', $allowedStatusLulus),
        ]);

        $ajuan = TAjuanSidang::where('ID_JUDUL', $id)
            ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
            ->first();
        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
        }

        // Check if user is Pembimbing (any type) or TU Prodi
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

        if (!$isPembimbing && !$isTUProdi) {
            return response()->json(['error' => 'Hanya Pembimbing atau TU Prodi yang dapat mengubah status kelulusan'], 403);
        }

        DB::table('t_ajuan_sidang')
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
        try {
            $user = session('auth_user');

            $request->validate([
                'nilai_terkunci' => 'required|in:y,t',
                'tahapan_sidang' => 'required|string',
                'id_tim_sidang' => 'required|string',
            ]);

            \Log::info('Lock Nilai Request', [
                'id_judul' => $id,
                'nilai_terkunci' => $request->nilai_terkunci,
                'tahapan_sidang' => $request->tahapan_sidang,
                'id_tim_sidang' => $request->id_tim_sidang,
                'status_lulus' => $request->status_lulus,
                'user' => $user
            ]);

            $statusLulus = $request->filled('status_lulus') ? $request->status_lulus : null;

            // 0. Guard kelengkapan tim. Berlaku untuk TU Prodi, dan juga untuk
            //    Pembimbing/Penguji yang merupakan Ketua Pembimbing pada judul +
            //    tahapan ini (mereka punya wewenang mengunci semua nilai).
            $isKetuaPembimbingRequest = TTimSidang::where('id', $request->id_tim_sidang)
                ->where('ID_JUDUL', $id)
                ->where('STATUS_TIM_SIDANG', 'LIKE', '%Ketua Pembimbing%')
                ->exists();

            $perluGate = ($user['role'] ?? null) === 'TU Prodi'
                || (in_array($user['role'] ?? null, ['Pembimbing', 'Penguji'], true) && $isKetuaPembimbingRequest);

            if ($perluGate && $request->nilai_terkunci === 'y') {
                $kodeProdi = DB::table('t_ajuan_sidang')
                    ->where('ID_JUDUL', $id)
                    ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                    ->orderByDesc('id')
                    ->value('KODE_PRODI');

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
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => \App\Services\KelengkapanNilaiSidang::pesan($kelengkapan),
                    ], 422);
                }
            }

            // 1. Tentukan peran penilai yang mengunci.
            $timSidang = TTimSidang::find($request->id_tim_sidang);
            $isKetuaPembimbing = $timSidang && (
                $timSidang->STATUS_TIM_SIDANG === 'Ketua Pembimbing' ||
                strpos($timSidang->STATUS_TIM_SIDANG, 'Ketua Pembimbing') !== false
            );

            // Apakah tim pada tahapan ini punya Ketua Pembimbing sama sekali?
            $adaKetuaPembimbing = TTimSidang::where('ID_JUDUL', $id)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                ->where('STATUS_TIM_SIDANG', 'LIKE', '%Ketua Pembimbing%')
                ->exists();

            // 2. Update t_penilaian.
            //    - TU Prodi mengunci (mewakili Ketua Pembimbing terpilih) -> SELURUH
            //      penilai pada judul+tahapan ini ikut terkunci: NILAI_TERKUNCI=1
            //      dan STATUS_LULUS ikut terisi.
            //    - Role lain, termasuk Pembimbing yang menjadi Ketua Pembimbing
            //      -> HANYA baris miliknya sendiri. Nilai penilai lain tidak
            //      boleh diubah dari ROLE 'Pembimbing'.
            $nilaiTerkunciInt = $request->nilai_terkunci === 'y' ? 1 : 0;
            $updatePenilaian = [
                'NILAI_TERKUNCI' => $nilaiTerkunciInt,
                'TGL_UPDATE' => now(),
            ];
            if ($statusLulus) {
                $updatePenilaian['STATUS_LULUS'] = $statusLulus;
            }

            $isTuProdi = ($user['role'] ?? null) === 'TU Prodi';

            $queryPenilaian = DB::table('t_penilaian')
                ->where('ID_JUDUL', $id)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang);

            if (! $isTuProdi) {
                $queryPenilaian->where('ID_TIM_SIDANG', $request->id_tim_sidang);
            }

            $jumlahPenilaian = $queryPenilaian->update($updatePenilaian);

            \Log::info('Lock Nilai: Updated t_penilaian', [
                'id_judul' => $id,
                'role' => $user['role'] ?? null,
                'tahapan_sidang' => $request->tahapan_sidang,
                'id_tim_sidang' => $request->id_tim_sidang,
                'peran' => $timSidang->STATUS_TIM_SIDANG ?? null,
                'is_ketua_pembimbing' => $isKetuaPembimbing,
                'scope' => $isTuProdi ? 'semua penilai (TU Prodi)' : 'hanya penilai sendiri',
                'nilai_terkunci' => $request->nilai_terkunci,
                'status_lulus' => $statusLulus,
                'rows_affected' => $jumlahPenilaian,
            ]);

            // 3. Sync t_ajuan_sidang - hak ini milik orang yang mengunci dalam
            //    peran Ketua Pembimbing: baik dia sendiri (role 'Pembimbing')
            //    maupun TU Prodi yang bertindak mewakilinya. Penilai lain
            //    tidak boleh menyentuh t_ajuan_sidang.
            //    NILAI_TERKUNCI tidak lagi tertahan bila dropdown "Pilih status"
            //    kosong (dulu membuat t_ajuan_sidang tetap NILAI_TERKUNCI='t'
            //    padahal nilai sudah dikunci).
            if ($isKetuaPembimbing) {
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
                    'is_ketua_pembimbing' => $isKetuaPembimbing,
                    'ada_ketua_pembimbing' => $adaKetuaPembimbing,
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
}
