<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes (login, password reset)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard (home page for authenticated users)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Results routes
    Route::prefix('results')->name('results.')->group(function () {
        Route::get('/create', [ResultController::class, 'create'])->name('create');
        Route::post('/', [ResultController::class, 'store'])->name('store');
    });

    // Challenge routes
    Route::prefix('challenges')->name('challenges.')->group(function () {
        Route::get('/create', [ChallengeController::class, 'create'])->name('create');
        Route::post('/', [ChallengeController::class, 'store'])->name('store');
        Route::patch('/{challenge}/accept', [ChallengeController::class, 'accept'])->name('accept');
        Route::patch('/{challenge}/reject', [ChallengeController::class, 'reject'])->name('reject');
        Route::delete('/{challenge}', [ChallengeController::class, 'destroy'])->name('destroy');
    });

    // Profile routes (player can edit their own profile)
    Route::get('/profile', [PlayerController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [PlayerController::class, 'update'])->name('profile.update');
});
