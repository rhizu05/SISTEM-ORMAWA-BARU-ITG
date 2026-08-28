# 01 — Pendahuluan

## 1.0 Konteks Pengembangan

SKIN dikembangkan dalam rangka **Kerja Praktik di Institut Teknologi Garut (ITG)**. Sistem ini bukan dibangun dari nol, melainkan merupakan kelanjutan dari sistem yang telah dikembangkan sebelumnya. Tugas tim KP difokuskan pada evaluasi, perbaikan bug, penambahan fitur, dan deployment.

Sebagai dasar pengembangan, telah dilakukan pengumpulan data melalui wawancara dan kuesioner terhadap stakeholder utama:

| Stakeholder | Peran dalam Sistem | Periode |
|---|---|---|
| BKKH (Biro Ketenagaan, Kemahasiswaan, dan Humas) | Pengelola & admin sistem | 13 Agustus 2026 |
| Ormawa — BPM & BEM | Pengguna utama (pengaju, monitoring) | 14–18 Agustus 2026 |
| Mahasiswa Umum *(Pending)* | Kandidat pengguna masa depan | Agustus 2026 |

> Berdasarkan hasil wawancara, seluruh anggota Ormawa (BEM, BPM, HIMA, UKM) dapat mengajukan proposal kegiatan. Namun ketiganya memiliki **fungsi yang hierarkis** dalam sistem:
> - **HIMA & UKM** — Pengaju murni: mengajukan proposal, meminjam fasilitas, upload LPJ
> - **BEM** — Pengaju aktif sekaligus **Verifikator**: menyetujui/menolak pengajuan dari HIMA/UKM sebelum naik ke BPM dan BKKH
> - **BPM** — Pengaju aktif sekaligus **Pengawas**: memantau seluruh program kerja, keuangan, dan LPJ Ormawa secara read-only; mengelola aspirasi dan regulasi

## 1.1 Gambaran Umum

SKIN (Sistem Kemahasiswaan) adalah aplikasi web yang dikembangkan sebagai **pusat layanan kemahasiswaan terpadu** di Institut Teknologi Garut (ITG). Sistem ini tidak hanya mengelola keuangan Ormawa, tetapi diarahkan untuk menjadi satu platform bagi seluruh kebutuhan administrasi dan layanan kemahasiswaan.

Berdasarkan kondisi sistem existing dan hasil wawancara stakeholder, SKIN mencakup:

1. **Pengajuan Dana** — Ormawa mengajukan proposal kegiatan beserta RAB dan dokumen pendukung.
2. **Verifikasi Berjenjang** — Proposal diverifikasi oleh BEM → BPM → BKKH → WR3 sebelum disetujui.
3. **Pencairan Dana** — BKKH meneruskan proposal disetujui ke Bendahara untuk proses pencairan.
4. **Laporan Pertanggungjawaban (LPJ)** — Ormawa mengunggah LPJ yang kemudian diverifikasi BKKH.
5. **Peminjaman Sarana & Prasarana** — Peminjaman tempat dan barang dengan verifikasi dua tahap.
6. **Persuratan Digital** — Pembuatan proposal otomatis, surat resmi, LPJ otomatis, arsip digital, dan QR code verifikasi.
7. **Komunikasi & Informasi** — Pusat informasi, jadwal rapat, aspirasi, regulasi, dan surat peringatan.
8. **Administrasi** — Manajemen pengguna, saldo, arsip surat, dan pengaturan sistem (kop surat, logo, dll).

**Fitur yang diidentifikasi perlu dikembangkan** berdasarkan hasil wawancara:

- Monitoring & follow-up status pengajuan yang lebih informatif
- Perencanaan dan monitoring program kerja tahunan Ormawa
- **Notifikasi realtime (SSE) perubahan status** — dikirim instan ke pengguna tanpa perlu beralih stack; tetap berbasis PHP, memanfaatkan tabel `notifikasi` yang sudah tersedia
- Transparansi kegiatan Ormawa
- Sentralisasi peminjaman fasilitas dengan informasi ketersediaan

## 1.2 Identitas Sistem

| Atribut | Nilai |
|---------|-------|
| Nama sistem | SKIN / SKIN / skin-itg |
| Institusi | Institut Teknologi Garut (ITG) |
| Jenis aplikasi | Web application (PHP custom MVC) |
| Bahasa pemrograman | PHP 8.0.30 |
| Database | MariaDB 10.4 / MySQL 8.0 |
| Nama database | `db_pengajuan` |
| Frontend | Bootstrap 5.3.3 (via CDN) |
| Server pengembangan | Laragon (Apache, port 80) |
| License / kepemilikan | Internal ITG |

## 1.3 Repository GitHub

- **URL:** <https://github.com/rdreikhan-commits/SISTEM-ORMAWA-BARU-ITG.git>
- **Branch utama:** `main`, `develop`
- **Branch aktif (restrukturisasi):** `refactor/structure`

> Catatan: Saat dokumentasi ini ditulis, sistem sedang dalam proses restrukturisasi struktur project menjadi arsitektur MVC (folder `app/`). Dokumentasi ini menggambarkan kondisi setelah restrukturisasi tersebut.

## 1.4 Kebutuhan Environment Pengembangan

### Perangkat Lunak Wajib

| Komponen | Versi yang Disarankan | Keterangan |
|----------|----------------------|------------|
| Laragon | >= 5.x | Bundled Apache + MySQL/MariaDB + PHP |
| PHP | 8.0+ | Dengan ekstensi `mysqli`, `gd`, `fileinfo` |
| MariaDB / MySQL | 10.4 / 8.0 | RDBMS untuk `db_pengajuan` |
| Browser | Modern | Chrome/Edge/Firefox terbaru |
| Git | >= 2.x | Untuk clone repository |

### Ekstensi PHP yang Dibutuhkan

- `mysqli` — koneksi database (digunakan di `config.php`)
- `gd` — image processing (profil, logo, tanda tangan)
- `session` — otentikasi pengguna
- `fileinfo` — (opsional) deteksi tipe file
- `json` — (opsional) untuk JavaScript interop

## 1.5 Cara Menjalankan Sistem

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/rdreikhan-commits/SISTEM-ORMAWA-BARU-ITG.git
cd sistem_keuangan
```

> Folder project lokal pada environment saat ini: `C:\laragon\www\sistem_keuangan`

### Langkah 2 — Menyiapkan Database

**Opsi A (rekomendasi) — Import file SQL:**

1. Buka phpMyAdmin / MySQL CLI.
2. Buat database baru dengan nama `db_pengajuan`.
3. Import `scripts/db_pengajuan.sql`.

**Opsi B — Jalankan script setup lengkap:**

```bash
# Dari folder project
php scripts/setup_complete.php
```

Script ini otomatis membuat database, tabel, seed data, user default, dan folder uploads.

### Langkah 3 — Konfigurasi Koneksi Database

Edit `config.php` di root project sesuai environment Anda:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Sesuaikan dengan password MySQL Anda
define('DB_NAME', 'db_pengajuan');
define('DB_PORT', 3306);
```

> `BASE_URL` dihitung otomatis dari request (`HTTP_HOST` + path), sehingga tidak perlu diubah manual.

### Langkah 4 — Menjalankan Server

Akses melalui Laragon virtual host:

```
http://localhost/sistem_keuangan
```

Atau jalankan PHP built-in server:

```bash
php -S localhost:8000
# lalu buka http://localhost:8000
```

### Langkah 5 — Login

| Username | Password | Role |
|----------|----------|------|
| `bem` | *(dari SQL dump)* | BEM |
| `bpm` | *(dari SQL dump)* | BPM |
| `bkkh` | *(dari SQL dump)* | BKKH |
| `himatif` | *(dari SQL dump)* | Ormawa — HIMA Teknik Informatika |
| `hima_si` | *(dari SQL dump)* | Ormawa — HIMA Sistem Informasi |
| `hima_sipil` | *(dari SQL dump / Manajemen User)* | Ormawa — HIMA Teknik Sipil |
| `hima_arsi` | *(dari SQL dump / Manajemen User)* | Ormawa — HIMA Arsitektur |
| `hima_industri` | *(dari SQL dump / Manajemen User)* | Ormawa — HIMA Teknik Industri |
| `wr3` | *(dari SQL dump)* | Wakil Rektor 3 |
| `bendahara` | *(dari SQL dump)* | Bendahara |
| `sarpras_ruangan` | `sarpras123` | Sarpras Ruangan |
| `sarpras_barang` | `barang123` | Sarpras Barang |

> **Konvensi akun Ormawa:** Seluruh HIMA (5 program studi) dan UKM memakai **role `ormawa` yang sama** — tidak ada role terpisah per organisasi. Pembedaan dilakukan melalui `id_user` (identitas unik) dan nama tampilan `nama_lengkap`. Akun diasumsikan menggunakan password *default* (dari SQL dump atau dibuat BKKH melalui menu **Manajemen User**), lalu pengurus mengubahnya sendiri di menu **Profil**. Rincian lengkap ada di `02-struktur-sistem.md` bagian 2.5.6.

> Password default user lama mengikuti isi SQL dump (`db_pengajuan.sql`). Jika perlu reset, gunakan `scripts/setup_complete.php` atau script reset yang tersedia.

## 1.6 Verifikasi Sistem Berjalan

Setelah semua langkah selesai:

1. Buka `http://localhost/sistem_keuangan`.
2. Sistem akan otomatis mengarahkan ke halaman login.
3. Masukkan kredensial salah satu akun di atas.
4. Pastikan dashboard sesuai role muncul tanpa error.

## 1.7 Struktur Halaman Utama (Entry Point)

| File | Fungsi |
|------|--------|
| `index.php` | Front controller — bootstrap aplikasi & memanggil Router |
| `config.php` | Konfigurasi DB, konstanta `ROOT_PATH`/`BASE_URL`, session |
| `app/core/Router.php` | Peta halaman, hak akses, render view, notifikasi |
| `app/core/Controller.php` | Base class untuk semua controller |
| `app/helpers/functions.php` | Helper: session, sanitasi, redirect, check_role, add_history |
| `app/controllers/*.php` | Logika bisnis (POST/GET action) per modul |
| `app/views/**/*.php` | Template/halaman UI per role |
