<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Products\ProductController;
use App\Http\Controllers\Api\V1\Orders\CartController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Reports\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public Auth Endpoints 
    Route::prefix('auth')->name('api.auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
        Route::get('/me', [AuthController::class, 'me'])
            ->middleware('auth:sanctum')
            ->name('me');
    });

    // ── Authenticated API Routes 
    Route::middleware([
        'auth:sanctum',
        \App\Http\Middleware\IdentifyTenant::class,
        \App\Http\Middleware\ScopeTenant::class,
        'throttle:api',
    ])->group(function () {

        // Products
        Route::prefix('products')->name('api.products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('barcode');
            Route::get('/{id}', [ProductController::class, 'show'])->name('show');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        // POS Cart (high-frequency — dedicated rate limit)
        Route::prefix('pos')->name('api.pos.')->middleware('throttle:pos-cart')->group(function () {
            Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
            Route::post('/cart/items', [CartController::class, 'addItem'])->name('cart.add');
            Route::patch('/cart/items/{variantId}', [CartController::class, 'updateItem'])->name('cart.update');
            Route::delete('/cart/items/{variantId}', [CartController::class, 'removeItem'])->name('cart.remove');
            Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
            Route::delete('/cart', [CartController::class, 'clearCart'])->name('cart.clear');
            Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
        });

        // Orders
        Route::prefix('orders')->name('api.orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderController::class, 'show'])->name('show');
            Route::post('/{id}/refund', [OrderController::class, 'refund'])->name('refund');
            Route::post('/{id}/void', [OrderController::class, 'void'])->name('void');
            Route::post('/offline-sync', [OrderController::class, 'syncOffline'])->name('offline-sync');
        });

        // Inventory
        Route::prefix('inventory')->name('api.inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::post('/transfer', [InventoryController::class, 'transfer'])->name('transfer');
        });

        // Reports
        Route::prefix('reports')->name('api.reports.')->group(function () {
            Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        });
    });
});
