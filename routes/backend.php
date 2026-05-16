<?php

use App\Http\Controllers\Web\Backend\Admin\FAQController;
use App\Http\Controllers\Web\Backend\CategoryController;
use App\Http\Controllers\Web\Backend\CustomerController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\OrderController;
use App\Http\Controllers\Web\Backend\PermissionController;
use App\Http\Controllers\Web\Backend\RoleController;
use App\Http\Controllers\Web\Backend\SettingController;
use App\Http\Controllers\Web\Backend\Settings\DynamicPagesController;
use App\Http\Controllers\Web\Backend\Settings\ProfileSettingController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Inventory\InventoryController;
use App\Http\Controllers\Web\POS\PosController;
use App\Http\Controllers\Web\Products\ProductController;
use App\Http\Controllers\Web\Purchases\PurchaseOrderController;
use App\Http\Controllers\Web\Reports\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\Admin\SupplierController;

// ── Public
Route::get('/', fn() => view('welcome'));

// ── Authenticated Admin/super_admin
Route::middleware(['auth', 'verified', 'role_or_permission:admin|super_admin'])->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'getAnalytics'])->name('analytics');

    // Settings
    Route::controller(SettingController::class)->group(function () {
        Route::get('/general/setting', 'create')->name('general.setting');
        Route::post('/system/update', 'update')->name('system.update');
        Route::get('/system/setting', 'systemSetting')->name('system.setting');
        Route::post('/system/setting/update', 'systemSettingUpdate')->name('system.settingupdate');
        Route::get('/setting', 'adminSetting')->name('setting');
        Route::get('/stripe', 'stripe')->name('setting.stripe');
        Route::post('/stripe', 'stripestore')->name('setting.stripestore');
        Route::get('/paypal', 'paypal')->name('setting.paypal');
        Route::post('/paypal', 'paypalstore')->name('setting.paypalstore');
        Route::get('/mail', 'mail')->name('setting.mail');
        Route::post('/mail', 'mailstore')->name('setting.mailstore');
        Route::post('/setting/update', 'adminSettingUpdate')->name('settingupdate');
    });

    // Profile
    Route::controller(ProfileSettingController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile');
        Route::post('/profile/update', 'updateProfile')->name('profile.update');
        Route::post('/profile/update/password', 'updatePassword')->name('profile.update.password');
        Route::post('/profile/update/profile-picture', 'updateProfilePicture')->name('profile.update.profile.picture');
        Route::get('/checkusername', 'checkusername')->name('checkusername');
    });

    // FAQ
    Route::controller(FAQController::class)->prefix('faq')->name('faq.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/get', 'get')->name('get');
        Route::post('/priorities', 'priority')->name('priority');
        Route::get('/status/{id}', 'changeStatus')->name('status');
        Route::post('/store', 'store')->name('store');
        Route::post('/update', 'update')->name('update');
        Route::get('/destroy/{id}', 'destroy')->name('destroy');
    });

    // Users
    Route::controller(UserController::class)->prefix('users')->name('user.')->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
        Route::get('/list', 'index')->name('list');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
        Route::post('/status/{id}', 'changeStatus')->name('status');
        Route::get('/view/{id}', 'show')->name('show');
        Route::post('/delete', 'destroy')->name('destroy');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::post('/status/{id}', [CategoryController::class, 'changeStatus'])->name('status');
        Route::post('/bulk-delete', [CategoryController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Products
    Route::prefix('products')->name('products.')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    // Inventory
    Route::prefix('inventory')->name('inventory.')->controller(InventoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/adjust', 'adjust')->name('adjust');
        Route::post('/transfer', 'transfer')->name('transfer');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/sales', 'sales')->name('sales');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/profit-loss', 'profitLoss')->name('profit-loss');
    });

    // Orders
    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/create', [OrderController::class, 'create'])->name('create');
        Route::post('/store', [OrderController::class, 'store'])->name('store');
        Route::get('/view/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [OrderController::class, 'destroy'])->name('destroy');
        Route::post('/status/{id}', [OrderController::class, 'changeStatus'])->name('status');
        Route::post('/bulk-delete', [OrderController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Customers
    Route::prefix('customers')->name('customer.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });
    // Suppliers
    Route::middleware(['auth', 'tenant'])->prefix('contacts')->name('suppliers.')->group(function () {
        Route::get('type=supplier',         [SupplierController::class, 'index'])->name('index');
        Route::get('suppliers/create',      [SupplierController::class, 'create'])->name('create');
        Route::post('suppliers',            [SupplierController::class, 'store'])->name('store');
        Route::get('suppliers/{supplier}',  [SupplierController::class, 'show'])->name('show');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('suppliers/{supplier}',  [SupplierController::class, 'update'])->name('update');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::patch('suppliers/{supplier}/toggle', [SupplierController::class, 'toggleStatus'])->name('toggle');
        Route::get('suppliers/export/csv',  [SupplierController::class, 'exportCsv'])->name('export.csv');
        Route::get('suppliers/export/pdf',  [SupplierController::class, 'exportPdf'])->name('export.pdf');
    });
    // Purchases
    Route::prefix('purchases')->name('purchases.')->controller(PurchaseOrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Dynamic Pages
    Route::prefix('dynamicpages')->name('dynamicpages.')->controller(DynamicPagesController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/store', 'store')->name('store');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
        Route::post('/status/{id}', 'changeStatus')->name('status');
        Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
    });

    // Permissions & Roles
    Route::prefix('permissions')->name('permissions.')->controller(PermissionController::class)->group(function () {
        Route::get('/list', 'index')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });

    Route::prefix('roles')->name('roles.')->controller(RoleController::class)->group(function () {
        Route::get('/list', 'index')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });
});

require __DIR__ . '/auth.php';
