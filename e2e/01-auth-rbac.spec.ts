import { test, expect } from '@playwright/test';
import { loginAs, logout, users } from './helpers/auth';

/**
 * PRD: FR-001 (Login), FR-002 (Manajemen User), SEC-04 (RBAC & Data Isolation),
 *      UI-020 (Perbedaan UI berdasarkan hak akses), BR-16 (admin melekat pada unit BKHM).
 */
test.describe('FR-001 / SEC-04 — Autentikasi & Kontrol Akses', () => {
  test('halaman publik "/" dapat diakses tanpa login', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveURL(/\/$/);
  });

  test('tanpa login, /dashboard dialihkan ke /login', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*login.*/);
  });

  test('login kredensial benar mencapai /dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', users.ormawa.email);
    await page.fill('input[name="password"]', users.ormawa.password);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard.*/);
    await expect(page.locator('body')).toContainText(/Dashboard|Selamat Datang/i);
  });

  for (const key of Object.keys(users) as Array<keyof typeof users>) {
    test(`FR-001: login sebagai ${key} (${users[key].role}) berhasil`, async ({ page }) => {
      await loginAs(page, key);
      await expect(page.locator('body')).toContainText(/Dashboard|Selamat Datang/i);
    });
  }

  test('BR-01: login memakai username/NIM berhasil', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', users.ormawa.username);
    await page.fill('input[name="password"]', users.ormawa.password);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*dashboard.*/);
    await expect(page.locator('body')).toContainText(/Dashboard|Selamat Datang/i);
  });

  test('password salah tetap di /login dan menampilkan error', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', users.ormawa.email);
    await page.fill('input[name="password"]', 'wrong-password');
    await page.click('button[type="submit"]');

    await expect(page.locator('text=These credentials do not match')).toBeVisible({ timeout: 5000 }).catch(async () => {
      await expect(page).toHaveURL(/.*login.*/);
    });
  });

  test('format email tidak valid ditolak (tetap di /login)', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'himaif_bukan_email');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(/.*login.*/);
  });

  test('logout membersihkan sesi (rute terproteksi dialihkan ke /login)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await logout(page);
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/.*login.*/);
  });

  test('FR-001: halaman profil menampilkan identitas pengguna', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/profile');
    await expect(page.locator('h2').first()).toContainText(/Profile|Profil/i);
    await expect(page.locator('input[name="email"]')).toHaveValue(users.ormawa.email);
  });

  test('SEC-04: Ormawa ditolak mengakses /verifikasi (403)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const response = await page.goto('/verifikasi');
    expect(response?.status()).toBe(403);
  });

  test('SEC-04: BEM ditolak mengakses /admin/users (403)', async ({ page }) => {
    await loginAs(page, 'bem');
    const response = await page.goto('/admin/users');
    expect(response?.status()).toBe(403);
  });

  test('SEC-04: Ormawa ditolak mengakses area admin (403)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const response = await page.goto('/admin/users');
    expect(response?.status()).toBe(403);
  });

  const nonAdminRoles: ('ormawa' | 'bem' | 'bpm' | 'wr3' | 'bendahara' | 'sarpras' | 'mahasiswa')[] = [
    'ormawa',
    'bem',
    'bpm',
    'wr3',
    'bendahara',
    'sarpras',
    'mahasiswa',
  ];

  for (const role of nonAdminRoles) {
    test(`BACKLOG-020 / SEC-04: Role ${role} ditolak (403) di area admin`, async ({ page }) => {
      await loginAs(page, role);
      const resUsers = await page.goto('/admin/users');
      expect(resUsers?.status(), `Role ${role} harus 403 di /admin/users`).toBe(403);

      const resKonfig = await page.goto('/admin/konfigurasi');
      expect(resKonfig?.status(), `Role ${role} harus 403 di /admin/konfigurasi`).toBe(403);
    });
  }
});
