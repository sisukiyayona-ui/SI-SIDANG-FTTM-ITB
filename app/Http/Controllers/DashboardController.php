<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\Notification;
use App\Models\TUser;
use App\Models\TUserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = TUser::where('JENIS_USER', 'Mahasiswa')->count();
        $totalPenguji   = TUser::where('JENIS_USER', 'Penguji')->count();

        $totalSidang  = TAjuanSidang::whereIn('TAHAPAN_SIDANG', [
            'tahap I', 'tahap II', 'SK I', 'SK II', 'SK III', 'SK IV', 'tahap IV',
        ])->count();

        $totalSeminar = TAjuanSidang::whereIn('TAHAPAN_SIDANG', ['SK I', 'SK II', 'SK III', 'SK IV'])->count();

        $mahasiswaAktif   = TUser::where('JENIS_USER', 'Mahasiswa')->where('STATUS_AKTIF', 'AKTIF')->count();
        $sidangSelesai    = TAjuanSidang::whereNotNull('STATUS_LULUS')->count();
        $seminarBerjalan  = TAjuanSidang::whereIn('TAHAPAN_SIDANG', ['SK I', 'SK II', 'SK III', 'SK IV'])
                            ->whereNull('STATUS_LULUS')
                            ->where('STATUS_AJUKAN_PRODI', 'y')
                            ->count();

        // Nilai TAHAPAN_SIDANG di DB -> label tampilan
        $tahapanGroups = [
            'Ujian Kualifikasi'          => ['tahap I'],
            'Ujian Proposal'             => ['tahap II'],
            'Tahap III (SK)'             => ['SK I', 'SK II', 'SK III', 'SK IV'],
            'Sidang Terbuka / Tertutup'  => ['tahap IV'],
        ];

        $progress = [];
        foreach ($tahapanGroups as $label => $values) {
            $total = TAjuanSidang::whereIn('TAHAPAN_SIDANG', $values)->count();
            $completed = TAjuanSidang::whereIn('TAHAPAN_SIDANG', $values)
                ->whereNotNull('STATUS_LULUS')->count();
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

        $chartYears = [];
        $chartTahapData = [];
        $currentYear = (int)Carbon::now()->format('Y');
        foreach ($tahapanGroups as $label => $values) {
            $chartTahapData[$label] = [];
        }
        for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
            $chartYears[] = $y;
            foreach ($tahapanGroups as $label => $values) {
                $q = TAjuanSidang::whereIn('TAHAPAN_SIDANG', $values)
                    ->where('STATUS_LULUS', 'lulus')
                    ->whereYear('TGL_SIDANG', $y);
                if ($user['role'] === 'TU Prodi') {
                    $q->where('KODE_PRODI', $user['kode_prodi']);
                }
                $chartTahapData[$label][] = $q->count();
            }
        }

        return view('dashboard.index', compact(
            'totalMahasiswa', 'totalSidang', 'totalSeminar', 'totalPenguji',
            'mahasiswaAktif', 'sidangSelesai', 'seminarBerjalan',
            'progress', 'recentActivities',
            'chartYears', 'chartTahapData'
        ));
    }

    public function gantiRolePage()
    {
        return view('ganti-role');
    }

    public function gantiRole(Request $request)
    {
        $user = session('auth_user');
        $role = $request->input('role');

        if ($user && $role) {
            $hasRole = TUserRole::where('ID_USER', $user['id'])
                ->where('ROLE', $role)
                ->exists();

            if ($hasRole) {
                session(['auth_user.role' => $role]);
                session(['auth_user.default_role' => $role]);
                session()->flash('success', 'Role berhasil diganti menjadi ' . $role . '.');
            } else {
                session()->flash('error', 'Role tidak valid.');
            }
        }

        return redirect()->back();
    }
}
