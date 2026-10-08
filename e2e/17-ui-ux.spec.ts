import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

/**
 * PRD: UI-014 (Terminologi Non-IT Friendly), UI-016 (Error & Validation Message),
 *      UI-018 (Mobile Responsiveness), UI-019 (Accessibility), UI-021 (Validasi Form Global),
 *      NFR-01 (Usability), NFR-03 (Responsiveness).
 */

async function hasHorizontalScroll(page: import('@playwright/test').Page) {
  return page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
}

test.describe('UI-018 / NFR-03 — Responsiveness', () => {
  test('halaman login tidak overflow horizontal pada 320px', async ({ page }) => {
    await page.setViewportSize({ width: 320, height: 640 });
    await page.goto('/login', { waitUntil: 'domcontentloaded' });

    expect(await hasHorizontalScroll(page)).toBe(false);
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    const size = await page.locator('button[type="submit"]').boundingBox();
    expect(size).not.toBeNull();
    expect(size!.height).toBeGreaterThanOrEqual(32);
  });

  test('dashboard tidak overflow horizontal pada 320px', async ({ page }) => {
    await page.setViewportSize({ width: 320, height: 640 });
    await loginAs(page, 'ormawa');
    expect(await hasHorizontalScroll(page)).toBe(false);
  });

  test('dashboard tidak overflow horizontal pada 768px & 1024px', async ({ page }) => {
    for (const width of [768, 1024]) {
      await page.setViewportSize({ width, height: 800 });
      await loginAs(page, 'ormawa');
      expect(await hasHorizontalScroll(page)).toBe(false);
    }
  });

  test('BACKLOG-026 / UI-018: dashboard dan halaman utama stabil tanpa horizontal scroll pada viewport desktop besar (1440px & 1920px FHD)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    for (const width of [1440, 1920]) {
      await page.setViewportSize({ width, height: 1080 });
      await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
      expect(await hasHorizontalScroll(page), `Viewport ${width}px tidak boleh overflow horizontal`).toBe(false);

      await page.goto('/pengajuan', { waitUntil: 'domcontentloaded' });
      expect(await hasHorizontalScroll(page), `Halaman /pengajuan pada viewport ${width}px tidak boleh overflow horizontal`).toBe(false);
    }
  });
});

test.describe('UI-014 / NFR-01 — Terminologi ramah pengguna', () => {
  test('dashboard memakai istilah kemahasiswaan, bukan jargon teknis', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const body = (await page.locator('body').textContent()) ?? '';

    expect(body).toMatch(/Pengajuan|Proposal|Dana|Verifikasi|Peminjaman|Profil/i);
    expect(body).not.toMatch(/SQLSTATE|Stack trace|Exception|Middleware|Eloquent/i);
  });

  test('BACKLOG-025: dashboard role non-IT (Bendahara & WR3) menampilkan istilah kemahasiswaan ramah pengguna tanpa kebocoran jargon teknis', async ({ page }) => {
    // 1. Uji peran Bendahara
    await loginAs(page, 'bendahara');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    const bodyBendahara = (await page.locator('body').textContent()) ?? '';

    // Positive check: istilah operasional pencairan & kemahasiswaan harus hadir
    expect(bodyBendahara).toMatch(/Proposal Siap Cair|Dana Dicairkan|Termin|Ekspor Rekap Pencairan/i);
    // Negative check: tidak boleh bocor istilah teknis internal/debugging
    expect(bodyBendahara).not.toMatch(/\b(null|undefined|array|object|migration|endpoint)\b/i);

    // 2. Uji peran Wakil Rektor 3 (WR3)
    await loginAs(page, 'wr3');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    const bodyWr3 = (await page.locator('body').textContent()) ?? '';

    // Positive check: istilah monitoring kegiatan & saldo harus hadir
    expect(bodyWr3).toMatch(/Agenda Rapat|Verifikasi Proposal|Manajemen Saldo|Jadwal Terpadu/i);
    // Negative check: tidak boleh ada jargon debugging
    expect(bodyWr3).not.toMatch(/\b(null|undefined|array|object|migration|endpoint)\b/i);
  });
});

test.describe('UI-016 / UI-021 — Pesan error & validasi', () => {
  test('halaman 404 informatif dan tidak membocorkan stack trace', async ({ page }) => {
    const response = await page.goto('/halaman-tidak-ada-e2e', { waitUntil: 'domcontentloaded' });
    expect(response?.status()).toBe(404);

    const body = (await page.locator('body').textContent()) ?? '';
    expect(body).not.toMatch(/Stack trace|Whoops|NotFoundHttpException|vendor\\laravel/i);
  });

  test('validasi form pengajuan menahan submit kosong tanpa error teknis', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create', { waitUntil: 'domcontentloaded' });

    if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
      test.skip(true, 'State blocking — validasi form tidak diuji');
      return;
    }

    await page.click('button:has-text("Simpan sebagai Draft")');
    expect(page.url()).toContain('/pengajuan/create');

    const body = (await page.locator('body').textContent()) ?? '';
    expect(body).not.toMatch(/Stack trace|SQLSTATE|Whoops/i);
  });
});

test.describe('UI-019 — Accessibility (WCAG 2.1 AA)', () => {
  test('dokumen memakai atribut lang & input form memiliki label terhubung', async ({ page }) => {
    await page.goto('/login', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('html')).toHaveAttribute('lang', /./);

    const email = page.locator('input[name="email"]');
    const emailId = await email.getAttribute('id');
    if (emailId) {
      await expect(page.locator(`label[for="${emailId}"]`)).toHaveCount(1);
    } else {
      await expect(email).toHaveAttribute('name', 'email');
    }

    const pass = page.locator('input[name="password"]');
    const passId = await pass.getAttribute('id');
    if (passId) {
      await expect(page.locator(`label[for="${passId}"]`)).toHaveCount(1);
    }
  });

  test('BACKLOG-021: skip link dan screen reader landmarks (<main>, <header>, <nav>) tersedia di dashboard', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });

    // 1. Skip link untuk keyboard navigation
    const skipLink = page.locator('a[href="#main-content"]');
    await expect(skipLink).toHaveCount(1);

    // 2. Landmarks HTML5 / ARIA
    await expect(page.locator('header')).toBeVisible();
    await expect(page.locator('main#main-content')).toBeVisible();
    await expect(page.locator('nav[aria-label="Navigasi utama"]')).toBeVisible();

    // 3. Tombol icon memiliki aria-label
    const menuBtn = page.locator('button[aria-label="Buka atau tutup menu navigasi"]');
    await expect(menuBtn).toHaveCount(1);
  });

  test('BACKLOG-021: navigasi keyboard form login via Tab dan Enter', async ({ page }) => {
    await page.goto('/login', { waitUntil: 'domcontentloaded' });

    // Fokus ke email input lalu tab ke password dan submit
    await page.focus('input[name="email"]');
    await page.keyboard.type('test@itg.ac.id');

    await page.keyboard.press('Tab');
    const isPassFocused = await page.evaluate(() => document.activeElement?.getAttribute('name') === 'password');
    expect(isPassFocused).toBe(true);

    await page.keyboard.type('wrongpassword');
    await page.keyboard.press('Enter');

    // Menghasilkan feedback validasi kredensial (form disubmit via Enter)
    await page.waitForLoadState('domcontentloaded');
    expect(page.url()).toContain('/login');
  });
});

test.describe('UI-020 — Sidebar navigasi (default terbuka, simetri ikon, ingat preferensi)', () => {
  const navWidth = (page: import('@playwright/test').Page) =>
    page.evaluate(() => Math.round((document.querySelector('nav[aria-label="Navigasi utama"]') as HTMLElement).getBoundingClientRect().width));

  test('desktop: sidebar terbuka secara default (label terlihat)', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    expect(await navWidth(page)).toBeGreaterThan(200);
    await expect(page.locator('nav span', { hasText: 'Dashboard' }).first()).toBeVisible();
  });

  test('saat ringkas, ikon terpusat dan sejajar dengan logo', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await page.locator('header button[aria-label="Buka atau tutup menu navigasi"]').click();
    await expect.poll(() => navWidth(page)).toBeLessThan(100);

    const m = await page.evaluate(() => {
      const nav = document.querySelector('nav[aria-label="Navigasi utama"]') as HTMLElement;
      const nr = nav.getBoundingClientRect();
      const navCenter = nr.left + nr.width / 2;
      const logo = (nav.querySelector('.w-10.h-10') as HTMLElement).getBoundingClientRect();
      const items = Array.from(nav.querySelectorAll('a,button')).filter(
        (el) => typeof (el as HTMLElement).className === 'string' &&
                (el as HTMLElement).className.includes('flex') &&
                (el as HTMLElement).offsetParent !== null
      );
      const deltas = items
        .map((el) => {
          const s = el.querySelector('svg');
          if (!s || (s as SVGElement).getBoundingClientRect().width === 0) return null;
          const r = s.getBoundingClientRect();
          return Math.round(r.left + r.width / 2 - navCenter);
        })
        .filter((v) => v !== null) as number[];
      return {
        logoDelta: Math.round(logo.left + logo.width / 2 - navCenter),
        maxIconDelta: deltas.length ? Math.max(...deltas.map((d) => Math.abs(d))) : 0,
      };
    });

    expect(Math.abs(m.logoDelta)).toBeLessThanOrEqual(1);
    expect(m.maxIconDelta).toBeLessThanOrEqual(1);
  });

  test('klik ikon grup saat ringkas membuka sidebar', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await page.locator('header button[aria-label="Buka atau tutup menu navigasi"]').click();
    await expect.poll(() => navWidth(page)).toBeLessThan(100);

    await page.locator('nav button[title="Kelola BKHM"]').click();
    await expect.poll(() => navWidth(page)).toBeGreaterThan(200);
  });

  test('preferensi ringkas diingat setelah reload', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await page.locator('header button[aria-label="Buka atau tutup menu navigasi"]').click();
    await expect.poll(() => navWidth(page)).toBeLessThan(100);

    await page.reload({ waitUntil: 'domcontentloaded' });
    await expect.poll(() => navWidth(page)).toBeLessThan(100);
  });

  test('mobile 360px: sidebar ringkas secara default', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.evaluate(() => localStorage.removeItem('skin.sidebarOpen')).catch(() => {});
    await page.setViewportSize({ width: 360, height: 640 });
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    expect(await navWidth(page)).toBeLessThan(100);
  });
});
