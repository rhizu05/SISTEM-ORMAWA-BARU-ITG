# Arsitektur & Struktur Kode: Sistem Kemahasiswaan ITG

Sistem ini dikembangkan tanpa *framework* penuh (seperti Laravel atau CodeIgniter) demi menjaga kecepatan dan kesederhanaan *deployment*. Namun, kode disusun secara terstruktur menyerupai pola **MVC (Model-View-Controller)** yang dimodifikasi dengan **Global Security Middleware**.

---

## 1. Siklus Request (Request Lifecycle)

Semua *request* ke sistem (kecuali gambar/aset statis) masuk melalui satu pintu gerbang utama: `index.php`.

**Alur Eksekusi:**
1. **`index.php`**: Memuat konfigurasi utama (`config.php`), menghubungkan ke database, memuat fungsi helper, dan menginisiasi kelas `Router`.
2. **`Router->dispatch()`**:
   - Memanggil `applySecurityMiddleware()` secara global (Menerapkan HTTP Security Headers, validasi Timeout Sesi, Proteksi CSRF untuk request POST, dan Pengecekan Rate-Limit / Brute Force).
   - Memetakan parameter `$_GET['page']` ke kelas Controller (untuk request POST/Action) atau ke file View (untuk render HTML).
3. **Controller**: Melakukan operasi logika bisnis (insert/update DB, verifikasi logika, dll). Jika berhasil/gagal, me-redirect kembali melalui HTTP Header `Location` dengan menempelkan `?success=` atau `?error=`.
4. **View (`app/views/`)**: Di-render melalui metode `$this->render()`. Fungsi ini otomatis membungkus konten dengan tata letak global (`header.php` dan `footer.php`).

---

## 2. Struktur Direktori Utama

```text
/sistem_keuangan
├── app/
│   ├── api/            # Endpoint respons mentah (SSE, JSON Data Dashboard, Ekspor PDF)
│   ├── controllers/    # Kelas penanganan POST request logika bisnis
│   ├── core/           # Kelas inti (`Router.php` & `Controller.php` dasar)
│   ├── helpers/        # Kumpulan fungsi prosedural (mailer, session, twofa, fungsi sistem)
│   └── views/          # Template HTML (.php), dibagi berdasarkan folder Role
├── assets/             # File statis (CSS custom, JS app.js, Gambar)
├── config.example.php  # Template konfigurasi sistem
├── docs/               # Laporan testing, catatan keamanan, dan panduan rilis
├── scripts/            # Skrip CLI (Migrasi manual, Seeder Dummy, dsb)
└── tests/              # Struktur PestPHP untuk Automated Unit Testing
```

---

## 3. Keamanan Sistem (Pertahanan Berlapis)

Sistem ini dilengkapi 5 tahap keamanan (*Security Enhancements Phase 1-5*):

- **Proteksi Session (Phase 4):**
  Aplikasi mematikan koneksi otomatis setelah 30 menit inaktivitas. Manajemen sesi disesuaikan menggunakan parameter Cookie ketat (`HttpOnly`, `SameSite=Strict`, dan `Secure`). Sesi diregenerasi *setelah* pengguna berhasil login (mencegah *Session Fixation*).
  
- **Proteksi CSRF (Phase 1):**
  Seluruh form yang merubah state sistem wajib memiliki fungsi `csrf_field()`. Token diverifikasi di `Router.php` dan akan memuntahkan status `419` / `500` jika tidak sah.

- **Proteksi Brute-Force & Rate Limit (Phase 3):**
  Penyaringan berbasis IP. Maksimal 5x gagal login dalam waktu 15 menit, atau maksimal 20x request form lainnya dalam 1 menit. Dicatat pada tabel database `login_attempts`.
  
- **Keamanan Dua Faktor (Phase 5 / 2FA):**
  Opsi TOTP berbasis waktu via Aplikasi Authenticator (Google Authenticator / Authy) dan *Backup Codes* untuk proteksi peran sentral (BEM, BKKH, Admin, Bendahara).

- **Upload File Aman (Phase 2):**
  Menggunakan `finfo` (File Info) untuk memeriksa isi asli file yang diunggah (MIME Tye), bukan hanya mencocokkan ektensi (`.pdf`/`.png`), agar file eksekutabel (PHP shell) tidak lolos dari injeksi.

---

## 4. Pola Pengembangan Selanjutnya (Guidelines)

Jika Anda Ingin **Menambahkan Halaman/Fitur Baru**:
1. Buat file tampilan UI (HTML/PHP) Anda di dalam folder `app/views/...`.
2. Buka `app/core/Router.php`, tambahkan nama halaman Anda di dalam array `private $pageMap = [...]` serta masukkan role siapa saja yang diizinkan untuk mengaksesnya.
3. (Opsional) Jika halaman Anda berisi form POST untuk merubah database, daftarkan *action* form tersebut ke array `$controllers` di dalam metode `Router::handlePostActions()`, kemudian buat logic-nya di folder `app/controllers/`.

---
**END OF DOCUMENT**