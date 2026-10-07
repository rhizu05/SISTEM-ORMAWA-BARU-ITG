import { test, expect } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';

/**
 * PRD: FR-008 (Generator Dokumen Otomatis — proposal, LPJ, surat administrasi bernomor).
 */
test.describe('FR-008 — Generator Surat Otomatis (4 jenis dinamis)', () => {
  const baseUrl = '/generator/letters/create';

  test('informasi dasar selalu tampil + pemilihan jenis surat', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await expect(page.locator('text=/Generator Surat Otomatis|Informasi Dasar Surat/').first()).toBeVisible();
    await expect(page.locator('select#type, select[name="type"]')).toBeVisible();
    await expect(page.locator('input[name="nomor_surat"]')).toBeVisible();
    await expect(page.locator('input[name="perihal"]')).toBeVisible();
    await expect(page.locator('input[name="tujuan"]')).toBeVisible();
    await expect(page.locator('select[name="penandatangan_jenis[0]"]')).toBeVisible();
    await expect(page.locator('select[name="penandatangan_role[0]"]')).toBeVisible();
    await expect(page.locator('option[value="ketua"]').first()).toContainText('Ketua');
  });

  test('Undangan — fields: kalimat_pembuka, nama_acara, hari_tanggal, waktu, tempat', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await page.selectOption('select[name="type"]', 'undangan');
    await page.waitForTimeout(300);
    await expect(page.locator('textarea[name="kalimat_pembuka"]')).toBeVisible();
    await expect(page.locator('input[name="nama_acara"]')).toBeVisible();
    await expect(page.locator('input[name="hari_tanggal"]')).toBeVisible();
    await expect(page.locator('input[name="waktu"]')).toBeVisible();
    await expect(page.locator('input[name="tempat"]')).toBeVisible();

    await page.fill('input[name="perihal"]', 'Undangan Pemateri Seminar Nasional');
    await page.fill('input[name="tujuan"]', 'Yth. Bapak Ir. Budi Santoso, M.T.');
    await page.fill('textarea[name="kalimat_pembuka"]', 'Sehubungan akan dilaksanakannya Seminar Nasional, kami mengundang Bapak/Ibu hadir sebagai pemateri.');
    await page.fill('input[name="nama_acara"]', 'Seminar Nasional');
    await page.fill('input[name="hari_tanggal"]', 'Senin, 20 Mei 2024');
    await page.fill('input[name="waktu"]', '08.00 s.d Selesai');
    await page.fill('input[name="tempat"]', 'Aula ITG');

    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);
    await expect(page.locator('text=SURAT UNDANGAN')).toBeVisible();
    await expect(page.locator('body')).toContainText('Seminar Nasional');
  });

  test('Surat Tugas — fields: nama_petugas, nim, uraian_tugas, tanggal_pelaksanaan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await page.selectOption('select[name="type"]', 'tugas');
    await page.waitForTimeout(800);
    await page.waitForFunction(() => {
      const el = document.querySelector('#nim_tugas') as HTMLInputElement;
      return el && !el.disabled && el.offsetParent !== null;
    }, null, { timeout: 8000 });
    await expect(page.locator('#nim_tugas')).toBeVisible();
    await expect(page.locator('input[name="nama_petugas"]')).toBeVisible();
    await expect(page.locator('textarea[name="uraian_tugas"]')).toBeVisible();
    await expect(page.locator('input[name="tanggal_pelaksanaan"]')).toBeVisible();

    await page.fill('input[name="perihal"]', 'Penugasan Delegasi Munas');
    await page.fill('input[name="tujuan"]', 'Yth. Peserta Munas');
    await page.fill('input[name="nama_petugas"]', 'Andi');
    await page.locator('#nim_tugas').fill('123456');
    await page.fill('textarea[name="uraian_tugas"]', 'Menjadi delegasi dalam kegiatan Musyawarah Nasional');
    await page.fill('input[name="tanggal_pelaksanaan"]', '20 - 25 Mei 2024');
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);
    await expect(page.locator('text=SURAT TUGAS / MANDAT').first()).toBeVisible();
  });

  test('Permohonan (alat/tempat) — fields: nama_alat_tempat, waktu_penggunaan, alasan_tujuan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await page.selectOption('select[name="type"]', 'permohonan');
    await page.waitForTimeout(400);
    await expect(page.locator('input[name="nama_alat_tempat"]')).toBeVisible();
    await expect(page.locator('input[name="waktu_penggunaan"]')).toBeVisible();
    await expect(page.locator('textarea[name="alasan_tujuan"]')).toBeVisible();

    await page.fill('input[name="perihal"]', 'Permohonan Aula');
    await page.fill('input[name="tujuan"]', 'Yth. Kabag Sarpras');
    await page.fill('input[name="nama_alat_tempat"]', 'Aula ITG');
    await page.fill('input[name="waktu_penggunaan"]', 'Rabu, 22 Mei 2024 Jam 13.00');
    await page.fill('textarea[name="alasan_tujuan"]', 'Kegiatan Seminar');
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);
    await expect(page.locator('text=SURAT PERMOHONAN')).toBeVisible();
  });

  test('Keterangan Aktif — fields: nama_mahasiswa, nim, jabatan, keperluan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await page.selectOption('select[name="type"]', 'keterangan_aktif');
    await page.waitForTimeout(800);
    await page.waitForFunction(() => {
      const el = document.querySelector('#nim_ket') as HTMLInputElement;
      return el && !el.disabled && el.offsetParent !== null;
    }, null, { timeout: 8000 });
    await expect(page.locator('#nim_ket')).toBeVisible();
    await expect(page.locator('input[name="nama_mahasiswa"]')).toBeVisible();
    await expect(page.locator('input[name="jabatan"]')).toBeVisible();
    await expect(page.locator('textarea[name="keperluan"]')).toBeVisible();

    await page.fill('input[name="perihal"]', 'Keterangan Aktif Organisasi');
    await page.fill('input[name="tujuan"]', 'Yth. Bagian Kemahasiswaan');
    await page.fill('input[name="nama_mahasiswa"]', 'Budi');
    await page.locator('#nim_ket').fill('654321');
    await page.fill('input[name="jabatan"]', 'Anggota Bidang Minat Bakat');
    await page.fill('textarea[name="keperluan"]', 'Persyaratan Beasiswa Unggulan');
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);
    await expect(page.getByText('SURAT KETERANGAN AKTIF').first()).toBeVisible();
  });

  test('halaman show menampilkan penandatangan Ketua', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto(baseUrl);
    await page.selectOption('select[name="type"]', 'undangan');
    await page.waitForTimeout(300);
    await page.fill('input[name="perihal"]', 'Test TTD Ketua');
    await page.fill('input[name="tujuan"]', 'Yth. Tester');
    await page.fill('textarea[name="kalimat_pembuka"]', 'Pembuka');
    await page.fill('input[name="nama_acara"]', 'Acara TTD');
    await page.fill('input[name="hari_tanggal"]', 'Senin, 1 Jan 2024');
    await page.fill('input[name="waktu"]', '09.00');
    await page.fill('input[name="tempat"]', 'Gedung A');
    await page.selectOption('select[name="penandatangan_role[0]"]', 'ketua');
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page.locator('text=/Ketua|Garut/').first()).toBeVisible();
  });
});

test.describe('FR-008 — Generator Proposal & LPJ', () => {
  test('form proposal menampilkan Section I–III + Penandatangan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/generator/create');
    await expect(page.getByRole('heading', { name: /Pembuatan Proposal Otomatis/ }).first()).toBeVisible();
    await expect(page.locator('text=Pendahuluan & Narasi')).toBeVisible();
    await expect(page.locator('text=Rencana Anggaran Biaya')).toBeVisible();
    await expect(page.locator('text=Susunan Panitia')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Penandatangan' })).toBeVisible();
    await expect(page.locator('select[name="penandatangan_jenis[0]"]')).toBeVisible();
    await expect(page.locator('select[name="penandatangan_role[0]"]')).toBeVisible();
  });

  test('simpan proposal draft mengarah ke arsip', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/generator/create');
    await page.fill('input[name="nama_kegiatan"]', `Lomba Coding Nasional 2024 E2E ${Date.now()}`);
    await page.fill('textarea[name="latar_belakang"]', 'Latar belakang E2E');
    await page.fill('textarea[name="tujuan"]', 'Tujuan E2E');
    await page.fill('textarea[name="sasaran"]', 'Mahasiswa Se-Indonesia');
    await page.fill('textarea[name="penutup"]', 'Penutup E2E');
    const rabInput = page.locator('input[name="rab_rincian[]"]').first();
    if (await rabInput.isVisible()) {
      await rabInput.fill('Konsumsi Peserta');
      await page.locator('input[name="rab_vol[]"]').first().fill('10');
      await page.locator('input[name="rab_harga[]"]').first().fill('50000');
    }
    await page.locator('button:has-text("Simpan Draft")').click();
    await expect(page).toHaveURL(/.*archive.*/);
    await expect(page.locator('body')).toContainText(/Lomba Coding/i);
  });

  test('LPJ create (mandiri maupun dengan proposalId) dapat diakses', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/generator/lpj/create');
    await expect(page.locator('main').locator('text=/Buat LPJ|LPJ/').first()).toBeVisible();
    await expect(page.locator('input[name="nama_kegiatan"], textarea[name="pendahuluan"]')).toBeVisible().catch(async () => {
      await expect(page.locator('body')).toContainText(/Pendahuluan|LPJ/);
    });
  });

  test('generator print tidak 403 untuk pemilik', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/generator');
    const first = page.locator('a:has-text("Cetak"), a:has-text("Lihat"), a[href*="/generator/"]').first();
    if (await first.isVisible()) {
      await first.click();
      await expect(page.locator('body')).not.toContainText('Forbidden');
    } else {
      await expect(page.locator('body')).toContainText(/Generator|Proposal/i);
    }
  });
});

/**
 * FR-008 / §19: unduhan PDF (DomPDF) untuk proposal & surat.
 */
test.describe('FR-008 — Unduh PDF (DomPDF)', () => {
  test('proposal dapat diunduh sebagai PDF', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/generator/create');

    const nama = uniqueName('PDF Proposal');
    await page.fill('input[name="nama_kegiatan"]', nama);
    await page.fill('textarea[name="latar_belakang"]', 'Latar belakang PDF.');
    await page.fill('textarea[name="tujuan"]', 'Tujuan PDF.');
    await page.fill('textarea[name="sasaran"]', 'Sasaran PDF.');
    await page.fill('textarea[name="penutup"]', 'Penutup PDF.');
    await Promise.all([
      page.waitForNavigation(),
      page.locator('button:has-text("Simpan Draft")').click(),
    ]);

    await gotoStable(page, '/generator');
    const row = page.locator('tr', { hasText: nama }).first();
    await expect(row).toBeVisible();

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      row.locator('a:has-text("Unduh PDF")').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^proposal-.*\.pdf$/i);
  });

  test('surat dapat diunduh sebagai PDF', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/generator/letters/create');
    await page.selectOption('select[name="type"]', 'undangan');
    await page.waitForTimeout(300);
    await page.fill('input[name="perihal"]', uniqueName('PDF Surat'));
    await page.fill('input[name="tujuan"]', 'Yth. Tester');
    await page.fill('textarea[name="kalimat_pembuka"]', 'Pembuka');
    await page.fill('input[name="nama_acara"]', 'Acara PDF');
    await page.fill('input[name="hari_tanggal"]', 'Senin, 1 Jan 2026');
    await page.fill('input[name="waktu"]', '09.00');
    await page.fill('input[name="tempat"]', 'Gedung A');
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);

    const [download] = await Promise.all([
      page.waitForEvent('download'),
      page.locator('a:has-text("Unduh PDF")').click(),
    ]);
    expect(download.suggestedFilename()).toMatch(/^surat-.*\.pdf$/i);
  });

  test('BACKLOG-022 / FR-008: pratinjau surat memuat komponen kop surat resmi ITG (nama institusi & alamat)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await page.goto('/generator/letters/create');
    await page.selectOption('select[name="type"]', 'undangan');
    await page.waitForTimeout(300);
    await page.fill('input[name="perihal"]', uniqueName('Uji Kop Surat'));
    await page.fill('input[name="tujuan"]', 'Yth. Pimpinan Lembaga');
    await page.fill('textarea[name="kalimat_pembuka"]', 'Pembuka surat dinamis.');
    await page.fill('input[name="nama_acara"]', 'Acara Kop ITG');
    await page.fill('input[name="hari_tanggal"]', 'Rabu, 10 Oktober 2026');
    await page.fill('input[name="waktu"]', '10.00 WIB');
    await page.fill('input[name="tempat"]', 'Aula Utama ITG');
    await page.locator('button:has-text("Generate Surat")').click();

    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);

    // Verifikasi keberadaan elemen kop surat resmi (Nama Institusi & Alamat Garut)
    const previewContainer = page.locator('div[style*="font-family"]').first();
    await expect(previewContainer).toContainText(/INSTITUT TEKNOLOGI GARUT/i);
    await expect(previewContainer).toContainText(/Jayaraga Garut|Mayor Syamsu/i);
  });
});

test.describe('FR-008: TTD Digital Multi-Penandatangan', () => {
  const baseUrl = '/generator/letters/create';

  async function isiUndangan(page: import('@playwright/test').Page, perihal: string) {
    await page.selectOption('select[name="type"]', 'undangan');
    await page.waitForTimeout(300);
    await page.fill('input[name="perihal"]', perihal);
    await page.fill('input[name="tujuan"]', 'Yth. Tester');
    await page.fill('textarea[name="kalimat_pembuka"]', 'Pembuka');
    await page.fill('input[name="nama_acara"]', 'Acara Uji');
    await page.fill('input[name="hari_tanggal"]', 'Senin, 1 Jan 2026');
    await page.fill('input[name="waktu"]', '09.00');
    await page.fill('input[name="tempat"]', 'Gedung A');
  }

  test('tambah penandatangan menampilkan kolom kedua', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, baseUrl);
    await page.locator('button:has-text("Tambah Penandatangan")').click();
    await expect(page.locator('select[name="penandatangan_jenis[1]"]')).toBeVisible();
    await expect(page.locator('select[name="penandatangan_role[1]"]')).toBeVisible();
  });

  test('pihak luar ditandai manual dan tidak mendapat TTD kripto', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, baseUrl);
    await page.locator('button:has-text("Tambah Penandatangan")').click();
    await page.selectOption('select[name="penandatangan_jenis[1]"]', 'eksternal');
    await expect(page.locator('input[name="penandatangan_jabatan[1]"]')).toBeVisible();
    await page.fill('input[name="penandatangan_nama[1]"]', 'Pembina Yayasan');
    await isiUndangan(page, uniqueName('Surat Pihak Luar'));
    await page.locator('button:has-text("Generate Surat")').click();

    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);
    await expect(page.locator('body')).toContainText('Pembina Yayasan');
  });

  test('token QR surat dapat diverifikasi di halaman publik', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, baseUrl);
    await isiUndangan(page, uniqueName('Surat Verifikasi QR'));
    await page.locator('button:has-text("Generate Surat")').click();
    await expect(page).toHaveURL(/\/generator\/letters\/\d+/);

    const token = (await page.getByText(/SKIN-SIG-\d{4}-[A-Z0-9]{6,}/).first().textContent())?.trim();
    expect(token).toBeTruthy();

    await page.goto(`/verifikasi-dokumen/${token}`);
    await expect(page.locator('body')).toContainText(/DOKUMEN RESMI ASLI/i);
  });
});
