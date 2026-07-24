<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTahapanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('T_TAHAPAN')->insert([
            ['TAHAPAN' => 'tahap I', 'KODE_TAHAP' => 'T1', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'tahap II', 'KODE_TAHAP' => 'T2', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'SK I', 'KODE_TAHAP' => 'SK1', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'SK II', 'KODE_TAHAP' => 'SK2', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'SK III', 'KODE_TAHAP' => 'SK3', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'SK IV', 'KODE_TAHAP' => 'SK4', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
            ['TAHAPAN' => 'tahap IV', 'KODE_TAHAP' => 'T4', 'STRATA' => 'S3', 'TGL_BUAT' => now(), 'TGL_UPDATE' => now()],
        ]);
    }
}
