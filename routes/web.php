<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Backend\DashboardController;
use Illuminate\Support\Facades\Route;

/**
 * Public
 */
Route::get('/', fn () => view('welcome'));

// Route::get('/dashboard', function () {
//     return view('dashboard');
// });

/**
 * Dashboard
 */
/**
 * Public
 */
Route::get('/', fn () => view('welcome'));

/**
 * Cashier
 */

require __DIR__.'/auth.php';
