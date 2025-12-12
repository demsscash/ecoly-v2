<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Admin\SchoolSettings;
use App\Livewire\Admin\SchoolYears;
use App\Livewire\Admin\Trimesters;
use App\Livewire\Admin\Classes;
use App\Livewire\Admin\Subjects;
use App\Livewire\Admin\ClassSubjects;
use App\Livewire\Admin\Users;
use App\Livewire\Admin\TeacherAssignments;
use App\Livewire\Admin\GradingConfig;
use App\Livewire\Admin\Students;
use App\Livewire\Admin\StudentShow;
use App\Livewire\Admin\ClassGrades;
use App\Livewire\Teacher\MyClasses;
use App\Livewire\Grades\GradeEntry;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/school-settings', SchoolSettings::class)->name('admin.school-settings');
        Route::get('/admin/school-years', SchoolYears::class)->name('admin.school-years');
        Route::get('/admin/trimesters', Trimesters::class)->name('admin.trimesters');
        Route::get('/admin/classes', Classes::class)->name('admin.classes');
        Route::get('/admin/subjects', Subjects::class)->name('admin.subjects');
        Route::get('/admin/class-subjects', ClassSubjects::class)->name('admin.class-subjects');
        Route::get('/admin/users', Users::class)->name('admin.users');
        Route::get('/admin/teacher-assignments', TeacherAssignments::class)->name('admin.teacher-assignments');
        Route::get('/admin/grading-config', GradingConfig::class)->name('admin.grading-config');
    });
    
    // Admin + Secretary routes
    Route::middleware('role:admin,secretary')->group(function () {
        Route::get('/students', Students::class)->name('students');
        Route::get('/students/{student}', StudentShow::class)->name('students.show');
        Route::get('/class-grades', ClassGrades::class)->name('class-grades');
    });

    // Teacher routes
    Route::middleware('role:teacher')->group(function () {
        Route::get('/my-classes', MyClasses::class)->name('teacher.my-classes');
    });
    
    // All authenticated users (grades)
    Route::get('/grades', GradeEntry::class)->name('grades');
    
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Bulletins
Route::get('/bulletins', App\Livewire\Admin\Bulletins::class)
    ->middleware(['auth', 'role:admin,secretary'])
    ->name('bulletins');

// Payments
Route::get('/payments', App\Livewire\Admin\Payments::class)
    ->middleware(['auth', 'role:admin,secretary'])
    ->name('payments');
