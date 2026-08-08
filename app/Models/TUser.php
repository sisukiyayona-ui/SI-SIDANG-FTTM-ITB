<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TUser extends Authenticatable
{
    use HasFactory, Notifiable, HasUppercaseColumns;

    protected $table = 't_user';

    public $timestamps = false;

    protected $fillable = [
        'NIP_NIM',
        'NAMA_LENGKAP',
        'EMAIL',
        'AKUN_INA',
        'USERNAME',
        'PASSWORD',
        'STATUS_PEGAWAI',
        'JENIS_USER',
        'KODE_PRODI',
        'NAMA_PRODI',
        'KODE_FS',
        'NAMA_FS',
        'STRATA',
        'THN_ANGKATAN',
        'STATUS_AKTIF',
        'STATUS_APPROVE',
        'TGL_CREATE',
        'TGL_UPDATE',
        'STATUS_KAPRODI',
        'STATUS_DEKAN',
        'STATUS_WDA',
        'SIGNATURE',
        'ASAL_INSTANSI',
        'INSTANSI',
    ];

    protected $hidden = [
        'PASSWORD',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'THN_ANGKATAN' => 'integer',
    ];

    public function getAuthPassword(): string
    {
        // Gunakan accessor agar trait HasUppercaseColumns bekerja
        return (string) ($this->PASSWORD ?? $this->password ?? '');
    }

    public function judul()
    {
        return $this->hasMany(TJudul::class, 'ID_USER_MHS');
    }

    public function ajuanSidang()
    {
        return $this->hasMany(TAjuanSidang::class, 'ID_USER');
    }

    public function ajuanSidangCreated()
    {
        return $this->hasMany(TAjuanSidang::class, 'ID_USER_CREATE');
    }

    public function timSidang()
    {
        return $this->hasMany(TTimSidang::class, 'ID_USER_PENILAI');
    }

    public function penilaian()
    {
        return $this->hasMany(TPenilaian::class, 'ID_USER_PENILAI');
    }

    public function penilaianCreated()
    {
        return $this->hasMany(TPenilaian::class, 'ID_USER_CREATE');
    }

    public function prodi()
    {
        return $this->belongsTo(TProdi::class, 'KODE_PRODI', 'KODE_PRODI');
    }

    public function userRoles()
    {
        return $this->hasMany(TUserRole::class, 'ID_USER', 'id');
    }

    public function roles(): array
    {
        return $this->userRoles->pluck('ROLE')->all();
    }

    public function defaultRole(): ?string
    {
        $default = $this->userRoles->firstWhere('STATUS_DEFAULT', 't');
        if ($default) {
            return $default->ROLE;
        }
        return $this->userRoles->isNotEmpty() ? $this->userRoles->first()->ROLE : null;
    }
}
