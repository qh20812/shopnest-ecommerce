<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use Inertia\Inertia;
use Laravel\Fortify\Features;



Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Cart routes
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart');
Route::post('/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/apply-coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.applyCoupon');

// Wishlist routes
Route::get('/wish-list', [WishlistController::class, 'index'])->name('wish-list');
Route::post('/wish-list', [WishlistController::class, 'store'])->name('wishlist.store');
Route::delete('/wish-list/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
Route::post('/wish-list/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::post('/wish-list/add-all-to-cart', [WishlistController::class, 'addAllToCart'])->name('wishlist.addAllToCart');

Route::get('/product/{productId}', [DetailController::class, 'show'])->name('product.detail');
Route::post('/product/{productId}/add-to-cart', [DetailController::class, 'addToCart'])->name('product.addToCart');
Route::post('/product/{productId}/buy-now', [DetailController::class, 'buyNow'])->name('product.buyNow');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Shopping routes
Route::get('/shopping', [App\Http\Controllers\ShoppingController::class, 'index'])->name('shopping');

// Banner management endpoints - admin only (apply middleware as needed)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::apiResource('banners', BannerController::class);
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
