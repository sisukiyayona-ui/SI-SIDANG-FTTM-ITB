<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TCekPersyaratan extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_CEK_PERSYARATAN';

    public $timestamps = false;

    protected $fillable = [
        'TAHAPAN_SIDANG',
        'ID_JUDUL',
        'ID_SYARAT_SIDANG',
        'PERSYARATAN',
        'STATUS_LENGKAP',
        'LINK_FILE',
        'TGL_BUAT',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_BUAT' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function judul()
    {
        return $this->belongsTo(TJudul::class, 'ID_JUDUL');
    }

    public function syaratSidang()
    {
        return $this->belongsTo(TSyaratSidang::class, 'ID_SYARAT_SIDANG');
    }
}
