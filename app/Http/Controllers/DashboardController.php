<?php

namespace App\Http\Controllers;

use App\Models\TAjuanSidang;
use App\Models\Notification;
use App\Models\TProdi;
use App\Models\TUser;
use App\Models\TUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $chartQuery = DB::table('v_dashboard_s3')
            ->select('TAHUN')
            ->selectRaw('SUM(jum_tahap1) as tahap1')
            ->selectRaw('SUM(jum_tahap2) as tahap2')
            ->selectRaw('SUM(jum_tahap3) as tahap3')
            ->selectRaw('SUM(jum_tahap4) as tahap4')
            ->groupBy('TAHUN')
            ->orderBy('TAHUN', 'asc');

        if ($user['role'] === 'TU Prodi') {
            $prodi = TProdi::where('KODE_PRODI', $user['kode_prodi'])->first();
            if ($prodi) {
                $chartQuery->where('id_prodi', $prodi->id);
            }
        }

        $chartRows = $chartQuery->get();

        $chartYears = [];
        $chartTahapData = [
            'Ujian Kualifikasi' => [],
            'Ujian Proposal' => [],
            'Tahap III (SK)' => [],
            'Sidang Terbuka / Tertutup' => [],
        ];

        foreach ($chartRows as $row) {
            $chartYears[] = $row->TAHUN;
            $chartTahapData['Ujian Kualifikasi'][] = $row->tahap1;
            $chartTahapData['Ujian Proposal'][] = $row->tahap2;
            $chartTahapData['Tahap III (SK)'][] = $row->tahap3;
            $chartTahapData['Sidang Terbuka / Tertutup'][] = $row->tahap4;
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
