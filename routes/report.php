<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::prefix('report')->name('report.')->middleware(['auth.dummy'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
});
