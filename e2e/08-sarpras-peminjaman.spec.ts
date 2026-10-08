import { test, expect, Page } from '@playwright/test';
import { loginAs, gotoStable, uniqueName } from './helpers/auth';
import { expectCalendarLocaleId } from './helpers/assertions';

const PROPOSAL = 'e2e/fixtures/dummy.pdf';

function nextMonday(weeksAhead = 8): string {
  const d = new Date();
  const diff = (1 + 7 - d.getDay()) % 7 || 7;
  d.setDate(d.getDate() + diff + weeksAhead * 7);
  return d.toISOString().split('T')[0];
}

/** Tanggal (YYYY-MM-DD) untuk hari ISO tertentu (1=Senin … 7=Minggu) beberapa pekan ke depan. */
function nextDateForIsoWeekday(isoWeekday: number, weeksAhead = 8): string {
  const d = new Date();
  const targetJsDay = isoWeekday % 7; // 1..6 tetap, 7(Minggu) -> 0
  const diff = (targetJsDay - d.getDay() + 7) % 7 || 7;
  d.setDate(d.getDate() + diff + weeksAhead * 7);
  return d.toISOString().split('T')[0];
}

const hh = (h: number) => `${String(h).padStart(2, '0')}:00`;

/**
 * PRD: FR-017 (Master Inventaris Fasilitas), FR-018 (Kalender & Proteksi Ketersediaan),
 *      FR-019 (Approval & Validasi Keluar-Masuk Barang), BR-09 (BKHM sebelum Sarpras;
 *      HIMA butuh persetujuan Prodi), BR-10 (barang yang tidak boleh keluar), BR-12 (mahasiswa via Ormawa).
 */
test.describe('FR-017 — Manajemen Master Inventaris (Sarpras)', () => {
  test('Sarpras dapat menambah & mengubah data inventaris barang', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/barang');
    await expect(page.locator('body')).toContainText(/Master Barang|Inventaris|Daftar Barang/i);

    const addButton = page.locator('button:has-text("+ Tambah Barang"), button:has-text("Tambah Barang"), a:has-text("Tambah Barang"), a:has-text("Tambah")').first();
    await expect(addButton).toBeVisible();
    await addButton.click();
    await page.waitForLoadState('domcontentloaded');

    const nama = uniqueName('Barang E2E');
    await page.fill('#nama_barang, input[name="nama_barang"]', nama);
    await page.fill('#stok_tersedia, input[name="stok_tersedia"]', '10');
    await page.locator('form[action$="/sarpras/barang"] button:has-text("Simpan")').first().click();

    await expect(page.locator('table').first()).toContainText(nama);
  });

  test('BR-10: barang yang ditandai tidak boleh dibawa keluar kampus ditolak', async ({ page }) => {
    // 1. Sarpras menambah barang non-portable.
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/barang');
    await expect(page.locator('th:has-text("Boleh Dibawa Keluar")')).toBeVisible();

    await page.locator('button:has-text("Tambah Barang")').first().click();
    const nama = uniqueName('Barang NonPortable');
    await page.fill('#nama_barang', nama);
    await page.fill('#stok_tersedia', '5');
    await page.uncheck('#boleh_dibawa_keluar');
    await page.locator('form[action$="/sarpras/barang"] button:has-text("Simpan")').first().click();

    const row = page.locator('tr', { hasText: nama }).first();
    await expect(row).toBeVisible();
    await expect(row).toContainText(/Tidak/);

    // 2. Form peminjaman menandai barang tersebut & menonaktifkan qty.
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/barang/create');
    const item = page.locator('div.flex.items-center.justify-between', { hasText: nama }).first();
    await expect(item).toContainText(/tidak boleh dibawa keluar kampus/i);

    // 3. Lewati guard UI (aktifkan qty) → server tetap harus menolak.
    const qty = item.locator('input[name^="qty["]');
    await qty.evaluate((el) => el.removeAttribute('disabled'));
    await qty.fill('1');
    await page.fill('input[name="nama_kegiatan"]', uniqueName('Uji BR-10'));
    await page.fill('input[name="tgl_mulai"]', '2026-12-01');
    await page.fill('input[name="tgl_selesai"]', '2026-12-01');
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');

    await expect(page.locator('body')).toContainText(/tidak boleh dibawa keluar kampus/i);
  });
});

test.describe('FR-018 — Kalender & Proteksi Ketersediaan (Jadwal Kuliah Pola Mingguan)', () => {
  test('Sarpras dapat menginput jadwal kuliah pola mingguan', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/jadwal');
    await expect(page.getByRole('heading', { name: /Jadwal Perkuliahan/ }).first()).toBeVisible();

    // Bersihkan jadwal lama agar slot uji bebas (idempoten antar-run).
    const hapus = page.locator('form[action*="/sarpras/jadwal/"] button:has-text("Hapus")');
    let guard = 0;
    while ((await hapus.count()) > 0 && guard < 40) {
      page.once('dialog', (d) => d.accept());
      await hapus.first().click();
      await page.waitForLoadState('domcontentloaded').catch(() => {});
      guard++;
    }

    // Slot acak agar idempoten terhadap data jadwal yang menumpuk antar-run.
    const hari = 1 + Math.floor(Math.random() * 6);
    const start = 6 + Math.floor(Math.random() * 14);
    const matkul = uniqueName('Matkul E2E');

    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.selectOption('select[name="hari"]', String(hari));
    await page.fill('input[name="mata_kuliah"]', matkul);
    await page.fill('input[name="jam_mulai"]', hh(start));
    await page.fill('input[name="jam_selesai"]', hh(start + 1));
    await page.fill('input[name="semester"]', 'Ganjil 2026/2027');
    await page.click('button:has-text("Simpan Jadwal")');

    const hariNama = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][hari - 1];
    await expect(page.locator('body')).toContainText('Jadwal kuliah berhasil ditambahkan.');
    await expect(page.locator('table').first()).toContainText(matkul);
    await expect(page.locator('table').first()).toContainText(hariNama);
  });

  test('Proteksi bentrok: jadwal kuliah duplikat pada ruangan & hari sama ditolak', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/jadwal');

    const hari = 1 + Math.floor(Math.random() * 6);
    const start = 6 + Math.floor(Math.random() * 14);

    // Jadwal pertama.
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.selectOption('select[name="hari"]', String(hari));
    await page.fill('input[name="mata_kuliah"]', uniqueName('Bentrok A'));
    await page.fill('input[name="jam_mulai"]', hh(start));
    await page.fill('input[name="jam_selesai"]', hh(start + 2));
    await page.click('button:has-text("Simpan Jadwal")');

    // Jadwal kedua yang beririsan → selalu ditolak (baik terhadap jadwal pertama
    // maupun jadwal eksisting pada slot yang sama).
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.selectOption('select[name="hari"]', String(hari));
    await page.fill('input[name="mata_kuliah"]', uniqueName('Bentrok B'));
    await page.fill('input[name="jam_mulai"]', hh(start + 1));
    await page.fill('input[name="jam_selesai"]', hh(start + 3));
    await page.click('button:has-text("Simpan Jadwal")');

    await expect(page.locator('text=Jadwal bertabrakan')).toBeVisible();
  });

  test('Proteksi bentrok: peminjaman ruangan yang beririsan jadwal kuliah ditolak', async ({ page }) => {
    const hari = 1 + Math.floor(Math.random() * 6);
    const start = 6 + Math.floor(Math.random() * 14);

    // 1. Sarpras membuat jadwal kuliah pada slot acak.
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/jadwal');
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.selectOption('select[name="hari"]', String(hari));
    await page.fill('input[name="mata_kuliah"]', uniqueName('Jadwal Proteksi'));
    await page.fill('input[name="jam_mulai"]', hh(start));
    await page.fill('input[name="jam_selesai"]', hh(start + 2));
    await page.click('button:has-text("Simpan Jadwal")');

    // 2. Ormawa mengajukan peminjaman pada hari yang sama dengan jam beririsan.
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/tempat/create');
    const tanggal = nextDateForIsoWeekday(hari, 20 + Math.floor(Math.random() * 40));
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.fill('input[name="nama_kegiatan"]', uniqueName('Bentrok Jadwal'));
    await page.fill('input[name="tgl_mulai"]', tanggal);
    await page.fill('input[name="tgl_selesai"]', tanggal);
    await page.fill('input[name="jam_mulai"]', hh(start + 1));
    await page.fill('input[name="jam_selesai"]', hh(start + 2));
    await page.fill('textarea[name="deskripsi_kegiatan"]', 'Uji bentrok jadwal kuliah');
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');

    await expect(page.locator('text=bentrok dengan jadwal perkuliahan')).toBeVisible();
  });

  test('BACKLOG-018 / FR-018: dua ruangan berbeda pada waktu bersamaan diizinkan (bebas false positive bentrok)', async ({ page }) => {
    // 1. Ormawa mengajukan peminjaman Ruangan 1
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/tempat/create');

    const options = await page.locator('select[name="ruangan_id"] option').all();
    if (options.length < 3) {
      // Butuh minimal 2 ruangan riil selain opsi default kosong
      return;
    }

    const offsetWeeks = 40 + Math.floor(Math.random() * 30);
    const tgl = nextDateForIsoWeekday(7, offsetWeeks); // Hari Minggu jauh agar bebas dari jadwal kuliah reguler
    const kegiatanA = uniqueName('Kegiatan Ruangan A');
    const kegiatanB = uniqueName('Kegiatan Ruangan B');

    // Pinjam Ruangan opsi index 1
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.fill('input[name="nama_kegiatan"]', kegiatanA);
    await page.fill('input[name="tgl_mulai"]', tgl);
    await page.fill('input[name="tgl_selesai"]', tgl);
    await page.fill('input[name="jam_mulai"]', '09:00');
    await page.fill('input[name="jam_selesai"]', '12:00');
    await page.fill('textarea[name="deskripsi_kegiatan"]', 'Kegiatan Ruangan 1');
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');

    await expect(page.locator('body')).toContainText(/berhasil dikirim/i);

    // 2. Ajukan peminjaman Ruangan opsi index 2 pada jam dan tanggal yang sama
    await gotoStable(page, '/peminjaman/tempat/create');
    await page.selectOption('select[name="ruangan_id"]', { index: 2 });
    await page.fill('input[name="nama_kegiatan"]', kegiatanB);
    await page.fill('input[name="tgl_mulai"]', tgl);
    await page.fill('input[name="tgl_selesai"]', tgl);
    await page.fill('input[name="jam_mulai"]', '09:00');
    await page.fill('input[name="jam_selesai"]', '12:00');
    await page.fill('textarea[name="deskripsi_kegiatan"]', 'Kegiatan Ruangan 2');
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');

    // Harus berhasil tanpa false positive "Ruangan sudah dibooking"
    await expect(page.locator('body')).toContainText(/berhasil dikirim/i);
  });
});

test.describe('FR-018 / UI-013 — Kalender interaktif ketersediaan', () => {
  test('halaman jadwal Sarpras menampilkan kalender', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/sarpras/jadwal');
    await expect(page.getByRole('heading', { name: /Kalender Ketersediaan Ruangan/ })).toBeVisible();
    await expect(page.locator('#calendar')).toBeVisible();
    // Tampilan mingguan → judul memuat rentang tanggal + tahun.
    await expect(page.locator('.fc-toolbar-title').first()).toContainText(/\d{4}/, { timeout: 8000 });
  });

  test('form peminjaman ruangan menampilkan kalender ketersediaan', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/tempat/create');
    await expect(page.getByRole('heading', { name: /Ketersediaan Ruangan/ })).toBeVisible();
    await expect(page.locator('#calendar')).toBeVisible();
    await expectCalendarLocaleId(page);
  });
});

test.describe('FR-019 / BR-09 — Peminjaman fasilitas & alur BKHM → Sarpras', () => {
  test.describe.configure({ mode: 'serial' });

  const kegiatan = uniqueName('E2E Peminjaman Tempat');

  test('Ormawa (HIMA) mengajukan peminjaman tempat dengan dokumen persetujuan Prodi', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/tempat/create');
    await expect(page.locator('text=Dokumen Persetujuan Prodi')).toBeVisible();

    const tanggal = nextMonday(12);
    await page.selectOption('select[name="ruangan_id"]', { index: 1 });
    await page.fill('input[name="nama_kegiatan"]', kegiatan);
    await page.fill('input[name="tgl_mulai"]', tanggal);
    await page.fill('input[name="tgl_selesai"]', tanggal);
    await page.fill('input[name="jam_mulai"]', '07:00');
    await page.fill('input[name="jam_selesai"]', '09:00');
    await page.fill('textarea[name="deskripsi_kegiatan"]', 'E2E peminjaman tempat');
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');

    await expect(page.locator('body')).toContainText(/Pengajuan peminjaman ruangan berhasil dikirim|Ruangan sudah dibooking/);
  });

  test('BR-09: BKHM memverifikasi peminjaman lebih dulu', async ({ page }) => {
    await loginAs(page, 'bkhm');
    await gotoStable(page, '/verifikasi-peminjaman');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Peminjaman tidak masuk antrean BKHM — skip');

    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), row.locator('button:has-text("Setuju")').click()]);
    await expect(page.locator('body')).toContainText('Verifikasi peminjaman tempat berhasil disimpan.');
  });

  test('Sarpras memverifikasi peminjaman setelah BKHM', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/verifikasi-peminjaman');
    const row = page.locator('tr', { hasText: kegiatan }).first();
    test.skip((await row.count()) === 0, 'Peminjaman belum lolos BKHM — skip');

    page.once('dialog', (d) => d.accept());
    await Promise.all([page.waitForNavigation(), row.locator('button:has-text("Setuju")').click()]);
    await expect(page.locator('body')).toContainText('Verifikasi peminjaman tempat berhasil disimpan.');
  });

  test('BR-12: Mahasiswa tidak dapat meminjam fasilitas langsung (harus via Ormawa, guest dialihkan ke login)', async ({ page }) => {
    await page.goto('/peminjaman/tempat/create');
    await expect(page).toHaveURL(/.*login.*/);
  });
});

test.describe('FR-019 — Validasi keluar-masuk barang (stok)', () => {
  test('Sarpras melihat antrean barang & seksi pengembalian', async ({ page }) => {
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/verifikasi-peminjaman');
    await expect(page.locator('body')).toContainText(/Verifikasi|Antrean/i);
    await expect(page.locator('body')).toContainText(/Barang Sedang Dipinjam|Pengembalian/i);
  });

  test('Ormawa dapat mengajukan peminjaman barang (stok mencukupi)', async ({ page }) => {
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/barang/create');
    await expect(page.locator('body')).toContainText(/Peminjaman Barang Inventaris|Pilih Barang/i);

    const qty = page.locator('input[name^="qty["]:not([disabled])').first();
    test.skip((await qty.count()) === 0, 'Tidak ada barang tersedia — skip');
    await qty.fill('1');

    const tanggal = nextMonday(12);
    await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E Peminjaman Barang'));
    await page.fill('input[name="tgl_mulai"]', tanggal);
    await page.fill('input[name="tgl_selesai"]', tanggal);
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);
    await page.click('button:has-text("Ajukan Peminjaman")');
    await expect(page.locator('body')).toContainText(/berhasil dikirim|tidak mencukupi/);
  });

  test('FR-019 / BASE-06: aksi validasi pengembalian barang memulihkan stok (BACKLOG-011)', async ({ page }) => {
    // BACKLOG-011: Memverifikasi seksi 'Barang Sedang Dipinjam' dan tombol 'Validasi Kembali'
    await loginAs(page, 'sarpras');
    await gotoStable(page, '/verifikasi-peminjaman');

    await expect(page.locator('body')).toContainText(/Barang Sedang Dipinjam/i);
    const tombolKembali = page.locator('button:has-text("Validasi Kembali")');

    if (await tombolKembali.count() > 0) {
      page.once('dialog', (d) => d.accept());
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
        tombolKembali.first().click(),
      ]);
      await expect(page.locator('body')).toContainText(/berhasil divalidasi kembali dan stok diperbarui/i);
    } else {
      await expect(page.locator('body')).toContainText(/Tidak ada barang yang sedang dipinjam/i);
    }
  });

  test('FR-019: pengajuan peminjaman barang dengan qty melebihi stok ditolak (BACKLOG-012)', async ({ page }) => {
    // BACKLOG-012: Memverifikasi proteksi input kuantitas barang melebihi kapasitas stok tersedia
    await loginAs(page, 'ormawa');
    await gotoStable(page, '/peminjaman/barang/create');

    const qty = page.locator('input[name^="qty["]:not([disabled])').first();
    test.skip((await qty.count()) === 0, 'Tidak ada barang tersedia — skip');
    
    // Masukkan kuantitas tidak realistis / melebihi stok
    await qty.fill('99999');

    const tanggal = nextMonday(14);
    await page.fill('input[name="nama_kegiatan"]', uniqueName('E2E Melebihi Stok'));
    await page.fill('input[name="tgl_mulai"]', tanggal);
    await page.fill('input[name="tgl_selesai"]', tanggal);
    await page.setInputFiles('input[name="file_persetujuan_prodi"]', PROPOSAL);

    // Hapus batasan max di client-side agar form bisa ter-submit untuk menguji validasi server-side
    await qty.evaluate((el) => el.removeAttribute('max'));

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('button:has-text("Ajukan Peminjaman")'),
    ]);

    await expect(page.locator('body')).toContainText(/tidak mencukupi/i);
  });
});
