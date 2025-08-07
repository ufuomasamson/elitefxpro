<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\RealTimeProfitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for real-time data (these get /api/ prefix automatically)
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/crypto-prices', [WalletController::class, 'getCryptoPrices']);
    Route::get('/portfolio-value', [WalletController::class, 'getPortfolioValue']);
    Route::get('/recent-transactions', [DashboardController::class, 'getRecentTransactions']);
    Route::get('/realtime-profit', [RealTimeProfitController::class, 'getRealTimeProfit'])->name('api.realtime-profit');
});