# Peran Frontend dan Backend

**Proyek:** Pengembangan Sistem Kemahasiswaan (SKIN) — Kerja Praktik  
**Institusi:** Institut Teknologi Garut  
**Tujuan:** Menjelaskan pembagian peran dan tanggung jawab lapisan **frontend** dan **backend** dalam SKIN, sekaligus menjadi **panduan pembagian kerja tim**.

> Dokumen ini berpasangan dengan `02-struktur-sistem.md` (arsitektur & struktur project), `03-role-dan-fitur.md` (daftar fitur), dan `05-permasalahan-dan-pengembangan.md` (catatan perbaikan arsitektur).

---

## 1. Ringkasan Pemisahan Peran

SKIN menggunakan arsitektur **Custom MVC** (PHP tanpa framework). Secara garis besar:

| Lapisan | Teknologi | Tanggung Jawab |
|---|---|---|
| **Backend** | PHP 8.0, MySQLi, MariaDB/MySQL | Routing, autentikasi & otorisasi, logika bisnis, akses data, keamanan |
| **Frontend** | HTML, CSS, Bootstrap (CDN), JavaScript inline | Tampilan, layout, interaksi pengguna, visualisasi data, notifikasi |

```
BROWSER (Frontend)
   │  HTTP Request (GET halaman / POST aksi / fetch AJAX)
   ▼
index.php → Router → Controller (Backend) → Database
   │
   ▼
View PHP (HTML ter-render) → dikirim ke browser
```

---

## 2. Peran Backend

### 2.1 Tanggung Jawab Utama

| No. | Tanggung Jawab | Implementasi di Sistem |
|---|---|---|
| 1 | Routing & pemetaan halaman | `app/core/Router.php` (`$pageMap`, `$dashboardMap`, `$standalonePages`) |
| 2 | Autentikasi | Session PHP, `check_login()` di `app/helpers/functions.php` |
| 3 | Otorisasi (hak akses per role) | `check_role()` di Router & controller + guard `defined('APP_RUNNING')` |
| 4 | Validasi & sanitasi input | `sanitize_input()` (trim + MySQLi escape), cek tipe data |
| 5 | Logika bisnis | Controller class di `app/controllers/` |
| 6 | Akses data | MySQLi **prepared statements** (`$conn->prepare()` + `bind_param()`) |
| 7 | Keamanan | `password_hash()`/`password_verify()` (bcrypt), session regenerate, anti-SQL Injection |

### 2.2 Inventaris File Backend

| File | Peran |
|---|---|
| `index.php` | Front controller / entry point (bootstrap aplikasi) |
| `config.php` | Konfigurasi DB, konstanta `ROOT_PATH`/`BASE_URL`, session |
| `app/core/Router.php` | Routing, peta halaman, cek role, render view, toast & notifikasi |
| `app/core/Controller.php` | Base class controller (require role, sanitasi, redirect, add_history) |
| `app/helpers/functions.php` | Helper global (session, sanitasi, redirect, check_login, check_role) |
| `app/controllers/PengajuanController.php` | Logika pengajuan dana (buat, edit, revisi) |
| `app/controllers/VerifikasiController.php` | Ajukan pencairan, simpan nomor surat |
| `app/controllers/BendaharaController.php` | Proses pencairan (setujui/tolak) |
| `app/controllers/UserController.php` | Manajemen user & saldo (tambah, edit, atur saldo) |
| `app/controllers/ProfilController.php` | Update profil pengguna |
| `app/controllers/InformasiController.php` | Pengumuman & jadwal rapat |
| `app/controllers/AspirasiController.php` | Submit & tanggapi aspirasi |

### 2.3 Aturan Pemisahan yang Wajib Dijaga

1. **Logika bisnis dan query harus berada di Controller**, bukan di View.
2. **Semua query data Ormawa wajib difilter `id_user_ormawa = ?`** dari `$_SESSION['user_id']` (cegah *Broken Access Control* — lihat `02-struktur-sistem.md` 2.5.6).
3. **Jangan menulis query mentah di dalam markup HTML view** — kapsulkan di controller (perbaikan dari temuan `05-permasalahan-dan-pengembangan.md` 5.3 #4 yang masih menyisakan logika di view seperti `tambah_pengajuan.php`, `upload_lpj.php`, `verifikasi.php`).
4. **Sanitasi setiap input** dan gunakan **prepared statements** secara konsisten.

---

## 3. Peran Frontend

### 3.1 Tanggung Jawab Utama

| No. | Tanggung Jawab | Implementasi di Sistem |
|---|---|---|
| 1 | Struktur & layout halaman | `app/views/layouts/` (`header.php`, `sidebar.php`, `footer.php`) |
| 2 | Tampilan fungsional per role | `app/views/ormawa`, `verifikator`, `bendahara`, `sarpras`, `admin`, `shared` |
| 3 | Komponen UI & gaya | Bootstrap 5.3.3 + Bootstrap Icons (CDN) |
| 4 | Interaksi client-side | JavaScript **inline** di dalam view + Vanilla JS |
| 5 | Popup & notifikasi | SweetAlert2, toast notification |
| 6 | Tabel & pencarian | DataTables |
| 7 | Kalender jadwal | FullCalendar (peminjaman & jadwal rapat) |
| 8 | Grafik dashboard | Chart.js |
| 9 | Tema terang/gelap | CSS custom `[data-bs-theme]` + toggle di navbar |

### 3.2 Inventaris File Frontend

| Folder / File | Peran |
|---|---|
| `app/views/layouts/header.php` | Head HTML, CSS custom, navbar, container toast |
| `app/views/layouts/sidebar.php` | Navigasi per role, tema terang/gelap, logo dinamis |
| `app/views/layouts/footer.php` | Footer & penutup layout |
| `app/views/auth/` | Halaman login & logout |
| `app/views/shared/` | Profil, aspirasi publik, halaman verifikasi surat, panduan |
| `app/views/ormawa/` | Halaman pengaju (pengajuan, riwayat, LPJ, peminjaman, persuratan, informasi) |
| `app/views/verifikator/` | Halaman verifikasi & monitoring (BEM/BPM/BKKH/WR3, pengelolaan aspirasi/regulasi) |
| `app/views/bendahara/` | Halaman pencairan dana & verifikasi LPJ |
| `app/views/sarpras/` | Halaman verifikasi peminjaman & inventaris barang |
| `app/views/admin/` | Halaman manajemen user, saldo, pengaturan sistem |

### 3.3 Catatan Penting Saat Ini

- **Tidak ada file `.js` lokal** — seluruh JavaScript ditulis **inline** di dalam file view PHP; library eksternal dimuat via CDN (jsDelivr).
- **Tidak ada build tooling** (tanpa Webpack/Vite/npm). Seluruh dependency berjalan via CDN.
- **Usulan ke depan (opsional):** apabila interaksi frontend makin kompleks, pisahkan JavaScript ke folder `assets/js/` (misal `notifikasi.js`, `dashboard.js`) dan CSS kustom ke `assets/css/` agar lebih mudah dikelola dan diuji.

---

## 4. Alur Kolaborasi Frontend–Backend

```
BROWSER (Frontend)
   │  1. User mengirim request (klik menu, submit form, fetch AJAX)
   ▼
index.php (Front Controller)
   │  2. Membutuhkan config.php (DB + session + helpers)
   ▼
app/core/Router.php (Backend)
   │  3. handleGetActions / handlePostActions → delegasi ke Controller
   │  4. Resolve halaman dari $pageMap + cek hak akses (check_role)
   ▼
app/controllers/*.php (Backend)
   │  5. Validasi input, proses logika bisnis, query data (prepared statements)
   ▼
DATABASE  ─── hasil query ───►  kembali ke controller
   ▼
app/views/.../*.php (Frontend)
   │  6. Render HTML di dalam layout header/sidebar/footer
   ▼
HTTP Response → browser menampilkan halaman
```

| Tipe Permintaan | Contoh | Pemegang Tanggung Jawab |
|---|---|---|
| GET halaman | `index.php?page=dashboard`, `?page=riwayat` | Router → render view |
| POST aksi | submit pengajuan, verifikasi, pencairan, profil | Router → Controller → DB → redirect |
| fetch AJAX | `tandai_notif_terlihat` (notifikasi dana cair) | Frontend (JS) → Backend endpoint → JSON/aksi |

---

## 5. Peta Pembagian Tugas Tim

Berikut peta tugas yang dapat dibagi kepada anggota tim berdasarkan **modul**. Isi kolom **Anggota** sesuai pembagian kelompok masing-masing.

**Kategori tugas:** `BE` = Backend (controller, routing, database) · `FE` = Frontend (view, UI, JS) · `FS` = Full-stack (kedua sisi)

| No. | Modul | Cakupan Kerja Backend | Cakupan Kerja Frontend | Kategori | Anggota |
|---|---|---|---|---|---|
| 1 | Pengajuan & Verifikasi Dana | Logika submit/edit, status berjenjang, rollback saldo | Form pengajuan, tabel riwayat, timeline status | **FS** | |
| 2 | Pencairan Dana | Proses setujui/tolak bendahara, update saldo | UI proses pencairan, notifikasi dana cair | **FS** | |
| 3 | LPJ (Laporan Pertanggungjawaban) | Upload/revisi, verifikasi BKKH/WR3 | Form upload, daftar arsip LPJ | **FS** | |
| 4 | Persuratan Digital | Generator proposal/surat/LPJ, nomor surat, QR | Form generator, halaman cetak/view, peta arsip | **FS** | |
| 5 | Peminjaman Fasilitas | Verifikasi BKKH & Sarpras, cek bentrok jadwal | Form peminjaman, kalender ketersediaan | **FS** | |
| 6 | Komunikasi & Informasi | Pengumuman, jadwal rapat, regulasi, aspirasi | Pusat informasi, kalender, form aspirasi | **FS** | |
| 7 | Admin & Manajemen User | CRUD user, saldo, pengaturan sistem (kop/logo) | Form tambah/edit user, pengaturan sistem | **FS** | |
| 8 | Notifikasi Realtime (SSE) | Endpoint SSE, INSERT tabel `notifikasi`, perbaikan route `tandai_notif_terlihat` | Lonceng notifikasi + badge, polling/EventSource, toast | **FS** | |
| 9 | Perbaikan Arsitektur (pindah logika dari view → controller) | Refactor query DB ke controller | Bersihkan view hanya untuk tampilan | **BE** | |

### Panduan Konvensi Kerja

1. **Struktur file**: tambahkan views di folder sesuai role; logika di controller dengan nama class konsisten (`XxxController`).
2. **Penamaan file/variabel**: konsisten membedakan *snake_case* (DB, file view) dan *camelCase* (method, variabel JS).
3. **Perubahan kecil bertahap**: jangan menulis ulang seluruh sistem; kerjakan modul per modul.
4. **Uji alur utama**: setiap selesai modul, uji alur *Pengajuan → Verifikasi → Pencairan → LPJ* agar tidak ada regresi.
5. **Commit terpisah**: satu modul/1 topik per commit dengan pesan jelas (misal: `feat: notifikasi realtime SSE`).

---

## 6. Kesimpulan

Pembagian peran frontend dan backend di SKIN harus tetap berpegang pada prinsip **MVC**: backend menangani logika, data, dan keamanan; frontend menangani tampilan dan interaksi pengguna. Meskipun saat ini sebagian logika masih berada di view, perbaikan arsitektur dilakukan bertahap per modul tanpa menulis ulang sistem dari nol.

Dengan peta pembagian tugas di atas, setiap anggota tim dapat bekerja secara paralel pada modul yang berbeda dengan batas tanggung jawab yang jelas, serta tetap menjaga konsistensi kode dan keamanan akses data.