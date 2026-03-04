<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MeetingMinuteController;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/system/check', [DashboardController::class, 'runSystemCheck'])->name('system.check');
});
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::resource('minutes', MeetingMinuteController::class);
    Route::put('minutes/{id}/approve', [MeetingMinuteController::class, 'approve'])->name('minutes.approve');
    Route::put('minutes/{id}/reject', [MeetingMinuteController::class, 'reject'])->name('minutes.reject');
    Route::get('minutes/{id}/export-word', [MeetingMinuteController::class, 'exportWord'])->name('minutes.export_word');
});