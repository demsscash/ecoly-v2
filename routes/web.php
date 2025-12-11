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
    });
    
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});
