<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\Admin\CategoryCrudController;
use App\Http\Controllers\Admin\StaffCrudController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AIController as AdminAIController;
use App\Http\Controllers\Admin\AuditTrailController;
use App\Http\Controllers\Cashier\PageController as CashierPageController;
use App\Http\Controllers\Cashier\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/admin/pincode', [LoginController::class, 'showAdminPincode'])->name('admin.pincode.show');
Route::post('/admin/pincode', [LoginController::class, 'verifyAdminPincode'])->name('admin.pincode.verify');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', ProductCrudController::class)->except(['show']);
        Route::resource('categories', CategoryCrudController::class)->except(['show']);
        Route::resource('staff', StaffCrudController::class)->except(['show']);

        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');
        Route::get('orders-reports', [OrderController::class, 'index'])->name('orders-reports.index');
        Route::get('payments-reports', [PaymentController::class, 'index'])->name('payments-reports.index');

        Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('audit-trails', [AuditTrailController::class, 'index'])->name('audit-trails.index');

        Route::get('ai', [AdminAIController::class, 'index'])->name('ai.index');
        Route::post('ai/chat', [AdminAIController::class, 'chat'])->name('ai.chat');
        Route::post('ai/analyze', [AdminAIController::class, 'analyze'])->name('ai.analyze');
        Route::get('ai/alerts', [AdminAIController::class, 'alerts'])->name('ai.alerts');
    });

    Route::middleware('role:cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/sales/new', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/receipts/{id}', [\App\Http\Controllers\Cashier\ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{id}/print', [\App\Http\Controllers\Cashier\ReceiptController::class, 'print'])->name('receipts.print');
        Route::get('/{page}', [CashierPageController::class, 'show'])->name('pages.show');
    });
});
