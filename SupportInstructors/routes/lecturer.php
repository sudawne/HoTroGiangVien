<?php

use Illuminate\Support\Facades\Route;

// Kéo chung Controller của thư mục Admin ra dùng cho Lecturer
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\MeetingMinuteController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\AcademicWarningController;
use App\Http\Controllers\Admin\CourseCancellationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AcademicResultController;
use App\Http\Controllers\Admin\TrainingPointController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\ProfileController;

/*
|--------------------------------------------------------------------------
| Lecturer Routes (Auto prefixed with 'lecturer' and named 'lecturer.')
|--------------------------------------------------------------------------
*/

// --- DASHBOARD ---
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/system/check', [DashboardController::class, 'runSystemCheck'])->name('system.check');

// --- QUẢN LÝ LỚP HỌC (CLASSES) ---
// Đã loại bỏ quyền thêm mới (create, store) và xóa (destroy)
Route::controller(ClassController::class)->prefix('classes')->name('classes.')->group(function () {
    Route::get('/{id}/export', 'exportStudents')->name('export');
    Route::post('/upload-preview', 'previewUpload')->name('upload.preview');
    Route::post('/send-emails', 'sendEmails')->name('send_emails');
});
Route::resource('classes', ClassController::class)->except(['create', 'store', 'destroy']);

// --- QUẢN LÝ SINH VIÊN (STUDENTS) ---
// Đã loại bỏ quyền thêm mới, ẩn, khôi phục sinh viên
Route::resource('students', StudentController::class)->except(['create', 'store', 'destroy']);

// --- QUẢN LÝ KẾT QUẢ HỌC TẬP (ACADEMIC RESULTS) ---
Route::get('academic-results/import', [AcademicResultController::class, 'import'])->name('academic_results.import');
Route::get('academic-results/export', [AcademicResultController::class, 'export'])->name('academic_results.export');
Route::post('academic-results/preview', [AcademicResultController::class, 'preview'])->name('academic_results.preview');
Route::post('academic-results/store-import', [AcademicResultController::class, 'storeImport'])->name('academic_results.store_import');
Route::resource('academic-results', AcademicResultController::class);

// --- THÔNG BÁO & TIN TỨC ---
Route::controller(NotificationController::class)->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/{id}/approve', 'approve')->name('approve');
    Route::post('/{id}/like', 'toggleLike')->name('like');
    Route::post('/{id}/comment', 'storeComment')->name('comment');
});
Route::resource('notifications', NotificationController::class);

// --- BIÊN BẢN HỌP (MEETING MINUTES) ---
Route::put('minutes/{id}/approve', [MeetingMinuteController::class, 'approve'])->name('minutes.approve');
Route::put('minutes/{id}/reject', [MeetingMinuteController::class, 'reject'])->name('minutes.reject');
Route::get('minutes/{id}/export-word', [MeetingMinuteController::class, 'exportWord'])->name('minutes.export_word');
Route::get('minutes/{id}/export-pdf', [MeetingMinuteController::class, 'exportPdf'])->name('minutes.export_pdf');
Route::resource('minutes', MeetingMinuteController::class);

// --- CẢNH BÁO HỌC VỤ (ACADEMIC WARNINGS) ---
Route::controller(AcademicWarningController::class)->prefix('academic-warnings')->name('academic_warnings.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/import', 'showImport')->name('import');
    Route::post('/preview', 'preview')->name('preview');
    Route::post('/store', 'store')->name('store');
    Route::post('/quick-add-student', 'quickAddStudent')->name('quick_add_student');
});
Route::get('academic-warnings/export', [AcademicWarningController::class, 'export'])->name('academic_warnings.export');
Route::resource('academic_warnings', AcademicWarningController::class);

// --- ĐIỂM RÈN LUYỆN (TRAINING POINTS) ---
Route::get('training-points/import', [TrainingPointController::class, 'import'])->name('training_points.import');
Route::get('training-points/export', [TrainingPointController::class, 'export'])->name('training_points.export');
Route::post('training-points/preview', [TrainingPointController::class, 'preview'])->name('training_points.preview');
Route::post('training-points/store-import', [TrainingPointController::class, 'storeImport'])->name('training_points.store_import');
Route::resource('training_points', TrainingPointController::class);

// --- HỦY HỌC PHẦN (COURSE CANCELLATIONS) ---
Route::get('course-cancellations/export', [CourseCancellationController::class, 'export'])->name('course_cancellations.export');
Route::get('course-cancellations/import', [CourseCancellationController::class, 'showImportForm'])->name('course_cancellations.import');
Route::post('course-cancellations/preview', [CourseCancellationController::class, 'preview'])->name('course_cancellations.preview');
Route::post('course-cancellations/store-import', [CourseCancellationController::class, 'storeImport'])->name('course_cancellations.store_import');
Route::get('course-cancellations', [CourseCancellationController::class, 'index'])->name('course_cancellations.index');
Route::delete('course-cancellations/{id}', [CourseCancellationController::class, 'destroy'])->name('course_cancellations.destroy');

// --- HỆ THỐNG IMPORT DỮ LIỆU CHUNG ---
Route::controller(ImportController::class)->prefix('imports')->name('imports.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::post('/{id}/publish', 'publish')->name('publish');
    Route::post('/students', 'storeStudent')->name('storeStudent');
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/{id}/import', 'showImportClass')->name('import');
        Route::post('/preview', 'previewImport')->name('preview');
        Route::post('/store', 'storeImport')->name('store');
    });
});

// --- TÍNH NĂNG KHÁC ---
Route::post('/alerts/mark-read', [NotificationController::class, 'markRead'])->name('alerts.mark_read');
Route::post('/alerts/mark-read-all', [NotificationController::class, 'markReadAll'])->name('alerts.mark_read_all');
Route::get('/global-search', [SearchController::class, 'globalSearch'])->name('global.search');

Route::get('/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change_password');
Route::post('/send-otp', [ProfileController::class, 'sendOtp'])->name('profile.send_otp');
Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');
