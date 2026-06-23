<?php

namespace Database\Factories;

use App\Models\TPenilaian;
use App\Models\TAjuanSidang;
use App\Models\TJudul;
use App\Models\TTimSidang;
use App\Models\TUser;
use App\Models\TPointPenilaian;
use Illuminate\Database\Eloquent\Factories\Factory;

class TPenilaianFactory extends Factory
{
    protected $model = TPenilaian::class;

    public function definition(): array
    {
        $ajuan = TAjuanSidang::inRandomOrder()->first();
        $judul = TJudul::inRandomOrder()->first();
        $tim = TTimSidang::inRandomOrder()->first();
        $dosen = TUser::where('jenis_user', 'dosen')->inRandomOrder()->first();
        $point = TPointPenilaian::inRandomOrder()->first();
        $creator = TUser::where('jenis_user', 'admin')->inRandomOrder()->first();
        
        return [
            'id_ajuan' => $ajuan ? $ajuan->id : TAjuanSidang::factory(),
            'id_judul' => $judul ? $judul->id : TJudul::factory(),
            'Judul' => $judul ? $judul->Judul : $this->faker->sentence(6),
            'Nim' => $judul && $judul->mahasiswa ? $judul->mahasiswa->nip_nim : $this->faker->regexify('[0-9]{10,15}'),
            'nama_mhs' => $judul && $judul->mahasiswa ? $judul->mahasiswa->nama_lengkap : $this->faker->name(),
            'tahapan_sidang' => $judul ? $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']) : 'Proposal',
            'id_tim_sidang' => $tim ? $tim->id : TTimSidang::factory(),
            'id_user_penilai' => $dosen ? $dosen->id : TUser::factory(),
            'status_tim_sidang' => $this->faker->randomElement(['ketua', 'anggota', 'penguji']),
            'nip' => $dosen ? $dosen->nip_nim : $this->faker->regexify('[0-9]{10,15}'),
            'Nama' => $dosen ? $dosen->nama_lengkap : $this->faker->name(),
            'id_penilaian' => $point ? $point->id : TPointPenilaian::factory(),
            'nama_penilaian' => $point ? $point->Penilaian : $this->faker->randomElement(['Presentasi', 'Materi', 'Tanya Jawab']),
            'Nilai' => $this->faker->optional(0.8)->randomFloat(2, 50, 100),
            'catatan' => $this->faker->optional(0.5)->sentence(),
            'status_submit' => 't',
            'tgl_create' => now(),
            'tgl_update' => now(),
            'id_user_create' => $creator ? $creator->id : ($dosen ? $dosen->id : TUser::factory()),
            'nama_user_create' => $creator ? $creator->nama_lengkap : ($dosen ? $dosen->nama_lengkap : $this->faker->name()),
        ];
    }
}