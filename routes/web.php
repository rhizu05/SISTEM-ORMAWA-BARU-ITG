<?php

use App\Http\Controllers\Admin\KonfigurasiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProposalGeneratorController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\Sarpras\MasterBarangController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileDataController;
use Illuminate\Support\Facades\Route;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Aspirasi Public Route
Route::get('/aspirasi/kirim', [AspirasiController::class, 'create'])->name('aspirasi.create');
Route::post('/aspirasi/kirim', [AspirasiController::class, 'store'])->name('aspirasi.store');

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

    // Pusat Informasi & Regulasi
    Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi.index');
    Route::post('/informasi/pengumuman', [InformasiController::class, 'storePengumuman'])->name('informasi.pengumuman.store');
    Route::delete('/informasi/pengumuman/{pengumuman}', [InformasiController::class, 'destroyPengumuman'])->name('informasi.pengumuman.destroy');
    Route::post('/informasi/regulasi', [InformasiController::class, 'storeRegulasi'])->name('informasi.regulasi.store');
    Route::delete('/informasi/regulasi/{regulasi}', [InformasiController::class, 'destroyRegulasi'])->name('informasi.regulasi.destroy');

    // Jadwal Rapat
    Route::get('/rapat', [RapatController::class, 'index'])->name('rapat.index');
    Route::post('/rapat', [RapatController::class, 'store'])->name('rapat.store');
    Route::delete('/rapat/{rapat}', [RapatController::class, 'destroy'])->name('rapat.destroy');
    
    // BPM Role: Kelola Aspirasi
    Route::middleware(['role:bpm'])->group(function () {
        Route::get('/aspirasi', [AspirasiController::class, 'index'])->name('aspirasi.index');
        Route::get('/aspirasi/{aspirasi}', [AspirasiController::class, 'show'])->name('aspirasi.show');
        Route::put('/aspirasi/{aspirasi}', [AspirasiController::class, 'update'])->name('aspirasi.update');
    });

    // Ormawa, BEM, BPM: Modul Pengajuan, LPJ, Peminjaman, Generator
    Route::middleware(['role:ormawa|bem|bpm'])->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::get('/pengajuan/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/{pengajuan}', [PengajuanController::class, 'update'])->name('pengajuan.update');
        Route::post('/pengajuan/{pengajuan}/ajukan', [PengajuanController::class, 'ajukan'])->name('pengajuan.ajukan');
        
        Route::get('/lpj', [LpjController::class, 'index'])->name('lpj.index');
        Route::get('/lpj/create/{pengajuan}', [LpjController::class, 'create'])->name('lpj.create');
        Route::post('/lpj/{pengajuan}', [LpjController::class, 'store'])->name('lpj.store');

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        // Opsi A: Riwayat terpisah per jenis - hanya milik sendiri
        Route::get('/peminjaman/tempat', [PeminjamanController::class, 'historyTempat'])->name('peminjaman.tempat.index');
        Route::get('/peminjaman/barang', [PeminjamanController::class, 'historyBarang'])->name('peminjaman.barang.index');
        Route::get('/peminjaman/tempat/create', [PeminjamanController::class, 'createTempat'])->name('peminjaman.tempat.create');
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

        // LPJ Generator
        Route::get('/generator/lpj/create/{proposal?}', [ProposalGeneratorController::class, 'createLpj'])->name('generator.lpj.create');
        Route::post('/generator/lpj', [ProposalGeneratorController::class, 'storeLpj'])->name('generator.lpj.store');
        Route::get('/generator/lpj/{lpj}', [ProposalGeneratorController::class, 'showLpj'])->name('generator.lpj.show');

        // Digital Archive
        Route::get('/archive', [ProposalGeneratorController::class, 'archive'])->name('archive.index');

    });

    // Rute cetak dokumen bisa diakses ormawa & verifikator
    Route::get('/generator/{proposal}/print', [ProposalGeneratorController::class, 'print'])
        ->middleware(['auth'])
        ->name('generator.print');

    // Verifikator Roles: Modul Verifikasi (BEM, BPM, BKH, WR3, Bendahara)
    Route::middleware(['role:bem|bpm|bkh|wr3|bendahara'])->group(function () {
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{pengajuan}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{pengajuan}/process', [VerifikasiController::class, 'process'])->name('verifikasi.process');
    });

    // Peminjaman Verifikasi (BKKH & Sarpras)
    Route::middleware(['role:bkh|sarpras|sarpras_barang'])->group(function () {
        Route::get('/verifikasi-peminjaman', [PeminjamanController::class, 'antrian'])->name('peminjaman.verifikasi.index');
        Route::post('/verifikasi-peminjaman/tempat/{peminjaman}', [PeminjamanController::class, 'prosesTempat'])->name('peminjaman.tempat.proses');
        Route::post('/verifikasi-peminjaman/barang/{peminjaman}', [PeminjamanController::class, 'prosesBarang'])->name('peminjaman.barang.proses');
    });

    // Sarpras Khusus: Manajemen Master Barang Inventaris
    Route::middleware(['role:sarpras_barang'])->prefix('sarpras')->name('sarpras.')->group(function () {
        Route::get('/barang', [MasterBarangController::class, 'index'])->name('barang.index');
        Route::post('/barang', [MasterBarangController::class, 'store'])->name('barang.store');
        Route::put('/barang/{barang}', [MasterBarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [MasterBarangController::class, 'destroy'])->name('barang.destroy');
    });

    // Bendahara Khusus: Proses Pencairan
    Route::middleware(['role:bendahara'])->group(function () {
        Route::post('/bendahara/proses/{pengajuan}', [BendaharaController::class, 'proses'])->name('bendahara.proses');
    });

    // BKKH Khusus: 8 menu sesuai spec
    Route::middleware(['role:bkh'])->prefix('bkkh')->name('bkkh.')->group(function () {
        Route::get('/saldo', [\App\Http\Controllers\Bkkh\BkkhController::class, 'saldo'])->name('saldo.index');
        Route::get('/arsip-surat', [\App\Http\Controllers\Bkkh\BkkhController::class, 'arsipSurat'])->name('arsip.index');
        Route::get('/surat-peringatan/create', [\App\Http\Controllers\Bkkh\BkkhController::class, 'spCreate'])->name('sp.create');
        Route::post('/surat-peringatan', [\App\Http\Controllers\Bkkh\BkkhController::class, 'spStore'])->name('sp.store');
        Route::get('/surat-peringatan/{sp}', [\App\Http\Controllers\Bkkh\BkkhController::class, 'spShow'])->name('sp.show');
        Route::get('/verifikasi-tempat', [\App\Http\Controllers\Bkkh\BkkhController::class, 'verifikasiTempat'])->name('verifikasi-tempat.index');
    });

    // Admin/BKKH Role: Admin Panel (legacy)
    Route::middleware(['role:bkh|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/saldo', [UserController::class, 'updateSaldo'])->name('users.saldo');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        Route::get('/konfigurasi', [KonfigurasiController::class, 'edit'])->name('konfigurasi.edit');
        Route::put('/konfigurasi', [KonfigurasiController::class, 'update'])->name('konfigurasi.update');
    });
});

require __DIR__.'/auth.php';
