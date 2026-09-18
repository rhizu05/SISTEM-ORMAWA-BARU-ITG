# SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu
**Institut Teknologi Garut (Versi 3.0)**

Aplikasi terpadu tata kelola kemahasiswaan, persuratan digital ormawa, pengajuan anggaran berbasis saldo, peminjaman sarana prasarana, serta portal layanan publik mahasiswa berbasis **Laravel 11 / 12 / 13 + Tailwind CSS + Breeze + Spatie Permission + DomPDF + PhpSpreadsheet**.

---

## 🚀 Panduan Inisialisasi Proyek (Project Setup & Initialization)

Ikuti langkah-langkah berikut secara berurutan untuk menginisialisasi proyek ini di lingkungan lokal Anda (mendukung Windows/Laragon, macOS, dan Linux):

### 1. Kloning Repositori & Masuk ke Direktori
```bash
git clone https://github.com/rhizu05/SISTEM-ORMAWA-BARU-ITG.git sistem_keuangan
cd sistem_keuangan
git checkout develop
```

### 2. Instalasi Dependensi Backend (Composer)
Pastikan PHP versi minimal **8.3** (teruji pada PHP 8.4) dan ekstensi `pdo_mysql`, `gd`, `zip`, `fileinfo`, `mbstring` telah aktif.
```bash
composer install
```

### 3. Instalasi Dependensi Frontend (NPM)
Pastikan Node.js versi minimal **v18** atau **v20+** telah terpasang.
```bash
npm install
```

### 4. Konfigurasi Environment (`.env`)
Salin berkas konfigurasi sampel dan hasilkan enkripsi kunci aplikasi:
```bash
# Windows:
copy .env.example .env

# Linux / macOS:
cp .env.example .env

# Generate Application Key:
php artisan key:generate
```

### 5. Konfigurasi Database
Buka file `.env` dan sesuaikan koneksi database Anda:

**Opsi A: Menggunakan MySQL (Disarankan untuk Laragon / XAMPP):**
Buat database bernama `sistem_kemahasiswaan` (atau `sistem_keuangan`) di phpMyAdmin / HeidiSQL, lalu atur di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_kemahasiswaan
DB_USERNAME=root
DB_PASSWORD=
```

**Opsi B: Menggunakan SQLite (Opsional untuk pengujian cepat):**
```bash
# Buat file database jika belum ada (Windows):
type nul > database\database.sqlite
# Linux/macOS:
touch database/database.sqlite
```
Sesuaikan `.env`:
```env
DB_CONNECTION=sqlite
```

### 6. Migrasi Database & Seeding Data Awal Lengkap
Jalankan migrasi seluruh 51 tabel dan isi seluruh data akun resmi, hak akses, alur kerja, data master ruangan/barang, serta sampel berita ber-poster dan tiket layanan publik:
```bash
php artisan migrate:fresh --seed
```

### 7. Buat Symlink Storage Publik
Wajib dijalankan agar gambar poster berita (`storage/app/public/pengumuman/sampul`), tanda tangan digital, dan lampiran publik dapat diakses oleh browser:
```bash
php artisan storage:link
```

### 8. Kompilasi Aset Frontend (Vite)
```bash
# Untuk mode pengembangan (Hot Reload / HMR):
npm run dev

# ATAU untuk kompilasi berkas produksi siap saji:
npm run build
```

### 9. Jalankan Server Aplikasi
Buka terminal baru dan jalankan server lokal Laravel:
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
Buka browser Anda di alamat: **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Akun Default Sistem

Seluruh akun di bawah ini telah disiapkan oleh seeder dengan kata sandi default: **`password`**

| Peran / Aktor | Email Login | Username (NIM) | Keterangan Akses |
| :--- | :--- | :--- | :--- |
| **Admin Sistem** | `admin@test.com` | `admin` | Pengelolaan pengguna dan konfigurasi global. |
| **Biro Kemahasiswaan (BKHM)** | `bkhm@test.com` | `bkhm` | Saldo ormawa, ekspor Excel/PDF, konseling, dan verifikasi prestasi. |
| **BEM ITG** | `bem@test.com` | `bem` | Kurasi berita HIMA/UKM, verifikasi proposal tahap 1, dan agenda BEM. |
| **BPM ITG** | `bpm@test.com` | `bpm` | Himpun aspirasi mahasiswa publik, regulasi UU ormawa, verifikasi tahap 2. |
| **Sarana & Prasarana (Sarpras)**| `sarpras@test.com` | `sarpras` | Kalender ruangan (slot checker kuliah vs ormawa), persetujuan tempat/barang. |
| **Wakil Rektor III (WR3)** | `wr3@test.com` | `wr3` | Persetujuan proposal tahap 4 dan monitoring LPJ. |
| **Bendahara Kampus** | `bendahara@test.com` | `bendahara` | Verifikasi pencairan dana dan konfirmasi transfer ormawa. |
| **Himpunan (HIMA IF)** | `himaif@test.com` | `himaif` | Pengajuan anggaran saldo, proposal ber-TTD, pinjam sarpras, draf berita. |
| **Unit Kegiatan (UKM Olahraga)**| `ukm.olahraga@test.com`| `ukmolahraga` | Pengajuan kegiatan UKM, surat pengantar sarpras jalur mandiri. |

> [!IMPORTANT]
> **Kebijakan Mahasiswa Umum (Guest Model — Aturan Bisnis BR-01):**
> Mahasiswa umum **tidak memiliki akun login dan tidak terdaftar di tabel `users`**. Seluruh layanan mahasiswa (Aspirasi, Konseling Rahasia BKHM, dan Pelaporan Prestasi) diakses secara publik melalui sistem tiket unik (`SKIN-TKT-YYYY-XXXX`). Pelacakan status perkembangan dilakukan via halaman **Lacak Tiket** menggunakan kombinasi **Kode Tiket + Alamat Email**.

---

## 🌟 Fitur Unggulan Sistem

1. **Portal Layanan Publik Mahasiswa Bertiket:**
   - Formulir Aspirasi Mahasiswa (opsi kirim anonim, terhubung ke BPM dan dapat dieskalasi ke BKHM).
   - Layanan Konseling Personal BKHM Rahasia (data dienkripsi *at-rest* AES-256-CBC, penetapan jadwal temu, dan tombol konfirmasi kehadiran mahasiswa).
   - Pelaporan Prestasi Mandiri & Pengajuan Bantuan Dana Delegasi Lomba.
   - Showcase Galeri Prestasi Mahasiswa Publik.
2. **Pusat Informasi & Kurasi Berita Berjenjang (Model Delegasi):**
   - Halaman detail berita interaktif dengan tampilan poster pamflet gambar, lightbox modal, tombol share ke WhatsApp, dan unduh dokumen Juknis PDF.
   - Panel Kurasi BEM: Validasi draf poster pamflet dan narasi kegiatan HIMA/UKM sebelum dipublikasikan ke publik.
3. **Sistem Tata Kelola Pengajuan Anggaran Ormawa:**
   - Alur persetujuan berjenjang 11 status workflow (Ormawa &rarr; BEM &rarr; BPM &rarr; BKHM &rarr; WR3 &rarr; Bendahara &rarr; Cair &rarr; LPJ).
   - Generator Proposal & LPJ otomatis berkop surat resmi ITG dan tanda tangan digital QR Code.
   - Kolom komunikasi revisi interaktif dua arah antara pengaju dan verifikator.
4. **Modul Rekapitulasi Keuangan BKHM:**
   - Pelacakan mutasi saldo ormawa per periode anggaran (*audit trail*).
   - Fitur ekspor laporan keuangan instan ke format **Excel (.xlsx)** dan **PDF Resmi**.
5. **Manajemen Sarpras & Kalender Interaktif:**
   - *Slot checker* ketersediaan ruangan real-time yang memetakan jam perkuliahan rutin kampus vs jadwal peminjaman kegiatan ormawa.
   - Mendukung 2 jalur peminjaman: Jalur HIMA/UKM ber-surat rekomendasi prodi (*bypass* langsung ke Sarpras) dan Jalur BEM/BPM (melalui verifikasi BKHM).

---

## 🧪 Pengujian Sistem (Testing)

Proyek ini dilengkapi dengan cakupan pengujian otomatis (*automated testing*) yang komprehensif:

```bash
# Menjalankan seluruh test suite unit & fitur (121 tests, 480 assertions):
php artisan test

# Menjalankan filter spesifik modul:
php artisan test --filter=DetailBeritaDanGambarTest
php artisan test --filter=PublicTicketingTest
php artisan test --filter=KonselingDanSarprasPenyempurnaanTest
php artisan test --filter=KurasiPengumumanTest
```

---

## 📁 Dokumentasi Tambahan

- **Spesifikasi Lengkap Frontend & UI/UX:** Lihat berkas [`FRONTEND_UIUX_REQUIREMENTS.md`](FRONTEND_UIUX_REQUIREMENTS.md) pada root proyek.
- **Out of Scope & Saran Pengembangan Masa Depan:** Lihat berkas [`docs/OUT_OF_SCOPE_DAN_SARAN_PENGEMBANGAN.md`](docs/OUT_OF_SCOPE_DAN_SARAN_PENGEMBANGAN.md).

---

## 🛠️ Troubleshooting Umum

- **Gambar poster tidak muncul / Broken Image:** Pastikan Anda telah menjalankan perintah `php artisan storage:link`.
- **Vite manifest not found:** Jalankan perintah `npm run build` atau `npm run dev`.
- **Pembersihan Cache Sistem:** Jalankan `php artisan optimize:clear`.

---

## 📄 Lisensi

Hak Cipta &copy; Institut Teknologi Garut (ITG). Seluruh hak cipta dilindungi undang-undang.