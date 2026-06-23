<?php

namespace Database\Factories;

use App\Models\TPointPenilaian;
use App\Models\TProdi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TPointPenilaianFactory extends Factory
{
    protected $model = TPointPenilaian::class;

    public function definition(): array
    {
        $prodi = TProdi::inRandomOrder()->first();
        
        return [
            'Penilaian' => $this->faker->randomElement([
                'Presentasi',
                'Materi',
                'Tanya Jawab',
                'Originalitas',
                'Karya Tulis',
            ]),
            'id_prodi' => $prodi ? $prodi->id : 1,
            'kode_prodi' => $prodi ? $prodi->kode_prodi : 'TI01',
            'nama_prodi' => $prodi ? $prodi->nama_prodi : 'Teknik Informatika',
            'tahapan_sidang' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'status_aktif' => 'aktif',
            'strata' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'tgl_create' => now()->toDateString(),
            'tgl_update' => now()->toDateString(),
        ];
    }
}