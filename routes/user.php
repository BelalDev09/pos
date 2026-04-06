<?php

use App\Http\Controllers\Api\backend\Auth;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserController;

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [UserAuthController::class, 'logout']);
    Route::get('me', [UserAuthController::class, 'me']);
    Route::post('refresh', [UserAuthController::class, 'refresh']);

    Route::delete('/delete/user', [UserController::class, 'deleteUser']);

    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('user-update', [UserController::class, 'updateUserInfo']);

    // Get Notifications
    Route::get('/my-notifications', [UserController::class, 'getMyNotifications']);
});
