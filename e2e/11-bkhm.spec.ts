import { test, expect } from '@playwright/test';
import { loginAs, gotoStable } from './helpers/auth';

/**
 * PRD: FR-002 (Manajemen User CRUD + saldo), FR-004 (Dashboard BKHM),
 *      BR-16 (admin melekat pada unit BKHM), UI-020 (UI sesuai hak akses).
 */
test.describe('FR-004 / UI-020 — BKHM sebagai administrator pusat', () => {
  test('sidebar BKHM menampilkan "Kelola BKHM" dan menyembunyikan Persuratan', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await expect(page.locator('span:has-text("Kelola BKHM")')).toBeAttached();
    await expect(page.getByRole('button', { name: /Persuratan/ })).toHaveCount(0);
  });

  test('dashboard BKHM menampilkan kartu antrean + kalender (FR-004)', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/dashboard');
    await expect(page.locator('text=Verifikasi Proposal').first()).toBeVisible();
    await expect(page.locator('text=Tabel Verifikasi Proposal').first()).toBeVisible();
    await expect(page.locator('text=Antrean Verifikasi Tempat').first()).toBeVisible();
    await expect(page.locator('text=Antrean Verifikasi Barang').first()).toBeVisible();
    await expect(page.locator('#calendar')).toBeVisible();
  });

  test('menu BKHM: saldo, arsip surat, SP, verifikasi tempat dapat diakses', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/bkhm/saldo');
    await expect(page.locator('body')).toContainText(/Saldo|Manajemen Saldo/i);

    await gotoStable(page, '/bkhm/arsip-surat');
    await expect(page.locator('body')).toContainText(/Arsip/i);

    await gotoStable(page, '/bkhm/surat-peringatan/create');
    await expect(page.getByRole('heading', { name: /Buat Surat Peringatan/ })).toBeVisible();
    await expect(page.locator('select[name="target_user_id"]')).toBeVisible();

    await gotoStable(page, '/bkhm/verifikasi-tempat');
    await expect(page.locator('body')).toContainText(/Verifikasi Tempat/);
  });
});

test.describe('FR-002 — Manajemen User (BKHM)', () => {
  test.beforeEach(async ({ page }) => {
    await loginAs(page, 'bkhm');
  });

  test('menampilkan daftar pengguna sistem', async ({ page }) => {
    await page.goto('/admin/users', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('h3').first()).toContainText('Daftar Pengguna Sistem');
    await expect(page.locator('table').first()).toContainText(/@test\.com/);
    await expect(page.locator('tbody tr').first()).toBeVisible();
  });

  test('dapat membuat pengguna baru', async ({ page }) => {
    await page.goto('/admin/users');
    await page.click('button:has-text("+ Tambah Pengguna")');

    const uniqueId = Date.now();
    await page.fill('input[name="name"]', 'UKM Basket');
    await page.fill('input[name="email"]', `basket${uniqueId}@test.com`);
    await page.fill('input[name="username"]', `ukmbasket${uniqueId}`);
    await page.fill('input[name="password"]', 'password');
    await page.selectOption('select[name="role"]', 'ormawa');

    await Promise.all([
      page.waitForNavigation(),
      page.click('form[action$="/admin/users"] button:has-text("Simpan")'),
    ]);

    await expect(page.locator('text=User berhasil ditambahkan.')).toBeVisible();
    await expect(page.locator('table').first()).toContainText(`basket${uniqueId}@test.com`);
  });

  test('dapat mengatur saldo Ormawa', async ({ page }) => {
    await page.goto('/admin/users', { waitUntil: 'domcontentloaded' });

    let row = page.locator('tr', { hasText: 'himaif@test.com' });
    let guard = 0;
    while (!(await row.isVisible().catch(() => false)) && guard < 3) {
      const next = page.locator('a[rel="next"]').first();
      if (!(await next.isVisible().catch(() => false))) break;
      await next.click();
      await page.waitForLoadState('domcontentloaded').catch(() => {});
      row = page.locator('tr', { hasText: 'himaif@test.com' });
      guard++;
    }
    if (!(await row.isVisible().catch(() => false))) {
      row = page.locator('tbody tr').first();
    }

    await row.locator('button:has-text("Atur Saldo")').click();
    await page.fill('input[name="saldo"]', '5000000');
    await page.fill('textarea[name="catatan"]', 'E2E set saldo').catch(() => {});

    await Promise.all([
      page.waitForNavigation(),
      page.click('button:has-text("Simpan Saldo")'),
    ]);

    await expect(page.locator('text=Saldo berhasil diperbarui').first()).toBeVisible();
  });

  test('dapat mengubah konfigurasi sistem', async ({ page }) => {
    await page.goto('/admin/konfigurasi');
    await page.fill('input[name="nama_aplikasi"]', 'SKIN-ITG (Test Playwright)');

    await Promise.all([
      page.waitForNavigation(),
      page.click('button:has-text("Simpan Pengaturan")'),
    ]);

    await expect(page.locator('text=Konfigurasi sistem berhasil diperbarui.')).toBeVisible();
    await expect(page.locator('input[name="nama_aplikasi"]')).toHaveValue('SKIN-ITG (Test Playwright)');
  });

  test('BACKLOG-014: dapat mengedit role dan status akun pengguna', async ({ page }) => {
    await page.goto('/admin/users');
    const firstRow = page.locator('tbody tr').first();
    await expect(firstRow).toBeVisible();

    // Buka modal edit pengguna
    await firstRow.locator('button:has-text("Edit")').click();
    await expect(page.locator('h3:has-text("Edit Pengguna")')).toBeVisible();

    // Verifikasi dropdown role dan status akun tersedia
    await expect(page.locator('select#edit_role')).toBeVisible();
    await expect(page.locator('select#edit_status')).toBeVisible();

    // Ganti status akun atau role
    await page.selectOption('select#edit_status', 'aktif');
    await Promise.all([
      page.waitForNavigation(),
      page.click('form[action*="/admin/users/"] button:has-text("Update")'),
    ]);

    await expect(page.locator('text=User berhasil diupdate.')).toBeVisible();
  });
});
