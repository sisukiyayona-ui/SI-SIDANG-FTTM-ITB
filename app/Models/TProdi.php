<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TProdi extends Model
{
    use HasFactory;
    protected $table = 'T_prodi';
    public $timestamps = false;
    protected $fillable = ['kode_prodi', 'nama_prodi', 'status_aktif', 'tgl_create', 'tgl_update'];
}
