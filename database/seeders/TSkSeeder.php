<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TSkSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('t_sk')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();

        $judulDede = DB::table('t_judul')->where('NIM', '456783210')->first();
        $judulBastian = DB::table('t_judul')->where('NIM', '32224001')->first();

        DB::table('t_sk')->insert([
            'NO_SK' => 'SK/SIDANG/322/2023/001',
            'ID_JUDUL' => $judulDede->id,
            'TAHAPAN_SIDANG' => 'tahap I',
            'TGL_BUAT' => $now,
            'TGL_UPDATE' => $now,
        ]);

        DB::table('t_sk')->insert([
            'NO_SK' => 'SK/SIDANG/322/2024/001',
            'ID_JUDUL' => $judulBastian->id,
            'TAHAPAN_SIDANG' => 'tahap I',
            'TGL_BUAT' => $now,
            'TGL_UPDATE' => $now,
        ]);
    }
}
