<?php

use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/sign-up', function () {
    return Inertia::render('SignUp');
});

Route::post('/register', [SignUpController::class, 'register']);

Route::get('/sign-in', function () {
    return Inertia::render('SignIn');
});
    
Route::post('/authenticate', [SignInController::class, 'authenticate']);

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Welcome');
    });
});
