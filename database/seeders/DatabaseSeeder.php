<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Urutan sesuai dependency FK antar tabel.
     */
    public function run(): void
    {
        $this->call([
            TFsSeeder::class,              // T_FS
            TProdiSeeder::class,           // T_PRODI
            TUserSeeder::class,            // T_USER
            TUserRoleSeeder::class,        // T_USER_ROLE
            TTahapanSeeder::class,         // T_TAHAPAN
            TSyaratSidangSeeder::class,    // T_SYARAT_SIDANG
            TPointPenilaianSeeder::class,  // T_POINT_PENILAIAN
            TJudulSeeder::class,           // T_JUDUL
            TJudulTempSeeder::class,       // T_JUDUL_TEMP
            TAjuanSidangSeeder::class,     // T_AJUAN_SIDANG
            TSkSeeder::class,              // T_SK
            TTimSidangSeeder::class,       // T_TIM_SIDANG
            TCekPersyaratanSeeder::class,  // T_CEK_PERSYARATAN
            TPenilaianSeeder::class,       // T_PENILAIAN
        ]);
    }
}
