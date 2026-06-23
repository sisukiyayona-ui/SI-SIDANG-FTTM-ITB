<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\ProdiController;
use App\Http\Controllers\Master\PersyaratanController;
use App\Http\Controllers\Master\PenilaianController;
use App\Http\Controllers\Master\UserController;

Route::prefix('master')->name('master.')->middleware(['auth.dummy'])->group(function () {
    Route::resource('prodi', ProdiController::class)->except(['show']);
    Route::get('prodi/{id}/detail', [ProdiController::class, 'show'])->name('prodi.detail');

    Route::resource('persyaratan', PersyaratanController::class)->except(['show']);
    Route::resource('penilaian', PenilaianController::class)->except(['show']);

    // Admin - User Management (1.4)
    Route::resource('user', UserController::class)->except(['show', 'create', 'edit']);
});
