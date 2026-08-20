# Gambaran Sistem SKIN — Ringkasan Hasil Wawancara Stakeholder

**Proyek:** Pengembangan Sistem Kemahasiswaan (SKIN) — Kerja Praktik  
**Institusi:** Institut Teknologi Garut  
**Periode Wawancara:** 13–20 Agustus 2026  
**Sumber Data:** Wawancara BEM, Wawancara BKKH, Wawancara BPM, Kuesioner Mahasiswa, Bimbingan DPL

---

## 1. Pendahuluan

Dokumen ini merupakan ringkasan dan sintesis dari seluruh hasil wawancara serta pengumpulan data yang telah dilakukan terhadap stakeholder dalam pengembangan Sistem Kemahasiswaan (SKIN) di Institut Teknologi Garut, yaitu:

| Stakeholder | Narasumber | Tanggal |
|---|---|---|
| BKKH (Biro Ketenagaan, Kemahasiswaan, dan Humas) | Bapak Encep Jianul Hayat S.T., M.T. | 13 Agustus 2026 |
| Ormawa — BPM (Badan Perwakilan Mahasiswa) | Kang Raikhan (Ketua BPM) | 14 Agustus 2026 |
| Ormawa — BEM (Badan Eksekutif Mahasiswa) | Kang Iqbal (Ketua BEM) | 18 Agustus 2026 |
| Mahasiswa Umum *(Pending — status aktor belum ditetapkan)* | 11 responden lintas program studi (TI: 6, SI: 3, Arsitektur: 1, Teknik Sipil: 1) | Agustus 2026 |

Tujuan dokumen ini adalah memberikan **gambaran menyeluruh tentang kondisi sistem, permasalahan yang ada, dan kebutuhan yang perlu diakomodasi** sebagai dasar pengembangan SKIN.

> **Catatan struktur Ormawa:** BEM, BPM, HIMA, dan UKM semuanya termasuk kelompok **Ormawa**, namun memiliki fungsi yang hierarkis dan berbeda dalam sistem:
> - **HIMA & UKM** — Pengaju murni: mengajukan proposal, meminjam fasilitas, upload LPJ, memantau status pengajuan sendiri
> - **BEM** — Pengaju aktif (kegiatan sendiri) sekaligus **Verifikator**: menyetujui/menolak pengajuan dari HIMA/UKM sebelum naik ke BPM
> - **BPM** — Pengaju aktif (kegiatan sendiri) sekaligus **Pengawas**: memantau seluruh program kerja, keuangan, dan LPJ Ormawa secara read-only; mengelola aspirasi dan regulasi
>
> Perbedaan akses yang signifikan hanya berlaku antara kelompok Ormawa dengan **BKKH** selaku admin/pengelola sistem.
>
> **Mahasiswa umum** saat ini berstatus **pending** — belum ditetapkan apakah akan menjadi aktor aktif dalam sistem atau diposisikan sebagai opsi pengembangan lanjutan.

---

## 2. Kondisi Sistem SKIN Saat Ini

### 2.1 Status Sistem

SKIN adalah sistem yang telah dikembangkan sebelumnya oleh mahasiswa lain dan **tidak dibangun dari awal**. Sistem sudah berjalan dan digunakan, khususnya untuk proses keuangan dan pengajuan proposal organisasi mahasiswa.

Tugas kelompok KP difokuskan pada:
- Mempelajari dan memahami sistem existing
- Menemukan dan memperbaiki bug
- Menambahkan fitur yang belum tersedia
- Melakukan deployment/hosting
- Menyempurnakan tampilan dan fungsionalitas

### 2.2 Fitur yang Sudah Berjalan

- Pengajuan proposal kegiatan Ormawa
- Pengajuan dan pengelolaan dana kegiatan
- Monitoring status pengajuan (multi-tahap persetujuan)
- Administrasi keuangan organisasi mahasiswa

### 2.3 Permasalahan pada Sistem Existing

| No. | Permasalahan | Sumber |
|---|---|---|
| 1 | Tidak ada mekanisme follow-up atau komunikasi saat pengajuan terlambat | Ormawa (BEM) |
| 2 | Status pengajuan kurang informatif setelah memasuki tahap persetujuan | Ormawa (BEM) |
| 3 | Proses penggunaan fasilitas (ruangan, barang, lapangan) cukup panjang dan berpotensi bentrok jadwal | Ormawa, BKKH |
| 4 | Penyampaian aspirasi mahasiswa tersebar melalui berbagai jalur (WhatsApp, datang langsung, Prodi, dosen wali) | BKKH |
| 5 | Dokumentasi kegiatan mahasiswa jangka panjang sulit ditemukan kembali | BKKH |
| 6 | Pengelolaan dokumen (proposal, surat, daftar hadir) belum terpusat | BKKH |
| 7 | ~82% (9 dari 11) mahasiswa umum belum pernah menggunakan SKIN sama sekali | Kuesioner *(Pending)* |
| 8 | ~45% (5 dari 11) mahasiswa hanya pernah mendengar SKIN tetapi tidak tahu fungsinya | Kuesioner *(Pending)* |

---

## 3. Alur Proses Bisnis Pengajuan

### 3.1 Alur Pengajuan Dana dan Kegiatan

Berdasarkan hasil wawancara Ormawa, alur pengajuan kegiatan melibatkan banyak pihak secara berjenjang:

```
Hima/UKM → BEM → BPM → BKKH (Pak Encep) → Kemahasiswaan → Wakil Rektor III → Bendahara
```

Setiap tahapan membutuhkan verifikasi atau persetujuan dari pihak terkait. Hal ini menyebabkan proses membutuhkan waktu yang cukup panjang, terutama ketika dana dibutuhkan dalam waktu dekat.

### 3.2 Alur Pertanggungjawaban Kegiatan

```
Pengajuan → Persetujuan → Pencairan Dana → Pelaksanaan Kegiatan → LPJ / Pertanggungjawaban
```

Ormawa membutuhkan visibilitas di seluruh rantai ini, sedangkan BKKH membutuhkan akses pengelolaan dan dokumentasi di setiap tahapnya.

### 3.3 Jalur Penyampaian Aspirasi (Existing)

Saat ini aspirasi mahasiswa dapat masuk melalui berbagai jalur yang tidak terpusat:
- Kemahasiswaan (langsung)
- Program Studi / Dosen Wali
- Pengurus BEM / HIMA / UKM
- Grup WhatsApp tidak resmi

Kondisi ini menyebabkan informasi berpotensi tercecer dan tidak tertangani secara sistematis.

---

## 4. Kebutuhan per Stakeholder

### 4.1 Kebutuhan BKKH

BKKH berperan sebagai pengelola dan admin sistem. Seluruh kebutuhan berikut merupakan bagian dari fungsi pengelolaan dan pengawasan kemahasiswaan secara menyeluruh:

| No. | Kebutuhan | Keterangan |
|---|---|---|
| 1 | Pengelolaan keuangan Ormawa | Sudah berjalan, perlu dipertahankan dan disempurnakan |
| 2 | Pengajuan kompetisi dan lomba | Ormawa dapat melaporkan keikutsertaan kompetisi |
| 3 | Pelaporan prestasi mahasiswa | Dokumentasi prestasi terpusat |
| 4 | Penyampaian aspirasi | Satu wadah terpusat menggantikan berbagai jalur yang ada |
| 5 | Peminjaman fasilitas | Ruangan, barang, lapangan, kendaraan, dll. |
| 6 | Pengajuan proposal dan administrasi kegiatan | Pengelolaan dokumen terpusat |
| 7 | Dokumentasi kegiatan mahasiswa | Arsip jangka panjang yang mudah ditelusuri |
| 8 | Pengelolaan dokumen terpusat | Proposal, surat, daftar hadir, dan dokumentasi kegiatan dikelola satu pusat — justifikasi fitur Persuratan Digital & Arsip |

> **Catatan BKKH:** Integrasi dengan sistem kampus lain (seperti data NIM) belum menjadi prioritas utama. SKIN sementara berdiri sebagai sistem mandiri, dengan opsi input data manual.

### 4.2 Kebutuhan Ormawa

Seluruh anggota Ormawa (HIMA, UKM, BEM, BPM) berbagi kebutuhan dasar yang sama sebagai organisasi yang mengajukan kegiatan. Di atas itu, BEM dan BPM memiliki kebutuhan tambahan sesuai fungsinya masing-masing.

#### 4.2a HIMA & UKM — Pengaju Tingkat Dasar

| No. | Kebutuhan | Tujuan |
|---|---|---|
| 1 | Pengajuan proposal kegiatan | Mengajukan proposal dengan RAB dan dokumen pendukung |
| 2 | Monitoring status pengajuan | Mengetahui posisi dan perkembangan pengajuan di setiap tahap verifikasi |
| 3 | Follow-up dan komunikasi pengajuan | Saluran tindak lanjut saat pengajuan terlambat atau mengalami kendala |
| 4 | Upload dan pelaporan LPJ | Melaporkan pertanggungjawaban kegiatan setelah dana cair |
| 5 | Peminjaman fasilitas | Pengajuan ruangan, barang, dan fasilitas kampus dengan informasi ketersediaan |
| 6 | Notifikasi perubahan status | Pemberitahuan otomatis saat ada perubahan status pengajuan |
| 7 | Persuratan digital | Pembuatan proposal, surat, dan LPJ secara otomatis di dalam sistem |

#### 4.2b BEM — Pengaju Aktif + Verifikator Internal

> BEM memiliki semua kebutuhan HIMA/UKM di atas, ditambah kebutuhan berikut sebagai koordinator dan verifikator pengajuan dari HIMA/UKM.

| No. | Kebutuhan Tambahan | Tujuan |
|---|---|---|
| 1 | Panel verifikasi pengajuan HIMA/UKM | Menyetujui atau menolak pengajuan dari HIMA/UKM sebelum naik ke BPM |
| 2 | Visibilitas pengajuan seluruh Ormawa di bawahnya | Mengetahui daftar dan status pengajuan yang masuk untuk diverifikasi |
| 3 | Publikasi informasi dan berita | Menyebarkan informasi/pengumuman kepada seluruh Ormawa melalui sistem |
| 4 | Transparansi kegiatan Ormawa | Menampilkan informasi kegiatan yang telah atau sedang dilaksanakan |
| 5 | Role dan hak akses yang sesuai struktur | Menyesuaikan hak akses dengan kepengurusan aktual BEM (misal: Seskab) |

#### 4.2c BPM — Pengaju Aktif + Pengawas

> BPM memiliki semua kebutuhan HIMA/UKM di atas, ditambah kebutuhan berikut sebagai lembaga legislatif dan pengawas seluruh Ormawa.

| No. | Kebutuhan Tambahan | Tujuan |
|---|---|---|
| 1 | Monitoring program kerja seluruh Ormawa | Memantau pelaksanaan kegiatan Ormawa tanpa meminta laporan manual |
| 2 | Perencanaan program kerja tahunan | Mendokumentasikan rencana kegiatan satu tahun; dasar monitoring berkala tiap 3–6 bulan |
| 3 | Monitoring keuangan Ormawa (read-only) | Transparansi penggunaan anggaran; BPM tidak berwenang mengubah data keuangan |
| 4 | Monitoring LPJ / pertanggungjawaban | Memantau pertanggungjawaban kegiatan dan penggunaan dana seluruh Ormawa |
| 5 | Notifikasi pengajuan & perubahan status | Pemberitahuan otomatis (realtime) saat ada pengajuan baru atau perubahan status proses tanpa harus membuka sistem terus-menerus |
| 6 | Kelola aspirasi | Menerima, memproses, dan menanggapi aspirasi yang masuk ke sistem |
| 7 | Kelola regulasi | Mengelola dokumen regulasi yang berlaku bagi Ormawa |
| 8 | Surat peringatan | Membuat dan mengirimkan surat peringatan kepada Ormawa yang melanggar |
| 9 | Papan informasi | Memusatkan informasi kegiatan dan organisasi |

> **Catatan Ormawa:** Fitur pengajuan proposal yang sudah ada dinilai **sudah cukup sederhana dan bisa digunakan**. Masalah utama justru muncul **setelah pengajuan**, pada tahap monitoring dan komunikasi.

### 4.3 Kebutuhan Mahasiswa Umum *(Pending — Status Aktor Belum Ditetapkan)*

> Data berikut diperoleh dari kuesioner terhadap 11 responden lintas program studi (TI: 6, SI: 3, Arsitektur: 1, Teknik Sipil: 1). Belum ada responden dari Teknik Industri. **Mahasiswa umum saat ini belum ditetapkan sebagai aktor aktif dalam sistem** — kebutuhan ini dicatat sebagai referensi untuk opsi pengembangan lanjutan.

| No. | Kebutuhan | Tujuan / Keterangan |
|---|---|---|
| 1 | Pusat Informasi Terpadu | Kalender kegiatan, prosedur layanan, ketersediaan fasilitas — tidak lagi tersebar di grup WhatsApp |
| 2 | Monitoring Status Pengajuan | Fitur tracking progres pengajuan (proposal/dana/surat) secara transparan beserta pihak yang menangani |
| 3 | Penyampaian Aspirasi | Wadah khusus untuk menyampaikan kritik, saran, dan keluhan secara terarah |
| 4 | Peminjaman Fasilitas | Reservasi/pengajuan peminjaman fasilitas dan ruangan kampus secara online |
| 5 | Sistem Notifikasi | Pemberitahuan status pengajuan dan informasi kemahasiswaan secara berkala |

**Data statistik kuesioner:**
- ~82% (9 responden) belum pernah menggunakan SKIN
- ~45% (5 responden) sering mengalami kesulitan; ~45% (5 responden) kadang-kadang
- 73% (8 responden) menyatakan fitur tracking status pengajuan bersifat **"Sangat Penting"**
- 73% (8 responden: 5 "Bersedia" + 3 "Sangat Bersedia") bersedia menggunakan SKIN secara aktif jika dikembangkan

**Kendala utama yang dialami mahasiswa:**
1. Informasi kurang lengkap — 7 responden
2. Tidak mengetahui pihak yang harus dihubungi — 7 responden
3. Proses pengajuan membutuhkan waktu lama — 6 responden
4. Tidak mengetahui prosedur yang harus dilakukan — 4 responden
5. Sulit mengetahui status pengajuan — 3 responden

---

## 5. Konsolidasi Kebutuhan Lintas Stakeholder

Berikut adalah fitur-fitur yang muncul dari hasil wawancara, diurutkan berdasarkan tingkat kepentingan lintas stakeholder:

| No. | Fitur | HIMA/UKM | BEM | BPM | BKKH | Mahasiswa *(Pending)* | Prioritas |
|---|---|:---:|:---:|:---:|:---:|:---:|---|
| 1 | Monitoring & tracking status pengajuan | ✓ | ✓ | ✓ | ✓ | ✓ | **Sangat Tinggi** |
| 2 | Peminjaman fasilitas terpusat | ✓ | ✓ | ✓ | ✓ | ✓ | **Sangat Tinggi** |
| 3 | Follow-up dan komunikasi pengajuan | ✓ | ✓ | ✓ | ✓ | — | **Tinggi** |
| 4 | Notifikasi realtime perubahan status | ✓ | ✓ | ✓ | — | ✓ | **Tinggi** |
| 5 | Transparansi kegiatan Ormawa | — | ✓ | ✓ | ✓ | ✓ | **Tinggi** |
| 6 | Verifikasi/approval pengajuan HIMA/UKM | — | ✓ | — | ✓ | — | **Tinggi** |
| 7 | Perencanaan & monitoring program kerja tahunan | — | — | ✓ | ✓ | — | **Tinggi** |
| 8 | Aspirasi terpusat | — | — | ✓ | ✓ | ✓ | **Tinggi** |
| 9 | Monitoring keuangan Ormawa (read-only BPM) | — | — | ✓ | ✓ | — | **Menengah** |
| 10 | Monitoring LPJ / pertanggungjawaban | — | — | ✓ | ✓ | — | **Menengah** |
| 11 | Pengelolaan dokumen administrasi terpusat | ✓ | ✓ | ✓ | ✓ | — | **Menengah** |
| 12 | Pelaporan prestasi dan kompetisi | — | — | — | ✓ | — | **Menengah** |
| 13 | Role dan hak akses sesuai struktur kepengurusan | ✓ | ✓ | ✓ | ✓ | — | **Menengah** |

### Fitur yang Tidak Perlu Diulang (Sudah Berjalan)

- Pengajuan proposal kegiatan (Ormawa menilai sudah cukup)
- Pengelolaan keuangan Ormawa (BKKH menyatakan perlu dipertahankan)

---

## 6. Gambaran Arah Pengembangan

### 6.1 Prinsip Pengembangan

Berdasarkan arahan BKKH dan DPL, pengembangan SKIN mengikuti prinsip berikut:

1. **Bukan sistem baru** — SKIN dikembangkan dari sistem existing, bukan dibangun dari nol
2. **Evaluate first** — Evaluasi fitur yang sudah ada sebelum menambahkan fitur baru
3. **Gap-based development** — Pengembangan berdasarkan gap antara sistem existing dan kebutuhan stakeholder
4. **Incremental** — Prioritaskan kebutuhan BKKH dan Ormawa terlebih dahulu; keterlibatan Prodi melalui **FGD di tahap berikutnya**. Dalam FGD tersebut, tim harus **mempresentasikan sistem yang telah dikembangkan** dan meminta masukan terkait: (a) kebutuhan akses bagi Prodi, (b) informasi yang perlu dilihat Prodi, (c) fitur yang dibutuhkan Prodi, serta (d) apakah Prodi cukup menerima laporan dari BKKH atau memerlukan akses langsung ke sistem
5. **Standalone system** — Integrasi dengan sistem kampus lain belum menjadi fokus utama

### 6.2 Hierarki Role Pengguna

| Role | Tipe | Akses Utama |
|---|---|---|
| BKKH / Admin | Pengelola sistem | Pengelolaan penuh: keuangan, administrasi, persetujuan, fasilitas, aspirasi, dokumentasi |
| BPM | Ormawa — Pengawas | Pengajuan kegiatan sendiri + monitoring seluruh Ormawa (proker, keuangan read-only, LPJ) + kelola aspirasi & regulasi |
| BEM | Ormawa — Koordinator | Pengajuan kegiatan sendiri + verifikasi/approval pengajuan HIMA/UKM + publikasi informasi |
| HIMA / UKM | Ormawa — Pengaju | Pengajuan proposal, peminjaman fasilitas, upload LPJ, monitoring status pengajuan sendiri |
| Mahasiswa *(Pending)* | Belum ditetapkan | *(Kandidat: aspirasi, informasi kegiatan, peminjaman fasilitas)* |

### 6.3 Ringkasan Roadmap Pengembangan

| Tahap | Kegiatan | Perkiraan Waktu | Output Utama |
|---|---|---|---|
| 1 | Persiapan & Pemahaman Sistem | Minggu 1 | Dokumentasi sistem existing, daftar role & fitur |
| 2 | Analisis Kebutuhan | Minggu 1–2 | Requirement, Gap Analysis, Business Process |
| 3 | Perancangan Pengembangan | Minggu 2 | Development plan, rancangan UI/UX & database |
| 4 | Implementasi & Pengembangan | Minggu 2–3 | Fitur baru, perbaikan bug |
| 5 | Integration & Testing | Minggu 3 | Test case, daftar bug, sistem siap evaluasi |
| 6 | Evaluasi & Presentasi | Minggu 4 | Feedback stakeholder, daftar revisi |
| 7 | Deployment / Hosting | Setelah siap | Sistem berjalan di production |
| 8 | User Acceptance Testing (UAT) | Setelah deployment | Hasil UAT, feedback pengguna nyata |
| 9 | Finalisasi & Dokumentasi | Tahap akhir | Sistem SKIN versi final, user guide, laporan KP |

> Urutan dan durasi setiap tahap dapat disesuaikan berdasarkan kondisi sistem existing, hasil wawancara stakeholder, dan evaluasi dari pembimbing lapangan.

---

## 7. Kesimpulan

Berdasarkan seluruh hasil wawancara, berikut poin-poin utama yang menjadi gambaran sistem SKIN yang akan dikembangkan:

### Masalah Inti

> Sistem SKIN saat ini sudah memiliki fondasi yang cukup untuk proses pengajuan, namun **belum memadai sebagai pusat layanan kemahasiswaan terpadu**. Masalah utama bukan pada proses pengajuan itu sendiri, melainkan pada **monitoring, komunikasi, transparansi, dan sentralisasi informasi** pasca-pengajuan.

### Tiga Fokus Pengembangan Utama

1. **Monitoring & Transparansi** — BKKH dan seluruh Ormawa membutuhkan visibilitas yang lebih baik terhadap status pengajuan, kegiatan, program kerja, dan penggunaan anggaran.

2. **Sentralisasi Layanan** — Peminjaman fasilitas, pelaporan prestasi, aspirasi, dan administrasi surat-menyurat perlu dipusatkan dalam satu sistem agar tidak tersebar di berbagai jalur informal.

3. **Komunikasi Sistemik** — Diperlukan mekanisme notifikasi dan follow-up di dalam sistem sehingga pengguna tidak perlu bergantung pada komunikasi manual di luar sistem.

### Catatan Penting

- Seluruh Ormawa (BEM, BPM, HIMA, UKM) berada dalam satu kelompok, namun memiliki fungsi yang **hierarkis dan berbeda**: HIMA/UKM sebagai pengaju, BEM sebagai koordinator & verifikator, BPM sebagai pengawas & legislatif.
- BPM tetap dapat mengajukan proposal kegiatan sendiri seperti Ormawa lainnya — fungsi pengawasannya bersifat tambahan, bukan pengganti.
- **Status mahasiswa umum sebagai aktor sistem masih pending** — kebutuhan dari kuesioner mahasiswa dicatat sebagai referensi untuk opsi pengembangan lanjutan setelah kebutuhan utama Ormawa dan BKKH terpenuhi.
- Responden kuesioner mahasiswa belum mencakup Teknik Industri — perwakilan Teknik Sipil sudah masuk; data dapat diperbarui apabila tersedia.
- Keterlibatan 5 Program Studi direncanakan melalui FGD setelah sistem memiliki bentuk yang lebih siap.
- Setiap candidate requirement perlu dibandingkan kembali dengan fitur SKIN existing sebelum ditetapkan sebagai fitur yang akan dikembangkan.

---

*Dokumen ini disusun berdasarkan: Wawancara BEM (18 Agustus 2026), Wawancara BKKH (13 Agustus 2026), Wawancara BPM (14 Agustus 2026), Kuesioner Kebutuhan Mahasiswa, Bimbingan DPL (28 Juli 2026), dan Roadmap Pengembangan Sistem SKIN.*
