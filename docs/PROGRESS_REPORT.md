# LAPORAN PROGRESS PENGEMBANGAN SISTEM KEMAHASISWAAN (SKIN)

Dokumen ini merangkum seluruh fase pengembangan dan perbaikan keamanan yang telah diselesaikan pada Sistem Kemahasiswaan (SKIN) ITG. Semua tahap di bawah ini telah diuji dan digabungkan (di-merge) ke dalam branch utama (`develop`).

---

## 🛡️ FASE KEAMANAN (SECURITY PHASES)

### **Phase 1: CSRF Universal Protection** ✅
- **Deskripsi**: Mencegah serangan *Cross-Site Request Forgery* pada seluruh formulir aplikasi.
- **Implementasi**: 
  - Penambahan fungsi `csrf_token()`, `csrf_field()`, dan `csrf_verify()` pada `app/helpers/functions.php`.
  - Integrasi token tersembunyi pada **36 form HTML** di seluruh sistem.
  - Pembuatan *interceptor* otomatis (`setupCsrfInterceptor()`) di `assets/js/app.js` untuk mengamankan semua permintaan AJAX secara senyap.
  - Pengecekan token diletakkan secara terpusat pada *Global Security Middleware* (`Router.php`).

### **Phase 2: File Upload Security** ✅
- **Deskripsi**: Mencegah eksekusi file berbahaya (misal: skrip PHP yang disamarkan) yang diunggah ke *server*.
- **Implementasi**:
  - Penambahan fungsi `validate_uploaded_file()` di `functions.php`.
  - Pemeriksaan ketat berdasarkan **MIME-Type** (bukan sekadar ekstensi `.pdf`/`.jpg`), batas ukuran (maksimal 5MB untuk PDF, 2MB untuk gambar), dan sterilisasi (*sanitization*) nama file menggunakan *random string*.
  - Pembaruan pada `PengajuanController.php` (untuk unggah Proposal & LPJ) dan `ProfilController.php` (untuk logo/avatar).

### **Phase 3: Rate Limiting & Brute Force Prevention** ✅
- **Deskripsi**: Membatasi laju permintaan sistem untuk melindungi halaman *Login* dari serangan *bot* yang menebak *password* secara berulang-ulang.
- **Implementasi**:
  - Penambahan tabel baru `login_attempts` dengan *composite index* di database.
  - Penambahan fungsi `get_client_ip()`, `check_rate_limit()`, dan `log_login_attempt()`.
  - Aturan: Maksimal 5x kegagalan login dalam waktu 15 menit per alamat IP.
  - Implementasi *Garbage Collection* (`cleanup_old_attempts()`) untuk menghapus log yang lebih dari 24 jam secara probabilitas.
  - Tampilan kotak peringatan merah *"Akun diblokir sementara..."* jika pengguna melewati batas.

### **Phase 4: Session Security & Hardening** ✅
- **Deskripsi**: Melindungi sesi masuk (*Session*) pengguna dari pencurian (Hijacking) maupun pembekuan (Fixation).
- **Implementasi**:
  - Penggunaan *cookie flag* tingkat lanjut: `HttpOnly` (anti-XSS), `SameSite=Strict` (anti-CSRF), dan `Secure` (otomatis aktif di HTTPS).
  - Penambahan fungsi `session_regenerate_id_safe()` yang meregenerasi ID sesi setelah login yang berhasil.
  - **Inactivity Timeout**: Pengguna akan **Logout Otomatis** jika diam selama 30 menit.
  - Pengiriman HTTP Security Headers: `X-Frame-Options: SAMEORIGIN` (anti-clickjacking), `X-Content-Type-Options: nosniff`, `Referrer-Policy`, dan `Cache-Control`.

---

## 🚀 FASE 5 (SPRINT 4): FITUR LANJUTAN & ANALISIS

### **5.A: Keamanan Dua Faktor (2FA)** ✅
- Implementasi TOTP menggunakan pustaka modern (`spomky-labs/otphp` dan `endroid/qr-code`).
- Pembuatan 8-digit *Backup Codes* untuk pemulihan darurat.
- Pembuatan antarmuka manajemen 2FA di halaman **Profil**.
- *Middleware* yang memaksa pengguna dengan peran sensitif (*admin*, *verifikator*, *bendahara*) untuk melewati halaman `login_2fa.php` sebelum mengakses sistem.

### **5.B: Notifikasi Real-Time (Server-Sent Events / SSE)** ✅
- Implementasi *event stream* di `api_sse_notif.php` menggunakan poling asinkron.
- Penambahan kolom *flagging* `terkirim_sse` di tabel `notifikasi` agar tidak terjadi *double-send*.
- Pembuatan *Pop-up Toast* hijau secara instan pada ujung layar seluruh pengguna yang sedang masuk.
- Integrasi menu lonceng notifikasi baru (dengan fitur *Tandai Sudah Dibaca*) pada *sidebar* sistem.

### **5.C: Alur Lupa Kata Sandi (Password Reset)** ✅
- Penambahan kolom `email` di tabel pengguna.
- Pembuatan URL *Reset Link* dengan sistem token *Hash* satu kali pakai (berlaku 30 menit).
- Integrasi modul pengiriman **PHPMailer** yang mendukung server SMTP profesional (seperti Brevo).
- *Mock Mode*: Saat dikembangkan di *localhost* tanpa SMTP, URL reset akan dicetak ke dalam file `storage/logs/email_mock.log` untuk dites.

### **5.D: Dashboard Analytics (Grafik & Ekspor Data)** ✅
- Mengganti tampilan statis dengan diagram Chart.js yang interaktif dan responsif (mendukung mode gelap bawaan).
- **Verifikator / Admin Dashboard:** Terdapat grafik tren pengajuan masuk (Line Chart), distribusi status global (Pie Chart), dan perbandingan serapan dana antar-ormawa (Horizontal Bar Chart).
- **Ormawa Dashboard:** Terdapat grafik status perorangan dan grafik garis pencairan dana mereka sendiri.
- Pembuatan tombol **Ekspor Laporan Bulanan (PDF & Excel)** memanfaatkan `dompdf` dan `phpoffice/phpspreadsheet`.

---

## 🛠️ PENYEMPURNAAN SISTEM (CODE QUALITY)

1. **Rebranding**: Mengubah seluruh penamaan proyek dari "SI-Keuangan" menjadi "Sistem Kemahasiswaan (SKIN)" sesuai arahan (*Find and Replace* meluas hingga struktur Composer).
2. **Audit Logs & Email Queue**:
   - Pembuatan tabel `audit_logs` untuk mencatat riwayat krusial tiap-tiap entitas (Log In, Export PDF, Setel 2FA).
   - Skrip *Queue Worker* (`scripts/queue_worker.php`) agar antrean pengiriman pesan SMTP berjalan cepat di belakang layar.
3. **Database Seeder**: 
   - Pembuatan skrip `seeder_universal.php` yang mengisi puluhan riwayat kegiatan, pencairan, peminjaman barang dan ruangan secara acak dalam kurun waktu satu tahun, guna menghidupkan seluruh visualisasi grafik di antarmuka sistem.
4. **Perbaikan *Bugs* (Bug Fixes)**:
   - Menghilangkan *Fatal Error / Undefined Offset* karena variabel MySQLi `bind_param` yang referensial.
   - Menghapus masalah `Headers already sent` dan JSON yang kotor akibat karakter penutup PHP `?>` ekstra.
   - Memastikan kolom yang *NULL* di dalam database (*deskripsi_kegiatan*, *nomor_surat*) tidak *crash* ketika melewati validasi `htmlspecialchars()` bawaan PHP 8.1+.

---

## 📚 KESIAPAN PRODUKSI (HANDOFF)

Proyek ini telah dilengkapi dengan tiga buah dokumentasi operasional khusus untuk Tim IT Kampus agar siap dimigrasikan ke peladen produksi (*Server Production*):
- `docs/DEPLOYMENT.md` (Panduan instalasi Nginx, Cron, dan dependencies)
- `docs/ARCHITECTURE.md` (Gambaran logika siklus request-response tanpa framework)
- `docs/Panduan_Manual_Testing.md` (Skenario simulasi fungsional aplikasi untuk tim *Quality Assurance*)

---
**Status Terakhir:** Seluruh rencana pengkodean **(Feature Complete 100%)** đã dituntaskan di branch lokal `develop` per tanggal rilis laporan ini.
