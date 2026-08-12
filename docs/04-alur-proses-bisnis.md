# 04 — Alur Proses Bisnis

## 4.1 Alur Utama: Pengajuan Dana → Pencairan → LPJ

```
┌─────────────────────────────────────────────────────────────────────────┐
│ 1. ORMAWA — Membuat Pengajuan                                            │
│    • Isi nama kegiatan, dana diajukan, tanggal                           │
│    • Upload file proposal (PDF)                                          │
│    • Sistem cek: status akun aktif + sisa saldo cukup                   │
│    • Status → "Diajukan Ke BEM" (atau "Verifikasi BKKH" jika BEM/BPM)   │
└──────────────────────────────┬──────────────────────────────────────────┘
                               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ 2. BEM — Verifikasi Tahap 1                                              │
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
