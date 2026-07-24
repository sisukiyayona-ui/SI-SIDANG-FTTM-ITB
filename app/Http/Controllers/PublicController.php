<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $jadwalSidang = TAjuanSidang::query()
            ->whereNotNull('ID_JUDUL')
            ->whereNull('STATUS_LULUS')
            ->whereNotNull('TGL_SIDANG')
            ->where('STATUS_AJUKAN_PRODI', 'y')
            ->orderBy('TGL_SIDANG', 'asc')
            ->get();

        $jenisSidangList = TAjuanSidang::query()
            ->whereNotNull('ID_JUDUL')
            ->whereNull('STATUS_LULUS')
            ->whereNotNull('TGL_SIDANG')
            ->where('STATUS_AJUKAN_PRODI', 'y')
            ->select('TAHAPAN_SIDANG')
            ->distinct()
            ->pluck('TAHAPAN_SIDANG');

        return view('welcome', compact('jadwalSidang', 'jenisSidangList'));
    }
}
