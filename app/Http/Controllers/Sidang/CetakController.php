<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TJudul;
use App\Models\TAjuanSidang;
use App\Models\TTimSidang;
use App\Models\TPenilaian;
use App\Models\TUser;
use App\Models\TPointPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class CetakController extends Controller
{
    /**
     * Cetak form penilaian untuk penilai yang dipilih.
     * Menggunakan template: cetak form penilaian proposal tipe nilai TEMPLATE.docx
     * Mengisi data per-baris (nama_penilaian, keterangan, nilai, catatan) dari DB.
     */
    public function cetakForm(Request $request, $idJudul, $tahapan)
    {
        $user    = session('auth_user');
        $tahapan = str_replace('_', ' ', $tahapan);

        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

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

        $idTimSidang = $request->input('id_tim_sidang');
        $noForm      = $request->input('no_form');

        if (!$idTimSidang) {
            return response()->json(['error' => 'id_tim_sidang diperlukan.'], 400);
        }

        // Data tim sidang (penilai yang dipilih)
        $timSidangRecord = TTimSidang::find($idTimSidang);

        // Semua tim sidang untuk header pembimbing
        $allTimSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)->get();

        $ketuaPembimbing = $allTimSidang->first(fn($t) =>
            in_array(strtolower(trim($t->status_tim_sidang ?? '')), ['ketua pembimbing', 'pembimbing (ketua)'])
        ) ?? $allTimSidang->first(fn($t) =>
            str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
        );

        $anggotaPembimbing = $allTimSidang->filter(fn($t) =>
            str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
            && $t->id !== ($ketuaPembimbing?->id)
        )->values();

        // Ambil data penilaian untuk penilai + no_form dengan join ke TPointPenilaian
        $query = TPenilaian::with(['pointPenilaian'])
            ->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->where('id_tim_sidang', $idTimSidang);
        if ($noForm) {
            $query->where('no_form', $noForm);
        }
        $penilaianRows = $query->orderBy('id_penilaian')->get();
        
        // Load KETERANGAN from TPointPenilaian for existing records
        if ($penilaianRows->isNotEmpty()) {
            foreach ($penilaianRows as $row) {
                if ($row->pointPenilaian) {
                    $row->KETERANGAN = $row->pointPenilaian->KETERANGAN;
                } else {
                    $row->KETERANGAN = '';
                }
            }
        }

        // Jika belum ada record, ambil point penilaian (baris kosong)
        if ($penilaianRows->isEmpty()) {
            $pointQuery = TPointPenilaian::where('tahapan_sidang', $tahapan)
                ->whereIn('status_aktif', ['AKTIF', 'aktif', 'Aktif', 'y', 'Y', 't']);
            if ($noForm) {
                $pointQuery->where('no_form', $noForm);
            }
            $pointRows     = $pointQuery->orderBy('id')->get();
            $penilaianRows = $pointRows->map(fn($p) => (object)[
                'NAMA_PENILAIAN' => $p->PENILAIAN  ?? '',
                'KETERANGAN'     => $p->KETERANGAN ?? '',
                'NILAI'          => '',
                'CATATAN'        => '',
                'NAMA'           => $timSidangRecord?->NAMA ?? '-',
                'NIP'            => $timSidangRecord?->NIP  ?? '-',
                'NO_FORM'        => $p->NO_FORM ?? '',
                'TGL_UPDATE'     => null,
                'TGL_CREATE'     => null,
            ]);
        }

        // Rata-rata nilai
        $nilaiValues = $penilaianRows->pluck('NILAI')->filter(fn($v) => $v !== '' && $v !== null);
        $rataNilai   = $nilaiValues->count() > 0 ? number_format($nilaiValues->avg(), 2) : '';

        // Info penilai & tanggal
        $firstRow    = $penilaianRows->first();
        $tgl         = $firstRow?->TGL_UPDATE ?? $firstRow?->TGL_CREATE ?? null;
        $tanggal     = $tgl ? \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') : '';
        $penilaiNama = $firstRow?->NAMA ?? $timSidangRecord?->NAMA ?? ($user['nama_lengkap'] ?? '-');
        $penilaiNip  = $firstRow?->NIP  ?? $timSidangRecord?->NIP  ?? ($user['nip_nim']      ?? '-');

        // Deteksi tahapan SK (SK I, SK II, SK III, SK IV) -> gunakan template SK
        // Deteksi tahapan Sidang Doktor (tahap IV / Sidang Terbuka/Tertutup) -> template form penilaian sidang akhir
        $tNormal       = strtolower(trim($tahapan));
        $isSk          = preg_match('/^sk\s+(i{1,3}|iv)$/', $tNormal, $skMatch) === 1;
        $skRomawi      = $isSk ? strtoupper($skMatch[1]) : '';
        $isSidangDoktor = preg_match('/^tahap\s+iv$/', $tNormal) === 1;

        // Tanggal cetak (fallback ke tanggal sidang jika penilaian belum diisi)
        $tglCreate = $tanggal;
        if ($tglCreate === '' && $ajuan->tgl_sidang) {
            $tglCreate = \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('d F Y');
        }

        $tglSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';

        // Template
        $templatePath = $isSk
            ? base_path('template/SK-1/form penilaian sk I sd sk III TEMPLATE.docx')
            : ($isSidangDoktor
                ? base_path('template/SIDANG/form penilaian sidang akhir TEMPLATE.docx')
                : base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai TEMPLATE.docx'));
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template cetak penilaian tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            $namaJudul = $ajuan->JUDUL ?? $judul->JUDUL ?? '';

            $tp->setValue('nama_mhs',              $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',                   $ajuan->NIM      ?? '-');
            $tp->setValue('nama_ketua_pembimbing', $ketuaPembimbing?->NAMA ?? '-');
            $tp->setValue('nama_pembimbing_i',     $anggotaPembimbing->get(0)?->NAMA ?? '-');
            $tp->setValue('nama_pembimbing_ii',    $anggotaPembimbing->get(1)?->NAMA ?? '-');
            $tp->setValue('nama_penilai',          $penilaiNama);
            $tp->setValue('tgl_create_penilaian',  $tglCreate);

            if ($isSk) {
                $tp->setValue('judul',                           $namaJudul);
                $tp->setValue('bidang_keahlian_ketua_pembimbing', $ketuaPembimbing?->userPenilai?->KK ?? '');
                $tp->setValue('bidang_keahlian_pembimbing_i',     $anggotaPembimbing->get(0)?->userPenilai?->KK ?? '');
                $tp->setValue('bidang_keahlian_pembimbing_ii',    $anggotaPembimbing->get(1)?->userPenilai?->KK ?? '');
                $tp->setValue('tgl_sidang',                       $tglSidang);
                $tp->setValue('ruang_sidang',                     $ajuan->ruang_sidang ?? '');
                $tp->setValue('nip_penilai',                      $penilaiNip);
            } elseif ($isSidangDoktor) {
                $tp->setValue('no_form',      $noForm ?? '');
                $tp->setValue('judul',        $namaJudul);
                $tp->setValue('tgl_sidang',   $tglSidang);
                $tp->setValue('ruang_sidang', $ajuan->ruang_sidang ?? '');
            } else {
                $tp->setValue('nama_judul',           $namaJudul);
                $tp->setValue('nip',                  $penilaiNip);
                $tp->setValue('tanggal',              $tanggal);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template: ' . $e->getMessage()], 500);
        }

        // Simpan DOCX sementara
        $filename = 'Form_Penilaian_'
            . str_replace(' ', '_', $tahapan) . '_'
            . ($noForm ? str_replace([' ', '.'], '_', $noForm) . '_' : '')
            . $idJudul . '_' . $idTimSidang . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Isi baris penilaian via manipulasi XML
        if ($isSk) {
            // Template SK: isi nomor form, heading romawi, dan expand baris detail penilaian
            $this->postProcessSkForm($docxPath, $noForm ?? '', $skRomawi, $penilaianRows->values()->toArray());
        } elseif ($isSidangDoktor) {
            // Template form penilaian sidang doktor: isi baris detail penilaian (5 baris) + rata-rata
            $this->postProcessSidangForm($docxPath, $penilaianRows->values()->toArray(), $rataNilai);
        } else {
            $this->fillPenilaianRows($docxPath, $penilaianRows->values()->toArray(), $rataNilai);
        }

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Isi placeholder per-baris penilaian di DOCX.
     * Template memiliki ${nama_penilaian}, ${keterangan}, ${nilai}, ${catatan}
     * berulang (satu per baris) — ganti SATU occurrence per iterasi row.
     */
    private function fillPenilaianRows(string $docxPath, array $rows, string $rataNilai): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // Rata-rata nilai
        $xml = preg_replace('/\$\{rata_nilai[^}]*\}/i', htmlspecialchars($rataNilai, ENT_XML1, 'UTF-8'), $xml);
        $xml = preg_replace('/\$\{nilai_rata2[^}]*\}/i', htmlspecialchars($rataNilai, ENT_XML1, 'UTF-8'), $xml);

        // Map field name → kemungkinan key di array/object
        $placeholders = [
            'nama_penilaian' => ['NAMA_PENILAIAN', 'nama_penilaian', 'PENILAIAN'],
            'keterangan'     => ['KETERANGAN', 'keterangan'],
            'nilai'          => ['NILAI', 'nilai'],
            'catatan'        => ['CATATAN', 'catatan'],
        ];
        
        // Add more placeholder patterns for case variations
        $extraPatterns = [
            'nama_penilaian' => ['Nama penilaian', 'Nama_penilaian', 'NAMA_PENILAIAN', 'Nama Penilaian'],
            'keterangan'     => ['Keterangan', 'KETERANGAN'],
            'nilai'          => ['Nilai', 'NILAI'],
            'catatan'        => ['Catatan', 'CATATAN'],
        ];

        // Build comprehensive patterns for each field to handle all variations
        foreach ($rows as $row) {
            $rowArr = is_object($row) ? (array) $row : $row;
            
            // Get values for each field
            $values = [];
            foreach ($placeholders as $ph => $fieldCandidates) {
                $val = '';
                foreach ($fieldCandidates as $fc) {
                    if (isset($rowArr[$fc]) && $rowArr[$fc] !== null) {
                        $val = $rowArr[$fc];
                        break;
                    }
                }
                $values[$ph] = htmlspecialchars((string) $val, ENT_XML1, 'UTF-8');
            }

            // Replace SATU occurrence saja per baris (gabungkan semua variasi
            // pattern dalam satu regex ber-limit 1 agar tidak menimpa baris lain)
            foreach ($placeholders as $ph => $fieldCandidates) {
                $safe = $values[$ph];

                $variants = [$ph];
                if (isset($extraPatterns[$ph])) {
                    $variants = array_merge($variants, $extraPatterns[$ph]);
                }

                $regex = [];
                foreach ($variants as $v) {
                    $regex[] = '\$\{' . preg_quote($v, '/') . '\}';
                    $regex[] = '\$\(' . preg_quote($v, '/') . '\)';
                }

                $xml = preg_replace(
                    '/' . implode('|', $regex) . '/i',
                    addcslashes($safe, '\\$'),
                    $xml,
                    1
                );
            }
        }

        // Bersihkan sisa placeholder yang belum terganti (baris lebih sedikit dari template)
        $xml = preg_replace('/\$\{nama_penilaian\}/i', '', $xml);
        $xml = preg_replace('/\$\{keterangan\}/i',     '', $xml);
        $xml = preg_replace('/\$\{nilai\}/i',           '', $xml);
        $xml = preg_replace('/\$\{catatan\}/i',         '', $xml);
        $xml = preg_replace('/\$\([^)]*\)/i',           '', $xml);  // $(xxx) variasi
        $xml = preg_replace('/\$\{[^}]*\}/i',           '', $xml);  // sisa ${xxx} lain

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Post-process template SK: isi nomor form, ganti heading romawi (I/II/III),
     * dan isi/expand baris detail penilaian sesuai jumlah record di database.
     */
    private function postProcessSkForm(string $docxPath, string $noForm, string $skRomawi, array $rows): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // (no form) -> nomor form yang dipilih (contoh: 304.1)
        if ($noForm !== '') {
            $xml = $this->fuzzyReplaceOnce($xml, '(no form)', htmlspecialchars($noForm, ENT_XML1, 'UTF-8'));
        }

        // Heading "PENILAIAN SEMINAR KEMAJUAN I" -> II / III sesuai tahapan
        if ($skRomawi !== '') {
            $xml = $this->fuzzyReplaceOnce(
                $xml,
                'PENILAIAN SEMINAR KEMAJUAN I',
                'PENILAIAN SEMINAR KEMAJUAN ' . $skRomawi
            );
        }

        // Expand + isi baris detail penilaian
        $xml = $this->fillSkDetailRows($xml, $rows);

        // Bersihkan sisa placeholder baris detail yang belum terganti
        $xml = preg_replace('/\$\{(nama_penilaian|keterangan|nilai)\}/i', '', $xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Post-process form penilaian sidang doktor (Form 309.1):
     * isi baris detail penilaian (nama_penilaian, keterangan, nilai, catatan)
     * dan nilai rata-rata (skala 5).
     */
    private function postProcessSidangForm(string $docxPath, array $rows, string $rataNilai): void
    {
        $this->fillPenilaianRows($docxPath, $rows, $rataNilai);
    }

    /**
     * Ganti SATU occurrence text yang mungkin terpecah antar <w:r> di dalam XML Word.
     * Formatting diambil dari run pertama dari text yang cocok.
     */
    private function fuzzyReplaceOnce(string $xml, string $search, string $replace): string
    {
        $chars = preg_split('//u', $search, -1, PREG_SPLIT_NO_EMPTY);
        $regex = '';
        foreach ($chars as $char) {
            if ($char === ' ') {
                $regex .= '(?:\s|<[^>]+>)*?';
            } else {
                $regex .= preg_quote($char, '/') . '(?:\s|<[^>]+>)*?';
            }
        }

        return preg_replace_callback('/' . $regex . '/ui', function ($matches) use ($replace) {
            $match    = $matches[0];
            $first    = true;
            $replaced = preg_replace_callback('/>([^<]+)</', function ($m) use (&$first, $replace) {
                if ($first) {
                    $first = false;
                    return '>' . $replace . '<';
                }
                return '><';
            }, '>' . $match . '<');
            return substr($replaced, 1, -1);
        }, $xml, 1);
    }

    /**
     * Expand baris detail penilaian pada template SK.
     * Template hanya memiliki 3 baris, sedangkan database bisa > 3 record,
     * sehingga blok baris disalin otomatis (clone) per record.
     */
    private function fillSkDetailRows(string $xml, array $rows): string
    {
        if (empty($rows)) {
            return $xml;
        }

        $firstPh = strpos($xml, '${nama_penilaian}');
        if ($firstPh === false) {
            return $xml;
        }

        $regionStart = strrpos(substr($xml, 0, $firstPh), '<w:tr ');
        if ($regionStart === false) {
            return $xml;
        }

        $lastNilai = strrpos($xml, '${nilai}');
        if ($lastNilai === false) {
            return $xml;
        }
        $regionEnd = strpos($xml, '</w:tr>', $lastNilai);
        if ($regionEnd === false) {
            return $xml;
        }
        $regionEnd += strlen('</w:tr>');

        // Blok baris pertama (nama_penilaian + keterangan + nilai) sebagai template clone
        $firstNilai    = strpos($xml, '${nilai}');
        $firstBlockEnd = strpos($xml, '</w:tr>', $firstNilai) + strlen('</w:tr>');
        $blockTemplate = substr($xml, $regionStart, $firstBlockEnd - $regionStart);

        $generated = '';
        foreach ($rows as $row) {
            $rowArr = is_object($row) ? (array) $row : $row;
            $nama   = $this->rowValue($rowArr, ['NAMA_PENILAIAN', 'nama_penilaian', 'PENILAIAN']);
            $ket    = $this->rowValue($rowArr, ['KETERANGAN', 'keterangan']);
            $nilai  = $this->rowValue($rowArr, ['NILAI', 'nilai']);

            $block = $blockTemplate;
            $block = preg_replace('/\$\{nama_penilaian\}/', $nama,  $block, 1);
            $block = preg_replace('/\$\{keterangan\}/',     $ket,   $block, 1);
            $block = preg_replace('/\$\{nilai\}/',          $nilai, $block, 1);
            $generated .= $block;
        }

        return substr($xml, 0, $regionStart) . $generated . substr($xml, $regionEnd);
    }

    /**
     * Ambil nilai dari beberapa kemungkinan key dan escape untuk XML.
     */
    private function rowValue(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return htmlspecialchars((string) $row[$key], ENT_XML1, 'UTF-8');
            }
        }
        return '';
    }

    /**
     * Cetak Berita Acara (BA) Penilaian Proposal
     * Menggunakan template: BA proposal TEMPLATE.docx
     * Menghitung rata-rata nilai dari setiap penilai dan rata-rata keseluruhan
     */
    public function cetakBeritaAcara(Request $request, $idJudul, $tahapan)
    {
        $user    = session('auth_user');
        $tahapan = str_replace('_', ' ', $tahapan);

        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

        // Deteksi tahapan SK (SK I, SK II, SK III, SK IV) -> gunakan template BA SK
        $tNormal  = strtolower(trim($tahapan));
        if (preg_match('/^sk\s+(i{1,3}|iv)$/', $tNormal, $skMatch) === 1) {
            return $this->cetakBeritaAcaraSk($request, $idJudul, $tahapan, strtoupper($skMatch[1]));
        }

        // Deteksi tahapan Sidang Terbuka / Tertutup (tahap IV) -> gunakan template BA Sidang Doktor (Form 309.2)
        if (preg_match('/^tahap\s+iv$/', $tNormal) === 1) {
            return $this->cetakBeritaAcaraSidangAkhir($request, $idJudul, $tahapan);
        }

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

        // Semua tim sidang untuk header pembimbing
        $allTimSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)->get();

        $ketuaPembimbing = $allTimSidang->first(fn($t) =>
            in_array(strtolower(trim($t->status_tim_sidang ?? '')), ['ketua pembimbing', 'pembimbing (ketua)'])
        ) ?? $allTimSidang->first(fn($t) =>
            str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
        );

        $anggotaPembimbing = $allTimSidang->filter(fn($t) =>
            str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
            && $t->id !== ($ketuaPembimbing?->id)
        )->values();

        // Get all penilai (pembimbing + penguji) who have submitted evaluations
        $penilaiList = $allTimSidang->filter(function ($t) {
            $status = strtolower($t->status_tim_sidang ?? '');
            return str_contains($status, 'pembimbing') || str_contains($status, 'penguji');
        })->values();

        // Calculate average score for each penilai
        $penilaiAverages = [];
        $totalAllScores = 0;
        $penilaiCount = 0;

        foreach ($penilaiList as $penilai) {
            // Get all penilaian records for this penilai
            $penilaianRecords = TPenilaian::where('id_judul', $idJudul)
                ->where('tahapan_sidang', $tahapan)
                ->where('id_tim_sidang', $penilai->id)
                ->get();

            if ($penilaianRecords->isNotEmpty()) {
                // Calculate average for this penilai
                $nilaiValues = $penilaianRecords->pluck('NILAI')->filter(fn($v) => $v !== '' && $v !== null && $v !== 0);
                $rataNilai = $nilaiValues->count() > 0 ? number_format($nilaiValues->avg(), 2) : '0.00';
                
                $penilaiAverages[] = [
                    'nama' => $penilai->NAMA ?? '-',
                    'nip' => $penilai->NIP ?? '-',
                    'rata_nilai' => $rataNilai,
                    'jumlah_nilai' => $nilaiValues->sum(),
                    'jumlah_item' => $nilaiValues->count()
                ];
                
                $totalAllScores += (float)$rataNilai;
                $penilaiCount++;
            }
        }

        // Calculate overall average (sum of all penilai averages divided by number of penilai)
        $rataRataKeseluruhan = $penilaiCount > 0 ? number_format($totalAllScores / $penilaiCount, 2) : '0.00';
        
        // Debug log
        \Log::info('BA Calculation:', [
            'penilai_count' => $penilaiCount,
            'total_all_scores' => $totalAllScores,
            'rata_rata_keseluruhan' => $rataRataKeseluruhan,
            'penilai_averages' => $penilaiAverages
        ]);

        // Calculate index based on overall average
        $indeks = $this->calculateIndeks($rataRataKeseluruhan);

        // Get latest evaluation date
        $latestPenilaian = TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('TGL_UPDATE', 'desc')
            ->first();
        
        $tanggal = $latestPenilaian?->TGL_UPDATE ?? $latestPenilaian?->TGL_CREATE ?? null;
        $tanggalFormat = $tanggal ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') : '';

        // Template
        $templatePath = base_path('template/pROPOSAL/BA proposal TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA proposal tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Set basic placeholders
            $tp->setValue('judul',                  $ajuan->JUDUL    ?? $judul->JUDUL ?? '');
            $tp->setValue('nama_mhs',                $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',                     $ajuan->NIM      ?? '-');
            $tp->setValue('nama_ketua_pembimbing',   $ketuaPembimbing?->NAMA ?? '-');
            $tp->setValue('nama_pembimbing_i',       $anggotaPembimbing->get(0)?->NAMA ?? '-');
            $tp->setValue('nama_pembimbing_ii',      $anggotaPembimbing->get(1)?->NAMA ?? '-');
            $tp->setValue('nama ketua pembimbing',   $ketuaPembimbing?->NAMA ?? '-');
            $tp->setValue('nama pembimbing I',       $anggotaPembimbing->get(0)?->NAMA ?? '-');
            $tp->setValue('nama pembimbing II',      $anggotaPembimbing->get(1)?->NAMA ?? '-');
            $tp->setValue('tgl_create_penilaian',   $tanggalFormat);
            $tp->setValue('tgl create penilaian',   $tanggalFormat);
            $tp->setValue('nip_ketua_pembimbing',   $ketuaPembimbing?->NIP ?? '-');
            $tp->setValue('nip ketua pembimbing',   $ketuaPembimbing?->NIP ?? '-');
            
            // Set overall average for header field (Nilai Rata-rata)
            $tp->setValue('nilai_rata_rata', $rataRataKeseluruhan);
            $tp->setValue('nilai rata rata', $rataRataKeseluruhan);
            $tp->setValue('nilai_rata-rata', $rataRataKeseluruhan);
            $tp->setValue('nilai rata-rata', $rataRataKeseluruhan);
            $tp->setValue('nilai_rata2', $rataRataKeseluruhan);
            $tp->setValue('nilai rata2', $rataRataKeseluruhan);
            
            // Set overall average for Nilai Akhir Rata-rata (NA)
            $tp->setValue('nilai_rata2_keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai rata2 keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai_rata-keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai rata-keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai_akhir_rata_rata', $rataRataKeseluruhan);
            $tp->setValue('nilai akhir rata rata', $rataRataKeseluruhan);
            
            // Set the specific placeholder from template
            $tp->setValue(' Jumlah nilai dibagi dengan jumlah semua penilai ', $rataRataKeseluruhan);
            $tp->setValue('Jumlah nilai dibagi dengan jumlah semua penilai', $rataRataKeseluruhan);

            // Set individual penilai averages (up to 3 penilai)
            for ($i = 0; $i < 3; $i++) {
                $penilaiNum = $i + 1;
                $nilai = $penilaiAverages[$i]['rata_nilai'] ?? '';
                $tp->setValue("nilai_rata2_dari_form_penilaian_$penilaiNum", $nilai);
            }

            // Set overall average (for the summary field)
            $tp->setValue('nilai_rata2_keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai_akhir_indeks', $indeks);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template BA: ' . $e->getMessage()], 500);
        }

        // Simpan DOCX sementara
        $filename = 'BA_Penilaian_Proposal_'
            . str_replace(' ', '_', $tahapan) . '_'
            . $idJudul . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Manual replacement for penilai values - replace one occurrence at a time
        $this->replacePenilaiValuesInDocx($docxPath, $penilaiAverages, $rataRataKeseluruhan, $indeks);
        
        // Additional manual replacement for overall average using replaceInDocx
        $replacements = [
            'nilai_rata_rata' => $rataRataKeseluruhan,
            'nilai rata rata' => $rataRataKeseluruhan,
            'nilai_rata2_keseluruhan' => $rataRataKeseluruhan,
            'nilai rata2 keseluruhan' => $rataRataKeseluruhan,
            'nilai_akhir_rata_rata' => $rataRataKeseluruhan,
            'nilai akhir rata rata' => $rataRataKeseluruhan,
            'nilai_rata2' => $rataRataKeseluruhan,
            'nilai rata2' => $rataRataKeseluruhan,
            ' Jumlah nilai dibagi dengan jumlah semua penilai ' => $rataRataKeseluruhan,
            'Jumlah nilai dibagi dengan jumlah semua penilai' => $rataRataKeseluruhan,
        ];
        $this->replaceInDocx($docxPath, $replacements);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Cetak Berita Acara (BA) Penilaian SK I / SK II / SK III.
     * Menggunakan template: BA Penilaian SK I sd SK III TEMPLATE.docx
     * Nilai kolom (3) diambil dari rata-rata form penilaian 304.x masing-masing penilai.
     */
    private function cetakBeritaAcaraSk(Request $request, $idJudul, $tahapan, $skRomawi)
    {
        if ($skRomawi === 'IV') {
            return $this->cetakBeritaAcaraSk4($request, $idJudul, $tahapan);
        }

        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

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

        // Nomor BA & nomor form penilaian sumber per tahapan: I=1, II=2, III=3, IV=4
        $nomorTahapan = ['I' => '1', 'II' => '2', 'III' => '3', 'IV' => '4'];
        $angka        = $nomorTahapan[$skRomawi] ?? '';
        $baNo         = '305.' . $angka;
        $formNo       = '304.' . $angka;

        // Tim sidang per status
        $timByStatus = [];
        foreach (TTimSidang::where('id_judul', $idJudul)->where('tahapan_sidang', $tahapan)->get() as $t) {
            $timByStatus[strtolower(trim($t->status_tim_sidang ?? ''))] = $t;
        }

        // Baris kolom (3): Ketua Tim Pembimbing, Ko-pembimbing 1, Ko-pembimbing 2, Penguji 1, Penguji 2
        $rowStatuses = ['pembimbing i', 'pembimbing ii', 'pembimbing iii', 'penguji i', 'penguji ii'];

        $nilaiRows = [];
        foreach ($rowStatuses as $st) {
            $tim = $timByStatus[$st] ?? null;
            $rata = '';
            if ($tim) {
                $vals = TPenilaian::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->where('id_tim_sidang', $tim->id)
                    ->pluck('NILAI')
                    ->filter(fn($v) => $v !== '' && $v !== null);
                if ($vals->count() > 0) {
                    $rata = number_format((float) $vals->avg(), 2);
                }
            }
            $nilaiRows[] = $rata;
        }

        // Nama penilai (Ketua Pembimbing & Ko-pembimbing)
        $namaPenguji = [
            $timByStatus['pembimbing i']?->NAMA   ?? '-',
            $timByStatus['pembimbing ii']?->NAMA  ?? '-',
            $timByStatus['pembimbing iii']?->NAMA ?? '-',
        ];

        // Nilai Akhir Rata-rata (NA) = rata-rata kolom (3) no 1-5
        $adaNilai = array_values(array_filter($nilaiRows, fn($v) => $v !== ''));
        $na = count($adaNilai) > 0
            ? number_format(array_sum(array_map('floatval', $adaNilai)) / count($adaNilai), 2)
            : '';
        $indeks = $na !== '' ? $this->calculateIndeksSk((float) $na) : '';

        // Kaprodi
        $kaprodi = TUser::where('STATUS_KAPRODI', 't')->first();

        // Hari/Tanggal & jam seminar
        $tgl = $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang) : null;
        $tglHeader = $tgl ? $tgl->translatedFormat('l, d F Y') : '-';
        $tglFooter = $tgl ? $tgl->translatedFormat('d F Y') : '';
        $waktu = $ajuan->waktu_sidang
            ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i')
            : '-';

        // Template
        $templatePath = base_path('template/SK-1/BA Penilaian SK I sd SK III TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA SK tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Placeholder utuh (satu run)
            $tp->setValue('nama_kaprodi',    $kaprodi ? $kaprodi->NAMA_LENGKAP : '');
            $tp->setValue('nip_kaprodi',     $kaprodi ? $kaprodi->NIP_NIM : '');
            $tp->setValue('nama_penguji_i',  $namaPenguji[0]);
            $tp->setValue('nama_penguji_ii', $namaPenguji[1]);
            $tp->setValue('nama_penguji_iii', $namaPenguji[2]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template BA SK: ' . $e->getMessage()], 500);
        }

        // Simpan DOCX sementara
        $filename = 'BA_Penilaian_SK_' . $skRomawi . '_'
            . str_replace(' ', '_', $tahapan) . '_'
            . $idJudul . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Manipulasi XML untuk placeholder yang terpecah antar run / berulang
        $this->fillBaSkXml($docxPath, [
            'nama_mhs'     => $ajuan->NAMA_MHS ?? '-',
            'nim'          => $ajuan->NIM ?? '-',
            'judul'        => $ajuan->JUDUL ?? $judul->JUDUL ?? '',
            'tgl_header'   => $tglHeader,
            'tgl_footer'   => $tglFooter,
            'waktu'        => $waktu,
            'ba_no'        => $baNo,
            'form_no'      => $formNo,
            'sk_romawi'    => $skRomawi,
            'nilai_rows'   => $nilaiRows,
            'na'           => $na,
            'indeks'       => $indeks,
        ]);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Isi placeholder BA SK di word/document.xml.
     * Placeholder yang terpecah antar <w:r> ditangani secara fuzzy,
     * sedangkan yang berulang ($ {nilai_rata2}, $(Nilai rata2)) diganti per-kemunculan.
     */
    private function fillBaSkXml(string $docxPath, array $data): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

        // Data mahasiswa & judul
        $xml = $this->fuzzyReplaceOnce($xml, '$(nama mhs)', $esc($data['nama_mhs']));
        $xml = $this->fuzzyReplaceOnce($xml, '$(nim)',       $esc($data['nim']));
        $xml = $this->fuzzyReplaceOnce($xml, '$(judul)',     $esc($data['judul']));

        // Hari/Tanggal Seminar (kemunculan pertama) & Bandung (kemunculan kedua)
        $xml = $this->fuzzyReplaceOnce($xml, '${tgl_sidang}', $esc($data['tgl_header']));
        $xml = $this->fuzzyReplaceOnce($xml, '${tgl_sidang}', $esc($data['tgl_footer']));

        // Jam seminar
        $xml = $this->fuzzyReplaceOnce($xml, '${waktu}', $esc($data['waktu']));

        // Nomor form BA (FORM #305.x)
        $xml = $this->fuzzyReplaceOnce($xml, '#305.1', '#' . $data['ba_no']);

        // Heading romawi (BERITA ACARA SEMINAR KEMAJUAN I -> II / III)
        if ($data['sk_romawi'] !== 'I') {
            $xml = $this->fuzzyReplaceOnce(
                $xml,
                'BERITA ACARA SEMINAR KEMAJUAN I',
                'BERITA ACARA SEMINAR KEMAJUAN ' . $data['sk_romawi']
            );
        }

        // Kolom keterangan "Dari Form #304.3" -> sesuai tahapan (semua kemunculan)
        $xml = preg_replace('/#304\.3/i', '#' . $data['form_no'], $xml);

        // Nilai kolom (3) per baris (${nilai_rata2}, 5 kemunculan, nilai bisa beda)
        foreach ($data['nilai_rows'] as $v) {
            $xml = preg_replace('/\$\{nilai_rata2\}/', $esc($v), $xml, 1);
        }

        // Nilai Akhir Rata-rata (NA) & Indeks.
        // Template hasil regenerate: sisa placeholder ${nilai_rata2} (setelah 5 baris) -> NA & Indeks.
        $xml = preg_replace('/\$\{nilai_rata2\}/', $esc($data['na']), $xml, 1);
        $xml = preg_replace('/\$\{nilai_rata2\}/', $esc($data['indeks']), $xml, 1);
        // Template lama: placeholder $(Nilai rata2) -> NA & Indeks.
        $xml = $this->fuzzyReplaceOnce($xml, '$(Nilai rata2)', $esc($data['na']));
        $xml = $this->fuzzyReplaceOnce($xml, '$(Nilai rata2)', $esc($data['indeks']));
        // Bersihkan sisa placeholder agar tidak bocor ke output.
        $xml = preg_replace('/\$\{nilai_rata2\}/', '', $xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Cetak Berita Acara (BA) Penilaian SK IV (Disertasi).
     * Menggunakan template: ba SK IV TEMPLATE.docx
     * Nilai kolom (3) diambil dari rata-rata form penilaian 306.2 masing-masing penilai.
     * Baris 8 (Nilai Publikasi di Jurnal Internasional) tidak punya sumber di database,
     * sehingga baris 8, 9 (Nilai Akhir) dan 10 (Indeks) dibiarkan kosong untuk diisi manual KPPs.
     */
    private function cetakBeritaAcaraSk4(Request $request, $idJudul, $tahapan)
    {
        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

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

        // Tim sidang per status
        $timByStatus = [];
        foreach (TTimSidang::where('id_judul', $idJudul)->where('tahapan_sidang', $tahapan)->get() as $t) {
            $timByStatus[strtolower(trim($t->status_tim_sidang ?? ''))] = $t;
        }

        // Pemetaan peran (mendukung pola "Ketua Pembimbing" atau "Pembimbing I" sebagai ketua)
        $hasKetua = isset($timByStatus['ketua pembimbing']);
        $roleStatuses = [
            'ketua_pembimbing' => $hasKetua ? 'ketua pembimbing' : 'pembimbing i',
            'ko_pembimbing_1'  => $hasKetua ? 'pembimbing i'      : 'pembimbing ii',
            'ko_pembimbing_2'  => $hasKetua ? 'pembimbing ii'     : 'pembimbing iii',
            'penguji_1'        => 'penguji i',
            'penguji_2'        => 'penguji ii',
            'penguji_3'        => 'penguji iii',
        ];

        // Nilai kolom (3) per baris (Pembimbing Ketua, Ko 1, Ko 2, Penguji 1, Penguji 2, Penguji 3)
        $nilaiRows = [];
        foreach ($roleStatuses as $st) {
            $tim = $timByStatus[$st] ?? null;
            $rata = '';
            if ($tim) {
                $vals = TPenilaian::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->where('id_tim_sidang', $tim->id)
                    ->pluck('NILAI')
                    ->filter(fn($v) => $v !== '' && $v !== null);
                if ($vals->count() > 0) {
                    $rata = number_format((float) $vals->avg(), 2);
                }
            }
            $nilaiRows[] = $rata;
        }

        // Nilai Rata-Rata (baris 7) = rata-rata nilai 3 pembimbing
        $nilaiPembimbing = array_values(array_filter(
            array_slice($nilaiRows, 0, 3),
            fn($v) => $v !== ''
        ));
        $rataRata = count($nilaiPembimbing) > 0
            ? number_format(array_sum(array_map('floatval', $nilaiPembimbing)) / count($nilaiPembimbing), 2)
            : '';

        // Nama tim penguji/penilai (daftar & header)
        $namaPenguji = [
            'ketua' => $timByStatus[$roleStatuses['ketua_pembimbing']]?->NAMA ?? '-',
            'ko1'   => $timByStatus[$roleStatuses['ko_pembimbing_1']]?->NAMA  ?? '-',
            'ko2'   => $timByStatus[$roleStatuses['ko_pembimbing_2']]?->NAMA  ?? '-',
            'peng1' => $timByStatus['penguji i']?->NAMA   ?? '-',
            'peng2' => $timByStatus['penguji ii']?->NAMA  ?? '-',
            'peng3' => $timByStatus['penguji iii']?->NAMA ?? '-',
        ];

        // Ketua Sidang & Kaprodi
        $ketuaSidang = $timByStatus['ketua sidang'] ?? null;
        $kaprodi = TUser::where('STATUS_KAPRODI', 't')->first();

        // Tanggal sidang (footer "Bandung, d F Y")
        $tgl = $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang) : null;
        $tglFooter = $tgl ? $tgl->translatedFormat('d F Y') : '';

        // Template
        $templatePath = base_path('template/SK-4/ba SK IV TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA SK IV tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Placeholder utuh (satu run)
            $tp->setValue('judul',                 $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('nama_mhs',              $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',                   $ajuan->NIM ?? '-');
            $tp->setValue('nama_ketua_pembimbing', $namaPenguji['ketua']);
            $tp->setValue('nama_pembimbing_i',     $namaPenguji['ko1']);
            $tp->setValue('nama_pembimbing_ii',    $namaPenguji['ko2']);
            $tp->setValue('nama_kaprodi',          $kaprodi ? $kaprodi->NAMA_LENGKAP : '');
            $tp->setValue('nip_kaprodi',           $kaprodi ? $kaprodi->NIP_NIM : '');
            $tp->setValue('tgl_sidang',            $tglFooter);
            $tp->setValue('nama_ketua_sidang',     $ketuaSidang?->NAMA ?? '-');
            $tp->setValue('nip_ketua_sidang',      $ketuaSidang?->NIP ?? '-');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template BA SK IV: ' . $e->getMessage()], 500);
        }

        // Simpan DOCX sementara
        $filename = 'BA_Penilaian_SK_IV_' . str_replace(' ', '_', $tahapan) . '_' . $idJudul . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Manipulasi XML untuk placeholder yang terpecah antar run / berulang
        $this->fillBaSk4Xml($docxPath, [
            'nilai_rows'   => $nilaiRows,
            'rata_rata'    => $rataRata,
            'nama_penguji' => $namaPenguji,
        ]);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Isi placeholder BA SK IV di word/document.xml.
     */
    private function fillBaSk4Xml(string $docxPath, array $data): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

        // Normalisasi typo keterangan "Form #3062" -> "#306.2" (semua kemunculan)
        $xml = preg_replace('/#3062/i', '#306.2', $xml);

        // Nilai kolom (3) per baris (${nilai_rata2}, 6 kemunculan, nilai bisa beda)
        foreach ($data['nilai_rows'] as $v) {
            $xml = preg_replace('/\$\{nilai_rata2\}/', $esc($v), $xml, 1);
        }

        // Bersihkan sisa placeholder (baris 7-10 dibiarkan kosong untuk isian manual KPPs)
        $xml = preg_replace('/\$\{nilai_rata2\}/', '', $xml);

        // Nilai Rata-Rata (baris 7) -> injeksi ke sel kosong
        if ($data['rata_rata'] !== '') {
            $xml = $this->insertValueIntoSk4Row($xml, '7', $esc($data['rata_rata']));
        }

        // Daftar nama penilai: ganti literal "Nama penguji I/II/III/IV" -> nama asli
        $np = $data['nama_penguji'];
        $xml = $this->fuzzyReplaceOnce($xml, 'Nama penguji I',   $esc($np['ketua']));
        $xml = $this->fuzzyReplaceOnce($xml, 'Nama penguji II',  $esc($np['ko1']));
        $xml = $this->fuzzyReplaceOnce($xml, 'Nama penguji III', $esc($np['ko2']));
        $xml = $this->fuzzyReplaceOnce($xml, 'Nama penguji IV',  $esc($np['peng1']));

        // Baris 5 & 6 (Penguji II & III): isi sel nama yang masih titik-titik
        $anchor = strpos($xml, $esc($np['peng1']));
        $xml = $this->fillSk4ListNameCell($xml, '5', $esc($np['peng2']), $anchor === false ? 0 : $anchor);
        $xml = $this->fillSk4ListNameCell($xml, '6.', $esc($np['peng3']));

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Injeksi nilai ke sel kosong (kolom nilai, lebar 1303) pada baris dengan nomor tertentu.
     */
    private function insertValueIntoSk4Row(string $xml, string $rowNum, string $value): string
    {
        $pos = strpos($xml, '<w:t>' . $rowNum . '</w:t>');
        if ($pos === false) {
            return $xml;
        }
        $trEnd = strpos($xml, '</w:tr>', $pos);
        if ($trEnd === false) {
            return $xml;
        }
        $cellStart = strpos($xml, '<w:tcW w:w="1303"', $pos);
        if ($cellStart === false || $cellStart > $trEnd) {
            return $xml;
        }
        $cellEnd = strpos($xml, '</w:tc>', $cellStart);
        if ($cellEnd === false) {
            return $xml;
        }
        $pprEnd = strpos($xml, '</w:pPr>', $cellStart);
        if ($pprEnd === false || $pprEnd > $cellEnd) {
            return $xml;
        }
        $inject = '<w:r><w:rPr><w:color w:val="000000" w:themeColor="text1"/></w:rPr><w:t>' . $value . '</w:t></w:r>';
        $after  = $pprEnd + strlen('</w:pPr>');
        return substr($xml, 0, $after) . $inject . substr($xml, $after);
    }

    /**
     * Ganti teks run pertama pada sel nama (lebar 4102) di daftar penilai,
     * diposisikan dari baris dengan nomor tertentu (opsional setelah posisi anchor).
     */
    private function fillSk4ListNameCell(string $xml, string $rowNum, string $value, int $after = 0): string
    {
        $pos = strpos($xml, '<w:t>' . $rowNum . '</w:t>', $after);
        if ($pos === false) {
            return $xml;
        }
        $trEnd = strpos($xml, '</w:tr>', $pos);
        if ($trEnd === false) {
            return $xml;
        }
        $cellStart = strpos($xml, '<w:tcW w:w="4102"', $pos);
        if ($cellStart === false || $cellStart > $trEnd) {
            return $xml;
        }
        $cellEnd = strpos($xml, '</w:tc>', $cellStart);
        if ($cellEnd === false) {
            return $xml;
        }
        $tTagStart = strpos($xml, '<w:t>', $cellStart);
        if ($tTagStart === false || $tTagStart > $cellEnd) {
            $tTagStart = strpos($xml, '<w:t ', $cellStart);
        }
        if ($tTagStart === false || $tTagStart > $cellEnd) {
            return $xml;
        }
        $tOpenEnd = strpos($xml, '>', $tTagStart);
        $tClose   = strpos($xml, '</w:t>', $tTagStart);
        if ($tOpenEnd === false || $tClose === false || $tClose > $cellEnd) {
            return $xml;
        }
        return substr($xml, 0, $tOpenEnd + 1) . $value . substr($xml, $tClose);
    }

    /**
     * Cetak Berita Acara Sidang Doktor (Form 309.2) untuk tahapan Sidang Terbuka / Tertutup.
     * Template: template/SIDANG/BA sidang akhir TEMPLATE.docx
     */
    private function cetakBeritaAcaraSidangAkhir(Request $request, $idJudul, $tahapan)
    {
        if (!$idJudul) {
            return response()->json(['error' => 'ID Judul diperlukan.'], 400);
        }

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

        // Tim sidang per status
        $timByStatus = [];
        foreach (TTimSidang::where('id_judul', $idJudul)->where('tahapan_sidang', $tahapan)->get() as $t) {
            $timByStatus[strtolower(trim($t->status_tim_sidang ?? ''))] = $t;
        }

        // Pemetaan peran (mendukung pola "Ketua Pembimbing" atau "Pembimbing I" sebagai ketua)
        $hasKetua = isset($timByStatus['ketua pembimbing']);
        $roleStatuses = [
            'ketua_pembimbing' => $hasKetua ? 'ketua pembimbing' : 'pembimbing i',
            'ko_pembimbing_1'  => $hasKetua ? 'pembimbing i'      : 'pembimbing ii',
            'ko_pembimbing_2'  => $hasKetua ? 'pembimbing ii'     : 'pembimbing iii',
            'penguji_1'        => 'penguji i',
            'penguji_2'        => 'penguji ii',
            'penguji_3'        => 'penguji iii',
        ];

        // Nilai kolom (3) per baris (1-6): Ketua Tim Pembimbing, Ko 1, Ko 2, Penguji 1, Penguji 2, Penguji 3
        $nilaiRows = [];
        foreach ($roleStatuses as $st) {
            $tim = $timByStatus[$st] ?? null;
            $rata = '';
            if ($tim) {
                $vals = TPenilaian::where('id_judul', $idJudul)
                    ->where('tahapan_sidang', $tahapan)
                    ->where('id_tim_sidang', $tim->id)
                    ->pluck('NILAI')
                    ->filter(fn($v) => $v !== '' && $v !== null);
                if ($vals->count() > 0) {
                    $rata = number_format((float) $vals->avg(), 2);
                }
            }
            $nilaiRows[] = $rata;
        }

        // Nilai Rata-Rata (baris 7) = rata-rata nilai baris 1-6
        $nilaiFilled = array_values(array_filter($nilaiRows, fn($v) => $v !== ''));
        $rataRata = count($nilaiFilled) > 0
            ? number_format(array_sum(array_map('floatval', $nilaiFilled)) / count($nilaiFilled), 2)
            : '';

        // Nilai akhir Index (baris 8)
        $indeks = $rataRata !== '' ? $this->calculateIndeksSidang((float) $rataRata) : '';

        // Nama penilai untuk daftar tanda tangan
        $namaPenguji = [
            'ketua' => $timByStatus[$roleStatuses['ketua_pembimbing']]?->NAMA ?? '-',
            'ko1'   => $timByStatus[$roleStatuses['ko_pembimbing_1']]?->NAMA  ?? '-',
            'ko2'   => $timByStatus[$roleStatuses['ko_pembimbing_2']]?->NAMA  ?? '-',
            'peng1' => $timByStatus['penguji i']?->NAMA   ?? '-',
            'peng2' => $timByStatus['penguji ii']?->NAMA  ?? '-',
            'peng3' => $timByStatus['penguji iii']?->NAMA ?? '-',
        ];

        $ketuaSidang = $timByStatus['ketua sidang'] ?? null;
        $kaprodi = TUser::where('STATUS_KAPRODI', 't')->first();

        $tgl = $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang) : null;
        $tglFooter = $tgl ? $tgl->translatedFormat('d F Y') : '';

        $waktuSidang = $ajuan->waktu_sidang ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i') : '';
        $waktuSelesai = $ajuan->waktu_selesai ? substr((string) $ajuan->waktu_selesai, 0, 5) : '';

        // Tanggal create penilaian (terakhir)
        $latestPenilaian = TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('TGL_UPDATE', 'desc')
            ->first();
        $tglCreate = $latestPenilaian?->TGL_UPDATE ?? $latestPenilaian?->TGL_CREATE ?? null;
        $tglCreateFormat = $tglCreate ? \Carbon\Carbon::parse($tglCreate)->translatedFormat('d F Y') : $tglFooter;

        // Template
        $templatePath = base_path('template/SIDANG/BA sidang akhir TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA sidang akhir tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Placeholder utuh (satu run)
            $tp->setValue('judul',             $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('nama_mhs',          $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',               $ajuan->NIM ?? '-');
            $tp->setValue('tgl_sidang',        $tglFooter);
            $tp->setValue('waktu',             $waktuSidang);
            $tp->setValue('nama_kaprodi',      $kaprodi ? $kaprodi->NAMA_LENGKAP : '');
            $tp->setValue('nama_ketua_sidang', $ketuaSidang?->NAMA ?? '-');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memproses template BA Sidang Doktor: ' . $e->getMessage()], 500);
        }

        // Simpan DOCX sementara
        $filename = 'BA_Sidang_Doktor_' . str_replace(' ', '_', $tahapan) . '_' . $idJudul . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Manipulasi XML untuk placeholder berulang / terpecah / literal
        $this->fillSidangAkhirXml($docxPath, [
            'nilai_rows'           => $nilaiRows,
            'rata_rata'            => $rataRata,
            'indeks'               => $indeks,
            'nama_penguji'         => $namaPenguji,
            'waktu_selesai'        => $waktuSelesai,
            'tgl_create_penilaian' => $tglCreateFormat,
        ]);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Isi placeholder BA Sidang Doktor di word/document.xml.
     */
    private function fillSidangAkhirXml(string $docxPath, array $data): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

        // Nilai kolom (3) per baris 1-6 ("Nilai rata2", 6 kemunculan, nilai bisa beda)
        foreach ($data['nilai_rows'] as $v) {
            $xml = preg_replace('/Nilai rata2/', $esc($v), $xml, 1);
        }

        // Nilai Rata-Rata (baris 7) & Nilai akhir Index (baris 8) -> injeksi ke sel kosong
        if ($data['rata_rata'] !== '') {
            $xml = $this->insertSidangValueIntoRow($xml, '7', $esc($data['rata_rata']));
        }
        if ($data['indeks'] !== '') {
            $xml = $this->insertSidangValueIntoRow($xml, '8', $esc($data['indeks']));
        }

        // Daftar nama tanda tangan: ganti literal "(nama penguji I/II/III/IV)" -> nama asli
        $np = $data['nama_penguji'];
        $xml = str_replace('(nama penguji I)',   $esc($np['ketua']), $xml);
        $xml = str_replace('(nama penguji II)',  $esc($np['ko1']),   $xml);
        $xml = str_replace('(nama penguji III)', $esc($np['ko2']),   $xml);
        $xml = str_replace('(nama penguji IV)',  $esc($np['peng1']), $xml);

        // Baris 5 & 6 (Penguji II & III): isi sel nama yang masih titik-titik
        $anchor = strpos($xml, $esc($np['peng1']));
        $xml = $this->fillSidangListNameCell($xml, '5', $esc($np['peng2']), $anchor === false ? 0 : $anchor);
        $xml = $this->fillSidangListNameCell($xml, '6', $esc($np['peng3']), $anchor === false ? 0 : $anchor);

        // Waktu selesai (typo template "$wkatu(selesai)")
        if ($data['waktu_selesai'] !== '') {
            $xml = str_replace('$wkatu(selesai)', $esc($data['waktu_selesai']), $xml);
        }

        // Tanggal create penilaian
        if ($data['tgl_create_penilaian'] !== '') {
            $xml = str_replace('tgl create penilaian', $esc($data['tgl_create_penilaian']), $xml);
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Injeksi nilai ke sel kosong (kolom nilai, lebar 1303) pada baris penilaian dengan nomor tertentu.
     */
    private function insertSidangValueIntoRow(string $xml, string $rowNum, string $value): string
    {
        $pos = strpos($xml, '<w:t>' . $rowNum . '</w:t>');
        if ($pos === false) {
            return $xml;
        }
        $trEnd = strpos($xml, '</w:tr>', $pos);
        if ($trEnd === false) {
            return $xml;
        }
        $cellStart = strpos($xml, '<w:tcW w:w="1303"', $pos);
        if ($cellStart === false || $cellStart > $trEnd) {
            return $xml;
        }
        $cellEnd = strpos($xml, '</w:tc>', $cellStart);
        if ($cellEnd === false) {
            return $xml;
        }
        $pprEnd = strpos($xml, '</w:pPr>', $cellStart);
        if ($pprEnd === false || $pprEnd > $cellEnd) {
            return $xml;
        }
        $inject = '<w:r><w:rPr><w:color w:val="000000" w:themeColor="text1"/></w:rPr><w:t>' . $value . '</w:t></w:r>';
        $after  = $pprEnd + strlen('</w:pPr>');
        return substr($xml, 0, $after) . $inject . substr($xml, $after);
    }

    /**
     * Ganti teks run pertama pada sel nama (lebar 4084) di daftar tanda tangan,
     * diposisikan dari baris dengan nomor tertentu (opsional setelah posisi anchor).
     */
    private function fillSidangListNameCell(string $xml, string $rowNum, string $value, int $after = 0): string
    {
        $pos = strpos($xml, '<w:t>' . $rowNum . '</w:t>', $after);
        if ($pos === false) {
            return $xml;
        }
        $trEnd = strpos($xml, '</w:tr>', $pos);
        if ($trEnd === false) {
            return $xml;
        }
        $cellStart = strpos($xml, '<w:tcW w:w="4084"', $pos);
        if ($cellStart === false || $cellStart > $trEnd) {
            return $xml;
        }
        $cellEnd = strpos($xml, '</w:tc>', $cellStart);
        if ($cellEnd === false) {
            return $xml;
        }
        $tTagStart = strpos($xml, '<w:t>', $cellStart);
        if ($tTagStart === false || $tTagStart > $cellEnd) {
            $tTagStart = strpos($xml, '<w:t ', $cellStart);
        }
        if ($tTagStart === false || $tTagStart > $cellEnd) {
            return $xml;
        }
        $tOpenEnd = strpos($xml, '>', $tTagStart);
        $tClose   = strpos($xml, '</w:t>', $tTagStart);
        if ($tOpenEnd === false || $tClose === false || $tClose > $cellEnd) {
            return $xml;
        }
        return substr($xml, 0, $tOpenEnd + 1) . $value . substr($xml, $tClose);
    }

    /**
     * Indeks Nilai Akhir (NA) pada BA Sidang Doktor (Form 309.2):
     * A jika NA >= 4,5; AB jika 4,0 <= NA < 4,5; B jika 3,5 <= NA < 4,0; Mengulang jika NA < 3,5.
     */
    private function calculateIndeksSidang(float $na): string
    {
        if ($na >= 4.5) {
            return 'A';
        }
        if ($na >= 4.0) {
            return 'AB';
        }
        if ($na >= 3.5) {
            return 'B';
        }
        return 'Mengulang';
    }

    /**
     * Indeks kelulusan BA SK berdasarkan Nilai Akhir (NA).
     */
    private function calculateIndeksSk(float $na): string
    {
        if ($na >= 4.5) {
            return 'A';
        }
        if ($na >= 4.0) {
            return 'AB';
        }
        if ($na >= 3.5) {
            return 'B';
        }
        return 'TIDAK LULUS';
    }

    /**
     * Calculate grade index based on average score
     */
    private function calculateIndeks($rataRata)
    {
        $score = (float)$rataRata;
        
        if ($score >= 4.0) {
            return 'A';
        } elseif ($score >= 3.5) {
            return 'AB';
        } elseif ($score >= 3.0) {
            return 'B';
        } else {
            return 'BC';
        }
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

        // Filter pembimbing - HANYA ambil Pembimbing I, II, III (exclude Ketua Pembimbing dan Ketua Sidang)
        $pembimbings = $timSidang->filter(function ($tim) {
            $status = strtolower($tim->status_tim_sidang ?? '');
            $nama = trim($tim->NAMA ?? '');
            
            // Hanya ambil yang status "Pembimbing I", "Pembimbing II", dll
            // TIDAK ambil "Ketua Pembimbing" atau "Ketua Sidang"
            return (str_contains($status, 'pembimbing i') 
                    || str_contains($status, 'pembimbing ii') 
                    || str_contains($status, 'pembimbing iii')
                    || ($status === 'pembimbing' && !str_contains($status, 'ketua')))
                && $nama !== '' 
                && $nama !== 'Pembimbing';
        })->values();

        $pengujis = $timSidang->filter(function ($tim) {
            $status = strtolower($tim->status_tim_sidang ?? '');
            $nama = trim($tim->NAMA ?? '');
            
            // Hanya ambil Penguji I, II, III
            return (str_contains($status, 'penguji i') 
                    || str_contains($status, 'penguji ii') 
                    || str_contains($status, 'penguji iii')
                    || ($status === 'penguji'))
                && $nama !== ''
                && $nama !== 'Penguji';
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

        $templatePath = base_path('template/surat Kesediaan Tim Penelaah Proposal TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template surat kesediaan penelaah tidak ditemukan.');
        }

        $filename = 'Surat_Kesediaan_Tim_Penelaah_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);
        
        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }
        
        // Copy template
        copy($templatePath, $docxPath);
        
        // Manual replacement for ${} and {} format - ADD ALL VARIATIONS
        $replacements = [
            'no_surat' => $ajuan->NO_SURAT_PENELAAH ?? '-',
            'tgl_penelaah' => $fmt($ajuan->TGL_PENELAAH),
            'tgl_surat' => $fmt($ajuan->TGL_UNDANGAN),
            'nama_mhs' => $ajuan->NAMA_MHS ?? '-',
            'nama mhs' => $ajuan->NAMA_MHS ?? '-',  // ADD THIS
            'nama_mahasiswa' => $ajuan->NAMA_MHS ?? '-',
            'nama_mahasiwa' => $ajuan->NAMA_MHS ?? '-',
            'nama_mahaiswa' => $ajuan->NAMA_MHS ?? '-',
            'nim' => $ajuan->NIM ?? '-',
            'judul' => $ajuan->JUDUL ?? $judul->JUDUL ?? '',
            'prodi' => $prodi,
            'nama prodi' => $prodi,
            'Teknik Geofisika nama prodi' => $prodi,  // ADD THIS
            'tgl_hasil_penelaahan' => $fmt($ajuan->TGL_HASIL_PENELAHAN),
            'dari_tabel_t_user_status_dekan_y' => $dekan->NAMA_LENGKAP ?? '',
            'nip' => $dekan->NIP_NIM ?? '',
        ];
        
        $this->replaceInDocx($docxPath, $replacements);
        
        // Handle penguji and pembimbing separately with direct XML manipulation
        $this->handlePengujiAndPembimbingInXml($docxPath, $pembimbingNames, $pengujiList->toArray(), $prodi);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Handle pembimbing list in docx - each row separately
     */
    private function handlePembimbingList($docxPath, $pembimbingNames)
    {
        if ($pembimbingNames->isEmpty()) {
            return;
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            
            // Replace individual pembimbing rows (1., 2., etc)
            for ($i = 0; $i < 3; $i++) {
                if (isset($pembimbingNames[$i])) {
                    $nama = $pembimbingNames[$i];
                    $rowNum = $i + 1;
                    
                    // Replace for this specific row with number prefix
                    $xml = preg_replace(
                        '/' . $rowNum . '\\.\\s*\\$\\(pembimbing\\)/',
                        $rowNum . '. ' . htmlspecialchars($nama, ENT_XML1, 'UTF-8'),
                        $xml,
                        1
                    );
                    $xml = preg_replace(
                        '/' . $rowNum . '\\.\\s*\\$\\{pembimbing\\}/',
                        $rowNum . '. ' . htmlspecialchars($nama, ENT_XML1, 'UTF-8'),
                        $xml,
                        1
                    );
                }
            }
            
            // Cleanup any remaining unreplaced placeholders
            $xml = str_replace('$(pembimbing)', '', $xml);
            $xml = str_replace('${pembimbing}', '', $xml);
            $xml = str_replace('{pembimbing}', '', $xml);
            
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
    }
    
    /**
     * Handle penguji list (Kepada Yth section) - each row separately
     */
    private function handlePengujiList($docxPath, $pengujiList)
    {
        if (empty($pengujiList)) {
            return;
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            
            // Replace individual penguji rows (1, 2, 3)
            for ($i = 0; $i < 3; $i++) {
                if (isset($pengujiList[$i])) {
                    $nama = $pengujiList[$i]['nama'] ?? '-';
                    $institusi = $pengujiList[$i]['institusi'] ?? '';
                    
                    // Find pattern for row number (1., 2., etc)
                    $rowNum = $i + 1;
                    
                    // Replace for this specific row
                    $xml = preg_replace(
                        '/' . $rowNum . '\\.\\$\\{nama_penguji\\}\\s*\\$\\{institusi\\}/',
                        $rowNum . '.' . htmlspecialchars($nama, ENT_XML1, 'UTF-8') . ' ' . htmlspecialchars($institusi, ENT_XML1, 'UTF-8'),
                        $xml,
                        1
                    );
                }
            }
            
            // Cleanup any remaining unreplaced placeholders
            $xml = str_replace('${nama_penguji}', '', $xml);
            $xml = str_replace('${institusi}', '', $xml);
            $xml = str_replace('{nama_penguji}', '', $xml);
            $xml = str_replace('{institusi}', '', $xml);
            
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
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
        
        $tglSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';

        $templatePath = base_path('template/UNDANGAN SIDANG TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template undangan sidang tidak ditemukan.');
        }

        $filename = 'Undangan_Sidang_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);
        
        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }
        
        // Copy template
        copy($templatePath, $docxPath);
        
        // Manual replacement for $() format
        $replacements = [
            'NAM MAHASIWA' => $ajuan->NAMA_MHS ?? '-',
            'nama_mahasiswa' => $ajuan->NAMA_MHS ?? '-',
            'nama' => $ajuan->NAMA_MHS ?? '-',
            'nim' => $ajuan->NIM ?? '-',
            'nama prodi' => $prodi,
            'prodi' => $prodi,
            'judul' => $ajuan->JUDUL ?? $judul->JUDUL ?? '',
            'nama ketua pembimbing' => $pembimbing->get(0)->NAMA ?? '-',
            'nama pembimbing I' => $pembimbing->get(1)->NAMA ?? '-',
            'nama pembimbing II' => $pembimbing->get(2)->NAMA ?? '-',
            'nama penguji I' => $penguji->get(0)->NAMA ?? '-',
            'nama penguji II' => $penguji->get(1)->NAMA ?? '-',
            'nama penguji III' => $penguji->get(2)->NAMA ?? '-',
            'ketua sidang' => $ketua->NAMA ?? '-',
            'tgl sidang' => $tglSidang,
            'waktu_waktu_selesai' => $pukulPendahuluan,
            'ruang' => $tempat,
            'ruangan' => $tempat,
            'waktu selesai – di tambah 2,5 jam' => $pukulDoktor,
            'nama user status dekan \'y\'' => $dekan->NAMA_LENGKAP ?? '',
            'dekan' => $dekan->NAMA_LENGKAP ?? '',
        ];
        
        $this->replaceInDocx($docxPath, $replacements);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
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

        // Build kepada paragraphs – each row becomes a separate <w:p> so columns align properly
        $kepadaParagraphs = '';
        $i = 1;
        foreach ($rows as $r) {
            $nomor   = htmlspecialchars($i . '.', ENT_XML1, 'UTF-8');
            $nama    = htmlspecialchars($r['nama'], ENT_XML1, 'UTF-8');
            $jabatan = htmlspecialchars($r['jabatan'], ENT_XML1, 'UTF-8');
            $kepadaParagraphs .=
                '<w:p>' .
                  '<w:pPr>' .
                    '<w:tabs>' .
                      '<w:tab w:val="left" w:pos="5670"/>' .
                    '</w:tabs>' .
                  '</w:pPr>' .
                  '<w:r><w:t xml:space="preserve">' . $nomor . ' ' . $nama . '</w:t></w:r>' .
                  '<w:r><w:tab/></w:r>' .
                  '<w:r><w:t xml:space="preserve">' . $jabatan . '</w:t></w:r>' .
                '</w:p>';
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

        $templatePath = base_path('template/undangan seminar kemajuan TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template undangan kemajuan tidak ditemukan.');
        }

        try {
            $tp = new TemplateProcessor($templatePath);
            
            // Set all placeholders
            $tp->setValue('nama_mahasiwa', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nama_mahasiswa', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nama', $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim', $ajuan->NIM ?? '-');
            $tp->setValue('prodi', $prodi);
            $tp->setValue('judul', $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $tp->setValue('no_undnagan', $ajuan->NO_UNDANGAN ?? '');
            $tp->setValue('no_undangan', $ajuan->NO_UNDANGAN ?? '');
            $tp->setValue('nomor', $ajuan->NO_UNDANGAN ?? '');
            
            $tglUndangan = $ajuan->TGL_UNDANGAN
                ? \Carbon\Carbon::parse($ajuan->TGL_UNDANGAN)->translatedFormat('d F Y')
                : '';
            $tp->setValue('tgl_undangan', $tglUndangan);
            $tp->setValue('tgl_surat', $tglUndangan);
            
            $tp->setValue('perihal', $perihal);
            $tp->setValue('seminar', $seminar);
            $tp->setValue('nama_tim_sidang_ketua_sidang', $ketua->NAMA ?? '-');
            $tp->setValue('ketua_sidang', $ketua->NAMA ?? '-');
            
            $hariTanggal = $ajuan->tgl_sidang
                ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
                : '-';
            $tp->setValue('tanggal_sidang', $hariTanggal);
            $tp->setValue('hari', $hariTanggal);
            
            $tp->setValue('waktu_waktu_selesai', $waktu);
            $tp->setValue('waktu', $waktu);
            $tp->setValue('ruangan', $ajuan->ruang_sidang ?? '');
            $tp->setValue('tempat', $ajuan->ruang_sidang ?? '');
            $tp->setValue('nama_wda', $wda ? $wda->NAMA_LENGKAP : '');
            $tp->setValue('wda', $wda ? $wda->NAMA_LENGKAP : '');
            $tp->setValue('nip_wda', $wda ? $wda->NIP_NIM : '');
            $tp->setValue('wda_nip', $wda ? $wda->NIP_NIM : '');
        } catch (\Exception $e) {
            abort(500, 'Gagal memproses template undangan kemajuan: ' . $e->getMessage());
        }

        $filename = 'Undangan_' . str_replace(' ', '_', $perihal) . '_' . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_' . ($ajuan->NIM ?? '') . '.pdf';

        $postSave = null;
        if ($kepadaParagraphs !== '') {
            $postSave = function ($docxPath) use ($kepadaParagraphs) {
                $z = new \ZipArchive();
                if ($z->open($docxPath) === true) {
                    $xml = $z->getFromName('word/document.xml');

                    // Replace the entire <w:p> that contains the {kepada} placeholder
                    // with multiple <w:p> rows (one per tim sidang member)
                    $xml = preg_replace(
                        '/<w:p\b[^>]*>(?:(?!<w:p[\s>]).)*?\{kepada\}.*?<\/w:p>/s',
                        $kepadaParagraphs,
                        $xml
                    );

                    // Fallback: plain string replace if the above regex didn't match
                    if (strpos($xml, '{kepada}') !== false) {
                        $xml = str_replace('{kepada}', $kepadaParagraphs, $xml);
                    }

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
        // Prepare paths
        $docxPath = storage_path('app/uploads/' . str_replace('.pdf', '.docx', $filename));

        try {
            // Ensure directory exists
            if (!is_dir(dirname($docxPath))) {
                \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
            }

            // Save the generated DOCX
            $tp->saveAs($docxPath);

            // Apply any post‑save modifications (e.g., inserting "kepada" rows)
            if ($postSave) {
                $postSave($docxPath);
            }

            // Always return the DOCX file to the client. The original $filename is a PDF name; we replace the extension.
            $docxFilename = str_replace('.pdf', '.docx', $filename);
            return response()->download($docxPath, $docxFilename)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            // In case of any error, fallback to sending the DOCX directly (if it exists).
            if (file_exists($docxPath)) {
                $docxFilename = str_replace('.pdf', '.docx', $filename);
                return response()->download($docxPath, $docxFilename)->deleteFileAfterSend(true);
            }
            // If DOCX could not be generated, return a generic error response.
            abort(500, 'Gagal menghasilkan dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Replace penilai values in BA template - one occurrence at a time
     */
    private function replacePenilaiValuesInDocx($docxPath, $penilaiAverages, $rataRataKeseluruhan, $indeks)
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            
            // Replace individual penilai values - one occurrence per penilai
            foreach ($penilaiAverages as $penilai) {
                $nilai = htmlspecialchars($penilai['rata_nilai'], ENT_XML1, 'UTF-8');
                
                // Replace first occurrence of each format
                $xml = preg_replace('/\$\{nilai_rata2_dari_form_penilaian\}/', $nilai, $xml, 1);
                $xml = preg_replace('/\$\(nilai_rata2_dari_form_penilaian\)/', $nilai, $xml, 1);
                $xml = preg_replace('/\$\{nilai rata2 dari form penilaian\}/', $nilai, $xml, 1);
                $xml = preg_replace('/\$\(nilai rata2 dari form penilaian\)/', $nilai, $xml, 1);
            }
            
            // Replace overall average (Nilai Akhir Rata-rata) - multiple variations
            $safeRataRata = htmlspecialchars($rataRataKeseluruhan, ENT_XML1, 'UTF-8');
            $xml = str_replace('${nilai_rata2_keseluruhan}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai_rata2_keseluruhan)', $safeRataRata, $xml);
            $xml = str_replace('${nilai rata2 keseluruhan}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai rata2 keseluruhan)', $safeRataRata, $xml);
            $xml = str_replace('${nilai_rata_rata}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai_rata_rata)', $safeRataRata, $xml);
            $xml = str_replace('${nilai rata rata}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai rata rata)', $safeRataRata, $xml);
            $xml = str_replace('${nilai_rata2}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai_rata2)', $safeRataRata, $xml);
            $xml = str_replace('${nilai rata2}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai rata2)', $safeRataRata, $xml);
            $xml = str_replace('${nilai_akhir_rata_rata}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai_akhir_rata_rata)', $safeRataRata, $xml);
            $xml = str_replace('${nilai akhir rata rata}', $safeRataRata, $xml);
            $xml = str_replace('$(nilai akhir rata rata)', $safeRataRata, $xml);
            
            // Special handling for the specific placeholder with spaces
            $xml = str_replace('${ Jumlah nilai dibagi dengan jumlah semua penilai }', $safeRataRata, $xml);
            $xml = str_replace('$(' . ' Jumlah nilai dibagi dengan jumlah semua penilai ' . ')', $safeRataRata, $xml);
            $xml = str_replace('${Jumlah nilai dibagi dengan jumlah semua penilai}', $safeRataRata, $xml);
            $xml = str_replace('$(Jumlah nilai dibagi dengan jumlah semua penilai)', $safeRataRata, $xml);
            
            // Replace index
            $safeIndeks = htmlspecialchars($indeks, ENT_XML1, 'UTF-8');
            $xml = str_replace('${nilai_akhir_indeks}', $safeIndeks, $xml);
            $xml = str_replace('$(nilai_akhir_indeks)', $safeIndeks, $xml);
            $xml = str_replace('${nilai akhir indeks}', $safeIndeks, $xml);
            $xml = str_replace('$(nilai akhir indeks)', $safeIndeks, $xml);
            
            // Clean up any remaining placeholders
            $xml = preg_replace('/\$\{nilai_rata2_dari_form_penilaian\}/', '', $xml);
            $xml = preg_replace('/\$\(nilai_rata2_dari_form_penilaian\)/', '', $xml);
            
            // Insert computed index (A/AB/B/BC) into the empty value cell of row 5 "Nilai Akhir (Indeks)"
            $xml = $this->fillProposalIndeksCell($xml, $indeks);
            
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
    }

    /**
     * Insert the computed index (A/AB/B/BC) into the empty value cell
     * of row 5 "Nilai Akhir (Indeks)" in the BA proposal.
     */
    private function fillProposalIndeksCell($xml, $indeks)
    {
        $needle = 'Nilai Akhir (Indeks)';
        $pos = strpos($xml, $needle);
        if ($pos === false) {
            return $xml;
        }

        $tcPos = strpos($xml, '<w:tc>', $pos);
        if ($tcPos === false) {
            return $xml;
        }

        $tcEnd = strpos($xml, '</w:tc>', $tcPos);
        if ($tcEnd === false) {
            return $xml;
        }

        $cell = substr($xml, $tcPos, $tcEnd - $tcPos);

        if (strpos($cell, '<w:r>') !== false || strpos($cell, '<w:r ') !== false) {
            return $xml;
        }

        $pEnd = strpos($cell, '</w:p>');
        if ($pEnd === false) {
            return $xml;
        }

        $run = '<w:r><w:rPr><w:rFonts w:ascii="Cambria" w:hAnsi="Cambria"/><w:b/><w:bCs/><w:color w:val="000000" w:themeColor="text1"/></w:rPr><w:t xml:space="preserve">'
            . htmlspecialchars($indeks, ENT_XML1, 'UTF-8')
            . '</w:t></w:r>';

        $cell = substr($cell, 0, $pEnd) . $run . substr($cell, $pEnd);

        return substr($xml, 0, $tcPos) . $cell . substr($xml, $tcEnd);
    }

    /**
     * Replace placeholders in Word document XML that use $() format
     */
    private function replaceInDocx($docxPath, $replacements)
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            
            // Fix split variables in Word XML
            $xml = preg_replace('/\$((?:<[^>]+>)*)\{((?:<[^>]+>)*)nama mhs((?:<[^>]+>)*)\}/', '${nama mhs}', $xml);
            $xml = preg_replace('/\$((?:<[^>]+>)*)\{((?:<[^>]+>)*)nim((?:<[^>]+>)*)\}/', '${nim}', $xml);
            $xml = preg_replace('/\{(?:<[^>]+>)*nama mhs(?:<[^>]+>)*\}/', '{nama mhs}', $xml);
            $xml = preg_replace('/\{(?:<[^>]+>)*nim(?:<[^>]+>)*\}/', '{nim}', $xml);
            
            foreach ($replacements as $key => $value) {
                // Support multiple formats
                $xml = str_replace('$(' . $key . ')', $value, $xml);
                $xml = str_replace('${' . $key . '}', $value, $xml);
                $xml = str_replace('{' . $key . '}', $value, $xml);
                
                // Handle underscores in placeholders (replace with underscores)
                $keyWithUnderscores = str_replace('_', ' ', $key);
                $xml = str_replace('$(' . $keyWithUnderscores . ')', $value, $xml);
                $xml = str_replace('${' . $keyWithUnderscores . '}', $value, $xml);
            }
            
            // Special handling for the specific placeholder with spaces
            $specialPlaceholder = '${ Jumlah nilai dibagi dengan jumlah semua penilai }';
            $specialPlaceholder2 = '$(' . ' Jumlah nilai dibagi dengan jumlah semua penilai ' . ')';
            $xml = str_replace($specialPlaceholder, $replacements[' Jumlah nilai dibagi dengan jumlah semua penilai '] ?? $replacements['Jumlah nilai dibagi dengan jumlah semua penilai'] ?? '', $xml);
            $xml = str_replace($specialPlaceholder2, $replacements[' Jumlah nilai dibagi dengan jumlah semua penilai '] ?? $replacements['Jumlah nilai dibagi dengan jumlah semua penilai'] ?? '', $xml);
            
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
    }
    
    /**
     * Handle both penguji and pembimbing in one method - simpler approach
     */
    private function handlePengujiAndPembimbingInXml($docxPath, $pembimbingNames, $pengujiList, $prodi = '')
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            
            // Replace penguji - find each occurrence and replace individually
            $pengujiCount = count($pengujiList);
            for ($i = 0; $i < $pengujiCount && $i < 3; $i++) {
                $nama = $pengujiList[$i]['nama'] ?? '';
                $institusi = $pengujiList[$i]['institusi'] ?? '';
                
                // Replace first occurrence only
                $xml = preg_replace(
                    '/\$\{nama_penguji\}/',
                    htmlspecialchars($nama, ENT_XML1, 'UTF-8'),
                    $xml,
                    1
                );
                $xml = preg_replace(
                    '/\$\{institusi\}/',
                    htmlspecialchars($institusi, ENT_XML1, 'UTF-8'),
                    $xml,
                    1
                );
            }
            
            // Replace pembimbing - find each occurrence and replace individually  
            $pembimbingCount = $pembimbingNames->count();
            for ($i = 0; $i < $pembimbingCount && $i < 3; $i++) {
                $nama = $pembimbingNames[$i] ?? '';
                
                // Replace first occurrence of $( pembimbing) (template variant split across runs)
                $xml = $this->replaceSplitPlaceholder($xml, '$( pembimbing)', $nama, 1);
                
                // Replace first occurrence of $(pembimbing)
                $xml = preg_replace(
                    '/\$\(pembimbing\)/',
                    htmlspecialchars($nama, ENT_XML1, 'UTF-8'),
                    $xml,
                    1
                );
                $xml = preg_replace(
                    '/\$\{pembimbing\}/',
                    htmlspecialchars($nama, ENT_XML1, 'UTF-8'),
                    $xml,
                    1
                );
            }
            
            // Replace prodi placeholder (split across runs, no $ prefix)
            $xml = $this->replaceSplitPlaceholder($xml, '{Teknik Geofisika nama prodi}', $prodi, 0);
            
            // Clean up any remaining placeholders
            $xml = str_replace('${nama_penguji}', '', $xml);
            $xml = str_replace('${institusi}', '', $xml);
            $xml = $this->replaceSplitPlaceholder($xml, '$( pembimbing)', '', 0);
            $xml = str_replace('$(pembimbing)', '', $xml);
            $xml = str_replace('${pembimbing}', '', $xml);
            
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();
        }
    }

    /**
     * Replace a placeholder text that is split across multiple XML runs,
     * allowing tags and whitespace between its characters.
     * $limit: number of occurrences to replace (0 = all).
     */
    private function replaceSplitPlaceholder($xml, $placeholder, $value, $limit = 0)
    {
        $valueXml = '<w:r><w:t xml:space="preserve">' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</w:t></w:r>';
        $parts = str_split($placeholder);
        $middle = '';
        foreach ($parts as $c) {
            $middle .= preg_quote($c, '~') . '(?:<[^>]*>|\s)*';
        }
        $pattern = '~<w:r\b[^>]*>(?:<[^>]*>|\s)*' . $middle . '</w:t></w:r>~';
        $counter = 0;
        return preg_replace_callback($pattern, function ($m) use ($valueXml, $value, $limit, &$counter) {
            if ($limit > 0 && $counter >= $limit) {
                return $m[0];
            }
            $counter++;
            if ($value === '') {
                return '';
            }
            return $valueXml;
        }, $xml);
    }

}
