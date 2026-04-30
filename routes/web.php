<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::livewire('/', 'pages::frontend.home')->name('home');
Route::livewire('/products', 'frontend.product-list')->name('products');
Route::livewire('/detail/{slug}', 'pages::frontend.detail')->name('detail');
Route::livewire('/cart', 'pages::frontend.cart')->name('cart');
Route::middleware('auth')->group(function () {
    Route::livewire('/checkout', 'pages::frontend.checkout')->name('checkout');
    Route::livewire('/orders/history', 'frontend.order-history')->name('orders.history');
});
Route::get('/clear', function () {
    Session::forget('cart');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/category.php';
require __DIR__.'/product.php';
