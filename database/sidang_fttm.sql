-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2026 at 03:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sidang_fttm`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '2026_07_01_000001_create_t_prodi_table', 1),
(4, '2026_07_01_000002_create_t_user_table', 1),
(5, '2026_07_01_000003_create_t_tahapan_table', 1),
(6, '2026_07_01_000004_create_t_syarat_sidang_table', 1),
(7, '2026_07_01_000005_create_t_point_penilaian_table', 1),
(8, '2026_07_01_000006_create_t_judul_table', 1),
(9, '2026_07_01_000007_create_t_judul_temp_table', 1),
(10, '2026_07_01_000008_create_t_ajuan_sidang_table', 1),
(11, '2026_07_01_000009_create_t_sk_table', 1),
(12, '2026_07_01_000010_create_t_tim_sidang_table', 1),
(13, '2026_07_01_000011_create_t_cek_persyaratan_table', 1),
(14, '2026_07_01_000012_create_t_penilaian_table', 1),
(15, '2026_07_22_133705_create_notifications_table', 1),
(17, '2026_07_25_175724_add_nilai_terkunci_to_t_ajuan_sidang_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_user` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('hkrN4ErOZRtOmJpBCKEfsNe1zJxVW0rqsogULqdE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUp3bU03WjlFc1lqUFBUVTRTa2tPRUlOYUVOYVU4YW5mdDRuVnpOYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zaWRhbmcvczMiO3M6NToicm91dGUiO3M6OToic2lkYW5nLnMzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxNToiYWN0aXZlX2p1ZHVsX2lkIjtpOjI7czo5OiJhdXRoX3VzZXIiO2E6MTI6e3M6MjoiaWQiO2k6NDtzOjEyOiJuYW1hX2xlbmdrYXAiO3M6MTA6IlBlbWJpbWJpbmciO3M6NzoibmlwX25pbSI7czoxODoiMTk3ODAzMTUyMDAyMTIxMDAxIjtzOjU6ImVtYWlsIjtzOjI1OiJwZW1iaW1iaW5nQGZ0dG0uaXRiLmFjLmlkIjtzOjg6IlVzZXJuYW1lIjtzOjEwOiJwZW1iaW1iaW5nIjtzOjQ6InJvbGUiO3M6MTA6IlBlbWJpbWJpbmciO3M6Njoic3RyYXRhIjtOO3M6MTA6ImtvZGVfcHJvZGkiO3M6MzoiMzIyIjtzOjEwOiJuYW1hX3Byb2RpIjtzOjE4OiJUZWtuaWsgUGVybWlueWFrYW4iO3M6NjoiYXZhdGFyIjtzOjgwOiJodHRwczovL3VpLWF2YXRhcnMuY29tL2FwaS8/bmFtZT1QZW1iaW1iaW5nJmJhY2tncm91bmQ9MmY1NTk3JmNvbG9yPWZmZiZzaXplPTEyOCI7czo4OiJha3VuX2luYSI7TjtzOjY6InN0YXR1cyI7czo4OiJhcHByb3ZlZCI7fX0=', 1785373970),
('N6yUQghvn14xveu9OYTMZFpPBluKnE0VaOT7e9X7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWJwZEgyWjJmRHhZdGw3bXlRUlJPZnphY0RWSjR0RXNuMDhKV3lBcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1785373294);

-- --------------------------------------------------------

--
-- Table structure for table `t_ajuan_sidang`
--

CREATE TABLE `t_ajuan_sidang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ID_USER` bigint(20) UNSIGNED NOT NULL,
  `NIM` varchar(50) NOT NULL,
  `NAMA_MHS` varchar(250) NOT NULL,
  `ANGKATAN` int(11) NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED NOT NULL,
  `JUDUL` varchar(500) NOT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `STRATA` varchar(10) NOT NULL,
  `TGL_SIDANG` date DEFAULT NULL,
  `WAKTU_SIDANG` time DEFAULT NULL,
  `RUANG_SIDANG` varchar(250) DEFAULT NULL,
  `STATUS_LULUS` varchar(50) DEFAULT NULL,
  `NILAI_TERKUNCI` char(1) NOT NULL DEFAULT 't',
  `STATUS_SUBMIT` char(1) NOT NULL DEFAULT 't',
  `STATUS_AJUKAN_MHS` char(1) DEFAULT NULL,
  `NO_UNDANGAN` varchar(250) DEFAULT NULL,
  `STATUS_AJUKAN_PRODI` char(1) DEFAULT NULL,
  `NO_BA_SIDANG` varchar(250) DEFAULT NULL,
  `SK_LULUS` varchar(250) DEFAULT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `ID_USER_CREATE` bigint(20) UNSIGNED NOT NULL,
  `NAMA_USER_CREATE` varchar(250) NOT NULL,
  `THN_CREATE` int(11) NOT NULL,
  `ID_PRODI` bigint(20) UNSIGNED NOT NULL,
  `KODE_PRODI` varchar(50) NOT NULL,
  `NAMA_PRODI` varchar(250) NOT NULL,
  `TGL_UNDANGAN` date DEFAULT NULL,
  `TGL_PENGUMPULAN` date DEFAULT NULL,
  `TGL_PENELAAH` date DEFAULT NULL,
  `NO_SURAT_PENELAAH` varchar(250) DEFAULT NULL,
  `EMAIL_SURAT` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_ajuan_sidang`
--

INSERT INTO `t_ajuan_sidang` (`id`, `ID_USER`, `NIM`, `NAMA_MHS`, `ANGKATAN`, `ID_JUDUL`, `JUDUL`, `TAHAPAN_SIDANG`, `STRATA`, `TGL_SIDANG`, `WAKTU_SIDANG`, `RUANG_SIDANG`, `STATUS_LULUS`, `NILAI_TERKUNCI`, `STATUS_SUBMIT`, `STATUS_AJUKAN_MHS`, `NO_UNDANGAN`, `STATUS_AJUKAN_PRODI`, `NO_BA_SIDANG`, `SK_LULUS`, `TGL_CREATE`, `TGL_UPDATE`, `ID_USER_CREATE`, `NAMA_USER_CREATE`, `THN_CREATE`, `ID_PRODI`, `KODE_PRODI`, `NAMA_PRODI`, `TGL_UNDANGAN`, `TGL_PENGUMPULAN`, `TGL_PENELAAH`, `NO_SURAT_PENELAAH`, `EMAIL_SURAT`) VALUES
(1, 7, '32322004', 'Ade', 2023, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', 'tahap I', 'S3', '2026-07-22', '10:00:00', 'Ruang Seminar 1', 'lulus', 'y', 'y', 'y', NULL, 'y', NULL, NULL, '2026-07-26', '2026-07-28', 2, 'Dede', 2024, 1, '322', 'Teknik Perminyakan', NULL, NULL, NULL, NULL, NULL),
(2, 2, '32322004', 'Ade', 2026, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', 'tahap II', 'S3', '2026-07-26', '17:27:00', 'Ruang 1', 'lulus', 'y', 'y', 'y', NULL, 'y', NULL, NULL, '2026-07-26', '2026-07-29', 2, 'Dede', 2026, 1, '322', 'Teknik Perminyakan', '2026-07-26', '2026-07-26', '2026-07-26', 'dsds', 'test@gmail.com'),
(3, 7, '32322004', 'Ade', 2026, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', 'SK I', 'S3', '2026-07-29', '22:10:00', 'Ruang 1', 'lulus', 'y', 't', 'y', NULL, 'y', NULL, NULL, '2026-07-29', '2026-07-29', 2, 'Dede', 2026, 1, '322', 'Teknik Perminyakan', '2026-07-29', '2026-07-29', '2026-07-29', 'dsds', 'coba@gmail.com'),
(4, 7, '32322004', 'Ade', 2026, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', 'SK II', 'S3', '2026-07-29', '22:12:00', 'Ruang 1', 'lulus', 'y', 't', 'y', NULL, 'y', NULL, NULL, '2026-07-29', '2026-07-29', 2, 'Dede', 2026, 1, '322', 'Teknik Perminyakan', '2026-07-29', '2026-07-29', '2026-07-29', 'dsds', 'coba@gmail.com'),
(5, 39, '231401001', 'Ahmad Fauzan', 2026, 2, 'test buat mahasiswa 2', 'tahap I', 'S3', NULL, NULL, NULL, 'diajukan', 't', 't', 't', NULL, 'y', NULL, NULL, '2026-07-30', '2026-07-30', 39, 'Ahmad Fauzan', 2026, 2, '323', 'Teknik Geofisika', NULL, NULL, NULL, NULL, NULL),
(6, 39, '231401001', 'Ahmad Fauzan', 2019, 2, 'test buat mahasiswa 2', 'tahap II', 'S3', NULL, NULL, NULL, NULL, 't', 't', 't', NULL, 't', NULL, NULL, '2026-07-30', '2026-07-30', 38, 'Rina Kartika, S.T., M.T.', 2026, 2, '323', 'Teknik Geofisika', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_cek_persyaratan`
--

CREATE TABLE `t_cek_persyaratan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED NOT NULL,
  `ID_SYARAT_SIDANG` bigint(20) UNSIGNED NOT NULL,
  `PERSYARATAN` varchar(250) NOT NULL,
  `STATUS_LENGKAP` char(1) NOT NULL,
  `LINK_FILE` varchar(2000) DEFAULT NULL,
  `TGL_BUAT` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_cek_persyaratan`
--

INSERT INTO `t_cek_persyaratan` (`id`, `TAHAPAN_SIDANG`, `ID_JUDUL`, `ID_SYARAT_SIDANG`, `PERSYARATAN`, `STATUS_LENGKAP`, `LINK_FILE`, `TGL_BUAT`, `TGL_UPDATE`) VALUES
(8, 'tahap II', 1, 2, 'Lulus ujian persiapan (tahap I)', 'y', '/storage/uploads/persyaratan/1785253815_pdf-data-civitas-akademika-tm-itbdocx_compress.pdf', '2026-07-28', '2026-07-29'),
(9, 'tahap II', 1, 3, 'Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing', 'y', '/storage/uploads/persyaratan/1785331792_pdf-data-civitas-akademika-tm-itbdocx_compress.pdf', '2026-07-28', '2026-07-29'),
(10, 'tahap II', 1, 4, 'Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing', 'y', NULL, '2026-07-28', '2026-07-29'),
(11, 'tahap I', 1, 1, 'Mengikuti mata kuliah tahap persiapan', 'y', '/storage/uploads/persyaratan/1785253833_pdf-data-civitas-akademika-tm-itbdocx_compress.pdf', '2026-07-28', '2026-07-28'),
(12, 'SK I', 1, 5, 'Lulus ujian proposal', 'y', NULL, '2026-07-29', '2026-07-29'),
(13, 'SK I', 1, 6, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 'y', NULL, '2026-07-29', '2026-07-29'),
(14, 'SK I', 1, 7, 'Menyerahkan makalah/slide presentasi', 'y', NULL, '2026-07-29', '2026-07-29'),
(15, 'SK II', 1, 8, 'Menyerahkan laporan kemajuan I dan II', 'y', NULL, '2026-07-29', '2026-07-29'),
(16, 'SK II', 1, 9, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 'y', NULL, '2026-07-29', '2026-07-29'),
(17, 'SK II', 1, 10, 'Menyerahkan makalah/slide presentasi', 'y', NULL, '2026-07-29', '2026-07-29'),
(19, 'tahap I', 2, 83, 'Mengikuti mata kuliah tahap persiapan', 'y', NULL, '2026-07-30', '2026-07-30'),
(20, 'tahap II', 2, 84, 'Lulus ujian persiapan (tahap I)', 'y', NULL, '2026-07-30', '2026-07-30'),
(21, 'tahap II', 2, 85, 'Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing', 't', NULL, '2026-07-30', '2026-07-30'),
(22, 'tahap II', 2, 86, 'Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing', 't', NULL, '2026-07-30', '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `t_judul`
--

CREATE TABLE `t_judul` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `JUDUL` varchar(500) NOT NULL,
  `ID_USER_MHS` bigint(20) UNSIGNED NOT NULL,
  `NIM` varchar(50) NOT NULL,
  `THN_CREATE` int(11) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_judul`
--

INSERT INTO `t_judul` (`id`, `JUDUL`, `ID_USER_MHS`, `NIM`, `THN_CREATE`, `TGL_CREATE`, `TGL_UPDATE`) VALUES
(1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', 7, '32322004', 2024, '2026-07-26', '2026-07-26'),
(2, 'test buat mahasiswa 2', 39, '231401001', 2026, '2026-07-30', '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `t_judul_temp`
--

CREATE TABLE `t_judul_temp` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED NOT NULL,
  `JUDUL` varchar(500) NOT NULL,
  `ID_USER_MHS` bigint(20) UNSIGNED NOT NULL,
  `NIM` varchar(50) NOT NULL,
  `JUDUL_BARU` varchar(500) NOT NULL,
  `TAHAP_PERUBAHAN` varchar(250) NOT NULL,
  `ALASAN_PERUBAHAN` varchar(500) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_penilaian`
--

CREATE TABLE `t_penilaian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ID_AJUAN` bigint(20) UNSIGNED NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED NOT NULL,
  `JUDUL` varchar(500) NOT NULL,
  `NIM` varchar(50) NOT NULL,
  `NAMA_MHS` varchar(250) NOT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `ID_TIM_SIDANG` bigint(20) UNSIGNED NOT NULL,
  `ID_USER_PENILAI` bigint(20) UNSIGNED NOT NULL,
  `STATUS_TIM_SIDANG` varchar(250) NOT NULL,
  `NIP` varchar(50) NOT NULL,
  `NAMA` varchar(250) NOT NULL,
  `ID_PENILAIAN` bigint(20) UNSIGNED NOT NULL,
  `NAMA_PENILAIAN` varchar(250) NOT NULL,
  `NILAI` decimal(5,2) DEFAULT NULL,
  `CATATAN` varchar(500) DEFAULT NULL,
  `STATUS_SUBMIT` char(1) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `ID_USER_CREATE` bigint(20) UNSIGNED NOT NULL,
  `NAMA_USER_CREATE` varchar(250) NOT NULL,
  `NO_FORM` varchar(50) DEFAULT NULL,
  `STATUS_LULUS` varchar(20) DEFAULT NULL,
  `NILAI_TERKUNCI` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_penilaian`
--

INSERT INTO `t_penilaian` (`id`, `ID_AJUAN`, `ID_JUDUL`, `JUDUL`, `NIM`, `NAMA_MHS`, `TAHAPAN_SIDANG`, `ID_TIM_SIDANG`, `ID_USER_PENILAI`, `STATUS_TIM_SIDANG`, `NIP`, `NAMA`, `ID_PENILAIAN`, `NAMA_PENILAIAN`, `NILAI`, `CATATAN`, `STATUS_SUBMIT`, `TGL_CREATE`, `TGL_UPDATE`, `ID_USER_CREATE`, `NAMA_USER_CREATE`, `NO_FORM`, `STATUS_LULUS`, `NILAI_TERKUNCI`) VALUES
(64, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 2, 'Kebaruan dan Kualitas Topik Penelitian', NULL, 'test_input_pembimbing', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(65, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 3, 'Kejelasan Pemaparan Latar Belakang', 5.00, 'test_input_pembimbing', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(66, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 4, 'Kejelasan Pemaparan Hipotesis/Tujuan', 5.00, 'test_input_pembimbing', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(67, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 5, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 5.00, 'test_input_pembimbing', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(68, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 6, 'Format Penulisan Proposal', 5.00, 'test_input_pembimbing', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(69, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 3, 4, 'Ketua Sidang', '197803152002121001', 'Pembimbing', 7, 'Usulan Perbaikan', 1.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(70, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 2, 'Kebaruan dan Kualitas Topik Penelitian', NULL, 'qweqw', 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(71, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 3, 'Kejelasan Pemaparan Latar Belakang', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(72, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 4, 'Kejelasan Pemaparan Hipotesis/Tujuan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(73, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 5, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(74, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 6, 'Format Penulisan Proposal', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(75, 2, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap II', 4, 8, 'Ketua Pembimbing', '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 7, 'Usulan Perbaikan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '302.3', 'lulus', 1),
(76, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 5, 11, 'Ketua Sidang', '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 8, 'Kreatifitas dan Keuletan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(77, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 5, 11, 'Ketua Sidang', '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 9, 'Keberhasilan Penelitian', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(78, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 5, 11, 'Ketua Sidang', '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 10, 'Penulisan Laporan Kemajuan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(79, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 5, 11, 'Ketua Sidang', '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 11, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(80, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 5, 11, 'Ketua Sidang', '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 12, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(81, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 6, 12, 'Ketua Pembimbing', '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 8, 'Kreatifitas dan Keuletan', 4.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(82, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 6, 12, 'Ketua Pembimbing', '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 9, 'Keberhasilan Penelitian', 4.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(83, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 6, 12, 'Ketua Pembimbing', '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 10, 'Penulisan Laporan Kemajuan', 4.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(84, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 6, 12, 'Ketua Pembimbing', '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 11, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 4.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(85, 3, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK I', 6, 12, 'Ketua Pembimbing', '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 12, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 4.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.1', 'lulus', 1),
(86, 4, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK II', 7, 18, 'Ketua Pembimbing', '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 13, 'Kreatifitas dan Keuletan', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.2', 'lulus', 1),
(87, 4, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK II', 7, 18, 'Ketua Pembimbing', '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 14, 'Keberhasilan Penelitian', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.2', 'lulus', 1),
(88, 4, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK II', 7, 18, 'Ketua Pembimbing', '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 15, 'Penulisan Laporan Kemajuan', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.2', 'lulus', 1),
(89, 4, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK II', 7, 18, 'Ketua Pembimbing', '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 16, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.2', 'lulus', 1),
(90, 4, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'SK II', 7, 18, 'Ketua Pembimbing', '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 17, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '304.2', 'lulus', 1),
(91, 1, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap I', 1, 5, 'Ketua Sidang', '197210122003121001', 'Penguji', 1, 'Nilai Mata Kuliah Tahap Persiapan', 5.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '1', 'lulus', NULL),
(92, 1, 1, 'Optimasi Enhanced Oil Recovery pada Reservoir Minyak Berat', '32322004', 'Ade', 'tahap I', 2, 4, 'Ketua Pembimbing', '197803152002121001', 'Pembimbing', 1, 'Nilai Mata Kuliah Tahap Persiapan', 3.00, NULL, 't', '2026-07-29', '2026-07-29', 2, 'Dede', '1', 'lulus', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `t_point_penilaian`
--

CREATE TABLE `t_point_penilaian` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `PENILAIAN` varchar(500) NOT NULL,
  `ID_PRODI` bigint(20) UNSIGNED NOT NULL,
  `KODE_PRODI` varchar(50) NOT NULL,
  `NAMA_PRODI` varchar(250) NOT NULL,
  `TAHAPAN_SIDANG` varchar(250) NOT NULL,
  `STATUS_AKTIF` varchar(50) NOT NULL,
  `STRATA` varchar(10) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `NO_FORM` varchar(50) DEFAULT NULL,
  `STATUS_CATATAN` char(1) DEFAULT NULL,
  `KETERANGAN` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_point_penilaian`
--

INSERT INTO `t_point_penilaian` (`id`, `PENILAIAN`, `ID_PRODI`, `KODE_PRODI`, `NAMA_PRODI`, `TAHAPAN_SIDANG`, `STATUS_AKTIF`, `STRATA`, `TGL_CREATE`, `TGL_UPDATE`, `NO_FORM`, `STATUS_CATATAN`, `KETERANGAN`) VALUES
(1, 'Nilai Mata Kuliah Tahap Persiapan', 1, '322', 'Teknik Perminyakan', 'tahap I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '1', '', NULL),
(2, 'Kebaruan dan Kualitas Topik Penelitian', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-29', '302.3', 'y', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'),
(3, 'Kejelasan Pemaparan Latar Belakang', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '302.3', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'),
(4, 'Kejelasan Pemaparan Hipotesis/Tujuan', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '302.3', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik'),
(5, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '302.3', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'),
(6, 'Format Penulisan Proposal', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '302.3', 't', 'Kajian literatur dituliskan secara rinci dengan analisis yang komprehensif dan kritis, kesesuaian format/layout, penggunaan tata bahasa baku, kejelasan informasi gambar dan tabel, kerunutan penulisan referensi dan daftar pustaka'),
(7, 'Usulan Perbaikan', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '302.3', NULL, NULL),
(8, 'Kreatifitas dan Keuletan', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.1', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(9, 'Keberhasilan Penelitian', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.1', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'),
(10, 'Penulisan Laporan Kemajuan', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.1', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(11, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.1', NULL, NULL),
(12, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.1', NULL, NULL),
(13, 'Kreatifitas dan Keuletan', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.2', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(14, 'Keberhasilan Penelitian', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.2', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'),
(15, 'Penulisan Laporan Kemajuan', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.2', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(16, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.2', NULL, NULL),
(17, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.2', NULL, NULL),
(18, 'Kreatifitas dan Keuletan', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.3', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(19, 'Keberhasilan Penelitian', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.3', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal. Capaian luaran publikasi jurnal internasional. Jika status: Accepted: 5, Revisi: 4, Under Review: 3, Submitted: 2, Draft: 1'),
(20, 'Penulisan Laporan Kemajuan', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.3', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(21, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.3', NULL, NULL),
(22, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '304.3', NULL, NULL),
(23, 'Kebaruan dan Kualitas Topik Penelitian', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '306.2', 't', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'),
(24, 'Kejelasan Pemaparan Latar Belakang Masalah', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '306.2', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'),
(25, 'Kejelasan Pemaparan Hipotesis/Tujuan', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '306.2', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik dan jelas'),
(26, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '306.2', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'),
(27, 'Format Penulisan Disertasi', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '306.2', 't', 'Kajian literatur dituliskan rinci dengan analisis yang komprehensif dan kritis, format/layout rapi, menggunakan tata bahasa yang baku, gambar dan tabel jelas, penulisan referensi dan daftar pustaka runut dan memenuhi kaidah penulisan disertasi di ITB'),
(28, 'Penguasaan Materi', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '309.1', 't', 'Pemahaman materi yang disampaikan, kejelasan materi yang disampaikan'),
(29, 'Keberhasilan Penelitian', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '309.1', 't', 'Ketercapaian target luaran publikasi pada jurnal internasional bereputasi'),
(30, 'Kemampuan Berkomunikasi/Presentasi', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '309.1', 't', 'Organisasi presentasi, kemampuan mengkomunikasikan gagasan'),
(31, 'Tanya Jawab', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26', '309.1', 't', 'Kemampuan menyerap pertanyaan dan menjawab pertanyaan secara efisien dan efektif'),
(32, 'Penulisan Disertasi', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-29', '309.1', 'y', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(159, 'Nilai Mata Kuliah Tahap Persiapan', 2, '323', 'Teknik Geofisika', 'tahap I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '1', '', NULL),
(160, 'Kebaruan dan Kualitas Topik Penelitian', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', 'y', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'),
(161, 'Kejelasan Pemaparan Latar Belakang', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'),
(162, 'Kejelasan Pemaparan Hipotesis/Tujuan', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik'),
(163, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'),
(164, 'Format Penulisan Proposal', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', 't', 'Kajian literatur dituliskan secara rinci dengan analisis yang komprehensif dan kritis, kesesuaian format/layout, penggunaan tata bahasa baku, kejelasan informasi gambar dan tabel, kerunutan penulisan referensi dan daftar pustaka'),
(165, 'Usulan Perbaikan', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '302.3', NULL, NULL),
(166, 'Kreatifitas dan Keuletan', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.1', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(167, 'Keberhasilan Penelitian', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.1', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'),
(168, 'Penulisan Laporan Kemajuan', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.1', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(169, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.1', NULL, NULL),
(170, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.1', NULL, NULL),
(171, 'Kreatifitas dan Keuletan', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.2', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(172, 'Keberhasilan Penelitian', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.2', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal'),
(173, 'Penulisan Laporan Kemajuan', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.2', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(174, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.2', NULL, NULL),
(175, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.2', NULL, NULL),
(176, 'Kreatifitas dan Keuletan', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.3', 't', 'Kemampuan dalam menyelesaian masalah, keaktifan dan ketekunan'),
(177, 'Keberhasilan Penelitian', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.3', 't', 'Ketercapaian target luaran penelitian sesuai dengan proposal. Capaian luaran publikasi jurnal internasional. Jika status: Accepted: 5, Revisi: 4, Under Review: 3, Submitted: 2, Draft: 1'),
(178, 'Penulisan Laporan Kemajuan', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.3', 't', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel'),
(179, 'Kemampuan Berkomunikasi/Presentasi: organisasi dan teknik presentasi', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.3', NULL, NULL),
(180, 'Kemampuan Berkomunikasi/Presentasi: kemampuan Tanya jawab', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '304.3', NULL, NULL),
(181, 'Kebaruan dan Kualitas Topik Penelitian', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '306.2', 't', 'Topik penelitian yang dipilih memiliki nilai kebaruan sangat tinggi, baik dari sudut pandang keilmuan ataupun relevansi pada penerapannya dan memiliki kualitas serta orisinalitas yang terukur'),
(182, 'Kejelasan Pemaparan Latar Belakang Masalah', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '306.2', 't', 'Permasalahan dinyatakan dengan sangat jelas dan terkait erat dengan latar belakang yang disusun berdasarkan data literatur yang komprehensif dan mutakhir'),
(183, 'Kejelasan Pemaparan Hipotesis/Tujuan', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '306.2', 't', 'Pernyataan hipotesis atau tujuan atau target yang diinginkan dinyatakan dengan sangat baik dan jelas'),
(184, 'Kejelasan Pemaparan Metode/Teorema yang akan Digunakan', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '306.2', 't', 'Pemilihan metode/teorema tepat dan sesuai dengan topik penelitian yang dipilih, kerunutan dalam pemaparan metode/teorema yang dipilih'),
(185, 'Format Penulisan Disertasi', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '306.2', 't', 'Kajian literatur dituliskan rinci dengan analisis yang komprehensif dan kritis, format/layout rapi, menggunakan tata bahasa yang baku, gambar dan tabel jelas, penulisan referensi dan daftar pustaka runut dan memenuhi kaidah penulisan disertasi di ITB'),
(186, 'Penguasaan Materi', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '309.1', 't', 'Pemahaman materi yang disampaikan, kejelasan materi yang disampaikan'),
(187, 'Keberhasilan Penelitian', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '309.1', 't', 'Ketercapaian target luaran publikasi pada jurnal internasional bereputasi'),
(188, 'Kemampuan Berkomunikasi/Presentasi', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '309.1', 't', 'Organisasi presentasi, kemampuan mengkomunikasikan gagasan'),
(189, 'Tanya Jawab', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '309.1', 't', 'Kemampuan menyerap pertanyaan dan menjawab pertanyaan secara efisien dan efektif'),
(190, 'Penulisan Disertasi', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30', '309.1', 'y', 'Penggunaan bahasa, kejelasan informasi gambar dan tabel');

-- --------------------------------------------------------

--
-- Table structure for table `t_prodi`
--

CREATE TABLE `t_prodi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `KODE_PRODI` varchar(50) NOT NULL,
  `NAMA_PRODI` varchar(250) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `STATUS_AKTIF` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_prodi`
--

INSERT INTO `t_prodi` (`id`, `KODE_PRODI`, `NAMA_PRODI`, `TGL_CREATE`, `TGL_UPDATE`, `STATUS_AKTIF`) VALUES
(1, '322', 'Teknik Perminyakan', '2026-07-26', '2026-07-26', 'AKTIF'),
(2, '323', 'Teknik Geofisika', '2026-07-26', '2026-07-26', 'AKTIF');

-- --------------------------------------------------------

--
-- Table structure for table `t_sk`
--

CREATE TABLE `t_sk` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `NO_SK` varchar(250) NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED DEFAULT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `TGL_BUAT` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_sk`
--

INSERT INTO `t_sk` (`id`, `NO_SK`, `ID_JUDUL`, `TAHAPAN_SIDANG`, `TGL_BUAT`, `TGL_UPDATE`) VALUES
(1, 'SK/SIDANG/322/2024/001', 1, 'tahap I', '2026-07-26', '2026-07-26'),
(2, 'SK/TAHAPII/3222026001', 1, 'tahap II', '2026-07-26', '2026-07-26'),
(3, 'SK/SKI/3222026001', 1, 'SK I', '2026-07-29', '2026-07-29'),
(4, 'SK/SKII/3222026001', 1, 'SK II', '2026-07-29', '2026-07-29'),
(5, 'SK/TAHAPI/3232026002', 2, 'tahap I', '2026-07-30', '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `t_syarat_sidang`
--

CREATE TABLE `t_syarat_sidang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `NAMA_PERSYARATAN` varchar(500) NOT NULL,
  `ID_PRODI` bigint(20) UNSIGNED NOT NULL,
  `KODE_PRODI` varchar(50) NOT NULL,
  `NAMA_PRODI` varchar(250) NOT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `STATUS_AKTIF` varchar(50) NOT NULL,
  `STRATA` varchar(10) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_syarat_sidang`
--

INSERT INTO `t_syarat_sidang` (`id`, `NAMA_PERSYARATAN`, `ID_PRODI`, `KODE_PRODI`, `NAMA_PRODI`, `TAHAPAN_SIDANG`, `STATUS_AKTIF`, `STRATA`, `TGL_CREATE`, `TGL_UPDATE`) VALUES
(1, 'Mengikuti mata kuliah tahap persiapan', 1, '322', 'Teknik Perminyakan', 'tahap I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(2, 'Lulus ujian persiapan (tahap I)', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(3, 'Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(4, 'Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing', 1, '322', 'Teknik Perminyakan', 'tahap II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(5, 'Lulus ujian proposal', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(6, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(7, 'Menyerahkan makalah/slide presentasi', 1, '322', 'Teknik Perminyakan', 'SK I', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(8, 'Menyerahkan laporan kemajuan I dan II', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(9, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(10, 'Menyerahkan makalah/slide presentasi', 1, '322', 'Teknik Perminyakan', 'SK II', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(11, 'Menyerahkan laporan kemajuan I, II dan III', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(12, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(13, 'Menyerahkan makalah/slide presentasi', 1, '322', 'Teknik Perminyakan', 'SK III', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(14, 'Menyerahkan naskah laporan kemajuan tahap akhir/penulisan disertasi', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(15, 'Melampirkan lembar pengesahan atau persetujuan dari Tim Dosen Pembimbing', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(16, 'Menyerahkan makalah/slide presentasi', 1, '322', 'Teknik Perminyakan', 'SK IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(17, 'Telah mengambil dan lulus semua mata kuliah selain Sidang Doktor', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(18, 'Telah memiliki bukti status accepted/published untuk makalah riset di jurnal internasional bereputasi (first author, afiliasi ITB)', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(19, 'Telah menyelesaikan dan memenuhi seluruh logbook bimbingan akademik dengan promotor/pembimbing', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(20, 'Menyerahkan sejumlah draft disertasi dan ringkasan disertasi', 1, '322', 'Teknik Perminyakan', 'tahap IV', 'AKTIF', 'S3', '2026-07-26', '2026-07-26'),
(83, 'Mengikuti mata kuliah tahap persiapan', 2, '323', 'Teknik Geofisika', 'tahap I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(84, 'Lulus ujian persiapan (tahap I)', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(85, 'Menyerahkan draft proposal riset yang sudah ditandatangi pembimbing', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(86, 'Menyerahkan form bimbingan/kemajuan akademik yang sudah ditandatangi pembimbing', 2, '323', 'Teknik Geofisika', 'tahap II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(87, 'Lulus ujian proposal', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(88, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(89, 'Menyerahkan makalah/slide presentasi', 2, '323', 'Teknik Geofisika', 'SK I', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(90, 'Menyerahkan laporan kemajuan I dan II', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(91, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(92, 'Menyerahkan makalah/slide presentasi', 2, '323', 'Teknik Geofisika', 'SK II', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(93, 'Menyerahkan laporan kemajuan I, II dan III', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(94, 'Menyerahkan formulir bimbingan yang sudah ditandatangi pembimbing', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(95, 'Menyerahkan makalah/slide presentasi', 2, '323', 'Teknik Geofisika', 'SK III', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(96, 'Menyerahkan naskah laporan kemajuan tahap akhir/penulisan disertasi', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(97, 'Melampirkan lembar pengesahan atau persetujuan dari Tim Dosen Pembimbing', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(98, 'Menyerahkan makalah/slide presentasi', 2, '323', 'Teknik Geofisika', 'SK IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(99, 'Telah mengambil dan lulus semua mata kuliah selain Sidang Doktor', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(100, 'Telah memiliki bukti status accepted/published untuk makalah riset di jurnal internasional bereputasi (first author, afiliasi ITB)', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(101, 'Telah menyelesaikan dan memenuhi seluruh logbook bimbingan akademik dengan promotor/pembimbing', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30'),
(102, 'Menyerahkan sejumlah draft disertasi dan ringkasan disertasi', 2, '323', 'Teknik Geofisika', 'tahap IV', 'AKTIF', 'S3', '2026-07-30', '2026-07-30');

-- --------------------------------------------------------

--
-- Table structure for table `t_tahapan`
--

CREATE TABLE `t_tahapan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `TAHAPAN` varchar(250) NOT NULL,
  `KODE_TAHAP` varchar(50) NOT NULL,
  `STRATA` varchar(10) NOT NULL,
  `TGL_BUAT` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_tahapan`
--

INSERT INTO `t_tahapan` (`id`, `TAHAPAN`, `KODE_TAHAP`, `STRATA`, `TGL_BUAT`, `TGL_UPDATE`) VALUES
(1, 'tahap I', 'T1', 'S3', '2026-07-26', '2026-07-26'),
(2, 'tahap II', 'T2', 'S3', '2026-07-26', '2026-07-26'),
(3, 'SK I', 'SK1', 'S3', '2026-07-26', '2026-07-26'),
(4, 'SK II', 'SK2', 'S3', '2026-07-26', '2026-07-26'),
(5, 'SK III', 'SK3', 'S3', '2026-07-26', '2026-07-26'),
(6, 'SK IV', 'SK4', 'S3', '2026-07-26', '2026-07-26'),
(7, 'tahap IV', 'T4', 'S3', '2026-07-26', '2026-07-26');

-- --------------------------------------------------------

--
-- Table structure for table `t_tim_sidang`
--

CREATE TABLE `t_tim_sidang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `TAHAPAN_SIDANG` varchar(100) NOT NULL,
  `ID_JUDUL` bigint(20) UNSIGNED NOT NULL,
  `STATUS_TIM_SIDANG` varchar(250) NOT NULL,
  `ID_USER_PENILAI` bigint(20) UNSIGNED NOT NULL,
  `NIP` varchar(50) NOT NULL,
  `NAMA` varchar(250) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `ID_SK` bigint(20) UNSIGNED DEFAULT NULL,
  `URUTAN` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_tim_sidang`
--

INSERT INTO `t_tim_sidang` (`id`, `TAHAPAN_SIDANG`, `ID_JUDUL`, `STATUS_TIM_SIDANG`, `ID_USER_PENILAI`, `NIP`, `NAMA`, `TGL_CREATE`, `TGL_UPDATE`, `ID_SK`, `URUTAN`) VALUES
(1, 'tahap I', 1, 'Ketua Sidang', 5, '197210122003121001', 'Penguji', '2026-07-26', '2026-07-26', 1, 1),
(2, 'tahap I', 1, 'Ketua Pembimbing', 4, '197803152002121001', 'Pembimbing', '2026-07-26', '2026-07-26', 1, 2),
(3, 'tahap II', 1, 'Ketua Sidang', 4, '197803152002121001', 'Pembimbing', '2026-07-26', '2026-07-26', 2, 1),
(4, 'tahap II', 1, 'Ketua Pembimbing', 8, '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', '2026-07-28', '2026-07-28', 2, 2),
(5, 'SK I', 1, 'Ketua Sidang', 11, '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', '2026-07-29', '2026-07-29', 3, 1),
(6, 'SK I', 1, 'Ketua Pembimbing', 12, '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', '2026-07-29', '2026-07-29', 3, 2),
(7, 'SK II', 1, 'Ketua Pembimbing', 18, '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', '2026-07-29', '2026-07-29', 4, 1),
(8, 'tahap I', 1, 'Penguji I', 13, '195303251980031001', 'Ir. Leksono Mucharam, M.Sc., Ph.D', '2026-07-30', '2026-07-30', 1, 3),
(9, 'tahap I', 2, 'Ketua Pembimbing', 17, '196610171980031001', 'Dr. Ir. Sutopo, M.Sc', '2026-07-30', '2026-07-30', 5, 1),
(10, 'tahap I', 2, 'Ketua Sidang', 9, '195303031980031001', 'Prof. Ir. Pudji Permadi, M.Sc., Ph.D', '2026-07-30', '2026-07-30', 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `t_user`
--

CREATE TABLE `t_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `NIP_NIM` varchar(50) NOT NULL,
  `NAMA_LENGKAP` varchar(500) NOT NULL,
  `EMAIL` varchar(250) NOT NULL,
  `AKUN_INA` varchar(250) DEFAULT NULL,
  `USERNAME` varchar(50) DEFAULT NULL,
  `PASSWORD` varchar(250) DEFAULT NULL,
  `STATUS_PEGAWAI` varchar(250) DEFAULT NULL,
  `JENIS_USER` varchar(250) NOT NULL,
  `KODE_PRODI` varchar(50) DEFAULT NULL,
  `NAMA_PRODI` varchar(250) DEFAULT NULL,
  `KODE_FS` varchar(50) NOT NULL,
  `NAMA_FS` varchar(250) NOT NULL,
  `STRATA` varchar(10) DEFAULT NULL,
  `THN_ANGKATAN` int(11) DEFAULT NULL,
  `STATUS_AKTIF` varchar(50) NOT NULL,
  `STATUS_APPROVE` char(1) NOT NULL,
  `TGL_CREATE` date NOT NULL DEFAULT curdate(),
  `TGL_UPDATE` date NOT NULL DEFAULT curdate(),
  `STATUS_KAPRODI` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `t_user`
--

INSERT INTO `t_user` (`id`, `NIP_NIM`, `NAMA_LENGKAP`, `EMAIL`, `AKUN_INA`, `USERNAME`, `PASSWORD`, `STATUS_PEGAWAI`, `JENIS_USER`, `KODE_PRODI`, `NAMA_PRODI`, `KODE_FS`, `NAMA_FS`, `STRATA`, `THN_ANGKATAN`, `STATUS_AKTIF`, `STATUS_APPROVE`, `TGL_CREATE`, `TGL_UPDATE`, `STATUS_KAPRODI`) VALUES
(1, '112000021', 'Administrator', 'admin@itb.ac.id', NULL, 'admin', '$2y$12$jyIovqAhPBQErEvLAAMKMegTUgwNQguVc9lQHKD4U6sl.NrDqjhKa', 'Tendik', 'Admin', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(2, '197502082005012001', 'Dede', 'dede@tm.itb.ac.id', NULL, 'Dede', '$2y$12$mmiTtYDZvbxkJzuRncHb3.Xm1GVDgNGoezWlkzSfUXCmSitQKJqGa', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(3, '196508022009021002', 'Dede FS', 'dedefs@tm.itb.ac.id', NULL, 'dedefs', '$2y$12$ZUgZ8BgEIZ0UemCeSXl/jO4vn.fHIvIWvppHuHSJdGa/dp0fD7d3S', 'Tendik', 'FS', NULL, NULL, '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(4, '197803152002121001', 'Pembimbing', 'pembimbing@fttm.itb.ac.id', NULL, 'pembimbing', '$2y$12$OshSLC8VI9u16DrtkBun9eY.5xqdgi6M93nn1649EZ8v2MiRGXsZe', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(5, '197210122003121001', 'Penguji', 'penguji@itb.ac.id', NULL, 'penguji', '$2y$12$Y8uc0hhqlpmp9Gre4cKnSu3ghDcz.9WtmtCA8rSA9kHxnwNrUUJ8C', 'Dosen', 'Penguji', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(6, '197512012005011002', 'Monev', 'monev@itb.ac.id', NULL, 'monev', '$2y$12$68FnFDaSHk0PPBHSI23U6.7FOCQ0Fv8v4ZcVBG3igdmvKHJBDpks6', 'Dosen', 'Monev', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(7, '32322004', 'Ade', 'ade@mahasiswa.itb.ac.id', NULL, 'ade', '$2y$12$MP27cqmSqj9fsSLmrCDuE.hwKjKaHDhJqHJJYlqC8TGYQ4BWmZSnK', 'Mahasiswa', 'Mahasiswa', '322', 'Teknik Perminyakan', '13321002', 'FTTM', 'S3', NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(8, '195109141980031001', 'Prof. Dr. Ing. Ir. HP Septoratno Siregar, DEA', 'septo@tm.itb.ac.id', NULL, 'septoratno', '$2y$12$NFYeGS3fI270M4sFNZUGeO95IEH0L9OxwBNfBxF1HTqK7BIbchi7i', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(9, '195303031980031001', 'Prof. Ir. Pudji Permadi, M.Sc., Ph.D', 'pudji@tm.itb.ac.id', NULL, 'pudjipermadi', '$2y$12$tzWn0BFFhPQ8LP0mNXYeluEmhZDjEd0/GKadf2qtOPGnzW3PoEdsi', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(10, '195205101980031001', 'Prof. Ir. Doddy Abdassah, M.Sc., Ph.D', 'abdassah@tm.itb.ac.id', NULL, 'doddyabdassah', '$2y$12$d3fWR0t/RxJIFIHkgtncQO6NMkNaszp.OSJIm62R0S6QUiWWXv.im', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(11, '194612101980031001', 'Prof. Ir. Pudjo Sukarno, M.Sc, Ph.D', 'psukarno@tm.itb.ac.id', NULL, 'pudjosukarno', '$2y$12$9fwxm7Nl5YvXQSHje/mB2eEmqLGCHs5heEa4UsNAA0zXKjcf7NQii', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(12, '195509021980031001', 'Dr. Ir. Sudjati Rachmat, DEA', 'sudjati@tm.itb.ac.id', NULL, 'sudjatir', '$2y$12$2ils4xbT9YcTCEjaoxTqQewww0AYzFpLpJZ3uNwt8tBplZrlr57Dm', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(13, '195303251980031001', 'Ir. Leksono Mucharam, M.Sc., Ph.D', 'lm@tm.itb.ac.id', NULL, 'leksono', '$2y$12$6go6KfqihR3JIGNOiwCp3.yYUglaJGyNgo8eOVIefq9rT4cYzU0h.', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(14, '196311121980031001', 'Ir. Asep Kurnia Permadi, M.Sc., Ph.D', 'akp@tm.itb.ac.id', NULL, 'asepkurnia', '$2y$12$XnzPoyOwqw4hQUqX9nmFm.1ZTDA8GTu1vMcvum.sHHQuGJ4XSjJC6', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(15, '195508011980032001', 'Ir. Nenny Miryani Saptadji, Ph.D', 'nennys@tm.itb.ac.id', NULL, 'nennys', '$2y$12$1ITFASy./0aG6eB4Dv0DweihpebNTd3vqZd9lXPxpitYQ8OB8Hwqu', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(16, '196408261980031001', 'Ir. Tutuka Ariadji, M.Sc., Ph.D', 'tutukaariadji@tm.itb.ac.id', NULL, 'tutuka', '$2y$12$CI44qUFmi0mkB7tIIsFHKeSw8RZUT4x.UMpgBL5bxDF.OnntXtgOy', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(17, '196610171980031001', 'Dr. Ir. Sutopo, M.Sc', 'sutopo@tm.itb.ac.id', NULL, 'sutopo', '$2y$12$yHdH6.ZcK/N2qlbgtyUzeu1U0D4TNuq8sY51H5yBYmaK25KsgSfN6', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(18, '196801171980031001', 'Dr. Ir. Taufan Marhaendrajana', 'tmarhaendrajana@tm.itb.ac.id', NULL, 'taufanm', '$2y$12$EGtaqGi7/ZqQKanIeleXCe3ukwyQUpRurkKZzEEsZX/T9xLy7LQ2W', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(19, '196009191980031001', 'Ir. Ucok WR Siagian, M.Sc, Ph.D', 'ucokwrs@tm.itb.ac.id', NULL, 'ucokwrs', '$2y$12$9hQyi5eRcexF6jPdjtc8A.yz591ptLVi1V8x4sZZsndPAA.qDR9PW', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(20, '197206061980031001', 'Ir. Zuher Syihab, M.Sc, Ph.D', 'zuher.syihab@tm.itb.ac.id', NULL, 'zuhers', '$2y$12$H07ostGJqr6SOmutWd8FReHlOSzFJxH0p7eaGjFzfvkez5W5UXz9O', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(21, '197512221980031001', 'Dr.-Ing. Bonar Tua Halomoan Marbun', 'bonar.marbun@tm.itb.ac.id', NULL, 'bonarm', '$2y$12$LyuQPIkfEmFhawZe9xz4s.3Oz0YiHoPVjlTtk/z36/Ig/q6ajAm3S', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(22, '197511051980031001', 'Dedy Irawan, ST, MT', 'di@tm.itb.ac.id', NULL, 'dedyirawan', '$2y$12$gEI8oK9V2fP87f8i.opzfeIeSsEJfy7bCYcKx6uvrYJDEcKcHUcCS', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(23, '198009171980031001', 'Dr. Adityawarman, S.T., M.T.', 'warman@tm.itb.ac.id', NULL, 'adityawarman', '$2y$12$EQg3B1QuxuCzsTzNVwkev.yU0r3uc.Ph0ABMgjrWr.CfpqVfuRN.6', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(24, '197804121980031001', 'Dr. Amega Yasutra, S.T., M.T.', 'amega@tm.itb.ac.id', NULL, 'amegayasutra', '$2y$12$VTaluOqh/kQ6sZj85bV85OiLw7ZoHF4JxylLvZss.htsPmH4YlseW', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(25, '198402221980032001', 'Silvya Dewi Rahmawati, S.Si., M.Si., Ph.D', 'sdr@tm.itb.ac.id', NULL, 'silvyadr', '$2y$12$Nja6uAPZJG6YHibTPAGKiut4Yf0xzzQ.dgTi0hCD9qmne8Cgsx8ea', 'Dosen', 'Pembimbing', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(26, '195707121980031001', 'Idi Suwardi', 'idi@tm.itb.ac.id', NULL, 'idisuwardi', '$2y$12$6HHhrWIAXmh7JTDCh4s7t.cLM7zkbWyfQ/7KwT6B1OLbCFs1.4MwK', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(27, '197812131980031001', 'Irvan Zaenudin, A.Md.', 'irvan@tm.itb.ac.id', NULL, 'irvanz', '$2y$12$UASq7u9ep1v6aDhakgDJJepC2fpYls05DP4ZDJnUzNde/foUsbyOW', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(28, '196204031980031001', 'Oman Rohman', 'oman@tm.itb.ac.id', NULL, 'omanr', '$2y$12$.GwuLSesQBxDKFhEeS/jweTanswN4WZt72E8/h7oFZIVNL3MhdvnW', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(29, '195911181980031001', 'Rohenda', 'dohem@tm.itb.ac.id', NULL, 'rohenda', '$2y$12$C4trZSQr.wRfU/FTFd5k/elvBiqh3wI0Xsw9c/XQ6fuy1VrB9pTx.', 'Dosen', 'Penguji', '322', 'Teknik Perminyakan', '164', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-28', NULL),
(30, '195901311980031001', 'Suparyono', 'upar@tm.itb.ac.id', NULL, 'suparyono', '$2y$12$vnRBBhUSLgWjVsLnBtkz.uo6FsKqYE7OctYg7hnhcZevsBXZAgtxm', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(31, '196208281980032001', 'Tuti Suhaemi', 'tuti@tm.itb.ac.id', NULL, 'tutis', '$2y$12$Dibhte8nlTcDCHQ6cn5CPeZC.9afqtgsXBx23SUYNrEcydBbMNBFe', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(32, '198506161980031001', 'Witan Ermintan, A.Md.', 'witan@tm.itb.ac.id', NULL, 'witan', '$2y$12$TxItNnaPWG1eF9N8Ehvg7eCsUjUg.zOAORcplse6EONZa9iOgjf..', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(33, '196608151980031001', 'Agus Rahmansyah, SE.', 'agus@tm.itb.ac.id', NULL, 'agusrahmansyah', '$2y$12$iDVPUYPcGwbOSbjytzJwieXaxDvvN/cxkU/HTFZlbH3/oO.Nni04W', 'Tendik', 'TU Prodi', '322', 'Teknik Perminyakan', '13321002', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-26', '2026-07-26', 'f'),
(34, '138218', 'yest12', 'yest@gmail.com', 'yest@itb.ac.id', 'yest', '$2y$12$..MJbg2SDuPMXrG4HAh78uuzKtCKRts3qPP2MgxJee/GDK0SYi2tC', 'Tendik', 'Admin', '322', 'Teknik Perminyakan', '164', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-28', '2026-07-28', NULL),
(35, '12312', 'dfsdfs', 'user@example.com', 'dede.rosyani@itb.ac.id', 'Bastian', NULL, NULL, 'Mahasiswa', '322', 'Teknik Perminyakan', '164', 'FTTM', 'S3', 2026, 'AKTIF', 't', '2026-07-29', '2026-07-29', NULL),
(36, '112222', 'ayayay', 'ayayay@gmail.com', 'ayayay@itb.ac.id', 'ayayay', NULL, NULL, 'Mahasiswa', '323', 'Teknik Geofisika', '164', 'FTTM', 'S3', 2019, 'AKTIF', 't', '2026-07-29', '2026-07-29', NULL),
(37, '12312', 'didin', 'didin@gmail.com', 'didin@itb.ac.id', 'didin', NULL, 'Dosen', 'Dosen', '322', 'Teknik Perminyakan', '164', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-29', '2026-07-29', 'y'),
(38, '19850101001', 'Rina Kartika, S.T., M.T.', 'prodi.geofisika@kampus.ac.id', 'rina.kartika@itb.ac.id', 'prodi_geofisika', '$2y$12$dC6JmSaErj676vo5nS5dleh0nEK122Kg4ppOUB.IJFmKrQCXQmoJe', 'Tendik', 'TU Prodi', '323', 'Teknik Geofisika', '164', 'FTTM', NULL, NULL, 'AKTIF', 't', '2026-07-30', '2026-07-30', NULL),
(39, '231401001', 'Ahmad Fauzan', 'ahmad.fauzan@student.kampus.ac.id', 'ahmad.fauzan@itb.ac.id', 'ahmad_fauzan', '$2y$12$Oo.l4rBGm7E12gEhRMzKNeQwmJ7WUhqOwspOSYmUreZ6SUATpsIH6', NULL, 'Mahasiswa', '323', 'Teknik Geofisika', '164', 'FTTM', 'S3', 2019, 'AKTIF', 't', '2026-07-30', '2026-07-30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `t_ajuan_sidang`
--
ALTER TABLE `t_ajuan_sidang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_ajuan_sidang_id_user_foreign` (`ID_USER`),
  ADD KEY `t_ajuan_sidang_id_judul_foreign` (`ID_JUDUL`),
  ADD KEY `t_ajuan_sidang_id_user_create_foreign` (`ID_USER_CREATE`),
  ADD KEY `t_ajuan_sidang_id_prodi_foreign` (`ID_PRODI`);

--
-- Indexes for table `t_cek_persyaratan`
--
ALTER TABLE `t_cek_persyaratan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_cek_persyaratan_id_judul_foreign` (`ID_JUDUL`),
  ADD KEY `t_cek_persyaratan_id_syarat_sidang_foreign` (`ID_SYARAT_SIDANG`);

--
-- Indexes for table `t_judul`
--
ALTER TABLE `t_judul`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_judul_id_user_mhs_foreign` (`ID_USER_MHS`);

--
-- Indexes for table `t_judul_temp`
--
ALTER TABLE `t_judul_temp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_judul_temp_id_user_mhs_foreign` (`ID_USER_MHS`);

--
-- Indexes for table `t_penilaian`
--
ALTER TABLE `t_penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_penilaian_id_ajuan_foreign` (`ID_AJUAN`),
  ADD KEY `t_penilaian_id_judul_foreign` (`ID_JUDUL`),
  ADD KEY `t_penilaian_id_tim_sidang_foreign` (`ID_TIM_SIDANG`),
  ADD KEY `t_penilaian_id_user_penilai_foreign` (`ID_USER_PENILAI`),
  ADD KEY `t_penilaian_id_penilaian_foreign` (`ID_PENILAIAN`),
  ADD KEY `t_penilaian_id_user_create_foreign` (`ID_USER_CREATE`);

--
-- Indexes for table `t_point_penilaian`
--
ALTER TABLE `t_point_penilaian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_point_penilaian_id_prodi_foreign` (`ID_PRODI`);

--
-- Indexes for table `t_prodi`
--
ALTER TABLE `t_prodi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_sk`
--
ALTER TABLE `t_sk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_sk_id_judul_foreign` (`ID_JUDUL`);

--
-- Indexes for table `t_syarat_sidang`
--
ALTER TABLE `t_syarat_sidang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_syarat_sidang_id_prodi_foreign` (`ID_PRODI`);

--
-- Indexes for table `t_tahapan`
--
ALTER TABLE `t_tahapan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_tim_sidang`
--
ALTER TABLE `t_tim_sidang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `t_tim_sidang_id_judul_foreign` (`ID_JUDUL`),
  ADD KEY `t_tim_sidang_id_user_penilai_foreign` (`ID_USER_PENILAI`);

--
-- Indexes for table `t_user`
--
ALTER TABLE `t_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_ajuan_sidang`
--
ALTER TABLE `t_ajuan_sidang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `t_cek_persyaratan`
--
ALTER TABLE `t_cek_persyaratan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `t_judul`
--
ALTER TABLE `t_judul`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_judul_temp`
--
ALTER TABLE `t_judul_temp`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_penilaian`
--
ALTER TABLE `t_penilaian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `t_point_penilaian`
--
ALTER TABLE `t_point_penilaian`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT for table `t_prodi`
--
ALTER TABLE `t_prodi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `t_sk`
--
ALTER TABLE `t_sk`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `t_syarat_sidang`
--
ALTER TABLE `t_syarat_sidang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `t_tahapan`
--
ALTER TABLE `t_tahapan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `t_tim_sidang`
--
ALTER TABLE `t_tim_sidang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `t_user`
--
ALTER TABLE `t_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `t_ajuan_sidang`
--
ALTER TABLE `t_ajuan_sidang`
  ADD CONSTRAINT `t_ajuan_sidang_id_judul_foreign` FOREIGN KEY (`ID_JUDUL`) REFERENCES `t_judul` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_ajuan_sidang_id_prodi_foreign` FOREIGN KEY (`ID_PRODI`) REFERENCES `t_prodi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_ajuan_sidang_id_user_create_foreign` FOREIGN KEY (`ID_USER_CREATE`) REFERENCES `t_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_ajuan_sidang_id_user_foreign` FOREIGN KEY (`ID_USER`) REFERENCES `t_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_cek_persyaratan`
--
ALTER TABLE `t_cek_persyaratan`
  ADD CONSTRAINT `t_cek_persyaratan_id_judul_foreign` FOREIGN KEY (`ID_JUDUL`) REFERENCES `t_judul` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_cek_persyaratan_id_syarat_sidang_foreign` FOREIGN KEY (`ID_SYARAT_SIDANG`) REFERENCES `t_syarat_sidang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_judul`
--
ALTER TABLE `t_judul`
  ADD CONSTRAINT `t_judul_id_user_mhs_foreign` FOREIGN KEY (`ID_USER_MHS`) REFERENCES `t_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_judul_temp`
--
ALTER TABLE `t_judul_temp`
  ADD CONSTRAINT `t_judul_temp_id_user_mhs_foreign` FOREIGN KEY (`ID_USER_MHS`) REFERENCES `t_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_penilaian`
--
ALTER TABLE `t_penilaian`
  ADD CONSTRAINT `t_penilaian_id_ajuan_foreign` FOREIGN KEY (`ID_AJUAN`) REFERENCES `t_ajuan_sidang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_penilaian_id_judul_foreign` FOREIGN KEY (`ID_JUDUL`) REFERENCES `t_judul` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_penilaian_id_penilaian_foreign` FOREIGN KEY (`ID_PENILAIAN`) REFERENCES `t_point_penilaian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_penilaian_id_tim_sidang_foreign` FOREIGN KEY (`ID_TIM_SIDANG`) REFERENCES `t_tim_sidang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_penilaian_id_user_create_foreign` FOREIGN KEY (`ID_USER_CREATE`) REFERENCES `t_user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_penilaian_id_user_penilai_foreign` FOREIGN KEY (`ID_USER_PENILAI`) REFERENCES `t_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_point_penilaian`
--
ALTER TABLE `t_point_penilaian`
  ADD CONSTRAINT `t_point_penilaian_id_prodi_foreign` FOREIGN KEY (`ID_PRODI`) REFERENCES `t_prodi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_sk`
--
ALTER TABLE `t_sk`
  ADD CONSTRAINT `t_sk_id_judul_foreign` FOREIGN KEY (`ID_JUDUL`) REFERENCES `t_judul` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_syarat_sidang`
--
ALTER TABLE `t_syarat_sidang`
  ADD CONSTRAINT `t_syarat_sidang_id_prodi_foreign` FOREIGN KEY (`ID_PRODI`) REFERENCES `t_prodi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `t_tim_sidang`
--
ALTER TABLE `t_tim_sidang`
  ADD CONSTRAINT `t_tim_sidang_id_judul_foreign` FOREIGN KEY (`ID_JUDUL`) REFERENCES `t_judul` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `t_tim_sidang_id_user_penilai_foreign` FOREIGN KEY (`ID_USER_PENILAI`) REFERENCES `t_user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
