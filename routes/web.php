<?php

use App\Http\Controllers\Admin\DomainPriceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DomainOrderController;
use App\Http\Controllers\DomainSearchController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentController;
use App\Models\DomainPrice;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Public domain search page and API
Route::get('/', function () {
    $allPrices = DomainPrice::all()->keyBy('category');
    return view('index', compact('allPrices'));
});

Route::post('/domain-search/api', [DomainSearchController::class, 'search'])->name('domain.search.api');

// Domain order routes
Route::get('/domain/contact-info', [DomainOrderController::class, 'showContactForm'])->name('domain.contact.info');
Route::post('/domain/contact-submit', [DomainOrderController::class, 'store'])->name('domain.contact.submit');

Route::get('/otp-verification', [OtpController::class, 'showVerificationForm'])->name('otp.verification.page');
Route::post('/otp-resend', [OtpController::class, 'resendOtp'])->name('otp.resend');
Route::post('/payment-details', [OtpController::class, 'paymentDetails'])->name('payment.details');

Route::post('/pay-securely', [PaymentController::class, 'paySecurely'])->name('payment.paysecurely');
Route::post('/skip-payment', [PaymentController::class, 'skipPayment'])->name('payment.skip');

Route::get('/confirmation', function () {
    return view('layouts.confirmation');
});

Route::get('/invoice/view/{unique_code}', [DomainOrderController::class, 'viewInvoiceByCode'])->name('invoice.view');


// ===============================
// ✅ Admin Routes - Fixed Prefix
// ===============================
Route::prefix('lkadminslh')->name('admin.')->group(function () {

    // Admin login (guest)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');

        // If you ever enable registration:
        // Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
        // Route::post('register', [RegisterController::class, 'register'])->name('register.submit');
    });

    // Authenticated admin-only routes
    Route::middleware('auth:admin')->group(function () {

        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Domain Prices Management
        Route::get('domain-prices', [DomainPriceController::class, 'index'])->name('domain_prices.index');
        Route::post('domain-prices', [DomainPriceController::class, 'update'])->name('domain_prices.update');
        Route::put('/domain-prices/{id}/update', [DomainPriceController::class, 'updateSingle'])->name('domain_prices.update.single');

        Route::post('invoices/{id}/sms', [InvoiceController::class, 'sendSms'])->name('invoices.sendSms');

        // User management
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{admin}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{admin}', [UserController::class, 'update'])->name('users.update');

        Route::prefix('users')->group(function () {
            Route::get('/trash', [UserController::class, 'trash'])->name('users.trash');
            Route::put('/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        });

        // Super admin privileges
        Route::middleware('super_admin')->group(function () {
            Route::delete('users/{admin}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::put('users/{admin}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        });

        // Domain orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [DomainOrderController::class, 'adminIndex'])->name('orders.index');
            Route::get('/trash', [DomainOrderController::class, 'trashed'])->name('orders.trash');
            Route::get('/{id}', [DomainOrderController::class, 'show'])->name('orders.show');
            Route::delete('/{id}', [DomainOrderController::class, 'destroy'])->name('orders.destroy');
            Route::post('/{id}/restore', [DomainOrderController::class, 'restore'])->name('orders.restore');
            Route::delete('/{id}/force-delete', [DomainOrderController::class, 'forceDelete'])->name('orders.forceDelete');
        });

        // SMS Templates and Reports
        Route::prefix('sms')->name('sms.')->group(function () {
            Route::get('/template', [SmsController::class, 'createTemplate'])->name('template');
            Route::post('/template', [SmsController::class, 'storeTemplate'])->name('template.store');
            Route::get('/template/{id}/edit', [SmsController::class, 'editTemplate'])->name('template.edit');
            Route::put('/template/{id}', [SmsController::class, 'updateTemplate'])->name('template.update');
            Route::delete('/template/{id}', [SmsController::class, 'destroyTemplate'])->name('template.destroy');

            Route::get('/report', [SmsController::class, 'report'])->name('report');
            Route::get('/send', [SmsController::class, 'showSendForm'])->name('send');
            Route::post('/send', [SmsController::class, 'sendSms'])->name('send.post');
            Route::post('/request-sender-id', [SmsController::class, 'requestSenderId'])->name('request_sender_id');
        });

        // Invoices
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/report', [InvoiceController::class, 'report'])->name('invoices.report');
        Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');

        // Payment Verifications
        Route::get('verification', [VerificationController::class, 'index'])->name('verification.index');
        Route::get('verification/create', [VerificationController::class, 'create'])->name('verification.create');
        Route::post('verification/store', [VerificationController::class, 'store'])->name('verification.store');
        Route::get('verification/{id}', [VerificationController::class, 'show'])->name('verification.show');
        Route::patch('verification/{id}/update-status', [VerificationController::class, 'updateStatus'])->name('verification.updateStatus');
        Route::delete('verification/{id}', [VerificationController::class, 'destroy'])->name('verification.destroy');
        Route::post('verification/{id}/send-sms', [VerificationController::class, 'sendSms'])->name('verification.sendSms');
    });
});


// (Optional) cache clearing routes for dev
// Route::get('/clear-cache', function () {
//     Artisan::call('cache:clear');
//     Artisan::call('route:clear');
//     Artisan::call('config:clear');
//     Artisan::call('view:clear');
//     return 'Caches cleared!';
// });
