<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrepareTemplate extends Command
{
    protected $signature = 'template:prepare';
    protected $description = 'Create placeholder template for cetak form';

    public function handle()
    {
        $templates = [
            [
                'src' => base_path('template/pROPOSAL/Form. 302.3 Penilaian Proposal Penelitian Disertasi rev Aditya.docx'),
                'dst' => base_path('template/pROPOSAL/Form. 302.3 Penilaian Proposal Penelitian Disertasi rev Aditya TEMPLATE.docx'),
                'replacements' => [
                    'Pengaruh Alterasi Hidrotermal terhadap Sifat Magnetik dari Sedimen Permukaan Danau: Studi Kasus Danau Batur Bali.' => '${judul}',
                    'Ulvienin Harlianti' => '${nama_mhs}',
                    '32322004' => '${nim}',
                    'Prof. Dr. Satria Bijaksana.' => '${pembimbing_utama}',
                    'Dr. Irwan Iskandar' => '${pembimbing_1}',
                    '----' => '${pembimbing_2}',
                    '_______________ (jumlah total dibagi 5)' => '${rata_nilai} (jumlah total dibagi 5)',
                    'Diagram alur yang digunakan masih tidak sesuai dengan deskripsi dan ada kesalahan. Selain itu juga ada diagram alur yang membingungkan. Masih banyak ketidak konsitenan dalam penulisan. Komentar untuk perbaikan lebih detail dituliskan di formulir di bawah.' => '${catatan}',
                ]
            ],
            [
                'src' => base_path('template/pROPOSAL/BA proposal.docx'),
                'dst' => base_path('template/pROPOSAL/BA proposal TEMPLATE.docx'),
                'replacements' => [
                    '$(judul)' => '${judul}',
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    '$(nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '$(nama pembimbing I)' => '${nama_pembimbing_i}',
                    '$(nama pembimbing II)' => '${nama_pembimbing_ii}',
                    '$(nilai rata2 dari form penilaian)' => '${nilai_rata2_dari_form_penilaian}',
                    '$(nilai akhir rata rata)' => '${nilai_akhir_rata_rata}',
                    '$(nilai akhir indeks)' => '${nilai_akhir_indeks}',
                    '$(tgl create penilaian)' => '${tgl_create_penilaian}',
                    '$(Nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '$(nip ketua pembimbing)' => '${nip_ketua_pembimbing}',
                ]
            ],
            [
                'src' => base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai.docx'),
                'dst' => base_path('template/pROPOSAL/cetak form penilaian proposal tipe nilai TEMPLATE.docx'),
                'replacements' => [
                    '$(nama judul)' => '${nama_judul}',
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    '$(nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '$(nama pembimbing I)' => '${nama_pembimbing_i}',
                    '$(nama pembimbing II)' => '${nama_pembimbing_ii}',
                    '$(Nama penilaian)' => '${nama_penilaian}',
                    '$(Keterangan)' => '${keterangan}',
                    '$(Catatan)' => '${catatan}',
                    '$(Nilai)' => '${nilai}',
                    '$(Nama penilaian$(Keterangan)' => '${nama_penilaian_keterangan}',
                    '$(Catatan($(nilai)' => '${catatan_nilai}',
                    '$((Keterangan)' => '${keterangan}',
                    '$(Nama Penilai)' => '${nama_penilai}',
                    '$(nip)' => '${nip}',
                    '_______________ (jumlah total dibagi 5)' => '${rata_nilai} (jumlah total dibagi 5)',
                ]
            ],
            [
                'src' => base_path('template/cetak form penilain tipe text.docx'),
                'dst' => base_path('template/cetak form penilain tipe text TEMPLATE.docx'),
                'replacements' => [
                    '(No form)' => '${no_form}',
                    '(judul)' => '${judul}',
                    '(nama mhs)' => '${nama_mhs}',
                    '(nim)' => '${nim}',
                    '(nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '(nama pembimbing I)' => '${nama_pembimbing_i}',
                    '(nama pembimbing II)' => '${nama_pembimbing_ii}',
                    '_______________ (jumlah total dibagi 5)' => '${rata_nilai} (jumlah total dibagi 5)',
                    '(Nama penilaian)' => '${nama_penilaian}',
                    '(catatan)' => '${catatan}',
                    '(tgl create penilaian)' => '${tgl_create_penilaian}',
                    '(Nama penilai)' => '${nama_penilai}',
                    '(nip penilai)' => '${nip_penilai}',
                ]
            ],
            [
                'src' => base_path('template/SIDANG/BA sidang akhir.docx'),
                'dst' => base_path('template/SIDANG/BA sidang akhir TEMPLATE.docx'),
                'replacements' => [
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    '$(judul)' => '${judul}',
                    '$(tgl sidang)' => '${tgl_sidang}',
                    '$(waktu)' => '${waktu}',
                    '$(waktu selesai)' => '${waktu_selesai}',
                    '$(nama kaprodi)' => '${nama_kaprodi}',
                    '$(nama ketua sidang)' => '${nama_ketua_sidang}',
                    'Nilai rata2' => '${nilai_rata2}',
                ]
            ],
            [
                'src' => base_path('template/SIDANG/form penilaian sidang akhir.docx'),
                'dst' => base_path('template/SIDANG/form penilaian sidang akhir TEMPLATE.docx'),
                'replacements' => [
                    '(no form)' => '${no_form}',
                    '(judul)' => '${judul}',
                    '(nama mhs)' => '${nama_mhs}',
                    '(nim)' => '${nim}',
                    '(tgl sidang)' => '${tgl_sidang}',
                    '(ruang)' => '${ruang_sidang}',
                    '(nama penilaian)' => '${nama_penilaian}',
                    '(keterangan)' => '${keterangan}',
                    '(nilai)' => '${nilai}',
                    '(nama penguji/pembimbing)' => '${nama_penilai}',
                    '(nama penguji 1)' => '${nama_penilai}',
                    '(catatan)' => '${catatan}',
                    '(rata nilai)' => '${rata_nilai}',
                ]
            ],
            [
                'src' => base_path('template/SK-1/BA Penilaian SK I sd SK III.docx'),
                'dst' => base_path('template/SK-1/BA Penilaian SK I sd SK III TEMPLATE.docx'),
                'replacements' => [
                    '$(tgl sidang)' => '${tgl_sidang}',
                    '$(waktu)' => '${waktu}',
                    '$(Nilai rata2)' => '${nilai_rata2}',
                    '$(Nama Penguji I)' => '${nama_penguji_i}',
                    '$(Nama Penguji II)' => '${nama_penguji_ii}',
                    '$(Nama Penguji III)' => '${nama_penguji_iii}',
                    '$(Nama kaprodi)' => '${nama_kaprodi}',
                    '$(nip kaprodi)' => '${nip_kaprodi}',
                ]
            ],
            [
                'src' => base_path('template/SK-1/form penilaian sk I sd sk III.docx'),
                'dst' => base_path('template/SK-1/form penilaian sk I sd sk III TEMPLATE.docx'),
                'replacements' => [
                    '$(judul)' => '${judul}',
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    '$(nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '$(Bidang keahlian ketua pembimbing)' => '${bidang_keahlian_ketua_pembimbing}',
                    '$(nama pembimbing I)' => '${nama_pembimbing_i}',
                    '$(bidang keahlian pembimbing I)' => '${bidang_keahlian_pembimbing_i}',
                    '$(nama pembimbing II)' => '${nama_pembimbing_ii}',
                    '$(bidang keahlian pembimbing II)' => '${bidang_keahlian_pembimbing_ii}',
                    '$(Tgl sidang)' => '${tgl_sidang}',
                    '$(ruang sidang)' => '${ruang_sidang}',
                    '$(Nama penilaian)' => '${nama_penilaian}',
                    '$(keterangan)' => '${keterangan}',
                    '$(nilai)' => '${nilai}',
                    '$(tgl create penilaian)' => '${tgl_create_penilaian}',
                    '$(Nama_penilai)' => '${nama_penilai}',
                    '$(nip penilai)' => '${nip_penilai}',
                    'Nilai Rata-Rata : /skala 5 (jumlah skor detail dibagi 5)' => 'Nilai Rata-Rata : ${rata_nilai} /skala 5 (jumlah skor detail dibagi 5)',
                ]
            ],
            [
                'src' => base_path('template/SK-4/ba SK IV.docx'),
                'dst' => base_path('template/SK-4/ba SK IV TEMPLATE.docx'),
                'replacements' => [
                    '$(judul)' => '${judul}',
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    '$(nama ketua pembimbing)' => '${nama_ketua_pembimbing}',
                    '$(nama pembimbing I)' => '${nama_pembimbing_i}',
                    '$(nama pembimbing II)' => '${nama_pembimbing_ii}',
                    '$(Nilai rata2)' => '${nilai_rata2}',
                    '$(Nama kaprodi)' => '${nama_kaprodi}',
                    '$(nip kaprodi)' => '${nip_kaprodi}',
                    '$(tgl sidang)' => '${tgl_sidang}',
                    '$(nama ketua sidang)' => '${nama_ketua_sidang}',
                    '$(nip ketua sidang)' => '${nip_ketua_sidang}',
                ]
            ],
            [
                'src' => base_path('template/surat Kesediaan Tim Penelaah Proposal.docx'),
                'dst' => base_path('template/surat Kesediaan Tim Penelaah Proposal TEMPLATE.docx'),
                'replacements' => [
                    '$(tgl penelaah)' => '${tgl_penelaah}',
                    '$( nama penguji)' => '${nama_penguji}',
                    '$(institusi)' => '${institusi}',
                    '$(Nama Mahaiswa)' => '${nama_mahaiswa}',
                    '$(nim)' => '${nim}',
                    '$(Judul)' => '${judul}',
                    '$(pembimbing)' => '${pembimbing}',
                    '$(tgl hasil penelaahan)' => '${tgl_hasil_penelaahan}',
                    '$(dari tabel t user, status dekan \'y\')' => '${dari_tabel_t_user_status_dekan_y}',
                    '$(nip)' => '${nip}',
                    '$(email)' => '${email}',
                ]
            ],
            [
                'src' => base_path('template/UNDANGAN SIDANG.docx'),
                'dst' => base_path('template/UNDANGAN SIDANG TEMPLATE.docx'),
                'replacements' => [
                    '$(no undnagan)' => '${no_undnagan}',
                    '$(Tgl Undangan)' => '${tgl_undangan}',
                    '$(Nama)' => '${nama}',
                    '$(NIM)' => '${nim}',
                    '$(nama mahasiwa)' => '${nama_mahasiwa}',
                    '$(nim)' => '${nim}',
                    '$(judul)' => '${judul}',
                    '$(Tanggal Sidang)' => '${tanggal_sidang}',
                    '$(waktu – waktu selesai)' => '${waktu_waktu_selesai}',
                    '$(ruangan)' => '${ruangan}',
                    '$(NAMA WDA)' => '${nama_wda}',
                    '$(NIP WDA)' => '${nip_wda}',
                ]
            ],
        ];

        foreach ($templates as $template) {
            $src = $template['src'];
            $dst = $template['dst'];
            $replacements = $template['replacements'];

            if (!file_exists($src)) {
                $this->error('Source template not found: ' . $src);
                continue;
            }

            copy($src, $dst);

            $zip = new \ZipArchive();
            if ($zip->open($dst) !== true) {
                $this->error('Cannot open zip for: ' . $dst);
                continue;
            }

            $xml = $zip->getFromName('word/document.xml');

            $xml = $this->fuzzyReplaceAll($xml, $replacements);

            $zip->addFromString('word/document.xml', $xml);
            $zip->close();

            $this->info('Placeholder template created: ' . $dst);
        }

        // Handle undangan seminar kemajuan secara khusus
        // karena perlu mengganti blok daftar tim sidang (ListParagraph numId=2)
        // dengan placeholder {kepada}
        $this->prepareUndanganKemajuan();

        return 0;
    }

    /**
     * Buat template undangan seminar kemajuan dengan benar.
     * 
     * Pendekatan:
     * 1. Copy file asli → TEMPLATE
     * 2. Ganti $(xxx) → ${xxx} dengan fuzzy XML match
     * 3. Ganti semua paragraf daftar tim sidang (ListParagraph + numId=2)
     *    setelah "Kepada Yth." dengan satu paragraf {kepada}
     */
    private function prepareUndanganKemajuan(): void
    {
        $src = base_path('template/undangan seminar kemajuan.docx');
        $dst = base_path('template/undangan seminar kemajuan TEMPLATE.docx');

        if (!file_exists($src)) {
            $this->error('Source template not found: ' . $src);
            return;
        }

        copy($src, $dst);

        $zip = new \ZipArchive();
        if ($zip->open($dst) !== true) {
            $this->error('Cannot open zip for: ' . $dst);
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        // LANGKAH 1: Ganti $(xxx) → ${xxx}
        $textReplacements = [
            '$(no undnagan)'                  => '${no_undnagan}',
            '$(Tgl Undangan)'                 => '${tgl_undangan}',
            '$(Nama)'                         => '${nama}',
            '$(NIM)'                          => '${nim}',
            '$(nama mahasiwa)'                => '${nama_mahasiwa}',
            '$(nim)'                          => '${nim}',
            '$(judul)'                        => '${judul}',
            '$(Tanggal Sidang)'               => '${tanggal_sidang}',
            '$(waktu – waktu selesai)'        => '${waktu_waktu_selesai}',
            '$(ruangan)'                      => '${ruangan}',
            '$(NAMA WDA)'                     => '${nama_wda}',
            '$(NIP WDA)'                      => '${nip_wda}',
            '$(nama tim sidang ketua sidang)' => '${nama_tim_sidang_ketua_sidang}',
            '$(ketua_sidang)'                 => '${ketua_sidang}',
        ];

        $xml = $this->fuzzyReplaceAll($xml, $textReplacements);

        // LANGKAH 2: Ganti blok daftar tim sidang dengan {kepada}
        // Strategi: split XML per </w:p>, identifikasi paragraf-paragraf yang
        // masuk ke blok "Kepada Yth." (pStyle=ListParagraph dengan numId="2"
        // atau paragraf perantara seperti sectPr/column-break/BodyText di antara mereka),
        // lalu ganti semua itu dengan satu paragraf {kepada}.
        $xml = $this->replaceKepadaBlock($xml);

        // Simpan kembali
        $zip2 = new \ZipArchive();
        if ($zip2->open($dst) === true) {
            $zip2->addFromString('word/document.xml', $xml);
            $zip2->close();
            $this->info('Placeholder template created: ' . $dst);
        } else {
            $this->error('Cannot save: ' . $dst);
        }
    }

    /**
     * Ganti blok daftar tim sidang di bagian "Kepada Yth." dengan {kepada}.
     *
     * PENTING: Paragraf yang mengandung <w:sectPr> (section properties) menyimpan
     * referensi ke header/footer dokumen. Kita harus mengekstrak dan mempertahankan
     * sectPr tersebut agar header/footer tidak hilang.
     */
    private function replaceKepadaBlock(string $xml): string
    {
        // Pecah XML menjadi array paragraf berdasarkan </w:p>
        $parts = preg_split('/(<\/w:p>)/', $xml, -1, PREG_SPLIT_DELIM_CAPTURE);

        $paragraphs = [];
        for ($i = 0; $i < count($parts); $i += 2) {
            $p = $parts[$i] . (isset($parts[$i + 1]) ? $parts[$i + 1] : '');
            if (trim($p) !== '') {
                $paragraphs[] = $p;
            }
        }

        // Temukan paragraf "Kepada" sebagai penanda awal pencarian
        $kepadaFound   = false;
        $startIdx      = -1;
        $endIdx        = -1;

        for ($i = 0; $i < count($paragraphs); $i++) {
            $p = $paragraphs[$i];

            if (!$kepadaFound && strpos($p, 'Kepada') !== false) {
                $kepadaFound = true;
                continue;
            }

            if (!$kepadaFound) {
                continue;
            }

            $isListNumId2  = strpos($p, 'ListParagraph') !== false
                          && preg_match('/<w:numId\s+w:val="2"/', $p);
            $hasSectPr     = strpos($p, '<w:sectPr') !== false;
            $hasColBreak   = strpos($p, 'w:type="column"') !== false;
            $isKppsText    = strpos($p, 'Anggota') !== false && strpos($p, 'KPPs') !== false;

            if ($isListNumId2) {
                if ($startIdx === -1) {
                    $startIdx = $i;
                }
                $endIdx = $i;
            } elseif ($startIdx !== -1 && ($hasSectPr || $hasColBreak || $isKppsText)) {
                // Paragraf perantara di dalam blok — masukkan ke range
                $endIdx = $i;
            } elseif ($startIdx !== -1) {
                // Sudah keluar dari blok list
                break;
            }
        }

        if ($startIdx === -1) {
            $this->warn('  [undangan kemajuan] Blok daftar kepada tidak ditemukan, template tidak diubah.');
            return $xml;
        }

        // -------------------------------------------------------
        // SELAMATKAN sectPr dari paragraf yang akan dihapus
        // sectPr berisi headerReference & footerReference yang
        // WAJIB ada agar header/footer dokumen tidak hilang
        // -------------------------------------------------------
        $savedSectPr = '';
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            if (preg_match('/<w:sectPr\b.*?<\/w:sectPr>/s', $paragraphs[$i], $m)) {
                $savedSectPr = $m[0];
                break; // ambil sectPr pertama yang ditemukan
            }
        }

        // Paragraf pengganti {kepada}
        $kepadaP = '<w:p><w:r><w:t>{kepada}</w:t></w:r></w:p>';

        // Jika ada sectPr yang diselamatkan, bungkus dalam paragraf kosong
        // agar referensi header/footer tetap valid
        $sectPrP = '';
        if ($savedSectPr !== '') {
            $sectPrP = '<w:p><w:pPr>' . $savedSectPr . '</w:pPr></w:p>';
        }

        // Rekonstruksi XML: ganti paragraf startIdx..endIdx
        $before = implode('', array_slice($paragraphs, 0, $startIdx));
        $after  = implode('', array_slice($paragraphs, $endIdx + 1));

        return $before . $kepadaP . $sectPrP . $after;
    }


    /**
     * Ganti semua placeholder $(xxx) → ${xxx} di XML Word dengan fuzzy matching
     * yang toleran terhadap XML tags yang menyela di antara karakter teks.
     */
    private function fuzzyReplaceAll(string $xml, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            $chars = preg_split('//u', $search, -1, PREG_SPLIT_NO_EMPTY);
            $regex = '';
            foreach ($chars as $char) {
                if ($char === ' ') {
                    $regex .= '(?:\s|<[^>]+>)*';
                } else {
                    $regex .= preg_quote($char, '/') . '(?:<[^>]+>)*';
                }
            }
            $regex = '/' . $regex . '/ui';

            $xml = preg_replace_callback($regex, function ($matches) use ($replace) {
                $match = $matches[0];
                $first = true;
                $replaced = preg_replace_callback('/>([^<]+)</', function ($m) use (&$first, $replace) {
                    if ($first) {
                        $first = false;
                        return '>' . $replace . '<';
                    }
                    return '><';
                }, '>' . $match . '<');
                return substr($replaced, 1, -1);
            }, $xml);
        }

        return $xml;
    }
}