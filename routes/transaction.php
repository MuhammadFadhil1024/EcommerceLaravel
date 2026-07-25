<?php 

use Illuminate\Support\Facades\Route;
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::livewire('transaction', 'pages::transaction.index')->name('transaction.index');
});
