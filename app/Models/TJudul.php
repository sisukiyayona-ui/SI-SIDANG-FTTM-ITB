<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TJudul extends Model
{
    use HasFactory;
    protected $table = 'T_judul';
    public $timestamps = false;
    protected $fillable = ['Judul', 'id_user_mhs', 'Nim', 'thn_create', 'tgl_create', 'tgl_update'];

    public function mahasiswa()
    {
        return $this->belongsTo(TUser::class, 'id_user_mhs');
    }
}
