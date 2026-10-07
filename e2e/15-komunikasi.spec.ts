import { test, expect, Page } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';

/**
 * PRD: FR-011 (Follow-up / Komunikasi Pengajuan) — pengaju & verifikator dapat bertukar
 *      pesan pada satu pengajuan; memicu notifikasi ke pihak lain.
 */

async function openAnyPengajuan(page: Page): Promise<boolean> {
  await gotoStable(page, '/pengajuan');
  const link = page.locator('table a[href*="/pengajuan/"]').first();
  if (await link.count() === 0) return false;
  await link.click();
  await page.waitForLoadState('domcontentloaded');
  return true;
}

test.describe('FR-011 — Diskusi & Follow-up Pengajuan', () => {
  test('panel follow-up tersedia pada detail pengajuan', async ({ page }) => {
    await loginAs(page, 'ormawa');

    if (!(await openAnyPengajuan(page))) {
      // Belum ada pengajuan → buat draft.
      await gotoStable(page, '/pengajuan/create');
      if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
        test.skip(true, 'Tidak ada pengajuan & state blocking — skip');
        return;
      }
      await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E Followup'));
      await page.fill('input[name="dana_diajukan"]', '300000');
      await page.fill('input[name="tanggal_pengajuan"]', '2026-12-01');
      await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
      await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);
      await openAnyPengajuan(page);
    }

    await expect(page.getByRole('heading', { name: /Diskusi & Follow-up/ })).toBeVisible();
    await expect(page.locator('textarea[name="pesan"]')).toBeVisible();
  });

  test('pengaju dapat mengirim pesan follow-up', async ({ page }) => {
    await loginAs(page, 'ormawa');

    if (!(await openAnyPengajuan(page))) {
      test.skip(true, 'Tidak ada pengajuan untuk diuji — skip');
      return;
    }

    const pesan = uniqueName('Pesan follow-up E2E');
    await page.fill('textarea[name="pesan"]', pesan);
    await Promise.all([
      page.waitForNavigation(),
      page.click('button:has-text("Kirim")'),
    ]);

    await expect(page.locator('text=Pesan follow-up terkirim.')).toBeVisible();
    await expect(page.locator('body')).toContainText(pesan);
  });

  test('BACKLOG-024 / FR-011: penerima pesan follow-up menerima notifikasi in-app', async ({ page }) => {
    // 1. Ormawa membuat dan membuka pengajuan untuk mengirim pesan
    await loginAs(page, 'ormawa');
    if (!(await openAnyPengajuan(page))) {
      await gotoStable(page, '/pengajuan/create');
      if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
        test.skip(true, 'Blocking pengajuan aktif — skip');
        return;
      }
      const nama = uniqueName('Pengajuan Pesan E2E');
      await page.fill('input[name="nama_kegiatan"]', nama);
      await page.fill('input[name="dana_diajukan"]', '1000000');
      await page.fill('input[name="tanggal_pengajuan"]', '2026-11-01');
      await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
      await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);
      await openAnyPengajuan(page);
    }

    const pesanFollowup = uniqueName('Follow-up Notifikasi E2E');
    await page.fill('textarea[name="pesan"]', pesanFollowup);
    await Promise.all([
      page.waitForNavigation(),
      page.click('button:has-text("Kirim")'),
    ]);
    await expect(page.locator('text=Pesan follow-up terkirim.')).toBeVisible();

    // 2. Verifikator (BKHM / target role) login dan mengecek halaman notifikasi
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/notifikasi');

    // Verifikasi pesan notifikasi follow up tampil
    await expect(page.locator('body')).toContainText(/Pesan baru pada pengajuan/i);
  });
});
