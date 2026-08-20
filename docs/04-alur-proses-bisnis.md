# 04 — Alur Proses Bisnis

## 4.0 Konteks Alur

Alur proses bisnis berikut didasarkan pada kondisi nyata yang diperoleh dari hasil wawancara Ormawa (BEM dan BPM) serta BKKH. Seluruh anggota Ormawa (BEM, BPM, HIMA, UKM) dapat mengajukan proposal kegiatan, namun memiliki **fungsi yang hierarkis dan berbeda** dalam alur sistem:

| Entitas | Fungsi dalam Alur |
|---|---|
| **HIMA / UKM** | Pengaju murni — membuat dan mengajukan proposal, memantau status sendiri |
| **BEM** | Pengaju aktif (kegiatan sendiri) + verifikator tahap 1 untuk pengajuan dari HIMA/UKM |
| **BPM** | Pengaju aktif (kegiatan sendiri) + verifikator tahap 2 + pengawas seluruh Ormawa (read-only) |
| **BKKH** | Verifikator tahap 3 + admin sistem + pengelola pencairan |

> **Temuan utama dari wawancara:** Masalah utama yang dihadapi Ormawa **bukan pada proses pengajuan itu sendiri** (yang sudah dinilai cukup sederhana), melainkan pada **monitoring dan komunikasi pasca-pengajuan** — khususnya ketika pengajuan terlambat dan Ormawa tidak mengetahui penyebab atau pihak yang harus dihubungi.

## 4.1 Alur Utama: Pengajuan Dana → Pencairan → LPJ

```
┌─────────────────────────────────────────────────────────────────────────┐
│ 1. ORMAWA — Membuat Pengajuan                                            │
│    • Isi nama kegiatan, dana diajukan, tanggal                           │
│    • Upload file proposal (PDF)                                          │
│    • Sistem cek: status akun aktif + sisa saldo cukup                   │
│    • HIMA/UKM → Status: "Diajukan Ke BEM"                               │
│    • BEM mengajukan sendiri → Status: "Diajukan Ke BPM"                 │
│    • BPM mengajukan sendiri → Status: "Verifikasi BKKH"                 │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 2. BEM — Verifikasi Tahap 1 (hanya untuk pengajuan dari HIMA/UKM)       │
│    • Status "Diajukan Ke BEM"                                            │
│    • SETUJUI → "Diajukan Ke BPM"   |   TOLAK → "Ditolak BEM"            │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 3. BPM — Verifikasi Tahap 2                                              │
│    • Status "Diajukan Ke BPM"                                            │
│    • SETUJUI → "Verifikasi BKKH"   |   TOLAK → "Ditolak BPM"            │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 4. BKKH — Verifikasi Tahap 3                                             │
│    • Status "Verifikasi BKKH"                                            │
│    • SETUJUI → "Verifikasi WR3"   |   TOLAK → "Ditolak BKKH"            │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 5. WR3 — Persetujuan Akhir                                               │
│    • Status "Verifikasi WR3"                                             │
│    • SETUJUI → "Disetujui WR3, Siap Diajukan ke Bendahara"              │
│    • TOLAK → "Ditolak WR3"                                               │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 6. BKKH — Ajukan Pencairan                                               │
│    • BKKH input nomor surat (arsip surat) + meneruskan ke Bendahara      │
│    • Status → "Diajukan ke Bendahara"                                    │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 7. BENDAHARA — Proses Pencairan                                          │
│    • SETUJUI → "Dana Cair" (input dana disetujui)                        │
│    • TOLAK → "Ditolak Bendahara"                                         │
│    • Notifikasi SweetAlert ke ormawa (notif_cair_terlihat)               │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 8. ORMAWA — Upload LPJ                                                   │
│    • Status "Dana Cair" → upload LPJ (PDF)                               │
│    • Status → "LPJ Diajukan"                                             │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 9. BKKH/WR3 — Verifikasi LPJ                                             │
│    • SETUJUI → "LPJ Diverifikasi" → "Selesai"                            │
│    • TOLAK → "LPJ Ditolak BKKH" (revisi LPJ, kembali ke status "LPJ      │
│      Diajukan" setelah upload ulang)                                     │
└─────────────────────────────────────────────────────────────────────────┘
```

> **Catatan — Bypass verifikasi (Pencegahan Self-Approval):** Jika BEM atau BPM mengajukan kegiatannya sendiri, tahap verifikasi yang seharusnya mereka kerjakan dilewati (BEM langsung ke BPM; BPM langsung ke BKKH). Ini adalah **penyesuaian teknis** untuk menghindari self-approval (seseorang menyetujui pengajuannya sendiri), dan **tidak mengubah alur berjenjang yang diwawancarai** (Hima/UKM → BEM → BPM → BKKH → Kemahasiswaan → Bendahara). Sistem harus mengatur bypass ini secara eksplisit berdasarkan peran pengaju.

> **Catatan — Monitoring LPJ oleh BPM:** Pada tahap 8 (Upload LPJ) dan tahap 9 (Verifikasi LPJ), **BPM dapat memonitor status dan dokumen LPJ seluruh Ormawa** secara read-only sebagai bagian dari fungsi pengawasannya. Hal ini mendukung keterkaitan alur *Pengajuan → Persetujuan → Pencairan → Pelaksanaan → LPJ/Pertanggungjawaban* yang menjadi kebutuhan utama BPM.

## 4.2 Alur Penolakan & Revisi

Ketika proposal ditolak di salah satu tahap, ormawa dapat **merevisi** pengajuan. Status baru setelah revisi otomatis kembali ke tahap yang menolak:

| Status saat ini (Ditolak) | Status setelah revisi |
|---------------------------|------------------------|
| `Ditolak BEM` | `Diajukan Ke BEM` |
| `Ditolak BPM` | `Diajukan Ke BPM` |
| `Ditolak BKKH` | `Verifikasi BKKH` |
| `Ditolak WR3` | `Verifikasi WR3` |

> Revisi hanya diizinkan untuk pengajuan berstatus *Ditolak* (dicek `edit.php`).

## 4.3 Logika Blokir Pengajuan Baru (Cek Saldo)

Sebelum ormawa dapat mengajukan dana baru, sistem menghitung:

```
Sisa Saldo = Total Saldo − Saldo Terpakai − Saldo Dalam Proses
```

**Saldo Terpakai** (dana sudah berkomitmen/cair):
- `Disetujui WR3, Siap Diajukan ke Bendahara`
- `Diajukan ke Bendahara`
- `Dana Cair`
- `LPJ Diajukan`
- `LPJ Ditolak BKKH`
- `LPJ Diverifikasi`
- `Selesai`

**Saldo Dalam Proses** (masih diverifikasi):
- `Diajukan Ke BEM`
- `Diajukan Ke BPM`
- `Verifikasi BKKH`
- `Verifikasi WR3`

Jika `dana_diajukan > sisa_saldo` → pengajuan ditolak sistem dengan pesan *"Dana yang diajukan melebihi sisa saldo"*.

## 4.4 Alur Peminjaman Tempat

```
ORMAWA            BKKH                    SARPRAS
  │                 │                        │
  │ ajukan          │                        │
  ├──────────► status: Menunggu BKKH         │
  │                 │                        │
  │                 ├── Verifikasi (status_bkkh) ──► Disetujui → "Menunggu Sarpras"
  │                 │          │ Ditolak     │
  │                 │          ▼             │
  │                 │   status: Ditolak      │
  │                 │                        │
  │                 │                        ├── Verifikasi (status_sarpras)
  │                 │                        │      ├── Disetujui → status: "Disetujui"
  │                 │                        │      └── Ditolak  → status: "Ditolak"
```

## 4.5 Alur Peminjaman Barang

```
ORMAWA            BKKH                    SARPRAS BARANG
  │                 │                        │
  │ ajukan          │                        │
  ├──────────► status_bkkh: Pending          │
  │                 ├── Verifikasi (verifikasi_barang_bkkh)
  │                 │      └── Disetujui     │
  │                 │                        ├── Verifikasi (verifikasi_barang)
  │                 │                        │      └── Disetujui/Ditolak
```

## 4.6 Alur Persuratan Digital

### Buat Proposal Otomatis
1. Ormawa membuka `buat_proposal`.
2. Isi data: nama kegiatan, latar belakang, tujuan, sasaran, penutup.
3. Pilih TTD (dari profil atau kustom + upload file PNG).
4. Input RAB dinamis (rincian, volume, satuan, harga) & panitia.
5. Simpan sebagai **Draft** atau **Final**.
6. Lihat/print hasil via `view_proposal` / `cetak_surat` → QR code verifikasi.

### Surat Persetujuan + QR Code
1. Proposal yang sudah disetujui (status final) diberi **nomor surat** oleh BKKH.
2. Sistem generate surat balasan resmi dengan kop dinamis (`konfigurasi`) + rekam jejak persetujuan.
3. QR code berisi URL `verify_page?id=...&verify=<unique_code>`.
4. Siapa pun dapat memverifikasi keaslian surat melalui halaman publik.

### Surat Lain
1. Ormawa memilih jenis surat (undangan, permohonan, dll).
2. Mengisi nomor, perihal, isi, penerima, TTD (kustom/upload).
3. Simpan & cetak/arsip.

## 4.7 Alur Aspirasi Publik

```
PENGUNJUNG (publik)          BPM
      │                       │
      │ submit aspirasi       │
      ├─────────────► tabel aspirasi (status: baru)
      │                       ├── Baca aspirasi (manage_aspirasi)
      │                       ├── Isi tanggapan + ubah status
      │                       └── Simpan → aspirasi tertanggapi
```

## 4.8 Alur Berita, Jadwal Rapat, Regulasi

- **Berita (BEM):** BEM tambah/hapus pengumuman (judul, isi, lampiran) → tampil di Pusat Informasi untuk semua role.
- **Jadwal Rapat:** BEM/BPM tambah rapat (judul, tanggal, jam, lokasi, link, peserta) → tampil di daftar/kalender untuk semua role.
- **Regulasi (BPM):** BPM tambah regulasi (judul, kategori, file) → tampil di Pusat Informasi.

## 4.9 Alur Manajemen Pengguna & Saldo

```
BKKH
 ├── Tambah user (nama, username, password, role)
 ├── Edit user (ubah nama/username/password/role)
 ├── Toggle status akun (aktif/nonaktif) — mengaktifkan/nonaktifkan menu pengajuan
 └── Atur saldo (set nominal saldo ormawa) → tercermin di dashboard & cek saldo
```

## 4.10 Alur Atur Sistem (Kop Surat & Logo)

```
BKKH
 ├── Upload logo sistem (sidebar & login)
 ├── Upload logo kop surat
 ├── Edit nama aplikasi
 └── Edit teks kop (baris 1-4: institusi, unit, alamat, telepon/email)
      → disimpan di tabel `konfigurasi`, dipakai di cetak surat & view dokumen
```

## 4.11 Ringkasan Status → Aksi

| Status | Pemilik aksi | Aksi berikutnya |
|--------|--------------|-----------------|
| `Draft` | Ormawa | Lanjutkan/publikasikan |
| `Diajukan Ke BEM` | BEM | Setujui → BPM / Tolak |
| `Diajukan Ke BPM` | BPM | Setujui → BKKH / Tolak |
| `Verifikasi BKKH` | BKKH | Setujui → WR3 / Tolak |
| `Verifikasi WR3` | WR3 | Setujui → Siap Diajukan / Tolak |
| `Disetujui WR3, Siap Diajukan ke Bendahara` | BKKH | Ajukan pencairan ke Bendahara |
| `Diajukan ke Bendahara` | Bendahara | Setujui → Dana Cair / Tolak |
| `Dana Cair` | Ormawa | Upload LPJ |
| `LPJ Diajukan` | BKKH/WR3 | Setujui → Selesai / Tolak (revisi) |
| `LPJ Ditolak BKKH` | Ormawa | Revisi LPJ (upload ulang) |
| `LPJ Diverifikasi` / `Selesai` | — | Siklus selesai |

## 4.12 Alur Monitoring Program Kerja Tahunan *(Kebutuhan Baru — Fungsi Khusus BPM)*

> Fitur ini belum tersedia di sistem existing. Diidentifikasi sebagai kebutuhan dari wawancara BPM selaku lembaga pengawas. BEM dan HIMA/UKM berperan sebagai pihak yang menginput dan memperbarui data proker mereka masing-masing.

```
ORMAWA (HIMA/UKM/BEM/BPM) — Input Rencana Program Kerja
  • Nama program kerja
  • Tujuan / manfaat kegiatan
  • Rencana tanggal pelaksanaan
  • Detail kegiatan
  • Informasi pendukung lainnya
        │
        ▼
SISTEM — Menyimpan data proker per Ormawa
        │
        ▼
ORMAWA — Perbarui Status Proker (berkala)
  • Tandai status tiap program kerja:
      ├── Terlaksana
      ├── Sedang berjalan
      ├── Mengalami kendala
      └── Belum terlaksana
        │
        ▼
BPM — Monitoring & Tindak Lanjut (tiap 3–6 bulan)
  • Melihat rekapitulasi program kerja seluruh Ormawa (read-only)
  • Memberikan catatan atau tindak lanjut apabila ada kendala
  • BKKH dapat mengakses data yang sama untuk keperluan administrasi
```

**Tujuan fitur:**
- Mendokumentasikan rencana kegiatan Ormawa selama satu tahun secara terpusat
- Memudahkan **BPM** memantau progres pelaksanaan program kerja seluruh Ormawa tanpa harus meminta laporan secara manual
- Menjadi dasar evaluasi berkala BPM terhadap kinerja dan aktifitas Ormawa

## 4.13 Alur Aspirasi Terpusat *(Kebutuhan Pengembangan — Dikelola BPM)*

> Kondisi saat ini: aspirasi hanya dapat diajukan oleh publik (tanpa login) dan hanya dikelola BPM. Berdasarkan wawancara BKKH, dibutuhkan satu wadah terpusat yang menggantikan berbagai jalur informal. **Pengelolaan aspirasi adalah fungsi khusus BPM** — bukan BEM.

**Kondisi existing (4.7):**
```
PENGUNJUNG (publik) → submit aspirasi → BPM kelola & tanggapi
```

**Kondisi yang diinginkan:**
```
Semua jalur aspirasi (WhatsApp, datang langsung, Prodi, dosen wali)
        │
        ▼ (digantikan oleh)
SKIN — Form Aspirasi Terpusat
  • Aspirasi masuk ke satu sistem
  • Ditandai pihak yang bertanggung jawab menanggapi
  • Status aspirasi dapat dipantau pengaju
        │
        ▼
BKKH / BPM — Kelola & Tanggapi
  • Terima, proses, dan tanggapi aspirasi
  • Dokumentasi terpusat dan mudah ditelusuri
```

## 4.14 Alur Notifikasi Realtime *(Kebutuhan Pengembangan dari Wawancara)*

> Berdasarkan hasil wawancara (BEM, BPM, BKKH) dan kuesioner mahasiswa, notifikasi perubahan status adalah kebutuhan prioritas tinggi. Tujuan utamanya agar pengguna **tidak perlu terus-menerus membuka sistem** hanya untuk mengetahui apakah ada pengajuan baru atau perubahan status.

### Pendekatan Teknis: Tanpa Perlu Ganti Stack

Notifikasi realtime **dapat diimplementasikan tanpa mengganti stack** (tetap PHP 8 + Apache + MySQL). Pendekatan utama yang direkomendasikan:

| Pendekatan | Deskripsi | Realtime | Keterangan |
|---|---|---|---|
| **SSE (Server-Sent Events)** | Server push satu arah via endpoint PHP dengan header `text/event-stream` | ✅ Push milidetik | Rekomendasi utama; tidak butuh library/Node, memanfaatkan tabel `notifikasi` yang sudah ada |
| **Short polling (Ajax 10–30 detik)** | Frontend mengecek endpoint JSON secara berkala | ⚠️ ± 10–30 detik | Fallback paling aman untuk shared hosting |

Tabel `notifikasi` (kolom `id_notif`, `id_user`, `pesan`, `status_baca`, `waktu`) sudah tersedia di skema `db_pengajuan` dan siap digunakan sebagai sumber data notifikasi.

### Pemicu Notifikasi (Event → Penerima)

| Event | Penerima Notifikasi |
|---|---|
| HIMA/UKM mengajukan proposal | BEM (tugas verifikasi masuk) |
| BEM menyetujui proposal | BPM (tugas verifikasi masuk) |
| BPM menyetujui proposal | BKKH (tugas verifikasi masuk) |
| BKKH menyetujui proposal | WR3 (tugas persetujuan masuk) |
| WR3 menyetujui final | BKKH dan Ormawa pengaju |
| Pengajuan ditolak (setiap tahap) | Ormawa pengaju (dengan catatan revisi) |
| BKKH mengajukan pencairan | Bendahara |
| Dana cair | Ormawa pengaju |
| LPJ diupload oleh Ormawa | BKKH/WR3 (verifikasi LPJ) |
| LPJ diverifikasi selesai | Ormawa pengaju + BPM (read-only monitoring) |
| Peminjaman fasilitas disetujui/ditolak | Ormawa pengaju |
| Aspirasi baru masuk | BPM |

### Alur Kerja SSE

```
PERISTIWA (verifikasi, pencairan, upload, dll)
        │
        ▼
SISTEM — INSERT ke tabel `notifikasi` (id_user, pesan)
        │
        ▼
SSE ENDPOINT (index.php?page=notifikasi_stream)
  • Header: Content-Type: text/event-stream, Cache-Control: no-cache
  • Loop: query notifikasi belum dibaca user tsb
  • Kirim event → frontend
        │
        ▼
FRONTEND (EventSource / polling fallback)
  • Lonceng notifikasi + badge jumlah belum dibaca
  • Toast/SweetAlert muncul realtime saat ada notif baru
  • Klik → buka Pusat Notifikasi / tandai sudah dibaca
```

**Catatan implementasi (untuk tahap pengkodean):**
- Perbaiki route `tandai_notif_terlihat` yang saat ini **belum terdaftar** di `Router.php` (dead route — JS di `dashboard.php` memanggilnya tapi tidak pernah dieksekusi).
- Migrasikan notifikasi "Dana Cair" yang saat ini memakai flag `notif_cair_terlihat` di tabel `pengajuan` ke tabel `notifikasi` agar seragam.
- Lonceng notifikasi dan polling/EventSource cukup dipasang sekali di `app/views/layouts/header.php` agar berlaku di seluruh halaman, bukan hanya dashboard.
