<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sidang\UjianKualifikasiController;
use App\Http\Controllers\Sidang\SidangProposalController;
use App\Http\Controllers\Sidang\SeminarKemajuanIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIIIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIVController;
use App\Http\Controllers\Sidang\SidangAkhirController;
use App\Http\Controllers\Sidang\SidangS1Controller;
use App\Http\Controllers\Sidang\SidangS2Controller;
use App\Http\Controllers\Sidang\SidangS3Controller;
use App\Http\Controllers\Sidang\CetakController;
use App\Http\Controllers\Sidang\ApproveAjuanSidangController;

Route::prefix('sidang')->name('sidang.')->middleware(['auth.dummy'])->group(function () {
    // Existing routes (general access)
    Route::get('ujian-kualifikasi', [UjianKualifikasiController::class, 'index'])->name('ujian-kualifikasi');
    Route::get('sidang-proposal', [SidangProposalController::class, 'index'])->name('sidang-proposal');
    Route::get('seminar-kemajuan-i', [SeminarKemajuanIController::class, 'index'])->name('seminar-kemajuan-i');
    Route::get('seminar-kemajuan-ii', [SeminarKemajuanIIController::class, 'index'])->name('seminar-kemajuan-ii');
    Route::get('seminar-kemajuan-iii', [SeminarKemajuanIIIController::class, 'index'])->name('seminar-kemajuan-iii');
    Route::get('seminar-kemajuan-iv', [SeminarKemajuanIVController::class, 'index'])->name('seminar-kemajuan-iv');
    Route::get('sidang-akhir', [SidangAkhirController::class, 'index'])->name('sidang-akhir');
    
    // Route baru untuk S1, S2, S3 - TU Prodi, Admin, Pembimbing, Penguji, FS (2.3 Menu Sidang)
    Route::middleware(['role:Admin,TU Prodi,Pembimbing,Penguji,FS'])->group(function () {
        Route::get('tahap/{tahapan}', [\App\Http\Controllers\MahasiswaController::class, 'showTahap'])->name('tahap');
        Route::post('penilaian', [\App\Http\Controllers\Sidang\PenilaianController::class, 'store'])->name('penilaian.store');
        Route::put('penilaian/{id}', [\App\Http\Controllers\Sidang\PenilaianController::class, 'update'])->name('penilaian.update');
        Route::put('status-lulus/{id}', [\App\Http\Controllers\Sidang\PenilaianController::class, 'updateStatusLulus'])->name('penilaian.update-status-lulus');
        Route::post('lock-nilai/{id}', [\App\Http\Controllers\Sidang\PenilaianController::class, 'lockNilai'])->name('penilaian.lock-nilai');
        Route::post('tim-sidang', [\App\Http\Controllers\MahasiswaController::class, 'storeTimSidang'])->name('tim-sidang.store');
        Route::post('sk', [\App\Http\Controllers\MahasiswaController::class, 'storeSk'])->name('sk.store');
        Route::get('sk/next/{tahapan}', [\App\Http\Controllers\MahasiswaController::class, 'getNextSkNumber'])->name('sk.next');
        Route::put('tim-sidang/{id}', [\App\Http\Controllers\MahasiswaController::class, 'updateTimSidang'])->name('tim-sidang.update');
        Route::delete('tim-sidang/{id}', [\App\Http\Controllers\MahasiswaController::class, 'deleteTimSidang'])->name('tim-sidang.delete');
        Route::delete('jadwal/{id}', [\App\Http\Controllers\MahasiswaController::class, 'deleteJadwal'])->name('jadwal-sidang.delete');
        
        Route::get('s1', [SidangS1Controller::class, 'index'])->name('s1');
        Route::get('s1/{id}', [SidangS1Controller::class, 'show'])->name('s1.show');
        Route::put('s1/{id}', [SidangS1Controller::class, 'update'])->name('s1.update');
        
        Route::get('s2', [SidangS2Controller::class, 'index'])->name('s2');
        Route::get('s2/{id}', [SidangS2Controller::class, 'show'])->name('s2.show');
        Route::put('s2/{id}', [SidangS2Controller::class, 'update'])->name('s2.update');
        
        Route::get('cetak-form/{idJudul}/{tahapan}', [CetakController::class, 'cetakForm'])->name('cetak-form');
        Route::get('surat-kesediaan/{idJudul}/{tahapan}', [CetakController::class, 'suratKesediaanPenelaah'])->name('surat-kesediaan');
        Route::get('cetak-undangan/{idJudul}/{tahapan}', [CetakController::class, 'cetakUndangan'])->name('cetak-undangan');
        Route::get('cetak-berita-acara/{idJudul}/{tahapan}', [CetakController::class, 'cetakBeritaAcara'])->name('cetak-berita-acara');

        Route::get('s3', [SidangS3Controller::class, 'index'])->name('s3');
        Route::get('s3/{id}', [SidangS3Controller::class, 'show'])->name('s3.show');
        Route::put('s3/{id}', [SidangS3Controller::class, 'update'])->name('s3.update');
        Route::get('s3/{idJudul}/ubah-judul', [SidangS3Controller::class, 'ubahJudul'])->name('s3.ubah-judul');
        Route::post('s3/{idJudul}/ubah-judul', [SidangS3Controller::class, 'storeUbahJudul'])->name('s3.store-ubah-judul');
        Route::post('s3/judul', [SidangS3Controller::class, 'storeJudul'])->name('s3.store-judul');
    });

    // Jadwal Sidang — accessible by all authenticated roles including Mahasiswa
    Route::get('jadwal-sidang', [\App\Http\Controllers\Sidang\JadwalSidangController::class, 'index'])->name('jadwal-sidang');
    Route::post('jadwal-sidang', [\App\Http\Controllers\MahasiswaController::class, 'storeJadwal'])->name('jadwal-sidang.store');

    // Approve Ajuan Sidang — khusus role KPPS
    Route::middleware(['role:KPPS'])->group(function () {
        Route::get('approve-ajuan-sidang/{strata}', [ApproveAjuanSidangController::class, 'index'])->name('approve-ajuan.index');
        Route::get('approve-ajuan-sidang/{strata}/{id}', [ApproveAjuanSidangController::class, 'show'])->name('approve-ajuan.show');
        Route::post('approve-ajuan-sidang', [ApproveAjuanSidangController::class, 'store'])->name('approve-ajuan.store');
    });
});
