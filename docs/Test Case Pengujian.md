# Test Case Pengujian — SKIN

**Proyek:** Pengembangan Sistem Kemahasiswaan (SKIN) — Kerja Praktik  
**Cakupan:** Alur verifikasi berjenjang (perbaikan status WR3), notifikasi realtime (SSE), endpoint AJAX, proteksi CSRF, refactor verifikasi → controller.  
**Tanggal uji:** ________  **Penguji:** ________

> Petunjuk: jalankan sistem di `http://localhost/sistem_keuangan`. Gunakan **dua browser/tab berbeda** (mis. tab biasa + tab incognito) untuk menguji notifikasi realtime. Isi kolom **Hasil Aktual** dan **Status** (✅/❌).

---

## 1. Persiapan & Akun Uji

| Role | Username | Password | Catatan |
|---|---|---|---|
| Ormawa (HIMA Teknik Informatika) | `himatif` | `password123` (atau sesuai SQL dump) | Pengaju |
| Ormawa (HIMA SI) | `hima_si` | `password123` | Pengaju alternatif |
| BEM | `bem` | `password123` | Verifikator tahap 1 |
| BPM | `bpm` | `password123` | Verifikator tahap 2 + pengawas |
| BKKH | `bkkh` | `password123` | Verifikator tahap 3 + admin |
| WR3 | `wr3` | `password123` | Persetujuan akhir |
| Bendahara | `bendahara` | `password123` | Pencairan dana |
| Sarpras Ruangan | `sarpras_ruangan` | `sarpras123` | — |

> Pastikan `config.php` tersedia (salinan `config.example.php`) dan database `db_pengajuan` sudah terimport.

---

## 2. Test Case Alur Verifikasi Berjenjang (Refactor + Status WR3)

| ID | Skenario | Langkah | Data Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---------|---|---|
| TC-01 | Ormawa mengajukan proposal | Login `himatif` → menu **Buat Pengajuan** → isi nama kegiatan, dana, upload PDF → submit | `nama_kegiatan` = "Sinergi Mahasiswa", `dana_diajukan` = 500.000, file PDF | Redirect ke Riwayat dengan `tambah_sukses`; status `Diajukan Ke BEM`; **notifikasi muncul di lonceng BEM** (buka tab BEM) | | |
| TC-02 | Otorisasi tahap salah | BEM login, buka detail proposal dengan status bukan `Diajukan Ke BEM` (mis. yang sudah `Diajukan Ke BPM`) | — | Halaman menampilkan peringatan "tidak memiliki izin / status sudah diproses" (tanpa error 500) | | |
| TC-03 | BEM menyetujui | BEM → **Verifikasi Proposal** → isi catatan opsional → klik **Setujui & Lanjutkan** | — | Status → `Diajukan Ke BPM`; **notif muncul di BPM** | | |
| TC-04 | BPM menyetujui | BPM → verifikasi proposal → Setujui | — | Status → `Verifikasi BKKH`; **notif muncul di BKKH** | | |
| TC-05 | BKKH menyetujui | BKKH → verifikasi → Setujui | — | Status → `Verifikasi WR3`; **notif muncul di WR3** | | |
| TC-06 | **WR3 menyetujui (perbaikan)** | WR3 → verifikasi → Setujui | — | Status → `Disetujui WR3, Siap Diajukan ke Bendahara` (BUKAN langsung `Diajukan ke Bendahara`); histori berisi "Menunggu diteruskan ke Bendahara oleh BKKH"; **notif muncul di BKKH** | | |
| TC-07 | BKKH ajukan pencairan | BKKH → dashboard → tabel **"Siap Diajukan ke Bendahara"** → tombol Ajukan | — | Status → `Diajukan ke Bendahara`; **notif muncul di Bendahara** | | |
| TC-08 | Bendahara menyetujui pencairan | Bendahara → **Proses Pencairan** → isi dana disetujui → Setujui | `dana_disetujui` = 500.000 | Status → `Dana Cair`; **notif dana cair muncul di Ormawa (`himatif`)** | | |
| TC-09 | Ormawa upload LPJ | `himatif` → status `Dana Cair` → **Upload LPJ** (PDF) | file LPJ | Status → `LPJ Diajukan` | | |
| TC-10 | BKKH verifikasi LPJ (setujui) | BKKH → **Verifikasi LPJ** → Setujui | — | Status → `Selesai`; **notif selesai di Ormawa** | | |
| TC-11 | **Penolakan di tahap tengah** | Ulangi TC-01 dari `hima_si`; di tahap BEM klik **Tolak** dengan catatan | catatan wajib | Jika tanpa catatan → error `catatan_kosong`. Dengan catatan → status `Ditolak BEM`; **notif ditolak di `hima_si`** berisi catatan | | |
| TC-12 | Revisi setelah ditolak | `hima_si` → Riwayat → Edit proposal → submit ulang | — | Status kembali → `Diajukan Ke BEM`; histori tercatat | | |

---

## 3. Test Case Notifikasi Realtime (SSE)

Prinsip: loginkan dua peran di **dua tab berbeda**, lakukan aksi di tab A, notifikasi harus muncul di lonceng tab B tanpa reload dalam ±10 detik.

| ID | Skenario | Langkah | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-13 | Notif realtime saat pengajuan baru | Tab A: `bem` login (buka halaman). Tab B: `himatif` submit pengajuan baru | Dalam ±10 detik, badge lonceng di tab A bertambah & dropdown berisi notif "Pengajuan baru… menunggu verifikasi" (tanpa reload) | | |
| TC-14 | Notif saat tahap berpindah | Tab A: `bpm` login. Tab B: `bem` setujui pengajuan | Badge/notif "menunggu verifikasi" muncul realtime di tab A (BPM) | | |
| TC-15 | Badge hilang setelah dibuka | Login `bem`, buka dropdown lonceng | Dropdown menampilkan daftar notif; setelah dibuka, badge berkurang ke 0 (tandai sudah dibaca) | | |
| TC-16 | Notif tidak bocor antar user | Login `himatif` & `hima_si` di dua tab; lakukan aksi hanya di `hima_si` | Tab `himatif` TIDAK menerima notif milik `hima_si` | | |
| TC-17 | Fallback polling | Di DevTools, blok EventSource (`filter: notifikasi_stream`) lalu login & cek dalam 30–35 detik | Notif tetap muncul (fallback polling 30s) | | |

---

## 4. Test Case Endpoint AJAX (JSON)

| ID | Endpoint | Cara Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-18 | `index.php?page=api_notifikasi_belum_baca` | Login lalu buka URL di browser | JSON `{success:true, data:[...], total:N}` (data hanya milik user login) | | |
| TC-19 | `index.php?page=api_kalender_peminjaman` | Login BKKH lalu buka URL | JSON `{success:true, data:[...]}` berisi events `title/start/end/status` (bukan peminjaman `Ditolak`) | | |
| TC-20 | `index.php?page=tandai_notif_terlihat` | Dari dashboard Ormawa yang kebagian `Dana Cair`, panggil POST `{ids:[...]}` | JSON `{success:true}`; SweetAlert dana cair TIDAK muncul lagi setelah reload | | |
| TC-21 | `index.php?page=tandai_notif_baca` | POST `{ids:[id_notif]}` (id dari TC-18) | JSON `{success:true}`; `api_notifikasi_belum_baca` tidak lagi mengembalikan id tsb | | |
| TC-22 | Akses endpoint tanpa login | Buka `api_notifikasi_belum_baca` di tab incognito (belum login) | Dialihkan ke `?page=login` (bukan JSON) | | |

---

## 5. Test Case Proteksi CSRF

| ID | Skenario | Langkah | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-23 | Submit verifikasi tanpa token CSRF | Buka form **Verifikasi Proposal**, hapus hidden `csrf_token` (via DevTools) lalu Submit | Respon **419 "CSRF token tidak valid"** — status TIDAK berubah | | |
| TC-24 | Submit verifikasi dengan token valid | Buka ulang form (token segar) lalu Submit setuju/tolak | Berhasil → redirect dashboard `verifikasi_sukses`, status berubah sesuai tahap | | |
| TC-25 | Token berubah antar sesi | Login sebagai `bem`, ambil token; logout, login lagi, bandingkan token | Token berbeda (regenerasi per sesi); token lama ditolak | | |
| TC-26 | Form verifikasi LPJ | Ulangi TC-23/24 pada form **Verifikasi LPJ** | Tanpa token → 419; dengan token → sukses | | |

---

## 6. Test Case Refactor (Logika di Controller, View Bersih)

| ID | Skenario | Langkah | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-27 | Verifikasi tidak bergantung logika di view | Inspeksi `app/views/verifikator/verifikasi.php` & `verifikasi_lpj.php` | Tidak ada blok `$_SERVER['REQUEST_METHOD']==='POST'` / `UPDATE pengajuan` inline; hanya form + tampilan | | |
| TC-28 | Validasi MIME PDF | Upload file proposal dengan ekstensi `.pdf` tapi isi BUKAN PDF (rename file .txt→.pdf) | Ditolak `bukan_pdf` dari sisi MIME (bukan hanya ekstensi) | | |
| TC-29 | Validasi ekstensi non-PDF | Upload file `.jpg` sebagai proposal | Ditolak `bukan_pdf` | | |

---

## 7. Uji End-to-End (Ringkas)

Ikuti **satu kolak penuh** dari ormawa hingga selesai, pantau status di **Riwayat** Ormawa setiap tahap:

```
Diajukan Ke BEM → Diajukan Ke BPM → Verifikasi BKKH → Verifikasi WR3
→ Disetujui WR3, Siap Diajukan ke Bendahara → Diajukan ke Bendahara
→ Dana Cair → LPJ Diajukan → Selesai
```

| Tahap | Aktor | Status Setelah Tahap | Notif Terkirim Ke | Verifikasi |
|---|---|---|---|---|
| 1 | himatif (ajukan) | `Diajukan Ke BEM` | BEM | ☐ |
| 2 | BEM (setuju) | `Diajukan Ke BPM` | BPM | ☐ |
| 3 | BPM (setuju) | `Verifikasi BKKH` | BKKH | ☐ |
| 4 | BKKH (setuju) | `Verifikasi WR3` | WR3 | ☐ |
| 5 | WR3 (setuju) | `Disetujui WR3, Siap Diajukan ke Bendahara` | BKKH | ☐ |
| 6 | BKKH (ajukan pencairan) | `Diajukan ke Bendahara` | Bendahara | ☐ |
| 7 | Bendahara (setuju) | `Dana Cair` | Ormawa | ☐ |
| 8 | himatif (upload LPJ) | `LPJ Diajukan` | BKKH/WR3 | ☐ |
| 9 | BKKH (setujui LPJ) | `Selesai` | Ormawa | ☐ |

---

## 8. Kesimpulan Uji

| Hasil Total | Jumlah TC |
|---|---|
| ✅ Lulus | |
| ❌ Gagal | |
| ⚠️ Tidak Tereduksi | |

**Catatan / Temuan:** ______________________________________________________________________