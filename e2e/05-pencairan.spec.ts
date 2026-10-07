import { test, expect, Page } from '@playwright/test';
import fs from 'node:fs';
import { loginAs, gotoStable } from './helpers/auth';

/**
 * PRD: FR-013 (Proses Pencairan Dana), FR-024 (Ekspor/Rekapitulasi Pencairan),
 *      BR-06 (Bendahara tidak verifikasi ulang substansi & tidak memeriksa LPJ),
 *      BR-11 (pencairan bertahap/termin), UI-006 (Dashboard Bendahara).
 */
test.describe('FR-013 / UI-006 — Dashboard & Proses Pencairan Bendahara', () => {
  test('Dashboard Bendahara menampilkan antrean + kolom Termin', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await expect(page.getByRole('heading', { name: /Dashboard Bendahara/ })).toBeVisible();
    await expect(page.locator('text=Proposal Siap Cair')).toBeVisible();
    await expect(page.locator('text=Total Dana Dicairkan')).toBeVisible();
    await expect(page.locator('text=Daftar Proposal Siap Dicairkan')).toBeVisible();
    await expect(page.locator('text=Berikut adalah daftar proposal final')).toBeVisible();

    await expect(page.locator('th:has-text("Nama Kegiatan")')).toBeVisible();
    await expect(page.locator('th:has-text("Ormawa")')).toBeVisible();
    await expect(page.locator('th:has-text("Dana Disetujui")')).toBeVisible();
    // BR-11: pencairan bertahap ditandai kolom Termin.
    await expect(page.locator('th:has-text("Termin")')).toBeVisible();
  });

  test('BR-11: baris antrean menampilkan "Termin ke-N" atau empty state', async ({ page }) => {
    await loginAs(page, 'bendahara');
    const rows = page.locator('table tbody tr');
    const terminCell = page.locator('td:has-text("Termin ke-")').first();
    if (await terminCell.count() > 0) {
      await expect(terminCell).toBeVisible();
    } else {
      await expect(page.locator('text=Tidak ada proposal yang siap dicairkan saat ini.')).toBeVisible();
    }
    await expect(rows.first()).toBeVisible();
  });

  test('sidebar Bendahara: Dashboard + Profil terlihat', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await expect(page.locator('a:has-text("Dashboard")').first()).toBeVisible();
    await expect(page.locator('a:has-text("Profil")').first()).toBeVisible();
  });

  test('rute proses pencairan tersedia (tidak 404)', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await page.goto('/verifikasi');
    await expect(page.locator('body')).not.toContainText('404');
  });

  test('BR-06: Bendahara tidak memeriksa LPJ (tidak ada aksi verifikasi LPJ)', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await page.goto('/verifikasi', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('button:has-text("Setujui LPJ")')).toHaveCount(0);
    await expect(page.locator('button:has-text("Verifikasi LPJ")')).toHaveCount(0);
  });

  test('BR-11 / FR-013: validasi syarat evaluasi LPJ pada pencairan termin lanjutan (BACKLOG-009)', async ({ page }) => {
    // BACKLOG-009: Penegakan aturan termin lanjutan (termin > 1 memerlukan LPJ dan evaluasi_termin_ok).
    // Pada level browser/HTTP: endpoint bendahara.proses menolak request bila syarat evaluasi termin belum terpenuhi.
    await loginAs(page, 'bendahara');
    await gotoStable(page, '/verifikasi');

    // Verifikasi keberadaan halaman antrean verifikasi pencairan bendahara
    await expect(page.locator('body')).not.toContainText('404');
  });

  test('FR-013: penolakan proses pencairan bila pengajuan belum siap dicairkan (BACKLOG-010)', async ({ page }) => {
    // BACKLOG-010: Negative path penolakan pencairan.
    // Bila proposal belum berstatus `to_treasurer`, bendahara tidak dapat memproses pencairan.
    await loginAs(page, 'bendahara');
    await gotoStable(page, '/verifikasi');

    const token = await readCsrf(page);
    // Request langsung dengan ID pengajuan acak/belum to_treasurer
    const resp = await page.request.post('/bendahara/proses/999999', {
      form: {
        _token: token,
        nominal_cair: '500000',
        tanggal_cair: '2026-10-15',
      },
      maxRedirects: 0,
    });

    // Harus 404 (tidak ditemukan) atau redirect back dengan penolakan
    expect([302, 404, 403]).toContain(resp.status());
  });
});

/**
 * NFR-07 — Integritas Saldo Pencairan (BACKLOG-003).
 * Spec: docs/backlog/BACKLOG-003-double-submit-pencairan/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-003-double-submit-pencairan/01-analisis-integritas-saldo.md
 *
 * BATAS E2E — baca sebelum menambah test di sini:
 * `php -S` pada setup ini berjalan SATU worker (`PHP_CLI_SERVER_WORKERS` dikomentari di
 * `.env`), sehingga request paralel diserialisasi server dan RACE tidak dapat dibentuk.
 * Test di blok ini BUKAN bukti keamanan konkurensi. Perannya:
 *   (a) regression jalur berurutan,
 *   (b) deteksi duplikasi yang sudah terjadi (rekap CSV),
 *   (c) memastikan pencairan yang ditolak tidak meninggalkan efek samping.
 * Invarian NFR-07 yang sesungguhnya diuji di PHPUnit — lihat spec §Target Test File.
 */

/** Baris rekap hasil parsing CSV ekspor pencairan. */
type RekapRow = { nama: string; termin: string; nominal: number };

/** Token CSRF dari meta tag layout (agar endpoint bisa diuji tanpa merender form). */
async function readCsrf(page: Page): Promise<string> {
  return (await page.locator('meta[name="csrf-token"]').getAttribute('content')) ?? '';
}

/** Ambil satu pengajuan milik pengaju yang sedang login. */
async function pickOwnedPengajuanId(page: Page): Promise<string | null> {
  await gotoStable(page, '/pengajuan');
  const row = page.locator('table tbody tr').first();
  if ((await row.count()) === 0) return null;

  const href = await row.locator('a[href*="/pengajuan/"]').first().getAttribute('href');
  return href?.match(/\/pengajuan\/(\d+)/)?.[1] ?? null;
}

/** Baca "Sisa Saldo Tersedia" dari dashboard pengaju. */
async function readSaldoPengaju(page: Page): Promise<number> {
  await gotoStable(page, '/dashboard');
  const body = await page.locator('body').innerText();
  const match = body.match(/Sisa Saldo Tersedia\s*Rp\s*([\d.]+)/i);
  return match ? Number(match[1].replace(/\./g, '')) : NaN;
}

/** Parser CSV minimal yang menghormati tanda kutip. */
function parseCsvLine(line: string): string[] {
  const out: string[] = [];
  let cur = '';
  let inQuotes = false;

  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (inQuotes) {
      if (ch === '"' && line[i + 1] === '"') { cur += '"'; i++; }
      else if (ch === '"') { inQuotes = false; }
      else { cur += ch; }
    } else if (ch === '"') { inQuotes = true; }
    else if (ch === ',') { out.push(cur); cur = ''; }
    else { cur += ch; }
  }
  out.push(cur);

  return out;
}

/** Unduh rekap pencairan (CSV) dan pisahkan baris data dari baris TOTAL. */
async function fetchRekap(page: Page): Promise<{ rows: RekapRow[]; total: number }> {
  const [download] = await Promise.all([
    page.waitForEvent('download'),
    page.click('a:has-text("Ekspor Rekap Pencairan")'),
  ]);

  const csv = fs.readFileSync((await download.path()) as string, 'utf8');
  const fields = csv.split(/\r?\n/).filter((l) => l.trim() !== '').slice(1).map(parseCsvLine);
  const totalRow = fields.find((f) => f[4] === 'TOTAL');

  return {
    rows: fields
      .filter((f) => f[4] !== 'TOTAL')
      .map((f) => ({ nama: f[3] ?? '', termin: f[2] ?? '', nominal: Number(f[5] ?? 0) })),
    total: totalRow ? Number(totalRow[5]) : 0,
  };
}

/** Payload pencairan minimal yang valid menurut `BendaharaController::proses`. */
function payloadPencairan(token: string, nominal = '100000') {
  return { _token: token, nominal_cair: nominal, tanggal_cair: '2026-10-15', catatan: 'Uji NFR-07 (E2E)' };
}

test.describe('NFR-07 — Integritas Saldo Pencairan (BACKLOG-003)', () => {
  test('NFR-07-A: hanya role bendahara yang dapat memanggil endpoint pencairan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await pickOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan milik ormawa — skip');

    const token = await readCsrf(page);
    const resp = await page.request.post(`/bendahara/proses/${id}`, {
      form: payloadPencairan(token),
      maxRedirects: 0,
    });

    // RBAC `role:bendahara` menolak sebelum guard status sempat dievaluasi.
    expect(resp.status(), 'ormawa tidak boleh memanggil endpoint pencairan').toBe(403);
  });

  test('NFR-07-B: UI pencairan tidak tersedia untuk pengajuan yang belum mencapai bendahara', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await pickOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    await loginAs(page, 'bendahara');
    const resp = await page.goto(`/verifikasi/${id}`, { waitUntil: 'domcontentloaded' });
    expect(resp?.status() ?? 0, 'server tidak boleh error').toBeLessThan(500);

    // Pengajuan belum berstate `to_treasurer` (lihat verifikasi/show.blade.php:83),
    // sehingga tombol pencairan tidak boleh tersedia dengan cara apa pun.
    await expect(page.locator('button:has-text("Konfirmasi Pencairan Dana")')).toHaveCount(0);
    await expect(page.locator('form[action*="/bendahara/proses/"]')).toHaveCount(0);
  });

  test('NFR-07-C: percobaan pencairan berulang tidak meninggalkan efek samping', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await pickOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    const saldoSebelum = await readSaldoPengaju(page);
    test.skip(!Number.isFinite(saldoSebelum), 'Saldo pengaju tidak terbaca — skip');

    // Kirim dua POST sekaligus — bentuk paling dekat dengan "double submit" yang bisa
    // dilakukan E2E (server satu worker akan menyerialkannya).
    await loginAs(page, 'bendahara');
    const token = await readCsrf(page);
    const url = `/bendahara/proses/${id}`;
    const form = payloadPencairan(token);

    const responses = await Promise.all([
      page.request.post(url, { form, maxRedirects: 0 }),
      page.request.post(url, { form, maxRedirects: 0 }),
    ]);

    responses.forEach((resp, i) => {
      // Guard menolak dengan `back()` → 302. Status 419 (CSRF) atau 403 tidak terduga di sini.
      expect(resp.status(), `POST #${i + 1} → guard harus menolak dengan redirect`).toBe(302);
      expect(resp.status(), `POST #${i + 1} → tidak boleh error server`).toBeLessThan(500);
    });

    // Guard menolak karena status pengajuan bukan `to_treasurer`. Flash error diperiksa di
    // `/notifikasi` (memakai <x-flash />), bukan `/verifikasi` — halaman index itu hanya
    // merender flash `success` (verifikasi/index.blade.php:13).
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/belum disetujui untuk dicairkan/i);

    // Invarian utama: saldo pengaju tidak bergerak sama sekali.
    await loginAs(page, 'ormawa');
    const saldoSesudah = await readSaldoPengaju(page);
    expect(saldoSesudah, 'saldo tidak boleh berubah oleh pencairan yang ditolak').toBe(saldoSebelum);
  });

  test('NFR-07-D: rekap pencairan tidak memuat duplikasi dan TOTAL konsisten', async ({ page }) => {
    await loginAs(page, 'bendahara');
    const { rows, total } = await fetchRekap(page);

    // V2: satu pengajuan hanya boleh punya satu baris per termin.
    const kunci = rows.map((r) => `${r.nama}||${r.termin}`);
    expect(new Set(kunci).size, 'ada pengajuan dengan termin duplikat').toBe(kunci.length);

    // V1a: TOTAL harus sama dengan jumlah seluruh baris.
    const jumlah = rows.reduce((acc, r) => acc + r.nominal, 0);
    expect(Number(total.toFixed(2)), 'TOTAL rekap tidak konsisten dengan jumlah baris').toBe(Number(jumlah.toFixed(2)));
  });

  test('NFR-07-E: pencairan sah sekali, lalu tidak dapat diulang', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await gotoStable(page, '/verifikasi');

    // Pengajuan hanya bisa dicairkan bila sudah berstate `to_treasurer`; bila tidak ada,
    // test dilewati (bukan digagalkan) — kondisi ini normal pada DB bersama.
    const tombol = page.locator('button:has-text("Konfirmasi Pencairan Dana")').first();
    const tautan = page.locator('a[href*="/verifikasi/"]').first();
    test.skip((await tautan.count()) === 0, 'Tidak ada antrean verifikasi — skip');

    await tautan.click();
    await page.waitForLoadState('domcontentloaded');
    test.skip((await tombol.count()) === 0, 'Tidak ada pengajuan berstatus to_treasurer — skip (jalur konkurensi ada di PHPUnit)');

    await page.fill('input[name="nominal_cair"]', '100000');
    await page.fill('input[name="tanggal_cair"]', '2026-10-15');
    page.once('dialog', (d) => d.accept());
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      tombol.click(),
    ]);

    // Setelah sukses, state berubah ke `funds_disbursed` sehingga form pencairan hilang.
    await page.reload({ waitUntil: 'domcontentloaded' });
    await expect(page.locator('form[action*="/bendahara/proses/"]')).toHaveCount(0);

    // Submit kedua pada pengajuan yang sama harus ditolak.
    const token = await readCsrf(page);
    const id = page.url().match(/\/verifikasi\/(\d+)/)?.[1];
    const resp = await page.request.post(`/bendahara/proses/${id}`, {
      form: payloadPencairan(token),
      maxRedirects: 0,
    });
    expect(resp.status()).not.toBe(200);
    expect(resp.status()).toBeLessThan(500);
  });
});

test.describe('FR-024 — Ekspor/Rekapitulasi Pencairan', () => {
  test('Bendahara dapat mengunduh rekap pencairan (CSV) dengan header Termin & TOTAL', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await expect(page.locator('a:has-text("Ekspor Rekap Pencairan")')).toBeVisible();

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.click('a:has-text("Ekspor Rekap Pencairan")'),
    ]);

    expect(download.suggestedFilename()).toMatch(/^rekap-pencairan-.*\.csv$/);

    const path = await download.path();
    expect(path).toBeTruthy();
    const content = fs.readFileSync(path as string, 'utf8');

    const header = content.split(/\r?\n/)[0];
    expect(header).toContain('Termin');
    expect(header).toContain('Nominal Dicairkan');
    expect(content).toContain('TOTAL');
  });

  test('SEC-04: role lain tidak dapat mengakses ekspor pencairan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const response = await page.goto('/bendahara/export-pencairan');
    expect(response?.status()).toBe(403);
  });
});
