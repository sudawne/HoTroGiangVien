<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\MeetingMinuteController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\AcademicWarningController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('classes', ClassController::class);
Route::resource('students', StudentController::class);
Route::resource('minutes', MeetingMinuteController::class);

Route::get('/academic-warnings', [AcademicWarningController::class, 'index'])->name('academic_warnings.index');

Route::controller(ImportController::class)->group(function () {
    // Import chung
    Route::get('/imports', 'index')->name('imports.index');
    Route::post('/imports', 'store')->name('imports.store');
    Route::post('/imports/{id}/publish', 'publish')->name('imports.publish');

    // Import Sinh viên (Modal cũ - nếu còn dùng)
    Route::post('/imports/students', 'storeStudent')->name('imports.storeStudent');

    // Import Sinh viên theo Lớp (Quy trình mới: Preview -> Store)
    Route::get('/classes/{id}/import', 'showImportClass')->name('classes.import');
    Route::post('/classes/import/preview', 'previewImport')->name('classes.import.preview');
    Route::post('/classes/import/store', 'storeImport')->name('classes.import.store');
});
