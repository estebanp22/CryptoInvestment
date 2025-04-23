<?php

use App\Http\Controllers\CryptocurrencyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/cryptocurrencies', [CryptocurrencyController::class, 'index'])->name('cryptocurrencies.index');
    Route::get('/cryptocurrencies/update', [CryptocurrencyController::class, 'updatePrices'])->name('cryptocurrencies.update');
    Route::resource('cryptocurrencies', CryptocurrencyController::class);
});
