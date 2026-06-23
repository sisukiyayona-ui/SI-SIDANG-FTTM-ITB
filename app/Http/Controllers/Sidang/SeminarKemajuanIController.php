<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SeminarKemajuanIController extends Controller
{
    public function index()
    {
        $data = DummySidang::seminarKemajuanI();
        return view('sidang.seminar-kemajuan-i', compact('data'));
    }
}
