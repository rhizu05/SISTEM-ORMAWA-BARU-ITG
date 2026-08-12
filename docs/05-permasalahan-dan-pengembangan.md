# 05 — Catatan Permasalahan Awal dan Potensi Pengembangan

## 5.1 Status Proyek Saat Ini

- Sistem telah berjalan pada environment pengembangan (Laragon, `http://localhost/sistem_keuangan`).
- Source code telah mengalami **restrukturisasi** dari file berantakan di root menjadi arsitektur MVC (`app/core`, `app/controllers`, `app/helpers`, `app/views`).
- Database `db_pengajuan` (21 tabel) telah disiapkan dan tervalidasi.
- Seluruh modul fungsional (pengajuan, verifikasi, pencairan, LPJ, peminjaman, persuratan, informasi, admin) tersedia.

## 5.2 Permasalahan Awal yang Ditemukan & Diperbaiki

Catatan permasalahan yang ditemukan selama pemahaman sistem dan proses restrukturisasi:

| # | Permasalahan | Dampak | Solusi yang Diterapkan |
|---|--------------|--------|------------------------|
| 1 | **`BASE_URL` hardcode** `http://localhost:8000` | Redirect mengarah ke port yang tidak ada → `ERR_CONNECTION_REFUSED` | `BASE_URL` dihitung dinamis dari `HTTP_HOST` + `SCRIPT_NAME` di `config.php` |
| 2 | **`$conn` tidak tersedia di view** | `Undefined variable $conn` → fatal error saat render view dari dalam method `Router::render()` | Menambahkan `global $conn;` pada method `render()` |
| 3 | **Kolom DB tidak sinkron** (`logo_ormawa`, `nama_ketua`, `ttd_*`, dll tidak ada) | `Unknown column` saat query `users` | Menjalankan `scripts/setup_complete.php` → menambahkan semua kolom yang hilang |
| 4 | **Nama kolom `role` vs `user_role`** | Query `INSERT/UPDATE` gagal | Memperbaiki 4 query di `UserController.php` agar memakai `role` |
| 5 | **Referensi foto profil stale** (`user_3_*.png` tidak ada di disk) | Browser 404 pada avatar | Update data `foto_profil` di DB + fallback `file_exists()` + default avatar SVG lokal |
| 6 | **`logout.php` & `login.php` dipindah tapi tautan lama masih ada** | 404 `Not Found` | Semua tautan/redirect diubah ke router (`index.php?page=logout`, `?page=login`, `?page=panduan`) |
| 7 | **Pemanggilan fungsi `sanitize()` yang tidak ada** (`bendahara/verifikasi_lpj.php`) | Fatal `Call to undefined function` | Diganti ke `sanitize_input()` |
| 8 | **Library `phpqrcode` tidak pernah ada di project** | `cetak_surat.php` selalu `die()` "Library tidak ditemukan" | Hapus dependensi phpqrcode; QR code digenerate via API eksternal `qrserver.com` |
| 9 | **Undefined `$nama_aplikasi` di footer** | Warning saat render | Fallback `$nama_aplikasi = $nama_aplikasi ?? 'SI-Keuangan'` |
| 10 | **`$news_result->num_rows` bisa error jika query gagal** (`pusat_informasi.php`) | Fatal pada objek `false` | Guard `if ($news_result && $news_result->num_rows > 0)` + fallback session |
| 11 | **`$pengajuan` bisa undefined** (`ajukan_pencairan.php`) | Warning/Trying to access null | Inisialisasi variabel & guard `!empty($pengajuan)` |
| 12 | **`$nama_aplikasi` / double-close `<div>` pada layout** | Struktur HTML tidak valid | Menyesuaikan penutup div & fallback variabel |
| 13 | **File setup/migration berserakan di root** | Root tidak rapi | Dipindah ke folder `scripts/` |
| 14 | **File duplikat/sisa di root** (`sidebar (1).php`, `forward_bendahara.php`, dll) | Kebingungan struktur | Dipindah ke folder `_archive/` |

## 5.3 Permasalahan yang Masih Terbuka / Risiko

| # | Area | Deskripsi Risiko |
|---|------|------------------|
| 1 | **Keamanan (CSRF)** | Form POST tidak dilindungi token CSRF. Risiko serangan cross-site request forgery pada aksi verifikasi/pencairan. |
| 2 | **Keamanan (session)** | Belum ada batas percobaan login (brute force) & cookie `secure` untuk HTTPS. |
| 3 | **Validasi file upload** | Validasi hanya berdasarkan ekstensi (bukan MIME/isi file). Risiko upload file berbahaya. |
| 4 | **Logika bisnis di view** | Beberapa file view masih mengandung logika POST & akses DB langsung (mis. `tambah_pengajuan.php`, `upload_lpj.php`, `verifikasi.php`, `manage_regulasi.php`, dll) — belum konsisten dipindah ke controller. |
| 5 | **Error handling** | Beberapa query tidak memeriksa hasil `prepare()/execute()` secara menyeluruh. |
| 6 | **Dependensi CDN** | Bootstrap/Chart.js/DataTables bergantung koneksi internet; tanpa internet UI rusak. |
| 7 | **Backup database** | `db_pengajuan.sql` di folder `scripts/` mungkin tidak selalu sinkron dengan skema terbaru. |
| 8 | **QR code eksternal** | QR code via `api.qrserver.com` butuh internet; pertimbangkan library lokal untuk produksi. |
| 9 | **`admin` role tidak termanfaatkan** | Role `admin` ada di enum DB tapi tidak ada akun/halaman khusus. |
| 10 | **Konsistensi penamaan** | Campuran Bahasa Indonesia/Inggris & variabel lama (`user_role` di session vs `role` di DB). |

## 5.4 Potensi Pengembangan

### A. Perbaikan Teknis (Foundation)

- [ ] Implementasi **CSRF token** pada semua form POST.
- [ ] **Autoloading PSR-4** (spl_autoload) agar `require_once` manual berkurang.
- [ ] Pindahkan semua **logika bisnis POST dari view → controller** (konsistensi MVC).
- [ ] Gunakan **konstanta path** (`UPLOAD_PATH`, `VIEW_PATH`) untuk menghindari path relatif.
- [ ] Validasi upload berdasarkan **MIME type & ukuran** server-side.
- [ ] **Error/exception handler** sentral + halaman 404/500 kustom.
- [ ] Implementasi **query builder/ORM** (opsional) atau setidaknya repository layer.
- [ ] Migrasi ke **PHP 8.1+** & adopsi fitur modern (enums, readonly, match).

### B. Peningkatan Keamanan

- [ ] Rate limiting login (batas percobaan + lockout).
- [ ] Session `secure` + regenerasi berkala.
- [ ] Log aktivitas (audit login, logout, aksi admin).
- [ ] Sanitasi & validasi semua output dengan `htmlspecialchars()` secara konsisten.
- [ ] Proteksi direktori `uploads/` (mis. mencegah eksekusi PHP di dalam folder upload).

### C. Peningkatan Fitur

- [ ] **Dashboard** — tambah grafik pengeluaran per ormawa, trending kegiatan.
- [ ] **Notifikasi** — email/WhatsApp notification saat status berubah.
- [ ] **Cetak dokumen** — QR code lokal (library Composer seperti `endroid/qr-code`).
- [ ] **Peminjaman** — kalender ketersediaan ruangan/barang, pencegahan bentrok jadwal.
- [ ] **Export/Import** — ekspor data ke Excel/PDF, impor user massal.
- [ ] **Arsip** — filter, pencarian, dan retensi dokumen.
- [ ] **Rekening / pencairan** — integrasi data nominal per kegiatan yang lebih detail.
- [ ] **Role `admin`** — akun super admin terpisah untuk BKKH vs admin IT.

### D. Operasional & Maintainability

- [ ] Menulis **test** (unit/integrasi) untuk alur verifikasi.
- [ ] Menambahkan **CI/CD** sederhana (lint + test).
- [ ] Dokumentasi API/flow yang lebih teknis (ERD lengkap).
- [ ] Menjaga `db_pengajuan.sql` tetap sinkron (migration versioning).
- [ ] `.gitignore` untuk `uploads/` (kecuali file default) agar tidak terdorong ke repo.
- [ ] Standarisasi gaya kode (PSR-12).

## 5.5 Kesimpulan

Sistem SI-Keuangan telah berfungsi dan dapat dikembangkan lebih lanjut. Fondasi arsitektur MVC yang baru (hasil restrukturisasi) memberikan titik awal yang baik. Prioritas pengembangan berikutnya disarankan pada **keamanan (CSRF & validasi upload)**, **konsistensi arsitektur (pindahkan logika dari view ke controller)**, dan **manajemen dependensi** agar sistem lebih siap untuk tahap produksi.
