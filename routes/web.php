<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserCartController;
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

    Route::post('/cart', [UserCartController::class, 'store']);

    Route::get('/cart', [UserCartController::class, 'show']);

    Route::post('/cart/{ticket}/number-of-tickets', [UserCartController::class, 'updateNumberOfTickets']);
});
