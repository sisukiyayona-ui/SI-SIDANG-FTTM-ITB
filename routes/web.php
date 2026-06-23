<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApproveUserController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth.dummy'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('approve-user', [ApproveUserController::class, 'index'])->name('approve.user');
    Route::post('approve-user/{id}/approve', [ApproveUserController::class, 'approve'])->name('approve.user.approve');
    Route::post('approve-user/{id}/reject', [ApproveUserController::class, 'reject'])->name('approve.user.reject');

    Route::get('profile', function () {
        return view('profile.index');
    })->name('profile');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/master.php';
require __DIR__ . '/sidang.php';
require __DIR__ . '/report.php';
