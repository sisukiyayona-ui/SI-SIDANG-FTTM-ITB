<?php

namespace App\Data;

class DummySidang
{
    public static function ujianKualifikasi(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Muhammad Rizky', 'nim' => '12221001', 'prodi' => 'Teknik Perminyakan', 'tanggal' => '2026-07-15', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
            ['id' => 2, 'mahasiswa' => 'Aulia Rahman', 'nim' => '12221002', 'prodi' => 'Teknik Metalurgi', 'tanggal' => '2026-07-16', 'ruang' => 'Ruang Sidang B', 'status' => 'Terjadwal'],
            ['id' => 3, 'mahasiswa' => 'Rina Wijaya', 'nim' => '12221003', 'prodi' => 'Teknik Geologi', 'tanggal' => '2026-07-17', 'ruang' => 'Ruang Sidang A', 'status' => 'Selesai'],
            ['id' => 4, 'mahasiswa' => 'Bambang Susilo', 'nim' => '12221004', 'prodi' => 'Teknik Geofisika', 'tanggal' => '2026-07-18', 'ruang' => 'Ruang Sidang C', 'status' => 'Terjadwal'],
        ];
    }

    public static function sidangProposal(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Muhammad Rizky', 'nim' => '12221001', 'prodi' => 'Teknik Perminyakan', 'judul' => 'Optimasi Produksi Minyak Bumi', 'tanggal' => '2026-08-10', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
            ['id' => 2, 'mahasiswa' => 'Aulia Rahman', 'nim' => '12221002', 'prodi' => 'Teknik Metalurgi', 'judul' => 'Karakterisasi Bahan Logam', 'tanggal' => '2026-08-11', 'ruang' => 'Ruang Sidang B', 'status' => 'Terjadwal'],
            ['id' => 3, 'mahasiswa' => 'Rina Wijaya', 'nim' => '12221003', 'prodi' => 'Teknik Geologi', 'judul' => 'Analisis Struktur Batuan', 'tanggal' => '2026-08-12', 'ruang' => 'Ruang Sidang A', 'status' => 'Selesai'],
            ['id' => 4, 'mahasiswa' => 'Bambang Susilo', 'nim' => '12221004', 'prodi' => 'Teknik Geofisika', 'judul' => 'Interpretasi Data Seismik', 'tanggal' => '2026-08-13', 'ruang' => 'Ruang Sidang C', 'status' => 'Terjadwal'],
        ];
    }

    public static function seminarKemajuanI(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Muhammad Rizky', 'nim' => '12221001', 'prodi' => 'Teknik Perminyakan', 'bab' => 'Bab 1-2', 'tanggal' => '2026-09-05', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
            ['id' => 2, 'mahasiswa' => 'Aulia Rahman', 'nim' => '12221002', 'prodi' => 'Teknik Metalurgi', 'bab' => 'Bab 1-2', 'tanggal' => '2026-09-06', 'ruang' => 'Ruang Sidang B', 'status' => 'Selesai'],
            ['id' => 3, 'mahasiswa' => 'Bambang Susilo', 'nim' => '12221004', 'prodi' => 'Teknik Geofisika', 'bab' => 'Bab 1-2', 'tanggal' => '2026-09-07', 'ruang' => 'Ruang Sidang C', 'status' => 'Terjadwal'],
        ];
    }

    public static function seminarKemajuanII(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Muhammad Rizky', 'nim' => '12221001', 'prodi' => 'Teknik Perminyakan', 'bab' => 'Bab 3', 'tanggal' => '2026-10-10', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
            ['id' => 2, 'mahasiswa' => 'Aulia Rahman', 'nim' => '12221002', 'prodi' => 'Teknik Metalurgi', 'bab' => 'Bab 3', 'tanggal' => '2026-10-11', 'ruang' => 'Ruang Sidang B', 'status' => 'Terjadwal'],
        ];
    }

    public static function seminarKemajuanIII(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Muhammad Rizky', 'nim' => '12221001', 'prodi' => 'Teknik Perminyakan', 'bab' => 'Bab 4', 'tanggal' => '2026-11-15', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
        ];
    }

    public static function seminarKemajuanIV(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Rina Wijaya', 'nim' => '12221003', 'prodi' => 'Teknik Geologi', 'bab' => 'Bab 4-5', 'tanggal' => '2026-12-05', 'ruang' => 'Ruang Sidang A', 'status' => 'Selesai'],
        ];
    }

    public static function sidangAkhir(): array
    {
        return [
            ['id' => 1, 'mahasiswa' => 'Rina Wijaya', 'nim' => '12221003', 'prodi' => 'Teknik Geologi', 'judul' => 'Analisis Struktur Batuan dan Implikasinya', 'tanggal' => '2027-01-20', 'ruang' => 'Ruang Sidang A', 'status' => 'Terjadwal'],
            ['id' => 2, 'mahasiswa' => 'Aulia Rahman', 'nim' => '12221002', 'prodi' => 'Teknik Metalurgi', 'judul' => 'Karakterisasi Bahan Logam untuk Aplikasi Industri', 'tanggal' => '2027-01-22', 'ruang' => 'Ruang Sidang B', 'status' => 'Terjadwal'],
        ];
    }

    public static function statistics(): array
    {
        return [
            'total_mahasiswa' => 150,
            'total_sidang' => 45,
            'total_seminar' => 89,
            'total_penguji' => 28,
            'mahasiswa_aktif' => 120,
            'sidang_selesai' => 30,
            'seminar_berjalan' => 45,
        ];
    }

    public static function recentActivity(): array
    {
        return [
            ['user' => 'Muhammad Rizky', 'activity' => 'Mendaftar Ujian Kualifikasi', 'time' => '2 jam yang lalu'],
            ['user' => 'Aulia Rahman', 'activity' => 'Sidang Proposal telah dijadwalkan', 'time' => '5 jam yang lalu'],
            ['user' => 'Dr. Ahmad Fauzi', 'activity' => 'Mengisi nilai Seminar Kemajuan I', 'time' => '1 hari yang lalu'],
            ['user' => 'Rina Wijaya', 'activity' => 'Mengunggah revisi Sidang Akhir', 'time' => '2 hari yang lalu'],
            ['user' => 'Prof. Siti Rahayu', 'activity' => 'Bergabung sebagai Penguji', 'time' => '3 hari yang lalu'],
        ];
    }

    public static function reportRekapSidang(): array
    {
        return [
            ['bulan' => 'Jan', 'sidang' => 3, 'seminar' => 5],
            ['bulan' => 'Feb', 'sidang' => 4, 'seminar' => 6],
            ['bulan' => 'Mar', 'sidang' => 5, 'seminar' => 8],
            ['bulan' => 'Apr', 'sidang' => 3, 'seminar' => 7],
            ['bulan' => 'Mei', 'sidang' => 6, 'seminar' => 9],
            ['bulan' => 'Jun', 'sidang' => 4, 'seminar' => 6],
        ];
    }

    public static function reportKelulusan(): array
    {
        return [
            ['prodi' => 'Teknik Perminyakan', 'lulus' => 25, 'tidak_lulus' => 3],
            ['prodi' => 'Teknik Metalurgi', 'lulus' => 20, 'tidak_lulus' => 2],
            ['prodi' => 'Teknik Geologi', 'lulus' => 22, 'tidak_lulus' => 4],
            ['prodi' => 'Teknik Geofisika', 'lulus' => 18, 'tidak_lulus' => 1],
        ];
    }

    public static function reportMahasiswa(): array
    {
        return [
            ['prodi' => 'Teknik Perminyakan', 'aktif' => 45, 'cuti' => 3, 'lulus' => 25],
            ['prodi' => 'Teknik Metalurgi', 'aktif' => 35, 'cuti' => 2, 'lulus' => 20],
            ['prodi' => 'Teknik Geologi', 'aktif' => 40, 'cuti' => 4, 'lulus' => 22],
            ['prodi' => 'Teknik Geofisika', 'aktif' => 30, 'cuti' => 1, 'lulus' => 18],
        ];
    }

    public static function persyaratan(): array
    {
        return [
            ['id' => 1, 'nama' => 'Transkrip Nilai', 'keterangan' => 'Transkrip nilai terbaru', 'wajib' => true],
            ['id' => 2, 'nama' => 'Kartu Rencana Studi', 'keterangan' => 'KRS semester berjalan', 'wajib' => true],
            ['id' => 3, 'nama' => 'Draft Proposal', 'keterangan' => 'Draft proposal TA', 'wajib' => true],
            ['id' => 4, 'nama' => 'Lembar Bimbingan', 'keterangan' => 'Lembar bimbingan dari pembimbing', 'wajib' => true],
            ['id' => 5, 'nama' => 'Sertifikat TOEFL', 'keterangan' => 'Sertifikat TOEFL minimal 450', 'wajib' => false],
            ['id' => 6, 'nama' => 'Bukti Seminar', 'keterangan' => 'Sertifikat seminar yang pernah diikuti', 'wajib' => false],
        ];
    }

    public static function penilaian(): array
    {
        return [
            ['id' => 1, 'nama' => 'Naskah Proposal', 'bobot' => 20, 'komponen' => 'Sidang Proposal'],
            ['id' => 2, 'nama' => 'Presentasi', 'bobot' => 30, 'komponen' => 'Sidang Proposal'],
            ['id' => 3, 'nama' => 'Penguasaan Materi', 'bobot' => 30, 'komponen' => 'Sidang Proposal'],
            ['id' => 4, 'nama' => 'Penguasaan Metodologi', 'bobot' => 20, 'komponen' => 'Sidang Proposal'],
            ['id' => 5, 'nama' => 'Kemajuan Penelitian', 'bobot' => 40, 'komponen' => 'Seminar Kemajuan'],
            ['id' => 6, 'nama' => 'Presentasi Seminar', 'bobot' => 30, 'komponen' => 'Seminar Kemajuan'],
            ['id' => 7, 'nama' => 'Laporan Akhir', 'bobot' => 40, 'komponen' => 'Sidang Akhir'],
            ['id' => 8, 'nama' => 'Sidang Akhir', 'bobot' => 40, 'komponen' => 'Sidang Akhir'],
            ['id' => 9, 'nama' => 'Publikasi', 'bobot' => 20, 'komponen' => 'Sidang Akhir'],
        ];
    }
}
