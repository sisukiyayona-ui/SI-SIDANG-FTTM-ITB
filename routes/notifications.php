<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth.dummy'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});
