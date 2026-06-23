<?php

namespace Database\Factories;

use App\Models\TTimSidang;
use App\Models\TUser;
use App\Models\TJudul;
use Illuminate\Database\Eloquent\Factories\Factory;

class TTimSidangFactory extends Factory
{
    protected $model = TTimSidang::class;

    public function definition(): array
    {
        $dosen = TUser::where('jenis_user', 'dosen')->inRandomOrder()->first();
        $judul = TJudul::inRandomOrder()->first();
        
        return [
            'tahapan_sidang' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'id_judul' => $judul ? $judul->id : TJudul::factory(),
            'status_tim_sidang' => $this->faker->randomElement(['ketua', 'anggota', 'penguji']),
            'id_user_penilai' => $dosen ? $dosen->id : TUser::factory(),
            'nip' => $dosen ? $dosen->nip_nim : $this->faker->regexify('[0-9]{10,15}'),
            'Nama' => $dosen ? $dosen->nama_lengkap : $this->faker->name(),
            'tgl_create' => now(),
            'tgl_update' => now(),
        ];
    }
}