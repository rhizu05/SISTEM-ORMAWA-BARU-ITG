import { test, expect, Page } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';

/**
 * PRD: FR-007 (Pengajuan Proposal & Dana), FR-010 (Tracking & Histori),
 *      BR-02 (alur per pengaju), BR-03 (tidak melompati tahap),
 *      BR-04 (nominal ≤ sisa saldo), BR-13 (revisi ke titik penolakan),
 *      UI-010 (timeline), UI-015 (highlight revisi), UI-017 (form), UI-021 (validasi).
 */

async function isBlocked(page: Page) {
  return page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false);
}

async function createDraft(page: Page, nama: string) {
  await page.goto('/pengajuan/create', { waitUntil: 'domcontentloaded' });
  if (await isBlocked(page)) return false;
  await page.fill('input[name="nama_kegiatan"]', nama);
  await page.fill('input[name="dana_diajukan"]', '500000');
  await page.fill('input[name="tanggal_pengajuan"]', '2026-10-20');
  await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
  await Promise.all([
    page.waitForNavigation(),
    page.click('button:has-text("Simpan sebagai Draft")'),
  ]);
  return true;
}

test.describe('FR-007 / BR-03 — Pengajuan: form & pemblokiran', () => {
  test('UI-017: form create tampil saat tidak ada pengajuan aktif', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    await expect(page.locator('text=/Buat Pengajuan Baru|Pengajuan Ditangguhkan/').first()).toBeVisible();
  });

  test('BR-03: state blocking menampilkan Nama Kegiatan & Status + form disabled', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    await expect(page.locator('text=/Buat Pengajuan Baru|Pengajuan Ditangguhkan/').first()).toBeVisible();

    if (await isBlocked(page)) {
      await expect(page.locator('text=Nama Kegiatan').first()).toBeVisible();
      await expect(page.locator('text=Status Saat Ini').first()).toBeVisible();
      await expect(page.locator('text=Lihat Pengajuan')).toBeVisible();
      await expect(page.locator('#nama_kegiatan, input[name="nama_kegiatan"]')).toBeDisabled();
    } else {
      await expect(page.locator('#nama_kegiatan, input[name="nama_kegiatan"]')).toBeEnabled();
      await expect(page.locator('button:has-text("Simpan sebagai Draft")')).toBeVisible();
    }
  });

  test('BR-03: store yang diblokir tidak menghasilkan error 500', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await isBlocked(page)) {
      const csrf = await page.locator('input[name="_token"]').inputValue().catch(() => '');
      await page.request.post('/pengajuan', {
        form: { _token: csrf, nama_kegiatan: 'Test Block', dana_diajukan: '100000', tanggal_pengajuan: '2026-09-01' },
        headers: { Referer: '/pengajuan/create' },
      });
      await page.goto('/pengajuan');
      await expect(page.locator('body')).not.toContainText('Whoops, something went wrong');
    }
  });

  test('UI-021: submit tanpa file proposal tetap di halaman form (validasi)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await isBlocked(page)) {
      test.skip(true, 'Ada pengajuan aktif (blocking) — validasi file tidak dapat diuji');
      return;
    }
    await page.fill('input[name="nama_kegiatan"]', 'Kegiatan Tanpa File');
    await page.fill('input[name="dana_diajukan"]', '1000000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-10-10');
    await page.click('button:has-text("Simpan sebagai Draft")');
    expect(page.url()).toContain('/pengajuan/create');
  });
});

test.describe('FR-007 / FR-010 — Pengajuan: siklus & timeline', () => {
  test.describe.configure({ mode: 'serial' });

  const nama = `E2E Pengajuan ${Date.now()}`;

  test('FR-007: simpan draft berhasil dan muncul di riwayat', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const created = await createDraft(page, nama);
    test.skip(!created, 'Ada pengajuan aktif (blocking) untuk ormawa — skip');
    await expect(page.locator('body')).toContainText(/Draft|berhasil dibuat/i);
    await expect(page.locator('table').first()).toContainText(nama);
  });

  test('UI-010: halaman detail menampilkan timeline riwayat + aktor + waktu', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan');
    const row = page.locator('tr', { hasText: nama }).first();
    test.skip((await row.count()) === 0, 'Draft tidak tersedia (blocking) — skip');

    await row.locator('a[href*="/pengajuan/"]').first().click();
    await page.waitForLoadState('domcontentloaded');

    await expect(page.getByRole('heading', { name: /Riwayat & Status PIC/ })).toBeVisible();
    await expect(page.locator('text=Oleh:').first()).toBeVisible();
    // waktu berformat dd/mm/yyyy hh:mm
    await expect(page.locator('text=/\\d{2}\\/\\d{2}\\/\\d{4} \\d{2}:\\d{2}/').first()).toBeVisible();
    await expect(page.locator('text=Pengajuan draft dibuat')).toBeVisible();
  });

  test('BR-13: pengajuan draft dapat diedit', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan');
    const row = page.locator('tr', { hasText: nama }).first();
    test.skip((await row.count()) === 0, 'Draft tidak tersedia (blocking) — skip');

    await row.locator('a[href*="/pengajuan/"]').first().click();
    await page.waitForLoadState('domcontentloaded');
    const editLink = page.locator('a:has-text("Edit / Revisi")');
    await expect(editLink).toBeVisible();
    await editLink.click();
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('input[name="nama_kegiatan"]')).toBeVisible();
  });

  test('BR-04: nominal pengajuan melebihi sisa saldo ditolak', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await isBlocked(page)) {
      test.skip(true, 'Ada pengajuan aktif (blocking) — tidak dapat menguji batas saldo');
      return;
    }
    await page.fill('input[name="nama_kegiatan"]', uniqueName('Melebihi Saldo'));
    await page.fill('input[name="dana_diajukan"]', '999999999999');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-10-10');
    await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
    await page.click('button:has-text("Simpan sebagai Draft")');

    await expect(page.locator('body')).toContainText(/melebihi sisa saldo/i);
    expect(page.url()).toContain('/pengajuan/create');
  });
});

/**
 * BR-04 — Validasi nominal pengajuan vs sisa saldo.
 * Spec: docs/backlog/BACKLOG-001-validasi-saldo/02-spec-test.md (TC-BR04-001 s/d TC-BR04-010).
 *
 * Catatan desain:
 * - Nominal dihitung relatif terhadap saldo yang ditampilkan di form, bukan
 *   di-hardcode Rp 10.000.000 — saldo bisa berkurang oleh test pencairan di
 *   spec lain, sehingga hardcode akan membuat test rapuh.
 * - `isBlocked()` dipakai untuk auto-skip saat pengaju masih punya pengajuan aktif.
 */
const SALDO_MINIMAL = 1000;

/** Baca "Sisa Saldo Anda" dari kartu info finansial di /pengajuan/create. */
async function readSaldo(page: Page): Promise<number> {
  const body = await page.locator('body').innerText();
  const match = body.match(/Sisa\s+Saldo\s+Anda[\s\S]*?Rp\s*([\d.]+)/i);
  return match ? Number(match[1].replace(/\./g, '')) : NaN;
}

/** Format angka ala Indonesia untuk assertion teks tabel (5000000 -> "5.000.000"). */
function rupiah(n: number): string {
  return n.toLocaleString('id-ID');
}

/** Isi form pengajuan. `nominal === ''` membiarkan field dana kosong. */
async function fillPengajuan(page: Page, nama: string, nominal: number | '', tanggal: string) {
  await page.fill('input[name="nama_kegiatan"]', nama);
  if (nominal !== '') await page.fill('input[name="dana_diajukan"]', String(nominal));
  await page.fill('input[name="tanggal_pengajuan"]', tanggal);
  await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
}

/** Submit dan tunggu respons server (form lolos validasi HTML5 sehingga terkirim). */
async function submitDraft(page: Page) {
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
    page.click('button:has-text("Simpan sebagai Draft")'),
  ]);
  await page.waitForLoadState('domcontentloaded').catch(() => {});
}

/** Klik submit tanpa berharap navigasi (validasi HTML5 memblokir pengiriman). */
async function clickSubmit(page: Page) {
  await page.click('button:has-text("Simpan sebagai Draft")');
  await page.waitForTimeout(500);
}

/** Pastikan pengajuan dengan nama tersebut tidak menghasilkan baris baru. */
async function expectTidakTersimpan(page: Page, nama: string) {
  await gotoStable(page, '/pengajuan');
  await expect(page.locator('body')).not.toContainText(nama);
}

test.describe('BR-04 — Validasi nominal pengajuan vs sisa saldo', () => {
  let saldo = 0;

  test.beforeEach(async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan/create');
    if (await isBlocked(page)) {
      test.skip(true, 'Ada pengajuan aktif — tidak dapat menguji batas saldo');
    }
    saldo = await readSaldo(page);
    if (!Number.isFinite(saldo) || saldo < SALDO_MINIMAL) {
      test.skip(true, `Saldo pengaju tidak memadai untuk pengujian (saldo=${saldo})`);
    }
  });

  test('TC-BR04-001: pengajuan 50% saldo diterima sebagai draft', async ({ page }) => {
    const nama = uniqueName('BR04 50 Persen');
    const nominal = Math.floor(saldo / 2);

    await fillPengajuan(page, nama, nominal, '2026-12-15');
    await submitDraft(page);

    await expect(page).toHaveURL(/\/pengajuan$/);
    await expect(page.locator('body')).toContainText(/Draft|berhasil dibuat/i);
    const row = page.locator('tr', { hasText: nama }).first();
    await expect(row).toContainText(nama);
    await expect(row).toContainText(rupiah(nominal));
  });

  test('TC-BR04-002: pengajuan tepat 100% saldo diterima', async ({ page }) => {
    const nama = uniqueName('BR04 100 Persen');

    await fillPengajuan(page, nama, saldo, '2026-12-15');
    await submitDraft(page);

    await expect(page).toHaveURL(/\/pengajuan$/);
    await expect(page.locator('body')).not.toContainText(/melebihi sisa saldo/i);
    await expect(page.locator('tr', { hasText: nama }).first()).toBeVisible();
  });

  test('TC-BR04-003: pengajuan 1 rupiah di atas saldo ditolak', async ({ page }) => {
    const nama = uniqueName('BR04 Lewat Satu Rupiah');

    await fillPengajuan(page, nama, saldo + 1, '2026-12-15');
    await submitDraft(page);

    await expect(page.locator('body')).toContainText(/melebihi sisa saldo/i);
    expect(page.url()).toContain('/pengajuan/create');
    await expectTidakTersimpan(page, nama);
  });

  test('TC-BR04-004: pengajuan 5x saldo ditolak', async ({ page }) => {
    const nama = uniqueName('BR04 Lima Kali Saldo');

    await fillPengajuan(page, nama, saldo * 5, '2026-12-15');
    await submitDraft(page);

    await expect(page.locator('body')).toContainText(/melebihi sisa saldo/i);
    expect(page.url()).toContain('/pengajuan/create');
    await expectTidakTersimpan(page, nama);
  });

  test('TC-BR04-005: pengajuan nominal masif ditolak', async ({ page }) => {
    const nama = uniqueName('BR04 Nominal Masif');

    await fillPengajuan(page, nama, Math.max(999999999, saldo + 1), '2026-12-15');
    await submitDraft(page);

    await expect(page.locator('body')).toContainText(/melebihi sisa saldo/i);
    expect(page.url()).toContain('/pengajuan/create');
    await expectTidakTersimpan(page, nama);
  });

  test('TC-BR04-006: nominal nol ditolak', async ({ page }) => {
    const nama = uniqueName('BR04 Nominal Nol');

    await fillPengajuan(page, nama, 0, '2026-12-15');
    await submitDraft(page);

    expect(page.url()).toContain('/pengajuan/create');
    const invalidNol = await page.locator('input[name="dana_diajukan"]:invalid').count();
    const pesanValidasi = /dana|nominal|minimal/i.test(await page.locator('body').innerText());
    expect(invalidNol === 1 || pesanValidasi).toBeTruthy();
    await expectTidakTersimpan(page, nama);
  });

  test('TC-BR04-007: nominal kosong ditolak validasi form', async ({ page }) => {
    await fillPengajuan(page, uniqueName('BR04 Nominal Kosong'), '', '2026-12-15');
    await clickSubmit(page);

    expect(page.url()).toContain('/pengajuan/create');
    await expect(page.locator('input[name="dana_diajukan"]:invalid')).toHaveCount(1);
  });

  test('TC-BR04-008: nominal negatif ditolak validasi form', async ({ page }) => {
    await fillPengajuan(page, uniqueName('BR04 Nominal Negatif'), -100000, '2026-12-15');
    await clickSubmit(page);

    expect(page.url()).toContain('/pengajuan/create');
    await expect(page.locator('input[name="dana_diajukan"]:invalid')).toHaveCount(1);
  });
});

/**
 * BR-04 §5.4 — pengaju non-ormawa (BEM/BPM) tunduk pada batas saldo yang sama.
 * Dipisah dari blok di atas karena `beforeEach` blok tersebut mengunci login ormawa.
 */
const pengajuNonOrmawa = [
  { tc: 'TC-BR04-009', role: 'bem' as const },
  { tc: 'TC-BR04-010', role: 'bpm' as const },
];

test.describe('BR-04 — Batas saldo pengaju BEM/BPM', () => {
  for (const { tc, role } of pengajuNonOrmawa) {
    test(`${tc}: ${role.toUpperCase()} ajukan melebihi saldo ditolak`, async ({ page }) => {
      await loginAs(page, role);
      await gotoStable(page, '/pengajuan/create');
      if (await isBlocked(page)) {
        test.skip(true, `Ada pengajuan aktif untuk ${role} — tidak dapat menguji batas saldo`);
      }

      const saldoRole = await readSaldo(page);
      if (!Number.isFinite(saldoRole) || saldoRole < SALDO_MINIMAL) {
        test.skip(true, `Saldo ${role} tidak memadai untuk pengujian (saldo=${saldoRole})`);
      }

      const nama = uniqueName(`BR04 ${role.toUpperCase()} Lewat Saldo`);
      await fillPengajuan(page, nama, saldoRole + 1, '2026-12-15');
      await submitDraft(page);

      await expect(page.locator('body')).toContainText(/melebihi sisa saldo/i);
      expect(page.url()).toContain('/pengajuan/create');
      await expectTidakTersimpan(page, nama);
    });
  }
});

/**
 * BR-02 — Routing submit berdasarkan role pengaju:
 *   Ormawa/HIMA/UKM → BEM, BEM → BPM, BPM → BKHM.
 */
test.describe('BR-02 — Routing submit per role pengaju', () => {
  test('Ormawa: tombol submit mengarah ke BEM', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const created = await createDraft(page, `E2E Routing Ormawa ${Date.now()}`);
    test.skip(!created, 'Ada pengajuan aktif (blocking) untuk ormawa — skip');

    const row = page.locator('tr', { hasText: 'E2E Routing Ormawa' }).first();
    await expect(row).toBeVisible();
    await row.locator('a:has-text("Detail")').click();
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('button:has-text("Ajukan ke BEM")')).toBeVisible();
  });

  test('BEM: submit mengarah ke BPM', async ({ page }) => {
    await loginAs(page, 'bem');
    const created = await createDraft(page, `E2E Routing BEM ${Date.now()}`);
    test.skip(!created, 'Ada pengajuan aktif (blocking) untuk BEM — skip');

    const row = page.locator('tr', { hasText: 'E2E Routing BEM' }).first();
    await expect(row).toBeVisible();
    page.once('dialog', (d) => d.accept());
    await Promise.all([
      page.waitForNavigation(),
      row.locator('button:has-text("Ajukan")').click(),
    ]);
    await expect(page.locator('body')).toContainText('Pengajuan berhasil dikirim ke BPM.');
  });

  test('BPM: submit mengarah ke BKHM', async ({ page }) => {
    await loginAs(page, 'bpm');
    const created = await createDraft(page, `E2E Routing BPM ${Date.now()}`);
    test.skip(!created, 'Ada pengajuan aktif (blocking) untuk BPM — skip');

    const row = page.locator('tr', { hasText: 'E2E Routing BPM' }).first();
    await expect(row).toBeVisible();
    page.once('dialog', (d) => d.accept());
    await Promise.all([
      page.waitForNavigation(),
      row.locator('button:has-text("Ajukan")').click(),
    ]);
    await expect(page.locator('body')).toContainText('Pengajuan berhasil dikirim ke BKHM.');
  });

  test('BACKLOG-027 / FR-007: filter status dan pencarian keyword pada daftar pengajuan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan');

    // 1. Verifikasi form filter dan input search tampil
    const searchInput = page.locator('input[name="q"]');
    const statusSelect = page.locator('select[name="status"]');
    await expect(searchInput).toBeVisible();
    await expect(statusSelect).toBeVisible();

    // 2. Lakukan pencarian keyword sembarang yang tidak ada -> tabel menampilkan state kosong
    await searchInput.fill('KEYWORD_TIDAK_MUNGKIN_ADA_XYZ_999');
    await Promise.all([
      page.waitForNavigation(),
      page.click('button:has-text("Cari")'),
    ]);

    await expect(page.locator('body')).toContainText(/Tidak ada pengajuan yang sesuai|Belum ada pengajuan/i);

    // 3. Reset pencarian
    const resetLink = page.locator('a:has-text("Reset")');
    if (await resetLink.isVisible()) {
      await Promise.all([
        page.waitForNavigation(),
        resetLink.click(),
      ]);
    }

    // 4. Uji Filter Status dropdown (memicu onchange submit)
    await Promise.all([
      page.waitForNavigation(),
      statusSelect.selectOption({ index: 1 }),
    ]);
    expect(page.url()).toContain('status=');
  });
});
