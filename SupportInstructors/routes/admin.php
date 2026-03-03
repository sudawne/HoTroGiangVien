<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\MeetingMinuteController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\AcademicWarningController;
use App\Http\Controllers\Admin\LecturerController;

/*
|--------------------------------------------------------------------------
| Admin Routes (Auto prefixed with 'admin' and named 'admin.')
|--------------------------------------------------------------------------
*/

// --- DASHBOARD ---
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// --- QUẢN LÝ LỚP HỌC (CLASSES) ---
Route::controller(ClassController::class)->prefix('classes')->name('classes.')->group(function () {
    Route::get('/{id}/export', 'exportStudents')->name('export');
    Route::post('/upload-preview', 'previewUpload')->name('upload.preview');
    Route::post('/send-emails', 'sendEmails')->name('send_emails');
});
Route::resource('classes', ClassController::class);

// --- QUẢN LÝ SINH VIÊN (STUDENTS) ---
Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
    // Các route thao tác hàng loạt và khôi phục phải nằm TRƯỚC Route::resource
    Route::post('/bulk-delete', 'bulkDestroy')->name('bulk_destroy');
    Route::post('/bulk-restore', 'bulkRestore')->name('bulk_restore'); // Sửa lỗi RouteNotFound
    Route::post('/{id}/restore', 'restore')->name('restore');
});
Route::resource('students', StudentController::class);

// --- QUẢN LÝ GIẢNG VIÊN (LECTURERS) ---
Route::controller(LecturerController::class)->prefix('lecturers')->name('lecturers.')->group(function () {
    Route::post('/bulk-delete', 'bulkDelete')->name('bulk_delete');
    Route::post('/bulk-restore', 'bulkRestore')->name('bulk_restore');
    Route::post('/{id}/restore', 'restore')->name('restore');
});
Route::resource('lecturers', LecturerController::class);

// --- BIÊN BẢN HỌP (MEETING MINUTES) ---
Route::resource('minutes', MeetingMinuteController::class);

// --- CẢNH BÁO HỌC VỤ (ACADEMIC WARNINGS) ---
Route::controller(AcademicWarningController::class)->prefix('academic-warnings')->name('academic_warnings.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/import', 'showImport')->name('import');
    Route::post('/preview', 'preview')->name('preview');
    Route::post('/store', 'store')->name('store');
    Route::post('/quick-add-student', 'quickAddStudent')->name('quick_add_student');
});

// --- HỆ THỐNG IMPORT DỮ LIỆU ---
Route::controller(ImportController::class)->prefix('imports')->name('imports.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::post('/{id}/publish', 'publish')->name('publish');

    // Import Sinh viên (Modal)
    Route::post('/students', 'storeStudent')->name('storeStudent');

    // Import Sinh viên theo Lớp
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/{id}/import', 'showImportClass')->name('import');
        Route::post('/preview', 'previewImport')->name('preview');
        Route::post('/store', 'storeImport')->name('store');
    });
});
