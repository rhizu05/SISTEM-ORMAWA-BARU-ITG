# Test Case Manual: SKIN-ITG (Sistem Keuangan Ormawa v2)

Dokumen ini berisi panduan pengujian manual *End-to-End* (E2E) dan *Negative Testing* untuk memverifikasi fungsionalitas sistem setelah migrasi ke Laravel 11.

---

## 📌 Akun Pengujian (Testing Accounts)

Gunakan kredensial berikut untuk melakukan skenario pengujian. (Password untuk seluruh akun adalah: `password`).

| Peran (Role) | Nama | Username / Email Login |
| :--- | :--- | :--- |
| **Ormawa** | HIMA Informatika | `himaif@test.com` |
| **BEM** | BEM ITG | `bem@test.com` |
| **BPM** | BPM ITG | `bpm@test.com` |
| **BKKH / Admin**| BKKH | `bkh@test.com` |vV
| **WR3** | Wakil Rektor 3 | `wr3@test.com` |
| **Bendahara** | Bendahara ITG | `bendahara@test.com` |
| **Sarpras** | Sarpras Ruangan | `sarpras@test.com` |
| **Sarpras (Brg)**| Sarpras Inventaris | `sarprasbarang@test.com` |

---

## 🟢 Skenario 1: Profil & Konfigurasi Sistem (Happy Path)

| ID | Langkah Pengujian | Hasil yang Diharapkan | Status (Pass/Fail) |
|:---|:---|:---|:---|
| **TC-1.1** | Login sebagai `bkh@test.com`. Akses menu **Pengaturan**. Ubah Nama Aplikasi menjadi "SKIN-ITG" dan klik Simpan. | Muncul notifikasi "Konfigurasi sistem berhasil diperbarui". Nama sistem di header atas berubah. | [✔] |
| **TC-1.2** | Akses menu **Pengguna**. Cari baris "HIMA Informatika". Klik tombol **Atur Saldo**. Masukkan `10000000` (10 juta), lalu Simpan. | Saldo HIMA Informatika di tabel berubah menjadi Rp 10.000.000. | [✔] |
| **TC-1.3** | *Logout*. Login sebagai `himaif@test.com`. Buka menu dropdown pojok kanan atas, pilih **Profile**. | Halaman Edit Profile terbuka. Pada tab "Data Tambahan Profil", Ormawa dapat mengunggah file Logo dan Tanda Tangan (PNG). | [X] |
| **TC-1.4** | Cek dashboard utama Ormawa. | "Sisa Saldo Dana" harus menunjukkan angka Rp 10.000.000 sesuai yang di-set BKKH tadi. | [✔] |

---

## 🟢 Skenario 2: Alur Keuangan - Pengajuan Dana & LPJ (End-to-End)

| ID | Langkah Pengujian | Hasil yang Diharapkan | Status (Pass/Fail) |
|:---|:---|:---|:---|
| **TC-2.1** | Login sebagai `himaif@test.com`. Masuk menu **Pengajuan** > **+ Buat Pengajuan Baru**. Isi form (Nama: "Seminar IT", Dana: 1000000, Upload sembarang file PDF). Klik **Simpan sebagai Draft**. | Pengajuan berhasil dibuat. Status menjadi `Draft`. | [✔] |
| **TC-2.2** | Dari halaman Detail/Daftar Pengajuan, klik tombol **Ajukan**. Tekan OK pada dialog konfirmasi. | Status berubah menjadi `Diajukan ke BEM`. (Bisa dilihat di halaman Detail Pengajuan). | [✔] |
| **TC-2.3** | *Logout*. Login sebagai `bem@test.com`. Masuk menu **Verifikasi**. Temukan pengajuan "Seminar IT", klik **Verifikasi**. | Detail pengajuan (beserta preview PDF) terbuka. | [X] |
| **TC-2.4** | Pada form verifikasi BEM, kosongkan catatan (opsional), lalu klik **Setuju**. | Status berubah menjadi `Verifikasi BPM`. Pengajuan hilang dari antrian BEM. | [✔] |
| **TC-2.5** | *Logout*. Login sebagai `bpm@test.com`. Buka menu **Verifikasi**. Cari pengajuan, klik **Verifikasi**. Klik **Setuju**. | Status berubah menjadi `Verifikasi BKKH`. | [X] |
| **TC-2.6** | *Logout*. Login sebagai `bkh@test.com`. Buka menu **Verifikasi**. Klik **Verifikasi**. Di form persetujuan ini, isi kolom **Nomor Surat Resmi** (Misal: `001/ITG/2026`). Klik **Setuju**. | Status berubah menjadi `Verifikasi WR3`. | [X] |
| **TC-2.7** | *Logout*. Login sebagai `wr3@test.com`. Buka menu **Verifikasi**. Klik **Verifikasi**. Klik **Setuju**. | Status berubah menjadi `Disetujui WR3`. | [X] |
| **TC-2.8** | *Logout*. Login lagi sebagai `bkh@test.com`. Buka **Verifikasi**. Cari pengajuan yang sama, klik **Verifikasi**. Klik tombol **Ajukan Pencairan**. | Status berubah menjadi `Diajukan ke Bendahara`. | [✔] |
| **TC-2.9** | *Logout*. Login sebagai `bendahara@test.com`. Buka **Verifikasi**. Klik **Verifikasi**. Isi "Nominal Dicairkan" (misal: 1000000), isi Tanggal, klik **Konfirmasi Pencairan Dana**. | Status berubah menjadi `Dana Cair`. Proses pencairan selesai. | [✔] |
| **TC-2.10**| *Logout*. Login sebagai `himaif@test.com`. Buka menu **LPJ**. Cari pengajuan "Seminar IT", klik **Upload LPJ**. Upload sembarang file PDF. | File sukses terunggah. Status berubah menjadi `LPJ Diajukan`. | [✔] |
| **TC-2.11**| *Logout*. Login sebagai `bkh@test.com`. Buka **Verifikasi**. Verifikasi LPJ tersebut dengan klik **Setujui LPJ**. | Status berubah menjadi `Selesai`. Alur End-to-End komplit! | [✔] |

---

## 🟢 Skenario 3: Peminjaman Ruangan & Barang

| ID | Langkah Pengujian | Hasil yang Diharapkan | Status (Pass/Fail) |
|:---|:---|:---|:---|
| **TC-3.1** | Login sebagai `sarprasbarang@test.com`. Buka menu **Manajemen Inventaris Barang** (`/sarpras/barang`). Tambahkan Barang baru (Misal: "Proyektor Utama", Stok: 2). | Barang berhasil disimpan dan muncul di tabel. | [X] |
| **TC-3.2** | Login sebagai `himaif@test.com`. Buka menu **Peminjaman** > **Ajukan Peminjaman Ruangan**. Pilih Aula, isi tanggal untuk besok, lalu Simpan. | Peminjaman berhasil. Status: `Proses BKKH`. | [✔] |
| **TC-3.3** | Buka menu **Peminjaman** > **Ajukan Peminjaman Barang**. Pilih Proyektor Utama (Qty: 1), isi form, lalu Simpan. | Pengajuan barang berhasil. Status: `Proses BKKH`. | [✔] |
| **TC-3.4** | Login sebagai `bkh@test.com`. Buka menu **Verif Peminjaman**. Klik **Setuju** untuk kedua antrian (Tempat & Barang). | Status kedua peminjaman berubah menjadi `Proses Sarpras`. | [✔] |
| **TC-3.5** | Login sebagai `sarpras@test.com`. Buka **Verif Peminjaman**. Setujui peminjaman Aula. | Status peminjaman ruangan menjadi `Disetujui/Selesai`. | [X] |
| **TC-3.6** | Login sebagai `sarprasbarang@test.com`. Buka **Verif Peminjaman**. Setujui peminjaman Proyektor. Cek stok "Proyektor Utama" di menu Manajemen Barang. | Peminjaman barang Selesai. **Stok Proyektor Utama berkurang menjadi 1**. | [X] |

---

## 🔴 Skenario 4: Negative Testing & Otorisasi Keamanan

| ID | Langkah Pengujian | Hasil yang Diharapkan | Status (Pass/Fail) |
|:---|:---|:---|:---|
| **TC-4.1** | Login sebagai Ormawa (`himaif@test.com`). Ubah URL di address bar browser secara paksa menjadi `http://localhost:8000/verifikasi`. | Tampil halaman error **403 USER DOES NOT HAVE THE RIGHT ROLES.** (Otorisasi bekerja). | [✔] |
| **TC-4.2** | Login sebagai BEM (`bem@test.com`). Akses paksa URL `http://localhost:8000/admin/users`. | Tampil halaman error **403 Forbidden**. | [✔] |
| **TC-4.3** | Login sebagai Ormawa. Buka menu **Pengajuan** > **Buat Pengajuan Baru**. Isi form text, tapi **KOSONGKAN field File Proposal**. Klik Simpan. | Pengajuan **gagal disimpan**. Browser meminta file wajib diisi. | [✔] |
| **TC-4.4** | Pada form yang sama (Buat Pengajuan), unggah file berupa Gambar (`.jpg` / `.png`). | Pengajuan **gagal disimpan**. Muncul tulisan error validasi merah: *"The file proposal field must be a file of type: pdf."* | [✔] |
| **TC-4.5** | Pada alur Verifikasi Proposal di tingkat **BKKH** (status *Verifikasi BKKH* / `bpm_approved`), biarkan field **"Nomor Surat Resmi"** kosong, lalu klik **Setuju** (Meneruskan ke WR3). | BKKH gagal menyetujui. Muncul Flash Message Error: *"Nomor surat wajib diisi sebelum meneruskan ke WR3."* | [✔] |
| **TC-4.6** | Pada proses Verifikasi Proposal (role bebas, misal BEM), klik tombol **Tolak**, tapi biarkan field **Catatan** kosong. (Jika sistem mewajibkan). | *(Opsional)* Jika ditolak, sistem memunculkan pop-up atau error agar catatan diisi. Jika tidak diatur *required*, proposal tertolak dengan sukses. | [X] |
| **TC-4.7** | Login sebagai Ormawa. Buka form **Peminjaman Ruangan**. Pilih ruangan yang persis sama, dan tanggal/waktu yang **SAMA PERSIS** dengan peminjaman yang sudah disetujui sebelumnya (TC-3.2). | Pengajuan gagal. Muncul pesan Error: *"Ruangan sudah dibooking pada tanggal/waktu tersebut."* (Konflik dicegah). | [✔] |

---

## 📝 Catatan Tambahan Penguji

*(Gunakan area ini untuk mencatat bug, perbaikan UI/UX yang diperlukan, atau typo yang ditemukan saat melakukan pengujian manual).*

1.  TC-1.3 - Logo tidak termuat dan saat coba open image in new tab muncul 404 not found 
2.  TC-2.3 - Dashboard BEM tidak sinkron dengan tab verifikasi. Preview pdf hanya muncul 404 not found
3.  TC-2.5 - Dashboard BPM tidak sinkron dengan tab verifikasi. Preview pdf hanya muncul 404 not found. Pengajuan tidak menampilkan status Verifikasi BKKH, pengajuan tersebut langsung hilang
4.  TC-2.6 - Dashboard BKKH tidak sinkron dengan tab verifikasi. Preview pdf hanya muncul 404 not found. Setelah diverifikasi, pengajuan tersebut langsung hilang dan tidak menampilkan status Verifikasi WR3
5.  TC-2.7 - Dashboard WR3 tidak sinkron dengan tab verifikasi. Preview pdf hanya muncul 404 not found. Setelah diverifikasi, pengajuan tersebut langsung hilang dan tidak menampilkan status Disetujui WR3
6.  TC-2.9 - Dashboard bendahara tidak sinkron dengan tab verifikasi
7.  TC-3.1 - Barang yang ditambahkan tidak masuk ke daftar barang inventaris.
8.  TC-3.5 - Pada akun sarpras ruangan muncul inventaris barang juga. Jadi bukannya hanya ruangan saja tapi inventaris barang yang ingin dipinjam juga tampil dan dapat disetujui. Pada tab Verifikasi Peminjaman Tempat dan Barang terdapat Antrian Peminjaman Ruangan dan Antrian Peminjaman Barang
9.  TC-3.6 - Pada akun sarpras barang tidak muncul investaris barang yang dipinjam. Pada tab Verifikasi Peminjaman Tempat dan Barang terdapat Antrian Peminjaman Ruangan dan Antrian Peminjaman Barang
10. TC-4.6 - Pengajuan berhasil ditolak tanpa mengisi catatan
---
**Diuji Oleh:** Rasyid  
**Tanggal:** 30 Agustus 2026
