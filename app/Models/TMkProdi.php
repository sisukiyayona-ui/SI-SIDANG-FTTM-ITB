<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Model;

class TMkProdi extends Model
{
    use HasUppercaseColumns;

    protected $table = 't_mk_prodi';

    public $timestamps = false;

    protected $fillable = [
        'KODE_PRODI',
        'KODE_MK',
        'TGL_BUAT',
        'TGL_UPDATE',
        'NAMA_PRODI',
        'TAHAPAN_SIDANG',
    ];

    protected $casts = [
        'TGL_BUAT' => 'date',
        'TGL_UPDATE' => 'date',
    ];
}