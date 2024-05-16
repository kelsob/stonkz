<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\StockController;

Route::view('/', 'welcome');

Route::get('/stocks/{stockId}/data', [StockController::class, 'getData']);

Volt::route('/stocks', 'stocks.list')
    ->middleware(['auth', 'verified'])
    ->name('stocks'); 

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

