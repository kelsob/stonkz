<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\StockController;
use App\Livewire\Stocks\Details;
use App\Livewire\Portfolio;

Route::view('/', 'welcome');

Route::get('/stocks/{stockId}/data', [StockController::class, 'getData']);

Route::get('/portfolio', Portfolio::class)
    ->middleware(['auth', 'verified'])
    ->name('portfolio');

Volt::route('/stocks', 'stocks.list')
    ->middleware(['auth', 'verified'])
    ->name('stocks'); 

Route::get('/stocks/{stockId}', Details::class)
    ->middleware(['auth', 'verified'])
    ->name('stockdetails');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

