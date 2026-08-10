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
        
        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'id_tim_sidang' => 'required',
            'penilaian' => 'required|array',
            'penilaian.*.id_penilaian' => 'required',
            'penilaian.*.nilai' => 'nullable|numeric|min:1|max:5',
            'status_lulus' => 'nullable|in:' . implode(',', $allowedStatusLulus),
        ]);

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

            // 1. Update t_penilaian for this penilai
            $nilaiTerkunciInt = $request->nilai_terkunci === 'y' ? 1 : 0;
            $updatePenilaian = [
                'NILAI_TERKUNCI' => $nilaiTerkunciInt,
                'TGL_UPDATE' => now(),
            ];
            if ($statusLulus) {
                $updatePenilaian['STATUS_LULUS'] = $statusLulus;
            }

            DB::table('t_penilaian')
                ->where('ID_JUDUL', $id)
                ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                ->where('ID_TIM_SIDANG', $request->id_tim_sidang)
                ->update($updatePenilaian);

            // 2. Check if penilai is Ketua Pembimbing
            $timSidang = TTimSidang::find($request->id_tim_sidang);
            $isKetuaPembimbing = $timSidang && (
                $timSidang->STATUS_TIM_SIDANG === 'Ketua Pembimbing' ||
                strpos($timSidang->STATUS_TIM_SIDANG, 'Ketua Pembimbing') !== false
            );

            // 3. If Ketua Pembimbing, also update t_ajuan_sidang
            if ($isKetuaPembimbing && $statusLulus) {
                $updateAjuan = [
                    'STATUS_LULUS' => $statusLulus,
                    'NILAI_TERKUNCI' => $request->nilai_terkunci,
                    'TGL_UPDATE' => now(),
                ];

                DB::table('t_ajuan_sidang')
                    ->where('ID_JUDUL', $id)
                    ->where('TAHAPAN_SIDANG', $request->tahapan_sidang)
                    ->update($updateAjuan);

                \Log::info('Lock Nilai: Updated t_ajuan_sidang for Ketua Pembimbing');
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
