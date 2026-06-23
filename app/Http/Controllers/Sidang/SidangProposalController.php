<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Data\DummySidang;

class SidangProposalController extends Controller
{
    public function index()
    {
        $data = DummySidang::sidangProposal();
        return view('sidang.sidang-proposal', compact('data'));
    }
}
