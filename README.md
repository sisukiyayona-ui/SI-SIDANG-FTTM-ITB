# SI SIDANG FTTM ITB

> Sistem Informasi Manajemen Sidang Akademik  
> Fakultas Teknik Pertambangan dan Perminyakan — Institut Teknologi Bandung

---

## Latar Belakang

Proses pengelolaan sidang akademik di FTTM ITB sebelumnya dilakukan secara manual — mulai dari pencatatan pengajuan, koordinasi jadwal, distribusi dokumen, hingga rekapitulasi nilai. Proses ini rentan terhadap keterlambatan informasi, miskomunikasi antar pihak, dan kesulitan dalam pemantauan status per mahasiswa.

**SI SIDANG FTTM ITB** hadir sebagai solusi digital terpadu yang mengotomasi seluruh alur tersebut — dari pengajuan oleh mahasiswa hingga keluarnya hasil kelulusan — dalam satu platform berbasis web yang dapat diakses oleh semua pihak yang terlibat.

---

## Tentang Aplikasi

SI SIDANG FTTM ITB adalah aplikasi web full-stack berbasis **Laravel 12** yang dirancang untuk mengelola siklus sidang akademik mahasiswa **S1, S2, dan S3** secara menyeluruh. Sistem ini mengimplementasikan alur persetujuan multi-level, manajemen dokumen, penjadwalan, penilaian terstruktur, serta integrasi notifikasi email — semuanya dalam satu antarmuka yang responsif dan mendukung dark mode.

---

## Fitur Aplikasi

### 🎓 Manajemen Tahapan Sidang
Setiap strata memiliki tahapan sidang yang berbeda dan dikelola secara terpisah:

| Strata | Tahapan |
|--------|---------|
| **S1** | Sidang Proposal → Seminar Kemajuan I, II, III, IV → Sidang Akhir |
| **S2** | Seminar Kemajuan I, II, III, IV → Sidang Terbuka / Tertutup |
| **S3** | Ujian Kualifikasi (SK I–IV) → Ujian Proposal → Sidang Terbuka / Tertutup |

Setiap tahapan memiliki persyaratan, tim sidang, jadwal, dan form penilaian masing-masing.

---

### 👥 Sistem Multi-Role
Aplikasi mendukung beberapa peran pengguna dengan hak akses berbeda:

| Role | Fungsi Utama |
|------|-------------|
| **Mahasiswa** | Melihat status sidang, upload dokumen persyaratan, ajukan ke TU Prodi |
| **TU Prodi** | Verifikasi persyaratan, input jadwal & tim sidang, ajukan ke Fakultas |
| **Fakultas (FS)** | Review dan setujui pengajuan, teruskan ke KPPS |
| **KPPS** | Voting approve/reject ajuan, lihat penilaian |
| **Pembimbing / Penguji** | Input nilai per form penilaian |
| **Ketua Sidang** | Monitor nilai seluruh penilai, tentukan status kelulusan |
| **Admin** | Akses penuh ke seluruh fitur termasuk data master |

---

### 📋 Alur Pengajuan Bertingkat

```
Mahasiswa  ──ajukan──►  TU Prodi  ──ajukan──►  Fakultas  ──ajukan──►  KPPS
                            │                      │                     │
                        Input jadwal          Review & ACC            Voting
                        Tim sidang            Kirim ke KPPS           Penilaian
```

Setiap langkah memiliki status yang bisa dipantau secara real-time oleh pihak terkait.

---

### 📅 Penjadwalan Sidang
- Input tanggal, waktu mulai dan selesai, ruangan
- Nomor dan tanggal surat undangan
- Nomor dan tanggal surat penelaah
- Email pengiriman surat
- Cetak undangan dan surat kesediaan penelaah langsung dari sistem

---

### ✏️ Penilaian Terstruktur
- Form penilaian dikonfigurasi per tahapan melalui data master
- Setiap penilai mengisi nilai dan catatan secara independen
- **Kunci Nilai** — nilai terkunci setelah penilai menyelesaikan input, mencegah perubahan
- Status kelulusan ditentukan oleh Ketua Sidang atau TU Prodi
- Cetak Berita Acara Sidang dan rekap penilaian per penilai

---

### 📄 Cetak Dokumen
Dokumen yang dapat dicetak langsung dari sistem:
- Berita Acara Sidang
- Surat Undangan Sidang
- Surat Kesediaan Penelaah
- Form Penilaian per Penilai

---

### 🔔 Notifikasi Email
- Pengingat jadwal sidang otomatis (via Laravel Scheduler)
- Notifikasi approve ke anggota KPPS yang belum melakukan voting
- Dukungan SMTP standar maupun Microsoft Graph API (Office 365)

---

### 📊 Dashboard & Monitoring
- Ringkasan status sidang setiap mahasiswa
- Timeline progress per tahapan
- Filter berdasarkan prodi, strata, dan status

---

### 📥 Import Excel
- Import massal data persyaratan sidang per tahapan dan prodi
- Import massal komponen penilaian
- Preview dan validasi data sebelum disimpan, termasuk deteksi duplikat

---

### ⚙️ Data Master
Konfigurasi yang dapat dikelola melalui menu admin:
- **Persyaratan** — daftar persyaratan per tahapan dan prodi
- **Komponen Penilaian** — parameter dan form penilaian
- **Program Studi** — data prodi per strata dan fakultas
- **Fakultas** — daftar fakultas
- **KPPS** — anggota tim KPPS aktif per prodi
- **User & Role** — manajemen akun dan hak akses
- **Mata Kuliah Prodi** — referensi mata kuliah per prodi

---

### 🔐 Autentikasi & Keamanan
- Login berbasis session dengan proteksi CSRF
- Integrasi **SSO ITB** (opsional) — login terintegrasi dengan akun Institut
- Middleware role dan ownership untuk pembatasan akses per halaman dan data
- Security headers middleware

---

### 🌙 Tampilan
- Antarmuka berbasis **AdminLTE 3** dengan Bootstrap 4
- Dukungan **dark mode** dan light mode
- Responsif untuk desktop dan tablet
- Dropdown searchable dengan Select2

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Framework | Laravel 12 (PHP 8.2+) |
| Database | MySQL / MariaDB |
| Frontend | AdminLTE 3, Bootstrap 4, jQuery, Select2 |
| PDF | DomPDF |
| Excel | PhpSpreadsheet, PhpWord |
| Email | Laravel Mail — SMTP / Microsoft Graph |
| Auth | Session-based + SSO ITB (opsional) |

---

## Prasyarat

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Web server (Apache / Nginx) atau `php artisan serve` untuk lokal

---

## Instalasi

```bash
# 1. Clone repositori
git clone <repo-url> SI-SIDANG-FTTM-ITB
cd SI-SIDANG-FTTM-ITB

# 2. Install dependensi
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=si_sidang_fttm
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi
php artisan migrate

# 7. Buat symlink storage (untuk akses file upload)
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

---

## Konfigurasi Environment

```env
APP_NAME="SI SIDANG FTTM ITB"
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_sidang_fttm
DB_USERNAME=root
DB_PASSWORD=

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=yourpassword
MAIL_FROM_ADDRESS="sisidang@fttm.itb.ac.id"
MAIL_FROM_NAME="SI SIDANG FTTM ITB"

# SSO ITB — kosongkan untuk nonaktifkan
SSO_ENABLED=false
SSO_URL=
SSO_CLIENT_ID=
SSO_CLIENT_SECRET=
SSO_CALLBACK_TOKEN=
```

---

## Lisensi

Proyek ini dikembangkan untuk kebutuhan internal **Fakultas Teknik Pertambangan dan Perminyakan — Institut Teknologi Bandung**.  
Seluruh hak cipta milik FTTM ITB.
