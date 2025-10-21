<?php

use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/register', function () {
    return Inertia::render('Register');
});

Route::post('/register', [SignUpController::class, 'register']);

Route::get('/', function () {
    return Inertia::render('Welcome');
});
