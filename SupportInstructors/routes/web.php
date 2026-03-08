<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\ForgotPasswordController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('auth.forgot_password');
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('auth.forgot_password.send_otp');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('auth.forgot_password.reset');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/chat/contacts', [ChatController::class, 'getContacts']);
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/recall/{messageId}', [ChatController::class, 'recallMessage']);
    Route::post('/chat/delete-for-me/{messageId}', [ChatController::class, 'deleteForMe']);
});

Route::post('/ai/ask', [AIController::class, 'askAI'])
    ->middleware(['web', 'auth', 'role:ADMIN,LECTURER'])
    ->name('ai.ask');