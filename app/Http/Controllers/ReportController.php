<?php

namespace App\Http\Controllers;

use App\Data\DummySidang;

class ReportController extends Controller
{
    public function index()
    {
        $rekapSidang = collect(DummySidang::reportRekapSidang());
        $rekapMahasiswa = collect(DummySidang::reportMahasiswa());
        $rekapKelulusan = collect(DummySidang::reportKelulusan());
        $stats = DummySidang::statistics();

        return view('report.index', compact('rekapSidang', 'rekapMahasiswa', 'rekapKelulusan', 'stats'));
    }
}
