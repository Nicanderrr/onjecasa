<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ProductCrudController;
use App\Http\Controllers\Admin\ProductImportController;
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
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminAIController;
use App\Http\Controllers\Cashier\AIController as CashierAIController;
use App\Http\Controllers\Cashier\PageController as CashierPageController;
use App\Http\Controllers\Cashier\SaleController;
use App\Http\Controllers\PublicReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showUserOtpLogin'])->name('login');
Route::post('/otp/send', [LoginController::class, 'sendUserOtp'])->name('otp.send');
Route::post('/otp/verify', [LoginController::class, 'verifyUserOtp'])->name('otp.verify');
Route::redirect('/login', '/');
Route::get('/admin', [LoginController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/otp', [LoginController::class, 'showAdminOtp'])->name('admin.otp.show');
Route::post('/admin/otp', [LoginController::class, 'verifyAdminOtp'])->name('admin.otp.verify');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/r/{token}', [PublicReceiptController::class, 'show'])->name('receipts.public');
Route::get('/r/{token}/print', [PublicReceiptController::class, 'print'])->name('receipts.public.print');

Route::middleware('auth')->group(function () {
    Route::middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard.alias');
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::patch('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/audit', [SuperAdminController::class, 'auditLogs'])->name('audit');
        Route::get('/security', [SuperAdminController::class, 'security'])->name('security');
        Route::put('/security', [SuperAdminController::class, 'updateSecurity'])->name('security.update');
        Route::get('/settings', [SuperAdminController::class, 'settingsPage'])->name('settings');
        Route::put('/profile', [SuperAdminController::class, 'updateProfile'])->name('profile.update');
        Route::get('/maintenance', [SuperAdminController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/clear-cache', [SuperAdminController::class, 'clearCache'])->name('maintenance.clear-cache');
        Route::post('/maintenance/toggle', [SuperAdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');
        Route::post('/ai/chat', [SuperAdminAIController::class, 'chat'])->name('ai.chat');
        Route::get('/ai/alerts', [SuperAdminAIController::class, 'alerts'])->name('ai.alerts');
        Route::post('/ai/realtime/call', [SuperAdminAIController::class, 'realtimeCall'])->name('ai.realtime.call');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('products/import', [ProductImportController::class, 'create'])->name('products.import.create');
        Route::post('products/import', [ProductImportController::class, 'store'])->name('products.import.store');
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
        Route::post('ai/realtime/call', [AdminAIController::class, 'realtimeCall'])->name('ai.realtime.call');
    });

    Route::middleware('role:cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/sales/new', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::put('/profile', [CashierPageController::class, 'updateProfile'])->name('profile.update');
        Route::get('/receipts/{id}', [\App\Http\Controllers\Cashier\ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{id}/print', [\App\Http\Controllers\Cashier\ReceiptController::class, 'print'])->name('receipts.print');
        Route::post('/ai/chat', [CashierAIController::class, 'chat'])->name('ai.chat');
        Route::get('/ai/alerts', [CashierAIController::class, 'alerts'])->name('ai.alerts');
        Route::post('/ai/realtime/call', [CashierAIController::class, 'realtimeCall'])->name('ai.realtime.call');
        Route::get('/{page}', [CashierPageController::class, 'show'])->name('pages.show');
    });
});
