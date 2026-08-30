<?php

use App\Http\Controllers\AspirasiController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Aspirasi Public Route
Route::get('/aspirasi/kirim', [AspirasiController::class, 'create'])->name('aspirasi.create');
Route::post('/aspirasi/kirim', [AspirasiController::class, 'store'])->name('aspirasi.store');

// Auth Routes (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // BPM Role: Kelola Aspirasi
    Route::middleware(['role:bpm'])->group(function () {
        Route::get('/aspirasi', [AspirasiController::class, 'index'])->name('aspirasi.index');
        Route::get('/aspirasi/{aspirasi}', [AspirasiController::class, 'show'])->name('aspirasi.show');
        Route::put('/aspirasi/{aspirasi}', [AspirasiController::class, 'update'])->name('aspirasi.update');
    });

    // Ormawa Role: Modul Pengajuan
    Route::middleware(['role:ormawa'])->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/ajukan', [PengajuanController::class, 'ajukan'])->name('pengajuan.ajukan');
    });

    // Verifikator Roles: Modul Verifikasi (BEM, BPM, BKH, WR3, Bendahara)
    Route::middleware(['role:bem|bpm|bkh|wr3|bendahara'])->group(function () {
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{pengajuan}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{pengajuan}/process', [VerifikasiController::class, 'process'])->name('verifikasi.process');
    });
});

require __DIR__.'/auth.php';
