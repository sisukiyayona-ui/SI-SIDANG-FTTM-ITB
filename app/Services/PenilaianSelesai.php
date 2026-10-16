<?php

namespace App\Services;

/**
 * Status penilaian sebuah ajuan sidang: sudah selesai atau belum.
 *
 * Penilaian dianggap selesai bila:
 *  - nilai sudah dikunci (t_ajuan_sidang.NILAI_TERKUNCI = 'y' atau ada baris
 *    t_penilaian dengan NILAI_TERKUNCI = 1), ATAU
 *  - status lulus sudah ditentukan.
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
        if ($ajuan && strtolower((string) ($ajuan->nilai_terkunci ?? '')) === 'y') {
            return true;
        }

        if ($penilaian && count($penilaian) > 0 && $penilaian->where('NILAI_TERKUNCI', 1)->count() > 0) {
            return true;
        }

        return false;
    }

    public static function cek($ajuan, $penilaian = null): bool
    {
        return self::terkunci($ajuan, $penilaian) || self::statusDitentukan($ajuan);
    }
}
