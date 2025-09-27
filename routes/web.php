<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\ClassController;

Route::get('/', function () {
    return view('landing-page');
});



Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');

    // Teacher routes
    Route::get('/users/teacher/create', [AdminUserController::class, 'createTeacher'])->name('admin.users.teacher.create');
    Route::post('/users/teacher', [AdminUserController::class, 'storeTeacher'])->name('admin.users.teacher.store');
    Route::get('/users/teacher/import', [AdminUserController::class, 'importTeachersPage'])->name('admin.users.teacher.import.page');
    Route::post('/users/import-teachers', [AdminUserController::class, 'importTeachers'])->name('admin.users.import.teachers');

    // Student routes
    Route::get('/users/student/create', [AdminUserController::class, 'createStudent'])->name('admin.users.student.create');
    Route::post('/users/student', [AdminUserController::class, 'storeStudent'])->name('admin.users.student.store');
    Route::get('/users/student/import', [AdminUserController::class, 'importStudentsPage'])->name('admin.users.student.import.page');
    Route::post('/users/import-students', [AdminUserController::class, 'importStudents'])->name('admin.users.import.students');

    // Class Management¡
    Route::get('/classes', [ClassController::class, 'index'])->name('admin.classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('admin.classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('admin.classes.store');
    Route::get('/classes/{id}/edit', [ClassController::class, 'edit'])->name('admin.classes.edit');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('admin.classes.update');
    Route::delete('/classes/{id}', [ClassController::class, 'destroy'])->name('admin.classes.destroy');
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', function () {
        return view('guru.dashboard');
    })->name('guru.dashboard');

    Route::get('/attendances', [AttendanceController::class, 'teacherAttendancesPage'])
        ->name('guru.attendances');

    Route::get('/attendances/{id}', [AttendanceController::class, 'teacherStudentDetail'])
        ->name('guru.detail.attendances');
});

Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', function () {
        return view('siswa.dashboard');
    })->name('siswa.dashboard');

    Route::get('/attendances', [AttendanceController::class, 'studentAttendancesPage'])
        ->name('siswa.attendances');
});

Route::middleware(['auth', 'role:satpam'])->prefix('satpam')->group(function () {
    Route::get('/dashboard', function () {
        return view('satpam.dashboard');
    })->name('satpam.dashboard');


    Route::get('/scan', [AttendanceController::class, 'scanPage'])->name('satpam.attendance.scan.page');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('satpam.attendance.scan');
});

require __DIR__ . '/auth.php';
