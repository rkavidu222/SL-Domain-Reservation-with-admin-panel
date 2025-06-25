<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DomainCategory;
use App\Http\Controllers\DomainSearchController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return view('index');
});

Route::get('/domain-category', [DomainCategory::class, 'show'])->name('domain.category');

Route::get('/contact-information', [ContactController::class, 'showForm'])->name('contact.page');
Route::post('/contact-information', [ContactController::class, 'submit'])->name('contact.submit');

Route::post('/payment-details', [OtpController::class, 'paymentDetails'])->name('payment.details');

Route::get('/skip-payment', [PaymentController::class, 'skipPayment'])->name('payment.skip');

Route::get('/confirmation', function () {
    return view('layouts.confirmation');
});

Route::post('/domain-search', [DomainSearchController::class, 'search']);


// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (login, register)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.submit');

        Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
        Route::post('register', [RegisterController::class, 'register'])->name('register.submit');
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('dashboard', function () {
            return view('admin.layouts.dashboard');
        })->name('dashboard');

        // User management routes accessible to all authenticated admins
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{admin}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{admin}', [UserController::class, 'update'])->name('users.update');

        // Soft delete (Trash) routes accessible to authenticated admins
        Route::prefix('users')->group(function () {
            Route::get('/trash', [UserController::class, 'trash'])->name('users.trash');
            Route::put('/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        });

        // Super admin only routes (delete and suspend actions)
        Route::middleware('super_admin')->group(function () {
            Route::delete('users/{admin}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::put('users/{admin}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        });
    });



});
