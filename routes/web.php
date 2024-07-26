<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\StockController;
use App\Livewire\Stocks\Details;
use App\Livewire\Stocks\Catalog;
use App\Livewire\Portfolio;

Route::view('/', 'dashboard');

Route::get('/stocks/{stockId}/data', [StockController::class, 'getData']);

Route::get('/portfolio', Portfolio::class)
    ->middleware(['auth', 'verified'])
    ->name('portfolio');

Route::get('/stocks', Catalog::class)
    ->name('stocks'); 

Route::get('/stocks/{stockId}', Details::class)
    ->name('stockdetails');

Route::view('dashboard', 'dashboard')
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

