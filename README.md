# Sistem Keuangan Ormawa — ITG

Aplikasi manajemen keuangan, pengajuan dana, peminjaman sarpras, dan persuratan untuk Organisasi Mahasiswa (Ormawa) berbasis **Laravel 13 + Breeze + Spatie Permission + DomPDF**.

Verifikasi E2E terakhir: **90 passed / 0 failed / 11 skipped** (`npx playwright test`, `php -S 127.0.0.1:8000`, `retries:2`).

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
| BKKH | bkh@test.com | bkh |
| WR3 | wr3@test.com | wr3 |
| Bendahara | bendahara@test.com | bendahara |
| Sarpras Ruangan | sarprasruangan@test.com | sarpras_ruangan |
| Sarpras Barang | sarprasbarang@test.com | sarprasbarang |
| Ormawa (HIMA IF) | himaif@test.com | himaif |

Seed tambahan: `WorkflowSeeder`, `KonfigurasiSeeder`, `MasterDataSeeder` (4 ruangan + 6 barang).

## Testing

```bash
# Unit / Feature
php artisan test
# atau
composer run test

# E2E Playwright (90/90 passed, 11 skipped = cascade proposal-workflow blocking)
npx playwright install --with-deps   # sekali
npx playwright test --reporter=list
npx playwright show-report           # html report

# Satu file
npx playwright test e2e/04-peminjaman-sarpras.spec.ts --reporter=list
```

Konfigurasi E2E: `playwright.config.ts` — `baseURL http://127.0.0.1:8000`, `webServer: php -S 127.0.0.1:8000 -t public`, `workers:1`, `retries:2`, `timeout:60s`.

## Git & Kebersihan Repo

`.gitignore` sudah mengabaikan:
```
.env, /vendor, /node_modules, /storage/*.key, /public/build,
/test-results, /playwright-report, /playwright/.cache, CLAUDE.md, AGENTS.md
```
`CLAUDE.md` & `AGENTS.md` sengaja tidak di-track (instruksi internal agent). `docs/` & `e2e/` tetap ter-track agar dokumentasi progres dan test suite ikut terdistribusi — jangan di-ignore bila butuh audit E2E.

## Troubleshooting

- `vite manifest not found` → `npm run build` atau `npm run dev`.
- `SQLSTATE[HY000] database.sqlite not found` → `touch database/database.sqlite` lalu `php artisan migrate:fresh --seed`.
- `419 Page Expired` saat E2E → clear cookies/session: `php artisan optimize:clear`.
- `net::ERR_ABORTED` di `php -S` → sudah ditangani via `gotoStable` (`waitUntil: domcontentloaded`) dan `retries:2`; cukup rerun `npx playwright test`.

## Lisensi

MIT — lihat `LICENSE`.
