<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TEmailQueue extends Model
{
    protected $table = 't_email_queue';

    public $timestamps = true;

    protected $fillable = [
        'TIPE',
        'ID_AJUAN_SIDANG',
        'ID_USER_PENERIMA',
        'EMAIL',
        'NAMA_PENERIMA',
        'PAYLOAD',
        'STATUS',
        'ATTEMPTS',
        'LAST_ERROR',
        'TGL_KIRIM',
        'NEXT_ATTEMPT_AT',
    ];

    protected $casts = [
        'PAYLOAD' => 'array',
        'TGL_KIRIM' => 'datetime',
        'NEXT_ATTEMPT_AT' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('STATUS', 'pending');
    }
}
