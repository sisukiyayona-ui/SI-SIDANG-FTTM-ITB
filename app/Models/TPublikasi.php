<?php

namespace App\Models;

use App\Models\Concerns\HasUppercaseColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPublikasi extends Model
{
    use HasFactory, HasUppercaseColumns;

    protected $table = 't_publikasi';

    public $timestamps = false;

    protected $fillable = [
        'PUBLIKASI',
        'STATUS',
        'ID_AJUAN_SIDANG',
        'TGL_CREATE',
        'TGL_UPDATE',
    ];

    protected $casts = [
        'TGL_CREATE' => 'date',
        'TGL_UPDATE' => 'date',
        'ID_AJUAN_SIDANG' => 'integer',
    ];

    public function ajuan()
    {
        return $this->belongsTo(TAjuanSidang::class, 'ID_AJUAN_SIDANG');
    }
}
