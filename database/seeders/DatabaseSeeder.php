<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TProdi;
use App\Models\TTahapan;
use App\Models\TUser;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed T_prodi
        $prodis = [
            ['kode_prodi' => '322', 'nama_prodi' => 'Teknik Perminyakan', 'status_aktif' => 'AKTIF'],
            ['kode_prodi' => '328', 'nama_prodi' => 'Teknik Metalurgi', 'status_aktif' => 'AKTIF'],
            ['kode_prodi' => '329', 'nama_prodi' => 'Teknik Geologi', 'status_aktif' => 'AKTIF'],
            ['kode_prodi' => '330', 'nama_prodi' => 'Teknik Geofisika', 'status_aktif' => 'AKTIF'],
        ];

        foreach ($prodis as $p) {
            TProdi::create($p);
        }

        // Seed T_tahapan
        $tahapans = [
            ['Tahapan' => 'Ujian Kualifikasi', 'Kode_tahap' => 'UK', 'strata' => 'S3'],
            ['Tahapan' => 'Sidang Proposal', 'Kode_tahap' => 'SP', 'strata' => 'S3'],
            ['Tahapan' => 'Seminar Kemajuan I', 'Kode_tahap' => 'SK1', 'strata' => 'S3'],
            ['Tahapan' => 'Seminar Kemajuan II', 'Kode_tahap' => 'SK2', 'strata' => 'S3'],
            ['Tahapan' => 'Seminar Kemajuan III', 'Kode_tahap' => 'SK3', 'strata' => 'S3'],
            ['Tahapan' => 'Seminar Kemajuan IV', 'Kode_tahap' => 'SK4', 'strata' => 'S3'],
            ['Tahapan' => 'Sidang Akhir', 'Kode_tahap' => 'SA', 'strata' => 'S3'],
        ];

        foreach ($tahapans as $t) {
            TTahapan::create($t);
        }

        // Seed T_User
        $users = [
            [
                'nip_nim' => '198203012010121001',
                'nama_lengkap' => 'Admin FTTM',
                'email' => 'admin@fttm.itb.ac.id',
                'Username' => 'admin',
                'Password' => Hash::make('admin123'),
                'status_pegawai' => 'Tendik',
                'jenis_user' => 'Admin',
                'kode_fs' => '13321002',
                'nama_fs' => 'FTTM',
                'status_aktif' => 'AKTIF',
                'status_approve' => 'y',
            ],
            [
                'nip_nim' => '198504122015041002',
                'nama_lengkap' => 'TU Perminyakan',
                'email' => 'tu_tm@fttm.itb.ac.id',
                'Username' => 'tuprodi',
                'Password' => Hash::make('prodi123'),
                'status_pegawai' => 'Tendik',
                'jenis_user' => 'TU Prodi',
                'kode_prodi' => '322',
                'nama_prodi' => 'Teknik Perminyakan',
                'kode_fs' => '13321002',
                'nama_fs' => 'FTTM',
                'status_aktif' => 'AKTIF',
                'status_approve' => 'y',
            ],
            [
                'nip_nim' => '199001012020011003',
                'nama_lengkap' => 'TU Fakultas Sains',
                'email' => 'tu_fs@fttm.itb.ac.id',
                'Username' => 'tufs',
                'Password' => Hash::make('fs123'),
                'status_pegawai' => 'Tendik',
                'jenis_user' => 'FS',
                'kode_fs' => '13321002',
                'nama_fs' => 'FTTM',
                'status_aktif' => 'AKTIF',
                'status_approve' => 'y',
            ],
            [
                'nip_nim' => '32219001',
                'nama_lengkap' => 'Ahmad Hidayat',
                'email' => 'ahmad@mahasiswa.itb.ac.id',
                'Username' => 'mahasiswa',
                'Password' => Hash::make('mhs123'),
                'status_pegawai' => 'Mahasiswa',
                'jenis_user' => 'Mahasiswa',
                'kode_prodi' => '322',
                'nama_prodi' => 'Teknik Perminyakan',
                'strata' => 'S3',
                'thn_angkatan' => 2022,
                'kode_fs' => '13321002',
                'nama_fs' => 'FTTM',
                'status_aktif' => 'AKTIF',
                'status_approve' => 'y',
            ],
            [
                'nip_nim' => '197508082000031001',
                'nama_lengkap' => 'Prof. Dr. Ir. Sutrisno',
                'email' => 'sutrisno@fttm.itb.ac.id',
                'Username' => 'pembimbing',
                'Password' => Hash::make('dosen123'),
                'status_pegawai' => 'Dosen',
                'jenis_user' => 'Pembimbing',
                'kode_prodi' => '322',
                'nama_prodi' => 'Teknik Perminyakan',
                'kode_fs' => '13321002',
                'nama_fs' => 'FTTM',
                'status_aktif' => 'AKTIF',
                'status_approve' => 'y',
            ]
        ];

        foreach ($users as $u) {
            TUser::create($u);
        }

        // Seed T_Syarat_sidang
        \App\Models\TSyaratSidang::factory(10)->create();

        // Seed T_point_penilaian
        \App\Models\TPointPenilaian::factory(10)->create();

        // Seed T_judul
        \App\Models\TJudul::factory(10)->create();

        // Seed T_ajuan_sidang
        \App\Models\TAjuanSidang::factory(15)->create();

        // Seed T_tim_sidang
        \App\Models\TTimSidang::factory(20)->create();

        // Seed T_penilaian
        \App\Models\TPenilaian::factory(30)->create();

        // Seed T_cek_persyaratan
        \App\Models\TCekPersyaratan::factory(15)->create();

        // Seed T_judul_temp
        \App\Models\TJudulTemp::factory(3)->create();
    }
}
