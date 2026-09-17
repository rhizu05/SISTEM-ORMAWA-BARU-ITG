# Sistem Keuangan Ormawa — ITG

Aplikasi manajemen keuangan, pengajuan dana, peminjaman sarpras, dan persuratan untuk Organisasi Mahasiswa (Ormawa) berbasis **Laravel 13 + Breeze + Spatie Permission + DomPDF**.

Verifikasi E2E: suite **156 test (17 file spec)** di `e2e/` (`npx playwright test`, `php -S 127.0.0.1:8000`, `retries:2`). Jalankan `php artisan migrate:fresh --seed` sebelum E2E untuk hasil penuh (aturan *blocking* pengajuan membuat sebagian test di-skip bila state DB tidak bersih).

## Prasyarat

| Kebutuhan | Versi |
|-----------|-------|
| PHP | ^8.3 (teruji 8.4.14) |
| Composer | ^2.x |
| Node.js | ^18 / ^22 (teruji 22.22.3) |
| DB | SQLite (default) atau MySQL 8 |
| OS | Windows (Laragon) / Linux / macOS |

## Setup Lokal (Tim)

```bash
# 1. Clone
git clone <repo-url> sistem_keuangan
cd sistem_keuangan
git checkout develop   # atau main sesuai kesepakatan

# 2. Install dependencies
composer install
npm install

# 3. Environment
copy .env.example .env          # Windows
# cp .env.example .env          # Linux/macOS
php artisan key:generate

# SQLite (default .env.example: DB_CONNECTION=sqlite)
# buat file jika belum ada
if not exist database\database.sqlite type nul > database\database.sqlite
# Linux/macOS: touch database/database.sqlite

# MySQL (opsional) — edit .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sistem_keuangan
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Migrasi & seed (wajib: roles, users, workflow, konfigurasi, master sarpras)
php artisan migrate:fresh --seed

# 5. Build frontend
npm run build        # produksi
# npm run dev        # dev (vite HMR)

# 6. Storage link (untuk upload TTD & lampiran)
php artisan storage:link

# 7. Jalankan
php artisan serve --host=127.0.0.1 --port=8000
# atau via composer:
composer run dev     # serve + queue + vite concurrent (butuh @laravel/multiplex)
```

Buka `http://127.0.0.1:8000`.

## Akun Default (password semua: `password`)

| Role | Email | Username |
|------|-------|----------|
| Admin | admin@test.com | admin |
| BEM | bem@test.com | bem |
| BPM | bpm@test.com | bpm |
| BKHM | bkhm@test.com | bkhm |
| WR3 | wr3@test.com | wr3 |
| Bendahara | bendahara@test.com | bendahara |
| Sarpras | sarpras@test.com | sarpras |
| Ormawa (HIMA IF) | himaif@test.com | himaif |
| Mahasiswa | mahasiswa@test.com | mahasiswa |

Login dapat memakai **email atau username (NIM)**. Akun pengaju (Ormawa/BEM/BPM) di-seed dengan `saldo_awal` Rp 10.000.000 (batas BR-04).

Seed tambahan: `WorkflowSeeder`, `KonfigurasiSeeder`, `MasterDataSeeder` (4 ruangan + 6 barang), `PeriodeAnggaranSeeder` (periode anggaran berjalan).

## Testing

```bash
# Unit / Feature — 55 test, berjalan di sqlite :memory: (terisolasi dari DB kerja)
php artisan test
# atau
composer run test

# E2E Playwright (156 test / 17 file spec; sebagian skip bila ada pengajuan blocking)
npx playwright install --with-deps   # sekali
npx playwright test --reporter=list
npx playwright show-report           # html report

# Satu file
npx playwright test e2e/08-sarpras-peminjaman.spec.ts --reporter=list
```

Konfigurasi E2E: `playwright.config.ts` — `baseURL http://127.0.0.1:8000`, `webServer: php -S 127.0.0.1:8000 -t public`, `workers:1`, `retries:2`, `timeout:60s`.

> **Penting (isolasi test):** test PHPUnit dipaksa memakai `sqlite :memory:` oleh `tests/bootstrap.php`, karena sebagian mesin (mis. Laragon) mengekspor `APP_ENV`/`DB_CONNECTION` di level OS yang membuat `phpunit.xml` terabaikan dan `RefreshDatabase` bisa menghapus data DB kerja. Guard di `tests/TestCase.php` akan menggagalkan test bila koneksinya bukan sqlite in-memory.

## Git & Kebersihan Repo

`.gitignore` sudah mengabaikan:
```
.env, /vendor, /node_modules, /storage/*.key, /public/build,
/test-results, /playwright-report, /playwright/.cache, /e2e, /docs, CLAUDE.md, AGENTS.md
```
`CLAUDE.md` & `AGENTS.md` sengaja tidak di-track (instruksi internal agent). `e2e/` **dan** `docs/` juga **di-ignore** (lihat `.gitignore`), sehingga suite E2E dan dokumentasi bersifat lokal dan **tidak ikut terdistribusi lewat git** — lakukan backup manual bila dokumentasi perlu dibagikan.

## Troubleshooting

- `vite manifest not found` → `npm run build` atau `npm run dev`.
- `SQLSTATE[HY000] database.sqlite not found` → `touch database/database.sqlite` lalu `php artisan migrate:fresh --seed`.
- `419 Page Expired` saat E2E → clear cookies/session: `php artisan optimize:clear`.
- `net::ERR_ABORTED` di `php -S` → sudah ditangani via `gotoStable` (`waitUntil: domcontentloaded`) dan `retries:2`; cukup rerun `npx playwright test`.

## Lisensi

MIT — lihat `LICENSE`.
