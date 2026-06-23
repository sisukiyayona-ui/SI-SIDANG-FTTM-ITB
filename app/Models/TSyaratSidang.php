<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TSyaratSidang extends Model
{
    use HasFactory;
    protected $table = 'T_Syarat_sidang';
    public $timestamps = false;
    protected $fillable = [
        'nama_persyaratan', 'id_prodi', 'kode_prodi', 'nama_prodi',
        'tahapan_sidang', 'status_aktif', 'strata', 'tgl_create', 'tgl_update'
    ];

    protected $casts = [
        'tgl_create' => 'date',
        'tgl_update' => 'date',
    ];

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'id_prodi');
    }
}
