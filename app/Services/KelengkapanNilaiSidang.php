<?php

namespace App\Services;

use App\Models\TPointPenilaian;
use App\Models\TTimSidang;
use Illuminate\Support\Facades\DB;

class KelengkapanNilaiSidang
{
    public static function cek($idJudul, $tahapan, $prodi = null)
    {
        $tim = TTimSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->orderBy('URUTAN')
            ->get();

        $penilai = $tim->filter(function ($t) {
            $status = strtolower(trim((string) ($t->keterangan ?? $t->status_tim_sidang ?? '')));
            if ($status === 'ketua sidang') {
                return false;
            }
            return str_contains($status, 'pembimbing') || str_contains($status, 'penguji');
        })->values();

        $hasil = [
            'lengkap' => false,
            'ada_tim' => $penilai->isNotEmpty(),
            'belum' => [],
            'belum_parameter' => [],
            'sudah_pakai_form' => [],
            'per_tim' => [],
        ];

        if ($penilai->isEmpty()) {
            return $hasil;
        }

        $rows = DB::table('t_penilaian')
            ->where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->get(['ID_TIM_SIDANG', 'ID_PENILAIAN', 'NO_FORM', 'NILAI', 'CATATAN']);

        $filled = [];
        $filledByTim = [];
        foreach ($rows as $r) {
            $nilai = $r->NILAI === null ? '' : trim((string) $r->NILAI);
            $catatan = $r->CATATAN === null ? '' : trim((string) $r->CATATAN);
            if ($nilai === '' && $catatan === '') {
                continue;
            }
            $filled[] = (int) $r->ID_PENILAIAN;
            $filledByTim[(int) $r->ID_TIM_SIDANG][] = (int) $r->ID_PENILAIAN;
        }
        $filled = array_unique($filled);
        foreach ($filledByTim as $k => $v) {
            $filledByTim[$k] = array_unique($v);
        }

        $perTim = $rows->groupBy('ID_TIM_SIDANG');
        foreach ($penilai as $t) {
            if (($perTim[$t->id] ?? collect())->isEmpty()) {
                $hasil['belum'][] = self::label($t);
            }
        }

        $forms = $rows->pluck('NO_FORM')->filter()->unique()->values();
        $belumParameter = [];

        // Hitung sekali saja point aktif per form, dipakai untuk cek tim maupun per tim.
        $pointPerForm = [];
        foreach ($forms as $form) {
            $pointIds = self::pointIds($tahapan, $form, $prodi);
            if ($pointIds !== []) {
                $pointPerForm[$form] = $pointIds;
            }
        }

        foreach ($pointPerForm as $form => $pointIds) {
            foreach ($pointIds as $pointId) {
                if (! in_array($pointId, $filled, true)) {
                    $belumParameter[] = $form;
                    break;
                }
            }
        }

        // Rincian per penilai: dipakai untuk membedakan "belum mengisi sama sekali"
        // dari "sudah mengisi di form tapi belum klik Simpan" (data di DB masih kosong).
        $hasil['per_tim'] = [];
        foreach ($penilai as $t) {
            $own = $filledByTim[$t->id] ?? [];
            $belumOwn = [];
            foreach ($pointPerForm as $form => $pointIds) {
                foreach ($pointIds as $pointId) {
                    if (! in_array($pointId, $own, true)) {
                        $belumOwn[] = $form;
                        break;
                    }
                }
            }
            $belumOwn = array_values(array_unique($belumOwn));
            $ada = ! ($perTim[$t->id] ?? collect())->isEmpty();

            $hasil['per_tim'][(string) $t->id] = [
                'ada' => $ada,
                'lengkap' => $ada && $belumOwn === [],
                'belum_parameter' => $belumOwn,
            ];
        }

        $hasil['belum_parameter'] = array_values(array_unique($belumParameter));
        $hasil['lengkap'] = $hasil['belum'] === [] && $hasil['belum_parameter'] === [];
        $hasil['sudah_pakai_form'] = $forms->all();

        return $hasil;
    }

    public static function pesan(array $hasil)
    {
        if (! $hasil['ada_tim']) {
            return 'Tim Pembimbing dan Penguji belum diisi, nilai tidak bisa dikunci.';
        }

        $baris = [];

        if (! empty($hasil['belum'])) {
            $baris[] = 'Belum mengisi nilai: ' . implode(', ', $hasil['belum']) . '.';
        }

        if (! empty($hasil['belum_parameter'])) {
            $baris[] = 'Parameter penilaian form ' . implode(', ', $hasil['belum_parameter']) . ' belum lengkap diisi.';
        }

        if ($baris === []) {
            return 'Pembimbing/Penguji belum mengisi nilai.';
        }

        return implode(' ', $baris);
    }

    private static function pointIds($tahapan, $form, $prodi)
    {
        $query = TPointPenilaian::where('TAHAPAN_SIDANG', $tahapan)
            ->where('STATUS_AKTIF', 'AKTIF')
            ->where('NO_FORM', $form);

        if ($prodi) {
            $query->where('KODE_PRODI', $prodi);
        }

        $ids = $query->pluck('id')->map(fn($id) => (int) $id)->all();

        if ($ids === [] && $prodi) {
            $ids = TPointPenilaian::where('TAHAPAN_SIDANG', $tahapan)
                ->where('STATUS_AKTIF', 'AKTIF')
                ->where('NO_FORM', $form)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();
        }

        return $ids;
    }

    private static function label($tim)
    {
        $nama = trim((string) ($tim->NAMA ?? ''));
        $status = trim((string) ($tim->keterangan ?? $tim->status_tim_sidang ?? ''));

        if ($nama === '') {
            return $status;
        }

        return $status === '' ? $nama : $nama . ' (' . $status . ')';
    }
}
