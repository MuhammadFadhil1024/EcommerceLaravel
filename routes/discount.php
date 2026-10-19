<?php 

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::livewire('discount', 'pages::discount.index')->name('discount.index');
    Route::livewire('discount/create', 'pages::discount.create')->name('discount.create');
    Route::livewire('discount/create/{discountId}', 'pages::discount.create')->name('discount.edit');
});
