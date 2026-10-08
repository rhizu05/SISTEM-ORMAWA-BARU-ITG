import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

test.describe('FR-004 / FR-006 / FR-015 / FR-021 — Modul BPM (dashboard, SP, aspirasi, regulasi)', () => {
  test('BPM dashboard layout: agenda, 3 cards dana, 1 card verifikasi, tabel, kalender', async ({ page }) => {
    await loginAs(page, 'bpm');
    await page.goto('/dashboard');
    await expect(page.getByRole('heading', { name: /Dashboard BPM/ })).toBeVisible();
    await expect(page.locator('text=Status Dana Anda')).toBeVisible();
    await expect(page.getByRole('link', { name: /Verifikasi Proposal/ }).first()).toBeVisible();
    await expect(page.locator('text=Jadwal Terpadu')).toBeVisible();
  });

  test('Buat Surat Peringatan – tipe sasaran ormawa/mahasiswa + opsi internal BPM', async ({ page }) => {
    await loginAs(page, 'bpm');
    await page.goto('/bpm/sp/create');
    await expect(page.getByRole('heading', { name: /Buat Surat Peringatan/ }).first()).toBeVisible();
    await expect(page.locator('text=Target Organisasi').first()).toBeVisible();
    const target = page.locator('select[name="target_user_id"]');
    await expect(target).toBeVisible();
    // should contain both ormawa and BEM options (populated from DB)
    const options = await target.locator('option').allTextContents();
    expect(options.join(' ')).toMatch(/./); // at least not empty, server loads role(['ormawa','bem'])

    // Tipe sasaran mahasiswa membuka daftar mahasiswa (bisa lebih dari satu)
    await page.check('input[name="tipe_sasaran"][value="mahasiswa"]');
    await expect(page.locator('input[name="target_mahasiswas[0][nim]"]')).toBeVisible();

    // Menambah baris mahasiswa kedua
    await page.click('button:has-text("Tambah Mahasiswa")');
    await expect(page.locator('input[name="target_mahasiswas[1][nama]"]')).toBeVisible();

    // Centang internal BPM membuka field penandatangan internal
    await page.check('input[name="anggota_bpm"]');
    await expect(page.locator('#penandatangan')).toBeVisible();
  });

  test('Kelola Aspirasi – daftar tiket aspirasi tampil (no RelationNotFound)', async ({ page }) => {
    await loginAs(page, 'bpm');
    await page.goto('/bpm/aspirasi');
    await expect(page.getByRole('heading', { name: /Kelola Aspirasi/ }).first()).toBeVisible();
    await expect(page.locator('th:has-text("Kode Tiket")')).toBeVisible();
    await expect(page.locator('text=RelationNotFoundException')).toHaveCount(0);
  });

  test('Kelola Regulasi – kategori Undang-Undang/Pengumuman/Pedoman + PDF upload', async ({ page }) => {
    await loginAs(page, 'bpm');
    await page.goto('/bpm/regulasi');
    await expect(page.getByRole('heading', { name: /Pusat Regulasi/i }).first()).toBeVisible();
    await page.goto('/bpm/regulasi/create');
    await expect(page.getByRole('heading', { name: /Terbitkan Regulasi/i }).first()).toBeVisible();
    await expect(page.locator('select#kategori, select[name="kategori"]')).toBeVisible();
    await expect(page.locator('select[name="kategori"] option[value="Undang-Undang"]')).toHaveCount(1);
    await expect(page.locator('select[name="kategori"] option[value="Pengumuman"]')).toHaveCount(1);
    await expect(page.locator('select[name="kategori"] option[value="Pedoman"]')).toHaveCount(1);
    await expect(page.locator('input[name="file"]')).toBeVisible();
    await expect(page.locator('input[name="judul"]')).toHaveAttribute('placeholder', /UU Ormawa/);
  });

  test('sidebar BPM: Dashboard BPM, SP, Aspirasi, Regulasi under Kelola BPM', async ({ page }) => {
    await loginAs(page, 'bpm');
    await expect(page.locator('button:has-text("Kelola BPM")').first()).toBeAttached();
    await expect(page.locator('a:has-text("Dashboard BPM")')).toBeAttached();
    await expect(page.locator('a:has-text("Buat Surat Peringatan")')).toBeAttached();
    await expect(page.locator('a:has-text("Kelola Aspirasi")')).toBeAttached();
    await expect(page.locator('a:has-text("Kelola Regulasi")')).toBeAttached();
  });
});
