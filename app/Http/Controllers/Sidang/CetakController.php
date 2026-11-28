<?php

namespace App\Http\Controllers\Sidang;

use App\Http\Controllers\Controller;
use App\Models\TJudul;
use App\Models\TAjuanSidang;
use App\Models\TTimSidang;
use App\Models\TPenilaian;
use App\Models\TUser;
use App\Models\TPointPenilaian;
use App\Models\TMkProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class CetakController extends Controller
{
    /**
     * Ambil ajuan sidang untuk judul+tahapan.
     * Jika baris ajuan belum ada untuk tahapan tersebut (mis. SK/tahap IV
     * yang jadwalnya belum dibuat), gunakan data identitas dari ajuan lain
     * pada judul yang sama agar dokumen tetap bisa dicetak.
     */
    private function resolveAjuan($idJudul, string $tahapan): ?TAjuanSidang
    {
        $ajuan = TAjuanSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->first();
        if ($ajuan) {
            return $ajuan;
        }

        $base = TAjuanSidang::where('id_judul', $idJudul)
            ->orderByDesc('id')
            ->first();
        if (!$base) {
            return null;
        }

        $stub = new TAjuanSidang();
        $stub->setRawAttributes([
            'ID_JUDUL'       => $base->ID_JUDUL,
            'TAHAPAN_SIDANG' => $tahapan,
            'JUDUL'          => $base->JUDUL,
            'NAMA_MHS'       => $base->NAMA_MHS,
            'NIM'            => $base->NIM,
            'NAMA_PRODI'     => $base->NAMA_PRODI,
            'KODE_PRODI'     => $base->KODE_PRODI,
            'ID_PRODI'       => $base->ID_PRODI,
        ], true);

        return $stub;
    }

    /**
     * Nama program studi untuk dokumen cetak.
     * Pakai NAMA_PRODI yang tersimpan di t_ajuan_sidang (bukan relasi prodi),
     * karena ID_PRODI kadang menunjuk prodi yang berbeda dari data mahasiswa.
     */
    private function namaProdiAjuan($ajuan): string
    {
        $nama = trim((string) ($ajuan->NAMA_PRODI ?? ''));
        return $nama !== '' ? $nama : '-';
    }

    /**
     * Pilih template Berita Acara S2 berdasarkan KODE_PRODI dan tahapan:
     * 221 -> BA Tesis I prodi 221
     * 222 -> Berita Acara Sidang prodi 222 (Teknik Perminyakan)
     * 223 -> BA Tesis I 223 TG / BA Tesis 2 223 TG (Tesis II)
     * 225 atau lainnya -> BA Tesis I prodi 225
     */
    private function getS2BeritaAcaraTemplate($kodeProdi, $tahapan = null, string $jenis = ''): string
    {
        $kp = trim((string) $kodeProdi);
        $tahapanNorm = strtolower(trim((string) $tahapan));
        $isTesisII = $tahapanNorm === 'tesis ii' || $tahapanNorm === 'tesis 2';

        if ($kp === '221') {
            // Tesis II prodi 221: pilihan "Mata Kuliah" memakai BA MK khusus.
            if ($isTesisII && strtolower(trim($jenis)) === 'mata_kuliah') {
                return base_path('template/S2/BA penilaian tesis 2 MK kode prodi 221 TEMPLATE.docx');
            }
            return base_path('template/S2/BA Tesis I prodi 221 TEMPLATE.docx');
        } elseif ($kp === '222') {
            return base_path('template/S2/Berita Acara Sidang prodi 222 TEMPLATE.docx');
        } elseif ($kp === '223') {
            return $isTesisII
                ? base_path('template/S2/BA Tesis 2 223 TG TEMPLATE.docx')
                : base_path('template/S2/BA Tesis I 223 TG TEMPLATE.docx');
        }
        return base_path('template/S2/BA Tesis I prodi 225 TEMPLATE.docx');
    }

    /**
     * Pilih template Form Penilaian S2 berdasarkan KODE_PRODI dan tahapan:
     * 221 -> form penilaian prodi 221 / penilaian tesis 2 prodi 221,225,226 (Tesis II)
     * 222 -> Penilaian Sidang Tesis I dan II prodi 222 (Teknik Perminyakan)
     * 223 -> form penilaian Tesis I 223 TG / form penilaian Tesis 2 prodi 223 TG
     * 225, 226 -> form penilaian Tesis I prodi 225 / penilaian tesis 2 prodi 221,225,226 (Tesis II)
     */
    private function getS2FormTemplate($kodeProdi, $tahapan = null, string $jenis = ''): string
    {
        $kp = trim((string) $kodeProdi);
        $tahapanNorm = strtolower(trim((string) $tahapan));
        $isTesisII = $tahapanNorm === 'tesis ii' || $tahapanNorm === 'tesis 2';

        // Tesis II prodi 221: pilihan "Mata Kuliah" memakai form penilaian MK khusus.
        if ($kp === '221' && $isTesisII && strtolower(trim($jenis)) === 'mata_kuliah') {
            return base_path('template/S2/form penilaian tesis 2 MK kode prodi 221 TEMPLATE.docx');
        }

        if ($isTesisII && in_array($kp, ['221', '225', '226'], true)) {
            return base_path('template/S2/penilaian tesis 2 prodi 221,225,226 TEMPLATE.docx');
        } elseif ($kp === '221') {
            return base_path('template/S2/form penilaian prodi 221 TEMPLATE.docx');
        } elseif ($kp === '222') {
            return base_path('template/S2/Penilaian Sidang Tesis I dan II prodi 222 TEMPLATE.docx');
        } elseif ($kp === '223') {
            return $isTesisII
                ? base_path('template/S2/form penilaian Tesis 2 prodi 223 TG TEMPLATE.docx')
                : base_path('template/S2/form penilaian Tesis I 223 TG TEMPLATE.docx');
        }
        return base_path('template/S2/form penilaian Tesis I prodi 225 TEMPLATE.docx');
    }

    /**
     * Pilih template Berita Acara S1 berdasarkan KODE_PRODI dan tahapan (TA I / TA II):
     * 122 -> BA TA 1  prodi 122 / BA Sidang TA 2 prodi 122
     * 123 -> BA TA-1 prodi 123 / BA Sidang TA 2 prodi 123
     * 125 -> BA TA 1 TA 2 prodi 125 MG
     */
    private function getS1BeritaAcaraTemplate($kodeProdi, $tahapan = null): string
    {
        $kp = trim((string) $kodeProdi);
        $tahapanNorm = strtolower(trim((string) $tahapan));
        $isTa2 = in_array($tahapanNorm, ['ta ii', 'ta 2', 'ta-2'], true);

        if ($kp === '122') {
            return $isTa2
                ? base_path('template/S1/BA Sidang TA 2 prodi 122 TEMPLATE.docx')
                : base_path('template/S1/BA TA 1  prodi 122 TEMPLATE.docx');
        } elseif ($kp === '123') {
            return $isTa2
                ? base_path('template/S1/BA Sidang TA 2 prodi 123 TEMPLATE.docx')
                : base_path('template/S1/BA TA-1 prodi 123 TEMPLATE.docx');
        } elseif ($kp === '125') {
            return base_path('template/S1/BA TA 1 TA 2 prodi 125 MG TEMPLATE.docx');
        }

        return base_path('template/pROPOSAL/BA proposal TEMPLATE.docx');
    }

    /**
     * Pilih template Form Penilaian S1 berdasarkan KODE_PRODI:
     * 122 -> Lembar Penilai an TA1 TA 2 prodi 122
     * 123 -> penilaian TA I dan II prodi 123 TG
     * 125 -> Lembar Penilaian Skripsi prodi 125 Metalurgi
     */
    private function getS1FormTemplate($kodeProdi, string $jenis = '', string $noForm = ''): string
    {
        $kp = trim((string) $kodeProdi);

        if ($kp === '122') {
            return base_path('template/S1/Lembar Penilai an TA1 TA 2 prodi 122 TEMPLATE.docx');
        } elseif ($kp === '123') {
            return base_path('template/S1/penilaian TA I dan II prodi 123 TG TEMPLATE.docx');
        } elseif ($kp === '125') {
            if (str_contains(strtolower($jenis), 'publikasi') || str_contains(strtolower($noForm), 'publikasi')) {
                return base_path('template/S1/Form Penilaian Publikasi TA 2 prodi 125 TEMPLATE.docx');
            }
            return base_path('template/S1/Lembar Penilaian Skripsi prodi 125 Metalurgi TEMPLATE.docx');
        }

        return base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai TEMPLATE.docx');
    }

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

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
        }

        $idTimSidang = $request->input('id_tim_sidang');
        $noForm      = $request->input('no_form');
        // 221 Tesis II: 'sidang' (template lama) atau 'mata_kuliah' (template MK baru).
        $jenisCetak  = strtolower(trim((string) $request->input('jenis', '')));

        if (!$idTimSidang) {
            return response()->json(['error' => 'id_tim_sidang diperlukan.'], 400);
        }

        // Kode prodi dari ajuan (fallback ke judul) agar point penilaian dicetak per-prodi
        $kodeProdi = $ajuan->KODE_PRODI ?? $ajuan->kode_prodi
            ?? $judul->KODE_PRODI ?? $judul->kode_prodi
            ?? null;

        // Prodi 125 S1: selaraskan $noForm dengan pilihan modal `jenis` (SKRIPSI / SIDANG / PUBLIKASI)
        if (strtoupper((string) ($ajuan->STRATA ?? '')) === 'S1' && trim((string) $kodeProdi) === '125' && $jenisCetak !== '') {
            $matchedForm125 = TPointPenilaian::where('KODE_PRODI', '125')
                ->where('TAHAPAN_SIDANG', $tahapan)
                ->whereRaw("LOWER(COALESCE(NO_FORM, '')) LIKE ?", ['%' . $jenisCetak . '%'])
                ->value('NO_FORM');
            if ($matchedForm125) {
                $noForm = $matchedForm125;
            } elseif (!$noForm) {
                $noForm = $jenisCetak;
            }
        }

        // Data tim sidang (penilai yang dipilih)
        $timSidangRecord = TTimSidang::find($idTimSidang);

        // Semua tim sidang untuk header pembimbing
        $allTimSidang = TTimSidang::with('userPenilai')
            ->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderByRaw("CASE
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'III$' THEN 3
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'II$' THEN 2
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'I$' THEN 1
                ELSE 0 END")
            ->orderBy('URUTAN')
            ->get();

        // Fallback S1: bila Pembimbing hanya diinput di tahapan lain (mis. TA I saat cetak TA II), ambil dari judul yg sama
        $hasPembCurrent = $allTimSidang->contains(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'));
        if (!$hasPembCurrent) {
            $otherPemb = TTimSidang::with('userPenilai')
                ->where('id_judul', $idJudul)
                ->whereRaw("LOWER(COALESCE(STATUS_TIM_SIDANG, '')) LIKE '%pembimbing%'")
                ->orderByRaw("CASE
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'III$' THEN 3
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'II$' THEN 2
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'I$' THEN 1
                    ELSE 0 END")
                ->orderBy('URUTAN')
                ->get();
            if ($otherPemb->isNotEmpty()) {
                $allTimSidang = $otherPemb->concat($allTimSidang)->values();
            }
        }

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
            $query->whereRaw('LOWER(TRIM(NO_FORM)) = ?', [strtolower(trim((string) $noForm))]);
        }
        $penilaianRows = $query->orderBy('id_penilaian')->get();
        
        // Load KETERANGAN from TPointPenilaian for existing records
        if ($penilaianRows->isNotEmpty()) {
            foreach ($penilaianRows as $row) {
                if ($row->pointPenilaian) {
                    $row->KETERANGAN = $row->pointPenilaian->KETERANGAN;
                    // STATUS_CATATAN tidak ada di t_penilaian; ambil dari master point
                    // agar baris bertipe teks (STATUS_CATATAN='y') bisa dibedakan
                    // dari baris nilai saat cetak (mis. template prodi 222).
                    $row->STATUS_CATATAN = $row->pointPenilaian->STATUS_CATATAN ?? '';
                    $row->BOBOT = $row->pointPenilaian->BOBOT ?? $row->pointPenilaian->bobot ?? null;
                } else {
                    $row->KETERANGAN = '';
                }
            }
        }

        // Jika belum ada record, ambil point penilaian (baris kosong) — per prodi
        if ($penilaianRows->isEmpty()) {
            $pointQuery = TPointPenilaian::where('tahapan_sidang', $tahapan)
                ->whereIn('status_aktif', ['AKTIF', 'aktif', 'Aktif', 'y', 'Y', 't']);
            if ($kodeProdi) {
                $pointQuery->where('KODE_PRODI', $kodeProdi);
            }
            if ($noForm) {
                $pointQuery->whereRaw('LOWER(TRIM(NO_FORM)) = ?', [strtolower(trim((string) $noForm))]);
            }
            $pointRows     = $pointQuery->orderBy('id')->get();
            $penilaianRows = $pointRows->map(fn($p) => (object)[
                'NAMA_PENILAIAN' => $p->PENILAIAN  ?? '',
                'KETERANGAN'     => $p->KETERANGAN ?? '',
                'BOBOT'          => $p->BOBOT ?? $p->bobot ?? null,
                'NILAI'          => '',
                'CEK_MAKALAH'    => '',
                'CATATAN'        => '',
                'STATUS_CATATAN' => $p->STATUS_CATATAN ?? $p->status_catatan ?? '',
                'NAMA'           => $timSidangRecord?->NAMA ?? '-',
                'NIP'            => $timSidangRecord?->NIP  ?? '-',
                'NO_FORM'        => $p->NO_FORM ?? '',
                'TGL_UPDATE'     => null,
                'TGL_CREATE'     => null,
            ]);
        }

        // Rata-rata nilai: jumlah total skor dibagi jumlah kriteria detail (skala dinamis).
        // Baris "Usulan Perbaikan" dan baris bertipe teks (STATUS_CATATAN=y) bukan kriteria
        // ber-skala (bobotnya bukan angka) â€” jangan dihitung.
        $detailRows = $penilaianRows->filter(function ($r) {
            $nama          = (string) ($r->NAMA_PENILAIAN ?? $r->PENILAIAN ?? '');
            $statusCatatan = strtolower((string) ($r->STATUS_CATATAN ?? $r->status_catatan ?? ''));
            return strtolower(trim($nama)) !== 'usulan perbaikan'
                && $statusCatatan !== 'y';
        });
        $jumlahDetail = $detailRows->count();
        $nilaiValues  = $detailRows
            ->pluck('NILAI')
            ->filter(fn($v) => $v !== '' && $v !== null);
        $hasBobotDetail = $detailRows->contains(function ($r) {
            $b = $r->BOBOT ?? $r->bobot ?? null;
            return $b !== null && is_numeric($b) && (float) $b > 0;
        });
        if ($nilaiValues->count() > 0) {
            if ($hasBobotDetail) {
                $sumBobotNilai = $detailRows->sum(function ($r) {
                    $v = $r->NILAI ?? null;
                    $b = $r->BOBOT ?? $r->bobot ?? null;
                    if ($v === '' || $v === null || !is_numeric($v) || !is_numeric($b)) {
                        return 0.0;
                    }
                    return (((float) $b) / 100.0) * ((float) $v);
                });
                $rataNilai = number_format($sumBobotNilai, 2);
            } else {
                $rataNilai = number_format($nilaiValues->sum() / max(1, $jumlahDetail), 2);
            }
        } else {
            $rataNilai = '';
        }

        // Info penilai & tanggal (tanggal penilaian = tgl create penilaian)
        $firstRow    = $penilaianRows->first();
        $noForm      = $noForm ?: ($firstRow?->NO_FORM ?? $firstRow?->no_form ?? null);
        $tgl         = $firstRow?->TGL_CREATE ?? $firstRow?->TGL_UPDATE ?? null;
        $tanggal     = $tgl ? \Carbon\Carbon::parse($tgl)->translatedFormat('d F Y') : '';
        $penilaiNama = (isset($firstRow->NAMA) && trim((string) $firstRow->NAMA) !== '')
            ? $firstRow->NAMA
            : ($timSidangRecord?->NAMA ?: ($user['nama_lengkap'] ?? '-'));
        $penilaiNip  = (isset($firstRow->NIP) && trim((string) $firstRow->NIP) !== '')
            ? $firstRow->NIP
            : ($timSidangRecord?->NIP ?: ($user['nip_nim'] ?? '-'));

        // Deteksi tahapan SK (SK I, SK II, SK III, SK IV) -> gunakan template SK
        // Deteksi tahapan Sidang Doktor (tahap IV / Sidang Terbuka/Tertutup) -> template form penilaian sidang akhir
        $tNormal       = strtolower(trim($tahapan));
        $isSk          = preg_match('/^sk\s+(i{1,3}|iv)$/', $tNormal, $skMatch) === 1;
        $skRomawi      = $isSk ? strtoupper($skMatch[1]) : '';
        $isSidangDoktor = preg_match('/^tahap\s+iv$/', $tNormal) === 1;
        $isS1 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S1';
        $isS2 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S2';
        $isS1Form = $isS1 && !$isSk && !$isSidangDoktor && in_array(trim((string) $kodeProdi), ['122', '123', '125'], true);

        // Kode MK dari Data Master Mata Kuliah Prodi (t_mk_prodi):
        // dicocokkan dengan prodi mahasiswa + tahapan yang dicetak (SK/S2).
        $kodeMk = '';
        if (($isSk || $isS2) && $kodeProdi) {
            $mkRecord = TMkProdi::query()
                ->whereRaw('TRIM(KODE_PRODI) = ?', [trim((string) $kodeProdi)])
                ->whereRaw('UPPER(TRIM(TAHAPAN_SIDANG)) = ?', [strtoupper(trim($tahapan))])
                ->first();
            $kodeMk = trim((string) ($mkRecord->KODE_MK ?? ''));
        }

        // Tanggal cetak (fallback ke tanggal sidang jika penilaian belum diisi)
        $tglCreate = $tanggal;
        if ($tglCreate === '' && $ajuan->tgl_sidang) {
            $tglCreate = \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('d F Y');
        }

        $tglSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';

        // Template: S1 Prodi 122/123/125 selalu pakai template khusus S1 (jangan tertimpa deteksi $isTextForm).
        $isTextForm = false;
        if ($noForm && !$isSk && !$isSidangDoktor && !$isS1Form) {
            $pointsForForm = TPointPenilaian::where('tahapan_sidang', $tahapan)
                ->where('no_form', $noForm)
                ->whereIn('status_aktif', ['AKTIF', 'aktif', 'Aktif', 'y', 'Y', 't']);
            if ($kodeProdi) {
                $pointsForForm->where('KODE_PRODI', $kodeProdi);
            }
            $pointsForForm = $pointsForForm->get();
            $isTextForm = $pointsForForm->isNotEmpty()
                && $pointsForForm->every(fn($p) => strtolower((string) ($p->STATUS_CATATAN ?? $p->status_catatan ?? '')) === 'y');
        }

        // Siapkan nilai/catatan: tipe dokumen -> "terlampir"
        $penilaianRows = $penilaianRows->map(function ($row) {
            $nama = strtolower((string) ($row->NAMA_PENILAIAN ?? $row->PENILAIAN ?? ''));
            $ket  = strtolower((string) ($row->KETERANGAN ?? ''));
            $isDokumen = str_contains($nama, 'dokumen')
                || str_contains($ket, 'dokumen')
                || str_contains($nama, 'lampiran')
                || str_contains($ket, 'lampiran');
            if ($isDokumen) {
                if (is_object($row)) {
                    $row->CATATAN = 'terlampir';
                    $row->catatan = 'terlampir';
                    if (($row->NILAI ?? $row->nilai ?? '') === '' || ($row->NILAI ?? null) === null) {
                        $row->NILAI = 'terlampir';
                    }
                }
            }
            return $row;
        });

        // Template: S2 pakai template per-KODE_PRODI (221/222/223 TG/225/226), kecuali SK dan Text Form

        // 221 Tesis II dengan pilihan "Mata Kuliah": form penilaian MK khusus.
        $isForm221Mk = $isS2 && trim((string) $kodeProdi) === '221'
            && strtolower(trim($tahapan)) === 'tesis ii'
            && $jenisCetak === 'mata_kuliah';

        // Prodi 222 (Teknik Perminyakan): template Lembar Penguji gabungan (teks + tabel nilai).
        // Selalu dipakai (mengalahkan deteksi isTextForm) karena satu template memuat keduanya.
        $isS2Prodi222 = $isS2 && trim((string) $kodeProdi) === '222' && !$isSk && !$isSidangDoktor;

        // S2 tipe teks/catatan: pakai template Lembar Saran Perbaikan Tesis (S2),
        // bukan template tipe text generik S3/Diskertasi.
        $isS2TextForm = $isS2 && !$isSk && !$isSidangDoktor && $isTextForm && ! $isS2Prodi222;

        $isForm125Publikasi = $isS1Form && trim((string) $kodeProdi) === '125'
            && (str_contains($jenisCetak, 'publikasi') || str_contains(strtolower((string) $noForm), 'publikasi'));

        if ($isS1Form) {
            $templatePath = $this->getS1FormTemplate($kodeProdi, (string) $jenisCetak, (string) ($noForm ?? ''));
        } elseif ($isS2Prodi222) {
            $templatePath = $this->getS2FormTemplate($kodeProdi, $tahapan, $jenisCetak);
        } elseif ($isS2 && !$isSk && !$isSidangDoktor && !$isTextForm) {
            $templatePath = $this->getS2FormTemplate($kodeProdi, $tahapan, $jenisCetak);
        } elseif ($isS2TextForm) {
            // Semua S2 (termasuk 223) memakai Lembar Saran Perbaikan (form penilaian catatan).
            $templatePath = base_path('template/S2/form penilaian catatan TEMPLATE.docx');
        } else {
            $templatePath = $isSk
                ? base_path('template/SK-1/form penilaian sk I sd sk III TEMPLATE.docx')
                : ($isSidangDoktor
                    ? base_path('template/SIDANG/form penilaian sidang akhir TEMPLATE.docx')
                    : ($isTextForm
                        ? base_path('template/cetak form penilain tipe text TEMPLATE.docx')
                        : base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai TEMPLATE.docx')));
        }
        if (!file_exists($templatePath) && $isTextForm && ! $isS2TextForm) {
            // Fallback jika template tipe text belum ada
            $templatePath = base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai TEMPLATE.docx');
        }
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template cetak penilaian tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            $namaJudul = $ajuan->JUDUL ?? $judul->JUDUL ?? '';

            // Nama penguji untuk placeholder header template SK
            $pengujiNames = $allTimSidang
                ->filter(fn($t) => preg_match('/^penguji\s+(i{1,3}|iv|v)$/i', strtolower(trim($t->status_tim_sidang ?? ''))))
                ->sortBy(fn($t) => strtolower(trim($t->status_tim_sidang ?? '')))
                ->values()
                ->map(fn($t) => $t->NAMA ?? '-')
                ->all();

            $pembimbings = $allTimSidang->filter(fn($t) =>
                str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
            )->values();
            $p1 = $ketuaPembimbing ?? $pembimbings->get(0);
            $p2 = $pembimbings->first(fn($t) => $t->id !== ($p1?->id));

            $namaP1 = $isS2 ? ($p1?->NAMA ?? '-') : ($anggotaPembimbing->get(0)?->NAMA ?? '-');
            $namaP2 = $isS2 ? ($p2?->NAMA ?? '-') : ($anggotaPembimbing->get(1)?->NAMA ?? '-');

            $tp->setValue('nama_mhs',              $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',                   $ajuan->NIM ?? '-');
            $tp->setValue('nama_ketua_pembimbing', $ketuaPembimbing?->NAMA ?? '-');
            $tp->setValue('nama_pembimbing_i',     $namaP1);
            $tp->setValue('nama_pembimbing_ii',    $namaP2);
            $tp->setValue('nama pembimbing I',     $namaP1);
            $tp->setValue('nama pembimbing II',    $namaP2);
            $tp->setValue('nama_penguji_i',        $pengujiNames[0] ?? '-');
            $tp->setValue('nama_penguji_ii',       $pengujiNames[1] ?? '-');
            $tp->setValue('nama_penilai',          $penilaiNama);
            $tp->setValue('nip_penilai',           $penilaiNip);
            $tp->setValue('tgl_create_penilaian',  $tglCreate);

            // S2: nilai untuk template Berita Acara / Form Penilaian Magister
            if (($isS2 && !$isSk && !$isSidangDoktor && !$isTextForm) || $isS2Prodi222) {
                $pembimbings = $allTimSidang->filter(fn($t) =>
                    str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
                )->values();
                $p1 = $ketuaPembimbing ?? $pembimbings->get(0);
                $p2 = $pembimbings->first(fn($t) => $t->id !== ($p1?->id));

                $statusTimSidang = strtoupper((string) ($timSidangRecord?->status_tim_sidang ?? ''));

                $prodiS2 = \App\Models\TProdi::where('KODE_PRODI', $kodeProdi)->first();

                // Prodi 225: MK diambil dari pilihan di modal Cetak Penilaian.
                $kodeMkForm = trim((string) $request->input('kode_mk', ''));
                if (trim((string) $kodeProdi) === '225' && $kodeMkForm !== '') {
                    $kodeMk = $kodeMkForm;
                }

                $tp->setValue('status_tim_sidang',     $statusTimSidang);
                $tp->setValue('no_form',               $noForm ?? '');
                $tp->setValue('kode_mk',               $kodeMk ?? '');
                $tp->setValue('kode mk',               $kodeMk ?? '');
                if ($isForm221Mk) {
                    // Template MK memakai ${nilai_rata2} sekali di ringkasan;
                    // tidak diisi oleh fillPenilaianRows (baris detail memakai token ber-indeks).
                    $tp->setValue('nilai_rata2', $rataNilai);
                }
                $tp->setValue('nama_pembimbing_i',     $p1?->NAMA ?? '-');
                $tp->setValue('nama_pembimbing_ii',    $p2?->NAMA ?? '-');
                $tp->setValue('judul',           $namaJudul);
                $tp->setValue('nama mhs',        $ajuan->NAMA_MHS ?? '-');
                $tp->setValue('nim',             $ajuan->NIM ?? '-');
                $tp->setValue('tgl sidang',      $tglSidang);
                $tp->setValue('tgl_sidang',      $tglSidang);
                $tp->setValue('waktu',           $ajuan->waktu_sidang ?? '');
                $tp->setValue('ruang',           $ajuan->ruang_sidang ?? '');
                $tp->setValue('ruang_sidang',    $ajuan->ruang_sidang ?? '');
                $tp->setValue('alamat',          $prodiS2?->ALAMAT ?? '-');
                $tp->setValue('alamat_prodi',    $prodiS2?->ALAMAT ?? '-');
                $tp->setValue('tlp',             $prodiS2?->TLP ?? '-');
                $tp->setValue('tlp_prodi',       $prodiS2?->TLP ?? '-');
                $tp->setValue('email',           $prodiS2?->EMAIL ?? '-');
                $tp->setValue('email_prodi',     $prodiS2?->EMAIL ?? '-');
                $tp->setValue('fax',             $prodiS2?->FAX ?? '-');
                $tp->setValue('fax_prodi',       $prodiS2?->FAX ?? '-');
                $tp->setValue('web',             $prodiS2?->WEB ?? '-');
                $tp->setValue('website_prodi',   $prodiS2?->WEB ?? '-');
                $tp->setValue('nama_prodi',      $prodiS2?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('NAMA PRODI',      $prodiS2?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
            }

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
            } elseif ($isTextForm) {
                // Template tipe teks memakai ${judul}, ${no_form}, ${nip_penilai}
                $tp->setValue('judul',       $namaJudul);
                $tp->setValue('no_form',     $noForm ?? '');
                $tp->setValue('nip_penilai', $penilaiNip);
                if ($isS2TextForm) {
                    // Lembar Saran Perbaikan S2: satu ${catatan} berisi gabungan catatan penilai.
                    // Gunakan toArray()/akses properti langsung — (array) cast pada model Eloquent
                    // TIDAK mengekspos atribut (tersimpan di protected $attributes).
                    $allCatatan = [];
                    foreach ($penilaianRows as $row) {
                        $isiCatatan = '';
                        if (is_object($row) && method_exists($row, 'toArray')) {
                            $rowArr = $row->toArray();
                            $isiCatatan = $rowArr['CATATAN'] ?? $rowArr['catatan'] ?? '';
                        } elseif (is_object($row)) {
                            $isiCatatan = $row->CATATAN ?? $row->catatan ?? '';
                        } else {
                            $isiCatatan = $row['CATATAN'] ?? $row['catatan'] ?? '';
                        }
                        if (trim((string) $isiCatatan) !== '') {
                            $allCatatan[] = trim((string) $isiCatatan);
                        }
                    }
                    $tp->setValue('catatan',    implode("\n", $allCatatan));
                    $tp->setValue('tgl_sidang', $tglSidang);

                    // Identitas prodi untuk blok footer Lembar Saran Perbaikan
                    $prodiCatatan = \App\Models\TProdi::where('KODE_PRODI', $kodeProdi)->first();
                    $tp->setValue('nama_prodi',    $prodiCatatan?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                    $tp->setValue('alamat_prodi',  $prodiCatatan?->ALAMAT ?? '-');
                    $tp->setValue('tlp_prodi',     $prodiCatatan?->TLP ?? '-');
                    $tp->setValue('fax_prodi',     $prodiCatatan?->FAX ?? '-');
                    $tp->setValue('email_prodi',   $prodiCatatan?->EMAIL ?? '-');
                    $tp->setValue('website_prodi', $prodiCatatan?->WEB ?? '-');
                }
            } elseif ($isS1Form) {
                $prodiS1 = \App\Models\TProdi::where('KODE_PRODI', $kodeProdi)->first();
                $kpS1 = trim((string) $kodeProdi);
                $hariSidangS1 = $ajuan->tgl_sidang
                    ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l')
                    : '-';
                $tglSidangS1 = $ajuan->tgl_sidang
                    ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('d F Y')
                    : '-';
                $waktuS1 = $ajuan->waktu_sidang
                    ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i')
                    : '-';
                $statusTimS1 = strtoupper((string) ($timSidangRecord?->status_tim_sidang ?? ''));

                $tp->setValue('nama_prodi',        $prodiS1?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('judul',             $namaJudul);
                $tp->setValue('hari_sidang',       $hariSidangS1);
                $tp->setValue('tgl_sidang',        $tglSidangS1);
                $tp->setValue('waktu',             $waktuS1);
                $tp->setValue('ruang_sidang',      $ajuan->ruang_sidang ?? '-');
                $tp->setValue('status_tim_sidang', $statusTimS1);

                if ($kpS1 === '122') {
                    $textRows122 = [];
                    $nilaiRows122 = [];
                    foreach ($penilaianRows as $r) {
                        $sc = strtolower(trim((string) ($r->STATUS_CATATAN ?? $r->status_catatan ?? '')));
                        if ($sc === 'y') {
                            $textRows122[] = $r;
                        } else {
                            $nilaiRows122[] = $r;
                        }
                    }
                    for ($i = 0; $i < 3; $i++) {
                        $rowT = $textRows122[$i] ?? null;
                        $tp->setValue('param_text_' . ($i + 1),   $rowT ? ($rowT->NAMA_PENILAIAN ?? $rowT->PENILAIAN ?? '') : '');
                        $tp->setValue('catatan_text_' . ($i + 1), $rowT ? ($rowT->CATATAN ?? '') : '');
                    }
                    $jmlNilai122 = 0.0;
                    for ($i = 0; $i < 7; $i++) {
                        $rowN = $nilaiRows122[$i] ?? null;
                        $nVal = $rowN ? ($rowN->NILAI ?? '') : '';
                        $hVal = '';
                        if ($rowN && $nVal !== '' && $nVal !== null && is_numeric($nVal)) {
                            $jmlNilai122 += (float) $nVal;
                            $hVal = $this->calculateIndeksNilai222((float) $nVal);
                        }
                        $tp->setValue('param_nilai_' . ($i + 1), $rowN ? ($rowN->NAMA_PENILAIAN ?? $rowN->PENILAIAN ?? '') : '');
                        $tp->setValue('nilai_' . ($i + 1),       $nVal !== '' && $nVal !== null ? (string) $nVal : '');
                        $tp->setValue('nilai_huruf_' . ($i + 1), $hVal);
                    }
                    $rataNum122 = is_numeric($rataNilai) ? (float) $rataNilai : 0.0;
                    $tp->setValue('jml_nilai',         $jmlNilai122 > 0 ? (string) $jmlNilai122 : '');
                    $tp->setValue('nilai_rata2',       $rataNilai);
                    $tp->setValue('nilai_huruf_rata2', $rataNilai !== '' ? $this->calculateIndeksNilai222($rataNum122) : '');
                } elseif ($kpS1 === '123') {
                    $isTa2 = in_array(strtolower(trim($tahapan)), ['ta ii', 'ta 2', 'ta-2'], true);
                    $tp->setValue('tahap_angka', $isTa2 ? '2' : '1');
                    $rowSkripsi = $penilaianRows->first(fn($r) => str_contains(strtolower((string) ($r->NAMA_PENILAIAN ?? $r->PENILAIAN ?? '')), 'skripsi'));
                    $rowUmum    = $penilaianRows->first(fn($r) => str_contains(strtolower((string) ($r->NAMA_PENILAIAN ?? $r->PENILAIAN ?? '')), 'umum'));
                    $tp->setValue('status_lulus_skripsi', $rowSkripsi?->STATUS_LULUS ?? '-');
                    $tp->setValue('nilai_huruf_skripsi',  $rowSkripsi?->NILAI_HURUF  ?? '-');
                    $tp->setValue('status_lulus_umum',    $rowUmum?->STATUS_LULUS    ?? '-');
                    $tp->setValue('nilai_huruf_umum',     $rowUmum?->NILAI_HURUF     ?? '-');
                    $tp->setValue('catatan_1',            $rowSkripsi?->CATATAN      ?? '');
                    $tp->setValue('catatan_2',            $rowUmum?->CATATAN         ?? '');
                } elseif ($kpS1 === '125') {
                    if ($isForm125Publikasi) {
                        $masterPub125 = TPointPenilaian::where('KODE_PRODI', '125')
                            ->where('TAHAPAN_SIDANG', $tahapan)
                            ->whereIn('status_aktif', ['AKTIF', 'aktif', 'Aktif', 'y', 'Y', 't'])
                            ->whereRaw("LOWER(COALESCE(NO_FORM, '')) LIKE '%publikasi%'")
                            ->orderBy('id')
                            ->get();

                        $savedById125 = [];
                        $savedByName125 = [];
                        foreach ($penilaianRows as $sr) {
                            $pid = $sr->ID_PENILAIAN ?? $sr->id_penilaian ?? null;
                            if ($pid) {
                                $savedById125[(int) $pid] = $sr;
                            }
                            $nmKey = strtolower(trim((string) ($sr->NAMA_PENILAIAN ?? $sr->PENILAIAN ?? '')));
                            if ($nmKey !== '') {
                                $savedByName125[$nmKey] = $sr;
                            }
                        }

                        $kriteriaPub125 = [];
                        $catatanPubList125 = [];

                        if ($masterPub125->isNotEmpty()) {
                            foreach ($masterPub125 as $mp) {
                                $sc = strtolower(trim((string) ($mp->STATUS_CATATAN ?? $mp->status_catatan ?? '')));
                                $nmLower = strtolower(trim((string) ($mp->PENILAIAN ?? '')));
                                $savedRow = $savedById125[(int) $mp->id] ?? ($savedByName125[$nmLower] ?? null);
                                if ($sc === 'y') {
                                    $catTxt = trim((string) ($savedRow?->CATATAN ?? $savedRow?->catatan ?? ''));
                                    if ($catTxt !== '' && strtolower($catTxt) !== 'terlampir') {
                                        $catatanPubList125[] = $catTxt;
                                    }
                                } else {
                                    $kriteriaPub125[] = [
                                        'nama' => (string) ($mp->PENILAIAN ?? ''),
                                        'cek'  => strtolower(trim((string) ($savedRow?->CEK_MAKALAH ?? $savedRow?->cek_makalah ?? ''))),
                                    ];
                                    $extraCat = trim((string) ($savedRow?->CATATAN ?? $savedRow?->catatan ?? ''));
                                    if ($extraCat !== '' && strtolower($extraCat) !== 'terlampir') {
                                        $catatanPubList125[] = $extraCat;
                                    }
                                }
                            }
                        } else {
                            foreach ($penilaianRows as $sr) {
                                $sc = strtolower(trim((string) ($sr->STATUS_CATATAN ?? $sr->status_catatan ?? '')));
                                if ($sc === 'y') {
                                    $catTxt = trim((string) ($sr->CATATAN ?? $sr->catatan ?? ''));
                                    if ($catTxt !== '' && strtolower($catTxt) !== 'terlampir') {
                                        $catatanPubList125[] = $catTxt;
                                    }
                                } else {
                                    $kriteriaPub125[] = [
                                        'nama' => (string) ($sr->NAMA_PENILAIAN ?? $sr->PENILAIAN ?? ''),
                                        'cek'  => strtolower(trim((string) ($sr->CEK_MAKALAH ?? $sr->cek_makalah ?? ''))),
                                    ];
                                }
                            }
                        }

                        for ($i = 0; $i < 6; $i++) {
                            $itemPub = $kriteriaPub125[$i] ?? null;
                            $isCek = $itemPub && in_array($itemPub['cek'], ['y', 'ya', '1', 'true', 'on'], true);
                            $tp->setValue('nama_penilaian_' . ($i + 1), $itemPub['nama'] ?? '');
                            $tp->setValue('cek_makalah_' . ($i + 1), $isCek ? '✓' : '');
                        }
                        $tp->setValue('catatan', !empty($catatanPubList125) ? implode('; ', array_unique($catatanPubList125)) : '-');

                        $kaprodiPub125 = null;
                        if (!empty($ajuan->ID_PRODI)) {
                            $kapUid125 = DB::table('t_user_prodi')
                                ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.id')
                                ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                                ->where('t_user.STATUS_KAPRODI', 'y')
                                ->value('t_user_prodi.ID_USER');
                            if ($kapUid125) {
                                $kaprodiPub125 = TUser::find($kapUid125);
                            }
                        }
                        if (!$kaprodiPub125) {
                            $kaprodiPub125 = TUser::where('KODE_PRODI', '125')->where('STATUS_KAPRODI', 'y')->first();
                        }
                        if (!$kaprodiPub125) {
                            foreach (['225', '325'] as $serumpun125) {
                                $kaprodiPub125 = TUser::where('KODE_PRODI', $serumpun125)->where('STATUS_KAPRODI', 'y')->first();
                                if ($kaprodiPub125) {
                                    break;
                                }
                            }
                        }
                        $tp->setValue('nama_kaprodi', $kaprodiPub125?->NAMA_LENGKAP ?? '-');
                        $tp->setValue('nip_kaprodi',  $kaprodiPub125?->NIP_NIM ?? '-');
                        $pub125Signatures = [
                            'signature_kaprodi' => $this->signatureBinaryOfUser($kaprodiPub125?->id),
                            'signature_penilai' => $this->signatureBinary($timSidangRecord),
                        ];
                    } else {
                        $jenis125 = strtoupper(trim((string) $request->input('jenis', $noForm ?: 'SKRIPSI')));
                        $tp->setValue('jenis_penilaian', $jenis125 !== '' ? $jenis125 : 'SKRIPSI');
                        $rows125 = $penilaianRows->values();
                        for ($i = 0; $i < 8; $i++) {
                            $r = $rows125->get($i);
                            $tp->setValue('param_' . ($i + 1), $r ? ($r->NAMA_PENILAIAN ?? $r->PENILAIAN ?? '') : '');
                            $tp->setValue('nilai_' . ($i + 1), $r && ($r->NILAI ?? '') !== '' ? (string) $r->NILAI : '');
                        }
                    }
                }
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
            . str_replace(' ', '_', $tahapan) . '_' . ($noForm ? str_replace([' ', '.'], '_', $noForm) . '_' : '') . $idJudul . '_' . $idTimSidang . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

        // Prodi 225: header form penilaian memakai judul MK saja (tanpa "PROPOSAL TESIS").
        if ($isS2 && trim((string) $kodeProdi) === '225') {
            $this->postProcessForm225Header($docxPath, (string) ($kodeMk ?? ''));
        }

        // S1 Prodi 125 Publikasi: pusatkan tanda centang di kotak cek_makalah & isi 2 tanda tangan (Kaprodi + Penilai)
        if ($isForm125Publikasi) {
            $this->centerPublikasi125CekMakalahDocx($docxPath);
            if (!empty($pub125Signatures)) {
                $this->fillBa221MkSignatures($docxPath, $pub125Signatures);
            }
        }

        // Isi baris penilaian via manipulasi XML
        if ($isSk) {
            // Template SK: isi nomor form, heading romawi, dan expand baris detail penilaian.
            // Template form penilaian SK hanya punya SATU ${signature} (milik penilai terpilih),
            // jadi jangan diisi per-peran di sini â€” biar embedSignature di bawah yang mengisinya.
            $this->postProcessSkForm($docxPath, $noForm ?? '', $skRomawi, $kodeMk, $penilaianRows->values()->toArray(), $rataNilai, $jumlahDetail);
        } elseif ($isSidangDoktor) {
            // Template form penilaian sidang doktor: isi baris detail penilaian (5 baris) + rata-rata
            $this->postProcessSidangForm($docxPath, $penilaianRows->values()->toArray(), $rataNilai);
        } elseif ($isS2Prodi222) {
            // Prodi 222: Lembar Penguji gabungan (3 kriteria teks + 7 kriteria nilai).
            $this->postProcessS2Form222($docxPath, $penilaianRows->values()->toArray(), $rataNilai);
        } elseif ($isForm221Mk) {
            // 221 Tesis II MK: 10 baris detail penilaian dengan token ber-indeks.
            $this->postProcessForm221MkRows($docxPath, $penilaianRows->values()->toArray());
        } elseif ($isS1Form) {
            // S1 Prodi 122/123/125 sudah diisi melalui token spesifik prodi di atas.
        } else {
            $this->fillPenilaianRows($docxPath, $penilaianRows->values()->toArray(), $rataNilai, $tanggal);
        }

        // Sisipkan tanda tangan penilai (base64 dari t_user.SIGNATURE)
        if (!$isForm125Publikasi) {
            $this->embedSignature($docxPath, $timSidangRecord?->id_user_penilai, $penilaiNip, $penilaiNama, $isS2 ? 90 : 110, $isS2 ? 40 : 55);
            // Bila penilai tidak punya tanda tangan tersimpan, buang sisa ${signature}
            // agar tidak tampil sebagai teks literal di dokumen.
            $this->removePlaceholderText($docxPath, 'signature');
        }

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Sisipkan gambar tanda tangan penilai ke dokumen form penilaian.
     * Sumber: t_user.SIGNATURE (longtext base64 tanpa prefix data:image).
     * Pass 1: sisipkan paragraf placeholder ${ttd_penilai} sebelum paragraf
     *         yang memuat anchor (NIP penilai, fallback nama penilai).
     * Pass 2: isi placeholder dengan gambar via TemplateProcessor::setImageValue.
     */
    private function embedSignature(string $docxPath, $idUserPenilai, string $anchorNip = '', string $anchorNama = '', int $width = 110, int $height = 55): void
    {
        $binary = $this->signatureBinaryOfUser($idUserPenilai);
        if ($binary === null && trim($anchorNip) !== '' && trim($anchorNip) !== '-') {
            $uidByNip = TUser::where('NIP_NIM', trim($anchorNip))->value('id');
            if ($uidByNip) {
                $binary = $this->signatureBinaryOfUser($uidByNip);
            }
        }
        if ($binary === null && trim($anchorNama) !== '' && trim($anchorNama) !== '-') {
            $uidByNama = TUser::whereRaw('LOWER(TRIM(NAMA_LENGKAP)) = ?', [strtolower(trim($anchorNama))])->value('id');
            if ($uidByNama) {
                $binary = $this->signatureBinaryOfUser($uidByNama);
            }
        }
        if ($binary === null) {
            return;
        }

        // Deteksi tipe gambar dari magic bytes (hanya png/jpg yang didukung)
        if (substr($binary, 0, 4) === "\x89PNG") {
            $ext = 'png';
        } elseif (substr($binary, 0, 3) === "\xFF\xD8\xFF") {
            $ext = 'jpg';
        } else {
            return;
        }

        $tmpPath = storage_path('app/uploads/ttd_' . uniqid() . '.' . $ext);
        file_put_contents($tmpPath, $binary);
        if ($ext === 'png' && function_exists('imagecreatefrompng')) {
            $tmpFixed = $this->addWhiteBackgroundToPng($tmpPath);
            if ($tmpFixed && $tmpFixed !== $tmpPath) {
                @unlink($tmpPath);
                $tmpPath = $tmpFixed;
            }
        }

        try {
            // Pass 1: Cek apakah template sudah punya ${signature}
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return;
            }
            $xml = $zip->getFromName('word/document.xml');
            $pos = false;
            
            // Jika ada ${signature}, kita tidak perlu insert manual
            $hasSignaturePlaceholder = strpos($xml, '${signature}') !== false;
            
            // Jika ${signature} ada dan posisinya SETELAH nama_penilai, pindahkan ke depan
            if ($hasSignaturePlaceholder) {
                $xml = $this->moveSignatureBeforeName($xml, $anchorNip, $anchorNama);
                // Simpan XML yang sudah di-reorder ke dalam ZIP
                $zip->deleteName('word/document.xml');
                $zip->addFromString('word/document.xml', $xml);
            }
            
            if (!$hasSignaturePlaceholder) {
                // Utamakan NIP atau cari kemunculan terakhir nama di dokumen (bagian tanda tangan bawah)
                // agar tidak salah menyisipkan tanda tangan di baris nama pembimbing di atas.
                foreach (array_filter([$anchorNip, $anchorNama]) as $anchor) {
                    if ($anchor !== '' && ($pos = strrpos($xml, $anchor)) !== false) {
                        break;
                    }
                    $pos = false;
                }
                
                if ($pos !== false && strpos($xml, '${ttd_penilai}') === false) {
                    // Cari awal paragraf sebenarnya ('<w:p>' atau '<w:p '), bukan '<w:pPr'
                    $pStart = false;
                    foreach (['<w:p>', '<w:p '] as $needle) {
                        $i = strrpos(substr($xml, 0, $pos), $needle);
                        if ($i !== false && ($pStart === false || $i > $pStart)) {
                            $pStart = $i;
                        }
                    }
                    if ($pStart !== false) {
                        $placeholder = '<w:p><w:r><w:t>${ttd_penilai}</w:t></w:r></w:p>';
                        $xml = substr($xml, 0, $pStart) . $placeholder . substr($xml, $pStart);
                        $zip->deleteName('word/document.xml');
                        $zip->addFromString('word/document.xml', $xml);
                    }
                }
            }
            $zip->close();

            // Pass 2: isi gambar
            if ($hasSignaturePlaceholder) {
                $tp2 = new TemplateProcessor($docxPath);
                $tp2->setImageValue('signature', [
                    'path'   => $tmpPath,
                    'width'  => $width,
                    'height' => $height,
                ]);
                $tp2->saveAs($docxPath);
            } elseif ($pos !== false && strpos($xml, '${ttd_penilai}') !== false) {
                $tp2 = new TemplateProcessor($docxPath);
                $tp2->setImageValue('ttd_penilai', [
                    'path'   => $tmpPath,
                    'width'  => $width,
                    'height' => $height,
                ]);
                $tp2->saveAs($docxPath);
            }
        } catch (\Exception $e) {
            \Log::warning('Gagal menyisipkan tanda tangan penilai: ' . $e->getMessage());
        } finally {
            if (isset($tmpPath) && file_exists($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }

    /**
     * Pindahkan paragraf ${signature} ke depan paragraf nama/NIP penilai
     * dan samakan indentasinya agar gambar inline sejajar dengan nama.
     */
    private function moveSignatureBeforeName(string $xml, string $anchorNip = '', string $anchorNama = ''): string
    {
        $sigPara = $this->extractParagraph($xml, '${signature}');
        if ($sigPara === false) {
            return $xml;
        }

        $nameMarkers = ['nama_penilai', 'Nama Penilai', 'Nama_Penilai'];
        foreach ([$anchorNip, $anchorNama] as $anchor) {
            $anchor = trim($anchor);
            if ($anchor !== '' && $anchor !== '-') {
                $nameMarkers[] = $anchor;
            }
        }

        $namePos = false;
        foreach ([$anchorNip, $anchorNama] as $anchor) {
            $anchor = trim($anchor);
            if ($anchor !== '' && $anchor !== '-' && ($pos = strrpos($xml, $anchor)) !== false) {
                $namePos = $pos;
                break;
            }
        }

        if ($namePos === false) {
            foreach (['nama_penilai', 'Nama Penilai', 'Nama_Penilai'] as $marker) {
                $pos = strpos($xml, $marker);
                if ($pos !== false) {
                    $namePos = $pos;
                    break;
                }
            }
        }

        if ($namePos === false) {
            return $xml;
        }

        if ($sigPara['start'] < $namePos) {
            return $this->alignSignatureWithName($xml, $namePos);
        }

        $before = substr($xml, 0, $sigPara['start']);
        $after  = substr($xml, $sigPara['start'] + strlen($sigPara['xml']));
        $xml    = $before . $after;

        $namePos2 = false;
        foreach ($nameMarkers as $marker) {
            $pos = strpos($xml, $marker);
            if ($pos !== false) {
                $namePos2 = $pos;
                break;
            }
        }

        if ($namePos2 === false) {
            return $before . $sigPara['xml'] . $after;
        }

        $paraStart = $this->findParagraphStart($xml, $namePos2);
        if ($paraStart === false) {
            return $before . $sigPara['xml'] . $after;
        }

        $xml = substr($xml, 0, $paraStart) . $sigPara['xml'] . substr($xml, $paraStart);

        return $this->alignSignatureWithName($xml, $namePos2);
    }

    private function alignSignatureWithName(string $xml, int $namePos): string
    {
        $sigPara = $this->extractParagraph($xml, '${signature}');
        $nameStart = $this->findParagraphStart($xml, $namePos);
        if ($sigPara === false || $nameStart === false) {
            return $xml;
        }

        // Jangan timpa perataan jika paragraf ${signature} sudah memiliki tab / spasi indentasi sendiri dari template
        if (strpos($sigPara['xml'], '<w:tab') !== false || preg_match('/<w:t[^>]*>\s+\$\{signature\}/', $sigPara['xml'])) {
            return $xml;
        }

        $nameEnd = strpos($xml, '</w:p>', $nameStart);
        if ($nameEnd === false) {
            return $xml;
        }
        $nameEnd += strlen('</w:p>');
        $namePara = substr($xml, $nameStart, $nameEnd - $nameStart);

        if (!preg_match('/<w:ind\b[^>]*\/>/', $namePara, $indentMatch)) {
            return $xml;
        }

        $alignedSigPara = preg_replace_callback('/<w:pPr>.*?<\/w:pPr>/s', function ($m) use ($indentMatch) {
            $ppr = $m[0];
            if (preg_match('/<w:ind\b[^>]*\/>/', $ppr)) {
                return preg_replace('/<w:ind\b[^>]*\/>/', $indentMatch[0], $ppr, 1);
            }
            if (preg_match('/<w:rPr\b/', $ppr)) {
                return preg_replace('/(<w:rPr\b)/', $indentMatch[0] . '$1', $ppr, 1);
            }
            return str_replace('</w:pPr>', $indentMatch[0] . '</w:pPr>', $ppr);
        }, $sigPara['xml'], 1);

        if ($alignedSigPara === null || $alignedSigPara === $sigPara['xml']) {
            return $xml;
        }

        return substr($xml, 0, $sigPara['start']) . $alignedSigPara
            . substr($xml, $sigPara['start'] + strlen($sigPara['xml']));
    }

    /**
     * Ekstrak paragraf (<w:p>...</w:p>) yang memuat teks tertentu.
     * Mengembalikan array ['start' => int, 'xml' => string] atau false.
     */
    private function extractParagraph(string $xml, string $searchText): array|false
    {
        $textPos = strpos($xml, $searchText);
        if ($textPos === false) {
            return false;
        }

        // Cari awal <w:p> sebelum posisi teks
        $pStart = $this->findParagraphStart($xml, $textPos);
        if ($pStart === false) {
            return false;
        }

        // Cari akhir </w:p> setelah posisi teks
        $pEnd = strpos($xml, '</w:p>', $textPos);
        if ($pEnd === false) {
            return false;
        }
        $pEnd += strlen('</w:p>');

        return [
            'start' => $pStart,
            'xml'   => substr($xml, $pStart, $pEnd - $pStart),
        ];
    }

    /**
     * Cari awal paragraf (<w:p> atau <w:p ) sebelum posisi tertentu.
     */
    private function findParagraphStart(string $xml, int $beforePos): int|false
    {
        $best = false;
        foreach (['<w:p>', '<w:p '] as $needle) {
            $i = strrpos(substr($xml, 0, $beforePos), $needle);
            if ($i !== false && ($best === false || $i > $best)) {
                $best = $i;
            }
        }
        return $best;
    }

    /**
     * Isi placeholder ${signature} (atau placeholder lain) dengan gambar tanda tangan
     * dari t_user.SIGNATURE. Mendukung placeholder yang terpecah antar XML run.
     */
    private function fillSignaturePlaceholder(string $docxPath, $idUser, string $placeholder = 'signature', string $gantiKosong = '.............'): void
    {
        try {
            $signature = $idUser ? TUser::where('id', (int) $idUser)->value('SIGNATURE') : null;
            if (empty($signature)) {
                $this->removePlaceholderText($docxPath, $placeholder, $gantiKosong);
                return;
            }

            $binary = base64_decode($signature, true);
            if ($binary === false || strlen($binary) < 100) {
                $this->removePlaceholderText($docxPath, $placeholder, $gantiKosong);
                return;
            }

            $validImage = (substr($binary, 0, 4) === "\x89PNG" || substr($binary, 0, 3) === "\xFF\xD8\xFF");
            if (!$validImage) {
                $this->removePlaceholderText($docxPath, $placeholder, $gantiKosong);
                return;
            }

            $ext = substr($binary, 0, 4) === "\x89PNG" ? 'png' : 'jpg';
            $tmpPath = storage_path('app/uploads/ttd_sig_' . uniqid() . '.' . $ext);
            file_put_contents($tmpPath, $binary);

            // Fix: Tambahkan background putih untuk PNG transparan agar tidak hitam di Word
            if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                $tmpFixed = $this->addWhiteBackgroundToPng($tmpPath);
                if ($tmpFixed && $tmpFixed !== $tmpPath) {
                    @unlink($tmpPath);
                    $tmpPath = $tmpFixed;
                }
            }

            try {
                $tp = new \PhpOffice\PhpWord\TemplateProcessor($docxPath);
                $tp->setImageValue($placeholder, [
                    'path'   => $tmpPath,
                    'width'  => 110,
                    'height' => 55,
                ]);
                $tp->saveAs($docxPath);
            } finally {
                @unlink($tmpPath);
            }
        } catch (\Exception $e) {
            \Log::warning('Gagal isi placeholder signature: ' . $e->getMessage());
        }
    }

    /**
     * Form Penilaian Tesis 2 MK prodi 221: isi 10 baris detail penilaian
     * yang memakai token ber-indeks ${nama_penilaian_N}, ${keterangan_N}, ${nilai_N}.
     */
    private function postProcessForm221MkRows(string $docxPath, array $rows): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

        foreach ($rows as $i => $row) {
            $no = $i + 1;
            if ($no > 10) {
                break;
            }
            $arr   = is_object($row) ? (array) $row : $row;
            $nama  = $arr['NAMA_PENILAIAN'] ?? $arr['nama_penilaian'] ?? $arr['PENILAIAN'] ?? $arr['PENILAIAN_PARAMETER'] ?? '';
            $ket   = $arr['KETERANGAN'] ?? $arr['keterangan'] ?? '';
            $nilai = $arr['NILAI'] ?? $arr['nilai'] ?? '';

            $xml = preg_replace('/\$\{nama_penilaian_' . $no . '\}/', $esc($nama), $xml, 1);
            $xml = preg_replace('/\$\{keterangan_' . $no . '\}/', $esc($ket), $xml, 1);
            $xml = preg_replace('/\$\{nilai_' . $no . '\}/', $esc($nilai), $xml, 1);
        }

        // Bersihkan sisa token ber-indeks yang tidak terisi.
        $xml = preg_replace('/\$\{(?:nama_penilaian|keterangan|nilai)_\d+\}/', '', $xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * BA Penilaian Tesis 2 MK prodi 221: isi slot tanda tangan bernama
     * ${signature_1}..${signature_4}, ${signature_ketua_sidang}, ${signature_kaprodi}.
     * Tanpa tanda tangan tersimpan, slot diisi titik-titik.
     */
    private function fillBa221MkSignatures(string $docxPath, array $signatures, array $emptyFallbacks = []): void
    {
        try {
            $tp = new TemplateProcessor($docxPath);
            $tmpFiles = [];
            foreach ($signatures as $name => $binary) {
                if (empty($binary)) {
                    $fallbackText = array_key_exists($name, $emptyFallbacks) ? $emptyFallbacks[$name] : '.............';
                    $tp->setValue($name, $fallbackText);
                    continue;
                }
                $ext = substr($binary, 0, 4) === "\x89PNG" ? 'png' : 'jpg';
                $tmp = storage_path('app/uploads/ttd_ba221mk_' . uniqid() . '.' . $ext);
                file_put_contents($tmp, $binary);
                if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                    $tmpFixed = $this->addWhiteBackgroundToPng($tmp);
                    if ($tmpFixed && $tmpFixed !== $tmp) {
                        @unlink($tmp);
                        $tmp = $tmpFixed;
                    }
                }
                $tmpFiles[] = $tmp;
                $tp->setImageValue($name, ['path' => $tmp, 'width' => 110, 'height' => 50]);
            }
            $tp->saveAs($docxPath);
        } catch (\Exception $e) {
            \Log::warning('Gagal isi tanda tangan BA MK 221: ' . $e->getMessage());
        } finally {
            foreach ($tmpFiles ?? [] as $f) {
                @unlink($f);
            }
        }
    }

    /**
     * Hapus teks placeholder ${xxx} yang dibiarkan literal (mis. saat tidak ada tanda tangan),
     * agar tidak tampak sebagai `${signature}` mentah di dokumen hasil cetak.
     * $ganti: teks pengganti (default kosong); dipakai untuk menampilkan titik-titik.
     */
    private function removePlaceholderText(string $docxPath, string $placeholder, string $ganti = ''): void
    {
        try {
            $tp = new \PhpOffice\PhpWord\TemplateProcessor($docxPath);
            $tp->setValue($placeholder, $ganti);
            $tp->saveAs($docxPath);
        } catch (\Exception $e) {
            \Log::warning('Gagal hapus placeholder ' . $placeholder . ': ' . $e->getMessage());
        }
    }

    /**
     * BA prodi 222: hasilkan baris tabel "Nama Penguji / Peran / Tanda Tangan"
     * secara dinamis dari $penilaiList (urutan: pembimbing lalu penguji; ketua sidang
     * tidak diikutsertakan). Setiap baris memakai placeholder ttd unik
     * ${ba222_ttd_1}..${ba222_ttd_N} untuk disisipkan gambar tanda tangan per orang.
     *
     * Cara kerja: cari 3 baris prototype di tabel (baris dengan ${nama_pembimbing_i},
     * ${nama_pembimbing_ii}, ${nama_ketua_penguji}), ganti dengan N baris hasil generate.
     */
    private function buildBa222DynamicTimTable(string $docxPath, array $penilaiList): int
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return 0;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return 0;
        }

        // Susun entri penilai (abaikan ketua sidang)
        $entries = [];
        foreach ($penilaiList as $t) {
            $sts = strtolower(trim((string) ($t->status_tim_sidang ?? '')));
            if (str_contains($sts, 'ketua sidang')) {
                continue;
            }
            $nama = trim((string) ($t->NAMA ?? ''));
            $nip  = trim((string) ($t->NIP ?? ''));
            if ($nama === '') {
                continue;
            }

            if (str_contains($sts, 'pembimbing')) {
                $no = count(array_filter($entries, fn($e) => str_starts_with($e['peran'], 'Pembimbing'))) + 1;
                $peran = 'Pembimbing ' . $no;
            } elseif (str_contains($sts, 'penguji')) {
                $no = count(array_filter($entries, fn($e) => str_starts_with($e['peran'], 'Penguji'))) + 1;
                $peran = 'Penguji ' . $no;
            } else {
                $peran = 'Penilai';
            }

            $entries[] = [
                'nama'  => $nama,
                'nip'   => $nip !== '' ? $nip : '-',
                'peran' => $peran,
                'tim'   => $t,
                'ttd'   => 'ba222_ttd_' . (count($entries) + 1),
            ];
        }
        if (empty($entries)) {
            $zip->close();
            return 0;
        }

        // Cari 3 baris prototype di XML. Dikenali dari kombinasi lebar kolom khas
        // tabel (4800/1935/2880) + keberadaan ${signature} di baris tersebut
        // (header row tidak punya ${signature}; setValue tidak menyentuh token ini).
        preg_match_all('/<w:tr\b[^>]*>.*?<\/w:tr>/us', $xml, $mm);
        $rows = $mm[0] ?? [];
        $dataRows = [];
        foreach ($rows as $r) {
            if (str_contains($r, 'w:w="4800"') && str_contains($r, 'w:w="1935"')
                && str_contains($r, 'w:w="2880"') && str_contains($r, '${signature}')) {
                $dataRows[] = $r;
            }
        }

        $generated = '';
        $idx = 1;
        foreach ($entries as $e) {
            $generated .= $this->buildBa222TableRow($e['nama'], $e['nip'], $e['peran'], '${' . $e['ttd'] . '}', $idx);
            $idx++;
        }

        // Ganti baris prototype dengan baris hasil generate
        if (!empty($dataRows)) {
            $firstProto = $dataRows[0];
            $lastProto  = $dataRows[count($dataRows) - 1];
            $start = strpos($xml, $firstProto);
            $end   = strpos($xml, $lastProto) + strlen($lastProto);
            if ($start !== false && $end >= $start) {
                $xml = substr($xml, 0, $start) . $generated . substr($xml, $end);
            }
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        // Isi placeholder ttd per baris dengan gambar tanda tangan (atau titik-titik)
        $tp = new TemplateProcessor($docxPath);
        $tmpFiles = [];
        foreach ($entries as $e) {
            $binary = $this->signatureBinary($e['tim']);
            if (empty($binary)) {
                $tp->setValue($e['ttd'], '.............');
                continue;
            }
            $ext = substr($binary, 0, 4) === "\x89PNG" ? 'png' : 'jpg';
            $tmp = storage_path('app/uploads/ttd_ba222_' . uniqid() . '.' . $ext);
            file_put_contents($tmp, $binary);
            if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                $fixed = $this->addWhiteBackgroundToPng($tmp);
                if ($fixed && $fixed !== $tmp) { @unlink($tmp); $tmp = $fixed; }
            }
            $tmpFiles[] = $tmp;
            $tp->setImageValue($e['ttd'], ['path' => $tmp, 'width' => 110, 'height' => 55]);
        }
        $tp->saveAs($docxPath);
        foreach ($tmpFiles as $f) { @unlink($f); }

        return count($entries);
    }

    /**
     * Bangun satu baris <w:tr> untuk tabel Tim Penilai BA 222.
     */
    private function buildBa222TableRow(string $nama, string $nip, string $peran, string $ttdPlaceholder, int $no): string
    {
        $nama  = htmlspecialchars($nama, ENT_XML1, 'UTF-8');
        $nip   = htmlspecialchars($nip, ENT_XML1, 'UTF-8');
        $peran = htmlspecialchars($peran, ENT_XML1, 'UTF-8');

        return '<w:tr><w:trPr><w:trHeight w:val="900"/></w:trPr>'
            . '<w:tc><w:tcPr><w:tcW w:w="4800" w:type="dxa"/></w:tcPr>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:spacing w:before="154"/><w:rPr><w:b/></w:rPr></w:pPr>'
            . '<w:r><w:rPr><w:b/><w:color w:val="202429"/></w:rPr><w:t>' . $nama . '</w:t></w:r></w:p>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:spacing w:before="105"/><w:rPr><w:sz w:val="19"/></w:rPr></w:pPr>'
            . '<w:r><w:rPr><w:color w:val="333333"/><w:sz w:val="19"/></w:rPr><w:t>NIP: ' . $nip . '</w:t></w:r></w:p>'
            . '</w:tc>'
            . '<w:tc><w:tcPr><w:tcW w:w="1935" w:type="dxa"/></w:tcPr>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:spacing w:before="66"/></w:pPr></w:p>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:jc w:val="center"/></w:pPr>'
            . '<w:r><w:rPr><w:color w:val="202429"/></w:rPr><w:t>' . $peran . '</w:t></w:r></w:p>'
            . '</w:tc>'
            . '<w:tc><w:tcPr><w:tcW w:w="2880" w:type="dxa"/></w:tcPr>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:spacing w:before="93"/></w:pPr></w:p>'
            . '<w:p><w:pPr><w:pStyle w:val="TableParagraph"/><w:rPr><w:sz w:val="20"/></w:rPr></w:pPr>'
            . '<w:r><w:rPr><w:color w:val="202429"/><w:sz w:val="20"/></w:rPr><w:t>' . $no . '. </w:t></w:r>'
            . '<w:r><w:rPr><w:color w:val="202429"/><w:sz w:val="20"/></w:rPr><w:t>' . $ttdPlaceholder . '</w:t></w:r></w:p>'
            . '</w:tc></w:tr>';
    }

    /**
     * Sama seperti embedAllSkSignatures(), tetapi jumlah slot disesuaikan dengan
     * jumlah placeholder ${signature} yang benar-benar ada di dokumen.
     * Menghindari error saat template punya slot lebih sedikit dari daftar ttd.
     */
    private function embedAllSkSignaturesAvailable(string $docxPath, array $signatures): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            return;
        }
        $count = preg_match_all('/\$\{signature\}/', $xml);
        if ($count > 0) {
            $signatures = array_slice($signatures, 0, $count);
        }
        if (!empty($signatures)) {
            $this->embedAllSkSignatures($docxPath, $signatures);
        }
    }

    /**
     * Sisipkan gambar tanda tangan untuk SETIAP baris evaluator di tabel BA SK.
     * $roleSignatures: array berurutan (indeks 0..N mengikuti urutan baris tabel)
     * yang berisi binary gambar ttd (string) atau null bila peran tidak ada / tanpa ttd.
     * N placeholder ${signature} PERTAMA di document.xml (baris-baris tabel) di-rename
     * menjadi ${signature_1}..${signature_N} lalu diisi per-baris dengan ttd peran masing-masing.
     * Placeholder ${signature} sisanya (footer kaprodi / ketua sidang) TIDAK disentuh,
     * sehingga bisa diisi terpisah dengan ttd yang benar.
     */
    private function embedAllSkSignatures(string $docxPath, array $roleSignatures): void
    {
        $roleSignatures = array_values($roleSignatures);
        $n = count($roleSignatures);
        if ($n === 0) {
            return;
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return;
            }
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                $zip->close();
                return;
            }

            // Rename N ${signature} pertama (baris tabel) -> signature_1..signature_N
            $counter = 0;
            $xml = preg_replace_callback('/\$\{signature\}/', function ($matches) use (&$counter, $n) {
                $counter++;
                if ($counter <= $n) {
                    return '${signature_' . $counter . '}';
                }
                return '${signature}'; // footer tetap
            }, $xml);

            if ($xml === null) {
                $zip->close();
                return;
            }

            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();

            // Isi setiap signature_i dengan binary perorangan (null -> kosongkan)
            $tp = new \PhpOffice\PhpWord\TemplateProcessor($docxPath);
            $tmpFiles = [];
            for ($i = 1; $i <= $n; $i++) {
                $binary = $roleSignatures[$i - 1] ?? null;
                if (empty($binary)) {
                    $tp->setValue('signature_' . $i, '.............');
                    continue;
                }

                $ext = substr($binary, 0, 4) === "\x89PNG" ? 'png' : 'jpg';
                $tmpPath = storage_path('app/uploads/ttd_embed_' . uniqid() . '.' . $ext);
                file_put_contents($tmpPath, $binary);

                // Fix: Tambahkan background putih untuk PNG transparan agar tidak hitam di Word
                if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                    $tmpFixed = $this->addWhiteBackgroundToPng($tmpPath);
                    if ($tmpFixed && $tmpFixed !== $tmpPath) {
                        @unlink($tmpPath);
                        $tmpPath = $tmpFixed;
                    }
                }

                $tmpFiles[] = $tmpPath;

                $tp->setImageValue('signature_' . $i, [
                    'path'   => $tmpPath,
                    'width'  => 110,
                    'height' => 55,
                ]);
            }

            $tp->saveAs($docxPath);
        } catch (\Exception $e) {
            \Log::warning('Gagal sisipkan semua signature SK: ' . $e->getMessage());
        } finally {
            if (isset($tmpFiles)) {
                foreach ($tmpFiles as $f) {
                    if (file_exists($f)) {
                        @unlink($f);
                    }
                }
            }
        }
    }

    /**
     * Ambil binary gambar tanda tangan dari t_user.SIGNATURE untuk satu tim.
     * Mengembalikan string binary atau null bila tidak valid / tidak ada.
     */
    private function signatureBinary($tim): ?string
    {
        if (!$tim) {
            return null;
        }
        $idUser = $tim->id_user_penilai ?? $tim->ID_USER_PENILAI ?? null;
        $bin = $this->signatureBinaryOfUser($idUser);
        if ($bin !== null) {
            return $bin;
        }

        $nip = trim((string) ($tim->NIP ?? $tim->nip ?? ''));
        if ($nip !== '') {
            $uidByNip = TUser::where('NIP_NIM', $nip)->value('id');
            if ($uidByNip) {
                $bin = $this->signatureBinaryOfUser($uidByNip);
                if ($bin !== null) {
                    return $bin;
                }
            }
        }

        $nama = trim((string) ($tim->NAMA ?? $tim->nama ?? ''));
        if ($nama !== '') {
            $uidByNama = TUser::whereRaw('LOWER(TRIM(NAMA_LENGKAP)) = ?', [strtolower($nama)])->value('id');
            if ($uidByNama) {
                return $this->signatureBinaryOfUser($uidByNama);
            }
        }

        return null;
    }

    /**
     * Sisipkan tanda tangan (gambar dari t_user.SIGNATURE) ke template BA S2.
     * Placeholder gambar ${s2ba_ttd_N} disisipkan sebelum tiap paragraf yang
     * isinya hanya titik-titik di kolom "Tanda Tangan" (urutan kemunculan =
     * Tim Penilai). Slot kaprodi memakai ${signature} dari template; template
     * lama dengan ${s2ba_ttd_kaprodi} tetap didukung. Jika belum ada slot,
     * ${signature} disisipkan setelah paragraf "Ketua Program Studi".
     * Tanpa ttd tersimpan -> placeholder dikosongkan.
     */
    private function embedS2BaSignaturesByDots(string $docxPath, array $penilaiIds, $kaprodiId): void
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return;
            }
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                $zip->close();
                return;
            }

            // 1) ${s2ba_ttd_N} sebelum tiap paragraf murni titik-titik (urutan kemunculan)
            $n = count($penilaiIds);
            $idx = 0;
            $xml = preg_replace_callback('/<w:p\b[^>]*>.*?<\/w:p>/us', function ($m) use (&$idx, $n) {
                if ($idx >= $n) {
                    return $m[0];
                }
                $text = trim(strip_tags($m[0]));
                if ($text === '' || !preg_match('/^[.\x{2026}\x{00B7}\s]+$/u', $text)) {
                    return $m[0];
                }
                $idx++;
                return '<w:p><w:r><w:t>${s2ba_ttd_' . $idx . '}</w:t></w:r></w:p>' . $m[0];
            }, $xml);

            // 2) Slot kaprodi mengikuti posisi ${signature} pada template.
            //    Template lama memakai ${s2ba_ttd_kaprodi}; jika keduanya tidak ada,
            //    sisipkan ${signature} setelah paragraf "Ketua Program Studi".
            $kaprodiPlaceholder = 'signature';
            if (strpos($xml, '${signature}') === false) {
                if (strpos($xml, '${s2ba_ttd_kaprodi}') !== false) {
                    $kaprodiPlaceholder = 's2ba_ttd_kaprodi';
                } else {
                    $pos = strrpos($xml, 'Program Studi');
                    if ($pos !== false) {
                        $pEnd = strpos($xml, '</w:p>', $pos);
                        if ($pEnd !== false) {
                            $ph = '<w:p><w:r><w:t>${signature}</w:t></w:r></w:p>';
                            $insertAt = $pEnd + strlen('</w:p>');
                            $xml = substr($xml, 0, $insertAt) . $ph . substr($xml, $insertAt);
                        }
                    }
                }
            }

            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();

            // 3) Isi tiap placeholder dengan gambar tanda tangan orang yang bersangkutan
            foreach ($penilaiIds as $i => $uid) {
                $this->fillSignaturePlaceholder($docxPath, $uid, 's2ba_ttd_' . ($i + 1), '');
            }
            $this->fillSignaturePlaceholder($docxPath, $kaprodiId, $kaprodiPlaceholder, '');
        } catch (\Exception $e) {
            \Log::warning('Gagal sisipkan tanda tangan BA S2: ' . $e->getMessage());
        }
    }

    private function signatureBinaryOfUser($idUser): ?string
    {
        if (!$idUser) {
            return null;
        }
        $sig = TUser::where('id', (int) $idUser)->value('SIGNATURE');
        if (empty($sig)) {
            return null;
        }
        if (str_contains($sig, ',')) {
            $sig = substr($sig, strpos($sig, ',') + 1);
        }
        $binary = base64_decode(trim($sig), true);
        if ($binary === false || strlen($binary) < 100) {
            return null;
        }
        if (!(substr($binary, 0, 4) === "\x89PNG" || substr($binary, 0, 3) === "\xFF\xD8\xFF")) {
            return null;
        }
        // Abaikan kanvas putih/transparan kosong (belum ada goresan tanda tangan)
        if (function_exists('imagecreatefromstring')) {
            $im = @imagecreatefromstring($binary);
            if ($im !== false) {
                $w = imagesx($im);
                $h = imagesy($im);
                $hasInk = false;
                for ($x = 0; $x < $w && !$hasInk; $x += 2) {
                    for ($y = 0; $y < $h; $y += 2) {
                        $rgba = imagecolorat($im, $x, $y);
                        $alpha = ($rgba >> 24) & 0x7F;
                        $r = ($rgba >> 16) & 0xFF;
                        $g = ($rgba >> 8) & 0xFF;
                        $b = $rgba & 0xFF;
                        if ($alpha < 120 && ($r < 245 || $g < 245 || $b < 245)) {
                            $hasInk = true;
                            break;
                        }
                    }
                }
                imagedestroy($im);
                if (!$hasInk) {
                    return null;
                }
            }
        }
        return $binary;
    }

    /**
     * Isi dua signature footer BA SK IV: Kaprodi (kiri) & Ketua Sidang (kanan).
     * Setelah embedAllSkSignatures() merename signature baris-baris tabel,
     * ${signature} yang tersisa di footer di-rename menjadi
     * ${signature_kaprodi} & ${signature_ketua_sidang}, lalu diisi per orang.
     */
    private function fillSk4FooterSignatures(string $docxPath, $kaprodiId, $ketuaSidangId): void
    {
        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) !== true) {
                return;
            }
            $xml = $zip->getFromName('word/document.xml');
            if ($xml === false) {
                $zip->close();
                return;
            }

            $counter = 0;
            $xml = preg_replace_callback('/\$\{signature\}/', function ($m) use (&$counter) {
                $counter++;
                if ($counter === 1) {
                    return '${signature_kaprodi}';
                }
                if ($counter === 2) {
                    return '${signature_ketua_sidang}';
                }
                // Jangan isi slot sisa dengan TTD kaprodi/ketua sidang
                return '';
            }, $xml);

            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $xml);
            $zip->close();

            $mapping = ['signature_kaprodi' => $kaprodiId, 'signature_ketua_sidang' => $ketuaSidangId];
            $tp = new \PhpOffice\PhpWord\TemplateProcessor($docxPath);
            $tmpFiles = [];
            foreach ($mapping as $placeholder => $idUser) {
                $binary = $this->signatureBinaryOfUser($idUser);
                if (empty($binary)) {
                    $tp->setValue($placeholder, '.............');
                    continue;
                }
                $ext = substr($binary, 0, 4) === "\x89PNG" ? 'png' : 'jpg';
                $tmpPath = storage_path('app/uploads/ttd_embed_' . uniqid() . '.' . $ext);
                file_put_contents($tmpPath, $binary);
                if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                    $tmpFixed = $this->addWhiteBackgroundToPng($tmpPath);
                    if ($tmpFixed && $tmpFixed !== $tmpPath) {
                        @unlink($tmpPath);
                        $tmpPath = $tmpFixed;
                    }
                }
                $tmpFiles[] = $tmpPath;
                $tp->setImageValue($placeholder, [
                    'path'   => $tmpPath,
                    'width'  => 110,
                    'height' => 55,
                ]);
            }
            $tp->saveAs($docxPath);
        } catch (\Exception $e) {
            \Log::warning('Gagal isi footer signature SK IV: ' . $e->getMessage());
        } finally {
            if (isset($tmpFiles)) {
                foreach ($tmpFiles as $f) {
                    if (file_exists($f)) {
                        @unlink($f);
                    }
                }
            }
        }
    }

    /**
     * Tambahkan background putih ke PNG transparan agar tidak muncul hitam di Word.
     * Mengembalikan path file baru atau false jika gagal.
     */
    private function addWhiteBackgroundToPng(string $pngPath): string|false
    {
        if (!function_exists('imagecreatefrompng')) {
            return false;
        }
        $src = @imagecreatefrompng($pngPath);
        if (!$src) {
            return false;
        }
        $w = imagesx($src);
        $h = imagesy($src);
        $dst = imagecreatetruecolor($w, $h);
        if (!$dst) {
            imagedestroy($src);
            return false;
        }
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $w, $h, $white);
        imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
        imagedestroy($src);
        $out = $pngPath . '.wb.png';
        if (!imagepng($dst, $out)) {
            imagedestroy($dst);
            return false;
        }
        imagedestroy($dst);
        return $out;
    }

    /**
     * Isi template Penilaian Sidang Tesis I & II prodi 222 (Lembar Penguji).
     *
     * Template memuat DUA bagian:
     *  - 3 blok kriteria teks (saran perbaikan): ${nama_penilaian_text} + ${catatan}
     *  - tabel 7 baris kriteria nilai: ${nama_penilaian} + ${indeks_penilaian} + ${nilai}
     * plus ${indeks_nilai}, ${nilai_rata2}, dan ${total_nilai}.
     *
     * ${indeks_penilaian} diisi Nilai Huruf per kriteria dari Nilai Angka kriteria
     * bersangkutan; ${indeks_nilai} diisi indeks dari nilai rata-rata.
     *
     * Baris penilaian dipisah: STATUS_CATATAN='y' -> bagian teks, sisanya -> tabel nilai.
     */
    private function postProcessS2Form222(string $docxPath, array $rows, string $rataNilai): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

        // Pisahkan baris teks (saran perbaikan) dan baris nilai.
        $textRows  = [];
        $nilaiRows = [];
        foreach ($rows as $row) {
            $arr    = is_object($row) ? (array) $row : $row;
            $isText = strtolower((string) ($arr['STATUS_CATATAN'] ?? $arr['status_catatan'] ?? '')) === 'y';
            if ($isText) {
                $textRows[] = $arr;
            } else {
                $nilaiRows[] = $arr;
            }
        }

        // Bagian teks: tiap blok -> ${nama_penilaian_text} & ${catatan} (urut).
        foreach ($textRows as $arr) {
            $nama = $arr['NAMA_PENILAIAN'] ?? $arr['nama_penilaian'] ?? $arr['PENILAIAN'] ?? '';
            $cat  = $arr['CATATAN'] ?? $arr['catatan'] ?? '';
            $xml  = preg_replace('/\$\{nama_penilaian_text\}/', $esc($nama), $xml, 1);
            $xml  = preg_replace('/\$\{catatan\}/', $esc($cat), $xml, 1);
        }

        // Tabel nilai: tiap baris -> ${nama_penilaian}, ${indeks_penilaian} & ${nilai} (urut).
        // Nilai Huruf per kriteria dihitung dari Nilai Angka kriteria itu sendiri
        // (mis. NA 4,50 -> indeks A) sesuai bobot indeks prodi 222.
        $total       = 0.0;
        $jumlahNilai = 0;
        foreach ($nilaiRows as $arr) {
            $nama  = $arr['NAMA_PENILAIAN'] ?? $arr['nama_penilaian'] ?? $arr['PENILAIAN'] ?? '';
            $nilai = $arr['NILAI'] ?? $arr['nilai'] ?? '';
            $xml   = preg_replace('/\$\{nama_penilaian\}/', $esc($nama), $xml, 1);
            $xml   = preg_replace('/\$\{indeks_penilaian\}/', $esc($this->calculateIndeksNilai222($nilai)), $xml, 1);
            $xml   = preg_replace('/\$\{nilai\}/', $esc($nilai), $xml, 1);
            if ($nilai !== '' && $nilai !== null && is_numeric($nilai)) {
                $total += (float) $nilai;
                $jumlahNilai++;
            }
        }

        // Ringkasan: rata-rata, indeks huruf, total skor.
        $rata     = (string) $rataNilai;
        $indeks   = $this->calculateIndeksNilai222($rata);
        $totalStr = $jumlahNilai > 0 ? number_format($total, 2) : '';

        $xml = preg_replace('/\$\{nilai_rata2\}/', $esc($rata), $xml, 1);
        $xml = preg_replace('/\$\{indeks_nilai\}/', $esc($indeks), $xml, 1);
        $xml = preg_replace('/\$\{total_nilai\}/', $esc($totalStr), $xml, 1);

        // Bersihkan sisa placeholder yang belum terisi (kecuali ${signature}).
        $xml = preg_replace('/\$\{nama_penilaian_text\}/', '', $xml);
        $xml = preg_replace('/\$\{nama_penilaian\}/', '', $xml);
        $xml = preg_replace('/\$\{indeks_penilaian\}/', '', $xml);
        $xml = preg_replace('/\$\{nilai\}/', '', $xml);
        $xml = preg_replace('/\$\{catatan\}/', '', $xml);
        $xml = preg_replace('/\$\{indeks_nilai\}/', '', $xml);
        $xml = preg_replace('/\$\{nilai_rata2\}/', '', $xml);
        $xml = preg_replace('/\$\{total_nilai\}/', '', $xml);
        $xml = preg_replace('/\$\{(?!signature)[^}]*\}/i', '', $xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Isi placeholder per-baris penilaian di DOCX.
     * Template memiliki ${nama_penilaian}, ${keterangan}, ${nilai}, ${catatan}
     * berulang (satu per baris) â€” ganti SATU occurrence per iterasi row.
     */
    private function fillPenilaianRows(string $docxPath, array $rows, string $rataNilai, string $tanggal = ''): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // Rata-rata nilai
        $xml = preg_replace('/\$\{rata_nilai[^}]*\}/i', htmlspecialchars($rataNilai, ENT_XML1, 'UTF-8'), $xml);
        $xml = preg_replace('/\$\{nilai_rata2[^}]*\}/i', htmlspecialchars($rataNilai, ENT_XML1, 'UTF-8'), $xml);

        // Tanggal penilaian (template proposal tidak memiliki placeholder ${tanggal})
        if ($tanggal !== '') {
            $xml = $this->fuzzyReplaceOnce(
                $xml,
                'Tanggal penilaian:',
                'Tanggal penilaian: ' . htmlspecialchars($tanggal, ENT_XML1, 'UTF-8')
            );
        }

        // Map field name â†’ kemungkinan key di array/object
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
        foreach ($rows as $rowIndex => $row) {
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
                if ($ph === 'nilai') {
                    $variants[] = 'nilai_' . ($rowIndex + 1);
                }
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
        $xml = preg_replace('/\$\{(?!signature\})[^}]*\}/i', '', $xml);  // sisa ${xxx} lain (kecuali ${signature})

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Post-process template SK: isi nomor form, ganti heading romawi (I/II/III),
     * dan isi/expand baris detail penilaian sesuai jumlah record di database.
     */
    private function postProcessSkForm(string $docxPath, string $noForm, string $skRomawi, string $kodeMk, array $rows, string $rataNilai, int $jumlahDetail = 5): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // Skala dinamis: /skala N (jumlah skor detail dibagi N) sesuai jumlah detail penilaian
        if ($jumlahDetail > 0) {
            $xml = preg_replace(
                '~(/skala\s*)\d+(\s*\(\s*jumlah skor detail dibagi\s*)\d+(\s*\))~i',
                '${1}' . $jumlahDetail . '${2}' . $jumlahDetail . '${3}',
                $xml
            );
            // Header "skor maksimum adalah 5)" -> angka maksimum dinamis sesuai jumlah detail
            $xml = preg_replace(
                '~(<w:t>)5\)(?=</w:t>)~i',
                '${1}' . $jumlahDetail . ')',
                $xml
            );
        }

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

        // Baris "(kode mk) SEMINAR KEMAJUAN I" -> "<kode mk> SEMINAR KEMAJUAN I/II/III"
        // Kode MK diambil dari Data Master Mata Kuliah Prodi (t_mk_prodi) sesuai prodi + tahapan.
        // Dilakukan SEBELUM mengganti sisa "(kode mk)" agar baris ini masih punya pola unik.
        $xml = $this->fuzzyReplaceOnce(
            $xml,
            '(kode mk) SEMINAR KEMAJUAN I',
            $kodeMk . ' SEMINAR KEMAJUAN ' . ($skRomawi !== '' ? $skRomawi : 'I')
        );

        // Sisa literal "(kode mk)" (jika barisnya tidak persis seperti pola di atas)
        if ($kodeMk !== '') {
            $xml = $this->fuzzyReplaceOnce($xml, '(kode mk)', $kodeMk);
        }

        // Footer "(Penilai Seminar Kemajuan I)" -> II / III sesuai tahapan
        if ($skRomawi !== '' && $skRomawi !== 'I') {
            $xml = $this->fuzzyReplaceOnce(
                $xml,
                'Penilai Seminar Kemajuan I',
                'Penilai Seminar Kemajuan ' . $skRomawi
            );
        }

        // Expand + isi baris detail penilaian
        $xml = $this->fillSkDetailRows($xml, $rows);

        // Nilai Rata-Rata (jumlah total skor dibagi N skala dinamis)
        $xml = preg_replace('/\$\{rata_nilai[^}]*\}/i', htmlspecialchars($rataNilai, ENT_XML1, 'UTF-8'), $xml);

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
     *
     * Hapus label "Resume dari semua penguji" dari template, lalu isi ${catatan}
     * dengan catatan penilai yang dipilih saja.
     */
    private function postProcessSidangForm(string $docxPath, array $rows, string $rataNilai): void
    {
        // 1. Kumpulkan catatan non-kosong dari baris penilaian penilai terpilih
        $allCatatan = [];
        foreach ($rows as $row) {
            $rowArr = is_object($row) ? (array) $row : $row;
            $catatan = '';
            foreach (['CATATAN', 'catatan'] as $key) {
                if (isset($rowArr[$key]) && $rowArr[$key] !== null && trim((string) $rowArr[$key]) !== '') {
                    $catatan = trim((string) $rowArr[$key]);
                    break;
                }
            }
            if ($catatan !== '') {
                // Buang prefix label jika ikut tersimpan di DB
                $catatan = preg_replace('/^\s*Resume\s+dari\s+semua\s+penguji\s*/iu', '', $catatan);
                $catatan = trim($catatan);
                if ($catatan !== '') {
                    $allCatatan[] = $catatan;
                }
            }
        }

        $combinedCatatan = implode("\n", $allCatatan);
        $combinedCatatan = preg_replace('/Resume\s+dari\s+semua\s+penguji\s*/iu', '', $combinedCatatan ?? '') ?? '';
        $combinedCatatan = trim($combinedCatatan);

        // 2. Hapus "Resume dari semua penguji" + ganti ${catatan}
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml !== false) {
                $escaped = htmlspecialchars($combinedCatatan, ENT_XML1, 'UTF-8');
                // Hapus frasa (kontigu maupun terpecah antar run Word)
                $xml = str_replace('Resume dari semua penguji ${catatan}', '${catatan}', $xml);
                $xml = str_replace('Resume dari semua penguji ', '', $xml);
                $xml = str_replace('Resume dari semua penguji', '', $xml);
                $xml = $this->fuzzyReplaceAll($xml, 'Resume dari semua penguji', '');
                $xml = preg_replace('/Resume\s+dari\s+semua\s+penguji\s*/iu', '', $xml);
                $xml = preg_replace('/\$\{catatan\}/i', $escaped, $xml);

                $zip->deleteName('word/document.xml');
                $zip->addFromString('word/document.xml', $xml);
            }
            $zip->close();
        }

        // 3. Isi sisa placeholder (nama_penilaian, keterangan, nilai, rata_nilai)
        $this->fillPenilaianRows($docxPath, $rows, $rataNilai);
    }

    /**
     * Ganti SEMUA occurrence text yang mungkin terpecah antar <w:r> di dalam XML Word.
     */
    private function fuzzyReplaceAll(string $xml, string $search, string $replace): string
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
        }, $xml) ?? $xml;
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
        }, $xml, 1) ?? $xml;
    }

    /**
     * BA Tesis I prodi 225: samakan header dengan pilihan MK & Form di modal BA Sidang.
     *
     * Menangani dua kondisi template:
     *  - sudah di-regenerate: memakai ${kode_mk} / ${no_form}
     *  - template lama: teks literal "FORM #205.2" dan "BERITA ACARA (KODE MK) PROPOSAL TESIS"
     * Contoh hasil: FORM #204.1 / BERITA ACARA MG5090 PROPOSAL TESIS
     */
    private function postProcessBa225Header(string $docxPath, string $kodeMk, string $noForm): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }

        $esc  = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');
        $mk   = $esc($kodeMk);
        $form = $esc($noForm);

        // Placeholder hasil regenerate template:prepare.
        $xml = str_replace('${no_form}', $form, $xml);
        $xml = str_replace('${kode_mk}', $mk, $xml);

        // Teks literal pada template yang belum di-regenerate (run Word terpecah).
        $xml = $this->fuzzyReplaceOnce($xml, 'FORM #205.2', 'FORM #' . $form);
        $xml = $this->fuzzyReplaceOnce(
            $xml,
            'BERITA ACARA (KODE MK) PROPOSAL TESIS',
            'BERITA ACARA ' . $mk
        );
        $xml = $this->fuzzyReplaceOnce(
            $xml,
            'BERITA ACARA ' . $mk . ' (' . $form . ')',
            'BERITA ACARA ' . $mk
        );
        $xml = str_replace(
            '<w:tcW w:w="2573" w:type="dxa"/>',
            '<w:tcW w:w="3173" w:type="dxa"/>',
            $xml
        );
        $xml = str_replace(
            '<w:gridCol w:w="2573"/>',
            '<w:gridCol w:w="3173"/>',
            $xml
        );

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Form Penilaian Tesis I prodi 225: header cukup judul MK saja, tanpa
     * "PROPOSAL TESIS" dan tanpa nomor form (berbeda dengan BA Sidang).
     * Contoh: MG5093 STUDI MANDIRI
     */
    private function postProcessForm225Header(string $docxPath, string $kodeMk): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }

        $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');
        $mk  = $esc($kodeMk);

        // Template lama yang belum di-regenerate: "(Kode mk) PROPOSAL TESIS".
        $xml = $this->fuzzyReplaceOnce($xml, '(Kode mk) PROPOSAL TESIS', $mk);
        // Teks literal setelah ${kode_mk} terisi TemplateProcessor.
        if ($kodeMk !== '') {
            $xml = $this->fuzzyReplaceOnce($xml, $kodeMk . ' PROPOSAL TESIS', $mk);
        }
        // Placeholder belum terisi (jaga-jaga).
        $xml = $this->fuzzyReplaceOnce($xml, '${kode_mk} PROPOSAL TESIS', '${kode_mk}');

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
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

        // Prodi 225: MK & nomor form dipilih lewat modal saat klik "BA Sidang".
        $kodeMkPilih = trim((string) $request->input('kode_mk', ''));
        $noFormBA    = trim((string) $request->input('no_form', ''));
        // 221 Tesis II: 'sidang' (template lama) atau 'mata_kuliah' (template MK baru).
        $jenisCetak  = strtolower(trim((string) $request->input('jenis', '')));

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

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
        }

        // Kode prodi dari ajuan (fallback ke judul) untuk memilih template BA per-prodi.
        $kodeProdi = $ajuan->KODE_PRODI ?? $ajuan->kode_prodi
            ?? $judul->KODE_PRODI ?? $judul->kode_prodi ?? null;

        // Semua tim sidang untuk header pembimbing
        $allTimSidang = TTimSidang::with('userPenilai')
            ->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderByRaw("CASE
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'III$' THEN 3
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'II$' THEN 2
                WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'I$' THEN 1
                ELSE 0 END")
            ->orderBy('URUTAN')
            ->get();

        // Fallback S1: bila Pembimbing hanya diinput di tahapan lain (mis. TA I saat cetak TA II), ambil dari judul yg sama
        $hasPembCurrent = $allTimSidang->contains(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'));
        if (!$hasPembCurrent) {
            $otherPemb = TTimSidang::with('userPenilai')
                ->where('id_judul', $idJudul)
                ->whereRaw("LOWER(COALESCE(STATUS_TIM_SIDANG, '')) LIKE '%pembimbing%'")
                ->orderByRaw("CASE
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'III$' THEN 3
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'II$' THEN 2
                    WHEN UPPER(STATUS_TIM_SIDANG) REGEXP 'I$' THEN 1
                    ELSE 0 END")
                ->orderBy('URUTAN')
                ->get();
            if ($otherPemb->isNotEmpty()) {
                $allTimSidang = $otherPemb->concat($allTimSidang)->values();
            }
        }

        $ketuaPembimbing = $allTimSidang->first(fn($t) =>
            in_array(strtolower(trim($t->status_tim_sidang ?? '')), ['ketua pembimbing', 'pembimbing (ketua)'])
        ) ?? $allTimSidang->first(fn($t) =>
            str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
        );

        $ketuaSidang = $allTimSidang->first(fn($t) =>
            strtolower(trim($t->status_tim_sidang ?? '')) === 'ketua sidang'
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
            // Rata-rata nilai dari view v_ba_nilai_penilai (SUM(NILAI)/5 per penilai per ID_AJUAN)
            $penilaianRow = DB::table('v_ba_nilai_penilai')
                ->where('ID_AJUAN', $ajuan->id)
                ->where('ID_TIM_SIDANG', $penilai->id)
                ->first();

            if ($penilaianRow) {
                $rataNilai = number_format((float) $penilaianRow->NILAI_RATA2, 2);

                $penilaiAverages[] = [
                    'nama' => $penilai->NAMA ?? '-',
                    'nip' => $penilai->NIP ?? '-',
                    'rata_nilai' => $rataNilai,
                    'jumlah_nilai' => (float) $penilaianRow->TOTAL_NILAI,
                    'jumlah_item' => (int) $penilaianRow->JUMLAH_ITEM
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

        // Tanggal & waktu sidang
        $tglSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';
        $waktuMulai = $ajuan->waktu_sidang
            ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i')
            : '';
        $waktuSelesai = $ajuan->waktu_selesai
            ? (strlen($ajuan->waktu_selesai) > 5 ? \Carbon\Carbon::parse($ajuan->waktu_selesai)->format('H:i') : substr((string) $ajuan->waktu_selesai, 0, 5))
            : '';

        // Get latest evaluation date
        $latestPenilaian = TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('TGL_UPDATE', 'desc')
            ->first();
        
        $tanggal = $latestPenilaian?->TGL_UPDATE ?? $latestPenilaian?->TGL_CREATE ?? null;
        $tanggalFormat = $tanggal ? \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') : '';

        // Template: S1 pakai template BA per-KODE_PRODI (122/123/125), S2 pakai template BA per-KODE_PRODI (221/222/223 TG/225)
        $isS1 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S1';
        $isS1Ba = $isS1 && in_array(trim((string) $kodeProdi), ['122', '123', '125'], true);
        $isS2 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S2';
        $isS2Prodi222 = $isS2 && trim((string) $kodeProdi) === '222';
        $isS2Prodi223 = $isS2 && trim((string) $kodeProdi) === '223';
        // 221 Tesis II dengan pilihan "Mata Kuliah": template BA MK khusus.
        $isBa221Mk = $isS2 && trim((string) $kodeProdi) === '221'
            && strtolower(trim($tahapan)) === 'tesis ii'
            && $jenisCetak === 'mata_kuliah';
        if ($isS1Ba) {
            $templatePath = $this->getS1BeritaAcaraTemplate($kodeProdi, $tahapan);
        } elseif ($isS2) {
            $templatePath = $this->getS2BeritaAcaraTemplate($kodeProdi, $tahapan, $jenisCetak);
        } else {
            $templatePath = base_path('template/pROPOSAL/BA proposal TEMPLATE.docx');
        }
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA proposal tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            $pembimbingsBA = $allTimSidang->filter(fn($t) =>
                str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
            )->values();
            $p1BA = $ketuaPembimbing ?? $pembimbingsBA->get(0);
            $p2BA = $pembimbingsBA->first(fn($t) => $t->id !== ($p1BA?->id));

            $namaTimS1S2 = fn($t) => $t ? (trim((string) ($t->NAMA ?? '')) ?: ($t->userPenilai?->NAMA_LENGKAP ?? '-')) : '-';
            $namaP1BA = ($isS2 || $isS1Ba) ? $namaTimS1S2($p1BA) : ($anggotaPembimbing->get(0)?->NAMA ?? '-');
            $namaP2BA = ($isS2 || $isS1Ba) ? $namaTimS1S2($p2BA) : ($anggotaPembimbing->get(1)?->NAMA ?? '-');

            // Set basic placeholders
            $tp->setValue('judul',                  $ajuan->JUDUL    ?? $judul->JUDUL ?? '');
            $tp->setValue('nama_mhs',                $ajuan->NAMA_MHS ?? '-');
            $tp->setValue('nim',                     $ajuan->NIM      ?? '-');
            $tp->setValue('nama_ketua_pembimbing',   $namaTimS1S2($ketuaPembimbing));
            $tp->setValue('nama_pembimbing_i',       $namaP1BA);
            $tp->setValue('nama_pembimbing_ii',      $namaP2BA);
            $tp->setValue('nama ketua pembimbing',   $namaTimS1S2($ketuaPembimbing));
            $tp->setValue('nama pembimbing I',       $namaP1BA);
            $tp->setValue('nama pembimbing II',      $namaP2BA);
            $tp->setValue('tgl_create_penilaian',   $tanggalFormat);
            $tp->setValue('tgl create penilaian',   $tanggalFormat);
            $tp->setValue('nip_ketua_pembimbing',   $ketuaPembimbing?->NIP ?? '-');
            $tp->setValue('nip ketua pembimbing',   $ketuaPembimbing?->NIP ?? '-');
            // Signature block: gunakan Ketua Sidang
            $tp->setValue('nama_ketua_sidang',      $ketuaSidang ? $namaTimS1S2($ketuaSidang) : $namaTimS1S2($ketuaPembimbing));
            $tp->setValue('nip_ketua_sidang',       $ketuaSidang?->NIP ?? $ketuaPembimbing?->NIP ?? '-');
            
            // Hari sidang (Tesis I 223: "hari ${hari_sidang}")
            $tp->setValue('hari_sidang', $ajuan->tgl_sidang
                ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
                : '-');

            // Nilai akhir rata-rata (Tesis I 223: "dengan nilai: ${nilai_akhir_rata2}")
            $tp->setValue('nilai_akhir_rata2', $rataRataKeseluruhan);

            // Nama/NIP penilai pada blok tanda tangan Pembimbing
            $tp->setValue('nama_penilai', $ketuaPembimbing?->NAMA ?? '-');
            $tp->setValue('nip_penilai', $ketuaPembimbing?->NIP ?? '-');

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
            
            // Set individual penilai averages (up to 6 penilai)
            for ($i = 0; $i < 6; $i++) {
                $penilaiNum = $i + 1;
                $nilai = $penilaiAverages[$i]['rata_nilai'] ?? '';
                $tp->setValue("nilai_$penilaiNum", $nilai !== '' ? $nilai : '-');
                $tp->setValue("nilai_rata2_dari_form_penilaian_$penilaiNum", $nilai);
            }

            // Set overall average (for the summary field)
            $tp->setValue('nilai_rata2_keseluruhan', $rataRataKeseluruhan);
            $tp->setValue('nilai_akhir_indeks', $indeks);

            // S1: set Berita Acara Sarjana placeholders (prodi 122, 123, 125)
            $s1TtdMap = [];
            $s1TtdEmpty = [];
            if ($isS1Ba) {
                $kpS1 = trim((string) $kodeProdi);
                $isTa2S1 = in_array(strtolower(trim((string) $tahapan)), ['ta ii', 'ta 2', 'ta-2'], true);
                $prodiS1BA = \App\Models\TProdi::where('KODE_PRODI', $kodeProdi)->first();

                $hariSidangS1 = $ajuan->tgl_sidang
                    ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l')
                    : '-';
                $tglSidangS1 = $ajuan->tgl_sidang
                    ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('d F Y')
                    : '-';

                $kodeMkS1 = '';
                if ($kodeProdi) {
                    $mkRecS1 = TMkProdi::whereRaw('TRIM(KODE_PRODI) = ?', [$kpS1])
                        ->whereRaw('UPPER(TRIM(TAHAPAN_SIDANG)) = ?', [strtoupper(trim($tahapan))])
                        ->first()
                        ?? TMkProdi::whereRaw('TRIM(KODE_PRODI) = ?', [$kpS1])->first();
                    $kodeMkS1 = trim((string) ($mkRecS1->KODE_MK ?? ''));
                }

                $kaprodiS1 = null;
                if (!empty($ajuan->ID_PRODI)) {
                    $kaprodiUid = DB::table('t_user_prodi')
                        ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.id')
                        ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                        ->where('t_user.STATUS_KAPRODI', 'y')
                        ->value('t_user_prodi.ID_USER');
                    if ($kaprodiUid) {
                        $kaprodiS1 = TUser::find($kaprodiUid);
                    }
                }
                if (!$kaprodiS1 && $kpS1 !== '') {
                    $kaprodiS1 = TUser::where('KODE_PRODI', $kpS1)->where('STATUS_KAPRODI', 'y')->first();
                }
                if (!$kaprodiS1 && strlen($kpS1) === 3) {
                    foreach (['2' . substr($kpS1, 1), '3' . substr($kpS1, 1)] as $serumpunKode) {
                        $kaprodiS1 = TUser::where('KODE_PRODI', $serumpunKode)->where('STATUS_KAPRODI', 'y')->first();
                        if ($kaprodiS1) {
                            break;
                        }
                    }
                }

                $catatanRowsS1 = TPenilaian::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapan)
                    ->whereNotNull('CATATAN')
                    ->whereRaw("TRIM(COALESCE(CATATAN, '')) <> ''")
                    ->orderBy('id', 'asc')
                    ->get();
                $catatanByNamaS1 = [];
                foreach ($catatanRowsS1 as $cr) {
                    $nm = trim((string) ($cr->NAMA ?? ''));
                    $isi = trim((string) ($cr->CATATAN ?? ''));
                    if ($nm !== '' && $isi !== '') {
                        $catatanByNamaS1[$nm][] = $isi;
                    }
                }

                $pembS1 = $allTimSidang->filter(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'))->values();
                $pengS1 = $allTimSidang->filter(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'penguji'))->values();
                $p1S1 = $ketuaPembimbing ?? $pembS1->get(0);
                $p2S1 = $pembS1->first(fn($t) => $t->id !== ($p1S1?->id));
                $p3S1 = $pembS1->first(fn($t) => $t->id !== ($p1S1?->id) && $t->id !== ($p2S1?->id));

                $statusLulusS1 = strtoupper(trim((string) ($ajuan->STATUS_LULUS ?? '')));
                $tp->setValue('nama_prodi',     $prodiS1BA?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('alamat_prodi',   $prodiS1BA?->ALAMAT ?? '-');
                $tp->setValue('tlp_prodi',      $prodiS1BA?->TLP ?? '-');
                $tp->setValue('email_prodi',    $prodiS1BA?->EMAIL ?? '-');
                $tp->setValue('hari_sidang',    $hariSidangS1);
                $tp->setValue('tgl_sidang',     $tglSidangS1);
                $tp->setValue('waktu',          $waktuMulai !== '' ? $waktuMulai : '-');
                $tp->setValue('kode_mk',        $kodeMkS1 !== '' ? $kodeMkS1 : '-');
                $tp->setValue('status_lulus',   $statusLulusS1 !== '' ? $statusLulusS1 : '-');
                $tp->setValue('nama_kaprodi',   $kaprodiS1?->NAMA_LENGKAP ?? '-');
                $tp->setValue('nip_kaprodi',    $kaprodiS1?->NIP_NIM ?? '-');
                $tp->setValue('nama_pembimbing_i',   $namaTimS1S2($p1S1));
                $tp->setValue('nip_pembimbing_i',    $p1S1?->NIP ?? '-');
                $tp->setValue('dh_nip_pembimbing_i', $p1S1?->NIP ?? '-');
                $tp->setValue('nama_pembimbing_ii',  $namaTimS1S2($p2S1));
                $tp->setValue('nama_pembimbing_iii', $namaTimS1S2($p3S1));
                // Prodi 123 TA II: bila tidak ada tim berstatus "penguji", Penguji-1
                // diisi Ketua Sidang (setValue hanya bisa dipanggil sekali per token).
                $penguji1S1 = $namaTimS1S2($pengS1->get(0));
                if ($kpS1 === '123' && $isTa2S1 && ($penguji1S1 === '' || $penguji1S1 === '-')) {
                    $penguji1S1 = $ketuaSidang ? $namaTimS1S2($ketuaSidang) : $penguji1S1;
                }
                $tp->setValue('nama_penguji_i',      $penguji1S1);
                $tp->setValue('nama_penguji_ii',     $namaTimS1S2($pengS1->get(1)));
                $tp->setValue('nama_penguji_iii',    $namaTimS1S2($pengS1->get(2)));

                if ($kpS1 === '122' && !$isTa2S1) {
                    $tp->setValue('semester',        $ajuan->SEMESTER ?? '-');
                    $tp->setValue('tahun_akademik',  $ajuan->THN_AKADEMIK ?? '-');
                    $tp->setValue('nilai_huruf',     $indeks);
                    $tp->setValue('nilai_rata2',     $rataRataKeseluruhan);
                    for ($i = 0; $i < 3; $i++) {
                        $t = $penilaiList->get($i);
                        $tp->setValue('nama_penilai_' . ($i + 1), $t?->NAMA ?? '-');
                        $tp->setValue('nip_penilai_' . ($i + 1),  $t?->NIP ?? '-');
                        $tp->setValue('status_tim_' . ($i + 1),   $t?->status_tim_sidang ?? '-');
                        $s1TtdMap['ttd_penilai_' . ($i + 1)] = $this->signatureBinary($t);
                        $s1TtdEmpty['ttd_penilai_' . ($i + 1)] = '';
                    }
                    $ttdKaprodi122 = $this->signatureBinaryOfUser($kaprodiS1?->id);
                    $s1TtdMap['ttd_kaprodi_1']    = $ttdKaprodi122;
                    $s1TtdMap['ttd_pembimbing_i'] = $this->signatureBinary($p1S1);
                    $s1TtdMap['ttd_kaprodi_2']    = $ttdKaprodi122;
                } elseif ($kpS1 === '122' && $isTa2S1) {
                    $slots122 = [$p1S1, $p2S1, $p3S1, $ketuaSidang, $pengS1->get(0), $pengS1->get(1)];
                    foreach ($slots122 as $idx => $slotTim) {
                        $bin = $this->signatureBinary($slotTim);
                        $k1 = 'ttd_' . ($idx + 1);
                        $k2 = 'dh_ttd_' . ($idx + 1);
                        $s1TtdMap[$k1] = $bin;
                        $s1TtdMap[$k2] = $bin;
                        $s1TtdEmpty[$k1] = '';
                        $s1TtdEmpty[$k2] = '';
                    }
                    $ttdKetua122 = $this->signatureBinary($ketuaSidang ?? $p1S1);
                    $s1TtdMap['ttd_ketua_ba'] = $ttdKetua122;
                    $s1TtdMap['dh_ttd_ketua'] = $ttdKetua122;
                } elseif ($kpS1 === '123' && !$isTa2S1) {
                    $huruf123 = TPenilaian::where('ID_JUDUL', $idJudul)
                        ->where('TAHAPAN_SIDANG', $tahapan)
                        ->whereNotNull('NILAI_HURUF')
                        ->whereRaw("TRIM(COALESCE(NILAI_HURUF, '')) <> ''")
                        ->value('NILAI_HURUF') ?? $indeks;
                    $predikatMap = ['A' => 'Sangat Memuaskan', 'AB' => 'Memuaskan', 'B' => 'Memuaskan', 'BC' => 'Cukup', 'C' => 'Cukup', 'D' => 'Tidak Lulus'];
                    $tp->setValue('nilai_huruf', $huruf123);
                    $tp->setValue('predikat', $predikatMap[strtoupper(trim((string) $huruf123))] ?? '-');
                    $tp->setValue('nama_pembimbing_ttd', $p1S1?->NAMA ?? '-');
                    for ($i = 0; $i < 3; $i++) {
                        $t = $penilaiList->get($i);
                        $tp->setValue('nama_penilai_' . ($i + 1), $t?->NAMA ?? '-');
                        $tp->setValue('status_tim_' . ($i + 1),   $t?->status_tim_sidang ?? '-');
                        $tp->setValue('dh_nama_' . ($i + 1),      $t?->NAMA ?? '-');
                        $tp->setValue('dh_status_' . ($i + 1),    $t?->status_tim_sidang ?? '-');
                        $bin = $this->signatureBinary($t);
                        $s1TtdMap['ttd_' . ($i + 1)] = $bin;
                        $s1TtdMap['dh_ttd_' . ($i + 1)] = $bin;
                    }
                    $ttdPemb123 = $this->signatureBinary($p1S1);
                    $s1TtdMap['ttd_pembimbing']    = $ttdPemb123;
                    $s1TtdMap['dh_ttd_pembimbing'] = $ttdPemb123;
                    $catKeys = array_keys($catatanByNamaS1);
                    for ($i = 0; $i < 3; $i++) {
                        $nm = $catKeys[$i] ?? '';
                        $tp->setValue('cat_nama_' . ($i + 1), $nm !== '' ? $nm : '-');
                        $tp->setValue('catatan_' . ($i + 1),  $nm !== '' ? implode('; ', $catatanByNamaS1[$nm]) : '-');
                    }
                } elseif ($kpS1 === '123' && $isTa2S1) {
                    $huruf123 = TPenilaian::where('ID_JUDUL', $idJudul)
                        ->where('TAHAPAN_SIDANG', $tahapan)
                        ->whereNotNull('NILAI_HURUF')
                        ->whereRaw("TRIM(COALESCE(NILAI_HURUF, '')) <> ''")
                        ->value('NILAI_HURUF') ?? $indeks;
                    $tp->setValue('nilai_huruf', $huruf123);

                    $slots123 = [$p1S1, $p2S1, $p3S1, $pengS1->get(0), $pengS1->get(1)];
                    foreach ($slots123 as $idx => $slotTim) {
                        $num = $idx + 1;
                        $skN = '-'; $skL = '-'; $umN = '-'; $umL = '-';
                        if ($slotTim) {
                            $rowsSlot = TPenilaian::where('ID_JUDUL', $idJudul)
                                ->where('TAHAPAN_SIDANG', $tahapan)
                                ->where('ID_TIM_SIDANG', $slotTim->id)
                                ->get();
                            $rSk = $rowsSlot->first(fn($r) => str_contains(strtolower((string) ($r->NAMA_PENILAIAN ?? '')), 'skripsi'));
                            $rUm = $rowsSlot->first(fn($r) => str_contains(strtolower((string) ($r->NAMA_PENILAIAN ?? '')), 'umum'));
                            if ($rSk) {
                                $skN = $rSk->NILAI_HURUF ?? (string) ($rSk->NILAI ?? '-');
                                $skL = $rSk->STATUS_LULUS ?? '-';
                            }
                            if ($rUm) {
                                $umN = $rUm->NILAI_HURUF ?? (string) ($rUm->NILAI ?? '-');
                                $umL = $rUm->STATUS_LULUS ?? '-';
                            }
                        }
                        // Kolom L/TL di tabel: ringkas status kelulusan jadi L atau TL.
                        $ringkasLT = function ($v) {
                            $s = strtoupper(trim((string) $v));
                            if ($s === '') { return '-'; }
                            if (str_contains($s, 'TIDAK') || str_contains($s, 'TL')) { return 'TL'; }
                            if (str_contains($s, 'LULUS')) { return 'L'; }
                            return $s === 'L' || $s === 'TL' ? $s : '-';
                        };
                        $tp->setValue('skripsi_n_' . $num, $skN ?: '-');
                        $tp->setValue('skripsi_l_' . $num, $ringkasLT($skL));
                        $tp->setValue('umum_n_' . $num,    $umN ?: '-');
                        $tp->setValue('umum_l_' . $num,    $ringkasLT($umL));
                    }
                    $ttdOrder123 = [$p1S1, $p2S1, $p3S1, $ketuaSidang, $pengS1->get(0), $pengS1->get(1)];
                    foreach ($ttdOrder123 as $idx => $t) {
                        $bin = $this->signatureBinary($t);
                        $s1TtdMap['ttd_' . ($idx + 1)] = $bin;
                        $s1TtdMap['dh_ttd_' . ($idx + 1)] = $bin;
                    }
                    // Tanda tangan Ketua Sidang (blok bawah): cari dari Ketua Sidang,
                    // fallback ke Pembimbing I lalu Penguji I bila ttd belum tersimpan.
                    $ttdKetua123 = $this->signatureBinary($ketuaSidang)
                        ?? $this->signatureBinary($p1S1)
                        ?? $this->signatureBinary($pengS1->get(0));
                    $s1TtdMap['ttd_ketua_ba'] = $ttdKetua123;
                    $s1TtdMap['dh_ttd_ketua'] = $ttdKetua123;
                    $catKeys = array_keys($catatanByNamaS1);
                    for ($i = 0; $i < 5; $i++) {
                        $nm = $catKeys[$i] ?? '';
                        $tp->setValue('cat_nama_' . ($i + 1), $nm !== '' ? $nm : '-');
                        $tp->setValue('catatan_' . ($i + 1),  $nm !== '' ? implode('; ', $catatanByNamaS1[$nm]) : '-');
                    }
                } elseif ($kpS1 === '125') {
                    $mhsUser125 = !empty($ajuan->ID_USER) ? TUser::find($ajuan->ID_USER) : null;
                    $tempatLahir125 = trim((string) ($mhsUser125?->TEMPAT_LAHIR ?? $mhsUser125?->tempat_lahir ?? '')) ?: '-';
                    $tglLahirRaw125 = $mhsUser125?->TGL_LAHIR ?? $mhsUser125?->tgl_lahir ?? null;
                    $tglLahir125 = $tglLahirRaw125 ? \Carbon\Carbon::parse($tglLahirRaw125)->translatedFormat('d F Y') : '-';
                    $tp->setValue('tempat_lahir', $tempatLahir125);
                    $tp->setValue('tgl_lahir',    $tglLahir125);

                    $rataSkripsi125 = $this->calcProdi125FormAverage($idJudul, $tahapan, 'skripsi');
                    $rataSidang125  = $this->calcProdi125FormAverage($idJudul, $tahapan, 'sidang');
                    $ipVal125 = trim((string) ($ajuan->IP ?? $ajuan->ip ?? ''));
                    if ($ipVal125 === '') {
                        $ipVal125 = trim((string) (\App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
                            ->whereNotNull('IP')
                            ->whereRaw("TRIM(CAST(IP AS CHAR)) <> ''")
                            ->orderByDesc('id')
                            ->value('IP') ?? ''));
                    }
                    $tp->setValue('ip',            $ipVal125 !== '' ? $ipVal125 : '-');
                    $tp->setValue('nilai_skripsi', $rataSkripsi125 !== null ? $rataSkripsi125 : ($rataRataKeseluruhan !== '0.00' ? $rataRataKeseluruhan : '-'));
                    $tp->setValue('nilai_sidang',  $rataSidang125 !== null ? $rataSidang125 : '-');
                    $firstCatNama = array_key_first($catatanByNamaS1) ?? '-';
                    $allCatStr = [];
                    foreach ($catatanByNamaS1 as $nm => $items) {
                        $allCatStr[] = $nm . ': ' . implode('; ', $items);
                    }
                    $tp->setValue('cat_nama_1', $firstCatNama);
                    $tp->setValue('catatan',    !empty($allCatStr) ? implode("\n", $allCatStr) : '-');
                    $s1TtdMap['ttd_ketua_125'] = $this->signatureBinary($ketuaSidang ?? $p1S1);
                }
            }

            // S2: set Berita Acara Magister placeholders (prodi, jadwal, nilai, status, catatan)
            if ($isS2) {
                $kodeProdiBA = $ajuan->KODE_PRODI ?? $ajuan->kode_prodi ?? null;
                $prodiBA = \App\Models\TProdi::where('KODE_PRODI', $kodeProdiBA)->first();

                $tp->setValue('tgl sidang',      $tglSidang);
                $tp->setValue('tgl_sidang',      $tglSidang);
                $tp->setValue('waktu',           $waktuMulai);
                $tp->setValue('waktu_selesai',   $waktuSelesai);
                $tp->setValue('nama prodi',      $prodiBA?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('NAMA PRODI',      $prodiBA?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('alamat prodi',    $prodiBA?->ALAMAT ?? '-');
                $tp->setValue('alamat_prodi',    $prodiBA?->ALAMAT ?? '-');
                $tp->setValue('alamat',          $prodiBA?->ALAMAT ?? '-');
                $tp->setValue('tlp prodi',       $prodiBA?->TLP ?? $prodiBA?->tlp ?? '-');
                $tp->setValue('tlp_prodi',       $prodiBA?->TLP ?? $prodiBA?->tlp ?? '-');
                $tp->setValue('tlp',             $prodiBA?->TLP ?? $prodiBA?->tlp ?? '-');
                $tp->setValue('fax prodi',       $prodiBA?->FAX ?? $prodiBA?->fax ?? '-');
                $tp->setValue('fax_prodi',       $prodiBA?->FAX ?? $prodiBA?->fax ?? '-');
                $tp->setValue('fax',             $prodiBA?->FAX ?? $prodiBA?->fax ?? '-');
                $tp->setValue('email prodi',     $prodiBA?->EMAIL ?? $prodiBA?->email ?? '-');
                $tp->setValue('email_prodi',     $prodiBA?->EMAIL ?? $prodiBA?->email ?? '-');
                $tp->setValue('email',           $prodiBA?->EMAIL ?? $prodiBA?->email ?? '-');
                $tp->setValue('website_prodi',   $prodiBA?->WEB ?? $prodiBA?->web ?? '-');
                $tp->setValue('web',             $prodiBA?->WEB ?? $prodiBA?->web ?? '-');

                // Kode MK from t_mk_prodi (match prodi + tahapan)
                $kodeMkBA = '';
                if ($kodeProdiBA) {
                    $mkRecBA = \App\Models\TMkProdi::whereRaw('TRIM(KODE_PRODI) = ?', [trim((string) $kodeProdiBA)])
                        ->first();
                    if ($mkRecBA) {
                        $kodeMkBA = trim((string) ($mkRecBA->KODE_MK ?? ''));
                    }
                }
                // Prodi 225: Tesis II selalu MG5093; tahapan lain memakai MK pilihan modal.
                if (trim((string) $kodeProdi) === '225') {
                    if (strtolower(trim($tahapan)) === 'tesis ii' && $kodeMkPilih === '') {
                        $kodeMkPilih = 'MG5093 STUDI MANDIRI';
                    }
                    if ($kodeMkPilih !== '') {
                        $kodeMkBA = $kodeMkPilih;
                    }
                }
                $tp->setValue('kode mk',   $kodeMkBA);
                $tp->setValue('KODE MK',   $kodeMkBA);
                $tp->setValue('kode_mk',   $kodeMkBA);
                $tp->setValue('no_form',   $noFormBA);

                // Kaprodi for prodi
                $kaprodiBA = null;
                if (!empty($ajuan->ID_PRODI)) {
                    $kaprodiUserIdBA = DB::table('t_user_prodi')
                        ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.id')
                        ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                        ->where('t_user.STATUS_KAPRODI', 'y')
                        ->value('t_user_prodi.ID_USER');
                    if ($kaprodiUserIdBA) {
                        $kaprodiBA = \App\Models\TUser::find($kaprodiUserIdBA);
                    }
                }
                if (!$kaprodiBA && $kodeProdiBA) {
                    $kaprodiBA = \App\Models\TUser::where('KODE_PRODI', $kodeProdiBA)
                        ->where('STATUS_KAPRODI', 'y')
                        ->first();
                }
                // Fallback: prodi S2 serumpun S3 (221 -> 321, dsb)
                if (!$kaprodiBA && $kodeProdiBA) {
                    $s3Prodi = '3' . substr((string) $kodeProdiBA, 1);
                    $kaprodiBA = \App\Models\TUser::where('KODE_PRODI', $s3Prodi)
                        ->where('STATUS_KAPRODI', 'y')
                        ->first();
                }
                if (!$kaprodiBA) {
                    $kaprodiBA = \App\Models\TUser::where('STATUS_KAPRODI', 'y')->first();
                }

                // Nilai & status kelulusan
                $statusLulusBA = strtoupper(trim((string) ($ajuan->STATUS_LULUS ?? '')));
                $tp->setValue('nilai rata2',   $rataRataKeseluruhan);
                $tp->setValue('nilai huruf',   $indeks);
                $tp->setValue('predikat',      $indeks);
                $tp->setValue('status lulus',  $statusLulusBA);

                // Catatan perbaikan diambil dari t_penilaian baris "Catatan Perbaikan"
                // (STATUS_CATATAN point = y), yang isinya ada di kolom CATATAN.
                $catatanRows = TPenilaian::where('ID_JUDUL', $idJudul)
                    ->where('TAHAPAN_SIDANG', $tahapan)
                    ->where(function ($q) {
                        $q->whereRaw("LOWER(TRIM(COALESCE(NAMA_PENILAIAN, ''))) LIKE '%catatan%'")
                          ->orWhereRaw("LOWER(TRIM(COALESCE(NAMA_PENILAIAN, ''))) LIKE '%perbaikan%'");
                    })
                    ->whereNotNull('CATATAN')
                    ->whereRaw("TRIM(COALESCE(CATATAN, '')) <> ''")
                    ->orderBy('id', 'asc')
                    ->get();

                // Catatan dikelompokkan per penilai (NAMA). Urut: pembimbing dulu, lalu penguji.
                $catatanByNama = [];
                foreach ($catatanRows as $cr) {
                    $nama = trim((string) ($cr->NAMA ?? ''));
                    $isi = trim((string) ($cr->CATATAN ?? ''));
                    if ($nama === '' || $isi === '') {
                        continue;
                    }
                    if (!isset($catatanByNama[$nama])) {
                        $catatanByNama[$nama] = [];
                    }
                    $catatanByNama[$nama][] = $isi;
                }

                // Urutan sesuai tim sidang: pembimbing (I,II,III), ketua sidang, penguji
                $catatanPembimbing = [];
                $seenNama = [];
                foreach ($allTimSidang as $ts) {
                    $namaTim = trim((string) ($ts->NAMA ?? ''));
                    if ($namaTim !== '' && isset($catatanByNama[$namaTim]) && !isset($seenNama[$namaTim])) {
                        $catatanPembimbing[] = implode(' ', $catatanByNama[$namaTim]);
                        $seenNama[$namaTim] = true;
                    }
                }
                // Sisa catatan dari penilai yang tidak ada di tim sidang
                foreach ($catatanByNama as $nama => $isiArr) {
                    if (!isset($seenNama[$nama])) {
                        $catatanPembimbing[] = implode(' ', $isiArr);
                    }
                }
                while (count($catatanPembimbing) < 3) {
                    $catatanPembimbing[] = '-';
                }

                $tp->setValue('catatan_pembimbing_i',   $catatanPembimbing[0] ?? '-');
                $tp->setValue('catatan_pembimbing_ii',  $catatanPembimbing[1] ?? '-');
                $tp->setValue('catatan_pembimbing_iii', $catatanPembimbing[2] ?? '-');
                // Versi spasi (kompatibilitas template lama)
                $tp->setValue('catatan pembimbing I', $catatanPembimbing[0] ?? '-');
                $tp->setValue('catatan pembimbing II', $catatanPembimbing[1] ?? '-');
                $tp->setValue('catatan pembimbing III', $catatanPembimbing[2] ?? '-');
                // Baris "dengan catatan: ${nama_pembimbing_i} : ${catatan}" -> catatan pembimbing utama
                $tp->setValue('catatan', $catatanPembimbing[0] ?? '-');

                $tp->setValue('nama kaprodi',    $kaprodiBA?->NAMA_LENGKAP ?? '-');
                $tp->setValue('nip kaprodi',     $kaprodiBA?->NIP_NIM ?? '-');

                // Placeholder versi underscore (sesuai hasil `template:prepare`)
                // agar field benar-benar terisi di template BA S2.
                $tp->setValue('nama_prodi',   $prodiBA?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
                $tp->setValue('kode_mk',      $kodeMkBA);
                $tp->setValue('nama_kaprodi', $kaprodiBA?->NAMA_LENGKAP ?? '-');
                $tp->setValue('nip_kaprodi',  $kaprodiBA?->NIP_NIM ?? '-');
                $tp->setValue('nilai_huruf',  $indeks);
                $tp->setValue('status_lulus', $statusLulusBA);

                // Tim sidang S2 (Pembimbing 1, Pembimbing 2, Penguji 1, Ketua Penguji)
                $pembimbings = $allTimSidang->filter(fn($t) =>
                    str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing')
                )->values();
                $p1 = $ketuaPembimbing ?? $pembimbings->get(0);
                $p2 = $pembimbings->first(fn($t) => $t->id !== ($p1?->id));

                $pengujis = $allTimSidang->filter(fn($t) =>
                    str_contains(strtolower($t->status_tim_sidang ?? ''), 'penguji')
                    || str_contains(strtolower($t->status_tim_sidang ?? ''), 'ketua sidang')
                )->values();
                $ketuaPenguji = $ketuaSidang ?? $pengujis->get(0);
                $penguji1 = $pengujis->first(fn($t) => $t->id !== ($ketuaPenguji?->id)) ?? $pengujis->get(0);

                if ($isBa221Mk) {
                    $pengujisBa221 = $allTimSidang->filter(fn($t) =>
                        str_contains(strtolower((string) $t->status_tim_sidang ?? ''), 'penguji')
                    )->values();
                    $penguji1 = $pengujisBa221->get(0);
                    $penguji2 = $pengujisBa221->get(1);
                }

                $tp->setValue('nama_pembimbing_i',  $p1?->NAMA ?? '-');
                $tp->setValue('nama_pembimbing_ii', $p2?->NAMA ?? '-');
                $tp->setValue('nip_pembimbing_i',   $p1?->NIP ?? '-');
                $tp->setValue('nip_pembimbing_ii',  $p2?->NIP ?? '-');
                $tp->setValue('nama_ketua_penguji', $ketuaPenguji?->NAMA ?? '-');
                $tp->setValue('nip_ketua_penguji',  $ketuaPenguji?->NIP ?? '-');
                $tp->setValue('nama_penguji_i',     $penguji1?->NAMA ?? '-');
                $tp->setValue('nip_penguji_i',      $penguji1?->NIP ?? '-');

                if ($isBa221Mk) {
                    $tp->setValue('nama_penguji_ii', $penguji2?->NAMA ?? '-');
                    $tp->setValue('nip_penguji_ii',  $penguji2?->NIP ?? '-');
                }

                // BA Tesis I prodi 221: baris Penilai 3 diisi Ketua Sidang (ketua penguji),
                // bukan penguji biasa. Ketua Sidang tidak menginput nilai -> nilai_3 = '-'.
                if (trim((string) $kodeProdi) === '221' && $ketuaSidang && ! $isBa221Mk) {
                    $tp->setValue('nama_penguji_i', $ketuaSidang->NAMA ?? '-');
                    $tp->setValue('nip_penguji_i',  $ketuaSidang->NIP ?? '-');
                    $tp->setValue('nilai_3', '-');
                }

                // BA Tesis I/II 223 TG: 6 baris tim penilai (Pembimbing, Ko-Pem 1, Ko-Pem 2, Ketua Sidang, Penguji 1, Penguji 2)
                if ($isS2Prodi223) {
                    $pembimbingUtama223 = $ketuaPembimbing ?? $pembimbings->get(0);
                    $anggota223 = $pembimbings->filter(fn($t) => $t->id !== ($pembimbingUtama223?->id))->values();
                    $ko1_223 = $anggota223->get(0);
                    $ko2_223 = $anggota223->get(1);
                    $pengujisOnly223 = $allTimSidang->filter(fn($t) =>
                        str_contains(strtolower($t->status_tim_sidang ?? ''), 'penguji')
                    )->values();
                    $ketuaSidang223 = $ketuaSidang ?? $allTimSidang->first(fn($t) =>
                        strtolower(trim($t->status_tim_sidang ?? '')) === 'ketua sidang'
                    );
                    $penguji1_223 = $pengujisOnly223->get(0);
                    $penguji2_223 = $pengujisOnly223->get(1);

                    $tp->setValue('nama_pembimbing_i',   $pembimbingUtama223?->NAMA ?? '-');
                    $tp->setValue('nama_pembimbing_ii',  $ko1_223?->NAMA ?? ($p2?->NAMA ?? '-'));
                    $tp->setValue('nama_pembimbing_iii', $ko2_223?->NAMA ?? '-');
                    $tp->setValue('nama_ketua_sidang',   $ketuaSidang223?->NAMA ?? ($ketuaPenguji?->NAMA ?? '-'));
                    $tp->setValue('nama_penguji_i',      $penguji1_223?->NAMA ?? ($penguji1?->NAMA ?? '-'));
                    $tp->setValue('nama_penguji_ii',     $penguji2_223?->NAMA ?? '-');
                }

                // Prodi 222 (Teknik Perminyakan): BA tabel 4 penilai + Form Pengeluaran Nilai
                if ($isS2Prodi222) {
                    $tp->setValue('semester',             $ajuan->SEMESTER ?? '');
                    $tp->setValue('tahun_akademik',       $ajuan->THN_AKADEMIK ?? '');
                    $tp->setValue('nilai_huruf_akhir_ba', $indeks);
                }
            }

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

        // Prodi 225: header "FORM #..." dan "BERITA ACARA ..." mengikuti
        // pilihan MK & Form di modal BA Sidang.
        if ($isS2 && trim((string) $kodeProdi) === '225') {
            $this->postProcessBa225Header(
                $docxPath,
                $kodeMkPilih !== '' ? $kodeMkPilih : $kodeMkBA,
                $noFormBA !== '' ? $noFormBA : '205.2'
            );
        }

        // BA MK 221: isi slot tanda tangan bernama (4 penilai + ketua sidang + kaprodi).
        if ($isBa221Mk) {
            $this->fillBa221MkSignatures($docxPath, [
                'signature_1'            => $this->signatureBinary($p1),
                'signature_2'            => $this->signatureBinary($p2),
                'signature_3'            => $this->signatureBinary($penguji1),
                'signature_4'            => $this->signatureBinary($penguji2),
                'signature_ketua_sidang' => $this->signatureBinary($ketuaSidang),
                'signature_kaprodi'      => $this->signatureBinaryOfUser($kaprodiBA?->id),
            ]);
        }

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
        ];
        $this->replaceInDocx($docxPath, $replacements);

        // Final safety net: replace any remaining literal placeholders directly in XML
        // (mengatasi kasus setValue tidak menemukan token karena pecahan run Word / cache).
        $finalReplacements = [
            'hari_sidang'              => $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') : '-',
            'nilai_akhir_rata2'        => $rataRataKeseluruhan,
            'nip_penilai'              => $ketuaPembimbing?->NIP ?? '-',
            'nama_penilai'             => $ketuaPembimbing?->NAMA ?? '-',
            'predikat'                 => $indeks,
            'nilai_akhir_indeks'       => $indeks,
            'catatan_pembimbing_i'     => $catatanPembimbing[0] ?? '-',
            'catatan_pembimbing_ii'    => $catatanPembimbing[1] ?? '-',
            'catatan_pembimbing_iii'   => $catatanPembimbing[2] ?? '-',
            'catatan'                  => $catatanPembimbing[0] ?? '-',
        ];
        if ($isBa221Mk) {
            $finalReplacements['nama_pembimbing_i']  = $p1?->NAMA ?? '-';
            $finalReplacements['nama_pembimbing_ii'] = $p2?->NAMA ?? '-';
            $finalReplacements['nama_penguji_i']     = $penguji1?->NAMA ?? '-';
            $finalReplacements['nama_penguji_ii']    = $penguji2?->NAMA ?? '-';
        }
        $this->replaceInDocx($docxPath, $finalReplacements);

        // Pusatkan nilai di cell tabel BA (kolom Nilai = center align)
        $this->centerValueCellsInBaProposal($docxPath);

        // Checkbox LULUS/TIDAK LULUS & Status Publikasi untuk semua template BA S2 (Tesis I & Tesis II)
        if ($isS2) {
            $publikasiBa = ['Submitt' => 't', 'Under Review' => 't', 'Publish' => 't'];
            try {
                $rowsPub = DB::table('t_publikasi')->where('ID_AJUAN_SIDANG', $ajuan->id)->get();
                foreach ($rowsPub as $rp) {
                    $k = (string) ($rp->PUBLIKASI ?? '');
                    if ($k !== '') {
                        $publikasiBa[$k] = (string) ($rp->STATUS ?? 't');
                    }
                }
            } catch (\Throwable $e) {}
            $this->applyS2BaCheckboxes($docxPath, $ajuan->STATUS_LULUS ?? '', $publikasiBa);
        }

        // Sisipkan tanda tangan penilai ke BA
        if ($isS1Ba) {
            if (trim((string) $kodeProdi) === '125') {
                $nh1Ba125 = trim((string) ($ajuan->NILAI_HURUF ?? $ajuan->nilai_huruf ?? ''));
                if ($nh1Ba125 === '') {
                    $nh1Ba125 = trim((string) (\App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
                        ->whereNotNull('NILAI_HURUF')
                        ->whereRaw("TRIM(COALESCE(NILAI_HURUF, '')) <> ''")
                        ->orderByDesc('id')
                        ->value('NILAI_HURUF') ?? ''));
                }
                $nh2Ba125 = trim((string) ($ajuan->NILAI_HURUF2 ?? $ajuan->nilai_huruf2 ?? ''));
                if ($nh2Ba125 === '') {
                    $nh2Ba125 = trim((string) (\App\Models\TAjuanSidang::where('ID_JUDUL', $idJudul)
                        ->whereNotNull('NILAI_HURUF2')
                        ->whereRaw("TRIM(COALESCE(NILAI_HURUF2, '')) <> ''")
                        ->orderByDesc('id')
                        ->value('NILAI_HURUF2') ?? ''));
                }
                $this->postProcessS1Ba125(
                    $docxPath,
                    $nh1Ba125,
                    $nh2Ba125,
                    (string) ($ajuan->STATUS_LULUS ?? '')
                );
            }
            if (!empty($s1TtdMap)) {
                $this->fillBa221MkSignatures($docxPath, $s1TtdMap, $s1TtdEmpty ?? []);
            }
            if (trim((string) $kodeProdi) === '122' && in_array(strtolower(trim((string) $tahapan)), ['ta ii', 'ta 2', 'ta-2'], true)) {
                $this->postProcessS1Ba122Ta2(
                    $docxPath,
                    $allTimSidang,
                    $penilaiAverages,
                    (string) ($ajuan->STATUS_LULUS ?? ''),
                    $indeks,
                    $catatanByNamaS1 ?? []
                );
            }
        } elseif ($isS2Prodi222) {
            // Bangun tabel Tim Penilai dinamis (baris mengikuti jumlah pembimbing/penguji).
            // Tanda tangan per baris sudah disisipkan di buildBa222DynamicTimTable().
            $this->buildBa222DynamicTimTable($docxPath, $penilaiList->all());

            // Sisa 3 slot ${signature}:
            //   1 = Kaprodi (blok "Ketua Program Studi Magister" sebelum Form Pengeluaran Nilai)
            //   2 = Pembimbing 1 di Form Pengeluaran Nilai ("Tanda Tangan Pembimbing")
            //   3 = Kaprodi (blok tanda tangan bawah)
            $this->embedAllSkSignatures($docxPath, [
                $this->signatureBinaryOfUser($kaprodiBA?->id),
                $this->signatureBinary($p1),
                $this->signatureBinaryOfUser($kaprodiBA?->id),
            ]);
        } elseif ($isS2Prodi223) {
            // BA Tesis I/II 223 TG: 6 baris (Pembimbing, Ko-Pem 1, Ko-Pem 2, Ketua Sidang, Penguji 1, Penguji 2)
            $pembimbingUtama223 = $ketuaPembimbing ?? $pembimbingsBA->get(0);
            $anggota223 = $pembimbingsBA->filter(fn($t) => $t->id !== ($pembimbingUtama223?->id))->values();
            $ko1_223 = $anggota223->get(0);
            $ko2_223 = $anggota223->get(1);
            $pengujisOnly223 = $allTimSidang->filter(fn($t) =>
                str_contains(strtolower($t->status_tim_sidang ?? ''), 'penguji')
            )->values();
            $ketuaSidang223 = $ketuaSidang ?? $allTimSidang->first(fn($t) =>
                strtolower(trim($t->status_tim_sidang ?? '')) === 'ketua sidang'
            );
            $penguji1_223 = $pengujisOnly223->get(0);
            $penguji2_223 = $pengujisOnly223->get(1);

            $ttdPembimbingUtama = $this->signatureBinary($pembimbingUtama223);
            $ttdKo1 = $this->signatureBinary($ko1_223);
            $ttdKo2 = $this->signatureBinary($ko2_223);
            $ttdKetuaSidang = $this->signatureBinary($ketuaSidang223);
            $ttdKaprodi = $this->signatureBinaryOfUser($kaprodiBA?->id);

            if (strtolower(trim((string) $tahapan)) === 'tesis ii') {
                // BA Tesis II 223: 6 baris Tim Penilai + kaprodi + ketua sidang.
                $this->embedAllSkSignatures($docxPath, [
                    $ttdPembimbingUtama, $ttdKo1, $ttdKo2,
                    $ttdKetuaSidang,
                    $this->signatureBinary($penguji1_223),
                    $this->signatureBinary($penguji2_223),
                    $ttdKaprodi,
                    $ttdKetuaSidang,
                ]);
            } else {
                // BA Tesis I 223: 3 slot di tabel "Penguji" + 1 slot di blok
                // tanda tangan "Pembimbing," (nama_penilai = ketua pembimbing).
                // Jumlah slot disesuaikan otomatis dengan isi template.
                $this->embedAllSkSignaturesAvailable($docxPath, [
                    $ttdPembimbingUtama, $ttdKo1, $ttdKo2,     // 1-3: Pembimbing 1, 2, 3
                    $ttdPembimbingUtama,                       // 4: Pembimbing (footer)
                    $ttdKetuaSidang,
                    $this->signatureBinary($penguji1_223),
                    $this->signatureBinary($penguji2_223),
                    $ttdKaprodi,
                    $ttdKetuaSidang,
                ]);
            }
        } elseif ($isS2 && ! $isBa221Mk) {
            // BA S2 221 & 225: tanda tangan penilai disisipkan sebelum baris
            // titik-titik, sedangkan slot kaprodi mengikuti ${signature} template.
            $penilaiTtd = [$p1, $p2];
            // Baris Penilai 3 template 221 = Ketua Sidang (sama seperti pengisian nama di atas).
            $penilaiTtd[] = (trim((string) $kodeProdi) === '221' && $ketuaSidang) ? $ketuaSidang : $penguji1;
            $this->embedS2BaSignaturesByDots(
                $docxPath,
                array_map(fn($t) => $t?->id_user_penilai, $penilaiTtd),
                $kaprodiBA?->id
            );
        } elseif ($ketuaSidang) {
            $this->fillSignaturePlaceholder($docxPath, $ketuaSidang->id_user_penilai);
        } elseif ($ketuaPembimbing) {
            $this->fillSignaturePlaceholder($docxPath, $ketuaPembimbing->id_user_penilai);
        }

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

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
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

        // Baris kolom (3): Ketua Pembimbing, Ko-pembimbing 1, Ko-pembimbing 2, Penguji 1, Penguji 2
        $rowStatuses = ['ketua pembimbing', 'pembimbing i', 'pembimbing ii', 'penguji i', 'penguji ii'];

        $nilaiRows = [];
        foreach ($rowStatuses as $st) {
            $tim = $timByStatus[$st] ?? null;
            $rata = '';
            if ($tim) {
                $nilaiRata2 = DB::table('v_ba_nilai_penilai')
                    ->where('ID_AJUAN', $ajuan->id)
                    ->where('ID_TIM_SIDANG', $tim->id)
                    ->value('NILAI_RATA2');
                if ($nilaiRata2 !== null) {
                    $rata = number_format((float) $nilaiRata2, 2);
                }
            }
            $nilaiRows[] = $rata;
        }

        // Nama kolom "Tim Penguji/Penilai" = daftar PENGUJI saja (penguji I, II, III, ...)
        $pengujiNames = [];
        foreach ($timByStatus as $status => $t) {
            if (preg_match('/^penguji\s+(i{1,3}|iv|v)$/', $status)) {
                $pengujiNames[] = $t->NAMA ?? '-';
            }
        }
        // Slot nama per baris tabel (1..5); baris 1-3 = pembimbing (${nama_ketua_pembimbing},
        // ${nama_pembimbing_i}, ${nama_pembimbing_ii}), baris 4-5 = penguji
        // (placeholder ${nama_penguji_i} muncul 2x, diisi per-kemunculan oleh fillBaSkXml).
        $slot1 = ($timByStatus['ketua pembimbing'] ?? null)?->NAMA ?: '.............';
        $slot2 = ($timByStatus['pembimbing i'] ?? null)?->NAMA ?: '.............';
        $slot3 = ($timByStatus['pembimbing ii'] ?? null)?->NAMA ?: '.............';
        $slot4 = ($timByStatus['penguji i'] ?? null)?->NAMA ?: ($pengujiNames[0] ?? '.............');
        $slot5 = ($timByStatus['penguji ii'] ?? null)?->NAMA ?: ($pengujiNames[1] ?? '.............');

        // Nilai Akhir Rata-rata (NA) = rata-rata kolom (3) no 1-5
        $adaNilai = array_values(array_filter($nilaiRows, fn($v) => $v !== ''));
        $na = count($adaNilai) > 0
            ? number_format(array_sum(array_map('floatval', $adaNilai)) / count($adaNilai), 2)
            : '';
        $indeks = $na !== '' ? $this->calculateIndeksSk((float) $na) : '';

        // Kaprodi (scope ke prodi mahasiswa)
        $kaprodi = null;
        if (!empty($ajuan->ID_PRODI)) {
            $kaprodiUserId = DB::table('t_user_prodi')
                ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.ID')
                ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                ->where('t_user.STATUS_KAPRODI', 'y')
                ->value('t_user_prodi.ID_USER');
            if ($kaprodiUserId) {
                $kaprodi = TUser::find($kaprodiUserId);
            }
        }
        if (!$kaprodi) {
            $kaprodi = TUser::where('STATUS_KAPRODI', 'y')->first();
        }

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
            $tp->setValue('nama_kaprodi',    $kaprodi ? $kaprodi->NAMA_LENGKAP : '.............');
            $tp->setValue('nip_kaprodi',     $kaprodi ? $kaprodi->NIP_NIM : '.............');
            // Kolom Nama "Tim Penguji/Penilai": baris 1-3 = pembimbing.
            // Baris 4-5 menggunakan ${nama_penguji_i} (muncul 2x) -> diisi per-kemunculan di fillBaSkXml.
            $tp->setValue('nama_ketua_pembimbing', $slot1);  // Row 1
            $tp->setValue('nama_pembimbing_i',     $slot2);  // Row 2
            $tp->setValue('nama_pembimbing_ii',    $slot3);  // Row 3
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
            'nama_kaprodi' => $kaprodi ? $kaprodi->NAMA_LENGKAP : '',
            'nip_kaprodi'  => $kaprodi ? $kaprodi->NIP_NIM : '',
            'nama_penguji_i'  => $slot4,   // Row 4 (Penguji 1)
            'nama_penguji_ii' => $slot5,   // Row 5 (Penguji 2)
            'prodi'        => $this->namaProdiAjuan($ajuan),
        ]);

        // Tanda tangan per-baris sesuai peran: Ketua, Ko-1, Ko-2, Penguji-1, Penguji-2
        // (indeks mengikuti urutan baris tabel; peran kosong -> null -> dibiarkan kosong).
        $roleStatuses = ['ketua pembimbing', 'pembimbing i', 'pembimbing ii', 'penguji i', 'penguji ii'];
        $signatures = [];
        foreach ($roleStatuses as $st) {
            $signatures[] = $this->signatureBinary($timByStatus[$st] ?? null);
        }

        // Isi ${signature} di tabel dengan tanda tangan per-orang
        $this->embedAllSkSignatures($docxPath, $signatures);

        // Isi ${signature} di footer dengan tanda tangan kaprodi
        $this->fillSignaturePlaceholder($docxPath, optional($kaprodi)->id);

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
        // Template hasil regenerate: placeholder ${nilai_akhir_rata_rata} & ${nilai_akhir_indeks}.
        $xml = preg_replace('/\$\{nilai_akhir_rata_rata\}/', $esc($data['na']), $xml, 1);
        $xml = preg_replace('/\$\{nilai_akhir_indeks\}/', $esc($data['indeks']), $xml, 1);
        // Template lama: placeholder $(Nilai rata2) -> NA & Indeks.
        $xml = $this->fuzzyReplaceOnce($xml, '$(Nilai rata2)', $esc($data['na']));
        $xml = $this->fuzzyReplaceOnce($xml, '$(Nilai rata2)', $esc($data['indeks']));

        // Nama & NIP Kaprodi (mungkin terpecah antar XML run)
        if (!empty($data['nama_kaprodi'])) {
            $xml = $this->fuzzyReplaceOnce($xml, '${nama_kaprodi}', $esc($data['nama_kaprodi']));
            $xml = $this->fuzzyReplaceOnce($xml, '$(Nama kaprodi)', $esc($data['nama_kaprodi']));
        }
        if (!empty($data['nip_kaprodi'])) {
            $xml = $this->fuzzyReplaceOnce($xml, '${nip_kaprodi}', $esc($data['nip_kaprodi']));
            $xml = $this->fuzzyReplaceOnce($xml, '$(nip kaprodi)', $esc($data['nip_kaprodi']));
        }

        // Nama prodi statis pada blok tanda tangan kaprodi -> NAMA_PRODI ajuan
        if (!empty($data['prodi'])) {
            $xml = $this->fuzzyReplaceAll($xml, 'Teknik Geofisika', $esc($data['prodi']));
        }

        // Baris 4-5 (Penguji I & II) menggunakan placeholder ${nama_penguji_i} (muncul 2x):
        // ganti per-kemunculan secara berurutan.
        $pengSlots = [$esc($data['nama_penguji_i'] ?? ''), $esc($data['nama_penguji_ii'] ?? '')];
        $pengIdx = 0;
        $xml = preg_replace_callback('/\$\{nama_penguji_i\}/', function ($m) use ($pengSlots, &$pengIdx) {
            $val = $pengSlots[$pengIdx] ?? $m[0];
            $pengIdx++;
            return $val;
        }, $xml);

        // Bersihkan sisa placeholder agar tidak bocor ke output.
        $xml = preg_replace('/\$\{nilai_rata2\}/', '', $xml);
        $xml = preg_replace('/\$\{nilai_akhir_rata_rata\}/', '', $xml);
        $xml = preg_replace('/\$\{nilai_akhir_indeks\}/', '', $xml);
        $xml = preg_replace('/\$\{nama_kaprodi\}/', '', $xml);
        $xml = preg_replace('/\$\{nip_kaprodi\}/', '', $xml);
        $xml = preg_replace('/\$\([^)]*kaprodi[^)]*\)/i', '', $xml);

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

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
        }

        // Tim sidang per status (+ alias fleksibel)
        $allTim = TTimSidang::where('id_judul', $idJudul)->where('tahapan_sidang', $tahapan)->get();
        $timByStatus = [];
        foreach ($allTim as $t) {
            $timByStatus[strtolower(trim($t->status_tim_sidang ?? ''))] = $t;
        }

        // Pemetaan peran (mendukung pola "Ketua Pembimbing" atau "Pembimbing I" sebagai ketua)
        $hasKetua = isset($timByStatus['ketua pembimbing'])
            || isset($timByStatus['pembimbing (ketua)'])
            || isset($timByStatus['ketua pembimbing (pembimbing)']);
        if (isset($timByStatus['pembimbing (ketua)']) && !isset($timByStatus['ketua pembimbing'])) {
            $timByStatus['ketua pembimbing'] = $timByStatus['pembimbing (ketua)'];
        }

        // Kumpulkan pembimbing & penguji berurutan (fallback jika label status tidak standar)
        $pembimbingOrdered = $allTim
            ->filter(function ($t) {
                $s = strtolower(trim($t->status_tim_sidang ?? ''));
                return str_contains($s, 'pembimbing') && !str_contains($s, 'ketua sidang');
            })
            ->sortBy(function ($t) {
                $s = strtolower(trim($t->status_tim_sidang ?? ''));
                if (str_contains($s, 'ketua')) return 0;
                if (preg_match('/\bi\b|1/', $s)) return 1;
                if (preg_match('/\bii\b|2/', $s)) return 2;
                if (preg_match('/\biii\b|3/', $s)) return 3;
                return 9;
            })
            ->values();

        $pengujiOrdered = $allTim
            ->filter(function ($t) {
                $s = strtolower(trim($t->status_tim_sidang ?? ''));
                return (bool) preg_match('/penguji/', $s);
            })
            ->sortBy(function ($t) {
                $s = strtolower(trim($t->status_tim_sidang ?? ''));
                if (preg_match('/\bi\b|1/', $s)) return 1;
                if (preg_match('/\bii\b|2/', $s)) return 2;
                if (preg_match('/\biii\b|3/', $s)) return 3;
                return 9;
            })
            ->values();

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
        $roleTims = [];
        foreach ($roleStatuses as $key => $st) {
            $tim = $timByStatus[$st] ?? null;
            // Fallback ke daftar berurutan
            if (!$tim) {
                if (str_starts_with($key, 'ketua') || str_starts_with($key, 'ko_')) {
                    $idx = match ($key) {
                        'ketua_pembimbing' => 0,
                        'ko_pembimbing_1' => 1,
                        'ko_pembimbing_2' => 2,
                        default => -1,
                    };
                    $tim = $idx >= 0 ? ($pembimbingOrdered->get($idx) ?? null) : null;
                } elseif (str_starts_with($key, 'penguji_')) {
                    $idx = (int) substr($key, -1) - 1;
                    $tim = $pengujiOrdered->get($idx) ?? null;
                }
            }
            $roleTims[$key] = $tim;
            $rata = '';
            if ($tim) {
                $nilaiRata2 = DB::table('v_ba_nilai_penilai')
                    ->where('ID_AJUAN', $ajuan->id)
                    ->where('ID_TIM_SIDANG', $tim->id)
                    ->value('NILAI_RATA2');
                if ($nilaiRata2 !== null) {
                    $rata = number_format((float) $nilaiRata2, 2);
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

        // Nama evaluator: baris 1-3 = pembimbing, baris 4-6 = penguji
        $namaKetuaPembimbing = ($roleTims['ketua_pembimbing'] ?? null)?->NAMA
            ?? ($pembimbingOrdered->get(0)?->NAMA)
            ?? '.............';
        $namaKoPembimbing1   = ($roleTims['ko_pembimbing_1'] ?? null)?->NAMA
            ?? ($pembimbingOrdered->get(1)?->NAMA)
            ?? '.............';
        $namaKoPembimbing2   = ($roleTims['ko_pembimbing_2'] ?? null)?->NAMA
            ?? ($pembimbingOrdered->get(2)?->NAMA)
            ?? '.............';
        $namaPenguji = [
            'peng1' => ($roleTims['penguji_1'] ?? null)?->NAMA ?? ($pengujiOrdered->get(0)?->NAMA) ?? '.............',
            'peng2' => ($roleTims['penguji_2'] ?? null)?->NAMA ?? ($pengujiOrdered->get(1)?->NAMA) ?? '.............',
            'peng3' => ($roleTims['penguji_3'] ?? null)?->NAMA ?? ($pengujiOrdered->get(2)?->NAMA) ?? '.............',
        ];

        // Kolom Nama "Tim Penguji/Penilai" = daftar PENGUJI saja
        $pengujiNameList = $pengujiOrdered->map(fn($t) => $t->NAMA ?? '.............')->values()->all();

        // Ketua Sidang & Kaprodi (baca dari t_user_prodi untuk prodi ajuan)
        $ketuaSidang = $timByStatus['ketua sidang'] ?? null;
        $kaprodi = null;
        if ($ajuan->ID_PRODI) {
            $kaprodiUserId = DB::table('t_user_prodi')
                ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.ID')
                ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                ->where('t_user.STATUS_KAPRODI', 'y')
                ->value('t_user_prodi.ID_USER');
            if ($kaprodiUserId) {
                $kaprodi = TUser::find($kaprodiUserId);
            }
        }
        if (!$kaprodi) {
            $kaprodi = TUser::where('STATUS_KAPRODI', 'y')->first();
        }

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
            $tp->setValue('nama_ketua_pembimbing', $namaKetuaPembimbing);  // Header & Baris 1
            $tp->setValue('nama_pembimbing_i',     $namaKoPembimbing1);     // Header & Baris 2
            $tp->setValue('nama_pembimbing_ii',    $namaKoPembimbing2);     // Header & Baris 3
            // Baris 4-5 (Penguji) menggunakan ${nama_penguji_i} (muncul 2x)
            // -> diisi per-kemunculan di fillBaSk4Xml.
            $tp->setValue('nama_kaprodi',          $kaprodi ? $kaprodi->NAMA_LENGKAP : '.............');
            $tp->setValue('nip_kaprodi',           $kaprodi ? $kaprodi->NIP_NIM : '.............');
            $tp->setValue('tgl_sidang',            $tglFooter);
            $tp->setValue('nama_ketua_sidang',     $ketuaSidang?->NAMA ?? '.............');
            $tp->setValue('nip_ketua_sidang',      $ketuaSidang?->NIP ?? '.............');

            // Nilai per penilai (template: ${nilai_1}..${nilai_6}) + rata-rata baris 7
            for ($i = 1; $i <= 6; $i++) {
                $tp->setValue('nilai_' . $i, $nilaiRows[$i - 1] ?? '');
            }
            $tp->setValue('nilai_rata_rata', $rataRata);
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
            'nilai_rows'           => $nilaiRows,
            'rata_rata'            => $rataRata,
            'nama_penguji'         => $namaPenguji,
            'nama_penguji_list'    => $pengujiNameList,
            'nama_ketua_pembimbing' => $namaKetuaPembimbing,
            'nama_pembimbing_i'    => $namaKoPembimbing1,
            'nama_pembimbing_ii'   => $namaKoPembimbing2,
            'nama_kaprodi'         => $kaprodi ? $kaprodi->NAMA_LENGKAP : '',
            'nip_kaprodi'          => $kaprodi ? $kaprodi->NIP_NIM : '',
            'prodi'                => $this->namaProdiAjuan($ajuan),
        ]);

        // Tanda tangan per-baris tabel: 5 slot = Ketua, Ko-1, Ko-2, Penguji-1, Penguji-2.
        // Nama & tanda tangan diambil dari sumber yang SAMA per baris, dan tanda tangan
        // hanya dipasang bila baris tersebut benar-benar punya nama (bukan '.............'),
        // agar tidak ada gambar tanda tangan pada baris nama yang kosong.
        $listTims = [
            $roleTims['ketua_pembimbing'] ?? $pembimbingOrdered->get(0),
            $roleTims['ko_pembimbing_1']  ?? $pembimbingOrdered->get(1),
            $roleTims['ko_pembimbing_2']  ?? $pembimbingOrdered->get(2),
            $pengujiOrdered->get(0),
            $pengujiOrdered->get(1),
        ];
        $listNames = [
            $namaKetuaPembimbing,
            $namaKoPembimbing1,
            $namaKoPembimbing2,
            $pengujiNameList[0] ?? $namaPenguji['peng1'],
            $pengujiNameList[1] ?? $namaPenguji['peng2'],
        ];
        $signatures = [];
        foreach ($listTims as $i => $tim) {
            $name = trim((string) ($listNames[$i] ?? ''));
            $hasName = $name !== '' && $name !== '.............';
            $signatures[] = $hasName ? $this->signatureBinary($tim) : null;
        }
        $this->embedAllSkSignatures($docxPath, $signatures);

        // Isi ${signature} footer: Kaprodi (kiri) & Ketua Sidang (kanan)
        $this->fillSk4FooterSignatures(
            $docxPath,
            optional($kaprodi)->id,
            $ketuaSidang?->id_user_penilai ?? null
        );

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

        // Nilai kolom (3) per baris.
        // Template baru: ${nilai_1}..${nilai_6}; template lama: $(Nilai rata2) terpecah antar run.
        foreach ($data['nilai_rows'] as $idx => $v) {
            $val = $esc($v);
            $n = $idx + 1;
            $xml = str_replace('${nilai_' . $n . '}', $val, $xml);
            $before = $xml;
            $xml = $this->fuzzyReplaceOnce($xml, '$(Nilai rata2)', $val);
            if ($xml === $before) {
                $xml = $this->fuzzyReplaceOnce($xml, '${Nilai rata2}', $val);
            }
            if ($xml === $before) {
                $xml = preg_replace('/\$\{nilai_rata2\}/i', $val, $xml, 1) ?? $xml;
            }
        }

        // Nilai Rata-Rata (baris 7)
        $xml = str_replace('${nilai_rata_rata}', $esc($data['rata_rata'] ?? ''), $xml);
        // Baris 8-10 diisi manual KPPs -> kosongkan
        $xml = str_replace('${nilai_publikasi}', '', $xml);
        $xml = str_replace('${nilai_akhir}', '', $xml);
        $xml = str_replace('${indeks_akhir}', '', $xml);

        // Bersihkan sisa placeholder nilai
        for ($i = 1; $i <= 6; $i++) {
            $xml = str_replace('${nilai_' . $i . '}', '', $xml);
        }
        $xml = $this->fuzzyReplaceAll($xml, '$(Nilai rata2)', '');
        $xml = $this->fuzzyReplaceAll($xml, '${Nilai rata2}', '');
        $xml = preg_replace('/\$\{nilai_rata2\}/i', '', $xml);

        // Kolom Nama "Tim Penguji/Penilai": rows 1-3 = pembimbing (dari setValue header),
        // rows 4-6 = penguji (placeholder ${nama_penguji_i} bisa muncul 2-3x).
        $pl = array_values($data['nama_penguji_list'] ?? []);
        $escName = fn($v) => ($v ?? '') === '' ? '.............': $esc($v);
        $pengSlots = [
            $escName($pl[0] ?? ($data['nama_penguji']['peng1'] ?? '')),
            $escName($pl[1] ?? ($data['nama_penguji']['peng2'] ?? '')),
            $escName($pl[2] ?? ($data['nama_penguji']['peng3'] ?? '')),
        ];
        $pengIdx = 0;
        $xml = preg_replace_callback('/\$\{nama_penguji_i\}/', function ($m) use ($pengSlots, &$pengIdx) {
            $val = $pengSlots[$pengIdx] ?? $m[0];
            $pengIdx++;
            return $val;
        }, $xml);
        // Bersihkan sisa placeholder nama_penguji agar tidak bocor.
        $xml = preg_replace('/\$\{nama_penguji_i\}/', '', $xml);
        $xml = preg_replace('/\$\{nama_penguji_ii\}/', $pengSlots[1] ?? '', $xml);
        $xml = preg_replace('/\$\{nama_penguji_iii\}/', $pengSlots[2] ?? '', $xml);

        // Nama & NIP Kaprodi (mungkin terpecah antar XML run)
        if (!empty($data['nama_kaprodi'])) {
            $xml = $this->fuzzyReplaceOnce($xml, '${nama_kaprodi}', $esc($data['nama_kaprodi']));
            $xml = $this->fuzzyReplaceOnce($xml, '$(Nama kaprodi)', $esc($data['nama_kaprodi']));
        }
        if (!empty($data['nip_kaprodi'])) {
            $xml = $this->fuzzyReplaceOnce($xml, '${nip_kaprodi}', $esc($data['nip_kaprodi']));
            $xml = $this->fuzzyReplaceOnce($xml, '$(nip kaprodi)', $esc($data['nip_kaprodi']));
        }

        // Bersihkan sisa placeholder kaprodi
        $xml = preg_replace('/\$\{nama_kaprodi\}/', '', $xml);
        $xml = preg_replace('/\$\{nip_kaprodi\}/', '', $xml);
        $xml = preg_replace('/\$\([^)]*kaprodi[^)]*\)/i', '', $xml);

        // Nama prodi statis pada blok tanda tangan kaprodi -> NAMA_PRODI ajuan
        if (!empty($data['prodi'])) {
            $xml = $this->fuzzyReplaceAll($xml, 'Teknik Geofisika', $esc($data['prodi']));
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
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

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
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
                $nilaiRata2 = DB::table('v_ba_nilai_penilai')
                    ->where('ID_AJUAN', $ajuan->id)
                    ->where('ID_TIM_SIDANG', $tim->id)
                    ->value('NILAI_RATA2');
                if ($nilaiRata2 !== null) {
                    $rata = number_format((float) $nilaiRata2, 2);
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

        // Nama kolom "Tim Penguji" = daftar PENGUJI saja (penguji I, II, III, ...)
        $pengujiNames = [];
        foreach ($timByStatus as $status => $t) {
            if (preg_match('/^penguji\s+(i{1,3}|iv|v)$/', $status)) {
                $pengujiNames[] = $t->NAMA ?? '-';
            }
        }

        // Pembimbing utama & anggota berdasarkan pemetaan peran
        $ketuaPembimbing = $timByStatus[$roleStatuses['ketua_pembimbing']] ?? null;
        $anggotaPembimbing = collect([
            $timByStatus[$roleStatuses['ko_pembimbing_1']] ?? null,
            $timByStatus[$roleStatuses['ko_pembimbing_2']] ?? null,
        ])->filter()->values();

        $ketuaSidang = $timByStatus['ketua sidang'] ?? null;
        // Kaprodi: baca dari t_user_prodi untuk prodi ajuan
        $kaprodi = null;
        if ($ajuan->ID_PRODI) {
            $kaprodiUserId = DB::table('t_user_prodi')
                ->join('t_user', 't_user_prodi.ID_USER', '=', 't_user.ID')
                ->where('t_user_prodi.ID_PRODI', $ajuan->ID_PRODI)
                ->where('t_user.STATUS_KAPRODI', 'y')
                ->value('t_user_prodi.ID_USER');
            if ($kaprodiUserId) {
                $kaprodi = TUser::find($kaprodiUserId);
            }
        }
        if (!$kaprodi) {
            $kaprodi = TUser::where('STATUS_KAPRODI', 'y')->first();
        }

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

        // Resume catatan dari SEMUA penilai (t_penilaian di-group per ID_TIM_SIDANG).
        // Tiap penilai yang mengisi catatan menjadi satu baris list dengan awalan "- ".
        $catatanAll = TPenilaian::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->whereNotNull('CATATAN')
            ->where('CATATAN', '!=', '')
            ->orderBy('ID_TIM_SIDANG')
            ->get();
        $resumeCatatan = [];
        foreach ($catatanAll->groupBy('ID_TIM_SIDANG') as $rows) {
            $nama = trim((string) ($rows->first()->NAMA ?? ''));
            $cat  = $rows->pluck('CATATAN')
                ->map(fn($v) => str_replace(["\r\n", "\r"], "\n", trim((string) $v)))
                ->filter(fn($v) => $v !== '')
                ->unique()
                ->implode("\n");
            if ($cat !== '') {
                $resumeCatatan[] = '- ' . ($nama !== '' ? $nama . ': ' : '') . $cat;
            }
        }

        // Template
        $templatePath = base_path('template/SIDANG/BA sidang akhir TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template BA sidang akhir tidak ditemukan.'], 500);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Placeholder utuh (satu run)
            $tp->setValue('judul',             $ajuan->JUDUL ?? $judul->JUDUL ?? '');
            $namaMhs = $ajuan->NAMA_MHS ?? '-';
            // Nama mahasiswa ditampilkan dengan huruf kapital semua (GEDE)
            // sesuai permintaan format dokumen BA.
            if (is_string($namaMhs) && $namaMhs !== '-' && $namaMhs !== '') {
                $namaMhs = mb_strtoupper($namaMhs, 'UTF-8');
            }
            $tp->setValue('nama_mhs',          $namaMhs);
            $tp->setValue('nim',               $ajuan->NIM ?? '-');
            $tp->setValue('tgl_sidang',        $tglFooter);
            $tp->setValue('waktu',             $waktuSidang);
            $tp->setValue('nama_kaprodi',      $kaprodi ? $kaprodi->NAMA_LENGKAP : '.............');
            $tp->setValue('nama_ketua_sidang', $ketuaSidang?->NAMA ?? '.............');
            // Nama di tabel "Tim Penguji" (baris 1-3 = pembimbing, baris 4-6 = penguji).
            // Placeholder ${nama_penguji_i} baris 4-6 diisi per-kemunculan di fillSidangAkhirXml.
            $tp->setValue('nama_ketua_pembimbing', $ketuaPembimbing?->NAMA ?? '.............');
            $tp->setValue('nama_pembimbing_i',     $anggotaPembimbing->get(0)?->NAMA ?? '.............');
            $tp->setValue('nama_pembimbing_ii',    $anggotaPembimbing->get(1)?->NAMA ?? '.............');
            $tp->setValue('tgl_create_penilaian',  $tglCreate);
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
            'nilai_akhir'          => $indeks,
            'indeks'               => $indeks,
            'penguji_names'        => $pengujiNames,
            'waktu_selesai'        => $waktuSelesai,
            'tgl_create_penilaian' => $tglCreateFormat,
            'catatan'              => $resumeCatatan,
            'ip'                   => $ajuan->IP ?? null,
            'jml_jurnal_q1'        => $ajuan->JML_JURNAL_Q1 ?? null,
            'jml_jurnal_bereputasi1' => $ajuan->JML_JURNAL_BEREPUTASI1 ?? null,
'jml_jurnal_bereputasi2' => $ajuan->JML_JURNAL_BEREPUTASI2 ?? null,
                'rekomendasi_yudisium' => $ajuan->REKOMENDASI_YUDISIUM ?? null,
                'lulus'                => $this->isSidangAkhirLulus($ajuan, $indeks),
            ]);

        // Isi tanda tangan per baris tabel "Tim Penguji" (6 baris: Ketua Tim Pembimbing,
        // Ko-Pembimbing 1, Ko-Pembimbing 2, Penguji 1, Penguji 2, Penguji 3)
        $rowSignatures = [];
        foreach ($roleStatuses as $st) {
            $rowSignatures[] = $this->signatureBinary($timByStatus[$st] ?? null);
        }
        $this->embedAllSkSignatures($docxPath, $rowSignatures);

        // Isi ${signature_kaprodi} (kiri) dengan ttd kaprodi & ${signature_ketua_sidang} (kanan) dengan ttd ketua sidang
        $this->fillSignaturePlaceholder($docxPath, optional($kaprodi)->id, 'signature_kaprodi');
        $this->fillSignaturePlaceholder($docxPath, $ketuaSidang?->id_user_penilai, 'signature_ketua_sidang');

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Cetak Pedoman Memimpin Sidang (PED Ketua Sidang) untuk Sidang Terbuka/Tertutup.
     * Menggunakan template: PED KETUA SIDANG TEMPLATE.docx
     */
    public function cetakPedomanKetuaSidang(Request $request, $idJudul, $tahapan)
    {
        $tahapan = str_replace('_', ' ', $tahapan);

        $judul = TJudul::find($idJudul);
        if (!$judul) {
            abort(404, 'Judul tidak ditemukan.');
        }

        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            abort(404, 'Data judul/ajuan tidak ditemukan.');
        }

        $timSidang = TTimSidang::with('userPenilai')
            ->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        // Normalize: strip \r\n, trim whitespace
        $normalizeStatus = fn($s) => preg_replace('/[\r\n]+/', '', strtolower(trim($s ?? '')));

        // Helper: resolve nama dari userPenilai jika NAMA kosong/generic/NIP
        $resolveNama = function ($t) {
            if (!$t) return '-';
            $nama = trim(optional($t->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($t->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $t->id_user_penilai ?? null;
                if ($userId) {
                    $nama = trim(optional(TUser::find($userId))->NAMA_LENGKAP ?? '');
                }
            }
            return $nama ?: '-';
        };

        // Helper: resolve instansi dari userPenilai (bukan literal "NON ITB")
        $resolveInstansi = function ($t) {
            if (!$t) return '-';
            $user   = $t->userPenilai;
            $asal   = trim($user->ASAL_INSTANSI ?? '');
            $namaIn = trim($user->INSTANSI ?? '');
            if ($asal === 'NON ITB') {
                return $namaIn ?: '-';
            }
            return $asal !== '' ? $asal : ($namaIn ?: '-');
        };

        // Ketua Pembimbing (mendukung "Ketua Pembimbing" / "Pembimbing (Ketua)")
        $ketuaPembimbing = $timSidang->first(fn($t) =>
            in_array($normalizeStatus($t->status_tim_sidang), ['ketua pembimbing', 'pembimbing (ketua)'], true)
        );

        $urutanPembimbing = ['pembimbing i', 'pembimbing ii', 'pembimbing iii', 'pembimbing iv', 'pembimbing v'];
        $pembimbingBerurut = $timSidang
            ->filter(fn($t) => in_array($normalizeStatus($t->status_tim_sidang), $urutanPembimbing, true))
            ->sortBy(fn($t) => array_search($normalizeStatus($t->status_tim_sidang), $urutanPembimbing, true))
            ->values();

        // Bila tidak ada Ketua Pembimbing, Pembimbing I berperan sebagai ketua.
        if (!$ketuaPembimbing) {
            $ketuaPembimbing = $pembimbingBerurut->shift();
        }
        $pembimbingI  = $pembimbingBerurut->shift();
        $pembimbingII = $pembimbingBerurut->shift();

        $urutanPenguji = ['penguji i', 'penguji ii', 'penguji iii', 'penguji iv', 'penguji v'];
        $penguji = $timSidang
            ->filter(fn($t) => in_array($normalizeStatus($t->status_tim_sidang), $urutanPenguji, true))
            ->sortBy(fn($t) => array_search($normalizeStatus($t->status_tim_sidang), $urutanPenguji, true))
            ->values();

        // Nilai Akhir = rata-rata nilai penilai (bila sudah ada penilaian).
        $nilaiRata = [];
        if (!empty($ajuan->id)) {
            $timNilai = [$ketuaPembimbing, $pembimbingI, $pembimbingII, $penguji->get(0), $penguji->get(1), $penguji->get(2)];
            foreach ($timNilai as $t) {
                if (!$t) continue;
                $nilai = DB::table('v_ba_nilai_penilai')
                    ->where('ID_AJUAN', $ajuan->id)
                    ->where('ID_TIM_SIDANG', $t->id)
                    ->value('NILAI_RATA2');
                if ($nilai !== null) {
                    $nilaiRata[] = (float) $nilai;
                }
            }
        }
        $rata = count($nilaiRata) > 0
            ? number_format(array_sum($nilaiRata) / count($nilaiRata), 2)
            : null;

        $templatePath = base_path('template/SIDANG/PED KETUA SIDANG TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template PED Ketua Sidang tidak ditemukan.');
        }

        $tanggalSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';

        try {
            $tp = new TemplateProcessor($templatePath);

            $esc = fn($v) => htmlspecialchars((string) $v, ENT_XML1, 'UTF-8');

            $tp->setValue('nama_mhs',                    $esc($ajuan->NAMA_MHS ?? '-'));
            $tp->setValue('nim',                         $esc($ajuan->NIM ?? '-'));
            $tp->setValue('judul',                       $esc($ajuan->JUDUL ?? $judul->JUDUL ?? '-'));
            $tp->setValue('tanggal_sidang',              $esc($tanggalSidang));

            $tp->setValue('nama_ketua_pembimbing',       $esc($resolveNama($ketuaPembimbing)));
            $tp->setValue('instansi_ketua_pembimbing',   $esc($resolveInstansi($ketuaPembimbing)));
            $tp->setValue('nama_pembimbing_i',           $esc($resolveNama($pembimbingI)));
            $tp->setValue('instansi_pembimbing_i',       $esc($resolveInstansi($pembimbingI)));
            $tp->setValue('nama_pembimbing_ii',          $esc($resolveNama($pembimbingII)));
            $tp->setValue('instansi_pembimbing_ii',      $esc($resolveInstansi($pembimbingII)));

            $tp->setValue('nama_penguji_i',              $esc($resolveNama($penguji->get(0))));
            $tp->setValue('instansi_penguji_i',          $esc($resolveInstansi($penguji->get(0))));
            $tp->setValue('nama_penguji_ii',             $esc($resolveNama($penguji->get(1))));
            $tp->setValue('instansi_penguji_ii',         $esc($resolveInstansi($penguji->get(1))));
            $tp->setValue('nama_penguji_iii',            $esc($resolveNama($penguji->get(2))));
            $tp->setValue('instansi_penguji_iii',        $esc($resolveInstansi($penguji->get(2))));

            $tp->setValue('nilai_akhir',                 $esc($rata ?? 'â€¦â€¦â€¦â€¦â€¦'));
            $tp->setValue('predikat',                    $esc($rata !== null ? $this->calculateIndeksSidang((float) $rata) : '________'));
        } catch (\Exception $e) {
            abort(500, 'Gagal memproses template PED Ketua Sidang: ' . $e->getMessage());
        }

        $filename = 'PED_Ketua_Sidang_'
            . preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($ajuan->NIM ?? 'MHS'))
            . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        $tp->saveAs($docxPath);

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

        // Nilai kolom (3) per baris 1-6: placeholder ${nilai_rata2} muncul
        // berulang, jadi setiap kemunculan diisi sesuai penilai pada baris tersebut.
        foreach ($data['nilai_rows'] as $v) {
            $xml = $this->fuzzyReplaceOnce($xml, '${nilai_rata2}', $esc($v));
        }
        $xml = $this->fuzzyReplaceAll($xml, '${nilai_rata2}', '');

        // Nilai Rata-Rata (baris 7) & Nilai akhir Index (baris 8)
        // Handle multiple placeholder variants + split XML runs
        $rataValue = $esc($data['rata_rata'] ?? '');
        $xml = str_replace('${nilai_rata_rata}', $rataValue, $xml);
        $xml = str_replace('${nilai_akhir_rata_rata}', $rataValue, $xml);
        // Handle split-run placeholder: ${nilai_rata` + `2} or similar fragmentation
        $xml = preg_replace(
            '/(<w:t[^>]*>)\$\{nilai_rata<\/w:t><w:r[^>]*><w:rPr>.*?<\/w:rPr><w:t[^>]*>2\}(<\/w:t>)/s',
            '$1' . $rataValue . '$2',
            $xml
        );
        $xml = str_replace('${nilai_akhir_index}', $esc($data['indeks'] ?? ''), $xml);
        $xml = str_replace('${nilai_akhir}', $esc($data['nilai_akhir'] ?? ''), $xml);

        // Daftar nama tanda tangan: baris 4-6 (Penguji 1, 2, 3) diisi ${nama_penguji_i}
        // secara per-kemunculan (placeholder muncul 3x di baris 4-6).
        $pn = $data['penguji_names'] ?? [];
        $pengSlots = [
            $esc($pn[0] ?? '.............'),
            $esc($pn[1] ?? '.............'),
            $esc($pn[2] ?? '.............'),
        ];
        $pengIdx = 0;
        $xml = preg_replace_callback('/\$\{nama_penguji_i\}/', function ($m) use ($pengSlots, &$pengIdx) {
            $val = $pengSlots[$pengIdx] ?? $m[0];
            $pengIdx++;
            return $val;
        }, $xml);
        // Bersihkan sisa placeholder penguji agar tidak bocor ke output.
        $xml = preg_replace('/\$\{nama_penguji_i\}/', '', $xml);

        // Waktu selesai (typo template "$wkatu(selesai)")
        if ($data['waktu_selesai'] !== '') {
            $xml = str_replace('$wkatu(selesai)', $esc($data['waktu_selesai']), $xml);
        }

        // Tanggal create penilaian
        if ($data['tgl_create_penilaian'] !== '') {
            $xml = str_replace('tgl create penilaian', $esc($data['tgl_create_penilaian']), $xml);
        }

        // Resume catatan dari semua penilai: blok "Catatan:" di template berisi
        // "(resume catatan dari semua penilai)" + titik-titik. Ganti seluruh blok
        // (mulai paragraf resume s/d sebelum tabel Tim Penguji) dengan daftar
        // catatan "- <penilai>: <catatan>" â€” satu paragraf per baris.
        if (isset($data['catatan']) && is_array($data['catatan'])) {
            $xml = $this->fillSidangAkhirCatatan($xml, $data['catatan']);
        }

        // Capaian Akademik: isi placeholder dari t_ajuan_sidang
        // (IP, jurnal Q1, publikasi bereputasi-1 & bereputasi-2).
        $capaian = [
            '${ip}'                     => $data['ip'] ?? '---',
            '${jml_jurnal_q1}'          => $data['jml_jurnal_q1'] ?? '---',
            '${jml_jurnal_bereputasi1}' => $data['jml_jurnal_bereputasi1'] ?? '---',
            '${jml_jurnal_bereputasi2}' => $data['jml_jurnal_bereputasi2'] ?? '---',
        ];
        foreach ($capaian as $ph => $val) {
            $xml = str_replace($ph, $esc($val), $xml);
        }

        // Kotak keputusan LULUS / Tidak Lulus Sidang Doktor.
        // Placeholder sengaja dibedakan tiap baris (di-rename saat template:prepare):
        //   - ${kotak_lulus}        baris "LULUS Sidang Doktor"
        //   - ${kotak_yudisium}     baris "dan Rekomendasi Yudisium" (dicentang bila
        //                            ada rekomendasi yudisium)
        //   - ${kotak_tidak_lulus}  baris "Tidak Lulus Sidang Doktor"
        // Baris lulus dicentang bila lulus; baris tidak lulus dicentang bila
        // tidak lulus.
        $lulus    = !empty($data['lulus']);
        $mcentang = "\u{2611}"; // â˜‘
        $kosong   = "\u{2610}"; // â˜
        $adaRekommendasi = trim((string) ($data['rekomendasi_yudisium'] ?? '')) !== '';
        $xml = str_replace('${kotak_lulus}',       $lulus ? $mcentang : $kosong, $xml);
        $xml = str_replace('${kotak_yudisium}',    $adaRekommendasi ? $mcentang : $kosong, $xml);
        $xml = str_replace('${kotak_tidak_lulus}', $lulus ? $kosong : $mcentang, $xml);

        // Rekomendasi Yudisium: isi titik-titik pada baris "dan Rekomendasi
        // Yudisium: â€¦â€¦â€¦â€¦" dengan nilai REKOMENDASI_YUDISIUM dari t_ajuan_sidang.
        // Bila kosong, titik-titik asli (â€¦ â€¦ .) dipertahankan.
        $rekomendasiValue = trim((string) ($data['rekomendasi_yudisium'] ?? ''));
        $rekomendasiFill  = $rekomendasiValue !== '' ? $rekomendasiValue : 'â€¦â€¦â€¦â€¦â€¦â€¦â€¦â€¦.';
        $xml = str_replace('${rekomendasi_yudisium}', $esc($rekomendasiFill), $xml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Isi blok "Catatan:" pada BA Sidang Doktor.
     * Template berisi teks "(resume catatan dari semua penilai)" & titik-titik.
     * Blok diganti dengan satu paragraf per baris catatan.
     */
    private function fillSidangAkhirCatatan(string $xml, array $lines): string
    {
        $marker = '(resume catatan dari semua penilai)';
        $pos = strpos($xml, $marker);
        if ($pos === false) {
            return $xml;
        }

        // Awal paragraf yang memuat marker ("<w:p " sebelum posisi marker)
        $pStart = strrpos(substr($xml, 0, $pos), '<w:p ');
        if ($pStart === false) {
            return $xml;
        }

        // Akhir blok: tabel Tim Penguji yang mengikuti blok catatan
        $tblPos = strpos($xml, '<w:tbl>', $pos);
        if ($tblPos === false) {
            return $xml;
        }

        $paragraphs = $this->buildSidangAkhirCatatanParagraphs($lines);

        return substr($xml, 0, $pStart) . $paragraphs . substr($xml, $tblPos);
    }

    /**
     * Bangun XML paragraf (satu per baris) untuk daftar catatan pada BA Sidang Doktor.
     * Format paragraf meniru paragraf BodyText pada template (bold, hitam, 11pt).
     */
    private function buildSidangAkhirCatatanParagraphs(array $lines): string
    {
        $rPr = '<w:rPr><w:b/><w:color w:val="000000" w:themeColor="text1"/>'
             . '<w:sz w:val="22"/><w:lang w:val="fi-FI"/></w:rPr>';
        $pPr = '<w:pPr><w:pStyle w:val="BodyText"/>'
             . '<w:spacing w:before="40" w:after="40" w:line="400" w:lineRule="atLeast"/>'
             . '<w:ind w:left="0"/>' . $rPr . '</w:pPr>';

        $out = '';
        foreach ($lines as $line) {
            $line = str_replace(["\r\n", "\r"], "\n", (string) $line);
            foreach (preg_split('/\n+/', $line) as $sub) {
                $sub = trim($sub);
                if ($sub === '') continue;
                $esc = htmlspecialchars($sub, ENT_XML1, 'UTF-8');
                $out .= '<w:p>'
                      . $pPr
                      . '<w:r>' . $rPr . '<w:t xml:space="preserve">' . $esc . '</w:t></w:r>'
                      . '</w:p>';
            }
        }

        return $out;
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
     * Penanda kotak "LULUS Sidang Doktor" pada BA Sidang Akhir.
     * Lulus bila indeks bukan Mengulang, atau STATUS_LULUS ajuan tidak
     * berisi penanda tidak lulus ('tidak lulus' / 'Tidak layak').
     */
    private function isSidangAkhirLulus($ajuan, string $indeks): bool
    {
        if ($indeks !== '' && $indeks !== 'Mengulang') {
            return true;
        }
        $status = strtolower((string) ($ajuan->STATUS_LULUS ?? ''));
        return $status !== '' && $status !== 'mengulang'
            && !in_array($status, ['tidak lulus', 'tidak layak'], true);
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

    /**
     * Indeks Nilai Huruf pada Lembar Penguji Sidang Magister prodi 222.
     * Dihitung dari Nilai Angka (NA), baik per kriteria maupun nilai rata-rata:
     *   A        jika NA >= 4,5
     *   AB       jika 4,0 <= NA < 4,5
     *   B        jika 3,5 <= NA < 4,0
     *   BC       jika 3,0 <= NA < 3,5
     *   C        jika 2,5 <= NA < 3,0
     *   Mengulang jika NA < 2,5
     */
    private function calculateIndeksNilai222($na): string
    {
        if ($na === null || $na === '' || !is_numeric($na)) {
            return '';
        }

        $score = (float) $na;
        if ($score >= 4.5) {
            return 'A';
        }
        if ($score >= 4.0) {
            return 'AB';
        }
        if ($score >= 3.5) {
            return 'B';
        }
        if ($score >= 3.0) {
            return 'BC';
        }
        if ($score >= 2.5) {
            return 'C';
        }
        return 'Mengulang';
    }

    public function suratKesediaanPenelaah(Request $request, $idJudul, $tahapan)
    {
        $judul = TJudul::find($idJudul);
        if (!$judul) {
            abort(404, 'Judul tidak ditemukan.');
        }

        $tahapan = str_replace('_', ' ', $tahapan);
        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            abort(404, 'Data judul/ajuan tidak ditemukan.');
        }

        $timSidang = TTimSidang::with('userPenilai')->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        // Tampilkan semua pembimbing (Ketua Pembimbing, Pembimbing I, II, III)
        $pembimbings = $timSidang->filter(function ($tim) {
            $status = strtolower(trim($tim->status_tim_sidang ?? ''));
            return str_contains($status, 'pembimbing');
        })->values();
        // Resolve nama dari userPenilai jika kolom NAMA kosong/generic
        $pembimbings = $pembimbings->map(function ($tim) {
            $nama = trim(optional($tim->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($tim->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $tim->id_user_penilai ?? null;
                if ($userId) {
                    $user = \App\Models\TUser::find($userId);
                    $nama = trim($user->NAMA_LENGKAP ?? '');
                }
            }
            $tim->NAMA = $nama ?: '-';
            return $tim;
        })->values();
        if ($pembimbings->isEmpty()) {
            $pembimbingNames = collect(['-']);
        } else {
            $pembimbingNames = $pembimbings->pluck('NAMA')->values();
        }

        $pengujis = $timSidang->filter(function ($tim) {
            $status = strtolower(trim($tim->status_tim_sidang ?? ''));
            return str_contains($status, 'penguji');
        })->values();

        // Tampilkan nama instansi, bukan "non ITB"
        $pengujiList = $pengujis->map(function ($t) {
            // Resolve nama: userPenilai->NAMA_LENGKAP > NAMA > TUser::find
            $nama = trim(optional($t->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['penguji', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($t->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['penguji', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $t->id_user_penilai ?? null;
                if ($userId) {
                    $user = \App\Models\TUser::find($userId);
                    $nama = trim($user->NAMA_LENGKAP ?? '');
                }
            }
            // Tampilkan nama instansi (bukan literal "NON ITB")
            $asal = trim(optional($t->userPenilai)->ASAL_INSTANSI ?? '');
            $instansi = '';
            if ($asal === 'NON ITB') {
                $instansi = trim(optional($t->userPenilai)->INSTANSI ?? '');
            } elseif ($asal === 'ITB') {
                $instansi = 'ITB';
            }
            return [
                'nama' => $nama ?: '-',
                'institusi' => $instansi,
            ];
        })->values();

        // pembimbingNames sudah diinisialisasi di atas bersamaan dengan filter pembimbing

        $wda = TUser::where('STATUS_WDA', 'y')->first();
        if (!$wda) {
            $wda = TUser::where('STATUS_DEKAN', 'y')->first();
        }

        $prodi = $this->namaProdiAjuan($ajuan);

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
            'no_surat_penelaah' => $ajuan->NO_SURAT_PENELAAH ?? '-',
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
            'dari_tabel_t_user_status_dekan_y' => $wda->NAMA_LENGKAP ?? '',
            'nama_wda' => $wda->NAMA_LENGKAP ?? '',
            'nip' => $wda->NIP_NIM ?? '',
            'nip_wda' => $wda->NIP_NIM ?? '',
            'email' => $ajuan->EMAIL_SURAT ?? $wda->EMAIL ?? '',
        ];
        
        $this->replaceInDocx($docxPath, $replacements);

        // Hapus tandatangan (nama + NIP penandatangan) dari template
        $zipSig = new \ZipArchive();
        if ($zipSig->open($docxPath) === true) {
            $xml = $zipSig->getFromName('word/document.xml');
            
            // Hapus baris nama penandatangan (e.g. "Administrator M.T.")
            $xml = $this->removeSignatureBlock($xml);
            
            // Replace hardcoded email dengan email dari form jadwal (email_surat), fallback ke email dekan/wda
            $emailSurat = $ajuan->EMAIL_SURAT ?? '';
            $emailFallback = $emailSurat ?: ($wda->EMAIL ?? '');
            if ($emailFallback) {
                $xml = str_replace('sitijenab@fttm.itb.ac.id', $emailFallback, $xml);
                $xml = str_replace('lilik@itb.ac.id', $emailFallback, $xml);
            }
            
            $zipSig->deleteName('word/document.xml');
            $zipSig->addFromString('word/document.xml', $xml);
            $zipSig->close();
        }

        // Handle penguji and pembimbing separately with direct XML manipulation
        $this->handlePengujiAndPembimbingInXml($docxPath, $pembimbingNames, $pengujiList->toArray(), $prodi);

        // Isi ${signature} dengan tanda tangan wda dari database
        $this->fillSignaturePlaceholder($docxPath, optional($wda)->id);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }
    
    /**
     * Helper: Format penguji name with instansi (Item 24)
     */
    private function pengujiWithInstansi($penguji): string
    {
        if (!$penguji) return '-';
        $nama = trim($penguji->NAMA ?? '');
        if ($nama === '' || $nama === 'Penguji') return '-';
        
        // Get instansi from userPenilai relationship
        $instansi = '';
        if ($penguji->userPenilai) {
            $asal = trim($penguji->userPenilai->ASAL_INSTANSI ?? '');
            if ($asal === 'NON ITB') {
                $instansi = trim($penguji->userPenilai->INSTANSI ?? '');
            } elseif ($asal === 'ITB') {
                $instansi = 'ITB';
            }
        }
        
        return $nama . ($instansi ? ' (' . $instansi . ')' : '');
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
        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            abort(404, 'Data judul/ajuan tidak ditemukan.');
        }

        $timSidang = TTimSidang::where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->get();

        $t = strtolower($tahapan);
        $isSidang = $t === 'tahap iv'
            || str_contains($t, 'sidang')
            || str_contains($t, 'terbuka')
            || str_contains($t, 'tertutup');

        $isS2 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S2';
        // S2 Tesis I / Tesis II â†’ gunakan template undangan.docx (undangan TEMPLATE.docx)
        $isTesis = $t === 'tesis i' || $t === 'tesis ii' || $isS2;

        if ($isTesis) {
            return $this->cetakUndanganS2Tesis($judul, $ajuan, $timSidang, $tahapan);
        }

        if ($isSidang) {
            return $this->cetakUndanganSidang($judul, $ajuan, $timSidang);
        }

        return $this->cetakUndanganKemajuan($judul, $ajuan, $timSidang, $tahapan);
    }

    /**
     * Cetak undangan sidang Tesis I / Tesis II (S2).
     * Menggunakan template: template/undangan TEMPLATE.docx
     * Data pembimbing dan penguji disesuaikan secara dinamis:
     * - "Kepada Yth." hanya memuat dosen yang sebenarnya menjadi tim sidang.
     * - Baris Pembimbing hanya menampilkan pembimbing yang ada (1, 2, atau 3).
     * - Baris Penguji hanya menampilkan penguji yang ada (1 atau 2).
     */
    private function cetakUndanganS2Tesis($judul, $ajuan, $timSidang, string $tahapan)
    {
        $templatePath = base_path('template/undangan TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            abort(500, 'Template undangan S2 tidak ditemukan. Jalankan php artisan template:prepare terlebih dahulu.');
        }

        // Pastikan timSidang terisi, fallback jika filter tahapan sedikit berbeda
        if ($timSidang->isEmpty()) {
            $timSidang = TTimSidang::where('id_judul', $judul->ID_JUDUL ?? $ajuan->ID_JUDUL)
                ->whereRaw('LOWER(TRIM(tahapan_sidang)) = ?', [strtolower(trim($tahapan))])
                ->get();
        }
        if ($timSidang->isEmpty()) {
            $timSidang = TTimSidang::where('id_judul', $judul->ID_JUDUL ?? $ajuan->ID_JUDUL)->get();
        }

        $normalizeStatus = fn($s) => preg_replace('/[\r\n]+/', '', strtolower(trim($s ?? '')));

        $resolveNama = function ($t) {
            if (!$t) return '-';
            $nama = trim(optional($t->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($t->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $t->id_user_penilai ?? null;
                if ($userId) {
                    $user = \App\Models\TUser::find($userId);
                    $nama = trim($user->NAMA_LENGKAP ?? '');
                }
            }
            return $nama ?: '-';
        };

        // 1. Pembimbing (urutkan ketua pembimbing / pembimbing 1 dulu, lalu pembimbing 2, dst)
        $pembimbings = $timSidang->filter(function ($t) use ($normalizeStatus) {
            return str_contains($normalizeStatus($t->status_tim_sidang), 'pembimbing');
        })->sort(function ($a, $b) use ($normalizeStatus) {
            $stA = $normalizeStatus($a->status_tim_sidang);
            $stB = $normalizeStatus($b->status_tim_sidang);
            $rank = function ($st, $urutan) {
                if (str_contains($st, 'ketua')) return 1;
                if (preg_match('/pembimbing\s*(i|1)\b/', $st)) return 2;
                if (preg_match('/pembimbing\s*(ii|2)\b/', $st)) return 3;
                if (preg_match('/pembimbing\s*(iii|3)\b/', $st)) return 4;
                return 10 + (int) $urutan;
            };
            return $rank($stA, $a->URUTAN ?? 0) <=> $rank($stB, $b->URUTAN ?? 0);
        })->values();

        // 2. Ketua Sidang
        $ketua = $timSidang->first(function ($t) use ($normalizeStatus) {
            return str_contains($normalizeStatus($t->status_tim_sidang), 'ketua sidang');
        });

        // 3. Penguji (jangan dobel jika ketua sidang memiliki status lain)
        $pengujis = $timSidang->filter(function ($t) use ($normalizeStatus, $ketua) {
            if ($ketua && $t->id === $ketua->id) {
                return false;
            }
            return str_contains($normalizeStatus($t->status_tim_sidang), 'penguji');
        })->sort(function ($a, $b) use ($normalizeStatus) {
            $stA = $normalizeStatus($a->status_tim_sidang);
            $stB = $normalizeStatus($b->status_tim_sidang);
            $rank = function ($st, $urutan) {
                if (preg_match('/penguji\s*(i|1)\b/', $st)) return 1;
                if (preg_match('/penguji\s*(ii|2)\b/', $st)) return 2;
                if (preg_match('/penguji\s*(iii|3)\b/', $st)) return 3;
                if (preg_match('/penguji\s*(iv|4)\b/', $st)) return 4;
                return 10 + (int) $urutan;
            };
            return $rank($stA, $a->URUTAN ?? 0) <=> $rank($stB, $b->URUTAN ?? 0);
        })->values();

        $pembimbingNames = $pembimbings->map($resolveNama)->filter(fn($n) => $n !== '-' && $n !== '')->values()->all();
        $ketuaSidangName = $ketua ? $resolveNama($ketua) : null;
        if ($ketuaSidangName === '-') $ketuaSidangName = null;
        $pengujiNames = $pengujis->map($resolveNama)->filter(fn($n) => $n !== '-' && $n !== '')->values()->all();

        // Daftar penerima undangan pada blok "Kepada Yth."
        $kepadaYthList = [];
        foreach ($pembimbingNames as $pName) {
            if (!in_array($pName, $kepadaYthList)) {
                $kepadaYthList[] = $pName;
            }
        }
        if ($ketuaSidangName && !in_array($ketuaSidangName, $kepadaYthList)) {
            $kepadaYthList[] = $ketuaSidangName;
        }
        foreach ($pengujiNames as $pgName) {
            if (!in_array($pgName, $kepadaYthList)) {
                $kepadaYthList[] = $pgName;
            }
        }

        // Data kaprodi dengan fallback rumpun S3/321
        $kaprodi = \App\Models\TUser::where('STATUS_KAPRODI', 'y')
            ->where('KODE_PRODI', $ajuan->KODE_PRODI ?? '')
            ->first();
        if (!$kaprodi && !empty($ajuan->KODE_PRODI)) {
            $s3Prodi = '3' . substr((string) $ajuan->KODE_PRODI, 1);
            $kaprodi = \App\Models\TUser::where('KODE_PRODI', $s3Prodi)
                ->where('STATUS_KAPRODI', 'y')
                ->first();
        }
        if (!$kaprodi) {
            $kaprodi = \App\Models\TUser::where('STATUS_KAPRODI', 'y')->first();
        }

        $tglSidang = $ajuan->tgl_sidang
            ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y')
            : '-';

        $waktuMulai = $ajuan->waktu_sidang
            ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H.i')
            : '-';

        $tglSuratUndangan = $ajuan->TGL_UNDANGAN
            ? \Carbon\Carbon::parse($ajuan->TGL_UNDANGAN)->translatedFormat('d F Y')
            : ($ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('d F Y') : '-');

        $namaProdi = $ajuan->NAMA_PRODI ?? '-';
        $noSuratUndangan = $ajuan->NO_UNDANGAN ?? '-';

        $prodiSurat = \App\Models\TProdi::where('KODE_PRODI', $ajuan->KODE_PRODI ?? '')->first()
            ?? ($ajuan->ID_PRODI ? \App\Models\TProdi::find($ajuan->ID_PRODI) : null);
        $alamatProdi = $prodiSurat?->ALAMAT ?? '';
        $emailProdi = $prodiSurat?->EMAIL ?? '';

        $filename = 'Undangan_' . str_replace(' ', '_', $tahapan) . '_'
            . str_replace(' ', '_', $ajuan->NAMA_MHS ?? '') . '_'
            . ($ajuan->NIM ?? '') . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);

        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }

        copy($templatePath, $docxPath);

        // Manipulasi struktur XML: sesuaikan jumlah baris Kepada Yth, Pembimbing, dan Penguji
        try {
            $zip = new \ZipArchive();
            if ($zip->open($docxPath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $xpath = new \DOMXPath($dom);
                $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                // 1. Sesuaikan blok "Kepada Yth."
                $ythNode = null;
                foreach ($xpath->query('//w:p') as $p) {
                    if (str_contains($p->textContent, 'Kepada Yth')) {
                        $ythNode = $p;
                        break;
                    }
                }

                if ($ythNode) {
                    $recipientPlaceholders = [
                        '${nama_ketua_pembimbing}',
                        '${nama_pembimbing_i}',
                        '${nama_pembimbing_ii}',
                        '${nama_ketua_sidang}',
                        '${nama_penguji_i}',
                        '${nama_penguji_ii}',
                    ];
                    $targetParas = [];
                    $curr = $ythNode->nextSibling;
                    while ($curr) {
                        if ($curr->nodeType === XML_ELEMENT_NODE && $curr->localName === 'p') {
                            $pTxt = $curr->textContent;
                            $hasPh = false;
                            foreach ($recipientPlaceholders as $ph) {
                                if (str_contains($pTxt, $ph)) {
                                    $hasPh = true;
                                    break;
                                }
                            }
                            if ($hasPh) {
                                $targetParas[] = $curr;
                            } else {
                                break;
                            }
                        }
                        $curr = $curr->nextSibling;
                    }

                    $templatePara = !empty($targetParas) ? $targetParas[0]->cloneNode(true) : null;
                    $insertAfter = $ythNode;

                    foreach ($targetParas as $tp) {
                        $tp->parentNode->removeChild($tp);
                    }

                    foreach ($kepadaYthList as $rName) {
                        if ($templatePara) {
                            $newP = $templatePara->cloneNode(true);
                            $tNodes = $xpath->query('.//w:t', $newP);
                            if ($tNodes->length > 0) {
                                $tNodes->item(0)->nodeValue = $rName;
                                for ($k = 1; $k < $tNodes->length; $k++) {
                                    $tNodes->item($k)->nodeValue = '';
                                }
                            }
                        } else {
                            $newP = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:p');
                            $r = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
                            $t = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t', htmlspecialchars($rName));
                            $r->appendChild($t);
                            $newP->appendChild($r);
                        }
                        $insertAfter->parentNode->insertBefore($newP, $insertAfter->nextSibling);
                        $insertAfter = $newP;
                    }
                }

                // 2. Sesuaikan Pembimbing, Ketua Sidang, dan Penguji di badan surat
                $bodyParas = $xpath->query('//w:p');
                $pembimbingParas = [];
                $ketuaSidangPara = null;
                $pengujiParas = [];

                foreach ($bodyParas as $p) {
                    $txt = $p->textContent;
                    if (str_contains($txt, 'Pembimbing:') && str_contains($txt, '${nama_ketua_pembimbing}')) {
                        $pembimbingParas[0] = $p;
                    } elseif (str_contains($txt, '${nama_pembimbing_i}') && preg_match('/^\s*2\./', $txt)) {
                        $pembimbingParas[1] = $p;
                    } elseif (str_contains($txt, '${nama_pembimbing_ii}') && preg_match('/^\s*3\./', $txt)) {
                        $pembimbingParas[2] = $p;
                    } elseif (str_contains($txt, 'Ketua Sidang:') && str_contains($txt, '${nama_ketua_sidang}')) {
                        $ketuaSidangPara = $p;
                    } elseif (str_contains($txt, 'Penguji:') && str_contains($txt, '${nama_penguji_i}')) {
                        $pengujiParas[0] = $p;
                    } elseif (str_contains($txt, '${nama_penguji_ii}') && preg_match('/^\s*2\./', $txt)) {
                        $pengujiParas[1] = $p;
                    }
                }

                // A) Pembimbing: tampilkan hanya baris pembimbing yang ada
                $pCount = count($pembimbingNames);
                if ($pCount === 0) {
                    $this->replaceTextInPara($xpath, $pembimbingParas[0] ?? null, '${nama_ketua_pembimbing}', '-');
                    if (isset($pembimbingParas[1])) $pembimbingParas[1]->parentNode->removeChild($pembimbingParas[1]);
                    if (isset($pembimbingParas[2])) $pembimbingParas[2]->parentNode->removeChild($pembimbingParas[2]);
                } elseif ($pCount === 1) {
                    $this->replaceTextInPara($xpath, $pembimbingParas[0] ?? null, '${nama_ketua_pembimbing}', $pembimbingNames[0]);
                    if (isset($pembimbingParas[1])) $pembimbingParas[1]->parentNode->removeChild($pembimbingParas[1]);
                    if (isset($pembimbingParas[2])) $pembimbingParas[2]->parentNode->removeChild($pembimbingParas[2]);
                } elseif ($pCount === 2) {
                    $this->replaceTextInPara($xpath, $pembimbingParas[0] ?? null, '${nama_ketua_pembimbing}', $pembimbingNames[0]);
                    $this->replaceTextInPara($xpath, $pembimbingParas[1] ?? null, '${nama_pembimbing_i}', $pembimbingNames[1]);
                    if (isset($pembimbingParas[2])) $pembimbingParas[2]->parentNode->removeChild($pembimbingParas[2]);
                } else {
                    $this->replaceTextInPara($xpath, $pembimbingParas[0] ?? null, '${nama_ketua_pembimbing}', $pembimbingNames[0]);
                    $this->replaceTextInPara($xpath, $pembimbingParas[1] ?? null, '${nama_pembimbing_i}', $pembimbingNames[1]);
                    $this->replaceTextInPara($xpath, $pembimbingParas[2] ?? null, '${nama_pembimbing_ii}', $pembimbingNames[2]);
                }

                // B) Ketua Sidang
                if ($ketuaSidangPara) {
                    if ($ketuaSidangName) {
                        $this->replaceTextInPara($xpath, $ketuaSidangPara, '${nama_ketua_sidang}', $ketuaSidangName);
                    } else {
                        $ketuaSidangPara->parentNode->removeChild($ketuaSidangPara);
                    }
                }

                // C) Penguji: tampilkan hanya baris penguji yang ada
                $pgCount = count($pengujiNames);
                if ($pgCount === 0) {
                    $this->replaceTextInPara($xpath, $pengujiParas[0] ?? null, '1. ${nama_penguji_i}', '-');
                    $this->replaceTextInPara($xpath, $pengujiParas[0] ?? null, '${nama_penguji_i}', '-');
                    if (isset($pengujiParas[1])) $pengujiParas[1]->parentNode->removeChild($pengujiParas[1]);
                } elseif ($pgCount === 1) {
                    $this->replaceTextInPara($xpath, $pengujiParas[0] ?? null, '${nama_penguji_i}', $pengujiNames[0]);
                    if (isset($pengujiParas[1])) $pengujiParas[1]->parentNode->removeChild($pengujiParas[1]);
                } else {
                    $this->replaceTextInPara($xpath, $pengujiParas[0] ?? null, '${nama_penguji_i}', $pengujiNames[0]);
                    $this->replaceTextInPara($xpath, $pengujiParas[1] ?? null, '${nama_penguji_ii}', $pengujiNames[1]);
                }

                // Ubah Ketua Program Studi Sarjana -> Magister untuk S2
                foreach ($xpath->query('//w:p') as $p) {
                    if (str_contains($p->textContent, 'Ketua Program Studi Sarjana')) {
                        $this->replaceTextInPara($xpath, $p, 'Sarjana', 'Magister');
                    }
                }

                $newXml = $dom->saveXML();
                $zip->addFromString('word/document.xml', $newXml);
                $zip->close();
            }
        } catch (\Throwable $e) {
            \Log::error('Gagal memproses struktur undangan S2: ' . $e->getMessage());
        }

        // Isi sisa metadata via TemplateProcessor
        $replacements = [
            'no_surat_undangan'    => $noSuratUndangan,
            'tgl_surat_undangan'   => $tglSuratUndangan,
            'nama_prodi'           => $namaProdi,
            'alamat_prodi'         => $alamatProdi,
            'email_prodi'          => $emailProdi,
            'nama_mhs'             => $ajuan->NAMA_MHS ?? '-',
            'nim'                  => $ajuan->NIM ?? '-',
            'judul'                => $ajuan->JUDUL ?? $judul->JUDUL ?? '',
            'tgl_sidang'           => $tglSidang,
            'waktu'                => $waktuMulai,
            'ruang_sidang'         => $ajuan->ruang_sidang ?? $ajuan->RUANG_SIDANG ?? '-',
            'nama_kaprodi'         => $kaprodi?->NAMA_LENGKAP ?? '-',
            'nip_kaprodi'          => $kaprodi?->NIP_NIM ?? '-',
        ];

        try {
            $tp = new \PhpOffice\PhpWord\TemplateProcessor($docxPath);
            foreach ($replacements as $key => $value) {
                $tp->setValue($key, htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8'));
            }
            $tp->saveAs($docxPath);
        } catch (\Exception $e) {
            \Log::error('cetakUndanganS2Tesis TemplateProcessor error: ' . $e->getMessage());
            $this->replaceInDocx($docxPath, $replacements);
        }

        // Sisipkan tanda tangan kaprodi jika ada
        if ($kaprodi) {
            $this->fillSignaturePlaceholder($docxPath, $kaprodi->id, 'signature');
        }
        $this->removePlaceholderText($docxPath, 'signature');

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Helper untuk mengganti teks di dalam run <w:t> sebuah paragraf DOM.
     */
    private function replaceTextInPara(\DOMXPath $xpath, ?\DOMNode $para, string $search, string $replace): void
    {
        if (!$para) return;
        $found = false;
        foreach ($xpath->query('.//w:t', $para) as $t) {
            if (str_contains($t->nodeValue, $search)) {
                $t->nodeValue = str_replace($search, $replace, $t->nodeValue);
                $found = true;
            }
        }
        if (!$found && str_contains($para->textContent, $search)) {
            $tNodes = $xpath->query('.//w:t', $para);
            if ($tNodes->length > 0) {
                $full = '';
                foreach ($tNodes as $t) {
                    $full .= $t->nodeValue;
                }
                $full = str_replace($search, $replace, $full);
                $tNodes->item(0)->nodeValue = $full;
                for ($k = 1; $k < $tNodes->length; $k++) {
                    $tNodes->item($k)->nodeValue = '';
                }
            }
        }
    }

    private function cetakUndanganSidang($judul, $ajuan, $timSidang)
    {
        // Normalize: strip \r\n, trim whitespace
        $normalizeStatus = fn($s) => preg_replace('/[\r\n]+/', '', strtolower(trim($s ?? '')));

        // Helper: resolve nama dari userPenilai jika NAMA kosong/generic/NIP
        $resolveNama = function ($t) {
            if (!$t) return '-';
            $nama = trim(optional($t->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($t->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $t->id_user_penilai ?? null;
                if ($userId) {
                    $user = \App\Models\TUser::find($userId);
                    $nama = trim($user->NAMA_LENGKAP ?? '');
                }
            }
            return $nama ?: '-';
        };

        // Ketua Pembimbing (Ketua)
        $ketuaPembimbing = $timSidang
            ->filter(fn($t) => $normalizeStatus($t->status_tim_sidang) === 'ketua pembimbing')
            ->first();

        // Pembimbing Anggota (I, II, III)
        $pembimbingAnggota = $timSidang
            ->filter(fn($t) => preg_match('/^pembimbing\s+(i{1,3}|iv|v)$/i', $normalizeStatus($t->status_tim_sidang)))
            ->values();

        // Penguji (I, II, III)
        $pengujis = $timSidang
            ->filter(fn($t) => preg_match('/^penguji\s+(i{1,3}|iv|v)$/i', $normalizeStatus($t->status_tim_sidang)))
            ->values();

        // Ketua Sidang
        $ketua = $timSidang
            ->filter(fn($t) => $normalizeStatus($t->status_tim_sidang) === 'ketua sidang')
            ->first();

        $dekan = TUser::where('STATUS_DEKAN', 'y')->first();

        $prodi = $this->namaProdiAjuan($ajuan);

        $mulai = $ajuan->waktu_sidang;
        $selesai = $ajuan->waktu_selesai;
        $pukulPendahuluan = ($mulai && $selesai)
            ? \Carbon\Carbon::parse($mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($selesai)->format('H:i')
            : '-';

        $pukulDoktor = '-';
        if ($selesai) {
            $doktorStart = \Carbon\Carbon::parse($selesai);
            $pukulDoktor = $doktorStart->format('H:i') . ' â€“ ' . $doktorStart->copy()->addMinutes(150)->format('H:i');
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
            'nama ketua pembimbing' => $resolveNama($ketuaPembimbing) ?? '-',
            'nama pembimbing I' => $pembimbingAnggota->get(0) ? $resolveNama($pembimbingAnggota->get(0)) : '-',
            'nama pembimbing II' => $pembimbingAnggota->get(1) ? $resolveNama($pembimbingAnggota->get(1)) : '-',
            'nama penguji I' => $pengujis->get(0) ? $this->pengujiWithInstansi($pengujis->get(0)) : '-',
            'nama penguji II' => $pengujis->get(1) ? $this->pengujiWithInstansi($pengujis->get(1)) : '-',
            'nama penguji III' => $pengujis->get(2) ? $this->pengujiWithInstansi($pengujis->get(2)) : '-',
            'ketua sidang' => $resolveNama($ketua) ?? '-',
            'tgl sidang' => $tglSidang,
            'waktu_waktu_selesai' => $pukulPendahuluan,
            'ruang' => $tempat,
            'ruangan' => $tempat,
            'waktu selesai â€“ di tambah 2,5 jam' => $pukulDoktor,
            'nama user status dekan \'y\'' => $dekan->NAMA_LENGKAP ?? '',
            'nama user status dekan â€˜yâ€™' => $dekan->NAMA_LENGKAP ?? '',
            'dekan' => $dekan->NAMA_LENGKAP ?? '',
        ];
        
        $this->replaceInDocx($docxPath, $replacements);

        // Item 24: Hapus tandatangan (cap ITB + Administrator NIP) dari undangan sidang
        $xmlSig = new \ZipArchive();
        if ($xmlSig->open($docxPath) === true) {
            $xml = $xmlSig->getFromName('word/document.xml');
            $xml = $this->removeSignatureBlock($xml);
            $xmlSig->deleteName('word/document.xml');
            $xmlSig->addFromString('word/document.xml', $xml);
            $xmlSig->close();
        }

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

        // Normalize: strip \r\n, trim whitespace
        $normalizeStatus = fn($s) => preg_replace('/[\r\n]+/', '', strtolower(trim($s ?? '')));

        // Helper: resolve nama dari userPenilai jika NAMA kosong/generic/NIP
        $resolveNama = function ($t) {
            if (!$t) return '-';
            $nama = trim(optional($t->userPenilai)->NAMA_LENGKAP ?? '');
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $nama = trim($t->NAMA ?? '');
            }
            if ($nama === '' || in_array(strtolower($nama), ['pembimbing', 'penguji', 'ketua sidang', 'ketua pembimbing', 'dua_role']) || preg_match('/^\d{5,}$/', $nama)) {
                $userId = $t->id_user_penilai ?? null;
                if ($userId) {
                    $user = \App\Models\TUser::find($userId);
                    $nama = trim($user->NAMA_LENGKAP ?? '');
                }
            }
            return $nama ?: '-';
        };

        // Helper: resolve instansi dari userPenilai (bukan literal "NON ITB")
        $resolveInstansi = function ($t) {
            $asal = trim(optional($t->userPenilai)->ASAL_INSTANSI ?? '');
            if ($asal === 'NON ITB') {
                return trim(optional($t->userPenilai)->INSTANSI ?? '');
            } elseif ($asal === 'ITB') {
                return 'ITB';
            }
            return '';
        };

        // Ketua Sidang
        $ketua = $timSidang
            ->filter(fn($x) => $normalizeStatus($x->status_tim_sidang) === 'ketua sidang')
            ->first();

        // Pembimbing (Ketua + Anggota) - urutkan: Ketua Pembimbing dulu, lalu I, II, III
        $pembimbingKetua = $timSidang
            ->filter(fn($x) => $normalizeStatus($x->status_tim_sidang) === 'ketua pembimbing')
            ->first();
        $pembimbingAnggota = $timSidang
            ->filter(fn($x) => preg_match('/^pembimbing\s+(i{1,3}|iv|v)$/i', $normalizeStatus($x->status_tim_sidang)))
            ->values();

        // Penguji - urutkan: Penguji I, II, III
        $pengujis = $timSidang
            ->filter(fn($x) => preg_match('/^penguji\s+(i{1,3}|iv|v)$/i', $normalizeStatus($x->status_tim_sidang)))
            ->values();

        // KPPS members from t_kpps (filtered by prodi and active status)
        $kppsUsers = collect();
        $kodeProdi = $ajuan->KODE_PRODI ?? $judul->KODE_PRODI ?? null;
        if ($kodeProdi) {
            $kppsUsers = \App\Models\TKpps::where('KODE_PRODI', $kodeProdi)
                ->where('STATUS_AKTIF', 'AKTIF')
                ->where('NAMA', '!=', '')
                ->orderByRaw("CASE WHEN STATUS_TIM = 'Ketua' THEN 1 WHEN STATUS_TIM = 'Sekretaris' THEN 2 ELSE 3 END")
                ->orderBy('NAMA')
                ->get()
                ->unique('NAMA')
                ->values();
        }

        // Build rows
        $rows = [];
        if ($ketua) {
            $rows[] = ['nama' => $resolveNama($ketua), 'jabatan' => 'Ketua Sidang'];
        }
        // Ketua Pembimbing
        if ($pembimbingKetua) {
            $rows[] = ['nama' => $resolveNama($pembimbingKetua), 'jabatan' => 'Pembimbing (Ketua)'];
        }
        // Pembimbing Anggota (I, II, III)
        foreach ($pembimbingAnggota as $p) {
            $rows[] = ['nama' => $resolveNama($p), 'jabatan' => 'Pembimbing (Anggota)'];
        }
        // Penguji dengan instansi
        foreach ($pengujis as $p) {
            $inst = $resolveInstansi($p);
            $rows[] = ['nama' => $resolveNama($p), 'jabatan' => $inst ? "Penguji ($inst)" : 'Penguji'];
        }
        // KPPS (dari t_kpps)
        foreach ($kppsUsers as $ku) {
            $jabatan = 'Anggota KPPs';
            if ($ku->STATUS_TIM) {
                $statusTim = trim($ku->STATUS_TIM);
                if (strtolower($statusTim) === 'ketua') {
                    $jabatan = 'Ketua KPPs';
                } elseif (strtolower($statusTim) === 'sekretaris') {
                    $jabatan = 'Sekretaris KPPs';
                } else {
                    $jabatan = $statusTim . ' KPPs';
                }
            }
            $rows[] = ['nama' => $ku->NAMA, 'jabatan' => $jabatan];
        }

        // Build kepada paragraphs â€“ each row becomes a separate <w:p> so columns align properly
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
            ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i') . ' â€“ ' . \Carbon\Carbon::parse($ajuan->waktu_selesai)->format('H:i') . ' WIB'
            : '-';

        $prodi = $this->namaProdiAjuan($ajuan);

        $wda = TUser::where('STATUS_WDA', 'y')->first();
        if (!$wda) {
            $wda = TUser::where('STATUS_DEKAN', 'y')->first();
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

        // Nama tahapan pada template masih statis ("Seminar Kemajuan IV") â€”
        // ganti dinamis sesuai tahapan yang dicetak.
        $postSave = function ($docxPath) use ($kepadaParagraphs, $perihal, $seminar, $wda, $prodi) {
            $z = new \ZipArchive();
            if ($z->open($docxPath) === true) {
                $xml = $z->getFromName('word/document.xml');

                // Perihal: "Undangan Seminar Kemajuan IV" -> sesuai tahapan
                $xml = $this->fuzzyReplaceOnce(
                    $xml,
                    'Undangan Seminar Kemajuan IV',
                    'Undangan ' . htmlspecialchars($perihal, ENT_XML1, 'UTF-8')
                );

                // Isi undangan: "... Seminar Kemajuan IV (SK-IV) ..." -> sesuai tahapan
                $xml = $this->fuzzyReplaceOnce(
                    $xml,
                    'Seminar Kemajuan IV (SK-IV)',
                    htmlspecialchars($seminar, ENT_XML1, 'UTF-8')
                );

                // Nama prodi statis ("Program Doktor Teknik Geofisika") -> NAMA_PRODI ajuan
                if ($prodi !== '' && $prodi !== '-') {
                    $xml = $this->fuzzyReplaceAll($xml, 'Teknik Geofisika', htmlspecialchars($prodi, ENT_XML1, 'UTF-8'));
                }

                if ($kepadaParagraphs !== '') {
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
                }

                // Catatan: tandatangan di undangan di-handle oleh template placeholders
                // ${nama_wda} dan ${nip_wda} sudah di-replace oleh replaceInDocx()
                // TIDAK perlu removeSignatureBlock karena template perlu nama + NIP WDA

                $z->deleteName('word/document.xml');
                $z->addFromString('word/document.xml', $xml);
                $z->close();
            }

            // Isi ${signature} dengan tanda tangan WDA dari database
            $this->fillSignaturePlaceholder($docxPath, optional($wda)->id);
        };

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

            // Apply any postâ€‘save modifications (e.g., inserting "kepada" rows)
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
            
            // Placeholder nilai_rata2_dari_form_penilaian: urutan pertama = "Nilai Rata-rata"
            // (rata-rata keseluruhan), urutan berikutnya = baris "Penilai 1..N" per penilai
            $placeholderPatterns = [
                '/\$\{nilai_rata2_dari_form_penilaian\}/',
                '/\$\(nilai_rata2_dari_form_penilaian\)/',
                '/\$\{nilai rata2 dari form penilaian\}/',
                '/\$\(nilai rata2 dari form penilaian\)/',
            ];

            $safeRataRata = htmlspecialchars($rataRataKeseluruhan, ENT_XML1, 'UTF-8');
            foreach ($placeholderPatterns as $pattern) {
                $xml = preg_replace($pattern, $safeRataRata, $xml, 1);
            }

            // Replace individual penilai values - satu occurrence per penilai
            foreach ($penilaiAverages as $penilai) {
                $nilai = htmlspecialchars($penilai['rata_nilai'], ENT_XML1, 'UTF-8');
                foreach ($placeholderPatterns as $pattern) {
                    $xml = preg_replace($pattern, $nilai, $xml, 1);
                }
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
     * Pusatkan nilai di kolom "Nilai" pada tabel BA proposal.
     * Mencari cell yang berisi angka nilai (2.60, dll) dan menambahkan
     * center alignment pada run-nya.
     */
    private function centerValueCellsInBaProposal(string $docxPath): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        // Tambahkan center alignment pada <w:jc> untuk cell yang berisi nilai angka
        // Pola: <w:rPr>...</w:rPr> yang tidak memiliki <w:jc w:val="center">
        // Kita cari cell yang berisi nilai desimal (angka + titik + angka) sebagai nilai penilaian
        $xml = preg_replace_callback(
            '/<w:tc>(.*?)<w:r>([^<]*(?:<[^>]*>)*?)<w:t xml:space="preserve">(\d+\.\d+)<\/w:t>/s',
            function ($matches) {
                $cellContent = $matches[1];
                $runStart = $matches[2];
                $value = $matches[3];

                // Jika run belum punya center alignment, tambahkan
                if (strpos($runStart, 'w:jc') === false && strpos($cellContent, 'w:jc') === false) {
                    $jc = '<w:jc w:val="center"/>';
                    // Insert jc after w:rPr opening tag if exists
                    if (strpos($runStart, '<w:rPr>') !== false) {
                        $runStart = str_replace('<w:rPr>', '<w:rPr>' . $jc, $runStart);
                    } elseif (strpos($runStart, '<w:rPr ') !== false) {
                        $runStart = preg_replace('/(<w:rPr[^>]*>)/', '$1' . $jc, $runStart);
                    }
                }

                return '<w:tc>' . $cellContent . '<w:r>' . $runStart . '<w:t xml:space="preserve">' . $value . '</w:t>';
            },
            $xml
        );

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();
    }

    /**
     * Hapus blok tandatangan (nama penandatangan + NIP) dari XML.
     * Pola: paragraf yang berisi nama + "M.T." atau "NIP." di sekitar
     * bagian "a.n Dekan" / "Wakil Dekan Bidang Akademik"
     */
    private function removeSignatureBlock(string $xml): string
    {
        // Hapus baris "Administrator M.T." (nama penandatangan) - hanya literal, bukan placeholder
        $xml = preg_replace(
            '/<w:p[^>]*>\s*(?:<[^>]*>)*\s*Administrator\s*M\.T\..*?<\/w:p>/is',
            '',
            $xml
        );
        
        // Hapus baris "NIP. <angka>" (NIP penandatangan) - hanya literal angka, bukan ${nip_xxx}
        $xml = preg_replace(
            '/<w:p[^>]*>\s*(?:<[^>]*>)*\s*NIP\.\s*\d{5,20}\s*<\/w:p>/is',
            '',
            $xml
        );
        
        // Hapus baris yang hanya berisi angka NIP (e.g. "112000021") - hanya angka, bukan placeholder
        $xml = preg_replace(
            '/<w:p[^>]*>\s*(?:<[^>]*>)*\s*\d{5,20}\s*<\/w:p>/is',
            '',
            $xml
        );
        
        // Hapus baris "a.n Dekan" (jika ada)
        $xml = preg_replace(
            '/<w:p[^>]*>\s*(?:<[^>]*>)*\s*a\.n\s*Dekan.*?<\/w:p>/is',
            '',
            $xml
        );
        
        // Hapus baris "Wakil Dekan Bidang Akademik" (jika ada)
        $xml = preg_replace(
            '/<w:p[^>]*>\s*(?:<[^>]*>)*\s*Wakil\s+Dekan\s+Bidang\s+Akademik.*?<\/w:p>/is',
            '',
            $xml
        );
        
        return $xml;
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

            // Ganti sisa nama prodi statis pada template (mis. "Program Doktor Teknik Geofisika")
            // dengan NAMA_PRODI dari ajuan agar sesuai program studi mahasiswa.
            if ($prodi !== '') {
                $xml = $this->fuzzyReplaceAll($xml, 'Teknik Geofisika', htmlspecialchars($prodi, ENT_XML1, 'UTF-8'));
            }

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

    /**
     * Tandai opsi checkbox pada paragraf yang teksnya terpecah antar <w:r>.
     * Mencari paragraf yang teks polosnya cocok dengan $labelRegex, lalu
     * menyisipkan simbol checkbox di awal <w:t> pertama pada paragraf tersebut.
     */
    private function markSplitCheckboxOption(string $xml, string $labelRegex, string $mark): string
    {
        if ($mark === '') {
            return $xml;
        }

        return preg_replace_callback('/<w:p\b[^>]*>.*?<\/w:p>/us', function ($m) use ($labelRegex, $mark) {
            $para = $m[0];
            // Teks polos paragraf (tanpa tag XML), sudah di-decode entity
            $plain = html_entity_decode(strip_tags($para), ENT_QUOTES, 'UTF-8');
            $plain = preg_replace('/\s+/', ' ', $plain);

            if (preg_match($labelRegex, $plain) !== 1) {
                return $para;
            }

            // Jangan menandai dua kali (idempoten)
            if (str_contains($plain, '☑') || str_contains($plain, '☐')) {
                return $para;
            }

            // Sisipkan simbol di awal <w:t> pertama yang berisi teks (bukan yang kosong)
            return preg_replace('/(<w:t[^>]*>)(?=[^<])/', '$1' . $mark, $para, 1);
        }, $xml);
    }

    /**
     * Tandai checkbox/opsi LULUS & Status Publikasi pada semua template BA S2 (Tesis I & Tesis II).
     * Template memakai teks/shape (bukan ${placeholder}), sehingga ditandai via
     * penyisipan simbol ☐/☑ pada teks yang sesuai. Pola yang tidak ada di template
     * akan diabaikan aman (str_replace/preg_replace tidak cocok).
     */
    private function applyS2BaCheckboxes(string $docxPath, $statusLulus, array $publikasi): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName("word/document.xml");
        if ($xml === false) {
            $zip->close();
            return;
        }

        $sl = strtolower(trim((string) $statusLulus));
        $isLulus = str_contains($sl, 'lulus sidang magister')
            || in_array($sl, ['lulus', 'y', 'ya', '1', 'lulus sidang'], true);
        $isTidak = str_contains($sl, 'tidak lulus') || str_contains($sl, 'mengulang');

        $cb = fn(bool $on) => $on ? '☑ ' : '☐ ';

        // --- Status publikasi (Submitt / Under review / Publish) ---
        if (str_contains($xml, 'Status publikasi :')) {
            $sub = $cb(strtolower(trim((string) ($publikasi['Submitt'] ?? 't'))) === 'y');
            $und = $cb(strtolower(trim((string) ($publikasi['Under Review'] ?? 't'))) === 'y');
            $pub = $cb(strtolower(trim((string) ($publikasi['Publish'] ?? 't'))) === 'y');
            $xml = preg_replace(
                '/>Status publikasi :\s+Submitt\s+Under review\s+Publish</u',
                '>Status publikasi :        ' . $sub . 'Submitt                 ' . $und . 'Under review                 ' . $pub . 'Publish<',
                $xml
            );
        }

        // --- Opsi kelulusan: label teks terpecah antar <w:r> (LULUS / TIDAK LULUS Sidang Magister) ---
        // Template memecah "LULUS" + " " + "Sidang Magister" ke run berbeda, jadi
        // pencocokan harus berbasis teks paragraf, bukan string XML kontigu.
        $xml = $this->markSplitCheckboxOption($xml, '/^\s*LULUS\s+Sidang\s+Magister/iu', $cb($isLulus && !$isTidak));
        $xml = $this->markSplitCheckboxOption($xml, '/^\s*TIDAK\s+LULUS\s+Sidang\s+Magister/iu', $cb($isTidak));

        // --- Kombinasi LULUS / TIDAK LULUS dengan strike (BA Tesis I gaya) ---
        if (str_contains($xml, '>LULUS / </w:t>')) {
            if ($isTidak) {
                // Strike LULUS, un-strike TIDAK LULUS
                $xml = preg_replace(
                    '/<w:r([^>]*)><w:rPr>(.*?)<\/w:rPr><w:t>LULUS<\/w:t><\/w:r>/u',
                    '<w:r$1><w:rPr>$2<w:strike/></w:rPr><w:t>LULUS</w:t></w:r>',
                    $xml,
                    1
                );
                $xml = preg_replace(
                    '/<w:r([^>]*)><w:rPr><w:strike\/><w:lang w:val="sv-SE"\/><\/w:rPr><w:t>TIDAK LULUS<\/w:t><\/w:r>/u',
                    '<w:r$1><w:rPr><w:lang w:val="sv-SE"/></w:rPr><w:t>TIDAK LULUS</w:t></w:r>',
                    $xml,
                    1
                );
            } elseif ($isLulus) {
                // Strike TIDAK LULUS, un-strike LULUS
                $xml = preg_replace(
                    '/<w:r([^>]*)><w:rPr><w:strike\/><w:lang w:val="sv-SE"\/><\/w:rPr><w:t>TIDAK LULUS<\/w:t><\/w:r>/u',
                    '<w:r$1><w:rPr><w:strike/><w:lang w:val="sv-SE"/></w:rPr><w:t>TIDAK LULUS</w:t></w:r>',
                    $xml,
                    1
                );
                $xml = preg_replace(
                    '/<w:r([^>]*)><w:rPr>(.*?)<w:strike\/>(.*?)<\/w:rPr><w:t>LULUS<\/w:t><\/w:r>/u',
                    '<w:r$1><w:rPr>$2$3</w:rPr><w:t>LULUS</w:t></w:r>',
                    $xml,
                    1
                );
            }
        }

        $zip->deleteName("word/document.xml");
        $zip->addFromString("word/document.xml", $xml);
        $zip->close();
    }

    /**
     * Isi tabel tim penilai, keputusan LULUS/TIDAK LULUS, catatan perbaikan,
     * dan tabel daftar hadir pada BA Sidang TA 2 prodi 122.
     */
    private function postProcessS1Ba122Ta2(
        string $docxPath,
        $allTimSidang,
        array $penilaiAverages,
        string $statusLulus,
        string $indeks,
        array $catatanByNama
    ): void {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = false;
        if (!@$dom->loadXML($xml)) {
            $zip->close();
            return;
        }

        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

        $pembS1 = $allTimSidang->filter(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'))->values();
        $pengS1 = $allTimSidang->filter(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'penguji'))->values();
        $ketuaS1 = $allTimSidang->first(fn($t) => strtolower(trim($t->status_tim_sidang ?? '')) === 'ketua sidang');

        $roleSlots = [
            $pembS1->get(0),
            $pembS1->get(1),
            $pembS1->get(2),
            $ketuaS1,
            $pengS1->get(0),
            $pengS1->get(1),
        ];

        $avgByNama = [];
        foreach ($penilaiAverages as $pa) {
            $avgByNama[strtolower(trim((string) ($pa['nama'] ?? '')))] = $pa['rata_nilai'] ?? '';
        }

        $setCellText = function (\DOMElement $tc, string $text) use ($xp, $dom, $wNs): void {
            $p = $xp->query('./w:p', $tc)->item(0);
            if (!$p) {
                $p = $dom->createElementNS($wNs, 'w:p');
                $tc->appendChild($p);
            }
            foreach ($xp->query('./w:r', $p) as $oldRun) {
                $p->removeChild($oldRun);
            }
            $r = $dom->createElementNS($wNs, 'w:r');
            $t = $dom->createElementNS($wNs, 'w:t', $text);
            $r->appendChild($t);
            $p->appendChild($r);
        };

        $tables = $xp->query('//w:body/w:tbl');
        // Tabel 0: Tim Penguji (baris 2..7 = 6 peran)
        if ($tables->length > 0) {
            $rows0 = $xp->query('./w:tr', $tables->item(0));
            for ($i = 0; $i < 6; $i++) {
                $tr = $rows0->item($i + 2);
                if (!$tr) {
                    continue;
                }
                $cells = $xp->query('./w:tc', $tr);
                $tim = $roleSlots[$i] ?? null;
                $nama = $tim?->NAMA ?? '-';
                if ($cells->item(1)) {
                    $setCellText($cells->item(1), $nama);
                }
                $nilai = $tim ? ($avgByNama[strtolower(trim((string) $nama))] ?? '') : '';
                if ($nilai !== '' && $cells->item(3) && trim($cells->item(3)->textContent) === '') {
                    $setCellText($cells->item(3), (string) $nilai);
                }
            }
        }

        // Tabel 2 (Daftar Hadir): baris 1..6 = 6 peran
        if ($tables->length > 2) {
            $rows2 = $xp->query('./w:tr', $tables->item(2));
            for ($i = 0; $i < 6; $i++) {
                $tr = $rows2->item($i + 1);
                if (!$tr) {
                    continue;
                }
                $cells = $xp->query('./w:tc', $tr);
                $tim = $roleSlots[$i] ?? null;
                if ($cells->item(1)) {
                    $setCellText($cells->item(1), $tim?->NAMA ?? '-');
                }
            }
        }

        $outXml = $dom->saveXML();

        // Tandai status lulus & nilai huruf pada teks keputusan
        $stUp = strtoupper(trim($statusLulus));
        if ($stUp === 'LULUS') {
            $outXml = str_replace('LULUS / TIDAK LULUS', 'LULUS', $outXml);
        } elseif ($stUp === 'TIDAK LULUS') {
            $outXml = str_replace('LULUS / TIDAK LULUS', 'TIDAK LULUS', $outXml);
        }
        if (trim($indeks) !== '') {
            $outXml = str_replace('A  /  AB  /  B  /  BC  /  C  /  D  /  E', trim($indeks), $outXml);
        }

        // Isi catatan perbaikan 1..3 jika ada
        $catFlat = [];
        foreach ($catatanByNama as $nm => $items) {
            $catFlat[] = $nm . ': ' . implode('; ', $items);
        }
        for ($i = 0; $i < 3; $i++) {
            if (!isset($catFlat[$i])) {
                break;
            }
            $num = $i + 1;
            $outXml = preg_replace(
                '/' . $num . '\.\s*\.{10,}/u',
                $num . '. ' . htmlspecialchars($catFlat[$i], ENT_XML1 | ENT_QUOTES, 'UTF-8'),
                $outXml,
                1
            );
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $outXml);
        $zip->close();
    }

    /**
     * Cetak Daftar Hadir Sidang S1 / S2 langsung dari database.
     */
    public function cetakDaftarHadir(Request $request, $idJudul, $tahapan)
    {
        $tahapan = str_replace('_', ' ', $tahapan);
        $judul = TJudul::find($idJudul);
        if (!$judul) {
            return response()->json(['error' => 'Judul tidak ditemukan.'], 404);
        }
        $ajuan = $this->resolveAjuan($idJudul, $tahapan);
        if (!$ajuan) {
            return response()->json(['error' => 'Data judul/ajuan tidak ditemukan.'], 404);
        }

        $isS1 = strtoupper((string) ($ajuan->STRATA ?? '')) === 'S1';
        $templatePath = $isS1
            ? base_path('template/S1/daftar hadir TEMPLATE.docx')
            : base_path('template/S2/daftar hadir TEMPLATE.docx');
        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'Template daftar hadir tidak ditemukan.'], 500);
        }

        $kodeProdi = $ajuan->KODE_PRODI ?? $ajuan->kode_prodi ?? $judul->KODE_PRODI ?? null;
        $prodi = \App\Models\TProdi::where('KODE_PRODI', $kodeProdi)->first();
        $allTimSidang = TTimSidang::with('userPenilai')
            ->where('id_judul', $idJudul)
            ->where('tahapan_sidang', $tahapan)
            ->orderBy('URUTAN')
            ->orderBy('id')
            ->get();
        if (!$allTimSidang->contains(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'))) {
            $otherPembDh = TTimSidang::with('userPenilai')
                ->where('id_judul', $idJudul)
                ->whereRaw("LOWER(COALESCE(STATUS_TIM_SIDANG, '')) LIKE '%pembimbing%'")
                ->orderBy('URUTAN')
                ->orderBy('id')
                ->get();
            if ($otherPembDh->isNotEmpty()) {
                $allTimSidang = $otherPembDh->concat($allTimSidang)->values();
            }
        }
        $ketuaSidang = $allTimSidang->first(fn($t) => strtolower(trim($t->status_tim_sidang ?? '')) === 'ketua sidang');
        $ketuaPembimbing = $allTimSidang->first(fn($t) => str_contains(strtolower($t->status_tim_sidang ?? ''), 'pembimbing'));

        $tp = new TemplateProcessor($templatePath);
        $tp->setValue('nama_tahapan', strtoupper($tahapan));
        $tp->setValue('nama_prodi',   $prodi?->NAMA_PRODI ?? $ajuan->NAMA_PRODI ?? '-');
        $tp->setValue('alamat_prodi', $prodi?->ALAMAT ?? '-');
        $tp->setValue('tlp_prodi',    $prodi?->TLP ?? '-');
        $tp->setValue('fax_prodi',    $prodi?->FAX ?? $prodi?->fax ?? '-');
        $tp->setValue('email_prodi',  $prodi?->EMAIL ?? '-');
        $tp->setValue('web_prodi',    $prodi?->WEB ?? $prodi?->web ?? '-');
        $tp->setValue('nama_mhs',     $ajuan->NAMA_MHS ?? '-');
        $tp->setValue('nim',          $ajuan->NIM ?? '-');
        $tp->setValue('tgl_sidang',   $ajuan->tgl_sidang ? \Carbon\Carbon::parse($ajuan->tgl_sidang)->translatedFormat('l, d F Y') : '-');
        $tp->setValue('waktu',        $ajuan->waktu_sidang ? \Carbon\Carbon::parse($ajuan->waktu_sidang)->format('H:i') : '-');
        $tp->setValue('ruang_sidang', $ajuan->ruang_sidang ?? '-');
        $tp->setValue('judul',        $ajuan->JUDUL ?? $judul->JUDUL ?? '-');
        $tp->setValue('nama_ketua_sidang', $ketuaSidang?->NAMA ?? $ketuaPembimbing?->NAMA ?? '-');
        $tp->setValue('nip_ketua_sidang',  $ketuaSidang?->NIP ?? $ketuaPembimbing?->NIP ?? '-');
        $tp->setValue('nama_pembimbing_i', $ketuaPembimbing?->NAMA ?? '-');
        $tp->setValue('nip_pembimbing_i',  $ketuaPembimbing?->NIP ?? '-');

        $dhTtdMap = [];
        $dhTtdEmpty = [];
        for ($i = 0; $i < 6; $i++) {
            $t = $allTimSidang->get($i);
            $tp->setValue('nama_tim_' . ($i + 1),   $t?->NAMA ?? '-');
            $tp->setValue('status_tim_' . ($i + 1), $t?->status_tim_sidang ?? '-');
            $dhTtdMap['ttd_tim_' . ($i + 1)] = $this->signatureBinary($t);
            $dhTtdEmpty['ttd_tim_' . ($i + 1)] = '';
        }
        $ttdKetuaDh = $this->signatureBinary($ketuaSidang ?? $ketuaPembimbing);
        $dhTtdMap['ttd_ketua_sidang'] = $ttdKetuaDh;
        $dhTtdMap['signature']        = $ttdKetuaDh;

        $filename = 'Daftar_Hadir_' . str_replace(' ', '_', $tahapan) . '_' . $idJudul . '_' . time() . '.docx';
        $docxPath = storage_path('app/uploads/' . $filename);
        if (!is_dir(dirname($docxPath))) {
            \Illuminate\Support\Facades\File::makeDirectory(dirname($docxPath), 0775, true);
        }
        $tp->saveAs($docxPath);

        $this->fillBa221MkSignatures($docxPath, $dhTtdMap, $dhTtdEmpty);

        return response()->download($docxPath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Hitung rata-rata nilai per jenis form (skripsi / sidang) untuk BA S1 Prodi 125.
     * Memperhitungkan bobot parameter jika tersedia, lalu dirata-ratakan antar penilai.
     */
    private function calcProdi125FormAverage($idJudul, string $tahapan, string $formKeyword): ?string
    {
        $rows = TPenilaian::with('pointPenilaian')
            ->where('ID_JUDUL', $idJudul)
            ->where('TAHAPAN_SIDANG', $tahapan)
            ->whereRaw("LOWER(COALESCE(NO_FORM, '')) LIKE ?", ['%' . strtolower($formKeyword) . '%'])
            ->get();

        // Fallback: bila cetak BA TA II dan nilai skripsi diinput pada TA I
        if ($rows->isEmpty() && strtolower($formKeyword) === 'skripsi' && in_array(strtolower(trim($tahapan)), ['ta ii', 'ta 2', 'ta-2'], true)) {
            $rows = TPenilaian::with('pointPenilaian')
                ->where('ID_JUDUL', $idJudul)
                ->whereRaw("LOWER(COALESCE(NO_FORM, '')) LIKE '%skripsi%'")
                ->get();
        }

        if ($rows->isEmpty()) {
            return null;
        }

        $perTimScores = [];
        foreach ($rows->groupBy('ID_TIM_SIDANG') as $timRows) {
            $weightedSum = 0.0;
            $weightTotal = 0.0;
            $plainValues = [];

            foreach ($timRows as $r) {
                if ($r->NILAI === null || trim((string) $r->NILAI) === '' || !is_numeric($r->NILAI)) {
                    continue;
                }
                $val = (float) $r->NILAI;
                $plainValues[] = $val;
                $bobot = $r->pointPenilaian?->BOBOT ?? $r->pointPenilaian?->bobot ?? null;
                if ($bobot !== null && is_numeric($bobot) && (float) $bobot > 0) {
                    $weightedSum += $val * ((float) $bobot);
                    $weightTotal += (float) $bobot;
                }
            }

            if ($weightTotal > 0) {
                $perTimScores[] = $weightedSum / $weightTotal;
            } elseif (!empty($plainValues)) {
                $perTimScores[] = array_sum($plainValues) / count($plainValues);
            }
        }

        if (empty($perTimScores)) {
            return null;
        }

        return number_format(array_sum($perTimScores) / count($perTimScores), 2);
    }

    /**
     * Post-process BA S1 Prodi 125:
     * 1) Urutkan dua kotak abu (#C0C0C0 / silver) berdasarkan posisi X:
     *    - Kotak kiri  -> Nilai Tugas Akhir huruf (t_ajuan_sidang.NILAI_HURUF)
     *    - Kotak kanan -> Nilai Ujian Sarjana huruf (t_ajuan_sidang.NILAI_HURUF2)
     *    pada baik <mc:Choice> (DrawingML wps:txbx) maupun <mc:Fallback> (VML v:textbox).
     * 2) Sesuaikan coretan Lulus / Tidak Lulus sesuai STATUS_LULUS.
     * 3) Rapikan posisi placeholder ${ttd_ketua_125} sejajar tab pos=4770 di bawah "Ketua Sidang :".
     */
    private function postProcessS1Ba125(string $docxPath, string $nilaiHuruf1, string $nilaiHuruf2, string $statusLulus): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml)) {
            $zip->close();
            return;
        }

        $wNs   = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $mcNs  = 'http://schemas.openxmlformats.org/markup-compatibility/2006';
        $wpNs  = 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing';
        $wpsNs = 'http://schemas.microsoft.com/office/word/2010/wordprocessingShape';
        $aNs   = 'http://schemas.openxmlformats.org/drawingml/2006/main';
        $vNs   = 'urn:schemas-microsoft-com:vml';
        $oNs   = 'urn:schemas-microsoft-com:office:office';

        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', $wNs);
        $xp->registerNamespace('mc', $mcNs);
        $xp->registerNamespace('wp', $wpNs);
        $xp->registerNamespace('wps', $wpsNs);
        $xp->registerNamespace('a', $aNs);
        $xp->registerNamespace('v', $vNs);

        $buildCenteredBoxPara = function (\DOMDocument $doc, string $val) use ($wNs): \DOMElement {
            $p = $doc->createElementNS($wNs, 'w:p');
            $pPr = $doc->createElementNS($wNs, 'w:pPr');
            $sp = $doc->createElementNS($wNs, 'w:spacing');
            $sp->setAttributeNS($wNs, 'w:before', '0');
            $sp->setAttributeNS($wNs, 'w:after', '0');
            $sp->setAttributeNS($wNs, 'w:line', '240');
            $sp->setAttributeNS($wNs, 'w:lineRule', 'auto');
            $pPr->appendChild($sp);
            $jc = $doc->createElementNS($wNs, 'w:jc');
            $jc->setAttributeNS($wNs, 'w:val', 'center');
            $pPr->appendChild($jc);
            $p->appendChild($pPr);

            $trimmed = trim($val);
            if ($trimmed !== '') {
                $r = $doc->createElementNS($wNs, 'w:r');
                $rPr = $doc->createElementNS($wNs, 'w:rPr');
                $rf = $doc->createElementNS($wNs, 'w:rFonts');
                $rf->setAttributeNS($wNs, 'w:ascii', 'Palatino Linotype');
                $rf->setAttributeNS($wNs, 'w:hAnsi', 'Palatino Linotype');
                $rPr->appendChild($rf);
                $rPr->appendChild($doc->createElementNS($wNs, 'w:b'));
                $clr = $doc->createElementNS($wNs, 'w:color');
                $clr->setAttributeNS($wNs, 'w:val', '000000');
                $rPr->appendChild($clr);
                $sz = $doc->createElementNS($wNs, 'w:sz');
                $sz->setAttributeNS($wNs, 'w:val', '22');
                $rPr->appendChild($sz);
                $szCs = $doc->createElementNS($wNs, 'w:szCs');
                $szCs->setAttributeNS($wNs, 'w:val', '22');
                $rPr->appendChild($szCs);
                $r->appendChild($rPr);

                $t = $doc->createElementNS($wNs, 'w:t');
                $t->appendChild($doc->createTextNode($trimmed));
                $r->appendChild($t);
                $p->appendChild($r);
            }

            return $p;
        };

        // 1) Kumpulkan semua kotak abu (#C0C0C0 / silver) dan urutkan kiri -> kanan
        $greyBoxes = [];
        foreach ($xp->query('//mc:AlternateContent') as $ac) {
            /** @var \DOMElement $ac */
            $isGrey = $xp->query('.//wps:spPr//a:srgbClr[@val="C0C0C0" or @val="c0c0c0"]', $ac)->length > 0
                || $xp->query('.//v:rect[@fillcolor="silver" or @fillcolor="#c0c0c0" or @fillcolor="#C0C0C0"]', $ac)->length > 0;
            if (!$isGrey) {
                continue;
            }
            $posNode = $xp->query('.//wp:positionH/wp:posOffset', $ac)->item(0);
            $xPos = $posNode ? (float) trim($posNode->textContent) : 0.0;
            if ($xPos <= 0) {
                $vRect = $xp->query('.//v:rect', $ac)->item(0);
                if ($vRect instanceof \DOMElement && preg_match('/margin-left\s*:\s*([\d.]+)pt/i', $vRect->getAttribute('style'), $m)) {
                    $xPos = ((float) $m[1]) * 12700.0;
                }
            }
            $greyBoxes[] = ['x' => $xPos, 'node' => $ac];
        }

        usort($greyBoxes, fn($a, $b) => $a['x'] <=> $b['x']);

        $boxValues = [
            0 => $nilaiHuruf1, // Kotak kiri  = Nilai Tugas Akhir (NILAI_HURUF)
            1 => $nilaiHuruf2, // Kotak kanan = Nilai Ujian Sarjana (NILAI_HURUF2)
        ];

        foreach ($boxValues as $idx => $gradeVal) {
            if (!isset($greyBoxes[$idx])) {
                continue;
            }
            /** @var \DOMElement $ac */
            $ac = $greyBoxes[$idx]['node'];

            // Update DrawingML (<mc:Choice>)
            foreach ($xp->query('.//wps:bodyPr', $ac) as $bodyPr) {
                if ($bodyPr instanceof \DOMElement) {
                    $bodyPr->setAttribute('lIns', '18000');
                    $bodyPr->setAttribute('rIns', '18000');
                    $bodyPr->setAttribute('tIns', '18000');
                    $bodyPr->setAttribute('bIns', '18000');
                    $bodyPr->setAttribute('anchor', 'ctr');
                }
            }
            foreach ($xp->query('.//wps:txbx/w:txbxContent', $ac) as $txbxContent) {
                while ($txbxContent->firstChild) {
                    $txbxContent->removeChild($txbxContent->firstChild);
                }
                $txbxContent->appendChild($buildCenteredBoxPara($dom, $gradeVal));
            }

            // Update VML (<mc:Fallback>)
            foreach ($xp->query('.//v:rect', $ac) as $vRect) {
                if ($vRect instanceof \DOMElement) {
                    if ($vRect->hasAttributeNS($oNs, 'gfxdata')) {
                        $vRect->removeAttributeNS($oNs, 'gfxdata');
                    }
                    if ($vRect->hasAttribute('o:gfxdata')) {
                        $vRect->removeAttribute('o:gfxdata');
                    }
                    $style = $vRect->getAttribute('style');
                    if (str_contains($style, 'v-text-anchor:')) {
                        $style = preg_replace('/v-text-anchor\s*:\s*[a-z]+/i', 'v-text-anchor:middle', $style) ?? $style;
                    } else {
                        $style = rtrim($style, ';') . ';v-text-anchor:middle';
                    }
                    $vRect->setAttribute('style', $style);
                }
            }
            foreach ($xp->query('.//v:textbox', $ac) as $vTextbox) {
                if ($vTextbox instanceof \DOMElement) {
                    $vTextbox->setAttribute('inset', '1pt,1pt,1pt,1pt');
                }
            }
            foreach ($xp->query('.//v:textbox/w:txbxContent', $ac) as $vContent) {
                while ($vContent->firstChild) {
                    $vContent->removeChild($vContent->firstChild);
                }
                $vContent->appendChild($buildCenteredBoxPara($dom, $gradeVal));
            }
        }

        // 2) Coret Lulus / Tidak Lulus sesuai STATUS_LULUS di semua txbxContent yg memuatnya
        $stNorm = strtolower(trim($statusLulus));
        if (in_array($stNorm, ['lulus', 'tidak lulus'], true)) {
            $strikeLulus = ($stNorm === 'tidak lulus');
            $makeStatusPara = function (\DOMDocument $doc, string $label, bool $striked) use ($wNs): \DOMElement {
                $p = $doc->createElementNS($wNs, 'w:p');
                $pPr = $doc->createElementNS($wNs, 'w:pPr');
                $jc = $doc->createElementNS($wNs, 'w:jc');
                $jc->setAttributeNS($wNs, 'w:val', 'center');
                $pPr->appendChild($jc);
                $p->appendChild($pPr);

                $r = $doc->createElementNS($wNs, 'w:r');
                $rPr = $doc->createElementNS($wNs, 'w:rPr');
                $rf = $doc->createElementNS($wNs, 'w:rFonts');
                $rf->setAttributeNS($wNs, 'w:ascii', 'Palatino Linotype');
                $rf->setAttributeNS($wNs, 'w:hAnsi', 'Palatino Linotype');
                $rPr->appendChild($rf);
                if ($striked) {
                    $rPr->appendChild($doc->createElementNS($wNs, 'w:strike'));
                }
                $r->appendChild($rPr);
                $t = $doc->createElementNS($wNs, 'w:t');
                $t->appendChild($doc->createTextNode($label));
                $r->appendChild($t);
                $p->appendChild($r);
                return $p;
            };

            foreach ($xp->query('//w:txbxContent') as $tc) {
                $plain = strtolower(preg_replace('/\s+/', ' ', trim($tc->textContent)) ?? '');
                if (str_contains($plain, 'lulus/') && str_contains($plain, 'tidak lulus')) {
                    while ($tc->firstChild) {
                        $tc->removeChild($tc->firstChild);
                    }
                    $tc->appendChild($makeStatusPara($dom, 'Lulus/', $strikeLulus));
                    $tc->appendChild($makeStatusPara($dom, 'Tidak Lulus', !$strikeLulus));
                }
            }
        }

        // 3) Rapikan posisi placeholder ${ttd_ketua_125} di bawah "Ketua Sidang :"
        $body = $xp->query('//w:body')->item(0);
        if ($body instanceof \DOMElement) {
            $bodyParas = [];
            foreach ($xp->query('./w:p', $body) as $bp) {
                $bodyParas[] = $bp;
            }
            $ketuaLabelIdx = null;
            $ttdParaIdx = null;
            foreach ($bodyParas as $i => $bp) {
                $txt = trim($bp->textContent);
                if ($ketuaLabelIdx === null && stripos($txt, 'Ketua Sidang') !== false) {
                    $ketuaLabelIdx = $i;
                }
                if (str_contains($txt, '${ttd_ketua_125}')) {
                    $ttdParaIdx = $i;
                }
            }

            if ($ketuaLabelIdx !== null && $ttdParaIdx !== null && $ttdParaIdx > $ketuaLabelIdx) {
                // Hapus paragraf spasi kosong berlebih di antara "Ketua Sidang :" dan "${ttd_ketua_125}"
                for ($k = $ketuaLabelIdx + 1; $k < $ttdParaIdx; $k++) {
                    if (trim($bodyParas[$k]->textContent) === '') {
                        $body->removeChild($bodyParas[$k]);
                    }
                }
                // Pastikan <w:tab/> berada di <w:r> terpisah dari <w:t>${ttd_ketua_125}</w:t>
                // agar setImageValue tidak menghapus tab perataan pos=4770
                /** @var \DOMElement $ttdP */
                $ttdP = $bodyParas[$ttdParaIdx];
                foreach ($xp->query('./w:r', $ttdP) as $oldRun) {
                    $ttdP->removeChild($oldRun);
                }
                $pPr = $xp->query('./w:pPr', $ttdP)->item(0);
                if (!$pPr instanceof \DOMElement) {
                    $pPr = $dom->createElementNS($wNs, 'w:pPr');
                    $ttdP->insertBefore($pPr, $ttdP->firstChild);
                }
                if ($xp->query('./w:tabs', $pPr)->length === 0) {
                    $tabs = $dom->createElementNS($wNs, 'w:tabs');
                    $tabDef = $dom->createElementNS($wNs, 'w:tab');
                    $tabDef->setAttributeNS($wNs, 'w:val', 'left');
                    $tabDef->setAttributeNS($wNs, 'w:pos', '4770');
                    $tabs->appendChild($tabDef);
                    $pPr->insertBefore($tabs, $pPr->firstChild);
                }
                $rTab = $dom->createElementNS($wNs, 'w:r');
                $rTab->appendChild($dom->createElementNS($wNs, 'w:tab'));
                $ttdP->appendChild($rTab);

                $rTok = $dom->createElementNS($wNs, 'w:r');
                $tTok = $dom->createElementNS($wNs, 'w:t');
                $tTok->appendChild($dom->createTextNode('${ttd_ketua_125}'));
                $rTok->appendChild($tTok);
                $ttdP->appendChild($rTok);
            } elseif ($ketuaLabelIdx !== null && $ttdParaIdx === null) {
                // Fallback bila template belum memiliki ${ttd_ketua_125}
                $refNode = $bodyParas[$ketuaLabelIdx + 1] ?? null;
                $newP = $dom->createElementNS($wNs, 'w:p');
                $pPr = $dom->createElementNS($wNs, 'w:pPr');
                $tabs = $dom->createElementNS($wNs, 'w:tabs');
                $tabDef = $dom->createElementNS($wNs, 'w:tab');
                $tabDef->setAttributeNS($wNs, 'w:val', 'left');
                $tabDef->setAttributeNS($wNs, 'w:pos', '4770');
                $tabs->appendChild($tabDef);
                $pPr->appendChild($tabs);
                $newP->appendChild($pPr);

                $rTab = $dom->createElementNS($wNs, 'w:r');
                $rTab->appendChild($dom->createElementNS($wNs, 'w:tab'));
                $newP->appendChild($rTab);

                $rTok = $dom->createElementNS($wNs, 'w:r');
                $tTok = $dom->createElementNS($wNs, 'w:t');
                $tTok->appendChild($dom->createTextNode('${ttd_ketua_125}'));
                $rTok->appendChild($tTok);
                $newP->appendChild($rTok);

                if ($refNode) {
                    $body->insertBefore($newP, $refNode);
                } else {
                    $body->appendChild($newP);
                }
            }
        }

        $outXml = $dom->saveXML();
        if ($outXml !== false) {
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $outXml);
        }
        $zip->close();
    }

    /**
     * Ubah tabel kriteria pada template Lembar Penilaian Prodi 125 menjadi
     * tabel Publikasi: NO | Parameter Penilaian | Cek Makalah | Catatan.
     */
    private function postProcessForm125PublikasiTable(string $docxPath, $penilaianRows): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml)) {
            $zip->close();
            return;
        }

        $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', $wNs);

        $tables = $xpath->query('//w:body/w:tbl');
        if (!$tables || $tables->length < 2) {
            $zip->close();
            return;
        }
        $tbl = $tables->item(1);
        $trs = $xpath->query('./w:tr', $tbl);
        if (!$trs || $trs->length < 2) {
            $zip->close();
            return;
        }

        $setCellText = function (\DOMElement $tc, string $text, string $align = 'left', bool $bold = false) use ($dom, $xpath, $wNs): void {
            $ps = $xpath->query('./w:p', $tc);
            while ($ps && $ps->length > 1) {
                $tc->removeChild($ps->item($ps->length - 1));
                $ps = $xpath->query('./w:p', $tc);
            }
            $p = ($ps && $ps->length > 0) ? $ps->item(0) : $dom->createElementNS($wNs, 'w:p');
            if (!$p->parentNode) {
                $tc->appendChild($p);
            }
            foreach (iterator_to_array($xpath->query('./*[not(self::w:pPr)]', $p)) as $child) {
                $p->removeChild($child);
            }
            $pPr = $xpath->query('./w:pPr', $p)->item(0);
            if (!$pPr) {
                $pPr = $dom->createElementNS($wNs, 'w:pPr');
                $p->insertBefore($pPr, $p->firstChild);
            }
            foreach (iterator_to_array($xpath->query('./w:jc', $pPr)) as $jcOld) {
                $pPr->removeChild($jcOld);
            }
            if ($align !== 'left') {
                $jc = $dom->createElementNS($wNs, 'w:jc');
                $jc->setAttributeNS($wNs, 'w:val', $align);
                $pPr->appendChild($jc);
            }

            $r = $dom->createElementNS($wNs, 'w:r');
            $rPr = $dom->createElementNS($wNs, 'w:rPr');
            $rFonts = $dom->createElementNS($wNs, 'w:rFonts');
            $rFonts->setAttributeNS($wNs, 'w:ascii', 'Times New Roman');
            $rFonts->setAttributeNS($wNs, 'w:hAnsi', 'Times New Roman');
            $rFonts->setAttributeNS($wNs, 'w:cs', 'Times New Roman');
            $rPr->appendChild($rFonts);
            if ($bold) {
                $rPr->appendChild($dom->createElementNS($wNs, 'w:b'));
            }
            $sz = $dom->createElementNS($wNs, 'w:sz');
            $sz->setAttributeNS($wNs, 'w:val', '22');
            $rPr->appendChild($sz);
            $r->appendChild($rPr);

            $t = $dom->createElementNS($wNs, 'w:t');
            $t->setAttribute('xml:space', 'preserve');
            $t->appendChild($dom->createTextNode($text));
            $r->appendChild($t);
            $p->appendChild($r);
        };

        $colWidths = [670, 5200, 1650, 3298];
        $headerTr = $trs->item(0);
        $headerTcs = $xpath->query('./w:tc', $headerTr);
        $headers = ['NO', 'Parameter Penilaian', 'Cek Makalah', 'Catatan'];
        if ($headerTcs && $headerTcs->length >= 4) {
            for ($c = 0; $c < 4; $c++) {
                $tc = $headerTcs->item($c);
                $tcW = $xpath->query('./w:tcPr/w:tcW', $tc)->item(0);
                if ($tcW) {
                    $tcW->setAttributeNS($wNs, 'w:w', (string) $colWidths[$c]);
                }
                $setCellText($tc, $headers[$c], 'center', true);
            }
        }

        $templateTr = $trs->item(1)->cloneNode(true);
        for ($rIdx = $trs->length - 1; $rIdx >= 1; $rIdx--) {
            $tbl->removeChild($trs->item($rIdx));
        }

        $num = 0;
        foreach ($penilaianRows as $row) {
            $num++;
            $tr = $templateTr->cloneNode(true);
            $tcs = $xpath->query('./w:tc', $tr);
            if ($tcs && $tcs->length >= 4) {
                for ($c = 0; $c < 4; $c++) {
                    $tcW = $xpath->query('./w:tcPr/w:tcW', $tcs->item($c))->item(0);
                    if ($tcW) {
                        $tcW->setAttributeNS($wNs, 'w:w', (string) $colWidths[$c]);
                    }
                }
                $sc = strtolower(trim((string) ($row->STATUS_CATATAN ?? $row->status_catatan ?? '')));
                $cm = strtoupper(trim((string) ($row->CEK_MAKALAH ?? $row->cek_makalah ?? '')));
                $cekSymbol = $sc === 'y' ? '-' : (in_array($cm, ['Y', '1'], true) ? '☑' : '☐');

                $setCellText($tcs->item(0), (string) $num, 'center', false);
                $setCellText($tcs->item(1), (string) ($row->NAMA_PENILAIAN ?? $row->PENILAIAN ?? ''), 'left', false);
                $setCellText($tcs->item(2), $cekSymbol, 'center', false);
                $setCellText($tcs->item(3), (string) ($row->CATATAN ?? $row->catatan ?? ''), 'left', false);
            }
            $tbl->appendChild($tr);
        }

        $outXml = $dom->saveXML();
        if ($outXml !== false) {
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $outXml);
        }
        $zip->close();
    }

    /**
     * Pusatkan tanda centang pada sel kolom ke-3 (kotak Cek Makalah) di tabel
     * kriteria Form Penilaian Publikasi TA 2 Prodi 125.
     */
    private function centerPublikasi125CekMakalahDocx(string $docxPath): void
    {
        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return;
        }
        $xml = $zip->getFromName('word/document.xml');
        if ($xml === false) {
            $zip->close();
            return;
        }
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = true;
        if (!@$dom->loadXML($xml)) {
            $zip->close();
            return;
        }
        $wNs = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', $wNs);

        $tbl = $xp->query('//w:body/w:tbl')->item(1);
        if ($tbl) {
            foreach ($xp->query('./w:tr', $tbl) as $tr) {
                $tc = $xp->query('./w:tc', $tr)->item(2);
                if (!$tc) {
                    continue;
                }
                foreach ($xp->query('./w:p', $tc) as $p) {
                    $pPr = $xp->query('./w:pPr', $p)->item(0);
                    if (!$pPr instanceof \DOMElement) {
                        $pPr = $dom->createElementNS($wNs, 'w:pPr');
                        $p->insertBefore($pPr, $p->firstChild);
                    }
                    $sp = $xp->query('./w:spacing', $pPr)->item(0);
                    if (!$sp instanceof \DOMElement) {
                        $sp = $dom->createElementNS($wNs, 'w:spacing');
                        $pPr->appendChild($sp);
                    }
                    $sp->setAttributeNS($wNs, 'w:before', '0');
                    $sp->setAttributeNS($wNs, 'w:after', '0');

                    $jc = $xp->query('./w:jc', $pPr)->item(0);
                    if (!$jc instanceof \DOMElement) {
                        $jc = $dom->createElementNS($wNs, 'w:jc');
                        $pPr->appendChild($jc);
                    }
                    $jc->setAttributeNS($wNs, 'w:val', 'center');

                    foreach ($xp->query('./w:r/w:t', $p) as $tNode) {
                        if (trim($tNode->textContent) !== '') {
                            $rNode = $tNode->parentNode;
                            if ($rNode instanceof \DOMElement && $xp->query('./w:rPr/w:b', $rNode)->length === 0) {
                                $rPr = $xp->query('./w:rPr', $rNode)->item(0);
                                if (!$rPr instanceof \DOMElement) {
                                    $rPr = $dom->createElementNS($wNs, 'w:rPr');
                                    $rNode->insertBefore($rPr, $rNode->firstChild);
                                }
                                $rPr->appendChild($dom->createElementNS($wNs, 'w:b'));
                            }
                        }
                    }
                }
            }
        }

        $outXml = $dom->saveXML();
        if ($outXml !== false) {
            $zip->deleteName('word/document.xml');
            $zip->addFromString('word/document.xml', $outXml);
        }
        $zip->close();
    }
}
