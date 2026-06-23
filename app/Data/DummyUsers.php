<?php

namespace App\Data;

class DummyUsers
{
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Admin Sistem',
                'email' => 'admin@fttm.itb.ac.id',
                'username' => 'admin',
                'password' => 'admin123',
                'role' => 'Admin',
                'avatar' => 'https://ui-avatars.com/api/?name=Admin+Sistem&background=2f5597&color=fff&size=128',
                'akun_ina' => 'admin.ina',
                'status' => 'approved',
            ],
            [
                'id' => 2,
                'name' => 'TU Prodi Teknik Perminyakan',
                'email' => 'tuprodi@fttm.itb.ac.id',
                'username' => 'tuprodi',
                'password' => 'prodi123',
                'role' => 'TU Prodi',
                'avatar' => 'https://ui-avatars.com/api/?name=TU+Prodi&background=2f5597&color=fff&size=128',
                'akun_ina' => 'tuprodi.ina',
                'status' => 'approved',
            ],
            [
                'id' => 3,
                'name' => 'TU Fakultas Sains',
                'email' => 'tufs@fttm.itb.ac.id',
                'username' => 'tufs',
                'password' => 'fs123',
                'role' => 'TU FS',
                'avatar' => 'https://ui-avatars.com/api/?name=TU+FS&background=2f5597&color=fff&size=128',
                'akun_ina' => 'tufs.ina',
                'status' => 'approved',
            ],
            [
                'id' => 4,
                'name' => 'Muhammad Rizky',
                'email' => 'rizky@fttm.itb.ac.id',
                'username' => 'mahasiswa',
                'password' => 'mhs123',
                'role' => 'Mahasiswa',
                'avatar' => 'https://ui-avatars.com/api/?name=Muhammad+Rizky&background=2f5597&color=fff&size=128',
                'akun_ina' => 'mrizky.ina',
                'status' => 'approved',
            ],
            [
                'id' => 5,
                'name' => 'Dr. Ahmad Fauzi',
                'email' => 'afauzi@fttm.itb.ac.id',
                'username' => 'pembimbing',
                'password' => 'dosen123',
                'role' => 'Pembimbing',
                'avatar' => 'https://ui-avatars.com/api/?name=Dr.+Ahmad+Fauzi&background=2f5597&color=fff&size=128',
                'akun_ina' => 'afauzi.ina',
                'status' => 'approved',
            ],
            [
                'id' => 6,
                'name' => 'Prof. Siti Rahayu',
                'email' => 'srahayu@fttm.itb.ac.id',
                'username' => 'penguji',
                'password' => 'uji123',
                'role' => 'Penguji',
                'avatar' => 'https://ui-avatars.com/api/?name=Prof.+Siti+Rahayu&background=2f5597&color=fff&size=128',
                'akun_ina' => 'srahayu.ina',
                'status' => 'approved',
            ],
            [
                'id' => 7,
                'name' => 'Dr. Budi Santoso',
                'email' => 'bsantoso@fttm.itb.ac.id',
                'username' => 'monev',
                'password' => 'monev123',
                'role' => 'Monev',
                'avatar' => 'https://ui-avatars.com/api/?name=Dr.+Budi+Santoso&background=2f5597&color=fff&size=128',
                'akun_ina' => 'bsantoso.ina',
                'status' => 'approved',
            ],
        ];
    }

    public static function pendingRegistrations(): array
    {
        return [
            [
                'id' => 8,
                'name' => 'Rina Wijaya',
                'email' => 'rina@fttm.itb.ac.id',
                'username' => 'rinaw',
                'role' => 'Mahasiswa',
                'akun_ina' => 'rinaw.ina',
                'status' => 'pending',
                'registered_at' => '2026-06-20 14:30:00',
            ],
            [
                'id' => 9,
                'name' => 'Doni Prasetyo',
                'email' => 'doni@fttm.itb.ac.id',
                'username' => 'donip',
                'role' => 'Mahasiswa',
                'akun_ina' => 'donip.ina',
                'status' => 'pending',
                'registered_at' => '2026-06-21 09:15:00',
            ],
            [
                'id' => 10,
                'name' => 'Dr. Maya Anggraini',
                'email' => 'manggraini@fttm.itb.ac.id',
                'username' => 'manggraini',
                'role' => 'Pembimbing',
                'akun_ina' => 'manggraini.ina',
                'status' => 'pending',
                'registered_at' => '2026-06-21 11:00:00',
            ],
            [
                'id' => 11,
                'name' => 'Fajar Hidayat',
                'email' => 'fajar@fttm.itb.ac.id',
                'username' => 'fajarh',
                'role' => 'Mahasiswa',
                'akun_ina' => 'fajarh.ina',
                'status' => 'approved',
                'registered_at' => '2026-06-19 08:00:00',
            ],
            [
                'id' => 12,
                'name' => 'Sari Dewi',
                'email' => 'sari@fttm.itb.ac.id',
                'username' => 'sarid',
                'role' => 'Mahasiswa',
                'akun_ina' => 'sarid.ina',
                'status' => 'rejected',
                'registered_at' => '2026-06-18 16:45:00',
                'rejection_reason' => 'Dokumen tidak lengkap',
            ],
        ];
    }
}
