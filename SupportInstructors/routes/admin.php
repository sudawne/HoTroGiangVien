<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\MeetingMinuteController;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('classes', ClassController::class);

Route::resource('students', StudentController::class);

Route::resource('minutes', MeetingMinuteController::class);
