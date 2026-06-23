<?php

namespace Database\Factories;

use App\Models\TJudulTemp;
use App\Models\TUser;
use App\Models\TJudul;
use Illuminate\Database\Eloquent\Factories\Factory;

class TJudulTempFactory extends Factory
{
    protected $model = TJudulTemp::class;

    public function definition(): array
    {
        $judul = TJudul::inRandomOrder()->first();
        $mahasiswa = TUser::where('jenis_user', 'mahasiswa')->inRandomOrder()->first();
        
        return [
            'id_judul' => $judul ? $judul->id : TJudul::factory(),
            'Judul' => $judul ? $judul->Judul : $this->faker->sentence(6),
            'id_user_mhs' => $mahasiswa ? $mahasiswa->id : TUser::factory(),
            'Nim' => $mahasiswa ? $mahasiswa->nip_nim : $this->faker->regexify('[0-9]{10,15}'),
            'judul_baru' => $this->faker->sentence(6),
            'tahap_perubahan' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'alasan_perubahan' => $this->faker->sentence(10),
            'tgl_create' => now()->toDateString(),
            'tgl_update' => now()->toDateString(),
        ];
    }
}