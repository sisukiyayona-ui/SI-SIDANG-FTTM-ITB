<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TJudulTemp extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_JUDUL_TEMP';

    public $timestamps = false;

    protected $fillable = [
        'ID_JUDUL',
        'JUDUL',
        'ID_USER_MHS',
        'NIM',
        'JUDUL_BARU',
        'TAHAP_PERUBAHAN',
        'ALASAN_PERUBAHAN',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function judul()
    {
        return $this->belongsTo(TJudul::class, 'ID_JUDUL');
    }

    public function user()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_MHS');
    }
}
