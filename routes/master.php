<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\PersyaratanController;
use App\Http\Controllers\Master\PenilaianController;
use App\Http\Controllers\Master\UserController;

Route::prefix('master')->name('master.')->middleware(['auth.dummy'])->group(function () {
    // Admin only - Prodi Management (1.3)
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('prodi', ProdiController::class)->except(['show']);
        Route::get('prodi/{id}/detail', [ProdiController::class, 'show'])->name('prodi.detail');
    });

    // Admin & TU Prodi - Persyaratan & Penilaian (1.1, 1.2, 2.1a, 2.1b)
    Route::middleware(['role:Admin,TU Prodi'])->group(function () {
        Route::resource('persyaratan', PersyaratanController::class)->except(['show']);
        Route::resource('penilaian', PenilaianController::class)->except(['show']);
    });

    // Admin & TU Prodi - User Management (1.4)
    Route::middleware(['role:Admin,TU Prodi'])->group(function () {
        Route::resource('user', UserController::class)->except(['show', 'create']);
    });
});
