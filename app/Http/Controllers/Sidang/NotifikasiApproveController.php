<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Mail\NotifikasiApproveAjuanMail;
use App\Models\TAjuanSidang;
use App\Models\TEmailQueue;
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

        $ajuan = TAjuanSidang::find($idAjuan);
        if (!$ajuan) {
            return response()->json(['success' => false, 'message' => 'Ajuan sidang tidak ditemukan'], 404);
        }

        $result = $this->antreKeKpps($ajuan);

        if (isset($result['empty'])) {
            return response()->json([
                'success' => true,
                'message' => 'Semua anggota KPPS sudah approve.',
                'queued' => 0,
                'skipped' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$result['queued']} email masuk antrean, akan dikirim otomatis oleh cron.",
            'queued' => $result['queued'],
            'skipped' => $result['skipped'],
            'queued_names' => $result['queued_names'],
            'skipped_names' => $result['skipped_names'],
        ]);
    }

    /**
     * Masukkan email notifikasi approve ke antrean (tanpa kirim langsung).
     * Dipanggil saat ajukan KPPS / tombol kirim notifikasi.
     */
    public function antreKeKpps(TAjuanSidang $ajuan): array
    {
        $recipients = $this->kumpulkanPenerimaKpps($ajuan);

        if ($recipients['empty']) {
            return ['empty' => true, 'queued' => 0, 'skipped' => 0, 'queued_names' => [], 'skipped_names' => []];
        }

        $approveUrl = route('login');
        $queued = 0;
        $skipped = 0;
        $queuedNames = [];
        $skippedNames = [];

        foreach ($recipients['list'] as $kpps) {
            $email = $kpps->EMAIL;
            if (!$email) {
                $skipped++;
                $skippedNames[] = ($kpps->NAMA ?? '-') . ' (tidak ada email)';
                continue;
            }

            $alreadyQueued = TEmailQueue::where('TIPE', 'notifikasi_approve')
                ->where('ID_AJUAN_SIDANG', $ajuan->id)
                ->where('EMAIL', $email)
                ->where('STATUS', 'pending')
                ->exists();

            if ($alreadyQueued) {
                $skipped++;
                $skippedNames[] = ($kpps->NAMA ?? '-') . ' (masih di antrean, belum terkirim)';
                continue;
            }

            TEmailQueue::create([
                'TIPE' => 'notifikasi_approve',
                'ID_AJUAN_SIDANG' => $ajuan->id,
                'ID_USER_PENERIMA' => $kpps->ID_USER,
                'EMAIL' => $email,
                'NAMA_PENERIMA' => $kpps->NAMA,
                'PAYLOAD' => [
                    'nama_mhs' => $ajuan->NAMA_MHS,
                    'nim' => $ajuan->NIM,
                    'judul' => $ajuan->JUDUL,
                    'tahapan' => $ajuan->TAHAPAN_SIDANG,
                    'strata' => $ajuan->STRATA,
                    'nama_kpps' => $kpps->NAMA,
                    'nip_kpps' => $kpps->NIP,
                    'approve_url' => $approveUrl,
                ],
                'STATUS' => 'pending',
                'NEXT_ATTEMPT_AT' => now(),
            ]);

            $queued++;
            $queuedNames[] = $kpps->NAMA;
        }

        return [
            'queued' => $queued,
            'skipped' => $skipped,
            'queued_names' => $queuedNames,
            'skipped_names' => $skippedNames,
        ];
    }

    /**
     * Kirim langsung (dipakai jika sewaktu-waktu perlu sinkron).
     * Untuk request web sebaiknya pakai antreKeKpps().
     */
    public function kirimKeKpps(TAjuanSidang $ajuan)
    {
        $recipients = $this->kumpulkanPenerimaKpps($ajuan);

        if ($recipients['empty']) {
            return ['empty' => true, 'sent' => 0, 'skipped' => 0, 'sent_names' => [], 'skipped_names' => []];
        }

        $approveUrl = route('login');
        $sent = 0;
        $skipped = 0;
        $sentNames = [];
        $skippedNames = [];

        foreach ($recipients['list'] as $kpps) {
            $email = $kpps->EMAIL;
            if (!$email) {
                $skipped++;
                $skippedNames[] = ($kpps->NAMA ?? '-') . ' (tidak ada email)';
                continue;
            }

            $mailData = [
                'nama_mhs' => $ajuan->NAMA_MHS,
                'nim' => $ajuan->NIM,
                'judul' => $ajuan->JUDUL,
                'tahapan' => $ajuan->TAHAPAN_SIDANG,
                'strata' => $ajuan->STRATA,
                'nama_kpps' => $kpps->NAMA,
                'nip_kpps' => $kpps->NIP,
                'approve_url' => $approveUrl,
            ];

            try {
                Mail::to($email)->send(new NotifikasiApproveAjuanMail($mailData));

                DB::table('t_notif_approve_log')->insert([
                    'ID_AJUAN_SIDANG' => $ajuan->id,
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
                $skippedNames[] = ($kpps->NAMA ?? '-') . ' (gagal kirim)';
            }
        }

        return [
            'sent' => $sent,
            'skipped' => $skipped,
            'sent_names' => $sentNames,
            'skipped_names' => $skippedNames,
        ];
    }

    /**
     * @return array{empty?:bool, list:\Illuminate\Support\Collection}
     */
    private function kumpulkanPenerimaKpps(TAjuanSidang $ajuan): array
    {
        $idAjuan = $ajuan->id;
        $kodeProdi = $ajuan->KODE_PRODI ?? $ajuan->kode_prodi ?? null;

        $kppsQuery = DB::table('t_kpps as k')
            ->leftJoin('t_app_ajuan_sidang as app', function ($join) use ($idAjuan) {
                $join->on('k.ID_USER', '=', 'app.ID_USER')
                    ->where('app.ID_AJUAN_SIDANG', '=', $idAjuan);
            })
            ->leftJoin('t_user as u', 'k.ID_USER', '=', 'u.ID')
            ->where('k.STATUS_AKTIF', 'AKTIF')
            ->whereNull('app.ID');

        if (!empty($kodeProdi)) {
            $kppsQuery->where('k.KODE_PRODI', $kodeProdi);
        }

        $kppsList = $kppsQuery
            ->select(
                'k.ID_USER',
                DB::raw('MAX(k.NIP) as NIP'),
                DB::raw('MAX(k.NAMA) as NAMA'),
                DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(k.STATUS_TIM ORDER BY CASE WHEN k.STATUS_TIM = 'Ketua' THEN 1 WHEN k.STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END SEPARATOR ','), ',', 1) as STATUS_TIM"),
                DB::raw('MAX(u.EMAIL) as EMAIL')
            )
            ->groupBy('k.ID_USER')
            ->get();

        $statusTimPriority = ['Ketua' => 1, 'Sekretaris' => 2];
        $kppsList = $kppsList->sortBy(function ($item) use ($statusTimPriority) {
            $priority = $statusTimPriority[trim($item->STATUS_TIM ?? '')] ?? 3;
            return str_pad($priority, 2, '0', STR_PAD_LEFT) . '|' . strtolower($item->NAMA ?? '');
        })->values();

        return ['empty' => $kppsList->isEmpty(), 'list' => $kppsList];
    }
}
