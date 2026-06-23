<?php

namespace App\Http\Controllers;

use App\Data\DummySidang;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = DummySidang::statistics();
        $recentActivities = DummySidang::recentActivity();
        $progress = [
            ['label' => 'Ujian Kualifikasi', 'total' => 30, 'completed' => 22],
            ['label' => 'Sidang Proposal', 'total' => 40, 'completed' => 28],
            ['label' => 'Seminar Kemajuan', 'total' => 60, 'completed' => 35],
            ['label' => 'Sidang Akhir', 'total' => 25, 'completed' => 15],
        ];

        return view('dashboard.index', compact('stats', 'recentActivities', 'progress'));
    }
}
