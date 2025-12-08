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
Route::get('/product/{productId}', [\App\Http\Controllers\DetailController::class, 'show'])->name('product.detail');
Route::post('/product/{productId}/add-to-cart', [\App\Http\Controllers\DetailController::class, 'addToCart'])->name('product.addToCart');
Route::post('/product/{productId}/buy-now', [\App\Http\Controllers\DetailController::class, 'buyNow'])->name('product.buyNow');
Route::get('/checkout', function(){
    return Inertia::render('checkout');
})->name('checkout');
Route::get('/shopping', function(){
    return Inertia::render('shopping');
})->name('shopping');

Route::get('/profile', function(){
    return Inertia::render('customer-profile');
})->name('profile');
Route::get('/orders', function(){
    return Inertia::render('customer-order-history');
})->name('orders');
Route::get('/faq', function(){
    return Inertia::render('f-a-q');
})->name('faq');
Route::get('/chinh-sach-doi-tra', function(){
    return Inertia::render('chinh-sach-doi-tra');
})->name('return-policy');
Route::get('sellerdashboard', function(){
    return Inertia::render('roles/sellers/dashboard');
})->name('seller.dashboard');
Route::get('sellerproduct', function(){
    return Inertia::render('roles/sellers/product-manage/index');
})->name('seller.product');
Route::get('sellerproductcreate', function(){
    return Inertia::render('roles/sellers/product-manage/create');
})->name('seller.product.create');
Route::get('sellerproductupdate', function(){
    return Inertia::render('roles/sellers/product-manage/update');
})->name('seller.product.update');
Route::get('sellerorder', function(){
    return Inertia::render('roles/sellers/order-manage/index');
})->name('seller.order');
Route::get('sellerorderread', function(){
    return Inertia::render('roles/sellers/order-manage/read');
})->name('seller.order.read');
Route::get('sellershopsettings', function(){
    return Inertia::render('roles/sellers/shop-profile/index');
})->name('seller.shop.settings');
Route::get('sellerpromotion', function(){
    return Inertia::render('roles/sellers/promotion-manage/index');
})->name('seller.promotion');
Route::get('sellerpromotion/create', function(){
    return Inertia::render('roles/sellers/promotion-manage/create');
})->name('seller.promotion.create');
Route::get('sellerpromotion/update', function(){
    return Inertia::render('roles/sellers/promotion-manage/update');
})->name('seller.promotion.update');
Route::get('seller/settings/profile', function(){
    return Inertia::render('roles/sellers/settings/profile');
})->name('seller.settings.profile');
Route::get('seller/settings/password', function(){
    return Inertia::render('roles/sellers/settings/password');
})->name('seller.settings.password');
Route::get('seller/settings/security', function(){
    return Inertia::render('roles/sellers/settings/security');
})->name('seller.settings.security');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Banner management endpoints - admin only (apply middleware as needed)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::apiResource('banners', BannerController::class);
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
