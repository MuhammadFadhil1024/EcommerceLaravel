<?php 

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::livewire('product', 'pages::product.index')->name('product.index');
    Route::livewire('product/create', 'pages::product.create')->name('product.create');
    Route::livewire('product/edit/{productid}', 'pages::product.edit')->name('product.edit');
    Route::livewire('product/images/{productid}', 'pages::product.image')->name('product.image.index');
});
