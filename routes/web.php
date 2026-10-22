<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});


Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class)->except('show');
    Route::resource('rooms', RoomController::class)->except('show');
    Route::resource('bookings', BookingController::class);
    Route::resource('payments', PaymentController::class);

    
    Route::get('payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
});
