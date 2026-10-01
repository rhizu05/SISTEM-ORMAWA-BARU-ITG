<?php

use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\KomunikasiController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\NudgeController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProposalGeneratorController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\Sarpras\MasterBarangController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDataController;
use App\Http\Controllers\ProgramKerjaController;
use App\Http\Controllers\RegulasiController;
use App\Http\Controllers\TiketPublicController;
use App\Http\Controllers\Bkhm\BkhmTiketController;
use App\Http\Controllers\DokumenVerifikasiController;
use Illuminate\Support\Facades\Route;

// Public Route: Portal Layanan Publik Mahasiswa (P-02) kini di root
Route::get('/', [TiketPublicController::class, 'index'])->name('layanan.index');

// Alias lama: /layanan dialihkan ke root
Route::get('/layanan', function () {
    return redirect()->route('layanan.index', [], 301);
})->name('layanan.redirect');

// FR-021 / UI-009: pusat informasi dapat diakses publik tanpa login.
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index');

// SEC-01: unduhan lampiran informasi (publik, tetapi file tersimpan privat).
Route::get('/informasi/pengumuman/{pengumuman}/lampiran', [InformasiController::class, 'lampiranPengumuman'])->name('informasi.pengumuman.lampiran');
Route::get('/informasi/regulasi/{regulasi}/unduh', [InformasiController::class, 'unduhRegulasi'])->name('informasi.regulasi.unduh');

// Detail Berita & Pengumuman Publik
Route::get('/informasi/{pengumuman}', [InformasiController::class, 'show'])->name('informasi.show');

// Portal Layanan Publik Mahasiswa (Aspirasi, Konseling, Prestasi, Tracking)
Route::prefix('layanan')->name('layanan.')->group(function () {
    Route::get('/aspirasi', [TiketPublicController::class, 'aspirasiCreate'])->name('aspirasi.create');
    Route::post('/aspirasi', [TiketPublicController::class, 'aspirasiStore'])->middleware('throttle:layanan-publik')->name('aspirasi.store');
    Route::get('/konseling', [TiketPublicController::class, 'konselingCreate'])->name('konseling.create');
    Route::post('/konseling', [TiketPublicController::class, 'konselingStore'])->middleware('throttle:layanan-publik')->name('konseling.store');
    Route::get('/prestasi', [TiketPublicController::class, 'prestasiCreate'])->name('prestasi.create');
    Route::post('/prestasi', [TiketPublicController::class, 'prestasiStore'])->middleware('throttle:layanan-publik')->name('prestasi.store');
    Route::get('/cek-status', [TiketPublicController::class, 'tracking'])->name('cek-status');
    Route::post('/cek-status', [TiketPublicController::class, 'trackingVerify'])->middleware('throttle:layanan-tracking')->name('cek-status.verify');
    Route::get('/tracking', [TiketPublicController::class, 'tracking'])->name('tracking');
    Route::post('/tracking', [TiketPublicController::class, 'trackingVerify'])->middleware('throttle:layanan-tracking')->name('tracking.verify');
    Route::post('/konseling/{tiket:kode_tiket}/konfirmasi', [TiketPublicController::class, 'konselingKonfirmasi'])->middleware('throttle:layanan-publik')->name('konseling.konfirmasi');
    Route::get('/lampiran/{tiket}', [TiketPublicController::class, 'unduhLampiran'])->name('lampiran');
});
Route::get('/prestasi/showcase', [TiketPublicController::class, 'showcasePrestasi'])->name('prestasi.showcase');

// Public Document Verification (Digital Signature & QR Code)
Route::get('/verifikasi-dokumen', [DokumenVerifikasiController::class, 'index'])->name('dokumen.verifikasi.index');
Route::post('/verifikasi-dokumen', [DokumenVerifikasiController::class, 'search'])->name('dokumen.verifikasi.search');
Route::get('/verifikasi/dokumen/{token}', [DokumenVerifikasiController::class, 'show'])->name('dokumen.verifikasi');

// Modul Lapor Kendala / Bug Sistem (Universal untuk Seluruh Role & Publik)
Route::get('/lapor-kendala', [\App\Http\Controllers\LaporanBugController::class, 'create'])->name('bug.create');
Route::post('/lapor-kendala', [\App\Http\Controllers\LaporanBugController::class, 'store'])->middleware('throttle:layanan-publik')->name('bug.store');
Route::get('/lapor-kendala/screenshot/{bug}', [\App\Http\Controllers\LaporanBugController::class, 'unduhScreenshot'])->name('bug.screenshot');
Route::get('/verifikasi-dokumen/{token}', [DokumenVerifikasiController::class, 'show'])->name('dokumen.verifikasi.alias');

// Auth Routes (Breeze)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Custom Profile Data (Logo, TTD)
    Route::patch('/profile/data', [ProfileDataController::class, 'update'])->name('profile.data.update');

    // Pusat Informasi & Regulasi (GET sudah publik; ini aksi penulisan)
    Route::post('/informasi/pengumuman', [InformasiController::class, 'storePengumuman'])->name('informasi.pengumuman.store');
    Route::delete('/informasi/pengumuman/{pengumuman}', [InformasiController::class, 'destroyPengumuman'])->name('informasi.pengumuman.destroy');
    Route::post('/informasi/regulasi', [InformasiController::class, 'storeRegulasi'])->name('informasi.regulasi.store');
    Route::delete('/informasi/regulasi/{regulasi}', [InformasiController::class, 'destroyRegulasi'])->name('informasi.regulasi.destroy');

    // Kurasi Berita (Ormawa, BEM, BPM) oleh BKHM (Humas Institusi)
    Route::middleware(['role:bkhm|admin', 'admin.readonly'])->prefix('bkhm')->name('bkhm.')->group(function () {
        Route::get('/kurasi-berita', [InformasiController::class, 'kurasiIndex'])->name('kurasi.index');
        Route::post('/kurasi-berita/{pengumuman}/approve', [InformasiController::class, 'kurasiApprove'])->name('kurasi.approve');
        Route::post('/kurasi-berita/{pengumuman}/reject', [InformasiController::class, 'kurasiReject'])->name('kurasi.reject');
    });

    // FR-025: pusat notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.readAll');

    // FR-011: follow-up/komunikasi pengajuan
    Route::post('/pengajuan/{pengajuan}/komunikasi', [KomunikasiController::class, 'store'])->name('pengajuan.komunikasi.store');
    Route::post('/pengajuan/{pengajuan}/nudge', [NudgeController::class, 'kirim'])->name('pengajuan.nudge');

    // Jadwal Rapat
    Route::get('/rapat', [RapatController::class, 'index'])->name('rapat.index');
    Route::post('/rapat', [RapatController::class, 'store'])->name('rapat.store');
    Route::delete('/rapat/{rapat}', [RapatController::class, 'destroy'])->name('rapat.destroy');
    
    // BPM Role: Dashboard & Management
    Route::middleware(['role:bpm|admin', 'admin.readonly'])->prefix('bpm')->name('bpm.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard'); // This will actually use the index method, but we can handle it in Controller
        Route::get('/aspirasi', [AspirasiController::class, 'index'])->name('aspirasi.index');
        Route::post('/aspirasi/{tiket}/teruskan', [AspirasiController::class, 'teruskanKeBkhm'])->name('aspirasi.teruskan');
        Route::get('/regulasi', [RegulasiController::class, 'index'])->name('regulasi.index');
        Route::get('/regulasi/create', [RegulasiController::class, 'create'])->name('regulasi.create');
        Route::post('/regulasi', [RegulasiController::class, 'store'])->name('regulasi.store');
        Route::delete('/regulasi/{regulasi}', [RegulasiController::class, 'destroy'])->name('regulasi.destroy');
        Route::get('/sp', [\App\Http\Controllers\Bpm\SpController::class, 'index'])->name('sp.index');
        Route::get('/sp/create', [\App\Http\Controllers\Bpm\SpController::class, 'create'])->name('sp.create');
        Route::post('/sp', [\App\Http\Controllers\Bpm\SpController::class, 'store'])->name('sp.store');
        Route::get('/sp/{sp}', [\App\Http\Controllers\Bpm\SpController::class, 'show'])->name('sp.show');
    });

    // BPM legacy aspirasi: removed duplicate global /aspirasi (kept only bpm/aspirasi.* to avoid shadow)


    // Ormawa, BEM, BPM: Modul Pengajuan, LPJ, Peminjaman, Generator
    Route::middleware(['role:ormawa|bem|bpm|admin', 'admin.readonly'])->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/pengajuan/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{pengajuan}', [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::post('/pengajuan/{pengajuan}/ajukan', [PengajuanController::class, 'ajukan'])->name('pengajuan.ajukan');
        
        // Form upload LPJ khusus pengaju (Ormawa, BEM, BPM)
        Route::get('/lpj/create/{pengajuan}', [LpjController::class, 'create'])->name('lpj.create');
        Route::post('/lpj/{pengajuan}', [LpjController::class, 'store'])->name('lpj.store');

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        // Opsi A: Riwayat terpisah per jenis - hanya milik sendiri
        Route::get('/peminjaman/tempat', [PeminjamanController::class, 'historyTempat'])->name('peminjaman.tempat.index');
        Route::get('/peminjaman/barang', [PeminjamanController::class, 'historyBarang'])->name('peminjaman.barang.index');
        Route::get('/peminjaman/tempat/create', [PeminjamanController::class, 'createTempat'])->name('peminjaman.tempat.create');
        Route::get('/peminjaman/tempat/jadwal-ruangan', [PeminjamanController::class, 'jadwalRuangan'])->name('peminjaman.tempat.jadwal-ruangan');
        Route::post('/peminjaman/tempat', [PeminjamanController::class, 'storeTempat'])->name('peminjaman.tempat.store');
        Route::get('/peminjaman/barang/create', [PeminjamanController::class, 'createBarang'])->name('peminjaman.barang.create');
        Route::post('/peminjaman/barang', [PeminjamanController::class, 'storeBarang'])->name('peminjaman.barang.store');

        // Proposal & Document Generator
        Route::get('/generator', [ProposalGeneratorController::class, 'index'])->name('generator.index');
        Route::get('/generator/create', [ProposalGeneratorController::class, 'create'])->name('generator.create');
        Route::post('/generator', [ProposalGeneratorController::class, 'store'])->name('generator.store');
        Route::get('/generator/{proposal}', [ProposalGeneratorController::class, 'show'])->name('generator.show');
        
        // New Letter Generator
        Route::get('/generator/letters/create', [ProposalGeneratorController::class, 'createLetter'])->name('generator.letters.create');
        Route::post('/generator/letters', [ProposalGeneratorController::class, 'storeLetter'])->name('generator.letters.store');
        Route::get('/generator/letters/{letter}', [ProposalGeneratorController::class, 'showLetter'])->name('generator.letters.show');
        Route::get('/generator/letters/{letter}/pdf', [ProposalGeneratorController::class, 'pdfLetter'])->name('generator.letters.pdf');

        // LPJ Generator
        Route::get('/generator/lpj/create/{proposal?}', [ProposalGeneratorController::class, 'createLpj'])->name('generator.lpj.create');
        Route::post('/generator/lpj', [ProposalGeneratorController::class, 'storeLpj'])->name('generator.lpj.store');
        Route::get('/generator/lpj/{lpj}', [ProposalGeneratorController::class, 'showLpj'])->name('generator.lpj.show');
        Route::get('/generator/lpj/{lpj}/pdf', [ProposalGeneratorController::class, 'pdfLpj'])->name('generator.lpj.pdf');

        // Surat Peringatan (Ormawa melihat SP milik sendiri)
        Route::get('/sp/saya', [\App\Http\Controllers\SuratPeringatanController::class, 'index'])->name('sp.saya.index');
        Route::get('/sp/saya/{sp}', [\App\Http\Controllers\SuratPeringatanController::class, 'show'])->name('sp.saya.show');
        Route::get('/sp/saya/{sp}/pdf', [\App\Http\Controllers\SuratPeringatanController::class, 'pdf'])->name('sp.saya.pdf');
    });

    // Cetak Surat Izin Peminjaman (bisa diakses ormawa pemohon, BKHM, Sarpras, dan Admin)
    Route::middleware(['role:ormawa|bem|bpm|bkhm|sarpras|admin', 'admin.readonly'])->group(function () {
        Route::get('/peminjaman/tempat/{peminjaman}/cetak', [PeminjamanController::class, 'cetakTempat'])->name('peminjaman.tempat.cetak');
        Route::get('/peminjaman/barang/{peminjaman}/cetak', [PeminjamanController::class, 'cetakBarang'])->name('peminjaman.barang.cetak');
    });
 
     // Modul Monitoring & Arsip LPJ serta Arsip Digital Dokumen (dapat dimonitor oleh Ormawa, BEM, BPM, BKHM, WR3, Admin)
     Route::middleware(['role:ormawa|bem|bpm|bkhm|wr3|admin', 'admin.readonly'])->group(function () {
         Route::get('/lpj', [LpjController::class, 'index'])->name('lpj.index');
         Route::get('/archive', [ProposalGeneratorController::class, 'archive'])->name('archive.index');
     });

    // Rute cetak dokumen bisa diakses ormawa & verifikator
    Route::get('/generator/{proposal}/print', [ProposalGeneratorController::class, 'print'])
        ->middleware(['auth', 'admin.readonly'])
        ->name('generator.print');

    // FR-008: unduh PDF proposal
    Route::get('/generator/{proposal}/pdf', [ProposalGeneratorController::class, 'pdf'])
        ->middleware(['auth', 'admin.readonly'])
        ->name('generator.pdf');

    // Dokumen privat (SEC-01): proposal & LPJ diakses via controller ber-RBAC
    Route::get('/dokumen/pengajuan/{pengajuan}/proposal', [DocumentController::class, 'proposal'])->name('dokumen.proposal');
    Route::get('/dokumen/pengajuan/{pengajuan}/lpj', [DocumentController::class, 'lpj'])->name('dokumen.lpj');
    Route::get('/dokumen/dana/{dana}/bukti-transfer', [DocumentController::class, 'buktiTransfer'])->name('dokumen.bukti-transfer');
    Route::get('/dokumen/peminjaman-tempat/{peminjaman}/prodi', [DocumentController::class, 'persetujuanProdiTempat'])->name('dokumen.peminjaman-tempat.prodi');
    Route::get('/dokumen/peminjaman-barang/{peminjaman}/prodi', [DocumentController::class, 'persetujuanProdiBarang'])->name('dokumen.peminjaman-barang.prodi');

    // Verifikator Roles: Modul Verifikasi (BEM, BPM, BKHM, WR3, Bendahara)
    Route::middleware(['role:bem|bpm|bkhm|wr3|bendahara|admin', 'admin.readonly'])->group(function () {
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{pengajuan}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{pengajuan}/process', [VerifikasiController::class, 'process'])->name('verifikasi.process');
        // BR-11: tandai evaluasi termin selesai
        Route::post('/verifikasi/{pengajuan}/evaluasi-termin', [VerifikasiController::class, 'evaluasiTermin'])->name('verifikasi.evaluasi-termin');
    });


    // Peminjaman Verifikasi (BKHM & Sarpras)
    Route::middleware(['role:bkhm|sarpras|admin', 'admin.readonly'])->group(function () {
        Route::get('/verifikasi-peminjaman', [PeminjamanController::class, 'antrian'])->name('peminjaman.verifikasi.index');
        Route::post('/verifikasi-peminjaman/tempat/{peminjaman}', [PeminjamanController::class, 'prosesTempat'])->name('peminjaman.tempat.proses');
        Route::post('/verifikasi-peminjaman/barang/{peminjaman}', [PeminjamanController::class, 'prosesBarang'])->name('peminjaman.barang.proses');
        Route::post('/verifikasi-peminjaman/barang/{peminjaman}/kembali', [PeminjamanController::class, 'kembalikanBarang'])->name('peminjaman.barang.kembali');
    });

    // Sarpras Khusus: Manajemen Master Barang Inventaris
    Route::middleware(['role:sarpras|admin', 'admin.readonly'])->prefix('sarpras')->name('sarpras.')->group(function () {
        Route::get('/barang', [MasterBarangController::class, 'index'])->name('barang.index');
        Route::post('/barang', [MasterBarangController::class, 'store'])->name('barang.store');
        Route::put('/barang/{barang}', [MasterBarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [MasterBarangController::class, 'destroy'])->name('barang.destroy');

        // Master Ruangan Kampus
        Route::get('/ruangan', [\App\Http\Controllers\Sarpras\MasterRuanganController::class, 'index'])->name('ruangan.index');
        Route::post('/ruangan', [\App\Http\Controllers\Sarpras\MasterRuanganController::class, 'store'])->name('ruangan.store');
        Route::put('/ruangan/{ruangan}', [\App\Http\Controllers\Sarpras\MasterRuanganController::class, 'update'])->name('ruangan.update');
        Route::delete('/ruangan/{ruangan}', [\App\Http\Controllers\Sarpras\MasterRuanganController::class, 'destroy'])->name('ruangan.destroy');

        // Q-SAR-02: jadwal perkuliahan pola mingguan
        Route::get('/jadwal', [\App\Http\Controllers\Sarpras\JadwalKuliahController::class, 'index'])->name('jadwal.index');
        Route::post('/jadwal', [\App\Http\Controllers\Sarpras\JadwalKuliahController::class, 'store'])->name('jadwal.store');
        Route::delete('/jadwal/{jadwal}', [\App\Http\Controllers\Sarpras\JadwalKuliahController::class, 'destroy'])->name('jadwal.destroy');
    });

    // Bendahara Khusus: Proses Pencairan
    Route::middleware(['role:bendahara'])->group(function () {
        Route::post('/bendahara/proses/{pengajuan}', [BendaharaController::class, 'proses'])->name('bendahara.proses');
        Route::get('/bendahara/export-pencairan', [BendaharaController::class, 'export'])->name('bendahara.export');
        Route::get('/bendahara/export-excel', [BendaharaController::class, 'exportExcel'])->name('bendahara.export.excel');
        Route::get('/bendahara/export-pdf', [BendaharaController::class, 'exportPdf'])->name('bendahara.export.pdf');
    });

    Route::get('/bkhm/surat-peringatan/{sp}/pdf', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spPdf'])
        ->middleware(['role:bkhm|wr3|admin', 'admin.readonly'])
        ->name('bkhm.sp.pdf');

    // BKHM Khusus: 8 menu sesuai spec
    Route::middleware(['role:bkhm|admin', 'admin.readonly'])->prefix('bkhm')->name('bkhm.')->group(function () {
        Route::get('/saldo', [\App\Http\Controllers\Bkhm\BkhmController::class, 'saldo'])->name('saldo.index');
        // Q-BKHM-02: periode anggaran
        Route::post('/periode', [\App\Http\Controllers\Bkhm\BkhmController::class, 'storePeriode'])->name('periode.store');
        Route::post('/periode/{periode}/aktifkan', [\App\Http\Controllers\Bkhm\BkhmController::class, 'aktifkanPeriode'])->name('periode.aktifkan');
        Route::get('/arsip-surat', [\App\Http\Controllers\Bkhm\BkhmController::class, 'arsipSurat'])->name('arsip.index');
        Route::get('/surat-peringatan/create', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spCreate'])->name('sp.create');
        Route::post('/surat-peringatan', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spStore'])->name('sp.store');
        Route::get('/surat-peringatan/{sp}', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spShow'])->name('sp.show');
        Route::post('/surat-peringatan/{sp}/teruskan', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spTeruskan'])->name('sp.teruskan');
        Route::post('/surat-peringatan/{sp}/kembalikan', [\App\Http\Controllers\Bkhm\BkhmController::class, 'spKembalikan'])->name('sp.kembalikan');
        Route::get('/verifikasi-tempat', [\App\Http\Controllers\Bkhm\BkhmController::class, 'verifikasiTempat'])->name('verifikasi-tempat.index');
    });

    // Ekspor Keuangan Resmi (dapat diunduh BKHM, WR3, Admin)
    Route::middleware(['role:bkhm|wr3|admin', 'admin.readonly'])->group(function () {
        Route::get('/bkhm/export-excel', [\App\Http\Controllers\Bkhm\BkhmController::class, 'exportExcel'])->name('bkhm.export.excel');
        Route::get('/bkhm/export-pdf', [\App\Http\Controllers\Bkhm\BkhmController::class, 'exportPdf'])->name('bkhm.export.pdf');
    });

    Route::middleware(['role:bkhm|admin', 'admin.readonly'])->prefix('bkhm')->name('bkhm.')->group(function () {

        // Manajemen Layanan & Tiket Mahasiswa (Konseling, Aspirasi Eskalasi, Prestasi & Delegasi)
        Route::get('/konseling', [BkhmTiketController::class, 'konselingIndex'])->name('konseling.index');
        Route::get('/konseling/{tiket}', [BkhmTiketController::class, 'konselingShow'])->name('konseling.show');
        Route::post('/konseling/{tiket}/update', [BkhmTiketController::class, 'konselingUpdate'])->name('konseling.update');
        Route::get('/tiket-aspirasi', [BkhmTiketController::class, 'aspirasiIndex'])->name('tiket-aspirasi.index');
        Route::post('/tiket-aspirasi/{tiket}/update', [BkhmTiketController::class, 'aspirasiUpdate'])->name('tiket-aspirasi.update');
        Route::get('/tiket-prestasi', [BkhmTiketController::class, 'prestasiIndex'])->name('tiket-prestasi.index');
        Route::post('/tiket-prestasi/{tiket}/update', [BkhmTiketController::class, 'prestasiUpdate'])->name('tiket-prestasi.update');

        // Helpdesk & Triage Kendala / Bug Sistem
        Route::get('/laporan-kendala', [\App\Http\Controllers\LaporanBugController::class, 'bkhmIndex'])->name('bug.index');
        Route::post('/laporan-kendala/{bug}/triage', [\App\Http\Controllers\LaporanBugController::class, 'bkhmTriage'])->name('bug.triage');
    });

    // WR3 Khusus: Validasi & Pengesahan Surat Peringatan Resmi
    Route::middleware(['role:wr3|admin', 'admin.readonly'])->prefix('wr3')->name('wr3.')->group(function () {
        Route::get('/surat-peringatan', [\App\Http\Controllers\Wr3\SuratPeringatanValidationController::class, 'index'])->name('sp.index');
        Route::get('/surat-peringatan/{sp}', [\App\Http\Controllers\Wr3\SuratPeringatanValidationController::class, 'show'])->name('sp.show');
        Route::post('/surat-peringatan/{sp}/approve', [\App\Http\Controllers\Wr3\SuratPeringatanValidationController::class, 'approve'])->name('sp.approve');
        Route::post('/surat-peringatan/{sp}/reject', [\App\Http\Controllers\Wr3\SuratPeringatanValidationController::class, 'reject'])->name('sp.reject');
    });

    // Admin/BKHM Role: User management remains available to both roles.
    Route::middleware(['role:bkhm|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/saldo', [UserController::class, 'updateSaldo'])->name('users.saldo');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['role:bkhm|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/konfigurasi', [KonfigurasiController::class, 'edit'])->name('konfigurasi.edit');
        Route::put('/konfigurasi', [KonfigurasiController::class, 'update'])->name('konfigurasi.update');

        // Panel IT / Admin untuk Penanganan Bug Sistem dari BKHM
        Route::get('/laporan-bug', [\App\Http\Controllers\LaporanBugController::class, 'itIndex'])->name('bug.index');
        Route::post('/laporan-bug/{bug}/resolve', [\App\Http\Controllers\LaporanBugController::class, 'itResolve'])->name('bug.resolve');
    });

    // Program Kerja Routes
    Route::get('/proker', [ProgramKerjaController::class, 'index'])->name('proker.index');
    Route::middleware(['role:ormawa|bem'])->group(function () {
        Route::get('/proker/tambah', [ProgramKerjaController::class, 'create'])->name('proker.create');
        Route::post('/proker', [ProgramKerjaController::class, 'store'])->name('proker.store');
    });
    Route::middleware(['role:bpm|admin'])->group(function () {
        Route::put('/proker/{proker}', [ProgramKerjaController::class, 'update'])->name('proker.update');
    });

    // FR-020: Pelaporan Prestasi / Kompetisi (mahasiswa, ormawa, verifikator)
    Route::middleware(['role:mahasiswa|ormawa|bem|bpm|bkhm|wr3|admin'])->group(function () {
        Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
        Route::get('/prestasi/create', [PrestasiController::class, 'create'])->name('prestasi.create');
        Route::post('/prestasi', [PrestasiController::class, 'store'])->name('prestasi.store');
        Route::get('/prestasi/{prestasi}/bukti', [PrestasiController::class, 'bukti'])->name('prestasi.bukti');
    });
    Route::middleware(['role:bkhm|wr3|admin'])->group(function () {
        Route::patch('/prestasi/{prestasi}/verify', [PrestasiController::class, 'verify'])->name('prestasi.verify');
    });
});


// DEV ONLY: Auto-login / logout routes for pen.dev design tool
if (app()->environment('local')) {
    Route::get('/dev-login/{email}', function (string $email) {
        $user = \App\Models\User::where('email', $email)->firstOrFail();
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect('/dashboard');
    });

    Route::get('/dev-logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    });
}

require __DIR__.'/auth.php';
