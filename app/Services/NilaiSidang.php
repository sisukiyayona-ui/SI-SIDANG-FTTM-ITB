<?php

namespace App\Services;

/**
 * Aturan input nilai per prodi.
 *
 * - 123 / 223 (Teknik Geofisika): nilai huruf A, AB, B, BC, C, D + status per baris.
 * - 124 / 224 (Metalurgi): angka 1 - 100.
 * - Prodi lain: angka 1 - 5.
 */
class NilaiSidang
{
    public const HURUF = ['A', 'AB', 'B', 'BC', 'C', 'D'];

    public const MODE_HURUF = 'huruf';
    public const MODE_100 = '100';
    public const MODE_5 = '5';

    public static function mode($kodeProdi): string
    {
        $kode = trim((string) $kodeProdi);

        if (in_array($kode, ['123', '223'], true)) {
            return self::MODE_HURUF;
        }

        if (in_array($kode, ['124', '224'], true)) {
            return self::MODE_100;
        }

        return self::MODE_5;
    }

    public static function isHuruf($kodeProdi): bool
    {
        return self::mode($kodeProdi) === self::MODE_HURUF;
    }

    /** Batas atas nilai numerik. */
    public static function max($kodeProdi): int
    {
        return self::mode($kodeProdi) === self::MODE_100 ? 100 : 5;
    }

    /** Aturan validasi untuk satu sel nilai. */
    public static function rule($kodeProdi, bool $required = false): string
    {
        if (self::isHuruf($kodeProdi)) {
            return ($required ? 'required' : 'nullable') . '|in:' . implode(',', self::HURUF);
        }

        return ($required ? 'required' : 'nullable') . '|numeric|min:1|max:' . self::max($kodeProdi);
    }

    public static function message($kodeProdi): string
    {
        if (self::isHuruf($kodeProdi)) {
            return 'Nilai huruf harus salah satu: A, AB, B, BC, C, D.';
        }

        return 'Nilai harus berupa angka antara 1 dan ' . self::max($kodeProdi) . '.';
    }

    public static function normalize($nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        $value = trim((string) $nilai);

        return $value === '' ? null : $value;
    }

    public static function isValid($kodeProdi, $nilai): bool
    {
        $value = self::normalize($nilai);
        if ($value === null) {
            return false;
        }

        if (self::isHuruf($kodeProdi)) {
            return in_array(strtoupper($value), self::HURUF, true);
        }

        if (! is_numeric($value)) {
            return false;
        }

        return (float) $value >= 1 && (float) $value <= self::max($kodeProdi);
    }

    /**
     * Kelengkapan satu baris nilai: angka/huruf valid, atau catatan bila
     * parameter mewajibkan catatan (STATUS_CATATAN = 'y').
     */
    public static function isComplete($kodeProdi, $nilai, $nilaiHuruf, $catatan, $statusCatatan): bool
    {
        if (strtolower(trim((string) $statusCatatan)) === 'y') {
            return trim((string) $catatan) !== '';
        }

        $value = self::isHuruf($kodeProdi) ? $nilaiHuruf : $nilai;

        return self::isValid($kodeProdi, $value);
    }
}
