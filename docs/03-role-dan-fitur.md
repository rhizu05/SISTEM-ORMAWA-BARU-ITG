# 03 — Role dan Fitur

## 3.1 Daftar Role Pengguna

Sistem memiliki **8 role aktif** (selain `admin` yang tidak digunakan sebagai akun login utama):

| # | Role | Nilai di DB | Deskripsi |
|---|------|-------------|-----------|
| 1 | **HIMA / UKM** | `ormawa` | Organisasi kemahasiswaan tingkat dasar (5 HIMA + UKM) — pengaju proposal, peminjaman fasilitas, upload LPJ. **Satu role dipakai semua HIMA/UKM**, data diisolasi per `id_user` |
| 2 | **BEM** | `bem` | Badan Eksekutif Mahasiswa — pengaju aktif (kegiatan sendiri) + verifikator pengajuan HIMA/UKM (tahap 1), publikasi berita |
| 3 | **BPM** | `bpm` | Badan Perwakilan Mahasiswa — pengaju aktif (kegiatan sendiri) + pengawas seluruh Ormawa (monitoring proker, keuangan read-only, LPJ), kelola aspirasi & regulasi |
| 4 | **BKKH** | `bkh` | Biro Kemahasiswaan & Hubungan Masyarakat — verifikasi tahap 3, admin sistem |
| 5 | **WR3** | `wr3` | Wakil Rektor 3 — persetujuan akhir proposal |
| 6 | **Bendahara** | `bendahara` | Pencairan dana |
| 7 | **Sarpras Ruangan** | `sarpras` | Verifikasi peminjaman tempat/ruangan |
| 8 | **Sarpras Barang** | `sarpras_barang` | Verifikasi peminjaman barang & manajemen inventaris |

> **Catatan struktur Ormawa (berdasarkan hasil wawancara stakeholder):** BEM, BPM, HIMA, dan UKM semuanya termasuk kelompok **Ormawa**, namun memiliki fungsi yang hierarkis:
> - **HIMA & UKM** — Pengaju murni; tidak memiliki fungsi verifikasi atau monitoring atas Ormawa lain
> - **BEM** — Pengaju aktif sekaligus verifikator; pengajuan dari HIMA/UKM harus melewati approval BEM sebelum naik ke BPM dan BKKH
> - **BPM** — Pengaju aktif sekaligus pengawas; dapat memonitor seluruh program kerja, keuangan, dan LPJ Ormawa secara read-only — BPM tidak berwenang mengubah data keuangan
>
> **Akun HIMA/UKM:** Ke-5 HIMA dan UKM memakai role `ormawa` yang sama — **tidak perlu role baru per organisasi**. Pembedaan dilakukan lewat `id_user` + nama tampilan `nama_lengkap`; query backend selalu difilter `id_user_ormawa = ?` (dari `$_SESSION['user_id']`) untuk mencegah *Broken Access Control*. Lihat `02-struktur-sistem.md` bagian 2.5.6.
>
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

### HIMA / UKM (`ormawa`)

> Pengaju murni — fokus pada pengajuan dan pelaporan kegiatan sendiri.

- Dashboard (ringkasan saldo & status pengajuan terbaru)
- Buat Pengajuan
- Peminjaman Tempat
- Peminjaman Barang
- **Persuratan Digital** (dropdown): Buat Proposal, Buat Surat Lain, Buat LPJ, Arsip Digital
- Riwayat
- Arsip LPJ
- Pusat Informasi & Berita
- Jadwal Rapat
- Profil

> **Penting:** Menu "Buat Pengajuan", "Peminjaman Tempat", "Peminjaman Barang", dan "Persuratan Digital" hanya muncul jika `status_akun` user = `aktif`.

### BEM (`bem`)

> Pengaju aktif + verifikator pengajuan dari HIMA/UKM. Memiliki semua menu HIMA/UKM, ditambah:

- Dashboard (+ panel tugas verifikasi: pengajuan berstatus "Diajukan Ke BEM")
- Buat Pengajuan *(kegiatan BEM sendiri)*
- Peminjaman Tempat & Barang
- Persuratan Digital
- Riwayat
- Arsip LPJ
- **Verifikasi Pengajuan** — approval/penolakan pengajuan dari HIMA/UKM
- Pusat Informasi & Berita *(BEM dapat menambah/menghapus pengumuman)*
- Jadwal Rapat *(BEM dapat menambah/menghapus jadwal)*
- Profil

### BPM (`bpm`)

> Pengaju aktif + pengawas seluruh Ormawa. Memiliki semua menu HIMA/UKM, ditambah menu pengawasan:

- Dashboard (+ panel tugas verifikasi: pengajuan berstatus "Diajukan Ke BPM")
- Buat Pengajuan *(kegiatan BPM sendiri)*
- Peminjaman Tempat & Barang
- Persuratan Digital
- Riwayat
- Arsip LPJ
- **Verifikasi Pengajuan** — approval/penolakan pengajuan setelah lolos BEM
- **Kelola Aspirasi** — menerima dan menanggapi aspirasi yang masuk
- **Kelola Regulasi** — mengelola dokumen regulasi Ormawa
- **Buat Surat Peringatan** — ke Ormawa yang melanggar
- Pusat Informasi & Berita
- Jadwal Rapat *(BPM dapat menambah/menghapus jadwal)*
- Profil

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
| Buat Pengajuan | Ormawa mengisi nama kegiatan, dana diajukan, tanggal, upload file proposal PDF. Cek saldo & blocklist status aktif. | HIMA/UKM, BEM, BPM |
| Riwayat Pengajuan | Daftar seluruh pengajuan user beserta status. | HIMA/UKM, BEM, BPM, BKKH, Bendahara |
| Detail Pengajuan | Lihat detail, file proposal, histori status, tombol aksi. | Semua (role tertentu) |
| Revisi Pengajuan | Mengedit pengajuan yang berstatus *Ditolak*. Status kembali ke tahap penolak. | HIMA/UKM, BEM, BPM |
| Verifikasi Proposal Tahap 1 | BEM menyetujui/menolak pengajuan dari HIMA/UKM dengan catatan. | BEM |
| Verifikasi Proposal Tahap 2 | BPM menyetujui/menolak pengajuan yang sudah lolos dari BEM. | BPM |
| Verifikasi Proposal Tahap 3 | BKKH menyetujui/menolak pengajuan yang sudah lolos dari BPM. | BKKH |
| Persetujuan Akhir | WR3 memberikan persetujuan final. | WR3 |
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
| Upload LPJ | Ormawa mengupload file LPJ PDF setelah dana cair. | HIMA/UKM, BEM, BPM |
| Revisi LPJ | Upload ulang LPJ yang ditolak. | HIMA/UKM, BEM, BPM |
| Verifikasi LPJ | BKKH/WR3 setujui atau tolak LPJ. | BKKH, WR3 |
| Monitoring LPJ Seluruh Ormawa | BPM dapat melihat status dan dokumen LPJ seluruh Ormawa (read-only). | BPM |
| LPJ Otomatis | Pembuatan LPJ otomatis dari data proposal (header + anggaran + lampiran). | HIMA/UKM, BEM, BPM |
| Arsip LPJ | Daftar arsip LPJ. | HIMA/UKM, BEM, BPM |

### D. Persuratan Digital

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Buat Proposal | Generator proposal otomatis (latar belakang, tujuan, sasaran, RAB dinamis, panitia, organisasi, TTD digital). | HIMA/UKM, BEM, BPM |
| Buat Surat Lain | Generator surat (surat undangan, permohonan, dll) dengan TTD kustom. | HIMA/UKM, BEM, BPM |
| Buat LPJ | Generator LPJ otomatis. | HIMA/UKM, BEM, BPM |
| Arsip Digital | Pusat arsip persuratan digital. | HIMA/UKM, BEM, BPM, BKKH, WR3, Bendahara |
| Cetak Surat Balasan | Surat persetujuan resmi dengan kop dinamis (dari `konfigurasi`), nomor surat, rekam jejak, dan QR code verifikasi. | Terkait |
| Nomor Surat | BKKH menginput nomor surat resmi (`arsip_surat`, `simpan_nomor_surat`). | BKKH |
| QR Code Verifikasi | Halaman publik `verify_page` untuk memvalidasi keaslian surat via kode unik. | Publik |

### E. Sarana & Prasarana (Peminjaman)

| Fitur | Deskripsi | Akses |
|-------|-----------|-------|
| Peminjaman Tempat | Ormawa memilih ruangan, tanggal, jam, keperluan. | HIMA/UKM, BEM, BPM |
| Peminjaman Barang | Ormawa memilih barang, jumlah, tanggal, kebutuhan. | HIMA/UKM, BEM, BPM |
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
| Aspirasi | Form untuk submit aspirasi; dikelola dan ditanggapi oleh BPM. | Publik (submit) / BPM (kelola) |
| Regulasi | BPM mengelola dokumen regulasi (judul, kategori, file). | BPM |
| Surat Peringatan | BPM/BKKH membuat surat peringatan ke Ormawa. | BPM, BKKH |

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
| HIMA / UKM | Ringkasan saldo, pengajuan terbaru milik sendiri, notifikasi dana cair |
| BEM | Kartu saldo BEM, pengajuan BEM sendiri, **panel tugas verifikasi** (pengajuan HIMA/UKM berstatus "Diajukan Ke BEM") |
| BPM | Kartu saldo BPM, pengajuan BPM sendiri, **panel tugas verifikasi** (berstatus "Diajukan Ke BPM"), **ringkasan aspirasi** masuk |
| BKKH | Verifikasi proposal (status "Verifikasi BKKH"), verifikasi LPJ, ajukan pencairan, antrean nomor surat |
| WR3 | Verifikasi proposal (status "Verifikasi WR3"), rincian saldo |
| Bendahara | Proses pencairan, verifikasi LPJ |
| Sarpras | Verifikasi ruangan |
| Sarpras Barang | Verifikasi barang, master barang |

## 3.7 Mahasiswa Umum *(Pending — Status Aktor Belum Ditetapkan)*

> Berdasarkan hasil kuesioner (11 responden lintas prodi), mahasiswa umum memiliki kebutuhan yang belum terakomodasi oleh sistem saat ini. Namun, **status mahasiswa sebagai aktor aktif dalam sistem masih pending** — belum ditetapkan apakah akan diakomodasi dalam pengembangan saat ini atau diposisikan sebagai opsi pengembangan lanjutan.

Kandidat fitur untuk mahasiswa umum apabila ditetapkan sebagai aktor:

| No. | Kandidat Fitur | Keterangan |
|---|---|---|
| 1 | Pusat Informasi Terpadu | Kalender kegiatan, prosedur layanan, ketersediaan fasilitas |
| 2 | Monitoring Status Pengajuan | Tracking progres pengajuan secara transparan |
| 3 | Penyampaian Aspirasi | Wadah terpusat untuk kritik, saran, dan keluhan |
| 4 | Peminjaman Fasilitas | Reservasi ruangan/fasilitas kampus secara online |
| 5 | Sistem Notifikasi | Pemberitahuan status layanan kemahasiswaan secara berkala |

**Data pendukung dari kuesioner:**
- ~82% responden belum pernah menggunakan SKIN
- 73% (8 responden) menyatakan fitur tracking status pengajuan bersifat "Sangat Penting"
- 73% responden (5 Bersedia + 3 Sangat Bersedia) bersedia menggunakan SKIN aktif jika dikembangkan
- Responden belum mencakup Teknik Industri (perwakilan Teknik Sipil sudah masuk)
