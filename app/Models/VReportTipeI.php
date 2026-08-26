<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VReportTipeI extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_report_tipe_i';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_judul',
        'tahun',
        'NIM',
        'nama_mahasiswa',
        'JUDUL',
        'NIP',
        'pembimbing_penguji',
        'STATUS_TIM_SIDANG',
        'tahapan_sidang',
        'status_lulus',
        'strata',
        'kode_prodi',
        'URUTAN',
        'tgl_sidang',
        'waktu_sidang',
        'ruang_sidang',
        'tgl_create',
        'tgl_update',
        'nilai_rata2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tgl_sidang' => 'date',
        'tgl_create' => 'datetime',
        'tgl_update' => 'datetime',
    ];
}
