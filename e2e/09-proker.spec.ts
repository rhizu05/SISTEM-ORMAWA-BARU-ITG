import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

test.describe('FR-005 / FR-006 / BR-15 — Program Kerja Tahunan & Monitoring (status manual)', () => {
  test('Ormawa dapat membuat program kerja tahunan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    
    await page.goto('/proker/tambah', { waitUntil: 'domcontentloaded' });
    await expect(page.getByRole('heading', { name: /Tambah Program Kerja/i }).first()).toBeVisible();
    
    const uniqueId = Date.now();
    await page.fill('input[name="nama_proker"]', `Proker Ormawa ${uniqueId}`);
    await page.fill('input[name="rencana_pelaksanaan"]', '2026-10-15');
    await page.fill('textarea[name="deskripsi"]', `Deskripsi program kerja ${uniqueId}`);
    
    await page.click('button:has-text("Simpan Program Kerja")');
    
    await expect(page).toHaveURL(/.*proker/);
    await expect(page.locator('body')).toContainText(/berhasil/i);
    await expect(page.locator('table')).toContainText(`Proker Ormawa ${uniqueId}`);
    await expect(page.locator('table')).toContainText(/Rencana/i);
  });

  test('BEM dapat membuat program kerja tahunan', async ({ page }) => {
    await loginAs(page, 'bem');
    
    await page.goto('/proker/tambah', { waitUntil: 'domcontentloaded' });
    await expect(page.getByRole('heading', { name: /Tambah Program Kerja/i }).first()).toBeVisible();
    
    const uniqueId = Date.now();
    await page.fill('input[name="nama_proker"]', `Proker BEM ${uniqueId}`);
    await page.fill('input[name="rencana_pelaksanaan"]', '2026-11-20');
    await page.fill('textarea[name="deskripsi"]', `Deskripsi BEM proker ${uniqueId}`);
    
    await page.click('button:has-text("Simpan Program Kerja")');
    
    await expect(page).toHaveURL(/.*proker/);
    await expect(page.locator('body')).toContainText(/berhasil/i);
    await expect(page.locator('table')).toContainText(`Proker BEM ${uniqueId}`);
  });

  test('BPM dapat melihat daftar program kerja dan mengubah status manual', async ({ page }) => {
    // 1. Create a proker as ormawa first to ensure one exists
    await loginAs(page, 'ormawa');
    await page.goto('/proker/tambah', { waitUntil: 'domcontentloaded' });
    const uniqueId = Date.now();
    await page.fill('input[name="nama_proker"]', `Proker Monitoring ${uniqueId}`);
    await page.fill('input[name="rencana_pelaksanaan"]', '2026-12-01');
    await page.fill('textarea[name="deskripsi"]', `Untuk monitoring BPM ${uniqueId}`);
    await page.click('button:has-text("Simpan Program Kerja")');
    await expect(page.locator('body')).toContainText(/berhasil/i);

    // 2. Login as BPM and monitor/update
    await loginAs(page, 'bpm');
    await page.goto('/proker', { waitUntil: 'domcontentloaded' });
    await expect(page.getByRole('heading', { name: /Daftar Program Kerja/i }).first()).toBeVisible();
    
    // Find row
    const row = page.locator('tr', { hasText: `Proker Monitoring ${uniqueId}` }).first();
    await expect(row).toBeVisible();

    // Change status to terlaksana + add note
    await row.locator('select[name="status"]').selectOption('terlaksana');
    await row.locator('input[name="catatan_bpm"]').fill('Proker telah dievaluasi BPM');
    await row.locator('button:has-text("Simpan")').click();

    await expect(page.locator('body')).toContainText(/berhasil diperbarui/i);
    
    // Check updated row
    const updatedRow = page.locator('tr', { hasText: `Proker Monitoring ${uniqueId}` }).first();
    await expect(updatedRow).toContainText('Terlaksana');
    await expect(updatedRow).toContainText('Proker telah dievaluasi BPM');
  });

  test('BACKLOG-016: Ormawa tidak dapat membuat proker duplikat dengan nama dan tanggal yang sama', async ({ page }) => {
    await loginAs(page, 'ormawa');
    
    const uniqueId = Date.now();
    const namaProker = `Proker Unik ${uniqueId}`;
    const tglProker = '2026-11-25';

    // 1. Submit proker pertama
    await page.goto('/proker/tambah', { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="nama_proker"]', namaProker);
    await page.fill('input[name="rencana_pelaksanaan"]', tglProker);
    await page.fill('textarea[name="deskripsi"]', 'Deskripsi awal');
    await page.click('button:has-text("Simpan Program Kerja")');

    await expect(page).toHaveURL(/.*proker/);
    await expect(page.locator('body')).toContainText(/berhasil/i);

    // 2. Coba submit proker kedua dengan nama dan tanggal sama
    await page.goto('/proker/tambah', { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="nama_proker"]', namaProker);
    await page.fill('input[name="rencana_pelaksanaan"]', tglProker);
    await page.fill('textarea[name="deskripsi"]', 'Deskripsi duplikat');
    await page.click('button:has-text("Simpan Program Kerja")');

    // Harus ditolak dengan pesan error
    await expect(page.locator('body')).toContainText(/sudah terdaftar|duplikat/i);
  });
});
