# 👤 Dokumentasi Redesign UI — Halaman Profil / Pengaturan Akun

**Proyek:** SKIN ITG — Sistem Informasi Kemahasiswaan Terpadu  
**Tanggal Update:** 1 Oktober 2026  
**Tool Design:** pen.dev (Canvas: `SKIN UI Redesign.pen`)  
**Status:** 🟢 **SELESAI & LIVE DI SISTEM (Opsi 3 Polished Standard)**

---

## 1. Analisis Kebutuhan & Pendekatan
Halaman profil (*User Account Settings*) bawaan dari *Laravel Breeze* sangat kaku dan tumpang tindih. Form informasi profil, data tambahan (alamat, kontak, pengurus ormawa), dan update password ditumpuk ke bawah secara vertikal dengan border kotak standar yang membosankan. Tujuannya adalah merapikan tampilan form panjang ini agar terlihat profesional, *clean*, dan memandu mata pengguna (*eye-guiding*).

---

## 2. Eksplorasi Opsi Desain
Terdapat 3 rancangan (*high fidelity mockups*) yang dibuat di pen.dev menggunakan fitur impor langsung antarmuka profil asli:
1. **Opsi 1 (Split View / Settings Sidebar):** Menu navigasi (Informasi, Data Tambahan, Password) dipindah ke *sidebar* kiri, sementara isian form dimuat di *panel* kanan secara dinamis (seperti halaman *Settings* di platform modern).
2. **Opsi 2 (Modern Card - Tabs Horizontal):** Seluruh sub-form disatukan ke dalam satu card/kotak lebar dengan sistem navigasi *Tab* horisontal di bagian atasnya.
3. **Opsi 3 (Polished Standard):** Terpilih. Mempertahankan aliran natural (*stacked layout*) yang sudah berjalan namun menaikkan spesifikasi visualnya secara drastis (kartu melengkung besar, pastel icon, *drop shadow* elegan).

---

## 3. Detail Implementasi (Opsi 3 Polished Standard)

### 3.1 Background Container
Warna latar belakang diubah dari `bg-gray-100` bawaan Tailwind menjadi `bg-slate-50/50` yang lebih premium dengan `min-h-screen` agar memenuhi seluruh tinggi layar, memberi ilusi *pop-out* pada *cards*.

### 3.2 Visual Form (Cards)
Setiap blok form (Informasi, Tambahan, Keamanan) kini memiliki spesifikasi berikut:
- **Shape & Shadow:** `p-6 sm:p-8 bg-white border border-slate-200 sm:rounded-2xl shadow-[0_4px_16px_rgba(0,0,0,0.04)]`
- Memberikan efek bayangan yang lebar, sangat halus (hanya 4% opacity), dan pendaran yang premium.

### 3.3 Visual Header Form Terstruktur
Setiap sub-bagian kini menggunakan *flex layout* dengan ikon pendamping (pastel *backdrop*) untuk membedakan kategori (menggunakan *Raw SVG Heroicons* agar tidak bergantung pada pustaka eksternal):
- **Informasi Profil:** Ikon *User/Person* biru pastel
- **Data Tambahan Profil:** Ikon *Document* biru pastel
- **Keamanan:** Ikon *Lock* biru pastel

Ditambah pemisah (`hr class="border-slate-100 mb-6"`) yang dengan tegas memisahkan bagian deskripsi dengan label *input*, mempermudah pengguna membaca dan mengisi data (*scanability*).

---

## 4. Distribusi Komponen (Halaman Terdampak)
Diimplementasikan secara utuh pada folder `resources/views/profile`:
1. `edit.blade.php` (Sebagai bungkus kontainer dan pewarnaan latar utama)
2. `partials/update-profile-information-form.blade.php` (Form profil dasar)
3. `partials/update-profile-data-form.blade.php` (Form data tambahan struktural, kontak, logo, & ttd)
4. `partials/update-password-form.blade.php` (Form ganti sandi)

Seluruh perubahan sudah disesuaikan dan tayang di *branch* `develop`.
