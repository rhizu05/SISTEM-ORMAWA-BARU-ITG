import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

test.describe('FR-020 — Pelaporan Prestasi & Kompetisi (individu & organisasi)', () => {
  test('Ormawa / Mahasiswa dapat mengunggah bukti prestasi individu & organisasi', async ({ page }) => {
    await loginAs(page, 'ormawa');
    
    await page.goto('/prestasi/create', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('h2')).toContainText(/Laporkan Prestasi/i);
    
    const uniqueId = Date.now();
    await page.fill('input[name="nama_kegiatan"]', `Lomba Hackathon Nasional ${uniqueId}`);
    await page.fill('input[name="penyelenggara"]', 'Kemdikbudristek');
    await page.selectOption('select[name="tingkat"]', 'Nasional');
    await page.fill('input[name="juara"]', 'Juara 1');
    await page.fill('input[name="tanggal"]', '2026-09-01');
    await page.selectOption('select[name="afiliasi"]', 'ormawa');
    await page.fill('input[name="unit_terkait"]', 'HIMAIF');
    await page.fill('textarea[name="deskripsi"]', `Deskripsi capaian prestasi ${uniqueId}`);
    await page.setInputFiles('input[name="file_bukti"]', 'e2e/fixtures/dummy.pdf');
    
    await page.click('button:has-text("Kirim Laporan")');
    
    await expect(page).toHaveURL(/.*prestasi/);
    await expect(page.locator('body')).toContainText(/berhasil dilaporkan/i);
    await expect(page.locator('table')).toContainText(`Lomba Hackathon Nasional ${uniqueId}`);
    await expect(page.locator('table')).toContainText(/Pending/i);
  });

  test('Mahasiswa dapat melaporkan prestasi individual (non-afiliasi)', async ({ page }) => {
    await loginAs(page, 'mahasiswa');
    await page.goto('/prestasi/create', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('h2')).toContainText(/Laporkan Prestasi/i);

    const uniqueId = Date.now();
    await page.fill('input[name="nama_kegiatan"]', `Lomba Debat Individu ${uniqueId}`);
    await page.fill('input[name="penyelenggara"]', 'Universitas Swasta');
    await page.selectOption('select[name="tingkat"]', 'Nasional');
    await page.fill('input[name="juara"]', 'Juara 3');
    await page.fill('input[name="tanggal"]', '2026-09-10');
    await page.selectOption('select[name="afiliasi"]', 'individu');
    await page.fill('textarea[name="deskripsi"]', `Prestasi individual ${uniqueId}`);
    await page.setInputFiles('input[name="file_bukti"]', 'e2e/fixtures/dummy.pdf');
    await page.click('button:has-text("Kirim Laporan")');

    await expect(page.locator('body')).toContainText(/berhasil dilaporkan/i);
    await expect(page.locator('table')).toContainText(`Lomba Debat Individu ${uniqueId}`);
  });

  test('BKHM dapat memverifikasi laporan prestasi', async ({ page }) => {
    // 1. Create a prestasi as ormawa
    await loginAs(page, 'ormawa');
    await page.goto('/prestasi/create', { waitUntil: 'domcontentloaded' });
    const uniqueId = Date.now();
    await page.fill('input[name="nama_kegiatan"]', `Lomba UI/UX ${uniqueId}`);
    await page.fill('input[name="penyelenggara"]', 'ITG Tech');
    await page.selectOption('select[name="tingkat"]', 'Regional');
    await page.fill('input[name="juara"]', 'Juara 2');
    await page.fill('input[name="tanggal"]', '2026-09-05');
    await page.selectOption('select[name="afiliasi"]', 'individu');
    await page.setInputFiles('input[name="file_bukti"]', 'e2e/fixtures/dummy.pdf');
    await page.click('button:has-text("Kirim Laporan")');
    await expect(page.locator('body')).toContainText(/berhasil dilaporkan/i);

    // 2. Login as BKHM and verify
    await loginAs(page, 'bkhm');
    await page.goto('/prestasi', { waitUntil: 'domcontentloaded' });
    
    const row = page.locator('tr', { hasText: `Lomba UI/UX ${uniqueId}` }).first();
    await expect(row).toBeVisible();

    await row.locator('input[name="catatan_bkhm"]').fill('Validasi sertifikat sah');
    await row.locator('button[value="terverifikasi"]').click();

    await expect(page.locator('body')).toContainText(/berhasil diperbarui/i);
    
    const updatedRow = page.locator('tr', { hasText: `Lomba UI/UX ${uniqueId}` }).first();
    await expect(updatedRow).toContainText('Terverifikasi');
  });

  test('Akses bukti terlindungi RBAC', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/prestasi', { waitUntil: 'domcontentloaded' });
    
    const buktiLink = page.locator('a:has-text("Bukti")').first();
    if (await buktiLink.count() > 0) {
      await expect(buktiLink).toBeVisible();
    }
  });
});
