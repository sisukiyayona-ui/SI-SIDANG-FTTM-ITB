<?php

namespace Database\Factories;

use App\Models\TProdi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TProdiFactory extends Factory
{
    protected $model = TProdi::class;

    public function definition(): array
    {
        return [
            'kode_prodi' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{2}'),
            'nama_prodi' => $this->faker->randomElement([
                'Teknik Informatika',
                'Teknik Komputer',
                'Sistem Informasi',
                'Teknik Elektro',
                'Matematika',
                'Fisika',
            ]),
            'status_aktif' => 'aktif',
            'tgl_create' => now()->toDateString(),
            'tgl_update' => now()->toDateString(),
        ];
    }
}