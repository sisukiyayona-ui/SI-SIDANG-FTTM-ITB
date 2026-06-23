<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TPointPenilaian extends Model
{
    use HasFactory;
    protected $table = 'T_point_penilaian';
    public $timestamps = false;
    protected $fillable = [
        'Penilaian', 'id_prodi', 'kode_prodi', 'nama_prodi',
        'tahapan_sidang', 'status_aktif', 'strata', 'tgl_create', 'tgl_update'
    ];

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'id_prodi');
    }
}
