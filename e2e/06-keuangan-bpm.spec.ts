import { test, expect } from '@playwright/test';
import { loginAs, gotoStable } from './helpers/auth';

/**
 * PRD: FR-014 (Monitoring Transparansi Keuangan — BPM),
 *      BR-07 (BPM hanya read-only pada keuangan: Komisi III controlling & budgeting),
 *      SEC-04 (RBAC — BPM tidak mengubah saldo/konfigurasi).
 */
test.describe('FR-014 — Monitoring Transparansi Keuangan BPM', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, 'bpm');
  });

  test('BPM melihat ringkasan dana pada dashboard', async ({ page }) => {
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await expect(page.getByRole('heading', { name: /Dashboard BPM/ })).toBeVisible();
    await expect(page.locator('text=Status Dana Anda')).toBeVisible();
    await expect(page.getByRole('link', { name: /Verifikasi Proposal/ }).first()).toBeVisible();
  });

  test('BPM dapat melihat data pengajuan lintas Ormawa beserta nominal dana', async ({ page }) => {
    await gotoStable(page, '/pengajuan');
    await expect(page.locator('body')).toContainText(/Pengajuan|Riwayat/i);

    const headers = await page.locator('th').allTextContents();
    expect(headers.join(' | ')).toMatch(/Dana|Nominal|Rp/i);

    // Nilai rupiah tampil (transparansi anggaran).
    const rupiah = page.locator('td', { hasText: /Rp/ }).first();
    if (await rupiah.count() > 0) {
      await expect(rupiah).toBeVisible();
    }
  });

  test('BR-07: BPM tidak dapat mengubah saldo/konfigurasi (403)', async ({ page }) => {
    const adminUsers = await page.goto('/admin/users');
    expect(adminUsers?.status()).toBe(403);

    const bkhmSaldo = await page.goto('/bkhm/saldo');
    expect(bkhmSaldo?.status()).toBe(403);

    const konfig = await page.goto('/admin/konfigurasi');
    expect(konfig?.status()).toBe(403);
  });

  test('BR-07: dashboard BPM tidak menampilkan kontrol ubah saldo', async ({ page }) => {
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('button:has-text("Atur Saldo")')).toHaveCount(0);
    await expect(page.locator('button:has-text("Update Dana")')).toHaveCount(0);
  });
});
