# 03 — Role dan Fitur

## 3.1 Daftar Role Pengguna

Sistem memiliki **8 role aktif** (selain `admin` yang tidak digunakan sebagai akun login utama):

| # | Role | Nilai di DB | Deskripsi |
|---|------|-------------|-----------|
| 1 | **Ormawa** | `ormawa` | Organisasi kemahasiswaan (HIMA, UKM, dll) — pengaju proposal & LPJ |
| 2 | **BEM** | `bem` | Badan Eksekutif Mahasiswa — verifikasi tahap 1, publikasi berita |
| 3 | **BPM** | `bpm` | Badan Permusyawaratan Mahasiswa — verifikasi tahap 2, aspirasi, regulasi |
| 4 | **BKKH** | `bkh` | Biro Kemahasiswaan & Hubungan Masyarakat — verifikasi tahap 3, admin sistem |
| 5 | **WR3** | `wr3` | Wakil Rektor 3 — persetujuan akhir proposal |
| 6 | **Bendahara** | `bendahara` | Pencairan dana |
| 7 | **Sarpras Ruangan** | `sarpras` | Verifikasi peminjaman tempat/ruangan |
| 8 | **Sarpras Barang** | `sarpras_barang` | Verifikasi peminjaman barang & manajemen inventaris |

> Terdapat juga role `admin` dalam enum DB namun tidak muncul sebagai akun login terpisah; fungsi administratif dipegang oleh BKKH (`bkh`).

## 3.2 Hak Akses per Halaman

Berdasarkan `$pageMap` di `app/core/Router.php`:

| Halaman (`?page=`) | Role yang Diizinkan |
|--------------------|---------------------|
| `login`, `logout`, `verify_page`, `aspirasi`, `panduan` | **Publik** (tanpa login) |
| `dashboard` | Semua role (view disesuaikan role) |
| `tambah`, `edit`, `upload_lpj`, `revisi_lpj`, `arsip_lpj`, `peminjaman_tempat`, `peminjaman_barang`, `buat_proposal`, `arsip_proposal`, `edit_proposal`, `buat_surat_lain`, `arsip_surat_lain`, `buat_lpj_otomatis`, `arsip_lpj_otomatis` | `ormawa`, `bem`, `bpm` |
| `riwayat` | `ormawa`, `bem`, `bpm`, `bkh`, `bendahara` |
| `arsip_digital`, `view_surat_lain`, `view_lpj_otomatis`, `view_peminjaman`, `view_proposal`, `detail` | `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara` |
| `cetak_surat`, `surat_balasan` | `ormawa`, `bem`, `bpm`, `bkh`, `bendahara` |
| `verifikasi` | `bem`, `bpm`, `bkh`, `wr3` |
| `verifikasi_lpj` | `bkh`, `wr3` |
| `verifikasi_tempat` | `bkh` |
| `verifikasi_barang_bkkh` | `bkh` |
| `ajukan_pencairan` | `bkh` |
| `arsip_surat` | `bkh` |
| `manage_users`, `tambah_user`, `edit_user`, `hapus_user`, `atur_saldo`, `atur_sistem` | `bkh` |
| `manage_saldo` | `bkh`, `wr3` |
| `proses` | `bendahara` |
| `profil` | Semua role login |
| `manage_regulasi`, `manage_aspirasi`, `buat_surat_peringatan` | `bpm` (surat peringatan juga `bkh`) |
| `sarpras_verifikasi_ruangan` | `sarpras` |
| `sarpras_verifikasi_barang`, `manage_barang` | `sarpras_barang` |
| `pusat_informasi`, `jadwal_rapat` | `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara` |

## 3.3 Menu Navigasi per Role (Sidebar)

### Ormawa / BEM / BPM (akun aktif)
- Dashboard
- Buat Pengajuan
- Peminjaman Tempat
- Peminjaman Barang
- **Persuratan Digital** (dropdown):
  - Buat Proposal
  - Buat Surat Lain
  - Buat LPJ
  - Arsip Digital
- Riwayat
- Arsip LPJ
- Pusat Informasi & Berita
- Jadwal Rapat
- *(khusus BPM)*: Buat Surat Peringatan, Kelola Aspirasi, Kelola Regulasi
- Profil

> **Penting:** Menu "Buat Pengajuan", "Peminjaman Tempat", "Peminjaman Barang", dan "Persuratan Digital" hanya muncul jika `status_akun` user = `aktif`.

### Sarpras Ruangan
- Dashboard
- Verifikasi Ruangan
- Profil

### Sarpras Barang
- Dashboard
- Verifikasi Barang
- Master Barang
- Profil

### BKKH (`bkh`)
- Dashboard (termasuk: verifikasi proposal, verifikasi LPJ, pengajuan pencairan, butuh nomor surat)
- Manajemen Saldo
- Manajemen User
- Manajemen Sistem
- Arsip Surat
- Buat Surat Peringatan
- Verifikasi Tempat
- Profil

### WR3 (`wr3`)
- Dashboard (verifikasi proposal tahap WR3)
- Rincian Saldo
- Profil

### Bendahara
- Dashboard
- Proses Pencairan Dana
- Profil

## 3.4 Daftar Fitur per Modul

### A. Pengajuan & Verifikasi Dana

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Buat Pengajuan | Ormawa mengisi nama kegiatan, dana diajukan, tanggal, upload file proposal PDF. Cek saldo & blocklist status aktif. | Ormawa/BEM/BPM |
| Riwayat Pengajuan | Daftar seluruh pengajuan user beserta status. | Ormawa/BEM/BPM/BKKH/Bendahara |
| Detail Pengajuan | Lihat detail, file proposal, histori status, tombol aksi. | Semua (role tertentu) |
| Revisi Pengajuan | Mengedit pengajuan yang berstatus *Ditolak*. Status kembali ke tahap penolak. | Ormawa/BEM/BPM |
| Verifikasi Proposal | Setiap verifikator (BEM→BPM→BKKH→WR3) setujui/tolak dengan catatan. | BEM/BPM/BKKH/WR3 |
| Histori Status | Audit trail setiap perubahan status (`histori_status`). | Terkait |

### B. Pencairan Dana

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Ajukan Pencairan | BKKH meneruskan proposal status "Disetujui WR3, Siap Diajukan ke Bendahara" ke Bendahara. | BKKH |
| Proses Pencairan | Bendahara menyetujui (dengan nominal dana disetujui) atau menolak; status menjadi "Dana Cair" / "Ditolak Bendahara". | Bendahara |
| Notifikasi Dana Cair | SweetAlert notifikasi ke ormawa saat dana cair. | Ormawa |

### C. LPJ (Laporan Pertanggungjawaban)

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Upload LPJ | Ormawa mengupload file LPJ PDF setelah dana cair. | Ormawa/BEM/BPM |
| Revisi LPJ | Upload ulang LPJ yang ditolak. | Ormawa/BEM/BPM |
| Verifikasi LPJ | BKKH/WR3 setujui atau tolak LPJ. | BKKH/WR3 |
| LPJ Otomatis | Pembuatan LPJ otomatis dari data proposal (header + anggaran + lampiran). | Ormawa/BEM/BPM |
| Arsip LPJ | Daftar arsip LPJ. | Ormawa/BEM/BPM |

### D. Persuratan Digital

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Buat Proposal | Generator proposal otomatis (latar belakang, tujuan, sasaran, RAB dinamis, panitia, organisasi, TTD digital). | Ormawa/BEM/BPM |
| Buat Surat Lain | Generator surat (surat undangan, permohonan, dll) dengan TTD kustom. | Ormawa/BEM/BPM |
| Buat LPJ | Generator LPJ otomatis. | Ormawa/BEM/BPM |
| Arsip Digital | Pusat arsip persuratan digital. | Ormawa/BEM/BPM/BKKH/WR3/Bendahara |
| Cetak Surat Balasan | Surat persetujuan resmi dengan kop dinamis (dari `konfigurasi`), nomor surat, rekam jejak, dan QR code verifikasi. | Terkait |
| Nomor Surat | BKKH menginput nomor surat resmi (`arsip_surat`, `simpan_nomor_surat`). | BKKH |
| QR Code Verifikasi | Halaman publik `verify_page` untuk memvalidasi keaslian surat via kode unik. | Publik |

### E. Sarana & Prasarana (Peminjaman)

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Peminjaman Tempat | Ormawa memilih ruangan, tanggal, jam, keperluan. | Ormawa/BEM/BPM |
| Peminjaman Barang | Ormawa memilih barang, jumlah, tanggal, kebutuhan. | Ormawa/BEM/BPM |
| Verifikasi Tempat (BKKH) | BKKH memverifikasi pengajuan peminjaman tempat. | BKKH |
| Verifikasi Barang (BKKH) | BKKH memverifikasi pengajuan peminjaman barang. | BKKH |
| Verifikasi Ruangan (Sarpras) | Sarpras menyetujui/menolak setelah BKKH. | Sarpras |
| Verifikasi Barang (Sarpras) | Sarpras Barang menyetujui/menolak setelah BKKH. | Sarpras Barang |
| Master Barang | CRUD inventaris barang (nama, stok, deskripsi). | Sarpras Barang |

### F. Komunikasi & Informasi

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Pusat Informasi | Berita/pengumuman BEM (dengan lampiran) & regulasi BPM. | Semua |
| Jadwal Rapat | Kalender & daftar jadwal rapat (tambah/hapus oleh BEM/BPM). | Semua |
| Aspirasi Publik | Form publik untuk submit aspirasi; BPM menanggapi. | Publik (submit) / BPM (kelola) |
| Regulasi | BPM mengelola dokumen regulasi (judul, kategori, file). | BPM |
| Surat Peringatan | BPM/BKKH membuat surat peringatan ke ormawa. | BPM, BKKH |

### G. Administrasi & Pengaturan

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Manajemen User | CRUD user + toggle status akun aktif/nonaktif. | BKKH |
| Manajemen Saldo | Set saldo per ormawa. | BKKH (kelola), WR3 (rincian) |
| Atur Sistem | Konfigurasi nama aplikasi, logo, kop surat (disimpan di tabel `konfigurasi`). | BKKH |
| Arsip Surat | Arsip surat balasan + input nomor surat. | BKKH |
| Profil | Edit profil, foto, logo, nama pengurus, TTD digital (ketua/sekretaris/bendahara). | Semua |
| Panduan | Halaman panduan penggunaan. | Publik |

## 3.5 Fitur Umum (Semua Role Login)

- **Dashboard** — ringkasan statistik (kartu saldo untuk BEM/BPM, tugas verifikasi, grafik).
- **Profil** — kelola profil & tanda tangan digital.
- **Mode Tema** — toggle terang/gelap (light/dark mode) di navbar.
- **Toast Notifikasi** — pesan sukses/error setelah aksi.

## 3.6 Dashboard per Role

| Role | Isi Dashboard |
|------|---------------|
| Ormawa | Ringkasan saldo, pengajuan terbaru, notifikasi dana cair |
| BEM | Kartu saldo BEM, tugas verifikasi proposal (status "Diajukan Ke BEM") |
| BPM | Kartu saldo BPM, tugas verifikasi proposal (status "Diajukan Ke BPM"), aspirasi |
| BKKH | Verifikasi proposal (status "Verifikasi BKKH"), verifikasi LPJ, ajukan pencairan, antrean nomor surat |
| WR3 | Verifikasi proposal (status "Verifikasi WR3"), rincian saldo |
| Bendahara | Proses pencairan, verifikasi LPJ |
| Sarpras | Verifikasi ruangan |
| Sarpras Barang | Verifikasi barang, master barang |
