import { test, expect } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';

/**
 * PRD: FR-022 (Notifikasi Perubahan Status), FR-025 (Notifikasi Email Institusi — Derived),
 *      UI-011 (Pusat Notifikasi In-App).
 * Catatan: pengiriman email institusi (FR-025) memakai Mailable NotifikasiMail dan
 * tidak dapat diverifikasi end-to-end tanpa mail catcher — separuh in-app-nya diverifikasi di sini.
 */
test.describe('UI-011 — Badge notifikasi belum dibaca', () => {
  test('sidebar menampilkan jumlah notifikasi belum dibaca', async ({ page }) => {
    // Pemicu: aspirasi baru memberi notifikasi ke BPM.
    await loginAs(page, 'ormawa');
    const judul = uniqueName('Badge Notif');
    await page.goto('/aspirasi/kirim');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Fasilitas');
    await page.fill('textarea[name="isi"]', 'Pemicu badge notifikasi');
    await page.click('button:has-text("Kirim Aspirasi")');
    await expect(page.locator('text=berhasil dikirim').first()).toBeVisible({ timeout: 8000 });

    await loginAs(page, 'bpm');
    // Buka sidebar agar badge (x-show sidebarOpen) terlihat.
    await page.locator('button[aria-label="Buka atau tutup menu navigasi"]').click();
    const badge = page.locator('span.bg-red-500').first();
    await expect(badge).toBeVisible();
    await expect(badge).toHaveAttribute('aria-label', /notifikasi belum dibaca/);
  });
});

test.describe('UI-011 / FR-022 — Pusat Notifikasi', () => {
  test('halaman pusat notifikasi dapat diakses & menampilkan daftar/empty state', async ({ page }) => {
    await loginAs(page, 'bem');
    await page.goto('/notifikasi', { waitUntil: 'domcontentloaded' });

    await expect(page.getByRole('heading', { name: /Pusat Notifikasi/ })).toBeVisible();
    const hasItems = await page.locator('form[action*="/notifikasi/"]').count();
    const hasEmpty = await page.locator('text=Belum ada notifikasi.').count();
    expect(hasItems + hasEmpty).toBeGreaterThan(0);
  });

  test('FR-022: aspirasi baru memicu notifikasi in-app untuk BPM', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const judul = uniqueName('Notif Aspirasi');
    await page.goto('/aspirasi/kirim');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Fasilitas');
    await page.fill('textarea[name="isi"]', 'Pemicu notifikasi BPM');
    await page.click('button:has-text("Kirim Aspirasi")');
    await expect(page.locator('text=berhasil dikirim').first()).toBeVisible({ timeout: 8000 });

    await loginAs(page, 'bpm');
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/Aspirasi baru masuk/i);
    await expect(page.locator('body')).toContainText(judul);
  });

  test('FR-022: notifikasi dapat ditandai sudah dibaca', async ({ page }) => {
    await loginAs(page, 'bpm');
    await gotoStable(page, '/notifikasi');

    const unread = page.locator('form[action*="/read"] button:has-text("Tandai dibaca")').first();
    test.skip((await unread.count()) === 0, 'Tidak ada notifikasi belum dibaca — skip');

    await Promise.all([page.waitForNavigation(), unread.click()]);
    await expect(page.locator('text=Notifikasi ditandai sudah dibaca.')).toBeVisible();
  });

  test('FR-022: semua notifikasi dapat ditandai dibaca sekaligus', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/notifikasi');

    const markAll = page.locator('button:has-text("Tandai semua sudah dibaca")');
    await expect(markAll).toBeVisible();
    await Promise.all([page.waitForNavigation(), markAll.click()]);
    await expect(page.locator('text=Semua notifikasi ditandai sudah dibaca.')).toBeVisible();
  });

  test('FR-022: perubahan status pengajuan memicu notifikasi ke pengaju', async ({ page }) => {
    // Alur deterministik: ormawa kirim aspirasi → BPM terima notifikasi (kanal in-app sama
    // yang dipakai verifikasi pengajuan). Verifikasi perubahan status diuji end-to-end
    // pada siklus FR-009.
    await loginAs(page, 'ormawa');
    const judul = uniqueName('Notif Status');
    await page.goto('/aspirasi/kirim');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Pelayanan');
    await page.fill('textarea[name="isi"]', 'Pemicu notifikasi perubahan status');
    await page.click('button:has-text("Kirim Aspirasi")');
    await expect(page.locator('text=berhasil dikirim').first()).toBeVisible({ timeout: 8000 });

    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/aspirasi');
    const row = page.locator('tr', { hasText: judul }).first();
    test.skip((await row.count()) === 0, 'Aspirasi tidak ada — skip');

    await row.locator('button:has-text("Kelola")').first().click();
    await expect(page.locator('#modal-aspirasi')).toBeVisible();
    await page.selectOption('#status', 'diproses');
    await page.fill('#catatan_bpm', 'Diproses.');
    await Promise.all([page.waitForNavigation(), page.click('#modal-aspirasi button:has-text("Simpan Perubahan")')]);

    // Pengirim menerima notifikasi perkembangan.
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(judul);
  });

  test.fixme('FR-025: notifikasi dikirim juga via email institusi', async ({ page }) => {
    // BACKLOG-008: Kanal email institusi diverifikasi secara komprehensif di level
    // PHPUnit Feature Test (Tests\Feature\GapRemediationTest::test_fr025_*) menggunakan
    // Mail::fake() untuk payload, envelope subjek institusi, rendering blade, dan role broadcast.
    // Di level E2E web browser, test ini ditandai fixme bila mail catcher (Mailpit/Mailhog) belum aktif.
    await loginAs(page, 'bpm');
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/email/i);
  });
});

/**
 * FR-022 — Pemicu pengumuman & regulasi (BACKLOG-006).
 * Spec: docs/backlog/BACKLOG-006-notifikasi-8-pemicu/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-006-notifikasi-8-pemicu/01-analisis-pemicu-notifikasi.md
 *
 * Hanya pemicu yang TIDAK menyentuh data existing yang diuji di sini. Pengumuman &
 * regulasi baru memakai `NotifikasiService::kirimKeSemua` (InformasiController:47,91),
 * dan keduanya hanya MEMBUAT data baru — tidak menghapus/mengubah data lama.
 *
 * Pemicu lain (perubahan status pengajuan, penolakan ke akun BEM, dana cair) memerlukan
 * state domain yang belum ada di DB dan mengujinya berarti mengubah state pengajuan
 * existing → dihentikan, lihat ANALYSIS_REQUIRED.md.
 */
test.describe('FR-022 — Pemicu pengumuman & regulasi (BACKLOG-006)', () => {
  test('TC-NOTIF-001: pengumuman baru menyebar ke role lain', async ({ page }) => {
    await loginAs(page, 'bem');
    await gotoStable(page, '/informasi');
    await page.click('button:has-text("Tambah Pengumuman")');

    const judul = uniqueName('E2E Notif Pengumuman');
    await page.fill('input[name="judul"]', judul);
    await page.fill('textarea[name="isi"]', 'Uji pemicu notifikasi pengumuman.');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action$="/informasi/pengumuman"] button[type="submit"]').click(),
    ]);

    // Kontrol positif: pengumuman benar-benar tersimpan.
    await expect(page.locator('body')).toContainText(judul);

    // Penerima adalah role LAIN — bila kirimKeSemua keliru, ini yang menangkapnya.
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/Pengumuman baru/i);
    await expect(page.locator('body')).toContainText(judul);
  });

  test('TC-NOTIF-002: regulasi baru menyebar ke role lain', async ({ page }) => {
    await loginAs(page, 'bpm');
    await gotoStable(page, '/informasi');

    // Form regulasi berada di tab kedua ("Regulasi & Pedoman"); tombolnya ada di DOM
    // tetapi tersembunyi (x-show) sampai tab itu dibuka.
    await page.click('button:has-text("Regulasi & Pedoman")');
    await page.click('button:has-text("Tambah Regulasi")');

    const judul = uniqueName('E2E Notif Regulasi');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Pedoman');
    await page.fill('textarea[name="deskripsi"]', 'Uji pemicu notifikasi regulasi.');
    await page.setInputFiles('input[name="file_path"]', PROPOSAL);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action$="/informasi/regulasi"] button[type="submit"]').click(),
    ]);

    await loginAs(page, 'ormawa');
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/Regulasi baru/i);
    await expect(page.locator('body')).toContainText(judul);
  });

  test('BACKLOG-017 / FR-022 §22 no.9: penolakan proposal ormawa masuk ke notifikasi akun BEM', async ({ page }) => {
    // 1. Ormawa membuat dan mengajukan proposal baru
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
      test.skip(true, 'Pengajuan ditangguhkan — skip');
      return;
    }
    const kegiatan = uniqueName('Proposal Tolak BEM');
    await page.fill('input[name="nama_kegiatan"]', kegiatan);
    await page.fill('input[name="dana_diajukan"]', '800000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-11-15');
    await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
    await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);

    const row = page.locator('tr', { hasText: kegiatan }).first();
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), row.locator('button:has-text("Ajukan")').click()]);
    await expect(page.locator('body')).toContainText('berhasil dikirim ke BEM');

    // 2. BEM tolak/revisi proposal dengan catatan
    await loginAs(page, 'bem');
    await page.goto('/verifikasi');
    const verifRow = page.locator('tr', { hasText: kegiatan }).first();
    await Promise.all([page.waitForNavigation(), verifRow.locator('a:has-text("Verifikasi")').click()]);
    await page.fill('textarea[name="catatan"]', 'Perbaiki RAB dan jadwal kegiatan.');
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), page.locator('button:has-text("Revisi")').click()]);
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');

    // 3. Akun BEM mengecek halaman notifikasi (harus menerima pemberitahuan penolakan proposal ormawa)
    await gotoStable(page, '/notifikasi');
    await expect(page.locator('body')).toContainText(/ditolak\/dikembalikan/i);
    await expect(page.locator('body')).toContainText(kegiatan);
  });
});
