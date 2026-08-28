# 02 — Struktur Sistem

## 2.1 Arsitektur Aplikasi

SKIN menggunakan arsitektur **Custom MVC (Model-View-Controller)** yang diimplementasikan secara manual tanpa framework. Alur eksekusi:

```
HTTP Request
    │
    ▼
index.php (Front Controller)
    │  require config.php (DB + session + helpers)
    ▼
app/core/Router.php
    │  1. Handle GET action (toggle status)
    │  2. Handle POST action (delegasi ke Controller)
    │  3. Resolve halaman dari $pageMap + cek hak akses (check_role)
    ▼
app/controllers/*.php  ──►  Database (MySQLi via $conn)
    ▼
app/views/**/*.php  (di-render di dalam layout header/sidebar/footer)
    ▼
HTML Response
```

Karakteristik arsitektur:

- **Single entry point** — semua request masuk melalui `index.php?page=<nama_halaman>`.
- **Routing terpusat** — peta halaman (`$pageMap`) dan hak akses didefinisikan di `app/core/Router.php`.
- **Controller per modul** — logika POST/GET dipisah ke 7 controller class.
- **View per role** — file tampilan dikelompokkan per folder role (`app/views/ormawa`, `app/views/verifikator`, dll).
- **Tanpa ORM** — akses database langsung menggunakan MySQLi + prepared statements.
- **Tanpa build tooling** — semua library frontend dimuat via CDN.

## 2.2 Teknologi & Library

### Backend

| Teknologi | Versi | Penggunaan |
|-----------|-------|------------|
| PHP | 8.0+ | Bahasa pemrograman utama |
| MySQLi | ekstensi PHP | Koneksi & query database (prepared statements) |
| `password_hash()` / `password_verify()` | native PHP | Hashing & verifikasi password (bcrypt) |
| PHP Session | native PHP | Autentikasi & state pengguna |
| PHP GD | ekstensi PHP | Pemrosesan gambar (foto profil, logo, TTD) |

### Frontend (semua via CDN)

| Library | Versi | Penggunaan |
|---------|-------|------------|
| Bootstrap | 5.3.3 | Framework CSS/UI (grid, komponen, navbar) |
| Bootstrap Icons | 1.11.3 | Icon set |
| Chart.js | latest | Grafik statistik dashboard |
| FullCalendar | 5.11.3 | Kalender jadwal rapat |
| SweetAlert2 | v11 | Popup notifikasi & konfirmasi |
| DataTables | CDN | Tabel dengan pagination/search |
| Vanilla JS | — | Sidebar toggle, toast, DOM manipulation |

> **Catatan:** Tidak ada `composer.json`, `package.json`, atau build pipeline (Webpack/Vite). Seluruh dependency eksternal dimuat via jsDelivr CDN.

## 2.3 Struktur Folder Project

```
sistem_keuangan/
│
├── index.php                      # Front controller / entry point (bootstrap)
├── config.php                     # DB, konstanta, session, helper
├── README.md
├── .gitignore
│
├── app/                           # Aplikasi inti (MVC)
│   ├── core/
│   │   ├── Router.php             # Routing, peta halaman, hak akses, render
│   │   └── Controller.php         # Base class controller
│   ├── helpers/
│   │   └── functions.php          # Helper global (session, sanitasi, redirect, dll)
│   ├── controllers/               # Logika bisnis per modul
│   │   ├── AspirasiController.php
│   │   ├── BendaharaController.php
│   │   ├── InformasiController.php
│   │   ├── PengajuanController.php
│   │   ├── ProfilController.php
│   │   ├── UserController.php
│   │   └── VerifikasiController.php
│   └── views/                     # Template/tampilan
│       ├── layouts/               # header.php, sidebar.php, footer.php
│       ├── auth/                  # login.php, logout.php
│       ├── shared/                # profil, aspirasi_publik, verify_page, panduan
│       ├── ormawa/                # 27 file halaman ormawa
│       ├── verifikator/           # 11 file halaman BEM/BPM/BKKH/WR3
│       ├── bendahara/             # 3 file halaman bendahara
│       ├── sarpras/               # 4 file halaman sarpras
│       └── admin/                 # 7 file halaman admin/BKKH
│
├── scripts/                       # Script setup & migration database
│   ├── db_pengajuan.sql           # Full database dump
│   ├── setup_complete.php         # Setup lengkap otomatis
│   ├── create_*.php               # Script pembuatan tabel
│   ├── update_*.php               # Script migrasi kolom
│   ├── fix_database.php           # Perbaikan skema
│   └── seed_sarpras.php           # Seeder data sarpras
│
├── uploads/                       # Penyimpanan file runtime
│   ├── proposal/                  # File PDF proposal
│   ├── lpj/                       # File LPJ
│   ├── lpj_lampiran/              # Lampiran LPJ
│   ├── profil/                    # Foto profil, logo, tanda tangan
│   ├── proposal_ttd/              # Upload TTD kustom
│   ├── pengumuman/                # Lampiran pengumuman
│   ├── regulasi/                  # Dokumen regulasi
│   ├── surat/                     # Arsip surat
│   ├── sistem/                    # Logo & kop surat sistem
│   └── qrcode/                    # QR code hasil generate
│
├── assets/                        # Aset statis lokal
│   └── images/                    # default-avatar.svg, dll
│
├── _archive/                      # File lama yang tidak terpakai (backup)
│   ├── sidebar (1).php
│   ├── forward_bendahara.php
│   ├── sync_data.php
│   └── ...
│
└── scratch/                       # Area pengembangan (dev tools)
```

## 2.4 Peta Halaman (Routing)

Definisi halaman dan hak akses berada di `app/core/Router.php` (`$pageMap`). Setiap entri terdiri dari:

```php
'<nama_halaman>' => [
    'file'  => 'app/views/.../file.php',  // view yang dirender
    'roles' => ['role1', 'role2'],        // role yang diizinkan ([] = publik)
]
```

Beberapa aturan penting:

- Jika `roles` kosong → halaman **publik** (tidak butuh login).
- Jika `roles` terisi → dicek `check_login()` lalu `check_role()`.
- Halaman `dashboard` dirender berdasarkan role user (`$dashboardMap`).
- Halaman **standalone** (tidak memakai layout header/sidebar/footer): `login`, `logout`, `cetak_surat`, `surat_balasan`, `verify_page`, `aspirasi`, `view_surat_lain`, `view_proposal`, `view_peminjaman`, `panduan`.

## 2.5 Database

### 2.5.1 Informasi Umum

| Atribut | Nilai |
|---------|-------|
| DBMS | MariaDB 10.4 / MySQL 8.0 |
| Nama database | `db_pengajuan` |
| Driver PHP | MySQLi |
| Kolasi (umum) | `utf8mb4_general_ci` |

### 2.5.2 Daftar Tabel (21 tabel)

#### Tabel Inti Keuangan

| Tabel | Fungsi | Kolom penting |
|-------|--------|---------------|
| `users` | Data semua pengguna (login, role, saldo, profil) | `id_user`, `username`, `password`, `role`, `foto_profil`, `status_akun`, `saldo`, `logo_ormawa`, `nama_ketua`, `ttd_ketua`, `nama_sekretaris`, `ttd_sekretaris`, `nama_bendahara`, `ttd_bendahara`, `alamat`, `telepon` |
| `pengajuan` | Data pengajuan dana (proposal) | `id_pengajuan`, `id_user_ormawa`, `nama_kegiatan`, `dana_diajukan`, `tanggal_pengajuan`, `file_proposal`, `file_lpj`, `status`, `catatan_revisi`, `unique_code`, `nomor_surat`, `notif_cair_terlihat` |
| `histori_status` | Riwayat/audit trail perubahan status | `id_histori`, `id_pengajuan`, `id_user`, `status`, `catatan`, `tanggal_update` |
| `dana` | Catatan pencairan dana | — |
| `notifikasi` | Notifikasi in-app | — |

#### Tabel Persuratan Digital

| Tabel | Fungsi |
|-------|--------|
| `proposal_otomatis` | Proposal yang dibuat otomatis (header) |
| `proposal_rab` | Rincian RAB proposal |
| `proposal_panitia` | Panitia/komite proposal |
| `lpj_otomatis` | LPJ otomatis (header) |
| `lpj_anggaran` | Rincian anggaran LPJ |
| `lpj_lampiran` | Lampiran LPJ (file/gambar) |
| `surat_otomatis` | Surat lain otomatis |

#### Tabel Sarana & Prasarana

| Tabel | Fungsi |
|-------|--------|
| `master_ruangan` | Data ruangan/tempat yang dapat dipinjam |
| `peminjaman_tempat` | Pengajuan peminjaman tempat (verifikasi BKKH + Sarpras) |
| `master_barang` | Data inventaris barang |
| `peminjaman_barang` | Pengajuan peminjaman barang (verifikasi BKKH + Sarpras) |

#### Tabel Komunikasi & Informasi

| Tabel | Fungsi |
|-------|--------|
| `pengumuman` | Berita/pengumuman (BEM) |
| `jadwal_rapat` | Jadwal rapat |
| `aspirasi` | Aspirasi publik (BPM menanggapi) |
| `regulasi` | Dokumen regulasi (BPM) |
| `konfigurasi` | Pengaturan sistem key-value (nama aplikasi, logo, kop surat) |

### 2.5.3 Status Enum Tabel `pengajuan`

```sql
status ENUM(
  'Draft',
  'Diajukan Ke BEM',
  'Ditolak BEM',
  'Diajukan Ke BPM',
  'Ditolak BPM',
  'Verifikasi BKKH',
  'Ditolak BKKH',
  'Verifikasi WR3',
  'Ditolak WR3',
  'Disetujui WR3, Siap Diajukan ke Bendahara',
  'Diajukan ke Bendahara',
  'Dana Cair',
  'LPJ Diajukan',
  'LPJ Ditolak BKKH',
  'LPJ Diverifikasi',
  'Selesai'
)
```

### 2.5.4 Role Enum Tabel `users`

```sql
role ENUM(
  'admin','ormawa','bem','bpm','bkh','wr3','bendahara','sarpras','sarpras_barang'
)
```

> **Catatan (dari wawancara stakeholder):** Secara bisnis, `bem`, `bpm`, dan `ormawa` (HIMA/UKM) semuanya termasuk kelompok **Ormawa**, namun memiliki fungsi yang **hierarkis dan berbeda**:
> - `ormawa` (HIMA/UKM) — Pengaju murni; tidak memiliki fungsi verifikasi atau monitoring atas Ormawa lain
> - `bem` — Pengaju aktif sekaligus verifikator tahap 1; pengajuan dari HIMA/UKM harus melewati approval BEM sebelum naik ke BPM
> - `bpm` — Pengaju aktif sekaligus pengawas; dapat memonitor seluruh program kerja, keuangan, dan LPJ Ormawa secara read-only — BPM tidak berwenang mengubah data keuangan
>
> **Panduan pemodelan akses data Ormawa:** HIMA dan seluruh UKM cukup digabung dalam **satu role** `ormawa` — **tidak perlu membuat role terpisah per HIMA/UKM**. Pemisahan akses data antar Ormawa dibedakan melalui **foreign key** pada data pengguna (misal: kolom `program_studi_id` pada tabel `users`) sehingga tiap akun hanya dapat mengakses data organisasinya sendiri. Pendekatan ini **mencegah *Broken Access Control*** (akses silang antar Ormawa) tanpa membanjiri sistem dengan puluhan role. Developer selanjutnya harus memastikan setiap query data Ormawa selalu difilter berdasarkan identitas organisasi pengguna yang sedang login.

### 2.5.5 Relasi Utama

```
users (1) ──< pengajuan >── (N) histori_status
users (1) ──< peminjaman_tempat
users (1) ──< peminjaman_barang
pengajuan (1) ──< proposal_rab / proposal_panitia
pengajuan (1) ──< lpj_anggaran / lpj_lampiran
users (1) ──< pengumuman / jadwal_rapat / aspirasi / regulasi
```

### 2.5.6 Pemodelan Akun Ormawa (HIMA/UKM)

Untuk mengakomodasi ke-5 himpunan mahasiswa dari masing-masing program studi (dan UKM lainnya), sistem **tidak membuat role baru**. Seluruh HIMA dan UKM menggunakan **satu role `ormawa`**, dibedakan hanya oleh data (identitas) penggunanya.

#### Konvensi Username

| Username | Role (DB) | Pemilik Akun |
|---|---|---|
| `himatif` (alias `hima_if`) | `ormawa` | HIMA Teknik Informatika |
| `hima_si` | `ormawa` | HIMA Sistem Informasi |
| `hima_sipil` | `ormawa` | HIMA Teknik Sipil |
| `hima_arsi` | `ormawa` | HIMA Arsitektur |
| `hima_industri` | `ormawa` | HIMA Teknik Industri |
| `ukm_*` (contoh: `ukm_futsal`) | `ormawa` | UKM dan lainnya |

> Akun HIMA/UKM ditambahkan oleh **BKKH melalui menu Manajemen User**. Gunakan password default, lalu pengurus mengubahnya sendiri melalui menu Profil.

#### Bagaimana Sistem Membedakannya?

Karena semua HIMA ber-role `ormawa`, pembedaan dilakukan melalui **`id_user`** (auto-increment, PRIMARY KEY) dan nama tampilan pada kolom **`nama_lengkap`**. Contoh: `hima_if` mungkin memiliki `id_user = 4`, `hima_si` `id_user = 5`, dst.

#### Session Saat Login

Saat pengurus HIMA Sistem Informasi login dengan `hima_si`, sistem menyimpan identitasnya ke sesi (`app/views/auth/login.php`):

```php
$_SESSION['user_id']   = $user['id_user'];   // misal: 5
$_SESSION['user_role'] = $user['role'];      // 'ormawa'
```

#### Aturan Isolasi Data (Cegah Broken Access Control)

Karena semua HIMA ber-role sama, seluruh query backend **wajib difilter berdasarkan `id_user_ormawa`** yang diisi dari `$_SESSION['user_id']` — bukan berdasarkan role.

```sql
-- SALAH: bisa diakses/dilihat HIMA lain
SELECT * FROM pengajuan WHERE role = 'ormawa';

-- BENAR: hanya data milik organisasi yang login
SELECT * FROM pengajuan WHERE id_user_ormawa = ?;
-- parameter ? diisi $_SESSION['user_id']
```

Referensi penerapan di sistem:
- Semua query data Ormawa di view/controller memakai `id_user_ormawa = ?` (dashboard, riwayat, LPJ, peminjaman, persuratan, dll).
- `app/views/ormawa/detail.php` menambahkan penjagaan eksplisit: role `ormawa`/`bem`/`bpm` hanya bisa melihat detail jika `id_user_ormawa == user_id`.

Dengan pendekatan ini, HIMA Sistem Informasi hanya melihat dasbor, riwayat pengajuan, dan saldo miliknya sendiri — meskipun hak akses halamannya sama persis dengan HIMA Teknik Informatika.

> **Enhancement opsional (belum diimplementasikan, dicatat sebagai usulan):** menambahkan kolom `kategori_ormawa` atau `program_studi_id` pada tabel `users` agar laporan/rekap dapat dikelompokkan per program studi secara langsung. Isolasi data tetap berjalan tanpa kolom ini karena bertumpu pada `id_user_ormawa`.

## 2.6 Mekanisme Keamanan Dasar

| Aspek | Implementasi |
|-------|--------------|
| Autentikasi | Session PHP (`$_SESSION['user_id']`), `session_regenerate_id()` saat login |
| Otorisasi | `check_role()` di Router & controller (berdasarkan `$_SESSION['user_role']`) |
| Input | `sanitize_input()` (trim + `mysqli_real_escape_string`) |
| SQL Injection | Prepared statements (`$conn->prepare()` + `bind_param()`) |
| Password | `password_hash()` / `password_verify()` (bcrypt) |
| Session cookie | `httponly`, `SameSite=Lax`, lifetime 1 hari |
| Guard file langsung | `defined('APP_RUNNING') or die(...)` di setiap file PHP non-entry |
| XSS (sebagian) | `htmlspecialchars()` pada output dinamis |
