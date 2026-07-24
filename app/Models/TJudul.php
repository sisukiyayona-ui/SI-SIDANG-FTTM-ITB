<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TJudul extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_JUDUL';

    public $timestamps = false;

    protected $fillable = [
        'JUDUL',
        'ID_USER_MHS',
        'NIM',
        'THN_CREATE',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'THN_CREATE' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_MHS');
    }

    public function judulTemp()
    {
        return $this->hasMany(TJudulTemp::class, 'ID_JUDUL');
    }

    public function ajuanSidang()
    {
        return $this->hasMany(TAjuanSidang::class, 'ID_JUDUL');
    }

    public function timSidang()
    {
        return $this->hasMany(TTimSidang::class, 'ID_JUDUL');
    }

    public function penilaian()
    {
        return $this->hasMany(TPenilaian::class, 'ID_JUDUL');
    }

    public function cekPersyaratan()
    {
        return $this->hasMany(TCekPersyaratan::class, 'ID_JUDUL');
    }

    public function sk()
    {
        return $this->hasMany(TSk::class, 'ID_JUDUL');
    }
}
