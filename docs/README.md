# Dokumentasi Sistem Informasi Kemahasiswaan (SKIN)

Sistem Informasi Kemahasiswaan (SKIN) — disebut juga **SKIN / skin-itg** — adalah aplikasi web yang dikembangkan untuk **Institut Teknologi Garut (ITG)** dalam mengelola seluruh siklus keuangan organisasi kemahasiswaan (Ormawa), mulai dari pengajuan dana, verifikasi, pencairan, hingga laporan pertanggungjawaban (LPJ).

Dokumentasi ini disusun sebagai **dokumentasi kondisi awal sistem** sebelum dilakukan perubahan atau penambahan fitur lebih lanjut.

---

## Daftar Isi

| No | File | Topik |
|----|------|-------|
| 1 | [01-pendahuluan.md](./01-pendahuluan.md) | Gambaran umum, cara menjalankan, dan environment pengembangan |
| 2 | [02-struktur-sistem.md](./02-struktur-sistem.md) | Arsitektur, teknologi, struktur project, dan database |
| 3 | [03-role-dan-fitur.md](./03-role-dan-fitur.md) | Daftar role, hak akses, dan fitur yang tersedia |
| 4 | [04-alur-proses-bisnis.md](./04-alur-proses-bisnis.md) | Gambaran alur proses bisnis sistem |
| 5 | [05-permasalahan-dan-pengembangan.md](./05-permasalahan-dan-pengembangan.md) | Catatan permasalahan awal dan potensi pengembangan |

---

## Ringkasan Cepat

- **Nama sistem:** SKIN (Sistem Informasi Kemahasiswaan) / skin-itg
- **Institusi:** Institut Teknologi Garut (ITG)
- **Jenis aplikasi:** Aplikasi web PHP (custom MVC, tanpa framework)
- **Database:** MariaDB 10.4 / MySQL 8 (`db_pengajuan`)
- **Repository:** [github.com/rdreikhan-commits/SISTEM-ORMAWA-BARU-ITG](https://github.com/rdreikhan-commits/SISTEM-ORMAWA-BARU-ITG)
- **Entry point:** `index.php` (front controller) → `app/core/Router.php`
- **Role pengguna:** `ormawa`, `bem`, `bpm`, `bkh`, `wr3`, `bendahara`, `sarpras`, `sarpras_barang`
- **Alur utama:** Pengajuan Dana → Verifikasi (BEM → BPM → BKKH → WR3) → Pencairan (Bendahara) → LPJ → Selesai
