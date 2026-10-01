# 📅 Dokumentasi Redesign UI — Elemen Kalender Ketersediaan

**Proyek:** SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu  
**Tanggal Update:** 1 Oktober 2026  
**Tool Design:** pen.dev (Canvas: `SKIN UI Redesign.pen`)  
**Status:** 🟢 **SELESAI & LIVE DI SISTEM (Opsi 3 Soft Glass + Navy Header)**

---

## 1. Analisis Kebutuhan & Pendekatan
Komponen kalender sebelumnya (menggunakan *FullCalendar*) terasa usang, tidak sinkron dengan gaya *academic navy* milik ITG, dan gaya penulisan legenda (teks biasa) kurang estetik. Dibutuhkan desain yang lebih bersih (*clean*), pembungkus (*wrapper*) kustom agar CSS tidak *bleeding*, dan *header* yang merepresentasikan *branding* kampus.

---

## 2. Pilihan Desain
Terdapat 3 eksplorasi opsi desain kalender yang dilakukan di *pen.dev*:
- **Opsi 1 (Minimalis Terang):** Putih bersih dengan list biru standar.
- **Opsi 2 (Dark Mode Aksen):** Mode gelap kontras tinggi.
- **Opsi 3 (Soft Glass + Navy Header):** Terpilih. Kombinasi *card* modern dengan bayangan lembut dan *header toolbar* biru Navy khas ITG.

---

## 3. Detail Implementasi & Perbaikan Visual (Live)

### 3.1 Styling Wrapper Terpusat (Blade Component)
Dibuat satu buah komponen `<x-calendar-style />` (`resources/views/components/calendar-style.blade.php`) untuk menyeragamkan *style* kalender tanpa perlu memodifikasi inisialisasi JavaScript asli *FullCalendar*. Komponen ini mengandung CSS kustom yang di-*scope* di dalam *class* `.skin-calendar-wrapper`.

### 3.2 Navy Header & Soft Glass Card
- **Header:** Bagian `.fc-header-toolbar` dirombak latar belakangnya menjadi `#0B1528` (Deep Navy) dengan sudut membulat di bagian atas (`rounded-t-2xl`).
- **Card Wrapper:** `.skin-calendar-wrapper` diberikan *styling* border tipis (`border-slate-200`), sudut melengkung `rounded-2xl`, dan `shadow-sm` untuk memberikan efek elegan dan melayang (*soft glass*).

### 3.3 Pembaruan Tag Legenda (Legend Interaktif)
Sebelumnya:
```html
<p class="text-xs text-gray-500 mb-4">Biru = jadwal kuliah. Oranye = peminjaman.</p>
```
**Diperbarui menjadi Flex Layout dengan Dot Indicator:**
Menggunakan susunan tag berbaris (sejajar horisontal) menggunakan Tailwind CSS modern. Setiap legenda diberikan *dot badge* warna (contoh: Indigo untuk Fasilitas/Jadwal Kuliah, Amber untuk Peminjaman/Barang/Rapat) lengkap dengan *ring* kontras:
```html
<span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
    <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
    Fasilitas
</span>
```

---

## 4. Distribusi Komponen (Halaman Terdampak)
Implementasi `.skin-calendar-wrapper` dan desain tag legenda baru diterapkan pada 7 halaman utama:
1. `resources/views/dashboard/bem.blade.php`
2. `resources/views/dashboard/bkhm.blade.php`
3. `resources/views/dashboard/bpm.blade.php`
4. `resources/views/dashboard/ormawa.blade.php`
5. `resources/views/dashboard/wr3.blade.php`
6. `resources/views/peminjaman/create_tempat.blade.php`
7. `resources/views/sarpras/jadwal/index.blade.php`

Semua perubahan sudah tersimpan dan tayang di *branch* `develop`.
