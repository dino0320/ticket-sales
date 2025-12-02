<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/sign-up', function () {
    return Inertia::render('SignUp');
})->name('sign-up');

Route::post('/register', [SignUpController::class, 'register']);

Route::get('/sign-in', function () {
    return Inertia::render('SignIn');
})->name('sign-in');
    
Route::post('/authenticate', [SignInController::class, 'authenticate']);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

    Route::post('/cart', [CartController::class, 'store']);

    Route::get('/cart', [CartController::class, 'show']);

    Route::post('/cart/{ticket}', [CartController::class, 'update']);

    Route::delete('/cart/{ticket}', [CartController::class, 'destroy']);

    Route::get('/review', [CheckoutController::class, 'show'])->name('review');

    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
 
    Route::get('/checkout/success', [CheckoutController::class, 'showCheckoutSuccess'])->name('checkout-success');

    Route::get('/my-account', [AccountController::class, 'show']);

    Route::get('/order-history', [AccountController::class, 'showOrderHistory']);
});
