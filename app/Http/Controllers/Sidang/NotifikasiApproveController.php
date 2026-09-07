<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Mail\NotifikasiApproveAjuanMail;
use App\Models\TAjuanSidang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifikasiApproveController extends Controller
{
    public function kirim(Request $request)
    {
        $user = session('auth_user');
        if (!$user || !in_array($user['role'] ?? '', ['TU Prodi', 'FS'])) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses.'], 403);
        }

        $request->validate([
            'id_ajuan_sidang' => 'required|integer',
            'id_judul' => 'required|integer',
        ]);

        $idAjuan = (int) $request->id_ajuan_sidang;
        $idJudul = (int) $request->id_judul;

        $ajuan = TAjuanSidang::find($idAjuan);
        if (!$ajuan) {
            return response()->json(['success' => false, 'message' => 'Ajuan sidang tidak ditemukan'], 404);
        }

        $tahapan = $ajuan->TAHAPAN_SIDANG;
        $strata = $ajuan->STRATA;

        $kppsList = DB::table('t_kpps as k')
            ->leftJoin('t_app_ajuan_sidang as app', function ($join) use ($idAjuan) {
                $join->on('k.ID_USER', '=', 'app.ID_USER')
                     ->where('app.ID_AJUAN_SIDANG', '=', $idAjuan);
            })
            ->leftJoin('t_user as u', 'k.ID_USER', '=', 'u.ID')
            ->whereNull('app.ID')
            ->select('k.ID_USER', 'k.NIP', 'k.NAMA', 'k.STATUS_TIM', 'u.EMAIL')
            ->orderByRaw("CASE WHEN k.STATUS_TIM = 'Ketua' THEN 1 WHEN k.STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END")
            ->orderBy('k.NAMA')
            ->get();

        if ($kppsList->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Semua anggota KPPS sudah approve.',
                'sent' => 0,
                'skipped' => 0,
            ]);
        }

        $approveUrl = route('sidang.approve-ajuan.index', ['strata' => $strata]);

        $sent = 0;
        $skipped = 0;
        $sentNames = [];
        $skippedNames = [];

        foreach ($kppsList as $kpps) {
            $email = $kpps->EMAIL;
            if (!$email) {
                $skipped++;
                $skippedNames[] = $kpps->NAMA . ' (tidak ada email)';
                continue;
            }

            $mailData = [
                'nama_mhs' => $ajuan->NAMA_MHS,
                'nim' => $ajuan->NIM,
                'judul' => $ajuan->JUDUL,
                'tahapan' => $tahapan,
                'strata' => $strata,
                'nama_kpps' => $kpps->NAMA,
                'nip_kpps' => $kpps->NIP,
                'approve_url' => $approveUrl,
            ];

            try {
                Mail::to($email)->send(new NotifikasiApproveAjuanMail($mailData));

                DB::table('t_notif_approve_log')->insert([
                    'ID_AJUAN_SIDANG' => $idAjuan,
                    'ID_USER_KPPS' => $kpps->ID_USER,
                    'EMAIL' => $email,
                    'TGL_KIRIM' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $sent++;
                $sentNames[] = $kpps->NAMA;
            } catch (\Exception $e) {
                Log::error('NotifikasiApprove: Gagal kirim ke ' . $email . ' - ' . $e->getMessage());
                $skipped++;
                $skippedNames[] = $kpps->NAMA . ' (gagal kirim)';
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengirim {$sent} email notifikasi.",
            'sent' => $sent,
            'skipped' => $skipped,
            'sent_names' => $sentNames,
            'skipped_names' => $skippedNames,
        ]);
    }
}
