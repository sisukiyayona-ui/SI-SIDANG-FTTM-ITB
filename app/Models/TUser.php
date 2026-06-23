<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TUser extends Authenticatable
{
    use HasFactory;
    protected $table = 'T_User';
    public $timestamps = false;

    protected $fillable = [
        'nip_nim', 'nama_lengkap', 'email', 'akun_ina', 'Username', 'Password',
        'status_pegawai', 'jenis_user', 'kode_prodi', 'nama_prodi', 'kode_fs', 'nama_fs',
        'strata', 'thn_angkatan', 'status_aktif', 'status_approve', 'tgl_create', 'tgl_update'
    ];

    public function getAuthPassword()
    {
        return $this->Password;
    }
}
