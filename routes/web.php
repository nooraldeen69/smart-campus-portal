<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Public login page
Route::view('/', 'welcome')->name('login');

// Standard Local Login
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Protected Campus Portal Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // SIS
    Route::get('/sis/transcript', [App\Http\Controllers\SisController::class, 'transcript'])->name('sis.transcript');
    Route::get('/sis/registration', [App\Http\Controllers\SisController::class, 'registration'])->name('sis.registration');
    Route::post('/sis/enroll', [App\Http\Controllers\SisController::class, 'enroll'])->name('sis.enroll');
    Route::post('/sis/drop', [App\Http\Controllers\SisController::class, 'drop'])->name('sis.drop');

    // LMS
    Route::get('/lms', [App\Http\Controllers\LmsController::class, 'index'])->name('lms.index');
    Route::get('/lms/course/{id}', [App\Http\Controllers\LmsController::class, 'show'])->name('lms.show');

    // Library
    Route::get('/library', [App\Http\Controllers\LibraryController::class, 'index'])->name('library.index');
    Route::post('/library/borrow', [App\Http\Controllers\LibraryController::class, 'borrow'])->name('library.borrow');
    Route::post('/library/return', [App\Http\Controllers\LibraryController::class, 'returnBook'])->name('library.returnBook');

    // Profile
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Schedule
    Route::get('/schedule', [App\Http\Controllers\LmsController::class, 'schedule'])->name('lms.schedule');
});

// Admin Panel
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Users
    Route::get('/users', [App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [App\Http\Controllers\AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [App\Http\Controllers\AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/{id}', [App\Http\Controllers\AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/{id}/delete', [App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Grades
    Route::get('/grades', [App\Http\Controllers\AdminGradeController::class, 'index'])->name('admin.grades.index');
    Route::get('/grades/create', [App\Http\Controllers\AdminGradeController::class, 'create'])->name('admin.grades.create');
    Route::post('/grades', [App\Http\Controllers\AdminGradeController::class, 'store'])->name('admin.grades.store');
    Route::post('/grades/{id}/delete', [App\Http\Controllers\AdminGradeController::class, 'destroy'])->name('admin.grades.destroy');

    // Courses
    Route::get('/courses', [App\Http\Controllers\AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [App\Http\Controllers\AdminCourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [App\Http\Controllers\AdminCourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{id}/edit', [App\Http\Controllers\AdminCourseController::class, 'edit'])->name('admin.courses.edit');
    Route::post('/courses/{id}', [App\Http\Controllers\AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::post('/courses/{id}/delete', [App\Http\Controllers\AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

    // Books
    Route::get('/books', [App\Http\Controllers\AdminBookController::class, 'index'])->name('admin.books.index');
    Route::get('/books/create', [App\Http\Controllers\AdminBookController::class, 'create'])->name('admin.books.create');
    Route::post('/books', [App\Http\Controllers\AdminBookController::class, 'store'])->name('admin.books.store');
    Route::get('/books/{id}/edit', [App\Http\Controllers\AdminBookController::class, 'edit'])->name('admin.books.edit');
    Route::post('/books/{id}', [App\Http\Controllers\AdminBookController::class, 'update'])->name('admin.books.update');
    Route::post('/books/{id}/delete', [App\Http\Controllers\AdminBookController::class, 'destroy'])->name('admin.books.destroy');
});
