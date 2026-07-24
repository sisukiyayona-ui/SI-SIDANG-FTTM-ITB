<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTimSidang extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_TIM_SIDANG';

    public $timestamps = false;

    protected $fillable = [
        'TAHAPAN_SIDANG',
        'ID_JUDUL',
        'STATUS_TIM_SIDANG',
        'ID_USER_PENILAI',
        'NIP',
        'NAMA',
        'TGL_CREATE',
        'TGL_UPDATE',
        'ID_SK',
        'URUTAN',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'URUTAN' => 'integer',
    ];

    public function judul()
    {
        return $this->belongsTo(TJudul::class, 'ID_JUDUL');
    }

    public function userPenilai()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_PENILAI');
    }

    public function sk()
    {
        return $this->belongsTo(TSk::class, 'ID_SK');
    }

    public function penilaian()
    {
        return $this->hasMany(TPenilaian::class, 'ID_TIM_SIDANG');
    }
}
