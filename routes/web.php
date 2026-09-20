<?php

use App\Http\Controllers\Auth\SSOController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Public login page (Route::view keeps `php artisan route:cache` working in production)
Route::view('/', 'welcome')->name('login');

// SSO OAuth2 Integration Routes (OBJ-1, WBS 1.2.1)
Route::prefix('auth/sso')->middleware('throttle:20,1')->group(function () {
    Route::get('/redirect', [SSOController::class, 'redirect'])->name('sso.redirect');
    Route::get('/callback', [SSOController::class, 'callback'])->name('sso.callback');
});

// Protected Campus Portal Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // POST (not GET) so a third-party page cannot log users out via a plain link/image (CSRF)
    Route::post('/logout', [SSOController::class, 'logout'])->name('logout');
});
