import { test, expect } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

/**
 * PRD: FR-015 (Sistem Tiket Aspirasi), FR-016 (Tracking Status Aspirasi),
 *      FR-021 (Pusat Informasi Terpusat), BR-08 (aspirasi perlu validasi; identitas disimpan
 *      namun akses dibatasi; aspirasi ≠ konseling), SEC-05 (Privacy), UI-012 (Form Aspirasi).
 *
 * Kirim aspirasi lewat form tiket publik (URL: /layanan/aspirasi).
 * Field wajib: nim, nama_mahasiswa, email, no_hp, prodi, judul, isi.
 */
async function kirimAspirasiPublik(page: import('@playwright/test').Page, judul: string) {
  await page.goto('/layanan/aspirasi', { waitUntil: 'domcontentloaded' });
  await page.fill('input[name="nim"]', '12345678');
  await page.fill('input[name="nama_mahasiswa"]', 'Mahasiswa E2E');
  await page.fill('input[name="email"]', 'mahasiswa.e2e@test.com');
  await page.fill('input[name="no_hp"]', '081234567890');
  await page.selectOption('select[name="prodi"]', 'Teknik Informatika');
  await page.fill('input[name="judul"]', judul);
  await page.fill('textarea[name="isi"]', 'Isi aspirasi dari pengujian E2E.');
  await Promise.all([
    // Kirim email tiket bisa memblokir sesaat bila mailer SMTP offline, beri waktu lebih.
    page.waitForURL(/cek-status/, { timeout: 25000, waitUntil: 'domcontentloaded' }),
    page.click('button:has-text("Kirim Aspirasi")'),
  ]);
  await expect(page.locator('body')).toContainText(/berhasil dikirim/i, { timeout: 10000 });
}

test.describe('FR-015 / UI-012 — Pengiriman Aspirasi (Tiket Publik)', () => {
  test('form aspirasi publik dapat diakses tanpa login', async ({ page }) => {
    await page.goto('/layanan/aspirasi', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('input[name="nim"]')).toBeVisible();
    await expect(page.locator('input[name="nama_mahasiswa"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('select[name="prodi"]')).toBeVisible();
    await expect(page.locator('input[name="judul"]')).toBeVisible();
    await expect(page.locator('textarea[name="isi"]')).toBeVisible();
  });

  test('guest dapat mengirim aspirasi dan mendapat kode tiket', async ({ page }) => {
    await kirimAspirasiPublik(page, uniqueName('Aspirasi Guest'));
    await expect(page).toHaveURL(/cek-status/);
  });

  test('pengguna login dapat mengirim aspirasi', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await kirimAspirasiPublik(page, uniqueName('Aspirasi Ormawa'));
  });
});

test.describe('FR-016 — Tracking & Tindak Lanjut Aspirasi oleh BPM', () => {
  test('pengirim dapat melacak status aspirasinya via halaman cek status', async ({ page }) => {
    const judul = uniqueName('Tracking Aspirasi');
    await kirimAspirasiPublik(page, judul);
    // Redirect pasca-kirim membawa ke halaman cek-status tiket.
    await expect(page.locator('body')).toContainText(judul);
  });

  test('BPM dapat meneruskan aspirasi ke BKHM dengan catatan rekomendasi', async ({ page }) => {
    const judul = uniqueName('Kelola Aspirasi BPM');
    await kirimAspirasiPublik(page, judul);

    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/aspirasi');
    await expect(page.getByRole('heading', { name: /Kelola Aspirasi/ }).first()).toBeVisible();
    await expect(page.locator('text=RelationNotFoundException')).toHaveCount(0);

    const row = page.locator('tr', { hasText: judul }).first();
    await expect(row).toBeVisible();
    await row.locator('button:has-text("Teruskan ke BKHM")').first().click();
    await expect(page.locator('#modal-teruskan')).toBeVisible();
    await page.fill('#catatan_bpm_teruskan', 'Rekomendasi BPM untuk tindak lanjut BKHM.');
    await Promise.all([
      page.waitForNavigation(),
      page.click('#form-teruskan button[type="submit"]'),
    ]);
    await expect(page.locator('text=/berhasil/i').first()).toBeVisible();
  });

  test('SEC-05: aspirasi tersimpan & dapat ditindaklanjuti oleh BPM', async ({ page }) => {
    const judul = uniqueName('Aspirasi Privasi E2E');
    await kirimAspirasiPublik(page, judul);
    await expect(page.locator('body')).toContainText(judul);

    // BPM (penerima berwenang) dapat melihat data aspirasi.
    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/aspirasi');
    await expect(page.locator('body')).toContainText(judul);
  });
});

test.describe('FR-021 — Pusat Informasi & Regulasi', () => {
  test('BEM dapat mengajukan pengumuman (masuk antrean kurasi BKHM)', async ({ page }) => {
    await loginAs(page, 'bem');
    await gotoStable(page, '/informasi');
    await page.locator('button:has-text("Ajukan Berita / Agenda BEM")').click();

    const judul = uniqueName('Pengumuman E2E');
    await page.fill('#judul', judul);
    await page.fill('#isi', 'Ini adalah isi pengumuman dari BEM.');
    await Promise.all([
      page.waitForNavigation(),
      page.click('form[action$="/informasi/pengumuman"] button:has-text("Ajukan ke BKHM")'),
    ]);

    await expect(page.locator('text=/menunggu kurasi/i').first()).toBeVisible();
  });

  test('BPM dapat menerbitkan regulasi dengan kategori baku', async ({ page }) => {
    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/regulasi');
    await expect(page.locator('text=Pusat Regulasi')).toBeVisible();

    await gotoStable(page, '/bpm/regulasi/create');
    await expect(page.locator('text=Terbitkan Regulasi')).toBeVisible();
    await expect(page.locator('select[name="kategori"] option[value="Undang-Undang"]')).toHaveCount(1);
    await expect(page.locator('select[name="kategori"] option[value="Pengumuman"]')).toHaveCount(1);
    await expect(page.locator('select[name="kategori"] option[value="Pedoman"]')).toHaveCount(1);
    await expect(page.locator('input[name="file"]')).toBeVisible();
  });

  test('informasi & rapat dapat diakses pengguna login tanpa error', async ({ page }) => {
    test.setTimeout(90000);
    for (const role of ['ormawa', 'bem', 'bpm', 'bkhm', 'wr3'] as const) {
      await loginAs(page, role);
      await gotoStable(page, '/informasi');
      await expect(page.locator('body')).not.toContainText('BindingResolutionException');
      await gotoStable(page, '/rapat');
      await expect(page.locator('body')).not.toContainText('RouteNotFound');
    }
  });

  test('AC Increment 3 / UI-009: pusat informasi dapat diakses publik tanpa login', async ({ page }) => {
    const response = await page.goto('/informasi', { waitUntil: 'domcontentloaded' });
    expect(response?.status()).toBe(200);
    await expect(page.locator('body')).toContainText(/Pusat Informasi|Pengumuman/i);
  });
});

/**
 * FR-021 / UI-009 — Akses Pusat Informasi Publik Tanpa Login (BACKLOG-007).
 * Spec: docs/backlog/BACKLOG-007-informasi-publik/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-007-informasi-publik/01-analisis-informasi-publik.md
 */
test.describe('FR-021 / UI-009 — Akses Pusat Informasi Publik Tanpa Login (BACKLOG-007)', () => {
  test('TC-INFO-001: guest dapat mengakses /informasi (200) dan melihat tab pengumuman & regulasi', async ({ page }) => {
    const response = await page.goto('/informasi', { waitUntil: 'domcontentloaded' });
    expect(response?.status()).toBe(200);
    await expect(page.getByRole('heading', { name: /Pusat Informasi & Regulasi/i })).toBeVisible();
    await expect(page.locator('button:has-text("Berita & Pengumuman")')).toBeVisible();
    await expect(page.locator('button:has-text("Regulasi & Pedoman")')).toBeVisible();
  });

  test('TC-INFO-002: guest tidak melihat tombol aksi administratif (+ Tambah, Hapus)', async ({ page }) => {
    await page.goto('/informasi', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('button:has-text("+ Tambah Pengumuman")')).toHaveCount(0);
    await expect(page.locator('button:has-text("+ Tambah Regulasi")')).toHaveCount(0);
    await expect(page.locator('button:has-text("Hapus")')).toHaveCount(0);
  });

  test('TC-INFO-003: mutasi pengumuman & regulasi ditolak jika belum login', async ({ page }) => {
    const resPengumuman = await page.request.post('/informasi/pengumuman', {
      data: { judul: 'Hack', isi: 'Hack isi' },
      maxRedirects: 0,
    });
    expect([302, 401, 419]).toContain(resPengumuman.status());

    const resRegulasi = await page.request.post('/informasi/regulasi', {
      data: { judul: 'Hack Reg', kategori: 'Pedoman', deskripsi: 'Hack desk' },
      maxRedirects: 0,
    });
    expect([302, 401, 419]).toContain(resRegulasi.status());
  });

  test('TC-INFO-004: guest dapat beralih ke tab regulasi dan melihat daftar regulasi', async ({ page }) => {
    await page.goto('/informasi', { waitUntil: 'domcontentloaded' });
    await page.click('button:has-text("Regulasi & Pedoman")');
    await expect(page.locator('th:has-text("Judul Dokumen")')).toBeVisible();
    await expect(page.locator('th:has-text("Kategori")')).toBeVisible();
  });
});

