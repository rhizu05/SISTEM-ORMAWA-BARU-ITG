# E2E Traceability — PRD SKIN v2.0 → Playwright

Dokumen ini memetakan setiap requirement pada `docs/prd/PRD_SKIN_Final_Konsolidasi.md`
ke file/spec Playwright di folder `e2e/`. Tujuan: memastikan setiap requirement memiliki
bukti uji yang dapat ditelusuri (Bagian D – Requirement Traceability Matrix).

Struktur spec diorganisasi per **grup requirement PRD** (FR / SEC / NFR / UI / BR),
menggantikan struktur lama berbasis fitur. Nama file menggunakan prefix nomor urut
sesuai alur (auth → dashboard → pengajuan → verifikasi → … → NFR).

---

## 1. Peta File Spec

| File | Requirement utama |
|------|-------------------|
| `01-auth-rbac.spec.ts` | FR-001, FR-002 (akses), SEC-04, UI-020, BR-16 |
| `02-dashboard-role.spec.ts` | FR-003, FR-004, UI-001…UI-008, UI-020 |
| `03-pengajuan.spec.ts` | FR-007, FR-010, BR-02, BR-03, BR-04, BR-13, UI-010, UI-015, UI-017, UI-021 |
| `04-verifikasi-workflow.spec.ts` | FR-009, FR-012, BR-05, BR-06, BR-11, BR-13, BR-14, UI-015, SEC-04 |
| `05-pencairan.spec.ts` | FR-013, FR-024, BR-06, BR-11, UI-006 |
| `06-keuangan-bpm.spec.ts` | FR-014, BR-07, SEC-04 |
| `07-generator-dokumen.spec.ts` | FR-008 (generator + unduh PDF DomPDF) |
| `08-sarpras-peminjaman.spec.ts` | FR-017, FR-018 (kalender interaktif), FR-019, BR-09, BR-10, BR-12 |
| `09-proker.spec.ts` | FR-005, FR-006, BR-15 |
| `10-bpm.spec.ts` | FR-004, FR-006, FR-015, FR-021 |
| `11-bkhm.spec.ts` | FR-002, FR-004, BR-16, UI-020 |
| `12-aspirasi-regulasi.spec.ts` | FR-015, FR-016, FR-021, BR-08, SEC-05, UI-012 |
| `13-prestasi.spec.ts` | FR-020 |
| `14-notifikasi.spec.ts` | FR-022, FR-025, UI-011 (badge belum dibaca) |
| `15-komunikasi.spec.ts` | FR-011 |
| `16-keamanan-nfr.spec.ts` | SEC-01, SEC-02, SEC-03, SEC-06, NFR-02, NFR-05, NFR-06 |
| `17-ui-ux.spec.ts` | UI-014, UI-016, UI-018, UI-019, UI-021, NFR-01, NFR-03 |

Helper: `helpers/auth.ts` (akun & login), `helpers/assertions.ts` (assertion lokal Indonesia).
Fixture: `fixtures/dummy.pdf`, `fixtures/dummy.png`.

---

## 2. Functional Requirements (FR)

| ID | Requirement | Spec | Status |
|----|-------------|------|--------|
| FR-001 | Login Pengguna | `01-auth-rbac` | ✅ |
| FR-002 | Manajemen User (CRUD) | `11-bkhm` | ✅ |
| FR-003 | Dashboard Ormawa | `02-dashboard-role` | ✅ |
| FR-004 | Dashboard Eksekutif (WR3 & BKHM) | `02-dashboard-role`, `10-bpm`, `11-bkhm` | ✅ |
| FR-005 | Pengajuan Program Kerja Tahunan | `09-proker` | ✅ |
| FR-006 | Monitoring Program Kerja (BPM) | `09-proker`, `10-bpm` | ✅ |
| FR-007 | Pengajuan Proposal & Dana | `03-pengajuan` | ✅ |
| FR-008 | Generator Dokumen Otomatis | `07-generator-dokumen` | ✅ |
| FR-009 | Verifikasi Berjenjang (Dynamic Workflow) | `04-verifikasi-workflow` | ✅ |
| FR-010 | Tracking & Histori Status | `03-pengajuan`, `16-keamanan-nfr` | ✅ |
| FR-011 | Follow-up / Komunikasi Pengajuan | `15-komunikasi` | ✅ |
| FR-012 | Upload & Verifikasi LPJ | `04-verifikasi-workflow` | ✅ |
| FR-013 | Proses Pencairan Dana (Bendahara) | `05-pencairan` | ✅ |
| FR-014 | Monitoring Transparansi Keuangan (BPM) | `06-keuangan-bpm` | ✅ |
| FR-015 | Sistem Tiket Aspirasi | `12-aspirasi-regulasi` | ✅ |
| FR-016 | Tracking Status Aspirasi | `12-aspirasi-regulasi` | ✅ |
| FR-017 | Manajemen Master Inventaris Fasilitas | `08-sarpras-peminjaman` | ✅ |
| FR-018 | Kalender & Proteksi Ketersediaan | `08-sarpras-peminjaman` | ✅ |
| FR-019 | Approval & Validasi Keluar-Masuk Barang | `08-sarpras-peminjaman` | ✅ |
| FR-020 | Pelaporan Prestasi / Kompetisi | `13-prestasi` | ✅ |
| FR-021 | Pusat Informasi Terpusat | `12-aspirasi-regulasi` | ⚠️ publik tanpa login belum ada (lihat §5) |
| FR-022 | Notifikasi Perubahan Status | `14-notifikasi` | ✅ |
| FR-023 | Private File Storage | `16-keamanan-nfr` (SEC-01) | ✅ |
| FR-024 | Ekspor/Rekapitulasi Pencairan | `05-pencairan` | ✅ |
| FR-025 | Notifikasi Email Institusi | `14-notifikasi` | ⚠️ kanal in-app diuji; email via mail catcher (lihat §5) |

## 3. Security / Non-Functional / UI

| ID | Requirement | Spec | Status |
|----|-------------|------|--------|
| SEC-01 | Private Storage | `16-keamanan-nfr` | ✅ |
| SEC-02 | Upload Validation (MIME/ekstensi) | `16-keamanan-nfr` | ✅ |
| SEC-03 | Flash Session (anti URL spoofing) | `16-keamanan-nfr` | ✅ |
| SEC-04 | RBAC & Data Isolation | `01-auth-rbac`, `05-pencairan`, `06-keuangan-bpm` | ✅ |
| SEC-05 | Privacy & Confidentiality (aspirasi) | `12-aspirasi-regulasi` | ✅ |
| SEC-06 | SQL Injection Prevention | `16-keamanan-nfr` | ✅ |
| NFR-01 | Usability | `17-ui-ux` | ✅ |
| NFR-02 | Performance / Reliability | `16-keamanan-nfr` | ✅ |
| NFR-03 | Responsiveness | `17-ui-ux` | ✅ |
| NFR-04 | Maintainability (MVC) | — | N/A (arsitektur, bukan E2E) |
| NFR-05 | Dynamic Workflow | `16-keamanan-nfr` | ✅ |
| NFR-06 | Auditability (audit trail) | `03-pengajuan`, `16-keamanan-nfr` | ✅ |
| NFR-07 | Data Integrity (saldo) | — | N/A (diuji di PHPUnit `Phase1Test`) |
| NFR-08 | Query via ORM | — | N/A (bukan E2E) |
| UI-001…UI-005 | Dashboard per role | `02-dashboard-role` | ✅ |
| UI-006 | Dashboard Bendahara | `02-dashboard-role`, `05-pencairan` | ✅ |
| UI-007 | Dashboard Sarpras | `02-dashboard-role` | ✅ |
| UI-008 | Dashboard Mahasiswa | `02-dashboard-role` | ✅ |
| UI-009 | Portal Publik | `12-aspirasi-regulasi` | ⚠️ lihat §5 |
| UI-010 | Timeline Tracking | `03-pengajuan`, `16-keamanan-nfr` | ✅ |
| UI-011 | Pusat Notifikasi In-App | `14-notifikasi` | ✅ |
| UI-012 | Form Aspirasi (Tiket) | `12-aspirasi-regulasi` | ✅ |
| UI-013 | Kalender Interaktif | `08-sarpras-peminjaman` | ✅ |
| UI-014 | Terminologi Non-IT Friendly | `17-ui-ux` | ✅ |
| UI-015 | Feedback / Highlight Revisi | `04-verifikasi-workflow` | ✅ |
| UI-016 | Error & Validation Message | `17-ui-ux` | ✅ |
| UI-017 | Form Input Pengajuan/Dokumen | `03-pengajuan`, `07-generator-dokumen` | ✅ |
| UI-018 | Mobile Responsiveness | `17-ui-ux` | ✅ |
| UI-019 | Accessibility | `17-ui-ux` | ✅ |
| UI-020 | Perbedaan UI per Role | `02-dashboard-role`, `11-bkhm` | ✅ |
| UI-021 | Validasi Form Global | `03-pengajuan`, `17-ui-ux` | ✅ |

## 4. Business Rules (BR)

| ID | Business Rule | Spec | Status |
|----|---------------|------|--------|
| BR-01 | Login mahasiswa dengan NIM (standalone) | `01-auth-rbac` | ⚠️ implementasi memakai email/username |
| BR-02 | Alur pengajuan per pengaju (HIMA/BEM/BPM) | `03-pengajuan` | ✅ |
| BR-03 | Tidak boleh melompati tahapan | `03-pengajuan`, `04-verifikasi-workflow` | ✅ |
| BR-04 | Nominal pengajuan ≤ sisa saldo/limit | `03-pengajuan` | ❌ **gap** (§5) |
| BR-05 | Alasan wajib saat menolak/merevisi | `04-verifikasi-workflow` | ✅ |
| BR-06 | Bendahara tidak verifikasi substansi/LPJ | `04-verifikasi-workflow`, `05-pencairan` | ✅ |
| BR-07 | BPM read-only pada keuangan | `06-keuangan-bpm` | ✅ |
| BR-08 | Aspirasi divalidasi; identitas disimpan | `12-aspirasi-regulasi` | ✅ |
| BR-09 | BKHM sebelum Sarpras; HIMA butuh persetujuan Prodi | `08-sarpras-peminjaman` | ✅ |
| BR-10 | Tidak semua barang boleh keluar kampus | `08-sarpras-peminjaman` | ❌ **gap** (§5) |
| BR-11 | Pencairan bertahap/termin | `04-verifikasi-workflow`, `05-pencairan` | ✅ |
| BR-12 | Mahasiswa meminjam fasilitas via Ormawa | `08-sarpras-peminjaman` | ✅ |
| BR-13 | Proposal ditolak dikembalikan & diajukan ulang | `03-pengajuan`, `04-verifikasi-workflow` | ✅ |
| BR-14 | Semua pengajuan HIMA melewati BPM | `04-verifikasi-workflow` | ✅ |
| BR-15 | Proker tanpa approval; status manual | `09-proker` | ✅ |
| BR-16 | Admin melekat pada unit BKHM | `01-auth-rbac`, `11-bkhm` | ✅ |

---

## 5. Gap yang Didokumentasikan (ditandai `test.fixme`)

Test berikut sengaja ditandai `test.fixme` agar tetap terlihat tanpa menggagalkan suite.
Aktifkan setelah implementasi tersedia.

> Analisis kesenjangan lengkap (PRD vs implementasi) ada di
> `docs/proyek/03-kesenjangan-prd-vs-implementasi.md`.

| Lokasi | Requirement | Gap |
|--------|-------------|-----|
| `03-pengajuan` → "BR-04: nominal … melebihi sisa saldo ditolak" | BR-04 | `PengajuanController::store` belum memvalidasi `dana_diajukan` terhadap sisa saldo user. |
| `08-sarpras-peminjaman` → "BR-10: barang ditandai tidak boleh dibawa keluar" | BR-10 | `MasterBarang` belum memiliki flag `boleh_dibawa_keluar`. |
| `12-aspirasi-regulasi` → "AC Increment 3: pusat informasi publik" | FR-021 / UI-009 | Rute `/informasi` masih di dalam middleware `auth`. |
| `14-notifikasi` → "FR-025: notifikasi via email institusi" | FR-025 | Perlu mail catcher / `Mail::fake()`; kanal in-app sudah diuji. |

Selain itu, `NFR-04`, `NFR-07`, dan `NFR-08` bersifat arsitektural dan tidak dapat
diverifikasi via E2E — dicakup oleh pengujian PHPUnit (`tests/Feature/Phase1Test.php`, dll.).

---

## 6. Peta Perubahan Nama File (sebelum → sesudah)

| Sebelum | Sesudah |
|---------|---------|
| `01-auth.spec.ts` | `01-auth-rbac.spec.ts` |
| `02-dashboard.spec.ts` | `02-dashboard-role.spec.ts` |
| `03-pengajuan-blocking.spec.ts` | `03-pengajuan.spec.ts` |
| `07-verifikasi-flow.spec.ts` | `04-verifikasi-workflow.spec.ts` |
| `11-bendahara.spec.ts` | `05-pencairan.spec.ts` |
| `19-keuangan-bpm.spec.ts` | `06-keuangan-bpm.spec.ts` |
| `05-generator-surat.spec.ts` + `06-generator-proposal-lpj.spec.ts` | `07-generator-dokumen.spec.ts` |
| `04-peminjaman-sarpras.spec.ts` + `peminjaman.spec.ts` | `08-sarpras-peminjaman.spec.ts` |
| `16-proker-monitoring.spec.ts` | `09-proker.spec.ts` |
| `09-bpm.spec.ts` | `10-bpm.spec.ts` |
| `08-bkhm.spec.ts` + `admin.spec.ts` | `11-bkhm.spec.ts` |
| `13-aspirasi-regulasi-public.spec.ts` + `komunikasi.spec.ts` | `12-aspirasi-regulasi.spec.ts` |
| `17-prestasi.spec.ts` | `13-prestasi.spec.ts` |
| `18-notifikasi.spec.ts` | `14-notifikasi.spec.ts` |
| `20-nfr-security.spec.ts` + `14-documents-regression.spec.ts` | `16-keamanan-nfr.spec.ts` |
| `21-ux-validation.spec.ts` | `17-ui-ux.spec.ts` |
| `15-pengajuan-pengaju-role.spec.ts`, `auth.spec.ts`, `negative-tests.spec.ts`, `proposal-workflow.spec.ts`, `10-wr3.spec.ts`, `12-sidebar.spec.ts`, `example.spec.ts`, `utils/auth.ts` | dilebur / dihapus |

## 7. Menjalankan

```bash
# Semua spec
npx playwright test --reporter=list

# Satu grup requirement
npx playwright test e2e/04-verifikasi-workflow.spec.ts --reporter=list
```

Catatan: test berjalan sekuensial (`workers: 1`) terhadap satu DB. Aturan *blocking*
pengajuan (satu pengajuan aktif per pengaju) membuat sebagian test otomatis di-skip
bila state DB tidak bersih; jalankan `php artisan migrate:fresh --seed` untuk hasil penuh.
