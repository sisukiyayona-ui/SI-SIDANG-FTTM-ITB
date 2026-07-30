<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TJudul;
use App\Models\TAjuanSidang;
use App\Models\TTimSidang;
use App\Models\TPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;

class CetakController extends Controller
{
    public function cetakForm(Request $request, $idJudul, $tahapan)
    {
        $user = session('auth_user');

        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

        $tahapan = str_replace('_', ' ', $tahapan);

        $judul = TJudul::find($idJudul);
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan.'], 404);
        }

        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->first();

        if (!$ajuan) {
            return response()->json(['error' => 'Ajuan sidang tidak ditemukan.'], 404);
        }

        $timSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        $pembimbings = $timSidang->filter(function ($tim) {
            return str_contains($tim->status_tim_sidang ?? '', 'Pembimbing');
        })->values();

        $pembimbingUtama = $pembimbings->get(0)->NAMA ?? '-';
        $pembimbing1 = $pembimbings->get(1)->NAMA ?? '-';
        $pembimbing2 = $pembimbings->get(2)->NAMA ?? '-';

        $idTimSidang = $request->input('id_tim_sidang');
        if ($idTimSidang) {
            $penilaian = TPenilaian::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->where('id_tim_sidang', $idTimSidang)
                ->get();
        } else {
            $penilaian = TPenilaian::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->get();
        }

        $rataNilai = $penilaian->avg('NILAI');
        $rataNilaiFormatted = $rataNilai ? number_format($rataNilai, 2) : '-';

        $catatanParts = $penilaian->pluck('CATATAN')->filter()->values();
        $catatan1 = $catatanParts->get(0) ?? '';
        $catatan2 = $catatanParts->get(1) ?? '';
        $catatan3 = $catatanParts->get(2) ?? '';

        $penilaiRecord = $penilaian->first();
        $tanggal = $penilaiRecord ? ($penilaiRecord->TGL_UPDATE ?? $penilaiRecord->TGL_CREATE ?? '') : '';
        if ($tanggal) {
            $tanggal = \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
        }
        $penilaiNama = $penilaiRecord->NAMA ?? $user['nama_lengkap'] ?? '-';
        $nip = $penilaiRecord->NIP ?? $user['nip_nim'] ?? '-';

        $judulText = $judul->JUDUL ?? '';
        $maxLen = 55;
        $judulPart1 = $judulText;
        $judulPart2 = '';
        if (mb_strlen($judulText) > $maxLen) {
            $breakPos = mb_strrpos(mb_substr($judulText, 0, $maxLen), ' ');
            if ($breakPos === false) {
                $breakPos = $maxLen;
            }
            $judulPart1 = mb_substr($judulText, 0, $breakPos);
            $judulPart2 = mb_substr($judulText, $breakPos + 1);
        }

        $templatePath = base_path('template/pROPOSAL/Form. 302.3 TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            $tp->setValue('judul', ': ' . $judulPart1);
            $tp->setValue('judul_lanjutan', $judulPart2);
            $tp->setValue('nama_mhs', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim', $ajuan->NIM ?? '-');
            $tp->setValue('pembimbing_utama', $pembimbingUtama);
            $tp->setValue('pembimbing_1', $pembimbing1);
            $tp->setValue('pembimbing_2', $pembimbing2);
            $tp->setValue('rata_nilai', $rataNilaiFormatted);
            $tp->setValue('catatan', $catatan1);
            $tp->setValue('catatan_lanjutan', $catatan2);
            $tp->setValue('catatan_lanjutan2', $catatan3);
            $tp->setValue('tanggal', $tanggal);
            $tp->setValue('penilai', $penilaiNama);
            $tp->setValue('ttd', $penilaiNama);
            $tp->setValue('nip', $nip);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template: ' . $e->getMessage()], 500);
        }

        $filename = 'Form_302_3_' . str_replace(' ', '_', $tahapan) . '_' . $idJudul . '_' . time() . '.docx';
        $storagePath = storage_path('app/public/uploads/' . $filename);
        $tp->saveAs($storagePath);

        return response()->download($storagePath, $filename)->deleteFileAfterSend(true);
    }
}
