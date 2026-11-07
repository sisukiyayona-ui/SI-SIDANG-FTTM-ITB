<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TTahapanSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['TAHAPAN' => 'tahap I', 'KODE_TAHAP' => 'T1', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'tahap II', 'KODE_TAHAP' => 'T2', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'SK I', 'KODE_TAHAP' => 'SK1', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'SK II', 'KODE_TAHAP' => 'SK2', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'SK III', 'KODE_TAHAP' => 'SK3', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'SK IV', 'KODE_TAHAP' => 'SK4', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'tahap IV', 'KODE_TAHAP' => 'T4', 'STRATA' => 'S3'],
            ['TAHAPAN' => 'TA I', 'KODE_TAHAP' => 'TA I', 'STRATA' => 'S1'],
            ['TAHAPAN' => 'TA II', 'KODE_TAHAP' => 'TA II', 'STRATA' => 'S1'],
            ['TAHAPAN' => 'Tesis I', 'KODE_TAHAP' => 'Tesis I', 'STRATA' => 'S2'],
            ['TAHAPAN' => 'Tesis II', 'KODE_TAHAP' => 'Tesis II', 'STRATA' => 'S2'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('t_tahapan')
                ->where('TAHAPAN', $row['TAHAPAN'])
                ->where('STRATA', $row['STRATA'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('t_tahapan')->insert($row + [
                'TGL_BUAT' => now(),
                'TGL_UPDATE' => now(),
            ]);
        }
    }
}
