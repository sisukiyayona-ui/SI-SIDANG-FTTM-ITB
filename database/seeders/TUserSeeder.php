<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $common = [
            'NAMA_FS' => 'FTTM',
            'STATUS_AKTIF' => 'AKTIF',
            'STATUS_APPROVE' => 't',
            'TGL_CREATE' => $now,
            'TGL_UPDATE' => $now,
            'STATUS_KAPRODI' => 'f',
            'AKUN_INA' => null,
            'THN_ANGKATAN' => null,
            'STRATA' => null,
        ];

        // =============================================
        // DEMO ACCOUNTS (8)
        // =============================================
        $extraMhs = [
            // Bastian - referenced by all other seeders
            [
                'NIP_NIM' => '32224001',
                'NAMA_LENGKAP' => 'Bastian',
                'EMAIL' => 'bastian@mahasiswa.itb.ac.id',
                'USERNAME' => 'bastian',
                'PASSWORD' => Hash::make('mhs123'),
                'STATUS_PEGAWAI' => 'Mahasiswa',
                'JENIS_USER' => 'Mahasiswa',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
                'STRATA' => 'S3',
            ],
        ];

        $demoAccounts = [
            // 1. Admin
            [
                'NIP_NIM' => '112000021',
                'NAMA_LENGKAP' => 'Dede Rosyani',
                'EMAIL' => 'Dede@itb.ac.id',
                'USERNAME' => 'admin',
                'PASSWORD' => Hash::make('admin123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'Admin',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            // 2. TU Prodi
            [
                'NIP_NIM' => '197502082005012001',
                'NAMA_LENGKAP' => 'Feri Rezeki Hastuti, SS',
                'EMAIL' => 'feri@tm.itb.ac.id',
                'USERNAME' => 'tuprodi',
                'PASSWORD' => Hash::make('prodi123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            // 3. TU FS
            [
                'NIP_NIM' => '196508022009021002',
                'NAMA_LENGKAP' => 'Haryanta',
                'EMAIL' => 'haryanta@tm.itb.ac.id',
                'USERNAME' => 'tufs',
                'PASSWORD' => Hash::make('fs123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'FS',
                'KODE_PRODI' => null,
                'NAMA_PRODI' => null,
                'KODE_FS' => '13321002',
            ],
            // 4. Pembimbing
            [
                'NIP_NIM' => '197803152002121001',
                'NAMA_LENGKAP' => 'Dr. Budi Santoso',
                'EMAIL' => 'budi@fttm.itb.ac.id',
                'USERNAME' => 'pembimbing',
                'PASSWORD' => Hash::make('dosen123'),
                'STATUS_PEGAWAI' => 'Dosen',
                'JENIS_USER' => 'Pembimbing',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            // 5. Penguji
            [
                'NIP_NIM' => '197210122003121001',
                'NAMA_LENGKAP' => 'Prof. Dr. Siti Aminah',
                'EMAIL' => 'siti@fttm.itb.ac.id',
                'USERNAME' => 'penguji',
                'PASSWORD' => Hash::make('dosen123'),
                'STATUS_PEGAWAI' => 'Dosen',
                'JENIS_USER' => 'Penguji',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            // 6. Monev
            [
                'NIP_NIM' => '197512012005011002',
                'NAMA_LENGKAP' => 'Dr. Ahmad Hidayat',
                'EMAIL' => 'ahmad@fttm.itb.ac.id',
                'USERNAME' => 'monev',
                'PASSWORD' => Hash::make('dosen123'),
                'STATUS_PEGAWAI' => 'Dosen',
                'JENIS_USER' => 'Monev',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            // 7. Mhs S3
            [
                'NIP_NIM' => '32322004',
                'NAMA_LENGKAP' => 'Ulvienin Harlianti',
                'EMAIL' => 'ulvienin@mahasiswa.itb.ac.id',
                'USERNAME' => 'ulvienin',
                'PASSWORD' => Hash::make('mhs123'),
                'STATUS_PEGAWAI' => 'Mahasiswa',
                'JENIS_USER' => 'Mahasiswa',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
                'STRATA' => 'S3',
            ],
            // 8. Mhs S3
            [
                'NIP_NIM' => '456783210',
                'NAMA_LENGKAP' => 'dede',
                'EMAIL' => 'dede@mahasiswa.itb.ac.id',
                'USERNAME' => 'dede',
                'PASSWORD' => Hash::make('mhs123'),
                'STATUS_PEGAWAI' => 'Mahasiswa',
                'JENIS_USER' => 'Mahasiswa',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
                'STRATA' => 'S3',
            ],
        ];

        // =============================================
        // 18 DOSEN from list
        // =============================================
        $dosen = [
            [
                'NIP_NIM' => '195109141980031001',
                'NAMA_LENGKAP' => 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA',
                'EMAIL' => 'septo@tm.itb.ac.id',
                'USERNAME' => 'septoratno',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '195303031980031001',
                'NAMA_LENGKAP' => 'Prof. Ir. Pudji Permadi, M.Sc., Ph.D',
                'EMAIL' => 'pudji@tm.itb.ac.id',
                'USERNAME' => 'pudjipermadi',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '195205101980031001',
                'NAMA_LENGKAP' => 'Prof. Ir. Doddy Abdassah, M.Sc., Ph.D',
                'EMAIL' => 'abdassah@tm.itb.ac.id',
                'USERNAME' => 'doddyabdassah',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '194612101980031001',
                'NAMA_LENGKAP' => 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D',
                'EMAIL' => 'psukarno@tm.itb.ac.id',
                'USERNAME' => 'pudjosukarno',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '195509021980031001',
                'NAMA_LENGKAP' => 'Dr. Ir. Sudjati Rachmat, DEA',
                'EMAIL' => 'sudjati@tm.itb.ac.id',
                'USERNAME' => 'sudjatir',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '195303251980031001',
                'NAMA_LENGKAP' => 'Ir. Leksono Mucharam, M.Sc., Ph.D',
                'EMAIL' => 'lm@tm.itb.ac.id',
                'USERNAME' => 'leksono',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '196311121980031001',
                'NAMA_LENGKAP' => 'Ir. Asep Kurnia Permadi, M.Sc., Ph.D',
                'EMAIL' => 'akp@tm.itb.ac.id',
                'USERNAME' => 'asepkurnia',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '195508011980032001',
                'NAMA_LENGKAP' => 'Ir. Nenny Miryani Saptadji, Ph.D',
                'EMAIL' => 'nennys@tm.itb.ac.id',
                'USERNAME' => 'nennys',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '196408261980031001',
                'NAMA_LENGKAP' => 'Ir. Tutuka Ariadji, M.Sc., Ph.D',
                'EMAIL' => 'tutukaariadji@tm.itb.ac.id',
                'USERNAME' => 'tutuka',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '196610171980031001',
                'NAMA_LENGKAP' => 'Dr. Ir. Sutopo, M.Sc',
                'EMAIL' => 'sutopo@tm.itb.ac.id',
                'USERNAME' => 'sutopo',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '196801171980031001',
                'NAMA_LENGKAP' => 'Dr. Ir. Taufan Marhaendrajana',
                'EMAIL' => 'tmarhaendrajana@tm.itb.ac.id',
                'USERNAME' => 'taufanm',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '196009191980031001',
                'NAMA_LENGKAP' => 'Ir. Ucok WR Siagian, M.Sc, Ph.D',
                'EMAIL' => 'ucokwrs@tm.itb.ac.id',
                'USERNAME' => 'ucokwrs',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '197206061980031001',
                'NAMA_LENGKAP' => 'Ir. Zuher Syihab, M.Sc, Ph.D',
                'EMAIL' => 'zuher.syihab@tm.itb.ac.id',
                'USERNAME' => 'zuhers',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '197512221980031001',
                'NAMA_LENGKAP' => 'Dr.-Ing. Bonar Tua Halomoan Marbun',
                'EMAIL' => 'bonar.marbun@tm.itb.ac.id',
                'USERNAME' => 'bonarm',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '197511051980031001',
                'NAMA_LENGKAP' => 'Dedy Irawan, ST, MT',
                'EMAIL' => 'di@tm.itb.ac.id',
                'USERNAME' => 'dedyirawan',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '198009171980031001',
                'NAMA_LENGKAP' => 'Dr. Adityawarman, S.T., M.T.',
                'EMAIL' => 'warman@tm.itb.ac.id',
                'USERNAME' => 'adityawarman',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '197804121980031001',
                'NAMA_LENGKAP' => 'Dr. Amega Yasutra, S.T., M.T.',
                'EMAIL' => 'amega@tm.itb.ac.id',
                'USERNAME' => 'amegayasutra',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
            [
                'NIP_NIM' => '198402221980032001',
                'NAMA_LENGKAP' => 'Silvya Dewi Rahmawati, S.Si., M.Si., Ph.D',
                'EMAIL' => 'sdr@tm.itb.ac.id',
                'USERNAME' => 'silvyadr',
                'PASSWORD' => Hash::make('dosen123'),
                'JENIS_USER' => 'Pembimbing',
            ],
        ];

        // =============================================
        // 10 STAF TU from list
        // =============================================
        $staff = [
            // 19. Feri already created as tuprodi demo
            // 20. Haryanta already created as tufs demo
            [
                'NIP_NIM' => '195707121980031001',
                'NAMA_LENGKAP' => 'Idi Suwardi',
                'EMAIL' => 'idi@tm.itb.ac.id',
                'USERNAME' => 'idisuwardi',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '197812131980031001',
                'NAMA_LENGKAP' => 'Irvan Zaenudin, A.Md.',
                'EMAIL' => 'irvan@tm.itb.ac.id',
                'USERNAME' => 'irvanz',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '196204031980031001',
                'NAMA_LENGKAP' => 'Oman Rohman',
                'EMAIL' => 'oman@tm.itb.ac.id',
                'USERNAME' => 'omanr',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '195911181980031001',
                'NAMA_LENGKAP' => 'Rohenda',
                'EMAIL' => 'dohem@tm.itb.ac.id',
                'USERNAME' => 'rohenda',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '195901311980031001',
                'NAMA_LENGKAP' => 'Suparyono',
                'EMAIL' => 'upar@tm.itb.ac.id',
                'USERNAME' => 'suparyono',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '196208281980032001',
                'NAMA_LENGKAP' => 'Tuti Suhaemi',
                'EMAIL' => 'tuti@tm.itb.ac.id',
                'USERNAME' => 'tutis',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '198506161980031001',
                'NAMA_LENGKAP' => 'Witan Ermintan, A.Md.',
                'EMAIL' => 'witan@tm.itb.ac.id',
                'USERNAME' => 'witan',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
            [
                'NIP_NIM' => '196608151980031001',
                'NAMA_LENGKAP' => 'Agus Rahmansyah, SE.',
                'EMAIL' => 'agus@tm.itb.ac.id',
                'USERNAME' => 'agusrahmansyah',
                'PASSWORD' => Hash::make('staff123'),
                'STATUS_PEGAWAI' => 'Tendik',
                'JENIS_USER' => 'TU Prodi',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ],
        ];

        // Insert demo accounts
        foreach ($demoAccounts as $u) {
            DB::table('t_user')->insert(array_merge($common, $u));
        }

        // Insert 18 dosen
        foreach ($dosen as $u) {
            DB::table('t_user')->insert(array_merge($common, [
                'STATUS_PEGAWAI' => 'Dosen',
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'KODE_FS' => '13321002',
            ], $u));
        }

        // Insert staff
        foreach ($staff as $u) {
            DB::table('t_user')->insert(array_merge($common, $u));
        }

        // Insert extra mahasiswa
        foreach ($extraMhs as $u) {
            DB::table('t_user')->insert(array_merge($common, $u));
        }
    }
}
