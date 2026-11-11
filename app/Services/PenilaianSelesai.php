<?php

namespace App\Services;

/**
 * Status penilaian sebuah ajuan sidang: sudah selesai atau belum.
 *
 * Penilaian dianggap selesai bila:
 *  - nilai sudah dikunci (t_ajuan_sidang.NILAI_TERKUNCI = 'y' atau ada baris
 *    t_penilaian dengan NILAI_TERKUNCI = 1), ATAU
 *  - status lulus sudah ditentukan.
 * S2 hanya selesai setelah ajuan dikunci Ketua Sidang; keputusan sebelum itu
 * masih berupa draf, dan kunci baris dari percobaan lama tidak berlaku.
 *
 * Saat selesai, parameter penilaian hanya diambil dari t_penilaian.
 * Parameter master yang ditambahkan SETELAH penilaian selesai tidak
 * ditampilkan dan tidak bisa diisi.
 */
class PenilaianSelesai
{
    /** Status lulus = keputusan kelulusan yang sudah ditentukan. */
    public static function statusDitentukan($ajuan): bool
    {
        if (! $ajuan) {
            return false;
        }

        if (self::isS2($ajuan)) {
            if (!self::terkunci($ajuan)) {
                return false;
            }

            return in_array(strtolower(trim((string) ($ajuan->STATUS_LULUS ?? $ajuan->status_lulus ?? ''))), [
                'lulus sidang magister',
                'tidak lulus sidang magister',
                'mengulangi sidang magister',
            ], true);
        }

        $status = $ajuan->status_lulus ?? '';

        if ($status === null || $status === '') {
            return false;
        }

        // Nilai lain (mis. 'diajukan') bukan keputusan kelulusan.
        return in_array($status, [
            'lulus',
            'tidak lulus',
            'Layak tanpa perbaikan',
            'Layak dengan perbaikan minor tanpa harus dibaca kembali',
            'Layak dengan perbaikan minor dan perbaikan harus dibaca kembali',
            'Layak dengan perbaikan major (substansial)',
            'Tidak layak',
        ], true);
    }

    public static function terkunci($ajuan, $penilaian = null): bool
    {
        if (self::isS2($ajuan)) {
            // S2 is locked collectively on the ajuan, not by individual/older score rows.
            return strtolower(trim((string) ($ajuan->NILAI_TERKUNCI ?? $ajuan->nilai_terkunci ?? ''))) === 'y';
        }

        if ($ajuan && strtolower((string) ($ajuan->nilai_terkunci ?? '')) === 'y') {
            return true;
        }

        if ($penilaian && count($penilaian) > 0 && $penilaian->where('NILAI_TERKUNCI', 1)->count() > 0) {
            return true;
        }

        return false;
    }

    private static function isS2($ajuan): bool
    {
        $strata = strtoupper(trim((string) ($ajuan->STRATA ?? $ajuan->strata ?? '')));
        if ($strata === '' && $ajuan instanceof \App\Models\TAjuanSidang) {
            // The JUDUL column masks property access to the judul relationship.
            $strata = strtoupper(trim((string) ($ajuan->judul()->first()?->user?->STRATA ?? '')));
        }

        return $strata === 'S2';
    }

    public static function cek($ajuan, $penilaian = null): bool
    {
        return self::terkunci($ajuan, $penilaian) || self::statusDitentukan($ajuan);
    }
}
