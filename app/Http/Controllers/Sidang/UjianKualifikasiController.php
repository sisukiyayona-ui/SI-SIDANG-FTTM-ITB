<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class UjianKualifikasiController extends Controller
{
    public function index()
    {
        $data = DummySidang::ujianKualifikasi();
        return view('sidang.ujian-kualifikasi', compact('data'));
    }
}
