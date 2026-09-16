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

                    // Placeholder tanda tangan dari database (t_user.SIGNATURE)
                    // diganti saat cetak menjadi gambar ttd penilai/pejabat terkait
                    'Tanda Tangan' => '${signature}',
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
                    '$(Nama ketua sidang)' => '${nama_ketua_sidang}',
                    '$(nip ketua sidang)' => '${nip_ketua_sidang}',

                    // Placeholder tanda tangan — ganti "Tanda Tangan" dengan ${signature}
                    'Tanda Tangan' => '${signature}',
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

                    // Placeholder tanda tangan dari database (t_user.SIGNATURE)
                    // diganti saat cetak menjadi gambar ttd penilai/pejabat terkait
                    'Tanda Tangan' => '${signature}',
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

                    // Placeholder tanda tangan dari database (t_user.SIGNATURE)
                    // diganti saat cetak menjadi gambar ttd penilai/pejabat terkait
                    'Tanda Tangan' => '${signature}',
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

                    // Nama tim penguji/penilai di tabel "Tim Penguji" — pola peran spesifik
                    // (baris 1-3 = pembimbing, baris 4-6 = penguji) seperti pada BA SK.
                    '$(Nama Ketua Pembimbing)'  => '${nama_ketua_pembimbing}',
                    '$(Nama Ko-Pembimbing 1)'   => '${nama_pembimbing_i}',
                    '$(Nama Ko-Pembimbing 2)'   => '${nama_pembimbing_ii}',
                    // Penguji baris 4-6 menggunakan placeholder ${nama_penguji_i} berulang;
                    // diisi per-kemunculan saat cetak (fillSidangAkhirXml).
                    '$(Nama Penguji)'           => '${nama_penguji_i}',

                    // Placeholder tanda tangan di baris pertama ("$ Tanda Tangan") — ambil ttd dari DB.
                    // Baris "Tanda Tangan dan Nama Jelas" TETAP sebagai teks (tidak diganti).
                    '$ Tanda Tangan' => '${signature}',
                ],
                'distinctBaSidangSignatures' => true,
                'baSidangRowSignatures'      => true,
                'baSidangCapaian'            => true,
                'baSidangLulusBox'           => true,
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

                    // Placeholder tanda tangan di baris "Penguji" ("$ Tanda Tangan") — ambil ttd dari DB.
                    // Baris "Tanda Tangan dan Nama Jelas" TETAP sebagai teks (tidak diganti).
                    '$ Tanda Tangan' => '${signature}',
                ]
            ],
            [
                'src' => base_path('template/SK-1/BA Penilaian SK I sd SK III.docx'),
                'dst' => base_path('template/SK-1/BA Penilaian SK I sd SK III TEMPLATE.docx'),
                'replacements' => [
                    '$(tgl sidang)' => '${tgl_sidang}',
                    '$(waktu)' => '${waktu}',
                    '$(Nilai rata2)' => '${nilai_rata2}',
                    // Updated role‑specific placeholders
                    '$(Nama Ketua Pembimbing)'   => '${nama_ketua_pembimbing}',
                    '$(Nama Ko-Pembimbing 1)'    => '${nama_pembimbing_i}',
                    '$(Nama Ko-Pembimbing 2)'    => '${nama_pembimbing_ii}',
                    // Examiner placeholders (two examiners)
                    '$(Nama Penguji)'           => '${nama_penguji_i}',
                    '$(Nama Penguji 2)'         => '${nama_penguji_ii}',
                    '$(Nama kaprodi)' => '${nama_kaprodi}',
                    '$(nip kaprodi)' => '${nip_kaprodi}',
                    '$(nip kaprodi)' => '${nip_kaprodi}',
                ],
                'replaceSignatureDots' => true,
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

                    // Placeholder tanda tangan
                    'Tanda Tangan' => '${signature}',
                ]
            ],
            [
                'src' => base_path('template/SK-4/ba SK IV.docx'),
                'dst' => base_path('template/SK-4/ba SK IV TEMPLATE.docx'),
                'replacements' => [
                    '$(judul)' => '${judul}',
                    '$(nama mhs)' => '${nama_mhs}',
                    '$(nim)' => '${nim}',
                    // Role‑specific placeholders for Ketua Pembimbing and Ko‑Pembimbing (including leading spaces as in the source docx)
                    // Role‑specific placeholders for Ketua Pembimbing and Ko‑Pembimbing (including leading spaces as in the source docx)
                    '$(nama ketua pembimbing)'   => '${nama_ketua_pembimbing}',
                    '$( Ketua Pembimbing)'       => '${nama_ketua_pembimbing}',
                    '$(nama pembimbing I)'       => '${nama_pembimbing_i}',
                    '$( Nama Ko-Pembimbing 1)'   => '${nama_pembimbing_i}',
                    '$(nama pembimbing II)'      => '${nama_pembimbing_ii}',
                    '$( Nama Ko-Pembimbing 2)'   => '${nama_pembimbing_ii}',
                    // Examiner placeholders
                    '$(Nama Penguji)'            => '${nama_penguji_i}',
                    '$(Nama Penguji 2)'          => '${nama_penguji_ii}',
                    '$(Nama kaprodi)'            => '${nama_kaprodi}',
                    '$(nip kaprodi)'             => '${nip_kaprodi}',
                    '$(tgl sidang)'              => '${tgl_sidang}',
                    '$(nama ketua sidang)'       => '${nama_ketua_sidang}',
                    '$(nip ketua sidang)'        => '${nip_ketua_sidang}',
                ],
                'replaceSignatureDots' => true,
                'fixSk4PengujiRows'     => true,
            ],
            [
                'src' => base_path('template/surat Kesediaan Tim Penelaah Proposal.docx'),
                'dst' => base_path('template/surat Kesediaan Tim Penelaah Proposal TEMPLATE.docx'),
                'replacements' => [
                    // Nomor surat penelaah dari database (T_AJUAN_SIDANG.NO_SURAT_PENELAAH)
                    '2397/IT1.C05.1/DA.05/2023 ini ambil dari no surat penelaah' => '${no_surat_penelaah}',
                    '2397/IT1.C05.1/DA.05/2023' => '${no_surat_penelaah}',
                    '$(no surat penelaah)' => '${no_surat_penelaah}',
                    '$(tgl penelaah)' => '${tgl_penelaah}',
                    '$( nama penguji)' => '${nama_penguji}',
                    '$(institusi)' => '${institusi}',
                    '$(Nama Mahaiswa)' => '${nama_mahaiswa}',
                    '$(nim)' => '${nim}',
                    '$(Judul)' => '${judul}',
                    '$(pembimbing)' => '${pembimbing}',
                    '$(tgl hasil penelaahan)' => '${tgl_hasil_penelaahan}',
                    '$(dari tabel t user, status dekan \'y\')' => '${nama_wda}',
                    '$(nip)' => '${nip_wda}',
                    '$(email)' => '${email}',

                    // Placeholder tanda tangan dari database (t_user.SIGNATURE)
                    // diganti saat cetak menjadi gambar ttd penilai/pejabat terkait
                    'Tanda Tangan' => '${signature}',
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

                    // Placeholder tanda tangan dari database (t_user.SIGNATURE)
                    // diganti saat cetak menjadi gambar ttd penilai/pejabat terkait
                    'Tanda Tangan' => '${signature}',
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

            // Jika ada flag replaceSignatureDots, ganti pola titik-titik
            // yang menjadi placeholder tanda tangan di tabel
            if (!empty($template['replaceSignatureDots'])) {
                $xml = $this->replaceSignatureDots($xml);
            }

            // Khusus BA Sidang Akhir: dua slot tanda tangan yang BEDA
            // (kiri = kaprodi, kanan = ketua sidang)
            if (!empty($template['distinctBaSidangSignatures'])) {
                $xml = $this->distinguishBaSidangSignatures($xml);
            }

            // Khusus BA Sidang Akhir: konversi titik-titik pada kolom "Tanda Tangan"
            // di tabel Tim Penguji (6 baris) menjadi placeholder ${signature} per baris,
            // agar tiap penguji punya tanda tangan sendiri saat cetak.
            // DIJALANKAN SETELAH distinguish, agar ${signature} footer (kaprodi/ketua sidang)
            // tidak ikut kehitung sebagai baris tabel.
            if (!empty($template['baSidangRowSignatures'])) {
                $xml = $this->replaceBaSidangRowSignatures($xml);
            }

            // Khusus BA Sidang Akhir: konversi strip (--- / - --) pada 4 baris Capaian
            // Akademik (IP, Q1, bereputasi-1, bereputasi-2) menjadi placeholder
            // ${ip}/${jml_jurnal_q1}/${jml_jurnal_bereputasi1}/${jml_jurnal_bereputasi2},
            // agar diisi dari t_ajuan_sidang saat cetak.
            if (!empty($template['baSidangCapaian'])) {
                $xml = $this->replaceBaSidangCapaian($xml);
            }

            // Khusus BA Sidang Akhir: SOURCE memakai ${kotak_lulus} di ketiga baris
            // keputusan (LULUS / dan Rekomendasi Yudisium / Tidak Lulus). Agar saat
            // cetak hanya kotak yang sesuai dicentang, placeholder tiap baris dibuat
            // berbeda: baris Rekomendasi -> ${kotak_yudisium}, baris Tidak Lulus ->
            // ${kotak_tidak_lulus}; baris LULUS tetap ${kotak_lulus}. Placeholder
            // yang terpecah antar-run (${ ... kotak_lulus ... }) juga dirapikan.
            if (!empty($template['baSidangLulusBox'])) {
                $xml = $this->replaceBaSidangLulusBox($xml);
            }

            // Khusus SK-4: baris 4-5 tabel "Tim Penguji/Penilai" diberi label
            // (Penguji-1)/(Penguji-2) dan sel tanda tangan baris 5 dikosongkan.
            if (!empty($template['fixSk4PengujiRows'])) {
                $xml = $this->fixSk4PengujiRows($xml);
            }

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

            // Placeholder tanda tangan dari database (t_user.SIGNATURE)
            'Tanda tangan'                    => '${signature}',
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

    /**
     * Khusus BA Sidang Akhir: ubah 2 placeholder ${signature} yang identik
     * menjadi placeholder BEDA agar saat cetak bisa diisi tanda tangan
     * orang yang berbeda (kiri = kaprodi, kanan = ketua sidang).
     *
     * Urutan kemunculan di dokumen: slot kiri (Kaprodi) dulu, slot kanan (Ketua Sidang) kedua.
     */
    private function distinguishBaSidangSignatures(string $xml): string
    {
        $count = 0;
        $xml = preg_replace_callback('/\$\{signature\}/', function ($m) use (&$count) {
            $count++;
            return $count === 1 ? '${signature_kaprodi}' : '${signature_ketua_sidang}';
        }, $xml, 2);

        return $xml;
    }

    /**
     * Ganti pola titik-ttitik (........, ................., dll)
     * yang menjadi placeholder tanda tangan di tabel
     * dengan ${signature}.
     *
     * Pola yang diganti:
     * - ........ (8 titik) - signature penguji biasa
     * - ................. (15 titik) - signature penguji tambahan
     * - .......... (10 titik) - signature penguji
     * - ........... (11 titik) - signature penguji
     */
    private function replaceSignatureDots(string $xml): string
    {
        // Ganti HANYA dots pendek (≤9 karakter) di kolom TANDATANGAN dengan ${signature}.
        // Pattern signature di template: "……………….." (6×U+2026 + 2×U+002E = 8 chars).
        // Dots panjang (10+ chars) di kolom nama PENGUJI tidak boleh diganti.
        $xml = preg_replace_callback('/(<w:t[^>]*>)[\x{002E}\x{2026}]{3,9}(<\/w:t>)/u', function ($matches) {
            return $matches[1] . '${signature}' . $matches[2];
        }, $xml);

        return $xml;
    }

    /**
     * Khusus BA Sidang Akhir: konversi titik-titik pada kolom "Tanda Tangan"
     * di tabel Tim Penguji (kolom sel berlebar 2070) menjadi ${signature} per baris.
     * Hanya sel berlebar 2070 yang diubah, sehingga:
     *  - sel kurung NAMA penguji baris 5-6 (lebar 4084) tetap titik-titik,
     *    dipakai nanti oleh fillSidangListNameCell();
     *  - kolom keterangan (lebar 4457) tidak ikut terubah.
     */
    private function replaceBaSidangRowSignatures(string $xml): string
    {
        return preg_replace_callback('/<w:tc>(.*?)<\/w:tc>/s', function ($matches) {
            $cell = $matches[0];

            // Identifikasi sel kolom signature pada tabel Tim Penguji:
            // tcPr memuat <w:tcW w:w="2070"
            if (strpos($cell, '<w:tcW w:w="2070"') === false) {
                return $cell;
            }

            // Ganti konten titik-titik (3+ karakter, U+002E/U+2026) dalam <w:t>.
            return preg_replace_callback('/(<w:t[^>]*>)[\x{002E}\x{2026}]{3,}(<\/w:t>)/u', function ($m) {
                return $m[1] . '${signature}' . $m[2];
            }, $cell);
        }, $xml);
    }

    /**
     * Khusus BA Sidang Akhir: konversi strip pada 4 baris "Capaian akademik"
     * (IP, jurnal Q1, bereputasi-1, bereputasi-2) menjadi placeholder per baris.
     * Strip bisa terpecah antar-run (mis. "-" + "--"), sehingga digabung:
     * run strip pertama dalam satu grup diisi placeholder, run lanjutan dikosongkan.
     */
    private function replaceBaSidangCapaian(string $xml): string
    {
        $capStart = strpos($xml, 'Capaian');
        if ($capStart === false) {
            return $xml;
        }
        $capEnd = strpos($xml, 'Penguji/', $capStart);
        if ($capEnd === false) {
            $capEnd = strlen($xml);
        }

        $section = substr($xml, $capStart, $capEnd - $capStart);

        $placeholders = [
            '${ip}',
            '${jml_jurnal_q1}',
            '${jml_jurnal_bereputasi1}',
            '${jml_jurnal_bereputasi2}',
        ];
        $idx = 0;
        $inGroup = false;

        $section = preg_replace_callback(
            '/(<w:t[^>]*>)([^<]*)(<\/w:t>)/u',
            function ($m) use ($placeholders, &$idx, &$inGroup) {
                $content = $m[2];
                $isDash = preg_match('/^\s*[\x{002D}]+\s*$/u', $content);
                if (!$isDash) {
                    $inGroup = false;
                    return $m[0];
                }
                if ($inGroup) {
                    return $m[1] . '' . $m[3];
                }
                $inGroup = true;
                if ($idx < count($placeholders)) {
                    return $m[1] . $placeholders[$idx++] . $m[3];
                }
                return $m[0];
            },
            $section
        );

        return substr_replace($xml, $section, $capStart, $capEnd - $capStart);
    }

    /**
     * Khusus BA Sidang Akhir: pembeda baris kotak keputusan kelulusan.
     * SOURCE memakai ${kotak_lulus} di 3 baris (LULUS / dan Rekomendasi
     * Yudisium / Tidak Lulus). Di sini placeholder di-rename per baris agar
     * saat cetak hanya kotak yang sesuai yang dicentang:
     *   - baris "LULUS Sidang Doktor"      -> ${kotak_lulus}
     *   - baris "dan Rekomendasi Yudisium" -> ${kotak_yudisium}
     *   - baris "Tidak Lulus Sidang"       -> ${kotak_tidak_lulus}
     * Placeholder ${kotak_lulus} yang terpecah antar-run (${ ... kotak_lulus
     * ... } karena <w:proofErr> di tengah) dirapikan menjadi satu run.
     * Titik-titik pada baris "dan Rekomendasi Yudisium: …………" diganti
     * ${rekomendasi_yudisium} agar diisi dari DB saat cetak.
     */
    private function replaceBaSidangLulusBox(string $xml): string
    {
        // Rapikan placeholder terpecah antar-run pada seluruh dokumen lebih dulu:
        // <w:t>${</w:t> ... <w:t>kotak_lulus</w:t> ... <w:t>}</w:t> -> kabungkan
        // bagian "${" dan "}" agar menjadi satu run berisi ${kotak_lulus}.
        $xml = $this->mergeSplitKotakLulus($xml);

        // Rename placeholder per baris (hanya paragraf yang memuat kata kunci).
        $variants = [
            '${kotak_yudisium}'    => ['Rekomendasi'],
            '${kotak_tidak_lulus}' => ['Tidak Lulus Sidang'],
        ];
        foreach ($variants as $placeholder => $keywords) {
            $xml = preg_replace_callback(
                '/<w:p\b[^>]*>.*?<\/w:p>/s',
                function ($m) use ($keywords, $placeholder) {
                    $p = $m[0];
                    foreach ($keywords as $kw) {
                        if (strpos($p, $kw) !== false) {
                            $p = preg_replace(
                                '/(<w:t[^>]*>)\$\{kotak_lulus\}(<\/w:t>)/',
                                '$1' . $placeholder . '$2',
                                $p,
                                1
                            );
                            break;
                        }
                    }
                    return $p;
                },
                $xml
            );
        }

        // Ganti titik-titik pada baris "dan Rekomendasi Yudisium: …………"
        // menjadi placeholder ${rekomendasi_yudisium}, agar saat cetak diisi
        // dari kolom REKOMENDASI_YUDISIUM di t_ajuan_sidang. Dots bisa berupa
        // U+002E (.) berulang atau U+2026 (…) berulang di akhir w:t.
        $xml = preg_replace_callback(
            '/<w:p\b[^>]*>.*?<\/w:p>/s',
            function ($m) {
                $p = $m[0];
                if (strpos($p, 'Rekomendasi Yudisium') === false) {
                    return $p;
                }
                return preg_replace(
                    '/(<w:t[^>]*>)([^<]*?)[\x{002E}\x{2026}]{3,}(<\/w:t>)/u',
                    '$1$2${rekomendasi_yudisium}$3',
                    $p,
                    1
                );
            },
            $xml
        );

        // Perbarui komentar fungsi agar mencakup placeholder rekomendasi.
        return $xml;
    }

    /**
     * Gabungkan placeholder kotak_lulus yang terpecah antar-run.
     * Word bisa menyisipkan <w:proofErr> di tengah teks sehingga w:t menjadi
     * berturut-turut: "${" | "kotak_lulus" | "}". Semua bagian digabung ke
     * run pertama menjadi "${kotak_lulus}", sisanya dikosongkan.
     */
    private function mergeSplitKotakLulus(string $xml): string
    {
        return preg_replace_callback(
            '/(<w:t[^>]*>)\$\{(<\/w:t>)(.*?)(<w:r\b[^>]*><w:rPr>.*?<\/w:rPr><w:t[^>]*>)kotak_lulus(<\/w:t>)(.*?)(<w:r\b[^>]*><w:rPr>.*?<\/w:rPr><w:t[^>]*>)\}(<\/w:t>)/s',
            function ($m) {
                return $m[1] . '${kotak_lulus}' . $m[2] . $m[3]
                    . $m[4] . '' . $m[5] . $m[6] . $m[7] . '' . $m[8];
            },
            $xml,
            1
        );
    }

    /**
     * Khusus BA SK IV: rapikan baris 4-5 tabel "Tim Penguji/Penilai".
     *  - baris ke-4 label "(Penguji)" -> "(Penguji-1)"
     *  - baris ke-5 label "(Penguji)" -> "(Penguji-2)" dan sel tanda tangan dikosongkan
     *    (sesuai format asli: baris 5 tidak punya kolom tandatangan).
     */
    private function fixSk4PengujiRows(string $xml): string
    {
        // Previously this function renamed (Penguji) to (Penguji-1)/(Penguji-2) and cleared the signature for the second row.
        // The current requirement is to keep the original labels and retain the signature placeholders for both examiner rows.
        // Therefore, we simply return the XML unchanged.
        return $xml;
    }
}