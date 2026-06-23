<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TPenilaian extends Model
{
    use HasFactory;
    protected $table = 'T_penilaian';
    public $timestamps = false;
    protected $fillable = [
        'id_ajuan', 'id_judul', 'Judul', 'Nim', 'nama_mhs', 'tahapan_sidang',
        'id_tim_sidang', 'id_user_penilai', 'status_tim_sidang', 'nip', 'Nama',
        'id_penilaian', 'nama_penilaian', 'Nilai', 'catatan', 'status_submit',
        'tgl_create', 'tgl_update', 'id_user_create', 'nama_user_create'
    ];
}
