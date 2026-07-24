<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TSkSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('T_SK')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $now = now();

        $judulDede = DB::table('T_JUDUL')->where('NIM', '456783210')->first();
        $judulBastian = DB::table('T_JUDUL')->where('NIM', '32224001')->first();

        DB::table('T_SK')->insert([
            'NO_SK' => 'SK/SIDANG/322/2023/001',
            'ID_JUDUL' => $judulDede->id,
            'TAHAPAN_SIDANG' => 'tahap I',
            'TGL_BUAT' => $now,
            'TGL_UPDATE' => $now,
        ]);

        DB::table('T_SK')->insert([
            'NO_SK' => 'SK/SIDANG/322/2024/001',
            'ID_JUDUL' => $judulBastian->id,
            'TAHAPAN_SIDANG' => 'tahap I',
            'TGL_BUAT' => $now,
            'TGL_UPDATE' => $now,
        ]);
    }
}
