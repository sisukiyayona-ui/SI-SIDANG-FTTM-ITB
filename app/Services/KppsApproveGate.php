<?php

namespace App\Services;

use App\Models\TAjuanSidang;
use App\Models\TJudul;
use Illuminate\Support\Facades\DB;

class KppsApproveGate
{
    /**
     * Gate approve KPPS prodi untuk input nilai Pembimbing/Penguji.
     *
     * Terblokir bila prodi punya minimal 1 KPPS AKTIF dan belum semuanya
     * meng-approve ajuan sidang pada tahapan tersebut.
     * Prodi tanpa KPPS aktif TIDAK dikunci (tidak ada yang harus approve).
     */
    public static function cek($idJudul, $tahapan, $kodeProdiMahasiswa = null)
    {
        $hasil = [
            'blocked' => false,
            'total' => 0,
            'approved' => 0,
            'semua' => true,
            'kode_prodi' => null,
            'id_ajuan' => null,
        ];

        $ajuan = TAjuanSidang::where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->latest('id')
            ->first();

        // Sumber kebenaran prodi mahasiswa (judul -> user), fallback ajuan.
        $kodeProdi = $kodeProdiMahasiswa;
        if (! $kodeProdi) {
            $judul = TJudul::find($idJudul);
            $kodeProdi = $judul?->user?->KODE_PRODI;
        }
        if (! $kodeProdi && $ajuan) {
            $kodeProdi = $ajuan->KODE_PRODI;
        }

        $hasil['kode_prodi'] = $kodeProdi;
        $hasil['id_ajuan'] = $ajuan?->id;

        if (! $kodeProdi) {
            return $hasil;
        }

        $total = DB::table('t_kpps')
            ->where('KODE_PRODI', $kodeProdi)
            ->where('STATUS_AKTIF', 'AKTIF')
            ->distinct()
            ->count('ID_USER');

        $approved = 0;
        if ($ajuan) {
            $approved = DB::table('t_kpps as k')
                ->join('t_app_ajuan_sidang as app', function ($join) use ($ajuan) {
                    $join->on('k.ID_USER', 'app.ID_USER')
                        ->where('app.ID_AJUAN_SIDANG', $ajuan->id);
                })
                ->where('k.KODE_PRODI', $kodeProdi)
                ->where('k.STATUS_AKTIF', 'AKTIF')
                ->whereIn('app.STATUS_APPROVE', ['t', 'y'])
                ->distinct()
                ->count('k.ID_USER');
        }

        $hasil['total'] = $total;
        $hasil['approved'] = $approved;
        $hasil['semua'] = $total > 0 && $approved >= $total;
        $hasil['blocked'] = $total > 0 && $approved < $total;

        return $hasil;
    }

    public static function pesan(array $hasil)
    {
        if (! ($hasil['blocked'] ?? false)) {
            return '';
        }

        return sprintf(
            'Menunggu approve KPPS prodi: %d dari %d anggota KPPS aktif sudah approve. Input nilai dan Kunci Nilai dibuka setelah semua approve.',
            (int) $hasil['approved'],
            (int) $hasil['total']
        );
    }
}
