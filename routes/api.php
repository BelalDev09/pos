<?php

use App\Http\Controllers\API\App\v1\AppOrderController;
use App\Http\Controllers\API\App\v1\CartController;
use App\Http\Controllers\API\App\v1\MenuController;
use App\Http\Controllers\API\App\v1\SeatController;
use App\Http\Controllers\API\App\v1\TableController;
use App\Http\Controllers\API\App\V1\BillController;
use App\Http\Controllers\API\App\V1\NotificationController;
use App\Http\Controllers\API\Cashier\CashierController;
use App\Http\Controllers\API\Cashier\CashierDiscountController;
use App\Http\Controllers\API\Cashier\CashierPaymentController;
use App\Http\Controllers\API\Customer\CustomerController;
use App\Http\Controllers\API\Customer\CustomerOrderController;
use App\Http\Controllers\API\Manager\ManagerController;
use App\Http\Controllers\API\SocialiteController;
use App\Http\Controllers\API\user\AccountController;
use App\Http\Controllers\API\user\ProfileController;
use App\Http\Controllers\API\UserAuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::controller(UserAuthController::class)->prefix('user')->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');

    // Resend Otp
    Route::post('resend-otp', 'resendOtp');

    // Forget Password
    Route::post('forget-password', 'forgetPassword');
    Route::post('verify-otp-password', 'varifyOtpWithOutAuth');
    Route::post('reset-password', 'resetPassword');
    // Social login
    Route::post('/social/login', 'socialLogin');
});
Route::any('/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');


Route::group(['prefix' => 'user', 'middleware' => 'jwt.verify'], function () {
    Route::get('me', [UserAuthController::class, 'me']);
    Route::post('logout', [UserAuthController::class, 'logout']);

    // Account routes
    Route::controller(AccountController::class)->prefix('account')->group(function () {
        Route::get('/', 'index');
        Route::post('/update', 'update');
        Route::post('/change-password', 'changePassword');
        Route::post('/delete', 'destroy');
    });

    /**
     * Cashier route
     */

    Route::prefix('cashier')->group(function () {

        // Orders
        Route::get('orders',                        [CashierController::class, 'getOrders']);
        Route::get('orders/{order_id}',             [CashierController::class, 'getOrderDetail']);

        // Payment —
        Route::post('payments/process',             [CashierPaymentController::class, 'processPayment']);

        // Discount
        Route::post('orders/{order_id}/discount',   [CashierDiscountController::class, 'applyDiscount']);
        Route::delete('orders/{order_id}/discount', [CashierDiscountController::class, 'removeDiscount']);

        // Tips
        Route::post('orders/{order_id}/tip',        [CashierController::class, 'addTip']);
        Route::delete('orders/{order_id}/tip',      [CashierController::class, 'removeTip']);

        // Refund
        Route::post('refunds/full',                 [CashierController::class, 'fullRefund']);

        // Report
        Route::get('reports/daily',                 [CashierController::class, 'dailySalesReport']);
    });

    // Manager deshboard api

    Route::prefix('manager')->group(function () {

        //  Dashboard
        Route::get('dashboard',                         [ManagerController::class, 'dashboard']);

        //  Approval Queue
        Route::get('approvals',                         [ManagerController::class, 'getApprovals']);
        Route::post('approvals/{id}/approve',           [ManagerController::class, 'approveRequest']);
        Route::post('approvals/{id}/reject',            [ManagerController::class, 'rejectRequest']);

        //  Discount / Promotion Control
        Route::get('promotions',                        [ManagerController::class, 'getPromotions']);
        Route::post('promotions',                       [ManagerController::class, 'createPromotion']);
        Route::patch('promotions/{id}/toggle',          [ManagerController::class, 'togglePromotion']);
        Route::delete('promotions/{id}',                [ManagerController::class, 'deletePromotion']);

        //  Cash Management
        Route::get('cash',                              [ManagerController::class, 'cashManagement']);

        //  Staff Shift Control
        Route::get('shifts',                            [ManagerController::class, 'getShifts']);
        Route::post('shifts/{shift_id}/close',          [ManagerController::class, 'forceCloseShift']);

        //  Live Kitchen Monitor
        Route::get('kitchen',                           [ManagerController::class, 'kitchenMonitor']);

        //  Reports
        Route::get('reports/sales',                     [ManagerController::class, 'salesReport']);
        Route::get('reports/category',                  [ManagerController::class, 'categoryReport']);
        Route::get('reports/staff',                     [ManagerController::class, 'staffReport']);
    });
});
// Profile
Route::controller(ProfileController::class)->prefix('profile')->group(function () {
    Route::get('/', 'index');
    Route::post('/update', 'update');
});
// customers routes
Route::prefix('customer')->group(function () {
    Route::get('qr/{token}',          [CustomerController::class, 'scanQr']);
    Route::get('menu/categories',     [CustomerController::class, 'getCategories']);
    Route::get('menu/items',          [CustomerController::class, 'getMenuItems']);
    Route::get('menu/items/{id}',     [CustomerController::class, 'getItemDetail']);
    Route::post('orders',             [CustomerOrderController::class, 'placeOrder']);
    Route::get('orders/{id}',         [CustomerController::class, 'getOrder']);
    Route::get('orders/{id}/track',   [CustomerController::class, 'trackOrder']);
});
