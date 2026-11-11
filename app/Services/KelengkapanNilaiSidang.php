<?php

namespace App\Services;

use App\Models\TAjuanSidang;
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

        $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->latest('id')
            ->first();

        // Tahapan tesis tetap memakai aturan S2 meskipun belum ada ajuan.
        if (strtoupper(trim((string) ($ajuan->STRATA ?? ''))) === 'S2'
            || in_array(strtolower(trim((string) $tahapan)), ['tesis i', 'tesis ii'], true)) {
            return self::cekS2($tim, $ajuan);
        }

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

        $filledByTim = [];
        foreach ($rows as $r) {
            $nilai = $r->NILAI === null ? '' : trim((string) $r->NILAI);
            $catatan = $r->CATATAN === null ? '' : trim((string) $r->CATATAN);
            if ($nilai === '' && $catatan === '') {
                continue;
            }
            $filledByTim[(int) $r->ID_TIM_SIDANG][] = (int) $r->ID_PENILAIAN;
        }
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

        // Hitung sekali saja point aktif per form, dipakai untuk cek per penilai.
        // Ketua Sidang tidak dihitung (sudah difilter dari $penilai).
        $pointPerForm = [];
        foreach ($forms as $form) {
            $pointIds = self::pointIds($tahapan, $form, $prodi);
            if ($pointIds !== []) {
                $pointPerForm[$form] = $pointIds;
            }
        }

        // Rincian per penilai: setiap Pembimbing/Penguji wajib melengkapi
        // seluruh parameter form yang dipakai — bukan gabungan nilai tim.
        $hasil['per_tim'] = [];
        $belumParameter = [];
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
            $belumParameter = array_merge($belumParameter, $belumOwn);
        }

        $hasil['belum_parameter'] = array_values(array_unique($belumParameter));
        $semuaPenilaiLengkap = collect($hasil['per_tim'])->every(fn ($info) => ! empty($info['lengkap']));
        $hasil['lengkap'] = $hasil['ada_tim']
            && $hasil['belum'] === []
            && $semuaPenilaiLengkap
            && $hasil['belum_parameter'] === [];
        $hasil['sudah_pakai_form'] = $forms->all();

        return $hasil;
    }

    private static function cekS2($tim, ?TAjuanSidang $ajuan)
    {
        $chairIds = $tim->filter(fn ($member) => strtolower(trim((string) $member->STATUS_TIM_SIDANG)) === 'ketua sidang')
            ->pluck('ID_USER_PENILAI');
        $penilai = $tim->filter(function ($member) use ($chairIds) {
            $status = strtolower(trim((string) $member->STATUS_TIM_SIDANG));
            return $status !== 'ketua sidang'
                && (str_contains($status, 'pembimbing') || str_contains($status, 'penguji'))
                && ! $chairIds->contains($member->ID_USER_PENILAI);
        });

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

        $points = collect();
        $rows = collect();
        if ($ajuan) {
            $query = TPointPenilaian::where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)
                ->where('STRATA', 'S2')
                ->where('STATUS_AKTIF', 'AKTIF');

            // Prodi harus berasal dari ajuan terbaru, tanpa fallback lintas prodi.
            if ($ajuan->KODE_PRODI) {
                $query->where('KODE_PRODI', $ajuan->KODE_PRODI);
            }
            if ($ajuan->ID_PRODI) {
                $query->where('ID_PRODI', $ajuan->ID_PRODI);
            }
            if (! $ajuan->KODE_PRODI && ! $ajuan->ID_PRODI) {
                $query->whereRaw('1 = 0');
            }
            $points = $query->get();

            $rows = DB::table('t_penilaian')
                ->where('ID_AJUAN', $ajuan->id)
                ->where('ID_JUDUL', $ajuan->ID_JUDUL)
                ->where('TAHAPAN_SIDANG', $ajuan->TAHAPAN_SIDANG)
                ->get(['ID_TIM_SIDANG', 'ID_USER_PENILAI', 'ID_PENILAIAN', 'NO_FORM', 'NILAI', 'CATATAN']);
        }

        $perTim = $rows->groupBy('ID_TIM_SIDANG');
        $forms = [];
        foreach ($penilai as $t) {
            $own = $perTim->get($t->id, collect())->where('ID_USER_PENILAI', $t->ID_USER_PENILAI);
            $ada = $own->isNotEmpty();
            if (! $ada) {
                $hasil['belum'][] = self::label($t);
            }
            $forms = array_merge($forms, $own->pluck('NO_FORM')->filter()->all());
            $own = $own->keyBy('ID_PENILAIAN');
            $belumOwn = [];

            // Setiap penilai wajib melengkapi seluruh point aktif, bukan gabungan nilai tim.
            foreach ($points as $point) {
                $score = $own->get($point->id);
                $complete = strtolower(trim((string) $point->STATUS_CATATAN)) === 'y'
                    ? trim((string) ($score->CATATAN ?? '')) !== ''
                    : is_numeric($score->NILAI ?? null) && $score->NILAI >= 1 && $score->NILAI <= 5;
                if (! $complete) {
                    $belumOwn[] = $point->NO_FORM;
                }
            }
            $belumOwn = array_values(array_unique($belumOwn));
            $hasil['per_tim'][(string) $t->id] = [
                'ada' => $ada,
                'lengkap' => $points->isNotEmpty() && $ada && $belumOwn === [],
                'belum_parameter' => $belumOwn,
            ];
            $hasil['belum_parameter'] = array_merge($hasil['belum_parameter'], $belumOwn);
        }

        $hasil['belum_parameter'] = array_values(array_unique($hasil['belum_parameter']));
        $hasil['lengkap'] = $points->isNotEmpty() && $hasil['belum'] === [] && $hasil['belum_parameter'] === [];
        $hasil['sudah_pakai_form'] = array_values(array_unique($forms));

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
            $baris[] = 'Parameter penilaian form ' . implode(', ', $hasil['belum_parameter']) . ' belum lengkap diisi oleh Pembimbing/Penguji (Ketua Sidang tidak mengisi nilai).';
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
