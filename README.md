# SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu
**Institut Teknologi Garut (Versi 3.0 — Arsitektur Repositori Terpisah)**

[![Laravel CI](https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG/actions/workflows/laravel-ci.yml/badge.svg)](https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG/actions/workflows/laravel-ci.yml)
[![Playwright Tests](https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG/actions/workflows/playwright.yml/badge.svg)](https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG/actions/workflows/playwright.yml)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777BB4?logo=php)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%20%2F%2012-FF2D20?logo=laravel)](https://laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v3%20%2F%20v4-38B2AC?logo=tailwind-css)](https://tailwindcss.com/)

Aplikasi terpadu tata kelola kemahasiswaan, persuratan digital ormawa, pengajuan anggaran berbasis saldo riil, peminjaman sarana prasarana cerdas, serta portal layanan publik mahasiswa berbasis **Laravel + Tailwind CSS + Alpine.js + Spatie Permission + DomPDF + PhpSpreadsheet**.

---

## 📌 Latar Belakang & Identitas Versi 3.0 (Repositori Terpisah)

Sistem ini merupakan **Versi 3.0** dari ekosistem Sistem Informasi & Keuangan Ormawa (SKIN ITG). 

Secara struktur repositori git, **versi ini sengaja dibangun dalam repositori mandiri yang terpisah** (`rhizu05/SISTEM-ORMAWA-BARU-ITG`) sebagai perombakan arsitektur besar-besaran (*major architectural rebuild*). Pemisahan ini dilakukan untuk meninggalkan keterbatasan teknis dan kompleksitas historis dari versi-versi sebelumnya:
1. **Pemisahan dari Arsitektur Monolit Legasi (v1.0 & v2.0):** Repositori lama memiliki ketergantungan yang kaku, schema basis data yang belum ternormalisasi penuh, serta ketiadaan audit trail keuangan ormawa.
2. **Standardisasi Tata Kelola Anggaran Modern:** Menghadirkan sistem pembukuan pagu saldo riil per periode anggaran yang terhubung langsung dengan rekening ormawa dan pencairan bertahap bendahara.
3. **Pondasi Keamanan Terpadu & Audit Trail:** Seluruh aksi kritis verifikasi, unduhan, komunikasi revisi, hingga legalisir persuratan tercatat rapi secara terstruktur.
4. **Penerapan Clean Code & Antislop Standard:** Penulisan antarmuka pengguna (UI/UX) modern, tata bahasa Indonesia yang lugas dan manusiawi, serta modularitas komponen Blade yang responsif dari mobile hingga desktop.

---

## 🌟 Pilar & Fitur Unggulan Sistem

### 1. Tata Kelola Anggaran & Persetujuan Proposal Berjenjang (11 Status Workflow)
- Alur verifikasi resmi: **Ormawa &rarr; BEM &rarr; BPM &rarr; BKHM &rarr; WR3 &rarr; Bendahara &rarr; Pencairan Dana &rarr; Pelaporan LPJ**.
- **Fitur Pembatalan Mandiri:** Pengaju dapat membatalkan proposal yang masih berada pada antrean awal (BEM/BPM) untuk revisi mandiri tanpa mengotori rekapitulasi audit.
- **Generator Dokumen Otomatis:** Pembuatan berkas proposal, surat rekomendasi, dan lembar pengesahan LPJ lengkap dengan kop surat institusi ITG serta tanda tangan digital ber-QR Code.
- **Ruang Komunikasi Interaktif:** Komunikasi catatan perbaikan dua arah antara ormawa dan tim verifikator secara langsung pada setiap berkas pengajuan.

### 2. Portal Layanan Publik Mahasiswa Bertiket (Guest Model — Aturan Bisnis BR-01)
- **Tanpa Akun Tambahan:** Mahasiswa umum mengakses layanan secara langsung tanpa mendaftar akun login guna melindungi privasi dan menyederhanakan birokrasi kampus.
- **Layanan Bertiket:**
  - **Aspirasi Mahasiswa:** Pengajuan suara mahasiswa (opsi anonim) yang langsung dikurasi oleh BPM dan dapat dieskalasi ke BKHM.
  - **Konseling Rahasia BKHM:** Konseling personal dengan enkripsi data *at-rest* AES-256-CBC, penentuan jadwal temu, serta konfirmasi kehadiran.
  - **Lapor Capaian Prestasi & Pengajuan Dana Delegasi:** Portal mandiri pelaporan prestasi mahasiswa yang telah diraih serta pengajuan bantuan dana delegasi lomba sebelum bertanding.
  - **Pelacakan Status Real-Time:** Pelacakan progres tiket publik via halaman **Lacak Tiket** menggunakan kombinasi **Kode Tiket unik (`SKIN-TKT-YYYY-XXXX`) + Alamat Email**.

### 3. Manajemen Sarana & Prasarana (Sarpras) Cerdas
- **Slot Checker & Anti-Bentrok:** Pengecekan otomatis ketersediaan ruangan antara jadwal perkuliahan harian dengan jadwal kegiatan ormawa.
- **Dual-Path Approval:**
  - *Jalur Mandiri HIMA/UKM:* Didukung surat pengantar/izin kegiatan prodi yang langsung mengarah ke Sarpras.
  - *Jalur BEM/BPM:* Melalui peninjauan awal oleh Biro Kemahasiswaan (BKHM).

### 4. Pusat Kurasi Berita & Agenda Ormawa
- Kurasi draf berita kegiatan ormawa oleh staf BEM sebelum dipublikasikan ke portal publik.
- Detail publik interaktif dilengkapi lightbox poster pamflet gambar, tombol bagikan ke WhatsApp, dan tautan unduh petunjuk teknis (Juknis).

### 5. Rekapitulasi Keuangan BKHM & Ekspor Instan
- Monitoring saldo per ormawa dengan riwayat mutasi masuk/keluar yang transparan.
- Fasilitas ekspor laporan berkas siap cetak dalam format **Excel (.xlsx)** dan **PDF Resmi**.

---

## 🚀 Panduan Inisialisasi Proyek (Lokal)

Ikuti langkah-langkah berikut untuk menjalankan sistem di komputer lokal Anda:

### 1. Kloning Repositori
```bash
git clone https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG.git sistem_keuangan
cd sistem_keuangan
git checkout develop
```

### 2. Instalasi Dependensi Backend & Frontend
Pastikan PHP minimal **8.3** dan Node.js minimal **v18+** / **v20+**:
```bash
# Backend (Composer):
composer install

# Frontend (NPM):
npm install
```

### 3. Konfigurasi Environment (`.env`)
```bash
# Windows:
copy .env.example .env

# Linux / macOS:
cp .env.example .env

# Buat kunci enkripsi aplikasi:
php artisan key:generate
```

### 4. Konfigurasi Database & Jalankan Migrasi
Sesuaikan kredensial basis data pada berkas `.env` (misal MySQL di Laragon / XAMPP):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_kemahasiswaan
DB_USERNAME=root
DB_PASSWORD=
```
Jalankan migrasi seluruh tabel beserta master seeder lengkap:
```bash
php artisan migrate:fresh --seed
```

### 5. Buat Symlink Storage Publik & Kompilasi Aset
```bash
# Symlink penyimpanan file publik (poster, ttd, lampiran):
php artisan storage:link

# Jalankan build frontend Vite (atau npm run dev untuk hot-reload):
npm run build
```

### 6. Jalankan Server Lokal
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
Akses aplikasi melalui peramban: **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Akun Default (Lingkungan Lokal / Pengujian)

Seluruh akun berikut disiapkan oleh seeder dengan kata sandi default: **`password`**

| Peran / Aktor | Email Login | Username (NIM) | Keterangan Akses |
| :--- | :--- | :--- | :--- |
| **Admin Sistem** | `admin@test.com` | `admin` | Pengelolaan pengguna dan konfigurasi global. |
| **Biro Kemahasiswaan (BKHM)** | `bkhm@test.com` | `bkhm` | Saldo ormawa, ekspor Excel/PDF, konseling, verifikasi prestasi. |
| **BEM ITG** | `bem@test.com` | `bem` | Kurasi berita ormawa, verifikasi proposal tahap 1, agenda BEM. |
| **BPM ITG** | `bpm@test.com` | `bpm` | Aspirasi mahasiswa, regulasi UU ormawa, verifikasi tahap 2. |
| **Sarana & Prasarana (Sarpras)**| `sarpras@test.com` | `sarpras` | Kalender ruangan & persetujuan peminjaman tempat/barang. |
| **Wakil Rektor III (WR3)** | `wr3@test.com` | `wr3` | Persetujuan proposal tahap 4 dan monitoring LPJ. |
| **Bendahara Kampus** | `bendahara@test.com` | `bendahara` | Verifikasi pencairan dana dan transfer ormawa. |
| **Himpunan (HIMA IF)** | `himaif@test.com` | `himaif` | Pengajuan anggaran saldo, proposal ber-TTD, pinjam sarpras. |
| **Unit Kegiatan (UKM Olahraga)**| `ukm.olahraga@test.com`| `ukmolahraga` | Pengajuan kegiatan UKM, surat pengantar sarpras. |

---

## 🛠️ Diagnostik Produksi & Verifikasi Email (SMTP)

Sistem dilengkapi dengan utilitas CLI bawaan untuk menguji kesiapan mail server di lingkungan produksi:

```bash
# Uji coba pengiriman email diagnostik:
php artisan mail:test devops@itg.ac.id
```
Perintah ini akan:
1. Memvalidasi sintaks format email penerima.
2. Menampilkan tabel ringkasan koneksi SMTP aktif (driver, host, port, enkripsi TLS/SSL).
3. Mengirimkan email HTML resmi institusi ITG ke kotak masuk target.
4. Menampilkan panduan solusi jika koneksi terhambat oleh firewall atau kredensial.

Panduan penerapan server produksi lengkap dapat dibaca pada berkas [`PRODUCTION_DEPLOYMENT_GUIDE.md`](PRODUCTION_DEPLOYMENT_GUIDE.md).

---

## 🧪 Pengujian Otomatis & Alur CI/CD GitHub Actions

Proyek ini telah dikonfigurasikan dengan alur kerja **Continuous Integration (CI)** berbasis **GitHub Actions**:

1. **Laravel CI (`.github/workflows/laravel-ci.yml`):**
   - Berjalan otomatis pada setiap aksi `push` dan `pull_request` ke cabang `develop`, `main`, dan `master`.
   - Melakukan setup PHP 8.3, instalasi dependensi Composer, kompilasi aset frontend Vite (`npm run build`), dan menjalankan rangkaian automated tests PHPUnit.
2. **Playwright E2E Tests (`.github/workflows/playwright.yml`):**
   - Menjalankan pengujian fungsional *End-to-End* antarmuka pengguna berbasis browser headless Playwright.

### Menjalankan Pengujian di Lokal:
```bash
# Menjalankan pengujian spesifik perintah email:
php vendor/phpunit/phpunit/phpunit tests/Feature/TestMailCommandTest.php

# Menjalankan seluruh pengujian unit & fitur:
php artisan test

# Menjalankan pengujian browser E2E Playwright:
npx playwright test
```

---

## 📁 Referensi Dokumentasi Proyek

- **Panduan Deployment Produksi (Docker & Bare-Metal):** [`PRODUCTION_DEPLOYMENT_GUIDE.md`](PRODUCTION_DEPLOYMENT_GUIDE.md)
- **Checklist Kesiapan Produksi (Go-Live Smoke Testing):** [`PRODUCTION_DEPLOYMENT_CHECKLIST.md`](PRODUCTION_DEPLOYMENT_CHECKLIST.md)
- **Spesifikasi Frontend & Standar Desain UI/UX:** [`FRONTEND_UIUX_REQUIREMENTS.md`](FRONTEND_UIUX_REQUIREMENTS.md)

---

## 📄 Lisensi & Hak Cipta

Hak Cipta &copy; 2026 **Institut Teknologi Garut (ITG)**. Seluruh hak cipta dilindungi undang-undang.