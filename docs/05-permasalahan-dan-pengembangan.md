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
| 9 | **Undefined `$nama_aplikasi` di footer** | Warning saat render | Fallback `$nama_aplikasi = $nama_aplikasi ?? 'SKIN'` |
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

- [ ] **Monitoring pengajuan** — tambah detail posisi & pihak yang menangani di setiap tahap status.
- [ ] **Follow-up & komunikasi pengajuan** — mekanisme tindak lanjut dan notifikasi di dalam sistem.
- [ ] **Notifikasi realtime (SSE)** — push perubahan status ke semua role tanpa ganti stack; fallback short polling; memanfaatkan tabel `notifikasi` yang sudah ada.
- [ ] **Peminjaman** — kalender ketersediaan ruangan/barang, pencegahan bentrok jadwal.
- [ ] **Program kerja tahunan** — fitur input dan monitoring proker Ormawa secara berkala.
- [ ] **Transparansi kegiatan** — halaman publik/internal yang menampilkan kegiatan Ormawa.
- [ ] **Aspirasi terpusat** — satu jalur resmi di sistem menggantikan berbagai saluran informal.
- [ ] **Dashboard** — tambah grafik pengeluaran per ormawa, rekapitulasi proker, trending kegiatan.
- [ ] **Cetak dokumen** — QR code lokal (library Composer seperti `endroid/qr-code`).
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

Sistem SKIN telah berfungsi dan dapat dikembangkan lebih lanjut. Fondasi arsitektur MVC yang baru (hasil restrukturisasi) memberikan titik awal yang baik secara teknis. Namun berdasarkan hasil wawancara stakeholder, pengembangan SKIN tidak hanya mencakup perbaikan teknis — sistem perlu diperluas menjadi **pusat layanan kemahasiswaan terpadu** yang mendukung monitoring, komunikasi, transparansi, dan sentralisasi informasi.

Prioritas pengembangan disarankan pada dua jalur paralel:

1. **Perbaikan teknis** — CSRF, validasi upload, konsistensi arsitektur MVC, keamanan session
2. **Ekspansi fungsional** — monitoring pengajuan, program kerja tahunan, notifikasi, peminjaman fasilitas terpusat, dan aspirasi terpusat

## 5.6 Permasalahan Berdasarkan Hasil Wawancara Stakeholder

Berikut adalah permasalahan yang diidentifikasi dari wawancara (belum tercakup dalam temuan teknis di 5.2/5.3):

| # | Permasalahan | Sumber | Dampak |
|---|---|---|---|
| 1 | Tidak ada mekanisme follow-up atau komunikasi saat pengajuan terlambat | BEM (sebagai pengaju & koordinator) | Harus menghubungi pihak terkait secara manual di luar sistem |
| 2 | Status pengajuan kurang informatif setelah masuk tahap persetujuan | BEM & Mahasiswa *(Pending)* | Tidak tahu posisi pengajuan atau penyebab keterlambatan; keluhan serupa juga diutarakan luas oleh mahasiswa melalui kuesioner |
| 3 | Tidak ada fitur monitoring program kerja tahunan beserta progres/kendala kegiatan | BPM (sebagai pengawas) | BPM tidak dapat memantau realisasi proker, progres, maupun kendala di lapangan tanpa meminta laporan manual |
| 4 | Tidak ada akses read-only keuangan & LPJ untuk BPM | BPM (sebagai pengawas) | BPM tidak dapat menjalankan fungsi pengawasan anggaran, status LPJ, dan pertanggungjawaban tanpa data di sistem |
| 5 | Peminjaman fasilitas berpotensi bentrok jadwal | Ormawa (BEM & BPM), BKKH | Beberapa Ormawa dapat mengajukan ruangan/fasilitas yang sama pada waktu bersamaan |
| 6 | Aspirasi tersebar di berbagai jalur informal | BKKH | Informasi tidak terpusat, berpotensi tidak tertangani — padahal BPM yang berwenang mengelolanya |
| 7 | Dokumentasi kegiatan mahasiswa jangka panjang sulit ditelusuri | BKKH | Data yang dibutuhkan kembali beberapa tahun kemudian sulit ditemukan |
| 8 | Belum ada transparansi kegiatan Ormawa di sistem | BEM (koordinator), BPM (pengawas) | Aktivitas Ormawa tidak dapat diketahui pihak berkepentingan tanpa bertanya langsung |
| 9 | Pengelolaan dokumen belum sepenuhnya terpusat | BKKH | Dokumen tersebar dan tidak mudah diakses kembali |
| 10 | Tidak ada notifikasi realtime perubahan status | BEM, BPM, BKKH, Mahasiswa *(Pending)* | Pengguna harus terus membuka sistem untuk mengetahui perkembangan pengajuan; komunikasi tetap manual di luar sistem |

## 5.7 Potensi Pengembangan — Kebutuhan Fungsional Baru dari Wawancara

Berdasarkan konsolidasi hasil wawancara stakeholder (BKKH, BEM, BPM), berikut fitur-fitur yang diidentifikasi sebagai kebutuhan pengembangan, diurutkan berdasarkan prioritas:

### Prioritas Sangat Tinggi (muncul dari semua/hampir semua stakeholder)

- [ ] **Monitoring & tracking status pengajuan yang lebih informatif** — tampilkan posisi pengajuan secara jelas di setiap tahap, lengkap dengan informasi pihak yang sedang menangani *(dibutuhkan: semua Ormawa, BKKH)*
- [ ] **Peminjaman fasilitas terpusat** — informasi ketersediaan real-time untuk ruangan, barang, lapangan; pencegahan bentrok jadwal otomatis *(dibutuhkan: semua Ormawa, BKKH)*

### Prioritas Tinggi

- [ ] **Notifikasi realtime perubahan status (SSE)** — pengguna mendapat pemberitahuan instan saat ada pengajuan baru atau status berubah, tanpa perlu membuka sistem terus-menerus; dapat diimplementasikan tetap di stack PHP (SSE) atau fallback short polling *(dibutuhkan: semua role — BEM, BPM, BKKH, HIMA/UKM)*
- [ ] **Mekanisme follow-up dan komunikasi pengajuan** — saluran tindak lanjut di dalam sistem saat pengajuan terlambat atau mengalami kendala *(dibutuhkan: HIMA/UKM, BEM sebagai pengaju)*
- [ ] **Verifikasi/approval pengajuan HIMA/UKM oleh BEM** — panel khusus di dashboard BEM untuk menyetujui/menolak pengajuan yang masuk *(dibutuhkan: BEM sebagai verifikator)*
- [ ] **Transparansi kegiatan Ormawa** — tampilkan daftar kegiatan yang telah/sedang dilaksanakan *(dibutuhkan: BEM sebagai koordinator, BPM sebagai pengawas)*
- [ ] **Perencanaan & monitoring program kerja tahunan** — Ormawa input rencana proker; **BPM** review berkala tiap 3–6 bulan *(dibutuhkan: BPM sebagai pengawas)*
- [ ] **Aspirasi terpusat** — satu wadah resmi di sistem dikelola oleh **BPM**, menggantikan berbagai jalur informal *(dibutuhkan: BKKH, BPM)*

### Prioritas Menengah

- [ ] **Monitoring keuangan Ormawa untuk BPM (read-only)** — BPM dapat melihat histori keuangan dan penggunaan anggaran tanpa bisa mengubah data *(dibutuhkan: BPM sebagai pengawas)*
- [ ] **Monitoring LPJ seluruh Ormawa untuk BPM** — keterkaitan pengajuan → pencairan → pelaksanaan → LPJ dapat ditelusuri BPM *(dibutuhkan: BPM sebagai pengawas)*
- [ ] **Sentralisasi dokumen administrasi** — proposal, surat, daftar hadir, dan dokumentasi kegiatan tersimpan dan mudah dicari kembali *(dibutuhkan: BKKH, semua Ormawa)*
- [ ] **Pelaporan prestasi dan kompetisi** — Ormawa dapat melaporkan keikutsertaan dan pencapaian di kompetisi *(dibutuhkan: BKKH)*
- [ ] **Penyesuaian role dan hak akses** — sesuaikan dengan struktur kepengurusan aktual (misal: Seskab untuk administrasi BEM) *(dibutuhkan: BEM)*
