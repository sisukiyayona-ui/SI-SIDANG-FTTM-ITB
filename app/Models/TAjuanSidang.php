<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAjuanSidang extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_AJUAN_SIDANG';

    public $timestamps = false;

    protected $fillable = [
        'ID_USER',
        'NIM',
        'NAMA_MHS',
        'ANGKATAN',
        'ID_JUDUL',
        'JUDUL',
        'TAHAPAN_SIDANG',
        'STRATA',
        'TGL_SIDANG',
        'WAKTU_SIDANG',
        'RUANG_SIDANG',
        'STATUS_LULUS',
        'STATUS_AJUKAN_MHS',
        'NO_UNDANGAN',
        'STATUS_AJUKAN_PRODI',
        'NO_BA_SIDANG',
        'SK_LULUS',
        'TGL_CREATE',
        'TGL_UPDATE',
        'ID_USER_CREATE',
        'NAMA_USER_CREATE',
        'THN_CREATE',
        'ID_PRODI',
        'KODE_PRODI',
        'NAMA_PRODI',
        'TGL_UNDANGAN',
        'TGL_PENGUMPULAN',
        'TGL_PENELAAH',
        'NO_SURAT_PENELAAH',
        'EMAIL_SURAT',
    ];

    protected $casts = [
        'TGL_SIDANG' => 'date',
        'WAKTU_SIDANG' => 'datetime',
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'TGL_UNDANGAN' => 'date',
        'TGL_PENGUMPULAN' => 'date',
        'TGL_PENELAAH' => 'date',
        'ANGKATAN' => 'integer',
        'THN_CREATE' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(TUser::class, 'ID_USER');
    }

    public function userCreate()
    {
        return $this->belongsTo(TUser::class, 'ID_USER_CREATE');
    }

    public function judul()
    {
        return $this->belongsTo(TJudul::class, 'ID_JUDUL');
    }

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'ID_PRODI');
    }

    public function cekPersyaratan()
    {
        return $this->hasMany(TCekPersyaratan::class, 'ID_JUDUL');
    }

    public function timSidang()
    {
        return $this->hasMany(TTimSidang::class, 'ID_JUDUL');
    }

    public function penilaian()
    {
        return $this->hasMany(TPenilaian::class, 'ID_AJUAN');
    }
}
