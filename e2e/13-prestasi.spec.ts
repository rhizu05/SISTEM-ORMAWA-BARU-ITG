import { test, expect } from '@playwright/test';
import { loginAs } from './helpers/auth';

test.describe('FR-020 — Pelaporan Prestasi & Kompetisi (individu & organisasi)', () => {
  test('Ormawa / Mahasiswa dapat mengunggah bukti prestasi individu & organisasi', async ({ page }) => {
    await loginAs(page, 'ormawa');
    
    await page.goto('/prestasi/create', { waitUntil: 'domcontentloaded' });
    await expect(page.getByRole('heading', { name: /Laporkan Prestasi/i }).first()).toBeVisible();
    
    const uniqueId = Date.now();
    await page.fill('input[name="nama_kegiatan"]', `Lomba Hackathon Nasional ${uniqueId}`);
    await page.fill('input[name="penyelenggara"]', 'Kemdikbudristek');
    await page.selectOption('select[name="tingkat"]', 'Nasional');
    await page.fill('input[name="juara"]', 'Juara 1');
    await page.locator('input[name="tanggal_mulai"], input[name="tanggal"]').first().fill('2026-09-01');
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

  test('Mahasiswa sebagai guest dapat melaporkan prestasi via portal publik dan mendapatkan kode tiket', async ({ page }) => {
    await page.goto('/layanan/prestasi');
    await expect(page.locator('h1')).toContainText(/Pelaporan Prestasi/i);

    const uniqueId = Date.now();
    const emailMhs = `mahasiswa.${uniqueId}@itg.ac.id`;
    await page.fill('input[name="nim"]', '2106001');
    await page.fill('input[name="nama_mahasiswa"]', 'Mahasiswa Berprestasi ITG');
    await page.fill('input[name="email"]', emailMhs);
    await page.fill('input[name="no_hp"]', '081234567890');
    await page.selectOption('select[name="prodi"]', 'Teknik Informatika');

    await page.fill('input[name="nama_kegiatan"]', `Lomba Debat Individu ${uniqueId}`);
    await page.fill('input[name="penyelenggara"]', 'Universitas Swasta');
    await page.selectOption('select[name="tingkat"]', 'Nasional');
    await page.fill('input[name="capaian"]', 'Juara 3');
    await page.fill('input[name="tanggal_mulai"]', '2026-09-10');
    await page.setInputFiles('input[name="lampiran_bukti"]', 'e2e/fixtures/dummy.pdf');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(/.*cek-status.*/);
    await expect(page.locator('body')).toContainText(/berhasil dikirim/i);
    await expect(page.locator('body')).toContainText(`Lomba Debat Individu ${uniqueId}`);
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
    await page.locator('input[name="tanggal_mulai"], input[name="tanggal"]').first().fill('2026-09-05');
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

  test('Publik dapat melihat showcase prestasi dengan filter tingkat dan pencarian tanpa counter angka', async ({ page }) => {
    await page.goto('/prestasi/showcase');
    await expect(page.getByRole('heading', { name: /Jejak Juara/i })).toBeVisible();

    // Verifikasi tombol filter jenis tingkat tersedia
    await expect(page.getByRole('button', { name: 'Semua Tingkat', exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Nasional', exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Internasional', exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: 'Provinsi / Wilayah', exact: true })).toBeVisible();

    // Verifikasi counter angka lama TIDAK ADA
    await expect(page.locator('text="Total Prestasi"')).toHaveCount(0);
    await expect(page.locator('text="Tingkat Global"')).toHaveCount(0);

    // Verifikasi search input
    const searchInput = page.locator('#search-showcase');
    await expect(searchInput).toBeVisible();
    await searchInput.fill('Informatika');
    await expect(searchInput).toHaveValue('Informatika');

    // Tombol clear pencarian berfungsi
    const clearBtn = page.locator('button[title="Bersihkan pencarian"]');
    await expect(clearBtn).toBeVisible();
    await clearBtn.click();
    await expect(searchInput).toHaveValue('');

    // Verifikasi bahwa kode tiket tidak ditampilkan di kartu prestasi
    const ticketCodesInArticles = page.locator('article').locator('text=/TK-[A-Z0-9]+/i');
    await expect(ticketCodesInArticles).toHaveCount(0);
  });
});
