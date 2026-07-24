<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTimSidangSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('T_TIM_SIDANG')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $pembimbing = DB::table('T_USER')->where('USERNAME', 'pembimbing')->first();
        $penguji = DB::table('T_USER')->where('USERNAME', 'penguji')->first();

        $judulDede = DB::table('T_JUDUL')->where('NIM', '456783210')->first();
        $judulBastian = DB::table('T_JUDUL')->where('NIM', '32224001')->first();
        $now = now();

        $skDede = DB::table('T_SK')
            ->where('ID_JUDUL', $judulDede->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        $skBastian = DB::table('T_SK')
            ->where('ID_JUDUL', $judulBastian->id)
            ->where('TAHAPAN_SIDANG', 'tahap I')
            ->first();

        DB::table('T_TIM_SIDANG')->insert([
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
