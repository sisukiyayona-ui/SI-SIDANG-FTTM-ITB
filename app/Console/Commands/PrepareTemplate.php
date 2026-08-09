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
                    '_______________ (jumlah total dibagi 5)' => '${rata_nilai}',
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
                    '$(tgl hasil penelaahan)' => '${tgl_hasil_penelaahan}',
                    '$(dari tabel t user, status dekan \'y\')' => '${dari_tabel_t_user_status_dekan_y}',
                    '$(nip)' => '${nip}',
                ]
            ],
            [
                'src' => base_path('template/undangan seminar kemajuan.docx'),
                'dst' => base_path('template/undangan seminar kemajuan TEMPLATE.docx'),
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
                    '$(nama tim sidang ketua sidang)' => '${nama_tim_sidang_ketua_sidang}'
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

                $xml = preg_replace_callback($regex, function($matches) use ($replace) {
                    $match = $matches[0];
                    $first = true;
                    // Extract text parts and replace only the first one, empty the rest
                    $replaced = preg_replace_callback('/>([^<]+)</', function($m) use (&$first, $replace) {
                        if ($first) {
                            $first = false;
                            return '>' . $replace . '<';
                        }
                        return '><';
                    }, '>' . $match . '<');
                    return substr($replaced, 1, -1);
                }, $xml);
            }

            $zip->addFromString('word/document.xml', $xml);
            $zip->close();

            $this->info('Placeholder template created: ' . $dst);
        }

        return 0;
    }
}