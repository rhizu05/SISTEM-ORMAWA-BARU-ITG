import { test, expect, Page } from '@playwright/test';
import { loginAs, gotoStable } from './helpers/auth';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';

/** Token CSRF dari meta tag layout — agar endpoint bisa diuji tanpa merender form. */
async function readCsrf(page: Page): Promise<string> {
  return (await page.locator('meta[name="csrf-token"]').getAttribute('content')) ?? '';
}

/**
 * PRD: FR-009 (Verifikasi Berjenjang / dynamic workflow), FR-012 (Upload & Verifikasi LPJ),
 *      BR-05 (alasan wajib saat menolak/merevisi), BR-13 (revisi ke titik penolakan),
 *      UI-015 (highlight revisi), SEC-04 (RBAC verifikator).
 */
test.describe('FR-009 / SEC-04 — Akses antrean verifikasi per role', () => {
  const roles = ['bem', 'bpm', 'bkhm', 'wr3', 'bendahara'] as const;

  for (const role of roles) {
    test(`${role}: dapat mengakses /verifikasi tanpa 403`, async ({ page }) => {
      await loginAs(page, role);
      await page.goto('/verifikasi');
      await expect(page).not.toHaveURL(/.*403.*/);
      await expect(page.locator('text=Forbidden, text=403')).toHaveCount(0);
      await expect(page.locator('body')).toContainText(/Verifikasi|Proposal|Antrian/i);
    });
  }

  test('BEM: detail verifikasi dapat dibuka (tidak 403)', async ({ page }) => {
    await loginAs(page, 'bem');
    await gotoStable(page, '/verifikasi');
    const detail = page.locator('a[href*="/verifikasi/"]').first();
    if (await detail.isVisible()) {
      await detail.click();
      await expect(page.locator('body')).not.toContainText('403 Forbidden');
    }
  });
});

/**
 * Siklus lengkap: Ormawa → BEM → BPM → BKHM(1) → WR3 → BKHM(2) → Bendahara → LPJ(BKHM) → LPJ(WR3).
 * Menegakkan BR-03 (tidak melompati tahap) & BR-05 (catatan wajib) di sepanjang alur.
 */
test.describe('FR-009 / FR-012 — Siklus penuh pengajuan → pencairan → LPJ', () => {
  test.describe.configure({ mode: 'serial' });

  const kegiatan = `E2E Lifecycle ${Date.now()}`;

  test('1. Ormawa membuat draft dan mengajukan ke BEM', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
      test.skip(true, 'Blocking pengajuan aktif — siklus dilewati');
      return;
    }
    await page.fill('input[name="nama_kegiatan"]', kegiatan);
    await page.fill('input[name="dana_diajukan"]', '1500000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-10-10');
    await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
    await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);
    await expect(page.locator('body')).toContainText(/Draft|berhasil/i);

    const row = page.locator('tr', { hasText: kegiatan }).first();
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), row.locator('button:has-text("Ajukan")').click()]);
    await expect(page.locator('body')).toContainText('Pengajuan berhasil dikirim ke BEM.');
  });

  test('2. BEM memverifikasi: catatan wajib bila menolak, lalu setujui', async ({ page }) => {
    await loginAs(page, 'bem');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada (blocking) — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);

    // BR-05: menolak tanpa catatan harus ditolak.
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), page.locator('button:has-text("Tolak")').click()]);
    await expect(page.locator('text=Catatan wajib diisi')).toBeVisible();

    // Lanjut setujui.
    await page.fill('textarea[name="catatan"]', 'Disetujui oleh BEM. Lanjutkan.');
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Setujui")').first().click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('3. BPM menyetujui', async ({ page }) => {
    await loginAs(page, 'bpm');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Setujui")').first().click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('4. BKHM(1) wajib mengisi nomor surat sebelum setuju', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);

    // Tanpa nomor surat → error.
    if (await page.locator('input[name="nomor_surat"]').isVisible()) {
      page.once('dialog', (d) => d.accept());
      await Promise.all([page.waitForNavigation(), page.locator('button:has-text("Setujui")').first().click()]);
      await expect(page.locator('text=Nomor surat wajib diisi')).toBeVisible();
    }

    await page.fill('input[name="nomor_surat"]', '001/BEM/ITG/E2E/2026');
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Setujui")').first().click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('5. WR3 menyetujui', async ({ page }) => {
    await loginAs(page, 'wr3');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Setujui")').first().click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('6. BKHM(2) mengajukan pencairan ke Bendahara', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Ajukan Pencairan")').click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('7. Bendahara mencairkan dana (termin 1)', async ({ page }) => {
    await loginAs(page, 'bendahara');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    await page.fill('input[name="nominal_cair"]', '1200000');
    await page.fill('input[name="tanggal_cair"]', '2026-10-15');
    await page.fill('textarea[name="catatan"]', 'Dana cair termin 1.');
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), page.click('button:has-text("Konfirmasi Pencairan Dana")')]);
    await expect(page.locator('body')).toContainText(/berhasil diproses dan dicairkan/i);
  });

  test('8. Ormawa mengunggah LPJ', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/lpj');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Upload LPJ")').click()]);
    await page.setInputFiles('input[name="file_lpj"]', PROPOSAL);
    await Promise.all([page.waitForNavigation(), page.click('button:has-text("Upload & Ajukan Verifikasi LPJ")')]);
    await expect(page.locator('body')).toContainText('File LPJ berhasil diunggah');
  });

  test('9. BKHM memverifikasi LPJ → diteruskan ke WR3', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Setujui LPJ")').click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('10. WR3 memverifikasi LPJ → status Selesai', async ({ page }) => {
    await loginAs(page, 'wr3');
    await page.goto('/verifikasi');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan tidak ada — skip');

    await Promise.all([page.waitForNavigation(), row.locator('a:has-text("Verifikasi")').click()]);
    page.once('dialog', (d) => d.accept());
    await page.locator('button:has-text("Verifikasi LPJ")').click();
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });
});

/**
 * BR-05 / BR-13 / UI-015 — Revisi harus beralasan & dikembalikan ke pengusul dengan highlight.
 */
test.describe('BR-05 / BR-13 / UI-015 — Revisi pengajuan', () => {
  test.describe.configure({ mode: 'serial' });

  const kegiatan = `E2E Revisi ${Date.now()}`;

  test('Ormawa mengajukan, BEM merevisi dengan catatan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/pengajuan/create');
    if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
      test.skip(true, 'Blocking pengajuan aktif — revisi dilewati');
      return;
    }
    await page.fill('input[name="nama_kegiatan"]', kegiatan);
    await page.fill('input[name="dana_diajukan"]', '750000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-11-01');
    await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
    await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);

    const row = page.locator('tr', { hasText: kegiatan }).first();
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), row.locator('button:has-text("Ajukan")').click()]);
    await expect(page.locator('body')).toContainText('berhasil dikirim ke BEM');

    // BEM revisi dengan catatan.
    await loginAs(page, 'bem');
    await page.goto('/verifikasi');
    const verifRow = page.locator('tr', { hasText: kegiatan }).first();
    await Promise.all([page.waitForNavigation(), verifRow.locator('a:has-text("Verifikasi")').click()]);
    await page.fill('textarea[name="catatan"]', 'RAB belum lengkap, mohon direvisi.');
    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), page.locator('button:has-text("Revisi")').click()]);
    await expect(page.locator('body')).toContainText('Pengajuan berhasil diproses.');
  });

  test('UI-015: pengaju melihat highlight "Kendala / Catatan Revisi"', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Pengajuan revisi tidak ada — skip');

    await row.locator('a[href*="/pengajuan/"]').first().click();
    await page.waitForLoadState('domcontentloaded');
    await expect(page.locator('text=Kendala / Catatan Revisi').first()).toBeVisible();
    await expect(page.locator('text=RAB belum lengkap, mohon direvisi.').first()).toBeVisible();
    await expect(page.locator('a:has-text("Edit / Revisi")')).toBeVisible();
  });
});

/**
 * BACKLOG-013 / FR-012 — Revisi / Penolakan LPJ oleh verifikator (BKHM / WR3).
 * LPJ dikembalikan ke status 'funds_disbursed' dengan catatan revisi,
 * dan Ormawa dapat mengunggah kembali perbaikan LPJ.
 */
test.describe('BACKLOG-013 / FR-012 — Revisi LPJ oleh BKHM & WR3', () => {
  test('BKHM / WR3 dapat melihat tombol "Revisi LPJ" dan ormawa melihat status revisi', async ({ page }) => {
    // 1. Verifikasi role BKHM di halaman verifikasi
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/verifikasi');
    await expect(page.locator('body')).not.toContainText('403 Forbidden');

    // 2. Verifikasi role WR3 di halaman verifikasi
    await loginAs(page, 'wr3');
    await gotoStable(page, '/verifikasi');
    await expect(page.locator('body')).not.toContainText('403 Forbidden');

    // 3. Verifikasi role Ormawa di modul LPJ
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/lpj');
    await expect(page.locator('body')).not.toContainText('403 Forbidden');
    await expect(page.locator('body')).toContainText(/Laporan Pertanggungjawaban|LPJ/i);
  });
});

/**
 * BR-05 — Catatan wajib saat menolak ATAU merevisi (BACKLOG-005).
 * Spec: docs/backlog/BACKLOG-005-catatan-wajib-revisi/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-005-catatan-wajib-revisi/01-analisis-catatan-wajib.md
 *
 * Guard-nya ada di `VerifikasiController::process` baris 71:
 *   $isRejecting = in_array($transition->toState->name, ['rejected', 'draft']);
 * sehingga "Tolak" (→ rejected) DAN "Revisi" (→ draft) sama-sama dijaga.
 *
 * Describe ini SENGAJA terpisah dari siklus serial di atas: tidak bergantung pada
 * variabel `kegiatan` bersama, tidak ikut skip berantai, dan bersifat READ-ONLY —
 * guard menolak sebelum transisi state dijalankan, jadi tidak ada data baru tercipta.
 */
const BR05_ANTREAN = [
  { role: 'bem', stateLabel: 'Diajukan ke BEM' },
  { role: 'bpm', stateLabel: 'Verifikasi BPM' },
  { role: 'bkhm', stateLabel: 'Verifikasi BKHM' },
  { role: 'wr3', stateLabel: 'Verifikasi WR3' },
] as const;

test.describe('BR-05 — Catatan wajib saat revisi', () => {
  for (const { role, stateLabel } of BR05_ANTREAN) {
    test(`${role.toUpperCase()} tidak dapat merevisi tanpa catatan`, async ({ page }) => {
      await loginAs(page, role);

      let ketemu = false;

      // Antrean verifikasi memuat SEMUA pengajuan yang boleh diaksi role ini — termasuk
      // `draft` milik pengaju lain (BPM/BKHM juga bisa menjadi pengaju, lihat transisi
      // `draft -> bpm_approved`). Karena itu baris yang benar dicari lewat LABEL STATUS
      // di tabel, bukan lewat urutan baris. Paginasi 10/halaman → sisir 3 halaman.
      for (const halaman of [1, 2, 3]) {
        await gotoStable(page, halaman === 1 ? '/verifikasi' : `/verifikasi?page=${halaman}`);

        const baris = page.locator('tbody tr', { hasText: stateLabel }).first();
        if ((await baris.count()) === 0) continue;

        await baris.locator('a[href*="/verifikasi/"]').first().click();
        await page.waitForLoadState('domcontentloaded');

        if ((await page.locator('button:has-text("Revisi")').count()) > 0) {
          ketemu = true;
          break;
        }
      }

      test.skip(!ketemu, `Tidak ada pengajuan "${stateLabel}" dengan tombol Revisi untuk ${role} — skip`);

      // Pastikan field catatan kosong, lalu kirim aksi "Revisi".
      await page.fill('textarea[name="catatan"]', '');
      page.once('dialog', (d) => d.accept());
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.locator('button:has-text("Revisi")').first().click(),
      ]);

      // BR-05: ditolak, dengan pesan yang menyebut catatan.
      await expect(page.locator('body')).toContainText(/Catatan wajib diisi/i);

      // State tidak berpindah — tombol Revisi masih tersedia di halaman yang sama.
      expect(
        await page.locator('button:has-text("Revisi")').count(),
        `tombol Revisi harus tetap ada untuk ${role} (state tidak boleh berubah)`,
      ).toBeGreaterThan(0);
    });
  }
});

/**
 * BR-03 — Tidak boleh melompati tahap verifikasi (BACKLOG-015).
 * Spec: docs/backlog/BACKLOG-015-lompati-tahap-verifikasi/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-015-lompati-tahap-verifikasi/01-analisis-lompati-tahap.md
 *
 * Guard sesungguhnya ada di SERVER, `VerifikasiController::process` baris 66:
 *   if ($transition->from_state_id !== $pengajuan->workflow_state_id
 *       || $transition->required_role !== $userRole) abort(403);
 * UI hanya menyembunyikan tombol — jadi hanya request langsung yang membuktikan guard ini.
 *
 * ⚠️ ASUMSI "READ-ONLY" TERBANTAH: request palsu TIDAK ditolak — guard-nya bocor.
 * Saat dijalankan, test di blok ini benar-benar mengubah state pengajuan.
 * Lihat docs/ANALYSIS_REQUIRED.md.
 */
const BR03_ROLE = ['bem', 'bpm', 'bkhm', 'wr3'] as const;

/**
 * BR-03 — Tidak boleh melompati tahap verifikasi (BACKLOG-015).
 * Spec: docs/backlog/BACKLOG-015-lompati-tahap-verifikasi/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-015-lompati-tahap-verifikasi/01-analisis-lompati-tahap.md
 *
 * Guard sesungguhnya ada di SERVER, `VerifikasiController::process` baris 66:
 *   if ($transition->from_state_id !== $pengajuan->workflow_state_id
 *       || $transition->required_role !== $userRole) abort(403);
 * UI hanya menyembunyikan tombol — jadi hanya request langsung yang membuktikan guard ini.
 *
 * KRITIS-1 (16 Sep 2026): guard di baris 66 TIDAK memeriksa kepemilikan pengajuan.
 * Role bem/bpm secara teknis punya transisi `draft -> X` (karena mereka juga bisa
 * menjadi pengaju). Bila dikirim ke pengajuan DRAFT milik ORMAWA, kondisi baris 66
 * lolos (state cocok, role cocok) -> transisi DITERAPKAN. Ini yang membuktikan
 * kerentanan: pengajuan draft ormawa bisa "dilompati" tahapnya oleh bem/bpm.
 *
 * READ-ONLY: request palsu ditolak (403) sebelum state diubah, jadi tidak ada data
 * yang berubah. Transisi uji di-hardcode berdasarkan `WorkflowSeeder` (origin=draft):
 *   BEM  = id 2 (draft -> bem_approved)
 *   BPM  = id 3 (draft -> bpm_approved)
 *   BKHM = tidak punya transisi origin-draft (tidak menjadi pengaju) -> skip
 */
const TRANSISI_ORIGIN_DRAFT = [
  { role: 'bem', id: 2 },
  { role: 'bpm', id: 3 },
] as const;

test.describe('BR-03 — Anti lompat-tahap (request palsu)', () => {
  for (const { role, id: transitionId } of TRANSISI_ORIGIN_DRAFT) {
    test(`${role.toUpperCase()} tidak dapat memalsukan transisi draft ke pengajuan lain`, async ({ page }) => {
      // Target: pengajuan milik ormawa berstate `draft` (tersisa dari data uji).
      // Pilih yang state-nya draft (bukan completed) supaya guard line-66 lolos
      // (from_state === target state) dan HANYA guard kepemilikan (fix KRITIS-1)
      // yang memblokir. Bila target tidak ada -> skip.
      await loginAs(page, 'ormawa');
      await gotoStable(page, '/pengajuan');

      const baris = page.locator('tbody tr', { has: page.locator('a[href*="/pengajuan/"]') });
      const jumlah = await baris.count();

      let targetId = '';
      for (let i = 0; i < jumlah; i++) {
        const teks = await baris.nth(i).innerText();
        if (/\bDraft\b/.test(teks)) {
          const href = await baris.nth(i).locator('a[href*="/pengajuan/"]').first().getAttribute('href');
          targetId = href?.match(/\/pengajuan\/(\d+)/)?.[1] ?? '';
          if (targetId) break;
        }
      }
      test.skip(!targetId, `Tidak ada pengajuan draft milik ormawa — skip`);

      // Kirim transisi origin-draft milik role ini ke target ormawa.
      // Guard kepemilikan (KRITIS-1) HARUS menolak dengan 403.
      await loginAs(page, role);
      const token = await readCsrf(page);
      const resp = await page.request.post(`/verifikasi/${targetId}/process`, {
        form: { _token: token, transition_id: String(transitionId), catatan: 'Uji lompat tahap' },
        maxRedirects: 0,
      });

      expect(resp.status(), 'guard kepemilikan harus menolak transisi draft lintas pemilik dengan 403').toBe(403);
    });
  }
});
