<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Models\Product;
use App\Models\Category;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Pick one random product id per category (collect ids only)
    $selectedIds = [];
    $categoryIds = Category::pluck('id');
    foreach ($categoryIds as $catId) {
        $prodId = Product::where('category_id', $catId)->inRandomOrder()->value('id');
        if ($prodId) {
            $selectedIds[$prodId] = $prodId; // use id as key to avoid duplicates
        }
    }

    $recommendedIds = array_values($selectedIds);

    // If fewer than 5 selected, fill with other random product ids (excluding already selected)
    if (count($recommendedIds) < 5) {
        $needed = 5 - count($recommendedIds);
        $fillIds = Product::whereNotIn('id', $recommendedIds)->inRandomOrder()->take($needed)->pluck('id')->toArray();
        $recommendedIds = array_merge($recommendedIds, $fillIds);
    }

    // If more than 5 (many categories), randomly pick 5 of them
    if (count($recommendedIds) > 5) {
        shuffle($recommendedIds);
        $recommendedIds = array_slice($recommendedIds, 0, 5);
    }

    // Fetch Eloquent models with category relation loaded so we have an Eloquent collection
    $recommended = Product::with('category')->whereIn('id', $recommendedIds)->get();

    return view('dashboard', ['recommendedProducts' => $recommended]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/products/{id}/add-to-cart', [ProductController::class, 'addToCart'])->name('products.addToCart');
    Route::post('/products/{id}/wishlist', [ProductController::class, 'addToWishlist'])->name('products.addToWishlist'); 
    Route::patch('/cart/select-all', [CartController::class, 'updateAllSelection'])->name('cart.selectAll');
    Route::patch('/cart/{id}/select', [CartController::class, 'updateSelection'])->name('cart.select');
    Route::post('/cart/{id}/quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/payment', [CartController::class, 'processPayment'])->name('cart.payment');
    Route::patch('/profile/photo/update', [UserController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::get('/profile/photo/{filename}', [UserController::class, 'showProfilePhoto'])->where('filename', '.*')->name('user.photo');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    // Cart resource routes
    Route::resource('cart', CartController::class);
    
    // Payment flow routes
    Route::get('/cart/payment', [CartController::class, 'processPayment'])->name('cart.showPayment');
    Route::post('/cart/payment/confirm', [CartController::class, 'confirmPayment'])->name('cart.confirmPayment');
});

// Move these outside auth middleware if they should be public
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

require __DIR__.'/auth.php';
