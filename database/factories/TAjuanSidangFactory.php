<?php

namespace Database\Factories;

use App\Models\TAjuanSidang;
use App\Models\TUser;
use App\Models\TJudul;
use App\Models\TProdi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TAjuanSidangFactory extends Factory
{
    protected $model = TAjuanSidang::class;

    public function definition(): array
    {
        $mahasiswa = TUser::where('jenis_user', 'mahasiswa')->inRandomOrder()->first();
        $judul = TJudul::inRandomOrder()->first();
        $prodi = $mahasiswa ? TProdi::where('kode_prodi', $mahasiswa->kode_prodi)->first() : null;
        $creator = TUser::where('jenis_user', 'admin')->inRandomOrder()->first();
        
        return [
            'id_user' => $mahasiswa ? $mahasiswa->id : TUser::factory(),
            'Nim' => $mahasiswa ? $mahasiswa->nip_nim : '1234567890',
            'nama_mhs' => $mahasiswa ? $mahasiswa->nama_lengkap : $this->faker->name(),
            'angkatan' => $mahasiswa && $mahasiswa->thn_angkatan ? $mahasiswa->thn_angkatan : $this->faker->year(),
            'id_judul' => $judul ? $judul->id : TJudul::factory(),
            'Judul' => $judul ? $judul->Judul : $this->faker->sentence(6),
            'tahapan_sidang' => $this->faker->randomElement(['Proposal', 'Seminar', 'Skripsi', 'Disertasi']),
            'Strata' => $mahasiswa ? $mahasiswa->strata : 'S1',
            'tgl_sidang' => $this->faker->optional()->date(),
            'waktu_sidang' => $this->faker->optional()->time(),
            'ruang_sidang' => $this->faker->optional()->randomElement(['Ruang A', 'Ruang B', 'Ruang C', 'Ruang D'], null),
            'status_lulus' => $this->faker->optional()->randomElement(['lulus', 'tidak lulus'], null),
            'sk_pembimbing' => $this->faker->optional()->regexify('SK[0-9]{5}'),
            'status_ajukan_mhs' => $this->faker->randomElement(['y', 't']),
            'sk_penguji' => $this->faker->optional()->regexify('SK[0-9]{5}'),
            'no_undangan' => $this->faker->optional()->regexify('UND[0-9]{5}'),
            'status_ajukan_prodi' => $this->faker->randomElement(['y', 't']),
            'no_BA_sidang' => $this->faker->optional()->regexify('BA[0-9]{5}'),
            'sk_lulus' => $this->faker->optional()->regexify('SKL[0-9]{5}'),
            'tgl_create' => now(),
            'tgl_update' => now(),
            'id_user_create' => $creator ? $creator->id : ($mahasiswa ? $mahasiswa->id : TUser::factory()),
            'nama_user_create' => $creator ? $creator->nama_lengkap : ($mahasiswa ? $mahasiswa->nama_lengkap : $this->faker->name()),
            'thn_create' => $this->faker->year(),
            'id_prodi' => $prodi ? $prodi->id : 1,
            'kode_prodi' => $prodi ? $prodi->kode_prodi : 'TI01',
            'nama_prodi' => $prodi ? $prodi->nama_prodi : 'Teknik Informatika',
        ];
    }
}