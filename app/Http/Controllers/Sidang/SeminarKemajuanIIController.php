<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SeminarKemajuanIIController extends Controller
{
    public function index()
    {
        $data = DummySidang::seminarKemajuanII();
        return view('sidang.seminar-kemajuan-ii', compact('data'));
    }
}
