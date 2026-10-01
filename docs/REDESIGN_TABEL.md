# 📊 Dokumentasi Redesign UI — Standardisasi Elemen Tabel Sistem

**Proyek:** SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu  
**Tanggal Update:** 1 Oktober 2026  
**Tool Design:** pen.dev (Canvas: `SKIN UI Redesign.pen` — Koordinat Y: 16000)  
**Status:** 🟢 **TERSTANDARISASI & LIVE DI SISTEM LOKAL**

---

## 1. Analisis Kebutuhan & Masalah Eksisting
Sebelumnya, tabel-tabel data di dalam sistem SKIN ITG (Pengajuan, Verifikasi, LPJ, Proker, dll.) ditulis secara *inline HTML* standar bawaan template dengan karakteristik yang kaku:
1. **Wadah Sederhana:** Menggunakan wrapper box kotak biasa dengan sudut kecil (`rounded-lg`) dan bayangan minim (`shadow-sm`).
2. **Header Datar:** Baris `<thead>` berlatar abu-abu kusam (`bg-gray-50`) dengan kontras teks yang rendah.
3. **Pill & Badge Monoton:** Status alur kerja dan urgensi berupa kotak bersudut tajam tanpa indikator titik (*dot*), membuat pemindaian mata (*scanning*) menjadi melelahkan.
4. **Tombol Aksi Kurang Tegas:** Tombol aksi hanya berupa garis tepi ungu tipis (`border-indigo-600`) atau teks polos tanpa hierarki visual yang mengarahkan keputusan pengguna.

---

## 2. Konsep Desain Baru: Modern Academic Data Grid
Mengadopsi identitas akademik ITG yang prestisius, profesional, dan bersih:

1. **Card Container Modern:**
   - Sudut membulat besar (`rounded-2xl`).
   - Border halus berpresisi tinggi (`border border-slate-200/80`).
   - Elevasi bayangan lembut (`shadow-[0_4px_20px_rgba(0,0,0,0.03)]`).
   - Scrollbar horizontal mulus (`skin-scrollbar`).

2. **Clean Slate Header:**
   - Latar belakang header semi-transparan yang bersih (`bg-slate-50/90`).
   - Tipografi uppercase berukuran `[11px]` dengan font tebal (`font-bold text-slate-600 tracking-wider`).

3. **Komponen Baris & Data:**
   - **Avatar Ormawa:** Avatar inisial 2 huruf berlatar biru muda (`bg-blue-50 text-[#1E40AF]`) + ID pengajuan numerik monospace.
   - **Pill Badges Membulat:**
     - ⏱️ Urgensi Acara: `rounded-full` berbobot tebal (`⚡ H-X Mendesak`).
     - 📋 Proker Tag: `bg-blue-50 text-blue-700` dengan batas maksimal karakter agar tidak merusak lebar kolom.
     - 🔔 Nudge Pengingat: `bg-amber-100 text-amber-900 border-amber-300`.
   - **Status Alur Dinamis:** Menggunakan *status chip* membulat (`rounded-full`) yang dilengkapi titik indikator berdenyut (*pulsing dot*) untuk menandakan proses aktif.
   - **Tipografi Angka Dana:** Menggunakan format *tabular numbers* (`font-mono font-extrabold`) agar perbandingan digit nominal antar-baris tersusun sejajar rapi.

4. **Tombol Aksi Berkarakter:**
   - **Tombol Verifikasi:** *Solid Royal Navy* (`bg-[#0B1528] hover:bg-[#1E3A8A]`) berikon panah, mencerminkan otoritas pejabat kampus.
   - **Tombol Detail:** Kartu putih dengan garis tepi halus (`border-slate-200/90 hover:border-blue-400`).
   - **Tombol Upload:** Tombol hijau emerald berikon panah unggah (`bg-emerald-600 hover:bg-emerald-700`).

5. **Empty State:**
   - Menggantikan teks miring kosong menjadi ilustrasi kartu terpusat dengan ikon centang sukses dan pesan ramah *"Semua Antrean Bersih!"*.

---

## 3. Komparasi Before vs After per Tabel di pen.dev
Seluruh dokumentasi visual Before & After tersimpan pada file canvas `SKIN UI Redesign.pen`:
- **Modul Tabel Utama (Y: 16000):**
  - **Tabel 1 (Y: 16120):** Tabel Daftar Pengajuan Proposal (`/pengajuan`)
  - **Tabel 2 (Y: 16580):** Tabel Antrean Verifikasi Proposal & LPJ (`/verifikasi`)
  - **Tabel 3 (Y: 17000):** Tabel Monitoring LPJ (`/lpj`)
  - **Tabel 4 (Y: 17420):** Tabel Program Kerja Tahunan (`/proker`)
- **Widget Mini-Table Dashboard (Y: 18200):**
  - **Widget 1 (Y: 18320):** Tabel Antrean Verifikasi Proposal di Dashboard (BEM, BKHM, WR3)
  - **Widget 2 (Y: 18660):** Tabel Agenda Rapat & Koordinasi di Dashboard (BEM, BPM)

---

## 4. Daftar File yang Berhasil Di-standarisasi
Pola desain ini telah sukses diterapkan pada 8 modul tampilan di `resources/views`:

### A. Halaman Modul Utama
1. 📄 `resources/views/verifikasi/index.blade.php` (Antrean Verifikasi Proposal & LPJ)
2. 📄 `resources/views/pengajuan/index.blade.php` (Daftar Pengajuan Anggaran & Proposal)
3. 📄 `resources/views/lpj/index.blade.php` (Monitoring Laporan Pertanggungjawaban)
4. 📄 `resources/views/proker/index.blade.php` (Program Kerja Tahunan Ormawa & Evaluasi BPM)

### B. Widget Mini-Table Dashboard Pimpinan & Verifikator
5. 📄 `resources/views/dashboard/bem.blade.php` (Agenda Rapat & Antrean Proposal BEM)
6. 📄 `resources/views/dashboard/bpm.blade.php` (Proposal Legislatif & Sidang Parlemen BPM)
7. 📄 `resources/views/dashboard/bkhm.blade.php` (Proposal, LPJ, Peminjaman Tempat & Barang, Audit Saldo, Quick Stat Cards)
8. 📄 `resources/views/dashboard/wr3.blade.php` (Validasi SP, Proposal WR3, Monitoring Saldo Ormawa, Audit Mutasi)

---

## 5. Redesign Kartu Metrik Dashboard (Opsi A: Modern Academic Metric Card)
Pada dashboard BKHM (`resources/views/dashboard/bkhm.blade.php`), 6 kartu metrik aksi cepat menggunakan **Opsi A (Modern Academic Metric Card / Icon Badge + Status Tint)**:
- **Karakter Visual:**
  - Card berpenampilan prestisius (`bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md`).
  - Baris Atas (*Top Row*): Icon badge bersudut halus (`rounded-xl p-2`) dengan latar warna lembut yang serasi + *Pill tag* status fungsional di sudut kanan (`Tahap 1`, `Akuntabilitas`, `Perlu Review`, `Pencairan`, `Sarpras`, `Inventaris`).
  - Bagian Tengah (*Mid*): Angka counter berukuran besar dengan font monospace tebal dan warna khas peran (`#1E40AF`, `#059669`, `#D97706`, `#7C3AED`, `#15803D`, `#334155`) di atas judul metrik tebal gelap.
  - Baris Bawah (*Bot Row*): Tautan aksi interaktif bergaris pemisah halus (`border-t border-slate-100`) dengan label aksi dan panah geser saat di-hover (`Antrean →`, `Buka Arsip →`, `Kurasi →`, `Antrean Kasir →`, `Kelola Slot →`, `Kelola Alat →`).
  - Efek Khusus: Kartu **Kurasi Berita** memiliki aksen batas kuning (*warm tint border* `border-amber-300 shadow-[0_4px_16px_rgba(245,158,11,0.10)]`) saat terdapat usulan berita yang memerlukan review.
- **Rincian Kartu:**
  1. 📑 **Verifikasi Proposal:** Icon badge biru (`bg-blue-50`), tag `Tahap 1`, angka `#1E40AF`, link `Antrean →`.
  2. 📋 **Verifikasi LPJ:** Icon badge emerald (`bg-emerald-50`), tag `Akuntabilitas`, angka `#059669`, link `Buka Arsip →`.
  3. 📰 **Kurasi Berita:** Icon badge amber (`bg-amber-50`), tag `Perlu Review`, angka `#D97706`, link `Kurasi →`.
  4. 💰 **Siap Bendahara:** Icon badge violet (`bg-purple-50`), tag `Pencairan`, angka `#7C3AED`, info `Antrean Kasir →`.
  5. 🏢 **Verifikasi Tempat:** Icon badge emerald/sky (`bg-emerald-50`), tag `Sarpras`, angka `#15803D`, link `Kelola Slot →`.
  6. 📦 **Verifikasi Barang:** Icon badge slate (`bg-slate-50`), tag `Inventaris`, angka `#334155`, link `Kelola Alat →`.

## 6. Redesign Dashboard Utama Ormawa (UI-020: Opsi A - Modern Academic Executive)
Pada dashboard pengurus ormawa (`resources/views/dashboard/ormawa.blade.php`), 3 elemen utama telah dimodernisasi menggunakan **Opsi A (Modern Academic Executive)**:
- **5 Kartu Counter Data (Top Stats):**
  1. 💰 **Sisa Saldo Tersedia:** Icon box emerald pastel (`bg-emerald-50 text-emerald-600`), tag `Kas Ormawa`, angka monospaced tebal, tautan `Cek Mutasi →`.
  2. 🏛️ **Total Dana Diberikan:** Icon box blue pastel (`bg-blue-50 text-[#1E40AF]`), tag `Pagu Anggaran`, angka monospaced, tautan `Rincian Pagu →`.
  3. ⏳ **Dana Terpakai & Diproses:** Icon box amber pastel (`bg-amber-50 text-amber-600`), tag `Realisasi`, angka monospaced, tautan `Tracking Dana →`.
  4. 📑 **Total Proposal Diajukan:** Icon box indigo pastel (`bg-indigo-50 text-indigo-600`), tag `Total Usulan`, counter angka, tautan `Lihat Semua →`.
  5. ⚡ **Proposal Dalam Proses:** Icon box purple pastel (`bg-purple-50 text-purple-600`), tag `Review Aktif`, counter angka, tautan `Pantau Progres →`.
- **Visualisasi Alokasi & Serapan Anggaran (Split Layout):**
  - **Sisi Kiri:** Bagan donat Chart.js modern (`cutout: '72%'`) dengan persentase di tengah lingkaran (`100% Saldo Utuh`) dan legenda status interaktif.
  - **Sisi Kanan:** 3 Bilah progres horizontal (*Progress Bars*) dengan persentase dan nominal riil (*Sisa Saldo Tersedia*, *Dana Sedang Diproses*, *Dana Terealisasi & Selesai*).
  - **Indikator Kesehatan Kas:** Badge dinamis `✓ Anggaran Sehat 100%` beranimasi pulse.
  - **Kaki Widget:** Tips pengajuan H-14 dan tombol call-to-action `+ Buat Pengajuan Baru` berwarna Solid Royal Navy (`#0B1528`).
- **Kartu Identitas Resmi Ormawa (Executive Identity):**
  - **Header Banner Resmi:** Banner navy gelap bertuliskan `PORTAL RESMI ORMAWA | T.A. 2026/2027`.
  - **Avatar & Verified Badge:** Avatar profil ormawa berbingkai ganda dengan titik indikator verifikasi hijau.
  - **Tag Peran Ganda:** Chip status `● Aktif` & `🏛️ Ormawa Kampus`.
  - **Metadata Struktural:** Kotak informasi pembina (`BKHM ITG`), pengawas (`BPM ITG`), dan status legalitas SK Rektor.
  - **Tautan Kaki:** Akses langsung ke `Pengaturan Profil & SK Organisasi →`.

## 7. Pembersihan Tombol Ganda Dashboard (UI-021)
Pada halaman publik Pusat Informasi & Regulasi (`resources/views/informasi/index.blade.php`), dilakukan perbaikan redundansi navigasi:
- **Latar Belakang:** Ketika pengguna dalam keadaan terautentikasi (*logged in*), navbar menampilkan dua tombol "Dashboard" sekaligus (satu tombol putih bergaris dari slot `nav` lokal dan satu tombol biru berikon dari layout dasar `public-layout`).
- **Tindakan:** Menghapus blok `@auth ... @endauth` pada slot `nav` lokal file `informasi/index.blade.php`.
- **Hasil:** Navbar kini bersih, hanya menampilkan tombol "Portal Layanan" di tengah dan tombol utama "Dashboard" di sisi kanan.

## 8. Optimasi Antarmuka Mobile & Sidebar Drawer (UI-022)
Dokumentasi lengkap mengenai optimasi antarmuka layar ponsel (*smartphone*), penghapusan tumpukan header (*anti-collision*), dan transformasi sidebar menjadi *mobile slide-over drawer* telah dipisahkan ke dalam dokumen tersendiri:
👉 **[Lihat Dokumentasi Lengkap Optimasi Mobile & Sidebar Drawer (docs/OPTIMASI_MOBILE_RESPONSIVE.md)](OPTIMASI_MOBILE_RESPONSIVE.md)**

---
*Dokumen ini diperbarui secara berkala sesuai perkembangan implementasi.*
