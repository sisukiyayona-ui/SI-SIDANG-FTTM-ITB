<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Model;

class TKpps extends Model
{
    use HasUppercaseColumns;

    protected $table = 't_kpps';

    public $timestamps = false;

    protected $fillable = [
        'ID_USER',
        'NIP',
        'NAMA',
        'KODE_PRODI',
        'NAMA_PRODI',
        'KODE_FS',
        'NAMA_FS',
        'STATUS_AKTIF',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(TUser::class, 'ID_USER', 'id');
    }
}
