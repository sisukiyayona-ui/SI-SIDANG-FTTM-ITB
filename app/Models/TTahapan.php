<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TTahapan extends Model
{
    use HasFactory;
    protected $table = 'T_tahapan';
    public $timestamps = false;
    protected $fillable = ['Tahapan', 'Kode_tahap', 'strata', 'tgl_buat', 'tgl_update'];
}
