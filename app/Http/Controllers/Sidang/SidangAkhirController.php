<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SidangAkhirController extends Controller
{
    public function index()
    {
        $data = DummySidang::sidangAkhir();
        return view('sidang.sidang-akhir', compact('data'));
    }
}
