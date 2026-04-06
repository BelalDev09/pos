<?php

use App\Http\Controllers\API\App\v1\AppOrderController;
use App\Http\Controllers\API\App\v1\CartController;
use App\Http\Controllers\API\App\v1\MenuController;
use App\Http\Controllers\API\App\v1\SeatController;
use App\Http\Controllers\API\App\v1\TableController;
use App\Http\Controllers\API\App\V1\BillController;
use App\Http\Controllers\API\App\V1\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('app')
    ->middleware(['jwt.verify'])
    ->group(function () {

        //  Table View
        Route::prefix('tables')->group(function () {
            Route::get('/',              [TableController::class, 'index']);
            Route::get('/stats',         [TableController::class, 'stats']);
            Route::get('/{table}',       [TableController::class, 'show']);
            Route::patch('/{table}/status', [TableController::class, 'updateStatus']);

            //  Seat Assign
            Route::get('/{table}/seats',       [SeatController::class, 'index']);
            Route::post('/{table}/seats/assign', [SeatController::class, 'assign']);
            Route::post('/{table}/seats/clear', [SeatController::class, 'clear']);
        });

        //   Menu
        Route::prefix('menu')->group(function () {
            Route::get('/categories',        [MenuController::class, 'categories']);
            Route::get('/items',             [MenuController::class, 'index']);
            Route::get('/items/{item}',      [MenuController::class, 'show']);
        });

        //   Cart
        Route::prefix('cart')->group(function () {
            Route::get('/{tableId}',         [CartController::class, 'show']);
            Route::post('/{tableId}/add',    [CartController::class, 'add']);
            Route::patch('/{tableId}/items/{cartItemId}', [CartController::class, 'update']);
            Route::delete('/{tableId}/items/{cartItemId}', [CartController::class, 'remove']);
            Route::delete('/{tableId}/clear', [CartController::class, 'clear']);
        });

        //   Current Order
        Route::prefix('orders')->group(function () {
            Route::get('/',                  [AppOrderController::class, 'index']);
            Route::post('/',                 [AppOrderController::class, 'store']);
            Route::get('/{order}',           [AppOrderController::class, 'show']);
            Route::patch('/{order}/status',  [AppOrderController::class, 'updateStatus']);
            Route::post('/{order}/hold',     [AppOrderController::class, 'hold']);
            Route::post('/{order}/note',     [AppOrderController::class, 'addNote']);

            // QR Orders
            Route::get('/qr/{tableId}',      [AppOrderController::class, 'qrOrders']);
            Route::patch('/qr/{qrOrder}/approve', [AppOrderController::class, 'approveQr']);
            Route::patch('/qr/{qrOrder}/reject',  [AppOrderController::class, 'rejectQr']);
        });

        //  Notification
        Route::prefix('notifications')->group(function () {
            Route::get('/',              [NotificationController::class, 'index']);
            Route::patch('/{id}/read',   [NotificationController::class, 'markRead']);
            Route::post('/read-all',     [NotificationController::class, 'markAllRead']);
        });

        //  Bill
        Route::prefix('bills')->group(function () {
            Route::get('/{tableId}',         [BillController::class, 'show']);
            Route::post('/{tableId}/request-payment', [BillController::class, 'requestPayment']);
            Route::post('/{tableId}/pay',    [BillController::class, 'pay']);
        });
    });
