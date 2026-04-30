<?php 

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::livewire('category', 'pages::category.index')->name('category.index');
    Route::livewire('category/create', 'pages::category.create')->name('category.create');
    Route::livewire('category/edit/{categoryid}', 'pages::category.edit')->name('category.edit');
});
