<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TCekPersyaratan extends Model
{
    use HasFactory;
    protected $table = 'T_cek_persyaratan';
    public $timestamps = false;
    protected $fillable = [
        'tahapan_sidang', 'id_judul', 'id_syarat_sidang', 'Persyaratan',
        'status_lengkap', 'link_file', 'tgl_buat', 'tgl_update'
    ];
}
