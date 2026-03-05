<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\DashboardController;

// Trang chủ bảng tin (Newsfeed) -> Tên route thực tế: student.dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Xử lý Like & Comment -> Tên route thực tế: student.notifications.like / .comment
Route::post('/notifications/{id}/like', [DashboardController::class, 'toggleLike'])->name('notifications.like');
Route::post('/notifications/{id}/comment', [DashboardController::class, 'storeComment'])->name('notifications.comment');
