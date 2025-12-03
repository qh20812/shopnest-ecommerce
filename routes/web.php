<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BannerController;
use Inertia\Inertia;
use Laravel\Fortify\Features;



Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/search', function(){
    return Inertia::render('search-result');
})->name('search');
Route::get('/cart', function(){
    return Inertia::render('cart');
})->name('cart');
Route::get('/wish-list', function(){
    return Inertia::render('wish-list');
})->name('wish-list');
Route::get('/detail', function(){
    return Inertia::render('detail');
})->name('detail');
Route::get('/checkout', function(){
    return Inertia::render('checkout');
})->name('checkout');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Banner management endpoints - admin only (apply middleware as needed)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::apiResource('banners', BannerController::class);
});

require __DIR__.'/settings.php';
