<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPointPenilaian extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 't_point_penilaian';

    public $timestamps = false;

    protected $fillable = [
        'PENILAIAN',
        'ID_PRODI',
        'KODE_PRODI',
        'NAMA_PRODI',
        'TAHAPAN_SIDANG',
        'STATUS_AKTIF',
        'STRATA',
        'TGL_CREATE',
        'TGL_UPDATE',
        'NO_FORM',
        'STATUS_CATATAN',
        'KETERANGAN',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'ID_PRODI');
    }

    public function penilaianRecords()
    {
        return $this->hasMany(TPenilaian::class, 'ID_PENILAIAN');
    }
}
