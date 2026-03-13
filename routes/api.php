<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

Route::post('/deposit', [WalletController::class, 'deposit']);
Route::post('/withdraw', [WalletController::class, 'withdraw']);
Route::post('/fee', [WalletController::class, 'fee']);
