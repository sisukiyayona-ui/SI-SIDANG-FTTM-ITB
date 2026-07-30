<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

// Report untuk Admin dan TU Prodi (2.4 Report)
Route::prefix('report')->name('report.')->middleware(['auth.dummy', 'role:Admin,TU Prodi,FS,Monev'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('detail/{idJudul}/{tahapan}', [ReportController::class, 'showDetail'])->name('detail');
});
