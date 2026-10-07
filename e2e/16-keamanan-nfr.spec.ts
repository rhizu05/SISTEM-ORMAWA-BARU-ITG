import { test, expect, Page } from '@playwright/test';
import { readFileSync } from 'node:fs';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';
const NO_SQL_ERROR = /SQLSTATE|SQL syntax|database error|MySQL error|syntax error|unclosed quotation/i;

async function openOwnedPengajuanId(page: Page): Promise<string | null> {
  await gotoStable(page, '/pengajuan');
  const link = page.locator('table a[href*="/pengajuan/"]').first();
  if (await link.count() === 0) return null;
  await link.click();
  await page.waitForLoadState('domcontentloaded');
  const match = page.url().match(/\/pengajuan\/(\d+)/);
  return match ? match[1] : null;
}

/**
 * BACKLOG-002 — Path Traversal Security (SEC-01 / FR-023).
 * Spec: docs/backlog/BACKLOG-002-path-traversal/02-spec-test.md
 * Analisis: docs/backlog/BACKLOG-002-path-traversal/01-analisis-keamanan.md
 *
 * PENTING: literal `../` akan dicuitkan oleh HTTP client sebelum request dikirim,
 * sehingga payload WAJIB percent-encoded (%2e%2e%2f). Tanpa encoding, test akan
 * lulus tanpa pernah menguji server (palsu-positif).
 */

/** Status yang diterima ketika payload traversal ditolak dengan rapi. */
const STATUS_DITOLAK = [400, 403, 404];

/** Penanda isi file yang TIDAK boleh muncul di body respons. */
const PENANDA_FILE_SISTEM = /^root:x:|\[extensions\]|APP_KEY=|DB_PASSWORD=|DB_DATABASE=|<\?php/m;

/** Lampiran legacy yang masih tersimpan di disk `public` (temuan V2, diagnostik non-fatal). */
const FILE_LEGACY_PUBLIK = [
  '/storage/pengumuman/poPw261il39UeWwX2yc3piTyg1ppYAPDxV7ykLti.pdf',
  '/storage/pengumuman/v1bNRMw8U9ePPwWNm6nc3rVCpDqPmXq6Vjx2wYTY.pdf',
];

async function isBlocked(page: Page) {
  return page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false);
}

/** Token CSRF dari meta tag layout — agar endpoint bisa diuji tanpa merender form. */
async function readCsrf(page: Page): Promise<string> {
  return (await page.locator('meta[name="csrf-token"]').getAttribute('content')) ?? '';
}

/** Pastikan ada pengajuan milik pengaju yang sedang login; buat bila belum ada. */
async function ensureOwnedPengajuanId(page: Page): Promise<string | null> {
  const existing = await openOwnedPengajuanId(page);
  if (existing) return existing;

  await gotoStable(page, '/pengajuan/create');
  if (await isBlocked(page)) return null;

  await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E Traversal Base'));
  await page.fill('input[name="dana_diajukan"]', '150000');
  await page.fill('input[name="tanggal_pengajuan"]', '2026-12-20');
  await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.click('button:has-text("Simpan sebagai Draft")'),
  ]);

  return openOwnedPengajuanId(page);
}

/** Traversal sebagai segmen path tambahan setelah route valid. */
function payloadSegmenTambahan(id: string): string[] {
  return [
    `/dokumen/pengajuan/${id}/proposal/%2e%2e%2f%2e%2e%2f.env`,
    `/dokumen/pengajuan/${id}/proposal/..%2f..%2f..%2f.env`,
    `/dokumen/pengajuan/${id}/proposal/%2e%2e%2f%2e%2e%2fstorage%2fapp%2fprivate%2f.env`,
  ];
}

/** Traversal ter-encode pada parameter route-model binding. */
const PAYLOAD_MODEL_BINDING = [
  '/dokumen/pengajuan/%2e%2e%2f%2e%2e%2fetc%2fpasswd/proposal',
  '/dokumen/pengajuan/..%2f..%2f..%2f.env/proposal',
  '/dokumen/pengajuan/%2e%2e%5c%2e%2e%5cwindows%5cwin.ini/lpj',
];

/** ID tidak valid / non-numerik / tidak ada. */
const PAYLOAD_ID_INVALID = [
  '/dokumen/pengajuan/abc/proposal',
  '/dokumen/pengajuan/999999999/proposal',
  '/dokumen/pengajuan/-1/proposal',
  '/prestasi/%2e%2e%2f%2e%2e%2fetc%2fpasswd/bukti',
];

/** Route informasi bersifat publik (tanpa middleware auth). */
const PAYLOAD_INFO_PUBLIK = [
  '/informasi/pengumuman/%2e%2e%2f%2e%2e%2f.env/lampiran',
  '/informasi/pengumuman/1/lampiran/%2e%2e%2f%2e%2e%2f.env',
  '/informasi/regulasi/..%2f..%2fapp%2f.env/unduh',
  '/informasi/regulasi/abc/unduh',
];

/** Payload harus ditolak dengan status 4xx yang rapi — bukan 200, bukan 5xx. */
function expectDitolakDenganRapi(status: number, label: string) {
  expect(status, `${label} → tidak boleh 200 (kebocoran file)`).not.toBe(200);
  expect(status, `${label} → tidak boleh 5xx (temuan V1: PathTraversalDetected tidak tertangani)`).toBeLessThan(500);
  expect(STATUS_DITOLAK, `${label} → status di luar 400/403/404`).toContain(status);
}

/**
 * PRD: SEC-01 (Private Storage), SEC-02 (Upload Validation), SEC-03 (Flash Session),
 *      SEC-04 (RBAC — diuji pada 01-auth-rbac), SEC-05 (Privacy — diuji pada 12-aspirasi),
 *      SEC-06 (SQL Injection Prevention), NFR-02 (Performance/Reliability),
 *      NFR-05 (Dynamic Workflow), NFR-06 (Auditability).
 */
test.describe('SEC-01 — Private Storage Dokumen', () => {
  test('dokumen proposal hanya dapat diakses pemilik/verifikator (bukan role lain)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    let id = await openOwnedPengajuanId(page);

    if (!id) {
      await gotoStable(page, '/pengajuan/create');
      if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
        test.skip(true, 'Tidak ada pengajuan & state blocking — skip');
        return;
      }
      await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E Dokumen'));
      await page.fill('input[name="dana_diajukan"]', '200000');
      await page.fill('input[name="tanggal_pengajuan"]', '2026-12-05');
      await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
      await Promise.all([page.waitForNavigation(), page.click('button:has-text("Simpan sebagai Draft")')]);
      id = await openOwnedPengajuanId(page);
    }

    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    // Pemilik dapat mengunduh dokumennya (via request agar tidak memicu unduhan browser).
    const ownerResp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect(ownerResp.status()).toBeLessThan(400);

    // Role tanpa kewenangan ditolak (SEC-04 + SEC-01).
    await loginAs(page, 'mahasiswa');
    const denied = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect([403, 404]).toContain(denied.status());
  });

  test('verifikator BKHM dapat mengakses dokumen proposal pengaju', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/verifikasi');
    const detail = page.locator('a[href*="/verifikasi/"]').first();
    test.skip((await detail.count()) === 0, 'Tidak ada antrean verifikasi — skip');

    await detail.click();
    await page.waitForLoadState('domcontentloaded');
    // Halaman verifikasi memuat iframe dokumen privat (SEC-01).
    await expect(page.locator('iframe[src*="/dokumen/pengajuan/"]')).toBeVisible();
  });
});

test.describe('SEC-01 — Path Traversal (BACKLOG-002)', () => {
  // ---- Attack scenario -------------------------------------------------

  test('TC-TRV-001: segmen path tambahan pada route dokumen privat ditolak', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan milik ormawa — skip');

    for (const url of payloadSegmenTambahan(id!)) {
      const resp = await page.request.get(url);
      expectDitolakDenganRapi(resp.status(), url);
      expect(await resp.text(), `${url} → body memuat penanda file sistem`)
        .not.toMatch(PENANDA_FILE_SISTEM);
    }
  });

  test('TC-TRV-002: traversal ter-encode pada parameter model binding ditolak', async ({ page }) => {
    await loginAs(page, 'ormawa');

    for (const url of PAYLOAD_MODEL_BINDING) {
      const resp = await page.request.get(url);
      expectDitolakDenganRapi(resp.status(), url);
      expect(await resp.text(), `${url} → body memuat penanda file sistem`)
        .not.toMatch(PENANDA_FILE_SISTEM);
    }
  });

  test('TC-TRV-003: ID non-numerik / tidak ada / traversal sebagai ID ditolak', async ({ page }) => {
    await loginAs(page, 'ormawa');

    for (const url of PAYLOAD_ID_INVALID) {
      const resp = await page.request.get(url);
      expectDitolakDenganRapi(resp.status(), url);
    }
  });

  test('TC-TRV-004: traversal pada route informasi publik ditolak', async ({ page }) => {
    // Route /informasi/* memang publik — sengaja tanpa login.
    for (const url of PAYLOAD_INFO_PUBLIK) {
      const resp = await page.request.get(url);
      expectDitolakDenganRapi(resp.status(), url);
      expect(await resp.text(), `${url} → body memuat penanda file sistem`)
        .not.toMatch(PENANDA_FILE_SISTEM);
    }
  });

  test('TC-TRV-005: nama file traversal saat upload disanitasi', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan/create');
    test.skip(await isBlocked(page), 'State blocking — form tidak tersedia');

    const nama = uniqueName('E2E Traversal Upload');
    await page.fill('input[name="nama_kegiatan"]', nama);
    await page.fill('input[name="dana_diajukan"]', '150000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-12-20');
    // Nama klien dibuat traversal; isi file tetap PDF valid agar lolos validasi MIME.
    await page.setInputFiles('input[name="file_proposal"]', {
      name: '../../../etc/passwd.pdf',
      mimeType: 'application/pdf',
      buffer: readFileSync(PROPOSAL),
    });
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Simpan sebagai Draft")'),
    ]);

    // Upload berhasil lewat jalur resmi (bukan 5xx) dan draft tercatat.
    await expect(page).toHaveURL(/\/pengajuan$/);
    const row = page.locator('tr', { hasText: nama }).first();
    await expect(row).toBeVisible();

    // Dokumen tetap dapat diunduh → file benar-benar tersimpan di dalam disk privat,
    // bukan ditulis ke path traversal di luar storage.
    const href = await row.locator('a[href*="/pengajuan/"]').first().getAttribute('href');
    const id = href?.match(/\/pengajuan\/(\d+)/)?.[1];
    expect(id, 'ID pengajuan baru harus terbaca dari tabel').toBeTruthy();

    const resp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect(resp.status(), 'dokumen hasil upload traversal harus tetap dapat diakses pemilik')
      .toBeLessThan(400);
    const disposition = resp.headers()['content-disposition'] ?? '';
    expect(disposition, 'nama unduhan tidak boleh memuat traversal').not.toMatch(/\.\.|\//);
    expect(disposition).toMatch(/proposal-\d+\.pdf/);
  });

  test('TC-TRV-006: respons traversal tidak membocorkan konten file sistem', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);

    const urls = [
      ...payloadSegmenTambahan(id ?? '1'),
      ...PAYLOAD_MODEL_BINDING,
      ...PAYLOAD_ID_INVALID,
      ...PAYLOAD_INFO_PUBLIK,
    ];

    for (const url of urls) {
      const resp = await page.request.get(url);
      const body = await resp.text().catch(() => '');
      expect(body, `${url} → body memuat penanda file sistem`).not.toMatch(PENANDA_FILE_SISTEM);
      expect(resp.status(), `${url} → tidak boleh 200`).not.toBe(200);
    }
  });

  test('TC-TRV-007: regression guard — payload traversal tidak menghasilkan 5xx', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);

    const urls = [
      ...payloadSegmenTambahan(id ?? '1'),
      ...PAYLOAD_MODEL_BINDING,
      ...PAYLOAD_ID_INVALID,
      ...PAYLOAD_INFO_PUBLIK,
    ];

    for (const url of urls) {
      const resp = await page.request.get(url);
      expect(
        resp.status(),
        `${url} → 5xx berarti PathTraversalDetected tidak tertangani (temuan V1)`,
      ).toBeLessThan(500);
    }
  });

  test('TC-TRV-008: file privat tidak terekspos langsung via /storage', async ({ page }) => {
    const direktoriPrivat = [
      '/storage/proposals/',
      '/storage/lpj/',
      '/storage/prestasi/',
      '/storage/persetujuan-prodi/',
    ];

    for (const url of direktoriPrivat) {
      const resp = await page.request.get(url);
      expect(resp.status(), `${url} tidak boleh mengembalikan 200 (temuan V2)`)
        .toBeGreaterThanOrEqual(400);
    }

    // Diagnostik NON-FATAL (temuan V2): lampiran pengumuman lama masih ada di disk `public`
    // sehingga dapat diakses tanpa otorisasi. Kontennya informasi publik, jadi dicatat
    // sebagai utang teknis — bukan kegagalan test. Ikut hijau bila kelak dibersihkan.
    for (const legacy of FILE_LEGACY_PUBLIK) {
      const resp = await page.request.get(legacy);
      if (resp.status() === 200) {
        test.info().annotations.push({
          type: 'V2-terkonfirmasi',
          description: `${legacy} masih dapat diakses publik (200) — lihat docs/backlog/BACKLOG-002-path-traversal/01-analisis-keamanan.md`,
        });
      }
    }
  });

  // ---- Normal scenario (kontrol: proteksi tidak over-blocking) ---------

  test('TC-TRV-101: pemilik dapat mengunduh proposal sendiri', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan milik ormawa — skip');

    const resp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect(resp.status(), 'jalur normal tidak boleh ikut terblokir').toBeLessThan(400);
    expect(resp.headers()['content-disposition'] ?? '').toMatch(/attachment/i);
  });

  test('TC-TRV-102: verifikator berwenang (BKHM) dapat mengakses dokumen pengaju', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    // BKHM bukan pemilik dokumen, aksesnya murni lewat kewenangan verifikator.
    await loginAs(page, 'bkhm');
    const resp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect(resp.status(), 'BKHM berwenang atas dokumen proposal').toBeLessThan(400);
  });

  test('TC-TRV-103: role tanpa kewenangan tetap ditolak', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan milik ormawa — skip');

    await loginAs(page, 'mahasiswa');
    const resp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect([403, 404], 'mahasiswa bukan pemilik & bukan verifikator').toContain(resp.status());
  });

  test('TC-TRV-104: upload normal tersimpan dan dapat diunduh', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan/create');
    test.skip(await isBlocked(page), 'State blocking — form tidak tersedia');

    const nama = uniqueName('E2E Traversal Normal');
    await page.fill('input[name="nama_kegiatan"]', nama);
    await page.fill('input[name="dana_diajukan"]', '150000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-12-20');
    await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Simpan sebagai Draft")'),
    ]);

    await expect(page).toHaveURL(/\/pengajuan$/);
    const row = page.locator('tr', { hasText: nama }).first();
    await expect(row).toBeVisible();

    const href = await row.locator('a[href*="/pengajuan/"]').first().getAttribute('href');
    const id = href?.match(/\/pengajuan\/(\d+)/)?.[1];
    expect(id, 'ID pengajuan baru harus terbaca dari tabel').toBeTruthy();

    const resp = await page.request.get(`/dokumen/pengajuan/${id}/proposal`);
    expect(resp.status()).toBeLessThan(400);
  });
});

test.describe('SEC-02 — Validasi Unggahan', () => {
  test('unggahan proposal dibatasi PDF (accept + validasi server)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan/create');

    if (await page.locator('text=Pengajuan Ditangguhkan').isVisible().catch(() => false)) {
      test.skip(true, 'State blocking — form tidak tersedia');
      return;
    }

    const fileInput = page.locator('input[name="file_proposal"]');
    await expect(fileInput).toHaveAttribute('accept', /pdf/i);

    // Unggah non-PDF harus ditolak (tetap di form).
    await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E MIME'));
    await page.fill('input[name="dana_diajukan"]', '100000');
    await page.fill('input[name="tanggal_pengajuan"]', '2026-12-10');
    await page.setInputFiles('input[name="file_proposal"]', 'e2e/fixtures/dummy.png');
    await page.click('button:has-text("Simpan sebagai Draft")');
    expect(page.url()).toContain('/pengajuan/create');
  });
});

test.describe('SEC-03 — Flash Session (bukan parameter URL)', () => {
  test('pesan error/sukses tidak dikirim via query string', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'salah@test.com');
    await page.fill('input[name="password"]', 'salah');
    await page.click('button[type="submit"]');

    // URL tidak boleh membawa pesan.
    expect(page.url()).not.toMatch(/\?(error|success|message)=/i);
    // Pesan tetap tampil (dari session).
    await expect(page.locator('text=These credentials do not match')).toBeVisible({ timeout: 5000 }).catch(async () => {
      await expect(page).toHaveURL(/.*login.*/);
    });
  });

  test('aksi berhasil memakai flash session tanpa query string', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/aspirasi/kirim');
    await page.fill('input[name="judul"]', uniqueName('Flash Session'));
    await page.selectOption('select[name="kategori"]', 'Lainnya');
    await page.fill('textarea[name="isi"]', 'Uji flash session');
    await page.click('button:has-text("Kirim Aspirasi")');
    await expect(page.locator('text=berhasil dikirim').first()).toBeVisible({ timeout: 8000 });
    expect(page.url()).not.toMatch(/\?(error|success|message)=/i);
  });

  // ---- BACKLOG-004: audit menyeluruh SEC-03 ----------------------------
  // Spec: docs/backlog/BACKLOG-004-audit-flash-session/02-spec-test.md
  // Analisis: docs/backlog/BACKLOG-004-audit-flash-session/01-analisis-flash-session.md
  //
  // Dua halves SEC-03 diuji di sini:
  //   (a) pesan TIDAK boleh lewat query string → TC-FLS-001
  //   (b) pesan flash HARUS tampil di body    → TC-FLS-002…005, TC-FLS-103
  // Half (b) adalah yang bocor di 3 tempat (F1–F3) dan sudah diperbaiki di view.

  /** Parameter pesan yang tidak boleh muncul di URL pasca-aksi. */
  const PESAN_DI_URL = /\?(success|error|message|status)=/i;

  test('TC-FLS-001: tidak ada aksi yang mengirim pesan lewat query string', async ({ page }) => {
    // Aksi 1 — buat draft pengajuan
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/pengajuan/create');
    if (!(await isBlocked(page))) {
      await page.fill('input[name="nama_kegiatan"]', uniqueName('Flash URL'));
      await page.fill('input[name="dana_diajukan"]', '150000');
      await page.fill('input[name="tanggal_pengajuan"]', '2026-12-20');
      await page.setInputFiles('input[name="file_proposal"]', PROPOSAL);
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        page.click('button:has-text("Simpan sebagai Draft")'),
      ]);
      expect(page.url(), 'buat draft pengajuan').not.toMatch(PESAN_DI_URL);
    }

    // Aksi 2 — kirim aspirasi
    await gotoStable(page, '/aspirasi/kirim');
    await page.fill('input[name="judul"]', uniqueName('Flash URL Aspirasi'));
    await page.selectOption('select[name="kategori"]', 'Lainnya');
    await page.fill('textarea[name="isi"]', 'Uji enumerasi URL SEC-03.');
    await page.click('button:has-text("Kirim Aspirasi")');
    await expect(page.locator('body')).toContainText(/berhasil dikirim/i);
    expect(page.url(), 'kirim aspirasi').not.toMatch(PESAN_DI_URL);

    // Aksi 3 — terbitkan regulasi (BPM)
    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/regulasi/create');
    await page.fill('input[name="judul"]', uniqueName('Flash URL Regulasi'));
    await page.selectOption('select[name="kategori"]', 'Pedoman');
    await page.fill('textarea[name="deskripsi"]', 'Uji enumerasi URL SEC-03.');
    await page.setInputFiles('input[name="file"]', PROPOSAL);
    await page.fill('input[name="tanggal_terbit"]', '2026-12-20');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Terbitkan Sekarang")'),
    ]);
    expect(page.url(), 'terbit regulasi').not.toMatch(PESAN_DI_URL);

    // Catatan: `/pengajuan?status=…` DIPERBOLEHKAN — itu filter daftar, bukan pesan.
  });

  test('TC-FLS-002: guard pencairan tetap memberi pesan walau tanpa Referer', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await ensureOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    await loginAs(page, 'bendahara');
    const token = await readCsrf(page);
    const resp = await page.request.post(`/bendahara/proses/${id}`, {
      form: { _token: token, nominal_cair: '100000', tanggal_cair: '2026-10-15' },
      maxRedirects: 0,
    });
    expect(resp.status(), 'guard menolak dengan redirect').toBe(302);

    // Dulu pemeriksaan ini harus dialihkan ke `/notifikasi`; setelah
    // `verifikasi/index.blade.php` memakai <x-flash />, pesan tampil di sini.
    await gotoStable(page, '/verifikasi');
    await expect(page.locator('body')).toContainText(/belum disetujui untuk dicairkan/i);
  });

  test('TC-FLS-003: error "Ajukan" tampil di halaman detail pengajuan', async ({ page }) => {
    // Pakai akun yang punya pengajuan non-draft — himaif (ormawa) hanya punya draft,
    // sedangkan `bem` memiliki pengajuan berstatus `completed`.
    await loginAs(page, 'bem');
    await gotoStable(page, '/pengajuan?status=completed');

    // Filter `has:` memastikan baris yang dipilih benar-benar punya link detail —
    // baris empty-state tidak ikut terpilih.
    const row = page.locator('table tbody tr', { has: page.locator('a[href*="/pengajuan/"]') }).first();
    test.skip((await row.count()) === 0, 'Tidak ada pengajuan non-draft untuk akun ini — skip');

    const href = await row.locator('a[href*="/pengajuan/"]').first().getAttribute('href');
    const id = href?.match(/\/pengajuan\/(\d+)/)?.[1];
    test.skip(!id, 'ID pengajuan tidak terbaca — skip');

    await gotoStable(page, `/pengajuan/${id}`);
    const token = await readCsrf(page);

    // Referer diset eksplisit agar `back()` kembali ke halaman detail —
    // inilah jalur yang dulu menjatuhkan pesan (F1).
    const resp = await page.request.post(`/pengajuan/${id}/ajukan`, {
      form: { _token: token },
      headers: { Referer: `/pengajuan/${id}` },
      maxRedirects: 0,
    });
    expect(resp.status(), 'guard menolak dengan redirect').toBe(302);

    await gotoStable(page, `/pengajuan/${id}`);
    // Guard mana yang aktif bergantung apakah pengaju punya pengajuan lain yang
    // menggantung (baris 183) atau tidak (baris 188) — keduanya sama-sama menolak
    // lewat `back()`, dan keduanya harus terlihat di halaman detail.
    await expect(page.locator('body')).toContainText(
      /Hanya pengajuan berstatus draft yang bisa diajukan|Pengajuan ditangguhkan/i,
    );
  });

  test('TC-FLS-004: konfirmasi terbit regulasi tampil di daftar regulasi', async ({ page }) => {
    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/regulasi/create');

    const judul = uniqueName('E2E Regulasi Flash');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Pedoman');
    await page.fill('textarea[name="deskripsi"]', 'Uji tampilan flash SEC-03.');
    await page.setInputFiles('input[name="file"]', PROPOSAL);
    await page.fill('input[name="tanggal_terbit"]', '2026-12-20');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Terbitkan Sekarang")'),
    ]);

    await expect(page).toHaveURL(/\/bpm\/regulasi$/);
    // Penjaga anti-palsu-positif: URL `/bpm/regulasi` juga muncul saat POST gagal 500,
    // jadi insert-nya harus dibuktikan lewat baris baru di daftar.
    await expect(page.locator('body'), 'halaman tidak boleh error server')
      .not.toContainText(/Server Error|doesn't have a default value/i);
    await expect(page.locator('tr', { hasText: judul }).first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/Regulasi berhasil diterbitkan/i);
  });

  test('TC-FLS-005: konfirmasi Surat Peringatan tampil di dashboard BPM', async ({ page }) => {
    await loginAs(page, 'bpm');
    await gotoStable(page, '/bpm/sp/create');

    await page.selectOption('select[name="target_user_id"]', { index: 1 });
    await page.fill('input[name="nomor_surat"]', `E2E/${Date.now()}/SP/BPM/TEST`);
    await page.selectOption('select[name="tingkat"]', 'SP-1');
    await page.fill('input[name="perihal"]', 'Uji flash SEC-03');
    await page.fill('input[name="alasan_singkat"]', 'Uji otomatis');
    await page.fill('textarea[name="deskripsi"]', 'Uji tampilan flash SEC-03.');
    await page.fill('textarea[name="sanksi"]', 'Teguran tertulis.');
    await page.fill('input[name="tanggal_surat"]', '2026-12-20');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Ajukan ke BKHM")'),
    ]);

    await expect(page.locator('body')).toContainText(/menunggu tinjauan BKHM/i);
  });

  test('TC-FLS-103: konfirmasi hapus regulasi tampil', async ({ page }) => {
    await loginAs(page, 'bpm');

    // Buat regulasi sendiri agar penghapusan hanya menyentuh data uji.
    const judul = uniqueName('E2E Regulasi Hapus');
    await gotoStable(page, '/bpm/regulasi/create');
    await page.fill('input[name="judul"]', judul);
    await page.selectOption('select[name="kategori"]', 'Pedoman');
    await page.fill('textarea[name="deskripsi"]', 'Akan dihapus oleh test.');
    await page.setInputFiles('input[name="file"]', PROPOSAL);
    await page.fill('input[name="tanggal_terbit"]', '2026-12-20');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Terbitkan Sekarang")'),
    ]);

    const row = page.locator('tr', { hasText: judul }).first();
    test.skip((await row.count()) === 0, 'Regulasi uji tidak ditemukan — skip');

    page.once('dialog', (d) => d.accept());
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      row.locator('button:has-text("Hapus")').click(),
    ]);

    await expect(page.locator('body')).toContainText(/Regulasi berhasil dihapus/i);
  });

  test('TC-FLS-006: konfirmasi buat pengguna tampil tanpa query string', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/admin/users');

    await page.click('button:has-text("Tambah Pengguna")');

    const username = `e2e${Date.now()}`;
    const nama = uniqueName('E2E Flash User');
    await page.fill('input[name="name"]', nama);
    await page.fill('input[name="email"]', `${username}@test.com`);
    await page.fill('input[name="username"]', username);
    await page.fill('input[name="password"]', 'password');
    await page.selectOption('select[name="role"]', 'mahasiswa');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.locator('form[action$="/admin/users"] button[type="submit"]').click(),
    ]);

    expect(page.url(), 'buat pengguna').not.toMatch(PESAN_DI_URL);
    // Anti-palsu-positif: buktikan barisnya benar-benar muncul, bukan sekadar teks flash.
    await expect(page.locator('tr', { hasText: username }).first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/User berhasil ditambahkan/i);
  });

  test('TC-FLS-007: konfirmasi peminjaman ruangan tampil tanpa query string', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/tempat/create');

    const opsiRuangan = page.locator('select[name="ruangan_id"] option');
    test.skip((await opsiRuangan.count()) < 2, 'Tidak ada data ruangan — skip');

    const ruanganId = (await opsiRuangan.nth(1).getAttribute('value')) ?? '';
    const nama = uniqueName('E2E Flash Peminjaman');

    // Hindari bentrok jadwal dengan run sebelumnya dengan tanggal unik di masa depan
    const d = new Date(Date.now() + 86400000 * (30 + (Date.now() % 200)));
    const tglStr = d.toISOString().split('T')[0];

    await page.selectOption('select[name="ruangan_id"]', ruanganId);
    await page.fill('input[name="nama_kegiatan"]', nama);
    await page.fill('input[name="tgl_mulai"]', tglStr);
    await page.fill('input[name="tgl_selesai"]', tglStr);
    await page.fill('input[name="jam_mulai"]', '09:00');
    await page.fill('input[name="jam_selesai"]', '11:00');
    await page.fill('textarea[name="deskripsi_kegiatan"]', 'Uji flash SEC-03.');
    // HIMA wajib melampirkan surat persetujuan Prodi.
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Ajukan Peminjaman")'),
    ]);

    expect(page.url(), 'peminjaman ruangan').not.toMatch(PESAN_DI_URL);
    await expect(page.locator('tr', { hasText: nama }).first()).toBeVisible();
    await expect(page.locator('body')).toContainText(/berhasil dikirim/i);
  });
});

test.describe('SEC-06 — Pencegahan SQL Injection', () => {
  test('upaya injeksi pada filter tidak membocorkan error SQL', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const attempts = ["' OR '1'='1", "'; DROP TABLE users; --", "1' OR '1' = '1"];
    for (const attempt of attempts) {
      await page.goto(`/pengajuan?status=${encodeURIComponent(attempt)}`, { waitUntil: 'domcontentloaded' });
      const body = (await page.locator('body').textContent()) ?? '';
      expect(body).not.toMatch(NO_SQL_ERROR);
    }
  });
});

test.describe('NFR-02 / NFR-05 / NFR-06 — Non-Functional', () => {
  test('NFR-02: halaman kritis merespons tanpa error server', async ({ page }) => {
    await loginAs(page, 'ormawa');
    for (const url of ['/dashboard', '/pengajuan', '/informasi', '/notifikasi']) {
      const resp = await page.goto(url, { waitUntil: 'domcontentloaded' });
      expect(resp?.status(), `${url} harus < 400`).toBeLessThan(400);
    }
  });

  test('BACKLOG-023 / NFR-02: Performance Budget — load time response halaman daftar pengajuan di bawah 2 detik', async ({ page }) => {
    await loginAs(page, 'ormawa');

    // Warm-up route & cache
    await page.request.get('/pengajuan');

    // Ukur waktu respons server murni (HTTP request cycle)
    const start = Date.now();
    const resp = await page.request.get('/pengajuan');
    const duration = Date.now() - start;

    expect(resp.status()).toBe(200);
    // Budget waktu respon di bawah 2000 ms (NFR-02: sistem stabil & responsif)
    expect(duration, `Durasi response server /pengajuan (${duration} ms) harus < 2000 ms`).toBeLessThan(2000);
  });

  test('NFR-05: aksi verifikasi dirender dinamis dari tabel workflow', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/verifikasi');
    const detail = page.locator('a[href*="/verifikasi/"]').first();
    test.skip((await detail.count()) === 0, 'Tidak ada antrean verifikasi — skip');

    await detail.click();
    await page.waitForLoadState('domcontentloaded');
    // Tombol transisi berasal dari WorkflowTransition (dinamis), bukan hardcode.
    await expect(page.locator('form[action*="/verifikasi/"][action$="/process"]')).toBeVisible();
    await expect(page.locator('button[name="transition_id"]').first()).toBeVisible();
  });

  test('NFR-06: histori status mencatat aktor & waktu (audit trail)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    const id = await openOwnedPengajuanId(page);
    test.skip(!id, 'Tidak ada pengajuan untuk diuji — skip');

    await expect(page.getByRole('heading', { name: /Riwayat & Status PIC/ })).toBeVisible();
    await expect(page.locator('text=Oleh:').first()).toBeVisible();
  });
});
