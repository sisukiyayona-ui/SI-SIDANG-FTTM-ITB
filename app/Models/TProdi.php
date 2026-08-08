<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TProdi extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 't_prodi';

    public $timestamps = false;

    protected $fillable = [
        'KODE_PRODI',
        'NAMA_PRODI',
        'TGL_CREATE',
        'TGL_UPDATE',
        'STATUS_AKTIF',
        'KODE_FS',
        'NAMA_FS',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(TUser::class, 'KODE_PRODI', 'KODE_PRODI');
    }

    public function syaratSidang()
    {
        return $this->hasMany(TSyaratSidang::class, 'ID_PRODI');
    }

    public function pointPenilaian()
    {
        return $this->hasMany(TPointPenilaian::class, 'ID_PRODI');
    }

    public function ajuanSidang()
    {
        return $this->hasMany(TAjuanSidang::class, 'ID_PRODI');
    }
}
