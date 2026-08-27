<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SessionController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth.dummy'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', function () {
        return view('profile.index');
    })->name('profile');

    // Ganti role tanpa harus login ulang
    Route::get('ganti-role', [DashboardController::class, 'gantiRolePage'])->name('ganti-role.index');
    Route::post('ganti-role', [DashboardController::class, 'gantiRole'])->name('ganti-role');

    // Session management
    Route::get('session/check', [SessionController::class, 'check'])->name('session.check');
    Route::post('session/renew', [SessionController::class, 'renew'])->name('session.renew')->middleware('throttle:10,1');
    
    // Upload persyaratan - accessible by all authenticated roles (Mahasiswa, Admin, TU Prodi, etc.)
    Route::post('mahasiswa/upload-persyaratan', [MahasiswaController::class, 'uploadPersyaratan'])->name('mahasiswa.upload-persyaratan');
    Route::post('mahasiswa/update-kelengkapan', [MahasiswaController::class, 'updateKelengkapan'])->name('mahasiswa.update-kelengkapan');
    Route::post('mahasiswa/save-all-persyaratan', [MahasiswaController::class, 'saveAllPersyaratan'])->name('mahasiswa.save-all-persyaratan');

    // Routes untuk Mahasiswa (Progress Sidang untuk semua strata)
    Route::middleware(['role:Mahasiswa', 'ownership'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('set-judul/{idJudul}', [MahasiswaController::class, 'setActiveJudul'])->name('set-judul');
        Route::get('tahap/{tahapan}', [MahasiswaController::class, 'showTahap'])->name('tahap');
        Route::post('judul', [MahasiswaController::class, 'storeJudul'])->name('store-judul');
        Route::get('ubah-judul/{idJudul}', [MahasiswaController::class, 'ubahJudul'])->name('ubah-judul');
        Route::post('ajukan-prodi', [MahasiswaController::class, 'ajukanProdi'])->name('ajukan-prodi');
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/master.php';
require __DIR__ . '/sidang.php';
require __DIR__ . '/report.php';
require __DIR__ . '/notifications.php';
