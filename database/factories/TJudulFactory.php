<?php

namespace Database\Factories;

use App\Models\TJudul;
use App\Models\TUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TJudulFactory extends Factory
{
    protected $model = TJudul::class;

    public function definition(): array
    {
        $mahasiswa = TUser::where('jenis_user', 'mahasiswa')->inRandomOrder()->first();
        
        return [
            'Judul' => $this->faker->sentence(6),
            'id_user_mhs' => $mahasiswa ? $mahasiswa->id : TUser::factory(),
            'Nim' => $mahasiswa ? $mahasiswa->nip_nim : $this->faker->regexify('[0-9]{10,15}'),
            'thn_create' => $this->faker->year(),
            'tgl_create' => now()->toDateString(),
            'tgl_update' => now()->toDateString(),
        ];
    }
}