<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTimSidangSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('t_tim_sidang')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $pembimbing = DB::table('t_user')->where('USERNAME', 'pembimbing')->first();
        $penguji = DB::table('t_user')->where('USERNAME', 'penguji')->first();

        $judulDede = DB::table('t_judul')->where('NIM', '456783210')->first();
        $judulBastian = DB::table('t_judul')->where('NIM', '32224001')->first();
        $now = now();

        $skDede = DB::table('t_sk')
            ->where('ID_JUDUL', $judulDede->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        $skBastian = DB::table('t_sk')
            ->where('ID_JUDUL', $judulBastian->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        DB::table('t_tim_sidang')->insert([
            // dede - Tahap I
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulDede->id,
                'STATUS_TIM_SIDANG' => 'Ketua Sidang',
                'ID_USER_PENILAI' => $penguji->id,
                'NIP' => $penguji->NIP_NIM,
                'NAMA' => $penguji->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skDede->id,
                'URUTAN' => 1,
            ],
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulDede->id,
                'STATUS_TIM_SIDANG' => 'Ketua Pembimbing',
                'ID_USER_PENILAI' => $pembimbing->id,
                'NIP' => $pembimbing->NIP_NIM,
                'NAMA' => $pembimbing->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skDede->id,
                'URUTAN' => 2,
            ],
            // Bastian - Tahap I
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulBastian->id,
                'STATUS_TIM_SIDANG' => 'Ketua Sidang',
                'ID_USER_PENILAI' => $penguji->id,
                'NIP' => $penguji->NIP_NIM,
                'NAMA' => $penguji->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skBastian?->id,
                'URUTAN' => 1,
            ],
            [
                'TAHAPAN_SIDANG' => 'tahap I',
                'ID_JUDUL' => $judulBastian->id,
                'STATUS_TIM_SIDANG' => 'Ketua Pembimbing',
                'ID_USER_PENILAI' => $pembimbing->id,
                'NIP' => $pembimbing->NIP_NIM,
                'NAMA' => $pembimbing->NAMA_LENGKAP,
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
                'ID_SK' => $skBastian?->id,
                'URUTAN' => 2,
            ],
        ]);
    }
}
