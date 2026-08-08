<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Model;

class TFs extends Model
{
    use HasUppercaseColumns;

    protected $table = 't_fs';

    public $timestamps = false;

    protected $fillable = [
        'KODE_FS',
        'NAMA_FS',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(TUser::class, 'KODE_FS', 'KODE_FS');
    }
}
