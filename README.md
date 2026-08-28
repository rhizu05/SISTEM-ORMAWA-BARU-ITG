# SKIN

Sistem Informasi Kemahasiswaan Organisasi Kemahasiswaan — aplikasi web berbasis PHP untuk mengelola pengajuan dana, persuratan digital, peminjaman sarana prasarana, dan komunikasi antar unit kemahasiswaan.

## Fitur Utama

### Keuangan
- Pengajuan dana dengan alur verifikasi multi-tahap (BEM → BPM → BKKH → WR3 → Bendahara)
- Upload proposal PDF & LPJ
- Pencairan dana dengan pencatatan nominal
- Audit trail histori status setiap pengajuan
- Manajemen saldo per organisasi

### Persuratan Digital
- Generator proposal otomatis (dengan RAB & struktur panitia)
- Generator LPJ otomatis (dengan realisasi anggaran & lampiran foto)
- Generator surat lainnya (Undangan, Tugas, Permohonan, Keterangan, Peringatan)
- TTD digital (ketua, sekretaris, bendahara)
- Verifikasi keaslian surat via QR code

### Sarana & Prasarana
- Peminjaman tempat/ruangan (alur 2 tahap: BKKH → Sarpras)
- Peminjaman barang/inventaris (alur 2 tahap: BKKH → Sarpras Barang)
- Manajemen master ruangan & inventaris barang

### Komunikasi & Informasi
- Pengumuman oleh BEM (dengan lampiran)
- Jadwal rapat interaktif (FullCalendar)
- Aspirasi publik (tanpa login) — dikelola BPM
- Regulasi & dokumen resmi oleh BPM
- Notifikasi in-app

## Tech Stack

| Komponen | Detail |
|----------|--------|
| Backend | PHP 8.0+, arsitektur Custom MVC (tanpa framework) |
| Database | MySQL 8.0 / MariaDB 10.4 |
| Driver DB | MySQLi + Prepared Statements |
| Frontend | Bootstrap 5.3, Bootstrap Icons, Chart.js, FullCalendar, SweetAlert2, DataTables |
| Auth | PHP Session native, password bcrypt |
| Server | Apache/Nginx (Laragon / XAMPP) |

## Prasyarat

- PHP >= 8.0
- MySQL >= 8.0 atau MariaDB >= 10.4
- Web server: Laragon, XAMPP, atau Apache/Nginx
- Ekstensi PHP: `mysqli`, `gd`, `fileinfo`, `session`

## Instalasi

### 1. Clone Repository

```bash
git clone <url-repo> sistem_keuangan
```

### 2. Konfigurasi Database

Salin file konfigurasi contoh dan sesuaikan dengan kredensial lokal kamu:

```bash
cp config.example.php config.php
```

Edit `config.php`:

```php
define('DB_HOST', 'localhost');    // host MySQL
define('DB_USER', 'root');         // username MySQL
define('DB_PASS', '');             // password MySQL
define('DB_NAME', 'db_pengajuan');
define('DB_PORT', 3306);           // Laragon default: 3306, bisa juga 3308
```

### 3. Jalankan Setup Otomatis

Akses script setup via browser:

```
http://sistem_keuangan.test/scripts/setup_complete.php
```

atau via CLI:

```bash
php scripts/setup_complete.php
```

Script ini akan secara otomatis:
- Membuat database `db_pengajuan`
- Mengimpor seluruh schema tabel (21 tabel)
- Mengisi data demo awal (user, ruangan, inventaris barang)
- Membuat folder `uploads/` yang diperlukan

### 4. Akses Aplikasi

```
http://sistem_keuangan.test/index.php?page=login
```

## User Demo

Semua user demo menggunakan password: **`password123`**

| Username | Role | Deskripsi |
|----------|------|-----------|
| `admin` | admin | Administrator |
| `bem` | bem | Badan Eksekutif Mahasiswa |
| `bpm` | bpm | Badan Permusyawaratan Mahasiswa |
| `bkkh` | bkh | BKKH (Super Admin & Verifikator) |
| `wr3` | wr3 | Wakil Rektor 3 |
| `bendahara` | bendahara | Bendahara Institusi |
| `himatif` | ormawa | Contoh Ormawa 1 |
| `hima_si` | ormawa | Contoh Ormawa 2 |
| `sarpras_ruangan` | sarpras | Bagian Sarpras Ruangan |
| `sarpras_barang` | sarpras_barang | Bagian Sarpras Barang |

## Alur Pengajuan Dana

```
[Ormawa] Buat Pengajuan
    └─> Diajukan Ke BEM
            ├─> Ditolak BEM  ──> (Ormawa revisi & ajukan ulang)
            └─> Diajukan Ke BPM
                    ├─> Ditolak BPM  ──> (Ormawa revisi & ajukan ulang)
                    └─> Verifikasi BKKH
                            ├─> Ditolak BKKH  ──> (Ormawa revisi & ajukan ulang)
                            └─> Verifikasi WR3
                                    ├─> Ditolak WR3  ──> (Ormawa revisi & ajukan ulang)
                                    └─> Disetujui WR3
                                            └─> Diajukan ke Bendahara
                                                    ├─> Ditolak Bendahara
                                                    └─> Dana Cair
                                                            └─> [Ormawa] Upload LPJ
                                                                    ├─> LPJ Ditolak BKKH  ──> (revisi)
                                                                    └─> Selesai
```

## Role & Akses

| Role | Tanggung Jawab |
|------|---------------|
| `ormawa` | Buat & kelola pengajuan dana, upload LPJ, peminjaman, persuratan digital |
| `bem` | Verifikasi tahap 1, kelola pengumuman & jadwal rapat |
| `bpm` | Verifikasi tahap 2, kelola aspirasi & regulasi, buat surat peringatan |
| `bkh` | Verifikasi tahap 3, ajukan pencairan, manajemen user & saldo & sistem |
| `wr3` | Persetujuan akhir proposal & LPJ |
| `bendahara` | Proses pencairan dana |
| `sarpras` | Verifikasi peminjaman tempat/ruangan |
| `sarpras_barang` | Verifikasi peminjaman barang, kelola inventaris |

## Struktur Direktori

```
sistem_keuangan/
├── index.php               # Entry point (front controller)
├── config.php              # Konfigurasi lokal (tidak di-commit)
├── config.example.php      # Template konfigurasi
├── app/
│   ├── core/
│   │   ├── Router.php      # Routing & access control
│   │   └── Controller.php  # Base controller
│   ├── controllers/        # Logic per fitur
│   ├── views/              # Template HTML (layout, per-role)
│   └── helpers/
│       └── functions.php   # Helper global
├── assets/                 # Aset statis (gambar)
├── uploads/                # File runtime (tidak di-commit)
├── scripts/
│   ├── db_pengajuan.sql    # Schema & data demo lengkap
│   ├── setup_complete.php  # Setup otomatis
│   └── *.php               # Script migrasi individual
└── docs/                   # Dokumentasi teknis
```
