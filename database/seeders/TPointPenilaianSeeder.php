<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TPointPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('t_point_penilaian')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $prodi = DB::table('t_prodi')->where('KODE_PRODI', '322')->first();
        $now = now();

        $data = [
            // Tahap I (no form: 1)
            ['tahap I', '1', 'Nilai Mata Kuliah Tahap Persiapan', null, null],
            // Tahap II (no form 302.3)
            ['tahap II', '302.3', 'Kebaruan dan Kualitas Topik Penelitian', 't', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'],
            ['tahap II', '302.3', 'Kejelasan Pemaparan Latar Belakang', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'],
            ['tahap II', '302.3', 'Kejelasan Pemaparan Hipotesis/Tujuan', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik'],
            ['tahap II', '302.3', 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'],
            ['tahap II', '302.3', 'Format Penulisan Proposal', 't', 'Kajian literatur dituliskan secara rinci dengan analisis yang komprehensif dan kritis, kesesuaian format/layout, penggunaan tata bahasa baku, kejelasan informasi gambar dan tabel, kerunutan penulisan referensi dan daftar pustaka'],
            ['tahap II', '302.3', 'Usulan Perbaikan', null, null],
            // SK I (no form 304.1)
            ['SK I', '304.1', 'Kreatifitas dan Keuletan', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'],
            ['SK I', '304.1', 'Keberhasilan Penelitian', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'],
            ['SK I', '304.1', 'Penulisan Laporan Kemajuan', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'],
            ['SK I', '304.1', 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', null, null],
            ['SK I', '304.1', 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', null, null],
            // SK II (no form 304.2)
            ['SK II', '304.2', 'Kreatifitas dan Keuletan', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'],
            ['SK II', '304.2', 'Keberhasilan Penelitian', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'],
            ['SK II', '304.2', 'Penulisan Laporan Kemajuan', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'],
            ['SK II', '304.2', 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', null, null],
            ['SK II', '304.2', 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', null, null],
            // SK III (no form 304.3)
            ['SK III', '304.3', 'Kreatifitas dan Keuletan', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'],
            ['SK III', '304.3', 'Keberhasilan Penelitian', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal. Capaian luaran publikasi jurnal internasional. Jika status: Accepted: 5, Revisi: 4, Under Review: 3, Submitted: 2, Draft: 1'],
            ['SK III', '304.3', 'Penulisan Laporan Kemajuan', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'],
            ['SK III', '304.3', 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', null, null],
            ['SK III', '304.3', 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', null, null],
            // SK IV (no form 306.2)
            ['SK IV', '306.2', 'Kebaruan dan Kualitas Topik Penelitian', 't', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'],
            ['SK IV', '306.2', 'Kejelasan Pemaparan Latar Belakang Masalah', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'],
            ['SK IV', '306.2', 'Kejelasan Pemaparan Hipotesis/Tujuan', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik dan jelas'],
            ['SK IV', '306.2', 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'],
            ['SK IV', '306.2', 'Format Penulisan Disertasi', 't', 'Kajian literatur dituliskan rinci dengan analisis yang komprehensif dan kritis, format/layout rapi, menggunakan tata bahasa yang baku, gambar dan tabel jelas, penulisan referensi dan daftar pustaka runut dan memenuhi kaidah penulisan disertasi di ITB'],
            // Tahap IV (no form 309.1)
            ['tahap IV', '309.1', 'Penguasaan Materi', 't', 'Pemahaman materi yang disampaikan, kejelasan materi yang disampaikan'],
            ['tahap IV', '309.1', 'Keberhasilan Penelitian', 't', 'Ketercapaian target luaran publikasi pada jurnal internasional bereputasi'],
            ['tahap IV', '309.1', 'Kemampuan Berkomunikasi/Presentasi', 't', 'Organisasi presentasi, kemampuan mengkomunikasikan gagasan'],
            ['tahap IV', '309.1', 'Tanya Jawab', 't', 'Kemampuan menyerap pertanyaan dan menjawab pertanyaan secara efisien dan efektif'],
            ['tahap IV', '309.1', 'Penulisan Disertasi', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'],
        ];

        foreach ($data as $d) {
            DB::table('t_point_penilaian')->insert([
                'PENILAIAN' => $d[2],
                'NO_FORM' => $d[1],
                'ID_PRODI' => $prodi->id,
                'KODE_PRODI' => '322',
                'NAMA_PRODI' => 'Teknik Perminyakan',
                'TAHAPAN_SIDANG' => $d[0],
                'STATUS_AKTIF' => 'AKTIF',
                'STRATA' => 'S3',
                'STATUS_CATATAN' => $d[3],
                'KETERANGAN' => $d[4],
                'TGL_CREATE' => $now,
                'TGL_UPDATE' => $now,
            ]);
        }
    }
}
