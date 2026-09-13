<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TUserProdi extends Model
{
    protected $table = 't_user_prodi';
    protected $primaryKey = 'ID';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'ID_USER',
        'ID_PRODI',
        'TGL_BUAT',
        'TGL_UPDATE',
    ];
}
