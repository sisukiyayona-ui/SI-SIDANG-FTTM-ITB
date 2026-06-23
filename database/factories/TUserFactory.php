<?php

namespace Database\Factories;

use App\Models\TUser;
use App\Models\TProdi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TUserFactory extends Factory
{
    protected $model = TUser::class;

    public function definition(): array
    {
        $jenisUser = $this->faker->randomElement(['mahasiswa', 'dosen', 'admin', 'koor_prodi']);
        $prodi = TProdi::inRandomOrder()->first();
        
        return [
            'nip_nim' => $this->faker->unique()->regexify('[0-9]{10,15}'),
            'nama_lengkap' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'akun_ina' => $jenisUser === 'mahasiswa' ? $this->faker->userName() : null,
            'Username' => $this->faker->unique()->userName(),
            'Password' => bcrypt('password'),
            'status_pegawai' => $jenisUser === 'dosen' ? $this->faker->randomElement(['aktif', 'tidak aktif']) : null,
            'jenis_user' => $jenisUser,
            'kode_prodi' => $prodi ? $prodi->kode_prodi : null,
            'nama_prodi' => $prodi ? $prodi->nama_prodi : null,
            'kode_fs' => $this->faker->randomElement(['FS01', 'FS02', 'FS03']),
            'nama_fs' => $this->faker->randomElement(['Fakultas Sains', 'Fakultas Teknik', 'Fakultas Ilmu Komputer']),
            'strata' => $jenisUser === 'mahasiswa' ? $this->faker->randomElement(['S1', 'S2', 'S3']) : $this->faker->randomElement(['S1', 'S2', 'S3']),
            'thn_angkatan' => $jenisUser === 'mahasiswa' ? $this->faker->year() : null,
            'status_aktif' => 'aktif',
            'status_approve' => 'y',
            'tgl_create' => now(),
            'tgl_update' => now(),
        ];
    }
}