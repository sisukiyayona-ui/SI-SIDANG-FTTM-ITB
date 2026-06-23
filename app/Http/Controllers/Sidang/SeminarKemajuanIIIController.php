<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SeminarKemajuanIIIController extends Controller
{
    public function index()
    {
        $data = DummySidang::seminarKemajuanIII();
        return view('sidang.seminar-kemajuan-iii', compact('data'));
    }
}
