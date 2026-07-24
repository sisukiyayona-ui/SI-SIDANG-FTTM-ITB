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
        
        $request->validate([
            'id_judul' => 'required',
            'tahapan_sidang' => 'required',
            'id_tim_sidang' => 'required',
            'penilaian' => 'required|array',
            'penilaian.*.id_penilaian' => 'required',
            'penilaian.*.nilai' => 'nullable|numeric|min:0|max:100',
            'status_lulus' => 'nullable|in:lulus,tidak lulus',
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

        // Save status_lulus if provided
        $statusLulusUpdated = false;
        if ($request->has('status_lulus') && $request->input('status_lulus') !== '') {
            $isPembimbing = TTimSidang::where('ID_JUDUL', $ajuan->id_judul)
                ->where('TAHAPAN_SIDANG', $ajuan->tahapan_sidang)
                ->where('ID_USER_PENILAI', $user['id'])
                ->where(function($query) {
                    $query->where('STATUS_TIM_SIDANG', 'Ketua Pembimbing')
                        ->orWhere('STATUS_TIM_SIDANG', 'Pembimbing')
                        ->orWhere('STATUS_TIM_SIDANG', 'Pembimbing II')
                        ->orWhere('STATUS_TIM_SIDANG', 'like', '%Pembimbing%');
                })
                ->exists();

            if ($isPembimbing || $isAdmin) {
                DB::table('t_ajuan_sidang')
                    ->where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapanSidang)
                    ->update([
                        'STATUS_LULUS' => $request->input('status_lulus'),
                        'TGL_UPDATE' => now(),
                    ]);
                $statusLulusUpdated = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Penilaian berhasil disimpan',
            'status_lulus_updated' => $statusLulusUpdated,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = session('auth_user');
        
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $penilaian = TPenilaian::find($id);
        if (!$penilaian) {
            return response()->json(['error' => 'Data penilaian tidak ditemukan'], 404);
        }

        // Validate that user owns this penilaian
        if ($penilaian->id_user_penilai != $user['id']) {
            return response()->json(['error' => 'Anda tidak memiliki akses untuk mengubah penilaian ini'], 403);
        }

        $penilaian->update([
            'nilai' => $request->nilai,
            'catatan' => $request->catatan,
            'tgl_update' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Penilaian berhasil diperbarui']);
    }

    public function updateStatusLulus(Request $request, $id)
    {
        $user = session('auth_user');
        
        $request->validate([
            'status_lulus' => 'required|in:lulus,tidak lulus',
        ]);

        $ajuan = TAjuanSidang::where('id_judul', $id)
            ->where('tahapan_sidang', $request->tahapan_sidang)
            ->first();
        if (!$ajuan) {
            return response()->json(['error' => 'Data ajuan sidang tidak ditemukan'], 404);
        }

        // Check if user is Pembimbing (any type) or TU Prodi
        $isPembimbing = TTimSidang::where('ID_JUDUL', $ajuan->id_judul)
            ->where('TAHAPAN_SIDANG', $ajuan->tahapan_sidang)
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
            ->where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapanSidang)
            ->update([
                'STATUS_LULUS' => $request->input('status_lulus'),
                'TGL_UPDATE' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Status kelulusan berhasil diperbarui']);
    }
}
