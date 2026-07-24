<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSk extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 'T_SK';

    public $timestamps = false;

    protected $fillable = [
        'NO_SK',
        'ID_JUDUL',
        'TAHAPAN_SIDANG',
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

    public function timSidang()
    {
        return $this->hasMany(TTimSidang::class, 'ID_SK');
    }
}
