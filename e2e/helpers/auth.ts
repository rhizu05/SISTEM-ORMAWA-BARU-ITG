import { Page, expect } from '@playwright/test';

/**
 * Akun default hasil seeder (password: `password`).
 * Role `admin` tersedia sebagai akun unit (read-only monitoring) di samping BKHM.
 */
export const users = {
  admin: { username: 'admin', email: 'admin@test.com', password: 'password', role: 'admin' },
  bem: { username: 'bem', email: 'bem@test.com', password: 'password', role: 'bem' },
  bpm: { username: 'bpm', email: 'bpm@test.com', password: 'password', role: 'bpm' },
  bkhm: { username: 'bkhm', email: 'bkhm@test.com', password: 'password', role: 'bkhm' },
  wr3: { username: 'wr3', email: 'wr3@test.com', password: 'password', role: 'wr3' },
  bendahara: { username: 'bendahara', email: 'bendahara@test.com', password: 'password', role: 'bendahara' },
  sarpras: { username: 'sarpras', email: 'sarpras@test.com', password: 'password', role: 'sarpras' },
  ormawa: { username: 'himaif', email: 'himaif@test.com', password: 'password', role: 'ormawa' },
} as const;

export type UserKey = keyof typeof users;

/**
 * Navigasi stabil untuk `php -S` (single-thread) — hindari menunggu semua aset.
 */
export async function gotoStable(page: Page, url: string) {
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 15000 });
  await page.waitForLoadState('domcontentloaded').catch(() => {});
  await page.waitForTimeout(200);
}

/**
 * Login via /login (Breeze). Mendukung field email maupun username.
 */
export async function loginAs(page: Page, key: UserKey) {
  const u = users[key];
  await page.goto('/login', { waitUntil: 'domcontentloaded' });
  // Jika sesi masih aktif, /login akan redirect ke /dashboard → bersihkan dulu.
  if (!/\/login/.test(page.url())) {
    await page.context().clearCookies();
    await page.goto('/login', { waitUntil: 'domcontentloaded' });
  }
  await expect(page).toHaveURL(/.*login/);

  const emailInput = page.locator('input[name="email"], input[name="username"], input[type="email"]').first();
  const passwordInput = page.locator('input[name="password"]').first();

  await emailInput.fill(u.email);
  await passwordInput.fill(u.password);
  await page.locator('button[type="submit"], button:has-text("Log in"), button:has-text("Masuk")').first().click();
  await page.waitForURL(/.*dashboard.*/, { timeout: 15000, waitUntil: 'domcontentloaded' });
  await expect(page).not.toHaveURL(/.*login/);
  await page.waitForLoadState('domcontentloaded', { timeout: 15000 });
  await expect(page.locator('body')).toContainText(/Dashboard|Selamat Datang|Sistem Keuangan/, { timeout: 10000 });
  await page.waitForTimeout(400);
}

export async function logout(page: Page) {
  // Breeze memakai POST /logout di dalam dropdown; form dirender di DOM (walau tersembunyi).
  const form = page.locator('form[action$="/logout"]').first();
  if (await form.count() > 0) {
    await Promise.all([
      page.waitForNavigation({ timeout: 15000 }).catch(() => {}),
      form.evaluate((f) => (f as HTMLFormElement).submit()),
    ]);
  } else {
    const toggle = page.locator('button:has-text("Log Out"), button:has-text("Keluar")').first();
    if (await toggle.isVisible().catch(() => false)) await toggle.click();
    const link = page.locator('a:has-text("Log Out"), a:has-text("Keluar")').first();
    if (await link.isVisible().catch(() => false)) await link.click();
  }
  await page.waitForTimeout(300);
}

/** Nama unik berbasis waktu untuk menghindari tabrakan data antar-run. */
export function uniqueName(prefix: string) {
  return `${prefix} ${Date.now()}${Math.floor(Math.random() * 1000)}`;
}
