<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('index');

// Xử lý Like & Comment
Route::post('/notifications/{id}/like', [HomeController::class, 'toggleLike'])->name('notifications.like');
Route::post('/notifications/{id}/comment', [HomeController::class, 'storeComment'])->name('notifications.comment');
// Route API đánh dấu đã đọc thông báo
Route::post('/alerts/mark-read', [HomeController::class, 'markRead'])->name('alerts.mark_read');
Route::post('/alerts/mark-read-all', [HomeController::class, 'markReadAll'])->name('alerts.mark_read_all');
Route::get('/search-api', [HomeController::class, 'searchApi']);
