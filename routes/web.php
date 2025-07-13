<?php

use App\Http\Controllers\Admin\DomainPriceController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DomainOrderController;
use App\Http\Controllers\DomainSearchController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentController;
use App\Models\DomainPrice;
use Illuminate\Support\Facades\Route;

// Public domain search page and API
Route::get('/', function () {
    $allPrices = DomainPrice::all()->keyBy('category');
    return view('index', compact('allPrices'));
});

Route::post('/domain-search/api', [DomainSearchController::class, 'search'])->name('domain.search.api');

// Domain order routes - consistent naming
Route::get('/domain/contact-info', [DomainOrderController::class, 'showContactForm'])->name('domain.contact.info');
Route::post('/domain/contact-submit', [DomainOrderController::class, 'store'])->name('domain.contact.submit');

Route::get('/otp-verification', [OtpController::class, 'showVerificationForm'])->name('otp.verification.page');

// Contact form routes (separate from domain order contact info)
Route::get('/contact-information', [ContactController::class, 'showForm'])->name('contact.page');
Route::post('/contact-information', [ContactController::class, 'submit'])->name('contact.submit');

// Payment routes
Route::post('/payment-details', [OtpController::class, 'paymentDetails'])->name('payment.details');
Route::get('/skip-payment', [PaymentController::class, 'skipPayment'])->name('payment.skip');

// Confirmation page
Route::get('/confirmation', function () {
    return view('layouts.confirmation');
});

// Admin routes group
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes for admin (login/register)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');

        Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register'])->name('register.submit');
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {

        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // Dashboard view
        Route::get('dashboard', function () {
            return view('admin.layouts.dashboard');
        })->name('dashboard');

        // Domain Prices Management
        Route::get('domain-prices', [DomainPriceController::class, 'index'])->name('domain_prices.index');
        Route::post('domain-prices', [DomainPriceController::class, 'update'])->name('domain_prices.update');

        // User management routes for all admins
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{admin}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{admin}', [UserController::class, 'update'])->name('users.update');
		Route::get('users/create', [UserController::class, 'create'])->name('users.create');
		Route::post('users', [UserController::class, 'store'])->name('users.store');


        // Soft delete (trash) functionality for users
        Route::prefix('users')->group(function () {
            Route::get('/trash', [UserController::class, 'trash'])->name('users.trash');
            Route::put('/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        });

        // Super admin-only routes for user delete and suspend
        Route::middleware('super_admin')->group(function () {
            Route::delete('users/{admin}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::put('users/{admin}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        });

        // Domain Orders Management (Admin)
        Route::prefix('orders')->group(function () {
            Route::get('/', [DomainOrderController::class, 'adminIndex'])->name('orders.index');
            Route::get('/trash', [DomainOrderController::class, 'trashed'])->name('orders.trash');
            Route::get('/{id}', [DomainOrderController::class, 'show'])->name('orders.show');
            Route::delete('/{id}', [DomainOrderController::class, 'destroy'])->name('orders.destroy');
            Route::post('/{id}/restore', [DomainOrderController::class, 'restore'])->name('orders.restore');
            Route::delete('/{id}/force-delete', [DomainOrderController::class, 'forceDelete'])->name('orders.forceDelete');
        });
    });
});
