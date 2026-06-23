<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sidang\UjianKualifikasiController;
use App\Http\Controllers\Sidang\SidangProposalController;
use App\Http\Controllers\Sidang\SeminarKemajuanIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIIIController;
use App\Http\Controllers\Sidang\SeminarKemajuanIVController;
use App\Http\Controllers\Sidang\SidangAkhirController;

Route::prefix('sidang')->name('sidang.')->middleware(['auth.dummy'])->group(function () {
    Route::get('ujian-kualifikasi', [UjianKualifikasiController::class, 'index'])->name('ujian-kualifikasi');
    Route::get('sidang-proposal', [SidangProposalController::class, 'index'])->name('sidang-proposal');
    Route::get('seminar-kemajuan-i', [SeminarKemajuanIController::class, 'index'])->name('seminar-kemajuan-i');
    Route::get('seminar-kemajuan-ii', [SeminarKemajuanIIController::class, 'index'])->name('seminar-kemajuan-ii');
    Route::get('seminar-kemajuan-iii', [SeminarKemajuanIIIController::class, 'index'])->name('seminar-kemajuan-iii');
    Route::get('seminar-kemajuan-iv', [SeminarKemajuanIVController::class, 'index'])->name('seminar-kemajuan-iv');
    Route::get('sidang-akhir', [SidangAkhirController::class, 'index'])->name('sidang-akhir');
});
