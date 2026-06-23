<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SeminarKemajuanIVController extends Controller
{
    public function index()
    {
        $data = DummySidang::seminarKemajuanIV();
        return view('sidang.seminar-kemajuan-iv', compact('data'));
    }
}
