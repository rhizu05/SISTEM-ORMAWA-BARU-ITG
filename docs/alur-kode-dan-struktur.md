# Penjelasan Alur Kode dan Struktur Folder Sistem Keuangan

Dokumen ini menjelaskan alur kode dan struktur folder dari **Sistem Pengajuan Keuangan Kemahasiswaan** (ITG) berbasis PHP MVC.

## 📋 Ringkasan Project

Aplikasi ini mengelola pengajuan dana, verifikasi berjenjang, pencairan, pengumpulan LPJ, serta administrasi ormawa (seperti peminjaman tempat dan barang).

---

## 🔄 Alur Kode Utama

Aplikasi menggunakan arsitektur MVC (Model-View-Controller) buatan sendiri (custom) dengan routing terpusat.

```text
index.php (Entry Point)
    │
    ├── config.php → Inisialisasi DB, session, constants, load helpers
    │
    ├── Load semua Controllers & Core classes
    │
    └── Router::dispatch()
            │
            ├── applySecurityMiddleware()
            │   ├── CSRF verify (khusus POST)
            │   ├── Session timeout check & regeneration
            │   ├── Rate limiting (brute force protection)
            │   └── Security headers
            │
            ├── handleGetActions()  → Menangani API endpoints via method GET
            ├── handlePostActions() → Delegasi aksi form ke method Controller
            │
            └── Render view
                ├── Cek Role & Login
                ├── Non-standalone: header.php + sidebar.php + content + footer.php
                └── Standalone: content only (contoh: login, cetak surat)
```

---

## 📁 Struktur Folder

### `/` (Root Directory)
| File | Fungsi |
|------|--------|
| `index.php` | Entry point aplikasi, bootstrap awal dan delegasi ke router. |
| `config.php` | Konfigurasi utama: koneksi DB, BASE_URL, inisialisasi session, load helper. |
| `config.example.php` | Template konfigurasi, harus disalin menjadi `config.php`. |

### `/app/core/`
| File | Fungsi |
|------|--------|
| `Router.php` | Menangani routing URL `?page=...` ke view/controller. Juga berisi middleware keamanan dan pengecekan otorisasi role (RBAC). |
| `Controller.php` | Abstract base class untuk semua controller. Menyediakan fungsi `requireLogin()`, `requireRole()`, `redirect()`, `sanitize()`, `addHistory()`, dan `jsonResponse()`. |

### `/app/controllers/`
| Controller | Fungsi |
|------------|--------|
| `UserController` | CRUD manajemen user, toggle status aktif/nonaktif, atur saldo. |
| `PengajuanController` | Tambah dan revisi pengajuan proposal, follow-up status pengajuan. |
| `VerifikasiController` | Proses verifikasi proposal, verifikasi LPJ, pengajuan pencairan, penomoran surat. |
| `BendaharaController` | Proses pencairan dana ke rekening tujuan. |
| `ProfilController` | Update profil user (avatar, dll). |
| `AspirasiController` | Submit aspirasi publik dan tanggapan dari verifikator. |
| `InformasiController` | Manajemen pengumuman dan jadwal rapat. |
| `NotifikasiController` | Endpoint untuk menandai notifikasi (terlihat/baca). |
| `ApiController` | Endpoint API untuk kalender peminjaman dan keperluan eksternal. |

### `/app/helpers/`
| File | Fungsi |
|------|--------|
| `functions.php` | Kumpulan fungsi utilitas: sanitasi, redirect, CSRF, validasi upload file, rate limit, audit log, dan broadcast notifikasi. |
| `session.php` | Manajemen sesi aman: timeout, regenerate ID, konfigurasi cookie (HttpOnly, Secure, SameSite=Strict). |
| `twofa.php` | Fungsi untuk setup dan verifikasi otentikasi dua faktor (2FA / TOTP). |
| `mailer.php` | Fungsi pengiriman email melalui SMTP Brevo. |

### `/app/api/`
| File | Fungsi |
|------|--------|
| `sse_notifikasi.php` | Endpoint Server-Sent Events (SSE) untuk mendorong notifikasi real-time ke klien. |
| `api_dashboard.php` | Mengembalikan data statistik dashboard dalam format JSON. |
| `export_report.php` | Menangani ekspor laporan ke format PDF atau Excel. |

### `/app/views/`
Menggunakan native PHP sebagai template engine.
| Folder | Fungsi |
|--------|--------|
| `layouts/` | `header.php`, `sidebar.php`, `footer.php` — template utama. |
| `auth/` | Halaman login, logout, setup/verify 2FA, dan lupa password. |
| `ormawa/` | 27 file view — dashboard ormawa, form pengajuan, LPJ, arsip, cetak surat, dan peminjaman. |
| `verifikator/` | 11 file view — dashboard dan form verifikasi (proposal, LPJ, tempat, barang), arsip surat. |
| `admin/` | 7 file view — manajemen pengguna, pengaturan saldo, dan konfigurasi sistem. |
| `bendahara/` | 3 file view — dashboard dan halaman proses pencairan dana. |
| `sarpras/` | 4 file view — dashboard sarpras, verifikasi ruangan, verifikasi dan manajemen barang. |
| `shared/` | File view yang bisa diakses multi-role: profil, aspirasi publik, panduan, halaman verifikasi publik. |

### `/assets/`
- `css/` — Stylesheet (Bootstrap CSS, custom CSS).
- `js/` — Skrip frontend (Bootstrap JS, handler notifikasi SSE, inisialisasi chart).
- `images/` — Asset gambar statis dan logo.

### `/uploads/`
Tempat penyimpanan file unggahan. Harus memiliki permission yang tepat (writeable).
| Folder | Isi |
|--------|-----|
| `proposal/` | File proposal (PDF) dari pengguna. |
| `proposal_ttd/` | File proposal yang sudah ditandatangani/diverifikasi. |
| `lpj/` | File Laporan Pertanggungjawaban (PDF). |
| `lpj_lampiran/` | File lampiran LPJ pendukung. |
| `pengumuman/` | Gambar banner untuk pusat informasi. |
| `profil/` | Avatar/foto profil pengguna. |
| `surat/` | Arsip surat resmi yang di-generate sistem. |
| `regulasi/` | Dokumen PDF tentang panduan dan regulasi. |
| `qrcode/` | Gambar QR Code yang dihasilkan untuk validasi surat. |
| `sistem/` | Asset logo dan konfigurasi sistem dinamis. |

### `/scripts/`
Berisi 35 script CLI untuk kebutuhan pemeliharaan. Contohnya: setup database (`.sql`), migrasi schema, data seeding, fix token, trigger simulasi, script test/debug.

### `/storage/logs/`
Tempat penyimpanan file log aplikasi.

### `/tests/`
Berisi direktori dan file untuk Unit Testing menggunakan Pest PHP.

---

## 🔄 Alur Pengajuan Dana

Sistem menggunakan alur verifikasi berjenjang.

```text
Ormawa / BEM / BPM
    │
    ├─1→ Tambah Pengajuan (Upload proposal PDF, pengecekan batas saldo)
    │       Status awal: "Diajukan Ke BEM" (untuk ormawa) atau "Verifikasi BKKH" (untuk BEM/BPM)
    │
    ├─2→ BEM/BPM Verifikasi
    │       Status berubah: "Verifikasi BKKH"
    │
    ├─3→ BKKH Verifikasi
    │       Status berubah: "Verifikasi WR3"
    │
    ├─4→ WR3 Verifikasi
    │       Status berubah: "Diajukan Ke Bendahara"
    │
    └─5→ Bendahara Proses Pencairan
            Status akhir: "Selesai" (Dana cair)
```
> **Catatan:** Pada setiap tahap verifikasi, pengajuan bisa **Ditolak** (misal: "Ditolak BKKH"). Jika ditolak, Ormawa dapat melakukan **revisi** dan mensubmit ulang dokumen tanpa membuat pengajuan baru.

---

## 🔐 Role-based Access Control (RBAC)

| Role | Hak Akses Utama |
|------|-----------------|
| `ormawa` | Membuat pengajuan, upload LPJ, melakukan peminjaman tempat/barang. |
| `bem` | Melakukan verifikasi awal, dashboard verifikator. |
| `bpm` | Verifikasi, manajemen regulasi, manajemen aspirasi publik. |
| `bkh` | Verifikasi final (sebelum WR3), manage akun user, manage saldo. |
| `wr3` | Verifikasi akhir dan penyetujuan dana. |
| `bendahara`| Eksekusi proses pencairan dana. |
| `admin` | Memiliki full akses layaknya `bkh` dan akses konfigurasi sistem. |
| `sarpras` | Melakukan verifikasi peminjaman ruangan. |
| `sarpras_barang`| Verifikasi peminjaman barang dan manajemen inventaris. |

---

## 🔒 Security Features (Fitur Keamanan)

- **CSRF Protection** — Token tervalidasi wajib ada di setiap payload POST.
- **Rate Limiting** — Maksimal 5 percobaan login gagal per IP/Username dalam 15 menit (mencegah Brute Force).
- **Session Security** — Timeout idle 30 menit, rotasi Session ID aman, dan penggunaan flag `HttpOnly` serta `SameSite=Strict`.
- **Two-Factor Authentication (2FA)** — Integrasi TOTP (Time-based One-Time Password) yang wajib untuk role sensitif.
- **Security Headers** — Konfigurasi `X-Frame-Options` (mencegah Clickjacking) dan `X-Content-Type-Options`.
- **Audit Logging** — Pencatatan (log) aktivitas penting/kritis ke dalam tabel `audit_logs` untuk forensik dan monitoring.
- **File Upload Validation** — Pengecekan MIME-Type ketat, batas ukuran, dan sanitasi nama file yang diunggah.
