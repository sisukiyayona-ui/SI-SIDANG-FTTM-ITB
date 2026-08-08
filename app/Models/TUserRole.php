<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TUserRole extends Model
{
    use \App\Models\Concerns\HasUppercaseColumns;

    protected $table = 't_user_role';

    public $timestamps = false;

    protected $fillable = [
        'ID_USER',
        'ROLE',
        'STATUS_DEFAULT',
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
