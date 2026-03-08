<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\MeetingMinuteController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\AcademicWarningController;
use App\Http\Controllers\Admin\CourseCancellationController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AcademicResultController;
use App\Http\Controllers\Admin\TrainingPointController;
use App\Http\Controllers\Admin\SubjectController;

/*
|--------------------------------------------------------------------------
| Admin Routes (Auto prefixed with 'admin' and named 'admin.')
|--------------------------------------------------------------------------
| Tất cả route trong file này đã được tự động thêm:
| - prefix: '/admin/'
| - name: 'admin.'
| - middleware: ['web', 'auth', 'role:ADMIN,LECTURER'] (từ bootstrap/app.php)
*/

// --- DASHBOARD ---
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/system/check', [DashboardController::class, 'runSystemCheck'])->name('system.check');

// --- QUẢN LÝ LỚP HỌC (CLASSES) ---
Route::controller(ClassController::class)->prefix('classes')->name('classes.')->group(function () {
    Route::get('/{id}/export', 'exportStudents')->name('export');
    Route::post('/upload-preview', 'previewUpload')->name('upload.preview');
    Route::post('/send-emails', 'sendEmails')->name('send_emails');
});
Route::resource('classes', ClassController::class);

// --- QUẢN LÝ SINH VIÊN (STUDENTS) ---
Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
    Route::post('/bulk-delete', 'bulkDestroy')->name('bulk_destroy');
    Route::post('/bulk-restore', 'bulkRestore')->name('bulk_restore');
    Route::post('/{id}/restore', 'restore')->name('restore');
});
Route::resource('students', StudentController::class);

// --- QUẢN LÝ KẾT QUẢ HỌC TẬP (ACADEMIC RESULTS) ---
Route::get('academic-results/import', [AcademicResultController::class, 'import'])->name('academic_results.import');
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

// --- QUẢN LÝ GIẢNG VIÊN (LECTURERS) ---
Route::controller(LecturerController::class)->prefix('lecturers')->name('lecturers.')->group(function () {
    Route::post('/bulk-delete', 'bulkDelete')->name('bulk_delete');
    Route::post('/bulk-restore', 'bulkRestore')->name('bulk_restore');
    Route::post('/{id}/restore', 'restore')->name('restore');
});
Route::resource('lecturers', LecturerController::class);

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
Route::post('training-points/preview', [TrainingPointController::class, 'preview'])->name('training_points.preview');
Route::post('training-points/store-import', [TrainingPointController::class, 'storeImport'])->name('training_points.store_import');
Route::resource('training_points', TrainingPointController::class);

// --- HỦY HỌC PHẦN (COURSE CANCELLATIONS) ---
Route::get('course-cancellations/export', [CourseCancellationController::class, 'export'])->name('course_cancellations.export');
Route::get('course-cancellations/import', [CourseCancellationController::class, 'showImportForm'])->name('course_cancellations.import'); // Trang upload
Route::post('course-cancellations/preview', [CourseCancellationController::class, 'preview'])->name('course_cancellations.preview'); // Xử lý xem trước
Route::post('course-cancellations/store-import', [CourseCancellationController::class, 'storeImport'])->name('course_cancellations.store_import'); // Lưu chính thức
Route::get('course-cancellations', [CourseCancellationController::class, 'index'])->name('course_cancellations.index');
Route::delete('course-cancellations/{id}', [CourseCancellationController::class, 'destroy'])->name('course_cancellations.destroy');
// --- MÔN HỌC ---
Route::resource('subjects', SubjectController::class)->except(['create', 'show', 'edit']);

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

// --- CHUÔNG THÔNG BÁO HEADER ---
Route::post('/alerts/mark-read', [NotificationController::class, 'markRead'])->name('alerts.mark_read');
Route::post('/alerts/mark-read-all', [NotificationController::class, 'markReadAll'])->name('alerts.mark_read_all');
