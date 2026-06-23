<?php

namespace Database\Factories;

use App\Models\TTahapan;
use Illuminate\Database\Eloquent\Factories\Factory;

class TTahapanFactory extends Factory
{
    protected $model = TTahapan::class;

    public function definition(): array
    {
        return [
            'Tahapan' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'Kode_tahap' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{2}'),
            'strata' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'tgl_buat' => now(),
            'tgl_update' => now(),
        ];
    }
}