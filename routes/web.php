<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\OrganizerApplicationController;
use App\Http\Controllers\Admin\SignInController as AdminSignInController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/sign-up', function () {
        return Inertia::render('SignUp');
    })->name('sign-up');

    Route::post('/register', [SignUpController::class, 'register']);

    Route::get('/sign-in', function () {
        return Inertia::render('SignIn');
    })->name('sign-in');
    
    Route::post('/authenticate', [SignInController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);

    Route::post('/cart/{ticket}', [CartController::class, 'store']);

    Route::get('/cart', [CartController::class, 'show']);

    Route::put('/cart/{ticket}', [CartController::class, 'update']);

    Route::delete('/cart/{ticket}', [CartController::class, 'destroy']);

    Route::get('/review', [CheckoutController::class, 'show'])->name('review');

    Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
 
    Route::get('/checkout/success', [CheckoutController::class, 'showCheckoutSuccess'])->name('checkout-success');

    Route::get('/my-account', [AccountController::class, 'show']);

    Route::get('/reset-password', function () {
        return Inertia::render('ResetPassword');
    })->name('reset-password');
    
    Route::post('/reset-password', [AccountController::class, 'resetPassword']);

    Route::get('/order-history', [AccountController::class, 'showOrderHistory']);

    Route::get('/organizer_application', function () {
        return Inertia::render('OrganizerApplication');
    })->name('organizer-application');

    Route::post('/organizer_application', [AccountController::class, 'applyToBeOrganizer']);

    Route::get('/issued_tickets', [AccountController::class, 'showIssuedTickets']);

    Route::get('/issued_tickets/{ticket}', [TicketController::class, 'showIssuedTicket']);

    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
});

Route::middleware('guest:admin')->prefix('admin')->group(function () {
    Route::get('/sign-in', function () {
        return Inertia::render('Admin/SignIn');
    });
    
    Route::post('/authenticate', [AdminSignInController::class, 'authenticate']);
});

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    });

    Route::get('/organizer_applications', [OrganizerApplicationController::class, 'index']);

    Route::get('/organizer_applications/{user_organizer_application}', [OrganizerApplicationController::class, 'show']);

    Route::put('/organizer_applications/{user_organizer_application}', [OrganizerApplicationController::class, 'updateStatus']);
});
