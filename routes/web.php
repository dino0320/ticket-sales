<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\OrganizerApplicationController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/sign-up', function () {
        return Inertia::render('SignUp');
    })->name('sign-up');

    Route::post('/register', [AccountController::class, 'register']);

    Route::get('/sign-in', function () {
        return Inertia::render('SignIn');
    })->name('sign-in');
    
    Route::post('/authenticate', [AccountController::class, 'authenticate']);
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

    Route::get('/user-tickets/{ticket}', [TicketController::class, 'showUserTicket']);

    Route::get('/user-tickets/{user_ticket}/use', [TicketController::class, 'useTicket'])->name('user-tickets.use');

    Route::get('/reset-password', function () {
        return Inertia::render('ResetPassword');
    })->name('reset-password');
    
    Route::post('/reset-password', [AccountController::class, 'resetPassword']);

    Route::get('/order-history', [AccountController::class, 'showOrderHistory']);

    Route::get('/organizer-application', function () {
        return Inertia::render('OrganizerApplication');
    })->name('organizer-application');

    Route::post('/organizer-application', [AccountController::class, 'applyToBeOrganizer']);

    Route::get('/issued-tickets', [AccountController::class, 'showIssuedTickets']);

    Route::get('/issue-ticket', function () {
        return Inertia::render('IssueTicket');
    })->name('issue-ticket');

    Route::post('/tickets', [TicketController::class, 'store']);

    Route::get('/issued-tickets/{ticket}', [TicketController::class, 'showIssuedTicket']);

    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
});

Route::middleware('guest:admin')->prefix('admin')->group(function () {
    Route::get('/sign-in', function () {
        return Inertia::render('Admin/SignIn');
    });
    
    Route::post('/authenticate', [AdminAccountController::class, 'authenticate']);
});

Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    });

    Route::get('/organizer-applications', [OrganizerApplicationController::class, 'index']);

    Route::get('/organizer-applications/{user_organizer_application}', [OrganizerApplicationController::class, 'show']);

    Route::put('/organizer-applications/{user_organizer_application}', [OrganizerApplicationController::class, 'updateStatus']);
});
