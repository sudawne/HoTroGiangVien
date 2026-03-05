<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('index');

// Xử lý Like & Comment
Route::post('/notifications/{id}/like', [HomeController::class, 'toggleLike'])->name('notifications.like');
Route::post('/notifications/{id}/comment', [HomeController::class, 'storeComment'])->name('notifications.comment');
Route::post('/student/alerts/mark-read', [HomeController::class, 'markRead'])->name('student.alerts.mark_read');
Route::post('/student/alerts/mark-read-all', [HomeController::class, 'markReadAll'])->name('student.alerts.mark_read_all');
