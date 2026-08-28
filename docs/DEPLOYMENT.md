# Panduan Deployment (Go-Live) Sistem Kemahasiswaan ITG

Dokumen ini diperuntukkan bagi **Tim IT / DevOps Kampus** yang akan mempublikasikan Sistem Kemahasiswaan (SKIN) ke server *Production*. Aplikasi ini dibangun menggunakan Native PHP (Procedural & OOP gabungan) dengan arsitektur keamanan tingkat tinggi.

---

## 1. Persyaratan Sistem (System Requirements)

Pastikan server produksi memenuhi spesifikasi berikut:
- **Web Server:** Nginx (Direkomendasikan) atau Apache
- **PHP Version:** Minimal **PHP 8.1** (Direkomendasikan PHP 8.2+)
- **Database:** MySQL 8.0+ atau MariaDB 10.4+
- **Composer:** v2.0+
- **Sertifikat SSL/HTTPS:** **WAJIB ADA** (Aplikasi menggunakan Cookie Security `Secure` yang hanya beroperasi di bawah HTTPS).

**Ekstensi PHP yang Wajib Aktif:**
`mysqli`, `mbstring`, `openssl`, `curl`, `json`, `gd` atau `fileinfo` (untuk manipulasi file upload).

---

## 2. Langkah-Langkah Deployment

Ikuti langkah berurutan di bawah ini untuk menghindari error instalasi.

### Step 2.1: Clone & Setup Files
1. Masuk ke direktori web server Anda (contoh: `/var/www/sistem_keuangan`).
2. *Clone* repositori (branch `develop` atau `main`).
3. Set *ownership* ke web server user (biasanya `www-data`):
   ```bash
   chown -R www-data:www-data /var/www/sistem_keuangan
   ```

### Step 2.2: Install Dependencies (Composer)
Aplikasi ini memiliki library untuk 2FA, QRCode, Export PDF, Excel, dan Testing.
Jalankan perintah ini di direktori root aplikasi:
```bash
composer install --no-dev --optimize-autoloader
```
*(Catatan: flag `--no-dev` mengabaikan library testing seperti Pest/PHPStan untuk menghemat ruang server).*

### Step 2.3: Konfigurasi Environment
Sistem ini menggunakan file `config.php` (bukan `.env`).
1. Buat file config dari template:
   ```bash
   cp config.example.php config.php
   ```
2. Edit file `config.php`:
   ```bash
   nano config.php
   ```
3. Sesuaikan blok kredensial berikut:
   - **Database:** `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
   - **SMTP Email:** `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` (Diperlukan agar fitur Lupa Password berfungsi).

### Step 2.4: Import Database
1. Buat database baru di MySQL/MariaDB (misal: `db_pengajuan`).
2. Import skema utama:
   ```bash
   mysql -u root -p db_pengajuan < scripts/db_pengajuan.sql
   ```
*(Catatan: Tabel ini sudah mencakup optimisasi indeks, kolom 2FA, tabel Notifikasi SSE, dan Log percobaan Login).*

---

## 3. Matikan Mock Email (Mode Production)

Secara default di tahap *Development*, sistem **tidak** mengirim email sungguhan (hanya mencetak ke file log `storage/logs/email_mock.log`). 

Saat aplikasi **Naik ke Production**, Anda HARUS mematikan fitur Mock ini:
1. Buka file `app/helpers/mailer.php`.
2. Cari baris `// === MOCK MODE UNTUK TESTING LOKAL ===` (sekitar baris ke 18).
3. **Hapus atau komentari (comment-out)** blok Mock Mode tersebut, dan aktifkan kembali (uncomment) blok kode asli `PHPMailer`.

---

## 4. Konfigurasi Cron Job (Maintenance)

Aplikasi ini mencatat setiap percobaan login gagal (Rate Limiting) dan menyimpan file sesi. Tambahkan tugas Cron di server Linux Anda untuk mencegah hard disk penuh.

Buka crontab: `crontab -e`, lalu tambahkan:
```bash
# Bersihkan log percobaan login gagal yang lebih dari 24 jam setiap jam 2 pagi
0 2 * * * /usr/bin/php /var/www/sistem_keuangan/scripts/cron_cleanup_db.php >> /dev/null 2>&1
```

> **Developer Note:** File `scripts/cron_cleanup_db.php` belum dibuat sebagai file fisik karena sudah di-handle oleh *probabilistic garbage collection* pada saat user mencoba login. Namun jika web traffic padat, membuat file cron terdedikasi sangat direkomendasikan.

---

## 5. Konfigurasi Web Server (Nginx) - Optional tapi Direkomendasikan

Karena aplikasi ini menggunakan **Server-Sent Events (SSE)** untuk Notifikasi Real-time, penting untuk mematikan *Buffering* di sisi Nginx agar notifikasi sampai secara seketika.

Di konfigurasi server Nginx Anda (misal `/etc/nginx/sites-available/sistem_keuangan`), tambahkan aturan ini:

```nginx
server {
    listen 443 ssl;
    server_name keuangan.itg.ac.id;
    root /var/www/sistem_keuangan;
    index index.php;

    # Matikan proxy buffering khusus untuk endpoint SSE
    location ~* /app/api/sse_notifikasi\.php$ {
        proxy_buffering off;
        fastcgi_buffering off;
        fastcgi_read_timeout 86400s;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Aturan PHP biasa
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    }
}
```

---

## 6. Penanganan Masalah Umum (Troubleshooting)

| Masalah / Gejala | Solusi |
|------------------|--------|
| **Layar Putih (HTTP 500)** pada saat Export PDF | Pastikan ekstensi `php-gd` atau `php-fileinfo` aktif. Naikkan `memory_limit` di `php.ini` menjadi minimal `256M`. |
| **Error CSRF Mismatch (419)** | Pastikan URL yang diakses *match* dengan konfigurasi, dan pastikan sesi berjalan baik (folder `/var/lib/php/sessions` *writable*). |
| **Tidak bisa login padahal password benar** | Cek tabel `login_attempts`. Jika IP terblokir, tunggu 15 menit atau hapus baris yang berkaitan dengan IP Anda secara manual di database. |
| **Link Reset Password tidak terkirim** | Periksa konfigurasi SMTP di `config.php`, dan pastikan Anda sudah mematikan Mock Mode di `mailer.php` (lihat poin 3). |

---
**END OF DOCUMENT**