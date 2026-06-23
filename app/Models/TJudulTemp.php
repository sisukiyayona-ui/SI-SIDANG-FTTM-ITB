<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TJudulTemp extends Model
{
    use HasFactory;
    protected $table = 'T_judul_temp';
    public $timestamps = false;
    protected $fillable = [
        'id_judul', 'Judul', 'id_user_mhs', 'Nim', 'judul_baru',
        'tahap_perubahan', 'alasan_perubahan', 'tgl_create', 'tgl_update'
    ];
}
