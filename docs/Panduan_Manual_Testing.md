# PANDUAN PENGUJIAN MANUAL SISTEM KEUANGAN (SKIN)

Panduan ini berisi langkah-langkah *click-by-click* untuk memverifikasi fitur-fitur baru (Security, 2FA, SSE, Password Reset, Dashboard Analytics). 
Karena data dummy telah disuntikkan dan mailer telah di-mocking, Anda dapat melakukan semua ini langsung di local browser Anda (http://localhost/sistem_keuangan).

---

## **1. UJI COBA DASBOR ANALYTICS (Chart.js)**
1. **Login sebagai BEM/BPM atau Admin:**
   - Gunakan `admin` / `password123`.
   - Pergi ke halaman **Dashboard**.
   - Anda harus melihat 3 grafik muncul: **Trend Pengajuan Masuk (Garis)**, **Status Pengajuan Keseluruhan (Pie)**, dan **Top 5 Ormawa (Bar Horizontal)**.
   - Arahkan kursor (*hover*) ke grafik untuk melihat detail angka interaktif.
2. **Uji Coba Export PDF/Excel:**
   - Pada kartu "Top 5 Ormawa", klik tombol **PDF**. File PDF harus ter-download otomatis.
   - Buka file PDF dan pastikan tabelnya terisi data dengan format yang rapi.
3. **Uji Coba Tampilan Ormawa:**
   - Logout.
   - Login sebagai `himatif` / `password123`.
   - Di Dashboard, Anda harus melihat grafik berbentuk Donat (*Doughnut*) dan Bar yang hanya menampilkan data milik "Himatif ITG" saja.

---

## **2. UJI COBA LUPA PASSWORD & RESET SANDI**
1. **Pastikan dalam Keadaan Logout.** Buka halaman Login.
2. Klik link **Lupa Password?** di bagian bawah form login.
3. Masukkan email: `admin@yopmail.com` lalu klik **Kirim Link Reset**.
   - Anda akan melihat notifikasi hijau *"Link reset password telah dikirim"*.
4. **Membuka "Email" Mock:**
   - Buka file `storage/logs/email_mock.log` menggunakan Code Editor/Notepad.
   - Temukan baris terbaru bertuliskan `Link Reset: http://localhost/sistem_keuangan/index.php?page=reset_password&token=xxxx...`
   - Copy URL lengkap tersebut dan paste ke Web Browser.
5. **Membuat Sandi Baru:**
   - Anda akan melihat form "Buat Password Baru".
   - Masukkan password baru (contoh: `adminbaru123`).
   - Login dengan username `admin` dan password `adminbaru123` tersebut. Harus berhasil.
6. **Mencegah Penggunaan Ulang:**
   - Buka kembali link reset password yang Anda *copy* tadi.
   - Sistem harus menolak dengan pesan *"Token reset password tidak valid atau sudah kedaluwarsa."*

---

## **3. UJI COBA NOTIFIKASI REAL-TIME (SSE)**
1. Buka browser dan login sebagai `admin` (gunakan password baru Anda di langkah sebelumnya).
2. **Buka dua tab (Tab A dan Tab B)** pada halaman Dashboard. Biarkan kedua tab terbuka bersebelahan.
3. Buka Terminal/Command Prompt di PC Anda, lalu jalankan script ini:
   ```bash
   php scripts/trigger_sse_notif.php
   ```
4. **Perhatikan Browser:** Dalam waktu maksimal 2 detik, **kedua tab** (Tab A & B) akan memunculkan *Pop-up Toast* hijau secara bersamaan di kanan atas layar berbunyi: *"🔥 TEST NOTIF SSE REAL-TIME..."* tanpa Anda perlu melakukan *refresh* halaman (F5).
5. Klik lonceng notifikasi di atas. Angka warna merah harusnya bertambah.

---

## **4. UJI COBA KEAMANAN DUA FAKTOR (2FA)**
1. Dalam posisi login sebagai `admin`, klik nama Anda di kanan atas -> pilih **Profil**.
2. Gulir ke bawah hingga menemukan kartu **Keamanan Dua Faktor (2FA)**.
3. Klik tombol hijau **Aktifkan 2FA Sekarang**.
4. Buka aplikasi *Google Authenticator* atau *Authy* di HP Anda.
   - Scan QR Code yang muncul di layar.
   - Lihat 6-digit angka di HP Anda, ketik ke dalam kolom di browser, klik **Aktifkan**.
   - Sistem akan me-redirect kembali ke Profil dengan notifikasi hijau. 2FA kini statusnya "Sedang Aktif".
5. **Uji Enforce Login:**
   - Logout dari sistem.
   - Login kembali dengan username `admin` dan password Anda.
   - Sistem akan *menahan* Anda di layar form **Keamanan Dua Faktor**, BUKAN langsung ke Dashboard.
   - Masukkan kembali kode 6-digit dari HP. Anda baru bisa masuk.
6. **Uji Backup Code:**
   - Pada profil, copy salah satu dari 8 Backup Codes (kode teks pendek).
   - Logout, login kembali.
   - Di layar 2FA, klik tautan *Gunakan Backup Code*.
   - Paste kode tersebut. Anda akan berhasil login.
   - Jika Anda coba ulangi langkah ini dengan kode yang persis sama, kode itu akan ditolak karena sudah *hangus*.

---

**Selesai!** Jika semua langkah di atas berjalan sesuai harapan, maka Milestone Security Phase 1-5 dan Analytics ini dinyatakan 100% sempurna tanpa bug fungsional.