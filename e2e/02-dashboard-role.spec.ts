import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';
import { expectCalendarLocaleId, expectNoRouteError } from './helpers/assertions';

/**
 * PRD: FR-003 (Dashboard Ormawa), FR-004 (Dashboard Eksekutif WR3 & BKHM),
 *      UI-001…UI-005 & UI-007/UI-008 (dashboard per role), UI-020 (UI sesuai hak akses).
 */
test.describe('FR-003 / FR-004 — Dashboard per Role', () => {
  test('UI-001 / FR-003: Dashboard Ormawa menampilkan saldo, proposal, kalender', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await expect(page.getByRole('heading', { name: /Dashboard Ormawa/ })).toBeVisible();
    await expect(page.getByText('Sisa Saldo Tersedia').first()).toBeVisible();
    await expect(page.locator('#calendar')).toBeVisible();
    await expect(page.locator('script:has-text("locale")')).toBeAttached().catch(async () => {
      await expectCalendarLocaleId(page);
    });
    await expectNoRouteError(page);
  });

  test('UI-002: Dashboard BEM menampilkan agenda, kartu dana, verifikasi, dan kalender', async ({ page }) => {
    await loginAs(page, 'bem');
    await expect(page.getByRole('heading', { name: /Dashboard BEM/ })).toBeVisible();
    await expect(page.locator('text=Selamat Datang kembali')).toBeVisible();
    await expect(page.getByRole('heading', { name: /Agenda Rapat/ })).toBeVisible();
    await expect(page.locator('text=Status Dana Anda')).toBeVisible();
    await expect(page.locator('text=Total Saldo Diberikan')).toBeVisible();
    await expect(page.locator('text=Saldo Terpakai & Diproses')).toBeVisible();
    await expect(page.locator('text=Sisa Saldo Tersedia')).toBeVisible();
    await expect(page.locator('text=Verifikasi Proposal').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: /Verifikasi Proposal/ })).toBeVisible();
    await expect(page.locator('text=Jadwal Terpadu Fasilitas')).toBeVisible();
    await expectCalendarLocaleId(page);
  });

  test('UI-003: Dashboard BPM menampilkan status dana & antrean verifikasi', async ({ page }) => {
    await loginAs(page, 'bpm');
    await expect(page.getByRole('heading', { name: /Dashboard BPM/ })).toBeVisible();
    await expect(page.locator('text=Selamat Datang, Presidium & Anggota BPM ITG')).toBeVisible();
    await expect(page.locator('text=Total Saldo Diberikan')).toBeVisible();
    await expect(page.locator('text=Verifikasi Proposal').first()).toBeVisible();
    await expectCalendarLocaleId(page);
  });

  test('UI-004 / FR-004: Dashboard BKHM menampilkan 5 antrean verifikasi + kalender', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await expect(page.getByRole('heading', { name: /Dashboard BKHM/ })).toBeVisible();
    const main = page.locator('main');
    await expect(main.locator('text=Verifikasi Proposal').first()).toBeVisible();
    await expect(main.locator('text=Verifikasi LPJ').first()).toBeVisible();
    await expect(main.locator('text=Siap Bendahara').first()).toBeVisible();
    await expect(main.locator('text=Verifikasi Tempat').first()).toBeVisible();
    await expect(main.locator('text=Verifikasi Barang').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: /Verifikasi Proposal/ })).toBeVisible();
    await expect(page.locator('#calendar')).toBeVisible();
  });

  test('UI-005 / FR-004: Dashboard WR3 menampilkan rincian saldo real dari DB', async ({ page }) => {
    await loginAs(page, 'wr3');
    await expect(page.getByRole('heading', { name: /Dashboard WR3/ })).toBeVisible();
    await expect(page.locator('text=Selamat Datang, Wakil Rektor III')).toBeVisible();
    await expect(page.getByRole('heading', { name: /Agenda Rapat/ })).toBeVisible();
    await expect(page.locator('text=Verifikasi Proposal').first()).toBeVisible();
    await expect(page.getByRole('heading', { name: /Antrean Persetujuan Proposal/ })).toBeVisible();
    await expect(page.locator('text=Monitoring Saldo Kas Ormawa & Lembaga').first()).toBeVisible();
    // Kolom harus dinamis dari DB (bukan contoh hard-code).
    await expect(page.locator('th:has-text("Nama Ormawa")')).toBeVisible();
    await expect(page.locator('th:has-text("Saldo Awal")')).toBeVisible();
    await expect(page.locator('th:has-text("Sisa Saldo")')).toBeVisible();
    const rows = page.locator('table:has(th:has-text("Saldo Awal")) tbody tr');
    await expect(rows.first()).toBeVisible().catch(async () => {
      await expect(page.locator('text=Belum ada data saldo')).toBeVisible();
    });
  });

  test('UI-006: Dashboard Bendahara menampilkan daftar proposal siap dicairkan', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await expect(page.getByRole('heading', { name: /Dashboard Bendahara/ })).toBeVisible();
    await expect(page.locator('text=Pencairan Dana, text=Proposal Siap Cair').first()).toBeVisible().catch(async () => {
      await expect(page.locator('text=Proposal Siap Cair')).toBeVisible();
    });
    await expect(page.locator('text=Daftar Proposal Siap Dicairkan')).toBeVisible();
    await expect(page.locator('table').first()).toBeVisible();
    await expect(page.locator('th:has-text("Nama Kegiatan")')).toBeVisible();
    await expect(page.locator('th:has-text("Ormawa")')).toBeVisible();
  });

  test('UI-007: Dashboard Sarpras dapat diakses', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await expect(page.locator('body')).toContainText(/Dashboard/i);
    await expect(page.locator('text=Kelola Sarpras').first()).toBeAttached();
  });

  test('UI-008: Portal Layanan Publik Mahasiswa menampilkan kanal layanan & pelacakan tiket', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('body')).toContainText(/Portal Layanan Mahasiswa|Layanan Terpadu & Kanal Aspirasi/i);
    await expect(page.getByRole('heading', { name: 'Kanal Aspirasi Mahasiswa', exact: true })).toBeAttached();
    await expect(page.getByRole('heading', { name: 'Konseling Personal BKHM', exact: true })).toBeAttached();
    await expect(page.getByRole('heading', { name: 'Prestasi & Dana Lomba', exact: true })).toBeAttached();
    await expect(page.locator('text=Sudah Memiliki Kode Tiket Layanan?')).toBeAttached();
  });

  test('tanpa RouteNotFound pada dashboard WR3 (memakai /dashboard global)', async ({ page }) => {
    await loginAs(page, 'wr3');
    await page.goto('/dashboard');
    await expect(page.locator('text=RouteNotFoundException')).toHaveCount(0);
    await expect(page.locator('button:has-text("Kelola WR3")').first()).toBeAttached();
  });
});

/**
 * UI-020 — Menu/aksi tersaring sesuai hak akses (role-based sidebar).
 */
test.describe('UI-020 — Sidebar sesuai Hak Akses', () => {
  test('Ormawa melihat Pengajuan, Sarpras, Persuratan Digital', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await expect(page.locator('button:has-text("Pengajuan")').first()).toBeAttached();
    await expect(page.locator('button:has-text("Sarpras")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Buat Pengajuan")').first()).toBeAttached();
    await expect(page.locator('text=Tempat & Fasilitas')).toBeAttached();
    await expect(page.locator('text=Sarana & Barang')).toBeAttached();
    await expect(page.locator('button:has-text("Persuratan Digital")').first()).toBeAttached();
  });

  test('BEM melihat tab Ormawa (Pengajuan + Sarpras)', async ({ page }) => {
    await loginAs(page, 'bem');
    await expect(page.locator('button:has-text("Pengajuan")').first()).toBeAttached();
    await expect(page.locator('button:has-text("Sarpras")').first()).toBeAttached();
  });

  test('BKHM melihat "Kelola BKHM" dan tidak melihat Persuratan/Laporan/Informasi', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await expect(page.locator('span:has-text("Kelola BKHM")')).toBeAttached();
    await expect(page.getByRole('button', { name: /Persuratan Digital/ })).toHaveCount(0);
  });

  test('BPM melihat section "Kelola BPM"', async ({ page }) => {
    await loginAs(page, 'bpm');
    await expect(page.locator('button:has-text("Kelola BPM")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Dashboard")').first()).toBeAttached();
  });

  test('WR3 melihat "Kelola WR3" dengan Dashboard WR3', async ({ page }) => {
    await loginAs(page, 'wr3');
    await expect(page.locator('button:has-text("Kelola WR3")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Dashboard")').first()).toBeAttached();
  });

  test('Bendahara: link Dashboard terlihat (regression sidebar tersembunyi)', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await expect(page.locator('a:has-text("Dashboard")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Profil")').first()).toBeAttached();
  });

  test('Sarpras melihat "Kelola Sarpras" (role disatukan)', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await expect(page.locator('button:has-text("Kelola Sarpras")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Jadwal Perkuliahan")')).toBeAttached();
  });

  test('Mahasiswa sebagai Guest diarahkan ke login saat mengakses dashboard internal', async ({ page }) => {
    await page.goto('/dashboard');
    await expect(page).toHaveURL(/.*login.*/);
  });
});
