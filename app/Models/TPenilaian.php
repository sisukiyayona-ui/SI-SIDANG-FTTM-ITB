<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPenilaian extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_PENILAIAN';

    public $timestamps = false;

    protected $fillable = [
        'ID_AJUAN',
        'ID_JUDUL',
        'JUDUL',
        'NIM',
        'NAMA_MHS',
        'TAHAPAN_SIDANG',
        'ID_TIM_SIDANG',
        'ID_USER_PENILAI',
        'STATUS_TIM_SIDANG',
        'NIP',
        'NAMA',
        'ID_PENILAIAN',
        'NAMA_PENILAIAN',
        'NILAI',
        'CATATAN',
        'STATUS_SUBMIT',
        'TGL_CREATE',
        'TGL_UPDATE',
        'ID_USER_CREATE',
        'NAMA_USER_CREATE',
        'NO_FORM',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'NILAI' => 'decimal:2',
    ];

    public function ajuan()
    {
        return $this->belongsTo(TAjuanSidang::class, 'ID_AJUAN');
    }

    public function judul()
    {
        return $this->belongsTo(TJudul::class, 'ID_JUDUL');
    }

    public function timSidang()
    {
        return $this->belongsTo(TTimSidang::class, 'ID_TIM_SIDANG');
    }

    public function userPenilai()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_PENILAI');
    }

    public function userCreate()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_CREATE');
    }

    public function pointPenilaian()
    {
        return $this->belongsTo(TPointPenilaian::class, 'ID_PENILAIAN');
    }
}
