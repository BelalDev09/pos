<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\DashboardController;
use App\Http\Controllers\ProfileController;

/**
 * Public
 */

Route::get('/', fn() => view('welcome'));

// Route::get('/dashboard', function () {
//     return view('dashboard');
// });

/**
 * Dashboard
 */

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

/**
 * Cashier
 */

require __DIR__ . '/auth.php';
