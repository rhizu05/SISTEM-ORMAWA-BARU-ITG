<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramKerjaController;

// Proker Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/proker', [ProgramKerjaController::class, 'index'])->name('proker.index');
    Route::get('/proker/tambah', [ProgramKerjaController::class, 'create'])->name('proker.create');
    Route::post('/proker', [ProgramKerjaController::class, 'store'])->name('proker.store');
    Route::put('/proker/{proker}', [ProgramKerjaController::class, 'update'])->name('proker.update');
});
