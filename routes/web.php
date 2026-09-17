<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SuperAdminOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirect root to dashboard if authenticated
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Super Admin routes - protected by SuperAdminOnly middleware
Route::middleware(['auth', 'verified', SuperAdminOnly::class])->group(function () {
    // Schools CRUD
    Route::resource('schools', SchoolController::class)->names([
        'index' => 'schools.index',
        'create' => 'schools.create',
        'store' => 'schools.store',
        'show' => 'schools.show',
        'edit' => 'schools.edit',
        'update' => 'schools.update',
        'destroy' => 'schools.destroy',
    ]);

    // Users CRUD
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);
});

// Academic Year routes (Super Admin, School Admin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('academic-years', AcademicYearController::class)->names([
        'index' => 'academic-years.index',
        'create' => 'academic-years.create',
        'store' => 'academic-years.store',
        'show' => 'academic-years.show',
        'edit' => 'academic-years.edit',
        'update' => 'academic-years.update',
        'destroy' => 'academic-years.destroy',
    ]);

    Route::resource('departments', DepartmentController::class)->names([
        'index' => 'departments.index',
        'create' => 'departments.create',
        'store' => 'departments.store',
        'show' => 'departments.show',
        'edit' => 'departments.edit',
        'update' => 'departments.update',
        'destroy' => 'departments.destroy',
    ]);

    Route::resource('classes', SchoolClassController::class)->names([
        'index' => 'classes.index',
        'create' => 'classes.create',
        'store' => 'classes.store',
        'show' => 'classes.show',
        'edit' => 'classes.edit',
        'update' => 'classes.update',
        'destroy' => 'classes.destroy',
    ]);

    Route::resource('students', StudentController::class)->names([
        'index' => 'students.index',
        'create' => 'students.create',
        'store' => 'students.store',
        'show' => 'students.show',
        'edit' => 'students.edit',
        'update' => 'students.update',
        'destroy' => 'students.destroy',
    ]);
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
