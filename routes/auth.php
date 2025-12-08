<?php

use App\Http\Controllers\Auth\LoginUserController;
use App\Http\Controllers\Auth\RegistrationUserController;
use Illuminate\Support\Facades\Route;

// Registration Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationUserController::class, 'create'])
        ->name('register');
    
    Route::post('/register', [RegistrationUserController::class, 'store'])
        ->name('register.store');
});

// Login Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginUserController::class, 'create'])
        ->name('login');
    
    Route::post('/login', [LoginUserController::class, 'store'])
        ->name('login.store');
});

// Logout Route
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginUserController::class, 'destroy'])
        ->name('logout');
});