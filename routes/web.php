<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
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
});
