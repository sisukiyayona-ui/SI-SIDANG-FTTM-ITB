<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TJudul;
use App\Models\TAjuanSidang;
use App\Models\TTimSidang;
use App\Models\TPenilaian;
use App\Models\TUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

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

    public function suratKesediaanPenelaah(Request $request, $idJudul, $tahapan)
    {
        $judul = TJudul::find($idJudul);
        if (!$judul) {
            abort(404, 'Judul tidak ditemukan.');
        }

        $tahapan = str_replace('_', ' ', $tahapan);
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->first();
        if (!$ajuan) {
            abort(404, 'Ajuan sidang tidak ditemukan.');
        }

        $timSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        $pembimbings = $timSidang->filter(function ($tim) {
            return str_contains($tim->status_tim_sidang ?? '', 'Pembimbing');
        })->values();

        $pengujis = $timSidang->filter(function ($tim) {
            return str_contains($tim->status_tim_sidang ?? '', 'Penguji');
        })->values();

        $pembimbingNames = $pembimbings->map(fn($t) => $t->NAMA ?? '-')->values();
        if ($pembimbingNames->isEmpty()) {
            $pembimbingNames->push('-');
        }

        $pengujiList = $pengujis->map(function ($t) {
            $institusi = optional($t->userPenilai)->ASAL_INSTANSI
                ?: optional($t->userPenilai)->INSTANSI
                ?: '';
            return [
                'nama' => $t->NAMA ?? '-',
                'institusi' => $institusi,
            ];
        })->values();

        $dekan = TUser::where('STATUS_DEKAN', 't')->first();

        $prodi = $ajuan->NAMA_PRODI ?? optional($ajuan->prodi)->NAMA_PRODI ?? '-';

        $fmt = function ($date, $default = '-') {
            return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : $default;
        };

        $templatePath = storage_path('app/templates/surat_kesediaan_penelaah.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template surat kesediaan penelaah tidak ditemukan.');
        }

        try {
            $tp = new TemplateProcessor($templatePath);
            $tp->setMacroChars('{', '}');

            $rows = [];
            foreach ($pembimbingNames->values() as $i => $nama) {
                $rows[] = ['n' => (string) ($i + 1), 'pembimbing' => $nama];
            }
            $tp->cloneRowAndSetValues('pembimbing', $rows);

            $tp->setValue('no_surat', $ajuan->NO_SURAT_PENELAAH ?? '-');
            $tp->setValue('tgl_surat', $fmt($ajuan->TGL_PENELAAH));
            $tp->setValue('nama_mhs', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim', $ajuan->NIM ?? '-');
            $tp->setValue('judul', $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('penguji_1', $pengujiList->get(0)['nama'] ?? '-');
            $tp->setValue('institusi_1', $pengujiList->get(0)['institusi'] ?? '');
            $tp->setValue('penguji_2', $pengujiList->get(1)['nama'] ?? '-');
            $tp->setValue('institusi_2', $pengujiList->get(1)['institusi'] ?? '');
            $tp->setValue('prodi', $prodi);
            $tp->setValue('tgl_hasil', $fmt($ajuan->TGL_HASIL_PENELAHAN));
            $tp->setValue('dekan', $dekan->NAMA_LENGKAP ?? '');
            $tp->setValue('dekan_nip', $dekan->NIP_NIM ?? '');
        } catch (\Exception $e) {
            abort(500, 'Gagal memproses template surat: ' . $e->getMessage());
        }

        $filename = 'Surat_Kesediaan_Tim_Penelaah_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.pdf';

        return $this->exportDocxToPdf($tp, $filename);
    }

    public function cetakUndangan(Request $request, $idJudul, $tahapan)
    {
        $judul = TJudul::find($idJudul);
        if (!$judul) {
            abort(404, 'Judul tidak ditemukan.');
        }

        $tahapan = str_replace('_', ' ', $tahapan);
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->first();
        if (!$ajuan) {
            abort(404, 'Ajuan sidang tidak ditemukan.');
        }

        $timSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        $t = strtolower($tahapan);
        $isSidang = $t === 'tahap iv'
            || str_contains($t, 'sidang')
            || str_contains($t, 'terbuka')
            || str_contains($t, 'tertutup');

        if ($isSidang) {
            return $this->cetakUndanganSidang($judul, $ajuan, $timSidang);
        }

        return $this->cetakUndanganKemajuan($judul, $ajuan, $timSidang, $tahapan);
    }

    private function cetakUndanganSidang($judul, $ajuan, $timSidang)
    {
        $namaUnik = function ($statusRule) use ($timSidang) {
            return $timSidang
                ->filter(fn($t) => str_contains($t->status_tim_sidang ?? '', $statusRule))
                ->filter(fn($t) => trim((string) ($t->NAMA ?? '')) !== '' && trim((string) ($t->NAMA ?? '')) !== 'Pembimbing' && trim((string) ($t->NAMA ?? '')) !== 'Penguji')
                ->unique(fn($t) => ($t->NIP ?? '') ?: ($t->NAMA ?? ''))
                ->values();
        };

        $pembimbing = $namaUnik('Pembimbing');
        $penguji = $namaUnik('Penguji');

        $ketua = $timSidang
            ->filter(fn($t) => trim((string) ($t->status_tim_sidang ?? '')) === 'Ketua Sidang')
            ->filter(fn($t) => trim((string) ($t->NAMA ?? '')) !== '' && trim((string) ($t->NAMA ?? '')) !== 'Penguji')
            ->first();

        $dekan = TUser::where('STATUS_DEKAN', 't')->first();

        $prodi = $ajuan->NAMA_PRODI ?? optional($ajuan->prodi)->NAMA_PRODI ?? '-';

        $mulai = $ajuan->waktu_sidang;
        $selesai = $ajuan->waktu_selesai;
        $pukulPendahuluan = ($mulai && $selesai)
            ? \Carbon\Carbon::parse($mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($selesai)->format('H:i')
            : '-';

        $pukulDoktor = '-';
        if ($selesai) {
            $doktorStart = \Carbon\Carbon::parse($selesai);
            $pukulDoktor = $doktorStart->format('H:i') . ' – ' . $doktorStart->copy()->addMinutes(150)->format('H:i');
        }

        $tempat = $ajuan->ruang_sidang ?? '';

        $templatePath = storage_path('app/templates/undangan_sidang.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template undangan sidang tidak ditemukan.');
        }

        try {
            $tp = new TemplateProcessor($templatePath);
            $tp->setMacroChars('{', '}');

            $tp->setValue('nama', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim', $ajuan->NIM ?? '-');
            $tp->setValue('prodi', $prodi);
            $tp->setValue('judul', $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('pembimbing_1', $pembimbing->get(0)->NAMA ?? '-');
            $tp->setValue('pembimbing_2', $pembimbing->get(1)->NAMA ?? '');
            $tp->setValue('penguji_1', $penguji->get(0)->NAMA ?? '-');
            $tp->setValue('penguji_2', $penguji->get(1)->NAMA ?? '');
            $tp->setValue('penguji_3', $penguji->get(2)->NAMA ?? '');
            $tp->setValue('ketua_sidang', $ketua->NAMA ?? '-');
            $tp->setValue('waktu', $ajuan->tgl_sidang
                ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
                : '-');
            $tp->setValue('pukul_pendahuluan', $pukulPendahuluan);
            $tp->setValue('tempat_pendahuluan', $tempat);
            $tp->setValue('pukul_doktor', $pukulDoktor);
            $tp->setValue('tempat_doktor', $tempat);
            $tp->setValue('dekan', $dekan->NAMA_LENGKAP ?? '');
        } catch (\Exception $e) {
            abort(500, 'Gagal memproses template undangan: ' . $e->getMessage());
        }

        $filename = 'Undangan_Sidang_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.pdf';

        return $this->exportDocxToPdf($tp, $filename);
    }

    private function cetakUndanganKemajuan($judul, $ajuan, $timSidang, $tahapan)
    {
        $t = strtolower($tahapan);
        if (str_contains($t, 'proposal') || $t === 'tahap ii') {
            $perihal = 'Ujian Proposal';
            $seminar = 'Ujian Proposal';
        } elseif (str_contains($t, 'sk') && preg_match('/sk\s*-?\s*(iv|iii|ii|i)\b/i', $tahapan, $m)) {
            $rom = strtoupper($m[1]);
            $perihal = "Seminar Kemajuan $rom";
            $seminar = "Seminar Kemajuan $rom (SK-$rom)";
        } elseif ($t === 'tahap i') {
            $perihal = 'Ujian Kualifikasi';
            $seminar = 'Ujian Kualifikasi';
        } else {
            $perihal = ucwords($tahapan);
            $seminar = $perihal;
        }

        $ketua = $timSidang
            ->filter(fn($x) => trim((string) ($x->status_tim_sidang ?? '')) === 'Ketua Sidang')
            ->filter(fn($x) => trim((string) ($x->NAMA ?? '')) !== '' && trim((string) ($x->NAMA ?? '')) !== 'Penguji')
            ->first();

        $pembimbing = $timSidang
            ->filter(fn($x) => str_contains($x->status_tim_sidang ?? '', 'Pembimbing'))
            ->filter(fn($x) => trim((string) ($x->NAMA ?? '')) !== '' && trim((string) ($x->NAMA ?? '')) !== 'Pembimbing')
            ->unique(fn($x) => ($x->NIP ?? '') ?: ($x->NAMA ?? ''))
            ->values();

        $penguji = $timSidang
            ->filter(fn($x) => str_contains($x->status_tim_sidang ?? '', 'Penguji'))
            ->filter(fn($x) => trim((string) ($x->NAMA ?? '')) !== '' && trim((string) ($x->NAMA ?? '')) !== 'Penguji')
            ->unique(fn($x) => ($x->NIP ?? '') ?: ($x->NAMA ?? ''))
            ->values();

        $rows = [];
        if ($ketua) {
            $rows[] = ['nama' => $ketua->NAMA, 'jabatan' => 'Ketua Sidang'];
        }
        foreach ($pembimbing->values() as $i => $p) {
            $rows[] = ['nama' => $p->NAMA, 'jabatan' => $i === 0 ? 'Pembimbing (Ketua)' : 'Pembimbing (Anggota)'];
        }
        foreach ($penguji->values() as $p) {
            $inst = optional($p->userPenilai)->ASAL_INSTANSI
                ?: optional($p->userPenilai)->INSTANSI
                ?: '';
            $rows[] = ['nama' => $p->NAMA, 'jabatan' => $inst ? "Penguji ($inst)" : 'Penguji'];
        }

        $kppsIds = DB::table('t_user_role')->where('ROLE', 'KPPS')->pluck('ID_USER');
        if ($kppsIds->isNotEmpty()) {
            $kppsUsers = TUser::whereIn('id', $kppsIds)
                ->where('NAMA_LENGKAP', '!=', '')
                ->orderBy('NAMA_LENGKAP')
                ->get();
            foreach ($kppsUsers as $ku) {
                $rows[] = ['nama' => $ku->NAMA_LENGKAP, 'jabatan' => 'Anggota KPPs'];
            }
        }

        $kepadaRows = '';
        $i = 1;
        foreach ($rows as $r) {
            $kepadaRows .= '<w:r><w:t xml:space="preserve">' . htmlspecialchars($i . '. ' . $r['nama'], ENT_QUOTES, 'UTF-8') . '</w:t></w:r>'
                . '<w:r><w:tab/></w:r>'
                . '<w:r><w:t xml:space="preserve">' . htmlspecialchars($r['jabatan'], ENT_QUOTES, 'UTF-8') . '</w:t></w:r>'
                . '<w:r><w:br/></w:r>';
            $i++;
        }

        $waktu = $ajuan->waktu_sidang && $ajuan->waktu_selesai
            ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i') . ' – ' . \Carbon\Carbon::parse($ajuan->waktu_selesai)->format('H:i') . ' WIB'
            : '-';

        $prodi = $ajuan->NAMA_PRODI ?? optional($ajuan->prodi)->NAMA_PRODI ?? '-';

        $wda = TUser::where('STATUS_WDA', 't')->first();
        if (!$wda) {
            $wda = TUser::where('STATUS_DEKAN', 't')->first();
        }

        $templatePath = storage_path('app/templates/undangan_kemajuan.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template undangan kemajuan tidak ditemukan.');
        }

        try {
            $tp = new TemplateProcessor($templatePath);
            $tp->setMacroChars('{', '}');

            $tp->setValue('nama', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim', $ajuan->NIM ?? '-');
            $tp->setValue('prodi', $prodi);
            $tp->setValue('judul', $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('nomor', $ajuan->NO_UNDANGAN ?? '');
            $tp->setValue('tgl_surat', $ajuan->TGL_UNDANGAN
                ? \Carbon\Carbon::parse($ajuan->TGL_UNDANGAN)->translatedFormat('d F Y')
                : '');
            $tp->setValue('perihal', $perihal);
            $tp->setValue('seminar', $seminar);
            $tp->setValue('ketua_sidang', $ketua->NAMA ?? '-');
            $tp->setValue('hari', $ajuan->tgl_sidang
                ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
                : '-');
            $tp->setValue('waktu', $waktu);
            $tp->setValue('tempat', $ajuan->ruang_sidang ?? '');
            $tp->setValue('wda', $wda ? $wda->NAMA_LENGKAP : '');
            $tp->setValue('wda_nip', $wda ? $wda->NIP_NIM : '');
        } catch (\Exception $e) {
            abort(500, 'Gagal memproses template undangan kemajuan: ' . $e->getMessage());
        }

        $filename = 'Undangan_' . str_replace(' ', '_', $perihal) . '_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.pdf';

        $postSave = null;
        if ($kepadaRows !== '') {
            $postSave = function ($docxPath) use ($kepadaRows) {
                $z = new \ZipArchive();
                if ($z->open($docxPath) === true) {
                    $xml = $z->getFromName('word/document.xml');
                    $xml = str_replace('{kepada}', $kepadaRows, $xml);
                    $z->deleteName('word/document.xml');
                    $z->addFromString('word/document.xml', $xml);
                    $z->close();
                }
            };
        }

        return $this->exportDocxToPdf($tp, $filename, $postSave);
    }

    private function exportDocxToPdf(TemplateProcessor $tp, $filename, callable $postSave = null)
    {
        $docxPath = storage_path('app/uploads/' . str_replace('.pdf', '.docx', $filename));
        $pdfPath = storage_path('app/uploads/' . $filename);

        try {
            if (!is_dir(dirname($docxPath))) {
                \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
            }
            $tp->saveAs($docxPath);

            if ($postSave) {
                $postSave($docxPath);
            }

            if (!class_exists('COM')) {
                abort(500, 'Ekstensi PHP COM tidak tersedia untuk konversi PDF.');
            }

            $word = new \COM('Word.Application');
            $word->Visible = false;
            $word->DisplayAlerts = 0;
            $doc = $word->Documents->Open($docxPath, false, true);
            $doc->ExportAsFixedFormat($pdfPath, 17);
            $doc->Close(false);
            $word->Quit();
            unset($word);
        } catch (\Throwable $e) {
            return response()->download($docxPath, str_replace('.pdf', '.docx', $filename))->deleteFileAfterSend(true);
        }

        return response()->download($pdfPath, $filename)->deleteFileAfterSend(true);
    }
}
