<?php 

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::livewire('discount', 'pages::discount.index')->name('discount.index');
    Route::livewire('discount/create', 'pages::discount.create')->name('discount.create');
});
