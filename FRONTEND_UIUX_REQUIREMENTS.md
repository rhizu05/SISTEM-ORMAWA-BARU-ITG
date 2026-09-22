# SPESIFIKASI KEBUTUHAN FRONTEND & UI/UX (FRONTEND & UI/UX REQUIREMENTS)
## SISTEM KEMAHASISWAAN ITG (SKIN ITG — VERSION 3.0)

Dokumen ini merupakan acuan resmi (*single source of truth*) bagi Tim Frontend Developer dan UI/UX Designer dalam merancang, membangun, dan menyempurnakan seluruh tampilan antarmuka Sistem Informasi Kemahasiswaan Institut Teknologi Garut (SKIN ITG).

---

## 1. PRINSIP DESAIN & ARSITEKTUR PERAN (DESIGN PRINCIPLES & ROLES)

### 1.1 Prinsip Utama
1. **Model Mahasiswa Guest (BR-01):** Mahasiswa umum **TIDAK memiliki akun login**. Mahasiswa mengakses layanan melalui portal publik ber-tiket (`SKIN-TKT-YYYY-XXXX`) dan melacak perkembangannya menggunakan kombinasi **Kode Tiket + Alamat Email**.
2. **Otentikasi Khusus Pengurus & Pejabat:** Halaman login internal hanya diperuntukkan bagi pengurus organisasi mahasiswa (HIMA & UKM) serta pejabat/pengelola kampus (BEM, BPM, BKHM, Sarpras, WR3, Bendahara, Admin). Tidak ada tombol registrasi mandiri publik.
3. **Responsif & Mobile-First:** Minimal 70% akses mahasiswa berasal dari perangkat seluler (*smartphone*). Seluruh formulir, katalog informasi, dan halaman detail harus nyaman digunakan pada layar kecil (360px – 420px).
4. **Keamanan & Privasi Tingkat Tinggi (SEC-01 s.d SEC-05):** Informasi konseling personal rahasia, data keuangan, dan berkas privat dilindungi dengan visualisasi status yang jelas dan konfirmasi aksi modal.

### 1.2 Matriks Hak Akses & Peran (Role-Based Access Matrix)

| Kode Peran | Nama Peran / Pengguna | Status Login | Akses Utama di Frontend |
| :--- | :--- | :--- | :--- |
| **GUEST** | Mahasiswa Umum & Publik | **Tanpa Login** | Portal Layanan Publik, Formulir Tiket (Aspirasi, Konseling Rahasia, Prestasi), Lacak Status Tiket, Konfirmasi Hadir Konseling, Showcase Prestasi, Pusat Informasi & Detail Berita, Unduh Regulasi BPM. |
| **ORMAWA** | Himpunan (HIMA) & Unit Kegiatan (UKM) | Wajib Login | Dashboard Ormawa, Pengajuan Anggaran, Generator Proposal & LPJ Otomatis, Peminjaman Tempat & Barang, Draf Pengumuman Acara (Antrean BEM), Pelaporan Prestasi Mahasiswa, Aspirasi Ormawa. |
| **BEM** | Badan Eksekutif Mahasiswa | Wajib Login | Dashboard BEM, **Pengajuan Anggaran Kegiatan BEM (Auto-bypass langsung ke BPM)**, Verifikasi Proposal Ormawa Tahap 1, **Panel Kurasi Berita HIMA/UKM**, Penerbitan Berita/Agenda BEM, Program Kerja, Peminjaman Fasilitas, Pelaporan Prestasi, Kirim Nudge ke BPM/BKHM/WR3/Bendahara. |
| **BPM** | Badan Perwakilan Mahasiswa | Wajib Login | Dashboard BPM, **Pengajuan Anggaran Kegiatan BPM (Auto-bypass langsung ke BKHM)**, Verifikasi Proposal BEM/Ormawa Tahap 2, **Panel Himpun Aspirasi Mahasiswa & Tombol Teruskan ke BKHM**, Penerbitan Regulasi/UU/Pedoman Kampus, Monitoring Proker, Kirim Nudge ke BKHM/WR3/Bendahara. |
| **BKHM** | Biro Kemahasiswaan | Wajib Login | Dashboard BKHM, Verifikasi Proposal Anggaran Tahap 3, **Manajemen Saldo Ormawa & Ekspor Keuangan (Excel & PDF)**, **Verifikasi Tiket Konseling Rahasia & Jadwal Temu**, **Verifikasi Tiket Prestasi & Delegasi**, Verifikasi Tempat Tahap 1 & Peminjaman Barang, Pengumuman Resmi Kampus. |
| **SARPRAS**| Bagian Sarana & Prasarana | Wajib Login | Dashboard Sarpras, **Kalender Interaktif Slot Ruangan (Jadwal Kuliah vs Ormawa)**, Verifikasi Tempat Tahap 2, Verifikasi Peminjaman Barang, Kelola Master Ruangan & Barang, Jadwal Perkuliahan Mingguan. |
| **WR3** | Wakil Rektor III | Wajib Login | Dashboard Pimpinan WR3, Verifikasi Proposal Pimpinan Tahap 4, Verifikasi Evaluasi LPJ, Monitoring Laporan Keuangan, Verifikasi & Monitoring Prestasi Mahasiswa. |
| **BENDAHARA**| Bagian Keuangan Kampus | Wajib Login | Dashboard Bendahara, Menu Khusus Kelola Bendahara, Antrean Pencairan Dana Proposal Tahap 5, Input Bukti Transfer & Konfirmasi Dana Cair, Ekspor Rekapitulasi Pencairan (Excel, PDF, CSV). |
| **ADMIN** | Administrator Sistem | Wajib Login | Dashboard Admin, Manajemen Akun Pengurus Terpusat (CRUD Users & Roles), Pengaturan Konfigurasi Sistem, Monitoring Read-Only Seluruh Modul. |

---

## 2. PANDUAN SISTEM DESAIN (DESIGN SYSTEM GUIDELINES)

### 2.1 Palet Warna Resmi ITG (Color Palette)
* **Primary Brand (ITG Navy & Indigo):**
  * `Primary 900`: `#1e1b4b` (Header, Sidebar background)
  * `Primary 800`: `#312e81` (Hover state, Dark cards)
  * `Primary 600`: `#4f46e5` (Primary buttons, Active tabs, Hero links)
  * `Primary 50`: `#eef2ff` (Badge background, Light focus rings)
* **Secondary / Accent (Campus Gold & Warm Amber):**
  * `Amber 500`: `#f59e0b` (Peringatan, Status Pending Review, Showcase Awards)
  * `Amber 50`: `#fffbeb` (Notice banner kurasi)
* **Success (Emerald Green):**
  * `Emerald 600`: `#059669` (Status Disetujui, Dana Cair, Hadir, Excel Export)
  * `Emerald 50`: `#ecfdf5` (Success toast/alert)
* **Danger / Urgent (Crimson Rose):**
  * `Rose 600`: `#e11d48` (Ditolak, Batal, PDF Export, Hapus data)
  * `Rose 50`: `#fff1f2` (Error alert)
* **Neutrals (Slate):**
  * `Slate 900`: `#0f172a` (Body text utama, Judul tebal)
  * `Slate 600`: `#475569` (Body text sekunder, deskripsi)
  * `Slate 400`: `#94a3b8` (Border halus, ikon nonaktif, placeholder)
  * `Slate 100`: `#f1f5f9` (Background tabel, container sekunder)
  * `Slate 50`: `#f8fafc` (Background halaman utama)

### 2.2 Tipografi (Typography)
* **Font Family:** `Figtree`, `Inter`, atau `system-ui, -apple-system, sans-serif`.
* **Ukuran & Hierarki:**
  * `Hero Title (H1 Display)`: 36px – 48px, Font-weight: 800 (Extra Bold), line-height: 1.15
  * `Page Title (H2)`: 24px – 30px, Font-weight: 700 (Bold), line-height: 1.25
  * `Card Title (H3)`: 18px – 20px, Font-weight: 700 (Bold)
  * `Body Text`: 14px – 16px, Font-weight: 400 (Regular), line-height: 1.6
  * `Caption / Meta Text`: 12px – 13px, Font-weight: 500 (Medium), text-slate-500
  * `Badge / Code`: 11px – 12px, Font-weight: 700 (Bold), tracking-wider, font-mono untuk Kode Tiket

### 2.3 Standar Aset & Logo Resmi ITG (Brand Assets)
* **Lokasi Berkas Resmi:**
  * Path Aset: `public/images/logo_itg.png` (dan alias `logo-itg.png`)
  * Path Konfigurasi Kop: `storage/app/public/konfigurasi/logo_itg.png`
* **Implementasi Desain:**
  * **Sidebar Pengurus (Background Gelap):** Wajib menggunakan kontainer lingkaran putih bersih (`w-10 h-10 rounded-full bg-white p-1 shadow-sm shrink-0`) agar logo roda gigi biru dan rantai ITG tampil kontras dan tajam.
  * **Halaman Login & Autentikasi (`/login`):** Menggunakan kartu rounded persegi (`w-24 h-24 rounded-2xl bg-white p-2 shadow-sm border border-gray-200`) dengan teks subjudul *"Institut Teknologi Garut"*.
  * **Kop Surat Dokumen Resmi (PDF & Print):** Logo diletakkan di sisi kiri kop surat dengan ukuran proporsional `width: 70px s.d 80px, height: 70px s.d 80px, object-fit: contain`.
  * **Navbar Publik:** Terpasang pada kartu rounded `w-10 h-10 bg-white p-1 border border-slate-200` bersanding dengan teks *"SKIN ITG"*.

---

## 3. SPESIFIKASI HALAMAN PUBLIK & MAHASISWA GUEST (TANPA LOGIN)

### 3.1 P-01: Landing Page Utama (`/`)
* **Pengguna:** Tamu Publik, Mahasiswa Umum, Pengurus Ormawa, Dosen/Pejabat.
* **Tujuan:** Gerbang selamat datang resmi kemahasiswaan ITG, memperkenalkan modul layanan, dan memisahkan alur Mahasiswa (Tanpa Login) vs Pengurus (Login).
* **Komponen & Layout:**
  1. **Navbar Publik Sticky:**
     * Logo ITG + Teks "SKIN ITG - Sistem Informasi Kemahasiswaan".
     * Menu Navigasi: *Portal Layanan*, *Lacak Tiket*, *Showcase Prestasi*, *Pengumuman & Regulasi*.
     * CTA Button: Tombol primer **"Login Pengurus"** (`/login`) jika belum login, atau **"Buka Dashboard"** jika sudah terotentikasi.
  2. **Hero Banner Interaktif:**
     * Pill Badge: *"Portal Resmi Kemahasiswaan Institut Teknologi Garut"*.
     * Judul Besar: *"Satu Portal Terpadu Aktivitas Kemahasiswaan"*.
     * Deskripsi Singkat: Layanan aspirasi, konseling rahasia, prestasi, pengajuan dana ormawa, dan peminjaman fasilitas.
     * 3 Tombol Aksi Cepat:
       - Tombol Biru: **"Portal Layanan Mahasiswa (Tanpa Login)"** &rarr; ke `/layanan`.
       - Tombol Putih/Border: **"Lacak Status Tiket"** &rarr; ke `/layanan/cek-status`.
       - Tombol Emas: **"Showcase Prestasi Mahasiswa"** &rarr; ke `/prestasi/showcase`.
  3. **Grid 3 Kartu Pilar Layanan:**
     * *Kartu 1:* Portal Layanan Mahasiswa & Konseling Rahasia (Tautan langsung ke form).
     * *Kartu 2:* Pusat Informasi, Berita BEM, dan Dokumen Regulasi BPM.
     * *Kartu 3:* Tata Kelola Administrasi & Saldo Pengajuan Ormawa (Tautan ke login pengurus).
  4. **Footer Kampus Standar:** Alamat Jl. Mayor Syamsu No. 1 Garut, kontak BKHM, email kemahasiswaan@itg.ac.id, hak cipta tahun berjalan.

---

### 3.2 P-02: Portal Katalog Layanan Publik (`/layanan`)
* **Pengguna:** Mahasiswa Umum (Guest).
* **Tujuan:** Memilih kategori layanan kemahasiswaan dan melacak tiket yang dimiliki.
* **Komponen & Layout:**
  1. **Header Section:** Hero mini bernuansa biru gelap dengan kotak input instan: Masukkan Kode Tiket & Email &rarr; tombol "Cari Tiket".
  2. **Katalog 3 Kartu Layanan (Interactive Grid):**
     * **Kartu Layanan 1 (Aspirasi):** Ikon pengeras suara, judul *"Kanal Aspirasi Mahasiswa"*, deskripsi penanganan oleh BPM, lencana *"Dapat Anonim"*, tombol *"Buat Tiket Aspirasi &rarr;"*.
     * **Kartu Layanan 2 (Konseling Personal):** Ikon gembok/hati, judul *"Konseling Personal BKHM"*, deskripsi *"Kerahasiaan Terjamin & Data Terenkripsi"*, lencana *"Tatap Muka / Daring"*, tombol *"Jadwalkan Konseling &rarr;"*.
     * **Kartu Layanan 3 (Prestasi & Delegasi):** Ikon piala/bintang, judul *"Lapor Prestasi & Bantuan Lomba"*, deskripsi pelaporan juara kompetisi dan pengajuan dana delegasi lomba, tombol *"Ajukan Sekarang &rarr;"*.
  3. **FAQ Accordion:** Pertanyaan umum seputar berapa lama tiket diproses, bagaimana jika lupa kode tiket, dan keamanan data.

---

### 3.3 P-03: Formulir Pengajuan Tiket Aspirasi (`/layanan/aspirasi`)
* **Pengguna:** Mahasiswa Umum (Guest).
* **Tujuan:** Mengirim keluhan, kritik, atau saran konstruktif ke BPM ITG.
* **Data & Field yang Harus Tampil:**
  * **Identitas Pengirim:**
    * Nama Lengkap (Wajib, teks)
    * NIM (Wajib, teks angka 7 digit)
    * Program Studi (Dropdown pilihan: Teknik Informatika, Teknik Sipil, Teknik Industri, Sistem Informasi, Arsitektur)
    * Nomor WhatsApp Aktif (Wajib, format ponsel Indonesia)
    * Alamat Email Aktif (Wajib, tipe email — untuk menerima Kode Tiket & notifikasi update)
  * **Pilihan Kerahasiaan (Privacy Toggle):**
    * Checkbox: *"Kirim sebagai Anonim"*.
    * Keterangan UX: *"Nama dan identitas Anda hanya diketahui oleh tim verifikator BPM dan tidak akan ditampilkan secara publik."*
  * **Isi Aspirasi:**
    * Kategori Aspirasi (Dropdown: Fasilitas Kampus, Pelayanan Akademik, Kebijakan Kampus, Lainnya)
    * Judul Aspirasi (Wajib, teks singkat)
    * Uraian Aspirasi (Wajib, textarea minimal 3 baris)
    * Upload Berkas Bukti/Foto (Opsional, format JPG, PNG, PDF maks 5MB)
* **Aksi & Respon Frontend:**
  * Tombol Submit: *"Kirim Aspirasi & Dapatkan Kode Tiket"*.
  * Feedback Sukses: Menampilkan modal sukses dengan **Kode Tiket Besar** (contoh: `SKIN-TKT-2026-0042`), tombol "Salin Kode", dan instruksi bahwa salinan telah dikirimkan ke email mahasiswa.

---

### 3.4 P-04: Formulir Konseling Personal Rahasia BKHM (`/layanan/konseling`)
* **Pengguna:** Mahasiswa Umum (Guest).
* **Tujuan:** Menjadwalkan konseling psikologis/akademik secara privat dengan konselor BKHM ITG.
* **Komponen Khusus UX & Keamanan:**
  * **Banner Garansi Kerahasiaan (SEC-05):** Banner hijau/teal dengan ikon gembok: *"Data permasalahan Anda dilindungi enkripsi kriptografis tingkat tinggi (AES-256). Hanya staf konselor resmi BKHM yang memiliki akses."*
* **Data & Field yang Harus Tampil:**
  * Nama Lengkap, NIM, Program Studi, No. WhatsApp, Email Aktif (Wajib).
  * Topik Konseling (Dropdown: Masalah Akademik/Nilai, Manajemen Stres & Tekanan Kuliah, Masalah Adaptasi/Sosial, Masalah Karir, Permasalahan Personal Lainnya).
  * Metode Konseling yang Diminati (Radio Button: *Tatap Muka di Ruangan BKHM Lt. 2 Rektorat* ATAU *Daring / Online Virtual Meeting*).
  * Preferensi Jadwal (Input Hari/Rentang Jam yang diharapkan).
  * Uraian Kendala / Cerita Masalah (Textarea, placeholder ramah dan suportif).
  * Unggah Berkas Pendukung (Opsional, PDF/Foto maks 5MB).
* **Aksi:** Tombol Submit *"Ajukan Permohonan Konseling"*.

---

### 3.5 P-05: Formulir Pelaporan Prestasi & Bantuan Delegasi (`/layanan/prestasi`)
* **Pengguna:** Mahasiswa Umum (Guest).
* **Tujuan:** Melaporkan prestasi lomba mandiri (agar tayang di Showcase) ATAU mengajukan bantuan dana delegasi kompetisi.
* **Struktur Komponen (Tabs Mode):**
  * **Tab 1: Lapor Capaian Prestasi Mandiri (Selesai Lomba)**
    * Field: Nama Mahasiswa, NIM, Prodi, WA, Email.
    * Nama Kegiatan / Kejuaraan (Teks).
    * Penyelenggara Kompetisi (Teks).
    * Tingkat Kompetisi (Dropdown: Kota/Kabupaten, Provinsi, Wilayah, Nasional, Internasional).
    * Capaian Juara (Dropdown: Juara 1, Juara 2, Juara 3, Harapan, Finalis / Best Paper).
    * Tanggal Perolehan Prestasi (Date picker).
    * Unggah Bukti Sertifikat / Piagam / Foto Penyerahan (Wajib, PDF/JPG maks 5MB).
  * **Tab 2: Permohonan Bantuan Dana Delegasi (Akan Mengikuti Lomba)**
    * Field: Identitas Pengaju (Mahasiswa/Tim).
    * Nama Kompetisi yang Diikuti & Tautan Lomba Resmi.
    * Tanggal & Lokasi Pelaksanaan Kompetisi.
    * Estimasi Biaya yang Dimohonkan (Input Currency Rupiah).
    * Unggah Proposal Delegasi / Bukti Lolos Tahap Final (Wajib PDF).
    * Nomor Rekening & Nama Bank Penerima.

---

### 3.6 P-06: Pelacakan Status Tiket & Konfirmasi Kehadiran (`/layanan/cek-status` / `/layanan/tracking`)
* **Pengguna:** Mahasiswa Umum (Guest).
* **Tujuan:** Memeriksa histori kemajuan tiket dan merespons jadwal temu dari BKHM.
* **Form Pengecekan:** 2 Input (Kode Tiket + Alamat Email) &rarr; Tombol "Lacak Status".
* **Data yang Tampil Setelah Tiket Ditemukan:**
  1. **Header Tiket:**
     * Kode Tiket (Font mono tebal, contoh: `SKIN-TKT-2026-0003`).
     * Lencana Kategori (Aspirasi / Konseling / Prestasi).
     * Lencana Status Saat Ini (Warna dinamis sesuai status).
     * Tanggal Dibuat & Pembaruan Terakhir.
  2. **Timeline Progres (Stepper Horizontal / Vertical):**
     * Tahap 1: Tiket Diterima Sistem.
     * Tahap 2: Sedang Ditinjau / Terverifikasi / Diteruskan ke BKHM.
     * Tahap 3: Jadwal Ditetapkan / Diproses.
     * Tahap 4: Selesai / Ditindaklanjuti.
  3. **Kotak Tanggapan Resmi Petugas:**
     * Nama Petugas / Unit (BPM atau BKHM).
     * Tanggal & Jam Tanggapan.
     * Catatan / Rekomendasi Resmi.
  4. **KOMPONEN KHUSUS: Respon Konfirmasi Jadwal Konseling (Jika status = `jadwal_ditentukan`):**
     * Tampilan Kotak Jadwal: Tanggal Temu, Jam Sesi, Lokasi Temu / Link Virtual.
     * Status Konfirmasi: Menampilkan apakah mahasiswa sudah konfirmasi (*Belum Merespon* / *Bersedia Hadir* / *Minta Reschedule* / *Batal*).
     * **Form Aksi Respon Interaktif (Jika Belum Menjawab):**
       - Pilihan Opsi:
         1. Tombol Hijau: *"✓ Ya, Saya Bersedia Hadir Tepat Waktu"*
         2. Tombol Kuning: *"Minta Penjadwalan Ulang (Reschedule)"*
         3. Tombol Abu-abu: *"Batalkan Sesi Konseling"*
       - Kolom Catatan Tambahan (Opsional).
       - Tombol Submit Konfirmasi (Mengirim feedback ke konselor BKHM seketika).

---

### 3.7 P-07: Galeri Publik Showcase Prestasi Mahasiswa (`/prestasi/showcase`)
* **Pengguna:** Publik, Mahasiswa, Calon Mahasiswa, Civitas Academica ITG.
* **Tujuan:** Menampilkan etalase prestasi resmi mahasiswa yang telah diverifikasi oleh BKHM.
* **Komponen & Layout:**
  * **Header:** Hero bergaya piala dengan statistik ringkas: Total Prestasi Nasional, Total Prestasi Internasional.
  * **Filter Bar:** Filter berdasarkan Tahun Capaian, Tingkat Kejuaraan (Nasional/Internasional), dan Program Studi.
  * **Grid Kartu Prestasi:**
    * Foto/Ikon Sertifikat atau Dokumentasi Juara.
    * Lencana Tingkat (Warna Emas untuk Internasional, Biru untuk Nasional).
    * Lencana Juara (Juara 1 / Juara 2 / Juara 3).
    * Judul Kegiatan / Nama Kompetisi.
    * Nama Mahasiswa & NIM (Sensor parsial untuk privasi: `2206***`).
    * Program Studi & Penyelenggara.
    * Tanggal Capaian.

---

### 3.8 P-08: Pusat Informasi & Regulasi Publik (`/informasi`)
* **Pengguna:** Publik, Mahasiswa, Ormawa, Pejabat.
* **Tujuan:** Portal berita kegiatan kampus resmi, pengumuman ormawa terkurasi BEM, serta repositori dokumen hukum BPM.
* **Komponen & Layout:**
  1. **Tab Navigasi:** *Tab 1: Berita & Pengumuman* | *Tab 2: Regulasi & Pedoman*.
  2. **Filter & Search Bar:**
     * Filter Kategori Berita: *Semua*, *Resmi Kampus (BKHM)*, *Agenda BEM*, *Kegiatan HIMA/UKM*.
     * Kolom pencarian teks instan judul pengumuman.
  3. **Grid Kartu Berita (Media Card 16:9):**
     * **Cover Image / Poster:** Tampil penuh jika ada gambar sampul (`gambar_sampul`), dengan lencana penerbit (*Resmi BKHM*, *BEM ITG*, atau nama Ormawa) di pojok gambar.
     * **Meta Info:** Tanggal publikasi + Tanggal pelaksanaan kegiatan (dengan ikon kalender).
     * **Judul Berita:** Teks tebal, dapat diklik menuju detail (`/informasi/{id}`).
     * **Cuplikan Narasi:** Ringkasan 3 baris (*line-clamp-3*).
     * **Footer Kartu:** Diterbitkan oleh nama ormawa, tombol unduh lampiran (jika ada PDF), dan tautan *"Detail / Baca Selengkapnya &rarr;"*.
  4. **Tabel Repositori Regulasi BPM (Tab 2):**
     * Kolom: Judul Regulasi, Kategori (UU / Pedoman / Keputusan), Diterbitkan Oleh, Tanggal Terbit, Tombol Aksi *"Unduh Dokumen PDF"*.

---

### 3.9 P-09: Halaman Detail Berita & Pengumuman (`/informasi/{pengumuman}`)
* **Pengguna:** Publik tanpa login.
* **Tujuan:** Membaca konten berita secara utuh, melihat poster ukuran penuh, mengunduh juknis, dan membagikan berita.
* **Komponen & Layout:**
  1. **Breadcrumbs:** `Pusat Informasi > Judul Berita` + Tombol `&larr; Kembali`.
  2. **Banner Peringatan Draf (Jika Status Belum Published):** Hanya tampil bagi pengunggah dan BEM bahwa artikel dalam mode pratinjau.
  3. **Header Artikel:**
     * Lencana Kategori & Tanggal Terbit.
     * Judul Artikel (H1 tebal).
     * Avatar & Nama Organisasi Pengunggah / Penulis.
     * Tombol Hapus (Hanya muncul untuk pemilik artikel atau Admin/BEM).
  4. **Hero Poster Image (Media Viewer):**
     * Foto poster beresolusi tinggi di bagian tengah.
     * Efek hover cursor pointer dengan keterangan *"Klik untuk memperbesar gambar"*.
     * **Lightbox Modal:** Saat poster diklik, muncul modal gelap dengan gambar ukuran asli untuk kenyamanan membaca teks pamflet.
  5. **Konten Narasi (Prose Body):**
     * Teks lengkap dengan format paragraf rapi dan spasi yang proporsional (*comfortable reading typography*).
  6. **Kotak Lampiran PDF Panduan (Jika Ada):**
     * Ikon berkas, judul dokumen lampiran, ukuran, dan tombol unduh langsung.
  7. **Action Share Bar:**
     * Tombol **"Bagikan ke WhatsApp"** (Auto-generate teks judul dan tautan URL).
     * Tombol **"Salin Tautan"** (Dengan animasi teks feedback *"✓ Tersalin!"* selama 2 detik).
  8. **Rekomendasi Berita Lainnya:** Grid 2 kolom menampilkan 4 berita terbaru lainnya.

---

## 4. SPESIFIKASI HALAMAN AUTENTIKASI & PENGURUS INTERNAL

### 4.1 A-01: Halaman Login Pengurus & Ormawa (`/login`)
* **Tujuan:** Titik masuk tunggal pengurus ormawa, verifikator, dan pejabat kampus.
* **Komponen UI:**
  * Logo resmi ITG di bagian atas form.
  * Judul: **"Login Pengurus & Ormawa"**.
  * Subjudul: *"Portal otentikasi khusus ormawa, verifikator, sarpras, dan pejabat kampus"*.
  * Kolom Input 1: **"Email Akun Pengurus / Ormawa"** (Placeholder: `contoh: bem@itg.ac.id atau hima@itg.ac.id`).
  * Kolom Input 2: **"Password"**.
  * Checkbox: *"Ingat Saya"*.
  * Tombol Submit Primer: **"Masuk ke Dashboard"**.
  * **Callout Khusus Mahasiswa di Bawah Form:**
    * Box berlatar abu-abu terang: *"Mahasiswa umum tidak perlu login untuk menyampaikan aspirasi, konseling BKHM, atau lapor prestasi."*
    * Link tebal berwarna biru: **"Buka Portal Layanan Mahasiswa (Tanpa Login) &rarr;"**.
* **Ketentuan Registrasi (A-02):**
  * Akses langsung ke URL `/register` harus otomatis **dialihkan (redirect)** kembali ke `/login` dengan notifikasi bahwa pendaftaran akun mandiri ditiadakan dan akun dikelola terpusat oleh BKHM.

---

## 5. SPESIFIKASI DASHBOARD & MODUL PENGURUS (AUTHENTICATED)

### 5.1 Navigasi Utama Pengurus (Sidebar & Topbar)
* **Sidebar Navigasi (Dinamis Berdasarkan Peran Pengguna):**
  * Logo resmi ITG dalam kontainer lingkaran putih bersih & teks *"Sistem Keuangan"*.
  * **Menu Bersama (Semua Role Login):**
    * Notifikasi (dengan lencana merah angka belum dibaca).
    * Dashboard Utama.
    * Informasi & Agenda: *Pusat Info & Berita* (`/informasi`) dan *Jadwal Rapat & Koordinasi* (`/rapat`).
  * **Menu Khusus Verifikator (BEM, BPM, BKHM, WR3, Bendahara):**
    * Tautan langsung: **"Verifikasi Proposal"** (atau **"Pencairan Dana"** khusus Bendahara) &rarr; menuju antrean kerja `/verifikasi`.
  * **Grup Menu Kelola BEM (`role: bem`):**
    * Verifikasi Proposal Ormawa (`/verifikasi`).
    * Kurasi Berita Ormawa (`/bem/kurasi-pengumuman`).
    * Monitoring Program Kerja Ormawa (`/proker`).
  * **Grup Menu Kelola BPM (`role: bpm`):**
    * Dashboard BPM (`/bpm/dashboard`).
    * Verifikasi Proposal (`/verifikasi`).
    * Buat Surat Peringatan SP (`/bpm/sp/create`).
    * Kelola Aspirasi Mahasiswa (`/bpm/aspirasi`).
    * Kelola Regulasi & Dokumen Hukum (`/bpm/regulasi`).
    * Monitoring Program Kerja Ormawa (`/proker`).
  * **Grup Menu Kelola WR3 (`role: wr3`):**
    * Dashboard Pimpinan WR3 (`/dashboard`).
    * Verifikasi Proposal Pimpinan (`/verifikasi`).
    * Verifikasi & Monitoring Prestasi Mahasiswa (`/prestasi`).
  * **Grup Menu Kelola Bendahara (`role: bendahara`):**
    * Antrean Pencairan Dana Proposal (`/verifikasi`).
    * Unduh Rekap Pencairan Excel (`/bendahara/export-excel`).
    * Unduh Rekap Pencairan PDF (`/bendahara/export-pdf`).
    * Unduh Rekap Pencairan CSV (`/bendahara/export`).
  * **Grup Menu Kelola BKHM (`role: bkhm`):**
    * *Sub-grup Verifikasi & Anggaran:* Verifikasi Proposal (`/verifikasi`), Verifikasi Peminjaman Ruangan & Barang (`/verifikasi-peminjaman`), Manajemen Saldo Ormawa (`/bkhm/saldo`), Manajemen User (`/admin/users`), Arsip Surat BKHM (`/bkhm/arsip-surat`), Buat Surat Peringatan SP (`/bkhm/surat-peringatan/create`).
    * *Sub-grup Layanan Mahasiswa:* Tiket Konseling Rahasia (`/bkhm/konseling`), Eskalasi Aspirasi (`/bkhm/tiket-aspirasi`), Verifikasi Prestasi (`/bkhm/tiket-prestasi`).
    * *Sub-grup Ekspor Laporan:* Ekspor Keuangan Excel (`/bkhm/export-excel`), Ekspor Keuangan PDF (`/bkhm/export-pdf`).
  * **Grup Pengajuan & Dokumen (Ormawa, BEM, BPM):**
    * *Pengajuan:* Buat Pengajuan Anggaran, Riwayat Pengajuan, Program Kerja.
    * *Sarpras:* Tempat & Fasilitas (Ajukan & Riwayat), Sarana & Barang (Ajukan & Riwayat).
    * *Persuratan Digital:* Buat Proposal, Buat Surat Lain, Buat LPJ, Arsip Digital.
    * *Laporan:* Arsip LPJ.
    * *Prestasi & Aspirasi:* Pelaporan Prestasi (`/prestasi`), Aspirasi Saya (`/aspirasi/saya` - khusus ormawa).
  * **Menu Admin:** Manajemen Pengguna, Konfigurasi Sistem, Monitoring Read-Only Seluruh Modul.
* **Topbar:** Tombol toggle sidebar, judul halaman aktif, profil user login (avatar, nama, role), dan menu dropdown profil / Logout.

---

### 5.2 M-01 s.d M-03: Modul Pengajuan Anggaran & Fitur Pengingat Cepat (Nudge)
* **Halaman Daftar Pengajuan (`/pengajuan`):**
  * Kartu Indikator Saldo: Saldo Awal, Total Terpakai, Sisa Saldo Tersedia.
  * Tabel Pengajuan: Nomor Surat, Nama Kegiatan, Nominal Diajukan, Tanggal Pengajuan, Lencana Status Workflow, Tombol Aksi (*Detail*, *Lacak Stepper*, *Unggah LPJ*).
* **Alur Visual Lencana Status Workflow (Workflow States):**
  1. `draft`: Abu-abu (*Draf Ormawa / BEM / BPM*)
  2. `submitted`: Biru Muda (*Menunggu Review BEM*)
  3. `bem_approved`: Indigo (*Menunggu Verifikasi BPM*) &mdash; *Proposal BEM otomatis mulai dari tahap ini.*
  4. `bpm_approved`: Ungu (*Menunggu Verifikasi BKHM Tahap 1*) &mdash; *Proposal BPM otomatis mulai dari tahap ini.*
  5. `bkhm_approved`: Biru Tua (*Menunggu Persetujuan WR3*)
  6. `wr3_approved`: Amber (*Disetujui WR3 - Siap ke Bendahara*)
  7. `to_treasurer`: Oranye (*Proses Verifikasi Bendahara*)
  8. `funds_disbursed`: Hijau (*Dana Telah Cair*)
  9. `lpj_submitted`: Teal (*LPJ Sedang Ditinjau*)
  10. `completed`: Hijau Tua (*Selesai & Diarsipkan*)
  11. `rejected`: Merah (*Ditolak / Perlu Revisi*)
* **Halaman Detail & Kolom Komunikasi (`/pengajuan/{id}`):**
  * Stepper timeline horizontal status persetujuan dari BEM &rarr; BPM &rarr; BKHM &rarr; WR3 &rarr; Bendahara.
  * Panel Dokumen: Pratinjau berkas proposal PDF bertanda tangan digital resmi.
  * Kotak Komunikasi Revisi (FR-011): Feed chat dua arah antara verifikator dan ormawa pengaju untuk mencatat revisi anggaran atau perbaikan teknis.
* **Fitur Pengingat Cepat (Nudge / Quick Reminder):**
  * **Lokasi UI:** Terletak di dalam kotak *"Status PIC Saat Ini"* pada halaman detail pengajuan (`/pengajuan/{id}`).
  * **Otorisasi:** Hanya tampil untuk pengusul pemilik proposal (`$pengajuan->user_id === Auth::id()`) saat proposal berada pada status aktif diverifikasi.
  * **Kondisi 1 (Tombol Aktif):**
    * Tombol warna Amber/Kuning: *"🔔 Kirim Pengingat ke [Lembaga Pemeriksa]"*.
    * Dialog Konfirmasi: Memunculkan konfirmasi *"Kirim notifikasi pengingat cepat kepada [Lembaga Pemeriksa] sekarang?"*.
  * **Kondisi 2 (Proteksi Cooldown 12 Jam Anti-Spam):**
    * Tombol otomatis dinonaktifkan jika pengingat telah dikirim dalam rentang waktu < 12 jam.
    * Menampilkan lencana peringatan: `Cooldown: X jam Y menit lagi` serta riwayat *"Pengingat terakhir dikirim [Tanggal & Jam] WIB (Nx dikirim)"*.
  * **Dampak Sistem Otomatis:**
    * Mengirimkan notifikasi in-app instan ke seluruh akun lembaga pemeriksa yang dituju.
    * Menyisipkan pesan audit otomatis ke thread diskusi follow-up: `🔔 [PENGINGAT CEPAT] Pengusul (...) telah mengirimkan pengingat verifikasi kepada [Lembaga] pada [Waktu] WIB`.

---

### 5.3 V-01: Modul Antrean Verifikasi Proposal Multi-Tier (`/verifikasi` & `/verifikasi/{id}`)
* **Pengguna:** BEM, BPM, BKHM, WR3, Bendahara, Admin.
* **Tampilan Tabel Antrean (`/verifikasi`):**
  * Menampilkan daftar proposal yang sedang menunggu persetujuan sesuai kewenangan peran pemeriksa.
  * **Badge Prioritas Pengingat (Nudge Priority Badge):** Baris proposal yang baru saja diingatkan oleh pengusul (< 48 jam) diberikan latar belakang warna lembut (`bg-amber-50/40`) dan lencana prioritas:
    * `🔔 Diingatkan (X jam lalu)` (warna amber tegas).
  * Tombol Aksi: **"Verifikasi"** (atau **"Proses Cair"** untuk Bendahara) &rarr; membuka `/verifikasi/{id}`.
* **Halaman Form Verifikasi (`/verifikasi/{id}`):**
  * Pratinjau proposal dan riwayat persetujuan sebelumnya.
  * Aksi transisi: *Setujui*, *Tolak dengan Catatan*, atau *Proses Transfer Dana*.

---

### 5.4 S-01: Modul Saldo Ormawa & Ekspor Keuangan BKHM (`/bkhm/saldo`)
* **Pengguna:** BKHM, Bendahara, WR3, Admin.
* **Komponen & Data yang Tampil:**
  1. **Widget Periode Anggaran Aktif:** Menampilkan nama periode (contoh: *Tahun Anggaran 2026*), rentang tanggal, dan status aktif. Form cepat aktivasi periode baru.
  2. **Toolbar Ekspor Keuangan (Export Actions):**
     * Tombol Hijau: **"Unduh Rekap Excel (.xlsx)"** &rarr; memicu unduhan spreadsheet rekapitulasi seluruh ormawa, saldo awal, realisasi, dan sisa saldo.
     * Tombol Merah: **"Unduh Rekap PDF"** &rarr; menghasilkan dokumen laporan resmi ber-kop surat ITG.
  3. **Tabel Ringkasan Saldo Ormawa:**
     * Kolom: No, Nama Ormawa & Jenis (HIMA/UKM), Saldo Awal (Rp), Total Terpakai (Rp), Sisa Saldo (Rp tebal), Rincian Kegiatan Terakhir, Aksi Kelola.
  4. **Tabel Riwayat Mutasi Saldo (*Audit Trail*):**
     * Kolom: Waktu Mutasi, Nama Ormawa, Aktor Pengubah (BKHM/Admin), Nominal Sebelum & Sesudah, Catatan Alasan Perubahan.

---

### 5.5 F-01: Modul Sarpras & Kalender Interaktif Ruangan (`/peminjaman/create-tempat`)
* **Pengguna:** Ormawa, BEM, BPM.
* **Tujuan:** Mengajukan peminjaman ruangan kampus tanpa risiko bentrok jadwal.
* **Komponen Kalender & Slot Checker:**
  1. **Slot Checker Real-Time:**
     * Dropdown pilihan Ruangan (Aula, Kelas A101, Ruang Sidang, Lapangan).
     * Input Tanggal Peminjaman.
     * Kotak Indikator Otomatis:
       - Jika Ruangan Bebas: Banner hijau *"✓ Ruangan Kosong & Siap Digunakan pada Tanggal Tersebut"*.
       - Jika Ada Jadwal: Banner kuning/merah merinci jam kuliah rutin yang terisi (contoh: *08:00 - 10:30: Algoritma & Pemrograman*) atau nama kegiatan ormawa lain yang meminjam.
  2. **Kalender Visual FullCalendar:**
     * Grid bulanan & mingguan interaktif.
     * Warna **Biru**: Jadwal Perkuliahan Rutin Kampus.
     * Warna **Oranye**: Kegiatan Ormawa yang Telah Disetujui.
  3. **Pilihan Alur Peminjaman (BR-09 - Dua Jalur Sarpras):**
     * Jalur 1 (HIMA/UKM): Memiliki berkas Surat Pengantar Ketua Prodi &rarr; bypass BKHM, langsung diverifikasi oleh Sarpras.
     * Jalur 2 (BEM/BPM): Verifikasi melalui BKHM terlebih dahulu &rarr; Sarpras.

---

### 5.6 K-01: Panel Kurasi Berita BEM (`/bem/kurasi-pengumuman`)
* **Pengguna:** Pengurus BEM ITG & Admin.
* **Tujuan:** Memvalidasi draf publikasi pengumuman/pamflet acara yang diajukan oleh HIMA dan UKM sebelum tayang ke mahasiswa umum.
* **Tampilan Data Antrean:**
  * Lencana Nama Ormawa Pengaju (HIMA IF, UKM Olahraga, dll).
  * Tanggal Pengajuan & Tanggal Pelaksanaan Acara.
  * **Pratinjau Gambar Poster:** Thumbnail poster pamflet 16:9 yang dapat diklik untuk memperbesar.
  * Judul & Uraian Narasi Berita.
  * Berkas Lampiran PDF (jika disertakan).
* **Aksi Kurator:**
  * Tombol Abu-abu: **"Pratinjau Tampilan Berita"** (Melihat tampilan show seperti yang akan dilihat mahasiswa).
  * Tombol Merah: **"Tolak Berita"** &rarr; Membuka modal input alasan penolakan (catatan dikirimkan ke Ormawa).
  * Tombol Ungu/Indigo: **"Setujui & Publikasikan"** &rarr; Mengubah status menjadi `published` seketika tayang di portal publik dan halaman detail.

---

### 5.7 K-02: Panel Himpun Aspirasi BPM (`/bpm/aspirasi`)
* **Pengguna:** Pengurus BPM ITG.
* **Tujuan:** Mengelola suara mahasiswa dan meneruskan aspirasi krusial ke BKHM.
* **Tampilan 2 Tab/Section:**
  * **Section 1: Aspirasi Bertiket Mahasiswa Publik (Guest)**
    * Tabel: Kode Tiket (`SKIN-TKT-YYYY-XXXX`), Identitas Mahasiswa (Nama, NIM, Prodi), Judul & Masukan, Lencana Status, Tombol Aksi.
    * **Aksi Khusus:** Tombol **"Teruskan ke BKHM &rarr;"** (Membuka modal input catatan rekomendasi BPM & mendisposisikan tiket ke dashboard BKHM).
  * **Section 2: Aspirasi Internal Pengurus Login.**

---

### 5.8 K-03 & K-04: Panel BKHM untuk Tiket Konseling & Prestasi
* **Panel Konseling Personal (`/bkhm/konseling` & `/bkhm/konseling/{tiket}`):**
  * Tampilan daftar mahasiswa yang membutuhkan pendampingan konseling.
  * Indikator data terenkripsi.
  * **Form Penetapan Jadwal Temu:** Input Tanggal Sesi, Jam Temu, Lokasi Konseling (Ruang BKHM / Link Google Meet), Catatan Konselor.
  * Otomasi Email: Penentuan jadwal otomatis memicu pengiriman email jadwal resmi ke mahasiswa.
  * **Indikator Feedback Mahasiswa:** Menampilkan apakah mahasiswa telah mengonfirmasi kehadiran (*Hadir Sesuai Jadwal*, *Minta Reschedule*, atau *Batal*).
* **Panel Verifikasi Prestasi (`/bkhm/tiket-prestasi`):**
  * Daftar laporan capaian lomba mahasiswa dan permohonan bantuan biaya delegasi.
  * Pratinjau berkas sertifikat atau proposal delegasi.
  * Tombol Switch Toggle: **"Tampilkan ke Showcase Publik"**.
  * Aksi: *Setujui Prestasi* / *Tolak dengan Catatan*.

---

## 6. STANDAR STATE HANDLING & FEEDBACK PENGGUNA (UX BEST PRACTICES)

1. **Empty State:** Setiap tabel atau grid kartu (Berita, Regulasi, Pengajuan, Tiket) yang tidak memiliki data wajib menampilkan ilustrasi/ikon kosong, judul *"Belum Ada Data"*, dan keterangan solutif (contoh: *"Belum ada berita yang diterbitkan untuk kategori ini"*).
2. **Loading State:** Tombol aksi form (Kirim Tiket, Setujui, Unggah Dokumen) wajib menampilkan spinner animasi dan status teks *"Memproses..."* saat diklik untuk mencegah duplikasi submit (*double submit*).
3. **Konfirmasi Aksi Destruktif:** Setiap aksi hapus pengumuman, tolak proposal, atau reset data wajib memunculkan modal konfirmasi *native* atau modal dialog (contoh: *"Apakah Anda yakin ingin menolak pengajuan ini? Tindakan ini tidak dapat dibatalkan."*).
4. **Notifikasi Toast / Flash Message:** Setiap aksi berhasil harus menampilkan banner/toast di pojok atas (Hijau untuk sukses, Merah untuk kegagalan validasi, Biru untuk informasi status).

---

## 7. CHECKLIST SERAH TERIMA UNTUK TIM FRONTEND & UI/UX

- [ ] Seluruh warna dan kontras teks memenuhi standar **WCAG 2.1 AA** (kontras minimal 4.5:1).
- [ ] Tombol dan link navigasi publik ramah sentuhan (*touch target* minimal 44x44 piksel pada mobile).
- [ ] Form publik tiket memiliki validasi client-side yang jelas sebelum request dikirim ke backend.
- [ ] Seluruh gambar poster pengumuman memiliki *aspect ratio* yang konsisten dan *lazy loading*.
- [ ] Halaman cetak laporan PDF memiliki tata letak halaman A4 yang rapi dan tidak terpotong.
- [ ] Modal lightbox poster pada detail berita dapat ditutup dengan tombol ESC maupun klik latar belakang.
- [ ] Tidak ada tautan atau tombol registrasi akun mandiri yang tampak pada antarmuka publik.

---
*Dokumen ini disusun resmi untuk pengembangan berkelanjutan Sistem Informasi Kemahasiswaan (SKIN) Institut Teknologi Garut versi 3.0.*