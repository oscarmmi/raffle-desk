<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\RaffleDrawController;
use App\Http\Controllers\RaffleNumberSaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', RaffleController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/owner/draw', RaffleDrawController::class)->name('owner.draw');
    Route::patch('/owner/numbers/{raffleNumber}/sell', RaffleNumberSaleController::class)
        ->name('owner.numbers.sell');
});
