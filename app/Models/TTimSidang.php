<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TTimSidang extends Model
{
    use HasFactory;
    protected $table = 'T_tim_sidang';
    public $timestamps = false;
    protected $fillable = [
        'tahapan_sidang', 'id_judul', 'status_tim_sidang', 'id_user_penilai',
        'nip', 'Nama', 'tgl_create', 'tgl_update'
    ];
}
