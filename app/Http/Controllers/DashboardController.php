<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\Notification;
use App\Models\TUser;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = TUser::where('JENIS_USER', 'Mahasiswa')->count();
        $totalPenguji   = TUser::where('JENIS_USER', 'Penguji')->count();

        $totalSidang  = TAjuanSidang::whereIn('TAHAPAN_SIDANG', [
            'Sidang Akhir', 'Sidang Proposal', 'Ujian Kualifikasi'
        ])->count();

        $totalSeminar = TAjuanSidang::where('TAHAPAN_SIDANG', 'like', 'Seminar Kemajuan%')->count();

        $mahasiswaAktif   = TUser::where('JENIS_USER', 'Mahasiswa')->where('STATUS_AKTIF', 'AKTIF')->count();
        $sidangSelesai    = TAjuanSidang::whereNotNull('STATUS_LULUS')->count();
        $seminarBerjalan  = TAjuanSidang::where('TAHAPAN_SIDANG', 'like', 'Seminar Kemajuan%')
                            ->whereNull('STATUS_LULUS')
                            ->where('STATUS_AJUKAN_PRODI', 'y')
                            ->count();

        $progress = [];
        $tahapanList = ['Ujian Kualifikasi', 'Sidang Proposal', 'Seminar Kemajuan', 'Sidang Akhir'];
        foreach ($tahapanList as $label) {
            if ($label === 'Seminar Kemajuan') {
                $total = TAjuanSidang::where('TAHAPAN_SIDANG', 'like', 'Seminar Kemajuan%')->count();
                $completed = TAjuanSidang::where('TAHAPAN_SIDANG', 'like', 'Seminar Kemajuan%')
                    ->whereNotNull('STATUS_LULUS')->count();
            } else {
                $total = TAjuanSidang::where('TAHAPAN_SIDANG', $label)->count();
                $completed = TAjuanSidang::where('TAHAPAN_SIDANG', $label)
                    ->whereNotNull('STATUS_LULUS')->count();
            }
            $progress[] = [
                'label'     => $label,
                'total'     => $total ?: 1,
                'completed' => $completed,
            ];
        }

        $notifications = Notification::orderBy('created_at', 'desc')->limit(5)->get();
        $recentActivities = $notifications->map(function ($n) {
            return [
                'user'     => $n->title,
                'activity' => $n->message ?? '',
                'time'     => $n->created_at->diffForHumans(),
            ];
        });

        $user = session('auth_user');

        $tahapLabels = ['Tahap I', 'Tahap II', 'Tahap III', 'Tahap IV'];
        $chartYears = [];
        $chartTahapData = [];
        $currentYear = (int)Carbon::now()->format('Y');
        foreach ($tahapLabels as $tahap) {
            $chartTahapData[$tahap] = [];
        }
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $chartYears[] = $y;
            foreach ($tahapLabels as $tahap) {
                $q = TAjuanSidang::where('TAHAPAN_SIDANG', $tahap)
                    ->where('STATUS_LULUS', 'lulus')
                    ->whereYear('TGL_SIDANG', $y);
                if ($user['role'] === 'TU Prodi') {
                    $q->where('KODE_PRODI', $user['kode_prodi']);
                }
                $chartTahapData[$tahap][] = $q->count();
            }
        }

        return view('dashboard.index', compact(
            'totalMahasiswa', 'totalSidang', 'totalSeminar', 'totalPenguji',
            'mahasiswaAktif', 'sidangSelesai', 'seminarBerjalan',
            'progress', 'recentActivities',
            'chartYears', 'chartTahapData'
        ));
    }
}
