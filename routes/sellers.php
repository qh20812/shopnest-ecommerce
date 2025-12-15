<?php

use App\Http\Controllers\Sellers\DashboardController;
use App\Http\Controllers\Sellers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| Routes for seller dashboard and management
|
*/

Route::middleware(['auth', 'verified', 'is.seller'])->prefix('seller')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('seller.dashboard');
    
    // Product management routes
    Route::resource('products', ProductController::class)->names([
        'index' => 'seller.products.index',
        'create' => 'seller.products.create',
        'store' => 'seller.products.store',
        'show' => 'seller.products.show',
        'edit' => 'seller.products.edit',
        'update' => 'seller.products.update',
        'destroy' => 'seller.products.destroy',
    ]);
});