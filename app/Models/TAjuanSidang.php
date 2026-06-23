<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TAjuanSidang extends Model
{
    use HasFactory;
    protected $table = 'T_ajuan_sidang';
    public $timestamps = false;
    protected $fillable = [
        'id_user', 'Nim', 'nama_mhs', 'angkatan', 'id_judul', 'Judul', 'tahapan_sidang', 'Strata',
        'tgl_sidang', 'waktu_sidang', 'ruang_sidang', 'status_lulus', 'sk_pembimbing', 'status_ajukan_mhs',
        'sk_penguji', 'no_undangan', 'status_ajukan_prodi', 'no_BA_sidang', 'sk_lulus', 'tgl_create', 'tgl_update',
        'id_user_create', 'nama_user_create', 'thn_create', 'id_prodi', 'kode_prodi', 'nama_prodi'
    ];
}
