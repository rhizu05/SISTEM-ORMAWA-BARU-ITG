# 🔔 Dokumentasi Redesign UI — Pusat Notifikasi

**Proyek:** SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu  
**Tanggal Update:** 1 Oktober 2026  
**Tool Design:** pen.dev (Canvas: `SKIN UI Redesign.pen`)  
**Status:** 🟡 **MENUNGGU PERSETUJUAN (Opsi 1 Clean Feed)**

---

## 1. Analisis Kebutuhan & Pendekatan
Halaman Pusat Notifikasi bawaan menggunakan kotak putih generik dengan pemisah garis (divide-y). Notifikasi yang belum dibaca hanya ditandai dengan perubahan warna latar seluruh baris menjadi ungu muda (`bg-indigo-50`). Tampilannya terkesan monoton, terutama saat ada banyak notifikasi.

Solusi: Mengubah *list* notifikasi menjadi format *Clean Feed* yang lebih hidup. Setiap baris kini diberikan ikon khusus berdasarkan kata kunci di pesan, dan penanda status "Belum Dibaca" dibuat lebih elegan (titik merah pada ikon).

---

## 2. Eksplorasi Opsi Desain
Di pen.dev, dua rute desain dieksplorasi:
- **Opsi 1 (Clean Feed):** Daftar disatukan dalam satu card raksasa, namun masing-masing item memiliki padding luas, *hover state*, ikon indikator yang *colorful*, dan dipisahkan garis super tipis.
- **Opsi 2 (Floating Cards):** Daftar dipecah menjadi card mandiri yang melayang terpisah, dengan indikator *border* kiri berwarna tebal (seperti Trello/Jira).

Opsi 1 (Clean Feed) dipilih karena lebih cocok untuk halaman yang mendedikasikan seluruh layarnya murni untuk membaca informasi (menghindari visual *clutter* / terlalu banyak box).

---

## 3. Detail Implementasi & Perbaikan Visual

### 3.1 Kontainer & Layout Utama
- **Latar Halaman:** Diseragamkan menggunakan `bg-slate-50/50` agar memberikan pendaran/elevasi bagi kontainer notifikasi.
- **Header:** Tombol *Tandai semua sudah dibaca* diubah wujudnya menjadi tombol modern bertipe *pill/badge* (`px-4 py-2 bg-blue-50 text-blue-700 rounded-xl`).

### 3.2 Dynamic Icon Engine (Blade PHP Logic)
Untuk menghindari *boring UI* (teks tok), logika pemindaian kata (*string scanning*) ditambahkan pada Blade untuk memilih ikon Heroicons SVG dan warna secara otomatis berdasarkan isi notifikasi:
- Pesan mengandung **"setuju" / "berhasil"** ➔ Ikon Centang Hijau (`text-emerald-600`)
- Pesan mengandung **"tolak" / "batal"** ➔ Ikon Silang Merah (`text-rose-600`)
- Pesan mengandung **"peringatan" / "telat" / "belum"** ➔ Ikon Seru Oranye (`text-amber-600`)
- Sisanya / Umum ➔ Ikon Lonceng Biru (`text-blue-600`)

### 3.3 Penanda Status "Belum Dibaca"
Notifikasi baru (belum dibaca) memiliki 3 penanda *subtle* (halus):
1. Titik merah (Dot) `bg-rose-500` di sudut atas ikon bulat.
2. Teks deskripsi menggunakan tebal (`font-bold`).
3. Latar belakang baris transparan kebiruan tipis (`bg-blue-50/20`).

### 3.4 Empty State (Kosong)
Menambahkan ilustrasi visual ikon kotak masuk dengan pesan *"Semua Bersih!"* ketika tidak ada notifikasi, menggantikan sekadar teks miring *"Belum ada notifikasi"*.

---
*(Dokumen ini akan di-update statusnya menjadi LIVE setelah pengguna memberikan instruksi push)*
