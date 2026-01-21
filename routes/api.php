<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\SecretaryController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ParentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth.api')->group(function () {

    // Auth routes
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);

    // Admin routes
    Route::prefix('admin')->middleware('api.role:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Students
        Route::get('/students', [AdminController::class, 'students']);
        Route::get('/students/{id}', [AdminController::class, 'showStudent']);
        Route::post('/students', [AdminController::class, 'storeStudent']);
        Route::put('/students/{id}', [AdminController::class, 'updateStudent']);
        Route::delete('/students/{id}', [AdminController::class, 'deleteStudent']);

        // Classes
        Route::get('/classes', [AdminController::class, 'classes']);

        // Users
        Route::get('/teachers', [AdminController::class, 'teachers']);
        Route::get('/parents', [AdminController::class, 'parents']);
        Route::post('/parents', [AdminController::class, 'storeParent']);

        // Configuration
        Route::get('/school-years', [AdminController::class, 'schoolYears']);
        Route::get('/trimesters', [AdminController::class, 'trimesters']);
    });

    // Secretary routes
    Route::prefix('secretary')->middleware('api.role:secretary')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SecretaryController::class, 'dashboard']);

        // Students (read-only)
        Route::get('/students', [SecretaryController::class, 'students']);
        Route::get('/students/{id}', [SecretaryController::class, 'showStudent']);
        Route::get('/students/{id}/payments', [SecretaryController::class, 'studentPayments']);

        // Payments
        Route::post('/students/{id}/payments', [SecretaryController::class, 'storePayment']);
        Route::put('/payments/{id}', [SecretaryController::class, 'updatePayment']);

        // Classes
        Route::get('/classes', [SecretaryController::class, 'classes']);

        // Configuration
        Route::get('/school-years', [SecretaryController::class, 'schoolYears']);

        // Search
        Route::get('/search/students', [SecretaryController::class, 'searchStudents']);
    });

    // Teacher routes
    Route::prefix('teacher')->middleware('api.role:teacher')->group(function () {
        // Classes
        Route::get('/classes', [TeacherController::class, 'classes']);

        // Students & Grades
        Route::get('/classes/{classId}/students', [TeacherController::class, 'classStudents']);

        // Grades management
        Route::post('/grades', [TeacherController::class, 'storeGrades']);

        // Subjects
        Route::get('/subjects', [TeacherController::class, 'subjects']);

        // Timetable
        Route::get('/timetable', [TeacherController::class, 'timetable']);
    });

    // Parent routes
    Route::prefix('parent')->middleware('api.role:parent')->group(function () {
        // Children
        Route::get('/children', [ParentController::class, 'children']);
        Route::get('/children/{id}', [ParentController::class, 'showChild']);

        // Bulletins
        Route::get('/children/{id}/bulletins', [ParentController::class, 'childBulletins']);
        Route::get('/children/{id}/bulletins/{trimesterId}', [ParentController::class, 'childBulletin']);

        // Grades
        Route::get('/children/{id}/grades', [ParentController::class, 'childGrades']);

        // Attendance
        Route::get('/children/{id}/attendance', [ParentController::class, 'childAttendance']);

        // Payments
        Route::get('/children/{id}/payments', [ParentController::class, 'childPayments']);
    });
});
