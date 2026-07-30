<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PrepareTemplate extends Command
{
    protected $signature = 'template:prepare';
    protected $description = 'Create placeholder template for cetak form';

    public function handle()
    {
        $src = base_path('template/pROPOSAL/Form. 302.3 Penilaian Proposal Penelitian Disertasi rev Aditya.docx');
        $dst = base_path('template/pROPOSAL/Form. 302.3 TEMPLATE.docx');

        if (!file_exists($src)) {
            $this->error('Source template not found: ' . $src);
            return 1;
        }

        copy($src, $dst);

        $zip = new \ZipArchive();
        if ($zip->open($dst) !== true) {
            $this->error('Cannot open zip');
            return 1;
        }

        $xml = $zip->getFromName('word/document.xml');

        $replacements = [
            'Pengaruh Alterasi Hidrotermal terhadap Sifat Magnetik dari Sedimen Permukaan Danau: Studi Kasus Danau Batur Bali.' => '${judul}',
            'Ulvienin Harlianti' => '${nama_mhs}',
            '32322004' => '${nim}',
            'Prof. Dr. Satria Bijaksana.' => '${pembimbing_utama}',
            'Dr. Irwan Iskandar' => '${pembimbing_1}',
            '----' => '${pembimbing_2}',
            '_______________ (jumlah total dibagi 5)' => '${rata_nilai}',
            'Diagram alur yang digunakan masih tidak sesuai dengan deskripsi dan ada kesalahan. Selain itu juga ada diagram alur yang membingungkan. Masih banyak ketidak konsitenan dalam penulisan. Komentar untuk perbaikan lebih detail dituliskan di formulir di bawah.' => '${catatan}',
        ];

        foreach ($replacements as $search => $replace) {
            $xml = str_replace($search, $replace, $xml);
        }

        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        $this->info('Placeholder template created: ' . $dst);
        return 0;
    }
}
