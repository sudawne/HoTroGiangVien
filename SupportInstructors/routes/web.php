<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MeetingMinuteController;
use App\Http\Controllers\Admin\TrainingPointController;
use App\Http\Controllers\Admin\AcademicResultController;
use App\Http\Controllers\ChatController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

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

    Route::get('training-points/import', [TrainingPointController::class, 'import'])->name('training_points.import');
    Route::post('training-points/preview', [TrainingPointController::class, 'preview'])->name('training_points.preview');
    Route::post('training-points/store-import', [TrainingPointController::class, 'storeImport'])->name('training_points.store_import');
    Route::resource('training_points', TrainingPointController::class);

    Route::get('academic-results/import', [AcademicResultController::class, 'import'])->name('academic_results.import');
    Route::post('academic-results/preview', [AcademicResultController::class, 'preview'])->name('academic_results.preview');
    Route::post('academic-results/store-import', [AcademicResultController::class, 'storeImport'])->name('academic_results.store_import');
    Route::resource('academic_results', AcademicResultController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/chat/contacts', [ChatController::class, 'getContacts']);
    Route::get('/chat/messages/{userId}', [ChatController::class, 'getMessages']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::post('/chat/recall/{messageId}', [ChatController::class, 'recallMessage']);
    Route::post('/chat/delete-for-me/{messageId}', [ChatController::class, 'deleteForMe']);
});
