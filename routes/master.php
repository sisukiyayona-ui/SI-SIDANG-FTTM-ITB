<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\FakultasController;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\PersyaratanController;
use App\Http\Controllers\Master\PenilaianController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\KppsController;

Route::prefix('master')->name('master.')->middleware(['auth.dummy'])->group(function () {
    // Admin only - Prodi Management (1.3)
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('fakultas/template', [FakultasController::class, 'template'])->name('fakultas.template');
        Route::post('fakultas/import', [FakultasController::class, 'import'])->name('fakultas.import');
        Route::post('fakultas/sync-spsi', [FakultasController::class, 'syncSpsi'])->name('fakultas.sync-spsi');
        Route::resource('fakultas', FakultasController::class)->except(['show']);
        Route::get('fakultas/{id}/detail', [FakultasController::class, 'show'])->name('fakultas.detail');

        Route::get('prodi/template', [ProdiController::class, 'template'])->name('prodi.template');
        Route::post('prodi/import', [ProdiController::class, 'import'])->name('prodi.import');
        Route::post('prodi/sync-spsi', [ProdiController::class, 'syncSpsi'])->name('prodi.sync-spsi');
        Route::resource('prodi', ProdiController::class)->except(['show']);
        Route::get('prodi/{id}/detail', [ProdiController::class, 'show'])->name('prodi.detail');
    });

    // Admin & TU Prodi - Persyaratan & Penilaian (1.1, 1.2, 2.1a, 2.1b)
    Route::middleware(['role:Admin,TU Prodi'])->group(function () {
        Route::get('persyaratan/template', [PersyaratanController::class, 'template'])->name('persyaratan.template');
        Route::post('persyaratan/import', [PersyaratanController::class, 'import'])->name('persyaratan.import');
        Route::resource('persyaratan', PersyaratanController::class)->except(['show']);

        Route::get('penilaian/template', [PenilaianController::class, 'template'])->name('penilaian.template');
        Route::post('penilaian/import', [PenilaianController::class, 'import'])->name('penilaian.import');
        Route::resource('penilaian', PenilaianController::class)->except(['show']);
    });

    // Admin & TU Prodi - KPPS Management
    Route::middleware(['role:Admin,TU Prodi'])->group(function () {
        Route::resource('kpps', KppsController::class)->except(['show']);
    });

    // Admin & TU Prodi - User Management (1.4)
    Route::middleware(['role:Admin,TU Prodi'])->group(function () {
        Route::resource('user', UserController::class)->except(['show', 'create']);
    });
});
