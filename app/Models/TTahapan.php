<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTahapan extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_TAHAPAN';

    public $timestamps = false;

    protected $fillable = [
        'TAHAPAN',
        'KODE_TAHAP',
        'STRATA',
        'TGL_BUAT',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_BUAT' => 'date',
        'TGL_UPDATE' => 'date',
    ];
}
