<?php

use Illuminate\Support\Facades\Route;

$appOrderController = 'App\Http\Controllers\API\App\V1\AppOrderController';
$billController = 'App\Http\Controllers\API\App\V1\BillController';
$cartController = 'App\Http\Controllers\API\App\V1\CartController';
$menuController = 'App\Http\Controllers\API\App\V1\MenuController';
$notificationController = 'App\Http\Controllers\API\App\V1\NotificationController';
$seatController = 'App\Http\Controllers\API\App\V1\SeatController';
$tableController = 'App\Http\Controllers\API\App\V1\TableController';

$appControllers = [
    $appOrderController,
    $billController,
    $cartController,
    $menuController,
    $notificationController,
    $seatController,
    $tableController,
];

if (collect($appControllers)->every(fn (string $controller): bool => class_exists($controller))) {
    Route::prefix('app')
        ->middleware(['jwt.verify'])
        ->group(function () use (
            $appOrderController,
            $billController,
            $cartController,
            $menuController,
            $notificationController,
            $seatController,
            $tableController
        ) {

            //  Table View
            Route::prefix('tables')->group(function () use ($tableController, $seatController) {
                Route::get('/', [$tableController, 'index']);
                Route::get('/stats', [$tableController, 'stats']);
                Route::get('/{table}', [$tableController, 'show']);
                Route::patch('/{table}/status', [$tableController, 'updateStatus']);

                //  Seat Assign
                Route::get('/{table}/seats', [$seatController, 'index']);
                Route::post('/{table}/seats/assign', [$seatController, 'assign']);
                Route::post('/{table}/seats/clear', [$seatController, 'clear']);
            });

            //   Menu
            Route::prefix('menu')->group(function () use ($menuController) {
                Route::get('/categories', [$menuController, 'categories']);
                Route::get('/items', [$menuController, 'index']);
                Route::get('/items/{item}', [$menuController, 'show']);
            });

            //   Cart
            Route::prefix('cart')->group(function () use ($cartController) {
                Route::get('/{tableId}', [$cartController, 'show']);
                Route::post('/{tableId}/add', [$cartController, 'add']);
                Route::patch('/{tableId}/items/{cartItemId}', [$cartController, 'update']);
                Route::delete('/{tableId}/items/{cartItemId}', [$cartController, 'remove']);
                Route::delete('/{tableId}/clear', [$cartController, 'clear']);
            });

            //   Current Order
            Route::prefix('orders')->group(function () use ($appOrderController) {
                Route::get('/', [$appOrderController, 'index']);
                Route::post('/', [$appOrderController, 'store']);
                Route::get('/{order}', [$appOrderController, 'show']);
                Route::patch('/{order}/status', [$appOrderController, 'updateStatus']);
                Route::post('/{order}/hold', [$appOrderController, 'hold']);
                Route::post('/{order}/note', [$appOrderController, 'addNote']);

                // QR Orders
                Route::get('/qr/{tableId}', [$appOrderController, 'qrOrders']);
                Route::patch('/qr/{qrOrder}/approve', [$appOrderController, 'approveQr']);
                Route::patch('/qr/{qrOrder}/reject', [$appOrderController, 'rejectQr']);
            });

            //  Notification
            Route::prefix('notifications')->group(function () use ($notificationController) {
                Route::get('/', [$notificationController, 'index']);
                Route::patch('/{id}/read', [$notificationController, 'markRead']);
                Route::post('/read-all', [$notificationController, 'markAllRead']);
            });

            //  Bill
            Route::prefix('bills')->group(function () use ($billController) {
                Route::get('/{tableId}', [$billController, 'show']);
                Route::post('/{tableId}/request-payment', [$billController, 'requestPayment']);
                Route::post('/{tableId}/pay', [$billController, 'pay']);
            });
        });
}
