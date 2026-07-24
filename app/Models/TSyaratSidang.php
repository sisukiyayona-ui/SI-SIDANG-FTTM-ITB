<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSyaratSidang extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 't_syarat_sidang';

    public $timestamps = false;

    protected $fillable = [
        'NAMA_PERSYARATAN',
        'ID_PRODI',
        'KODE_PRODI',
        'NAMA_PRODI',
        'TAHAPAN_SIDANG',
        'STATUS_AKTIF',
        'STRATA',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'ID_PRODI');
    }

    public function cekPersyaratan()
    {
        return $this->hasMany(TCekPersyaratan::class, 'ID_SYARAT_SIDANG');
    }
}
