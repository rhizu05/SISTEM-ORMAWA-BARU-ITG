# Kontrak Data & Endpoint — SKIN

**Proyek:** Pengembangan Sistem Kemahasiswaan (SKIN) — Kerja Praktik  
**Versi:** 1.0 (draf awal)  
**Branch kerja:** `feature/backend-contracts`  
**Tujuan:** Jembatan antara **backend** (controller/query) dan **frontend** — menjadi acuan saat integrasi HTML hasil Figma/MCP ke view PHP, serta spesifikasi endpoint AJAX yang dibutuhkan UI baru.

> Dokumen ini **bukan** API docs (OpenAPI/REST). SKIN adalah monolit PHP *page-based*: halaman di-render server-side. Bagian endpoint JSON hanya mencakup kebutuhan AJAX nyata (notifikasi, kalender).

---

## 1. Model Request–Response

### 1.1 Halaman (server-rendered)

```
Browser → GET index.php?page=<nama_halaman> → Router::dispatch()
         → cek check_login()/check_role() → render view PHP → HTML
```

### 1.2 Aksi Form (POST + redirect)

```
Browser → POST index.php?page=<aksi> → Router → Controller::method()
         → proses di DB → redirect(index.php?page=...&status/error=...)
```

### 1.3 AJAX JSON (terbatas, untuk komponen realtime)

```
Browser (JS) → fetch('index.php?page=<endpoint>') → Controller → JSON response
```

---

## 2. Session Variables (Tersedia di Semua View)

| Variabel | Di-set saat | Keterangan |
|---|---|---|
| `$_SESSION['user_id']` | login | Identitas unik pengguna (wajib dipakai filter data Ormawa) |
| `$_SESSION['user_role']` | login | Role (`ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara`, `sarpras`, `sarpras_barang`, `admin`) |
| `$_SESSION['nama_lengkap']` | login & update profil | Nama tampilan Ormawa/pengguna |
| `$_SESSION['foto_profil']` | login & update profil | Nama file foto profil di `uploads/profil/` |
| `$_SESSION['status_akun']` | login | `aktif` / `nonaktif` (mengontrol menu pengajuan) |
| `$_SESSION['username']` | login | Nama pengguna (bila tersedia) |
| `$_SESSION['konfigurasi']` | load | Pengaturan sistem (`nama_aplikasi`, logo, kop surat) |

---

## 3. Peta Halaman (Route GET)

Berdasarkan `$pageMap` di `app/core/Router.php` (roles kosong = publik).

| Halaman (`?page=`) | View | Role yang Diizinkan |
|---|---|---|
| `login`, `logout`, `verify_page`, `aspirasi`, `panduan` | auth/shared | **Publik** |
| `dashboard` | per role (`$dashboardMap`) | semua role login |
| `tambah`, `edit`, `upload_lpj`, `revisi_lpj`, `arsip_lpj`, `peminjaman_tempat`, `peminjaman_barang`, `buat_proposal`, `arsip_proposal`, `edit_proposal`, `buat_surat_lain`, `arsip_surat_lain`, `buat_lpj_otomatis`, `arsip_lpj_otomatis` | `ormawa/*` | `ormawa`, `bem`, `bpm` |
| `riwayat` | `ormawa/riwayat` | `ormawa`, `bem`, `bpm`, `bkh`, `bendahara`, `admin` |
| `arsip_digital`, `view_surat_lain`, `view_lpj_otomatis`, `view_peminjaman`, `view_proposal`, `detail` | `ormawa/*` | `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara`, `admin` |
| `cetak_surat`, `surat_balasan` | `ormawa/*` | `ormawa`, `bem`, `bpm`, `bkh`, `bendahara`, `admin` |
| `verifikasi` | `verifikator/verifikasi` | `bem`, `bpm`, `bkh`, `wr3`, `admin` |
| `verifikasi_lpj` | `verifikator/verifikasi_lpj` | `bkh`, `wr3`, `admin` |
| `verifikasi_tempat`, `verifikasi_barang_bkkh`, `ajukan_pencairan`, `arsip_surat` | `verifikator/*` | `bkh`, `admin` |
| `manage_users`, `hapus_user`, `tambah_user`, `edit_user`, `atur_saldo`, `atur_sistem` | `admin/*` | `bkh`, `admin` |
| `manage_saldo` | `admin/manage_saldo` | `bkh`, `wr3`, `admin` |
| `proses` | `bendahara/proses` | `bendahara` |
| `profil` | `shared/profil` | semua role login |
| `manage_regulasi`, `manage_aspirasi`, `buat_surat_peringatan` | `verifikator/*` | `bpm` (surat peringatan: `bpm`, `bkh`, `admin`) |
| `sarpras_verifikasi_ruangan` | `sarpras/verifikasi_ruangan` | `sarpras` |
| `sarpras_verifikasi_barang`, `manage_barang` | `sarpras/*` | `sarpras_barang` |
| `pusat_informasi`, `jadwal_rapat` | `ormawa/*` | `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara`, `admin` |

---

## 4. Aksi POST (Route → Controller → Field)

| Route (`?page=`) | Controller::method | Field yang Dikirim | Redirect / Sukses |
|---|---|---|---|
| `tambah` | `PengajuanController::tambah` | `nama_kegiatan`, `tanggal_pengajuan`, `dana_diajukan` (format rupiah), `file_proposal` (PDF) | `?page=riwayat&status=tambah_sukses` atau `?page=tambah&error=...` |
| `edit` (+`id` di GET) | `PengajuanController::edit` | `nama_kegiatan`, `tanggal_pengajuan`, `dana_diajukan`, `file_proposal` (opsional) | `?page=riwayat&status=edit_sukses` |
| `ajukan_pencairan` | `VerifikasiController::ajukanPencairan` | `id` | `?page=dashboard&success=bendahara_sukses` |
| `input_nomor_surat` | `VerifikasiController::simpanNomorSurat` | `id_pengajuan`, `nomor_surat` | `?page=arsip_surat&status=nomor_sukses` |
| `verifikasi_bendahara` | `BendaharaController::verifikasi` | `id_pengajuan`, `dana_disetujui`, `status_verifikasi` (`disetujui`/lain), `catatan` | `?page=proses&status=verifikasi_sukses` |
| `profil` | `ProfilController::update` | `nama_lengkap`, `nama_ketua`, `nama_sekretaris`, `nama_bendahara`, `alamat`, `telepon`; file: `foto_profil`, `logo_ormawa`, `ttd_ketua`, `ttd_sekretaris`, `ttd_bendahara` | `?page=profil&status=update_sukses` |
| `pusat_informasi` | `InformasiController::handlePengumuman` | `tambah_pengumuman`: `judul`, `isi`, `lampiran`; `hapus_pengumuman`: `id_pengumuman` | `?page=pusat_informasi&status=...` |
| `jadwal_rapat` | `InformasiController::handleJadwalRapat` | `tambah_rapat`: `judul_rapat`, `deskripsi`, `tanggal_rapat`, `jam_rapat`, `lokasi`, `link_meeting`, `peserta[]`; `hapus_rapat`: `id_rapat` | `?page=jadwal_rapat&status=...` |
| `aspirasi` | `AspirasiController::submit` | `kirim_aspirasi`, `nama`, `email`, `kategori`, `subjek`, `isi` (publik) | `?page=aspirasi&status=aspirasi_sukses` |
| `manage_aspirasi` | `AspirasiController::tanggapi` | `tanggapi_aspirasi`, `id_aspirasi`, `tanggapan`, `status` (BPM) | `?page=manage_aspirasi&status=tanggapan_sukses` |
| `tambah_user` | `UserController::tambahUser` | `nama_lengkap`, `username`, `password`, `role` | `?page=manage_users&status=tambah_user_sukses` |
| `edit_user` (+`id` GET) | `UserController::editUser` | `nama_lengkap`, `username`, `password` (opsional), `role` | `?page=manage_users&status=edit_user_sukses` |
| `atur_saldo` (+`id` GET) | `UserController::aturSaldo` | `saldo` | `?page=manage_saldo&status=saldo_sukses` |
| `toggle_status` (GET action) | `UserController::toggleStatus` | `id`, `new_status` (`aktif`/`nonaktif`) | `?page=manage_users&status=toggle_sukses` |

> **Catatan (gap arsitektur):** Refactor modul verifikasi proposal (BEM→BPM→BKKH→WR3) dan verifikasi LPJ **telah dipindah ke `VerifikasiController::verifikasiProposal` & `verifikasiLpj`** (route `?page=verifikasi`, `?page=verifikasi_lpj`). Aksi POST lain yang masih inline di view (mis. `tambah_pengajuan.php`, `upload_lpj.php`, `manage_regulasi.php`) masih butuh refactor bertahap (lihat `05-permasalahan-dan-pengembangan.md` 5.3 #4).

---

## 5. Status Enum — Tabel `pengajuan`

Sumber kebenaran: `scripts/db_pengajuan.sql` (kolom `status`).

```
Draft
Diajukan Ke BEM
Ditolak BEM
Diajukan Ke BPM
Ditolak BPM
Verifikasi BKKH
Ditolak BKKH
Verifikasi WR3
Ditolak WR3
Disetujui WR3, Siap Diajukan ke Bendahara
Diajukan ke Bendahara
Dana Cair
LPJ Diajukan
LPJ Ditolak BKKH
LPJ Diverifikasi
Selesai
```

> **Catatan konsistensi:** 
> - ~~`'Ditolak BKH'`~~ → telah diperbaiki menjadi `'Ditolak BKKH'` di `PengajuanController.php:28`.
> - `'Ditolak Bendahara'` **belum ada di enum** `status` (kolom ENUM di `scripts/db_pengajuan.sql`) padahal di-set oleh `BendaharaController.php:36`. Pada MySQL strict, update ini berisiko gagal/terpotong. **Perlu penambahan enum `'Ditolak Bendahara'`** (migrasi + audit data historic).

---

## 6. Enum Lainnya

| Kolom | Nilai |
|---|---|
| `users.role` | `admin`, `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara`, `sarpras`, `sarpras_barang` |
| `users.status_akun` | `aktif`, `nonaktif` |
| `aspirasi.status` | `baru`, `diproses`, `selesai` *(sesuaikan dengan aktual view `manage_aspirasi.php`)* |
| `peminjaman_tempat.status_bkkh` / `status_sarpras` | `Pending`, `Disetujui`, `Ditolak` |

---

## 7. Kontrak Data per View Utama (untuk Integrasi HTML dari Figma/MCP)

Variabel-variabel yang tersedia di view; HTML baru dari MCP harus memetakan token placeholder ke variabel ini (di-escape dengan `htmlspecialchars()` di sisi tampilan).

| View | Variabel Kunci | Bentuk Data |
|---|---|---|
| `ormawa/dashboard.php` | `total_saldo`, `saldo_terpakai`, `saldo_dalam_proses`, `sisa_saldo`, `saldo_digunakan_dan_proses`, `total_diajukan`, `total_proses` | angka |
| | `notifikasi_cair_ormawa` | `[{id_pengajuan, nama_kegiatan}]` (flag `notif_cair_terlihat=0`) |
| | `events_calendar` | `[{title, start, end, color, textColor, description}]` |
| `ormawa/riwayat.php` | `$result` (iterasi `fetch_assoc`) | `{id_pengajuan, nama_kegiatan, tanggal_pengajuan, status, dana_diajukan}` |
| `ormawa/detail.php` | `$pengajuan` | `p.* JOIN users (nama_ormawa, logo_ormawa, role)` |
| | `$histori` | timeline `histori_status` (urutan waktu) |
| | guard | `ormawa/bem/bpm` HANYA boleh jika `id_user_ormawa == user_id` |
| `verifikator/verifikasi.php` | `$pengajuan` (detail) + form aksi (`aksi`, `catatan`, `csrf_token`) | logika POST dipindah ke `VerifikasiController::verifikasiProposal` |
| `verifikator/verifikasi_lpj.php` | `$pengajuan` (LPJ) + form aksi (`aksi`, `catatan`, `csrf_token`) | logika POST dipindah ke `VerifikasiController::verifikasiLpj` |
| `verifikator/dashboard.php` | `$notifikasi_cair`, antrean verifikasi, kalender | `events_json`, daftar `nama_ormawa` |
| `ormawa/peminjaman_tempat.php` | `$master_ruangan` (daftar ruangan), riwayat peminjaman user | `{id_ruangan, nama_ruangan, ...}` |
| `ormawa/peminjaman_barang.php` | `$items_json`, riwayat peminjaman | barang yang tersedia |
| `ormawa/pusat_informasi.php` | `$news_result` (pengumuman), regulasi, `$user_role` | guard `isset()/num_rows` sudah diterapkan |
| `shared/profil.php` | `$profil` (baris users) + uploads TTD/logo | `{nama_lengkap, nama_ketua, ...}` |
| `admin/manage_users.php` | daftar user | `{id_user, nama_lengkap, username, role, status_akun, saldo}` |

> Catatan: pada beberapa view query masih langsung di file view. Saat integrasi HTML baru, pindahkan/minta backend menyediakan data di controller agar view hanya berisi tampilan (lihat Panduan `docs/Peran Frontend dan Backend.md`).

---

## 8. Endpoint AJAX (Spesifik — Bukan REST)

| Endpoint | Method | Tujuan | Kebutuhan | Status |
|---|---|---|---|---|
| `index.php?page=tandai_notif_terlihat` | POST, JSON `{ids: number[]}` | Menandai notifikasi dana cair sebagai terbaca (update `notif_cair_terlihat`) | Dipanggil JS di `dashboard.php` | ✅ **Diimplementasikan** (`NotifikasiController::tandaiTerlihat`) |
| `index.php?page=api_notifikasi_belum_baca` | GET → JSON | Data notifikasi belum dibaca utk badge/lonceng (tabel `notifikasi`) | Fitur notifikasi realtime | ✅ **Diimplementasikan** (`NotifikasiController::belumBaca`). Tabel `notifikasi` kini terisi: `add_notifikasi()` dipakai saat dana cair |
| `index.php?page=notifikasi_stream` | GET → `text/event-stream` | Push realtime SSE ke user | Fitur notifikasi realtime | 🔜 Baru (tahap berikutnya) |
| `index.php?page=api_kalender_peminjaman` | GET → JSON | Data kalender ketersediaan fasilitas seluruh peminjaman (format FullCalendar events) | UI kalender modern | ✅ **Diimplementasikan** (`ApiController::kalenderPeminjaman`) |

### Konvensi Respons JSON yang Diusulkan

```json
{ "success": true, "message": "…", "data": { }, "redirect": "index.php?page=…" }
{ "success": false, "message": "…" }
```

Helper yang akan ditambahkan di `app/core/Controller.php`:

```php
protected function jsonResponse($data, int $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
```

---

## 9. Checklist Backend (Branch `feature/backend-contracts`)

- [x] **P1** Dokumen Kontrak Data ini (v1)
- [x] **P1** Perbaiki route `tandai_notif_terlihat` + implementasi `tandaiTerlihat()` (`NotifikasiController`)
- [x] **P1** Tambah `jsonResponse()` helper di `Controller.php`
- [x] **P2** Perbaiki inkonsistensi `'Ditolak BKH'` → `'Ditolak BKKH'` di `PengajuanController.php`
- [x] **P2** Endpoint `api_notifikasi_belum_baca` (`NotifikasiController::belumBaca`) + helper `add_notifikasi()` — tabel `notifikasi` mulai dipakai (contoh: saat dana cair)
- [x] **P2** Endpoint `api_kalender_peminjaman` (`ApiController::kalenderPeminjaman`, format FullCalendar events)
- [x] **P2** Script migrasi enum `'Ditolak Bendahara'` (`scripts/update_enum_ditolak_bendahara.php`) — **tidak ikut dieksekusi** (menunggu config lokal & audit data historis, lihat catatan di script)
- [x] **P3** Refactor verifikasi proposal & LPJ dari view → `VerifikasiController` (tambah validasi status tahap + CSRF)
- [x] **P3** Fondasi CSRF (`csrf_token/csrf_field/csrf_verify`) + diterapkan pada form verifikasi & verifikasi LPJ
- [x] **P3** Validasi MIME PDF (`is_valid_pdf`) di `PengajuanController` (tambah & edit)
- [ ] **P3** CSRF diterapkan pada seluruh form POST lain (bertahap); validasi MIME upload lain (profil/TTD/gambar)