<?php

namespace Database\Factories;

use App\Models\TCekPersyaratan;
use App\Models\TJudul;
use App\Models\TSyaratSidang;
use Illuminate\Database\Eloquent\Factories\Factory;

class TCekPersyaratanFactory extends Factory
{
    protected $model = TCekPersyaratan::class;

    public function definition(): array
    {
        $judul = TJudul::inRandomOrder()->first();
        $syarat = TSyaratSidang::inRandomOrder()->first();
        
        return [
            'tahapan_sidang' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'id_judul' => $judul ? $judul->id : TJudul::factory(),
            'id_syarat_sidang' => $syarat ? $syarat->id : TSyaratSidang::factory(),
            'Persyaratan' => $syarat ? $syarat->nama_persyaratan : $this->faker->sentence(3),
            'status_lengkap' => $this->faker->randomElement(['y', 't']),
            'link_file' => $this->faker->optional(0.5)->url(),
            'tgl_buat' => now(),
            'tgl_update' => now(),
        ];
    }
}