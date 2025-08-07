<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\User;
use App\Models\Trade;
use App\Models\UserWallet;

echo "🔍 URGENT: NEW ACCOUNT ID 11 INVESTIGATION\n";
echo "==========================================\n\n";

// Check if user ID 11 exists
$user11 = User::find(11);
if (!$user11) {
    echo "❌ User ID 11 not found!\n";
    exit;
}

echo "👤 USER 11 DETAILS:\n";
echo "   Name: " . $user11->name . "\n";
echo "   Email: " . $user11->email . "\n";
echo "   Created: " . $user11->created_at . "\n\n";

// Check trades for user 11
$trades = Trade::where('user_id', 11)->get();
echo "📊 TRADES FOR USER 11:\n";
echo "   Total trades: " . $trades->count() . "\n";

foreach ($trades as $trade) {
    echo "   🔄 Trade ID " . $trade->id . ":\n";
    echo "      Direction: " . $trade->direction . "\n";
    echo "      Crypto: " . $trade->crypto_symbol . "\n";
    echo "      Amount: " . $trade->amount . "\n";
    echo "      Price: €" . number_format($trade->price, 2) . "\n";
    echo "      Total Value: €" . number_format($trade->total_value, 2) . "\n";
    echo "      Fee: €" . number_format($trade->fee, 2) . "\n";
    echo "      Created: " . $trade->created_at . "\n";
    echo "      Status: " . $trade->status . "\n\n";
}

// Check wallets for user 11
$wallets = UserWallet::where('user_id', 11)->get();
echo "💰 WALLETS FOR USER 11:\n";
foreach ($wallets as $wallet) {
    echo "   🏦 " . $wallet->currency . ":\n";
    echo "      Balance: " . $wallet->balance . "\n";
    echo "      Available: " . $wallet->available_balance . "\n";
    echo "      Balance USD: " . ($wallet->balance_usd ?? 'NULL') . "\n";
    echo "      Updated: " . $wallet->updated_at . "\n\n";
}

// SIMULATE EXACT DASHBOARD CALCULATION FOR USER 11
echo "🎯 DASHBOARD CALCULATION SIMULATION FOR USER 11:\n";
echo "================================================\n";

$userTrades = Trade::where('user_id', 11)->get();
$totalInvested = $userTrades->sum('total_value') ?: 0;
$hasActiveTrades = $userTrades->count() > 0;

echo "📈 Has trades: " . ($hasActiveTrades ? 'YES' : 'NO') . "\n";
echo "💰 Total invested (simple): €" . number_format($totalInvested, 2) . "\n";

if ($hasActiveTrades) {
    $totalRealizedProfit = 0;
    $sellTrades = $userTrades->where('direction', 'sell');
    $buyTrades = $userTrades->where('direction', 'buy');
    
    echo "🔴 Sell trades: " . $sellTrades->count() . "\n";
    echo "🟢 Buy trades: " . $buyTrades->count() . "\n";
    
    // Process sell trades
    foreach ($sellTrades as $sellTrade) {
        $cryptoSymbol = $sellTrade->crypto_symbol;
        $sellAmount = $sellTrade->amount;
        $sellValue = $sellTrade->total_value - $sellTrade->fee;
        
        echo "   🔴 SELL: " . $sellAmount . " " . $cryptoSymbol . " for €" . number_format($sellValue, 2) . " (net)\n";
        
        $buysForThisCrypto = $buyTrades->where('crypto_symbol', $cryptoSymbol);
        
        if ($buysForThisCrypto->count() > 0) {
            $totalBoughtAmount = $buysForThisCrypto->sum('amount');
            $totalBoughtCost = $buysForThisCrypto->sum(function($trade) {
                return $trade->total_value + $trade->fee;
            });
            
            if ($totalBoughtAmount > 0) {
                $avgCostPerUnit = $totalBoughtCost / $totalBoughtAmount;
                $costOfSoldUnits = $sellAmount * $avgCostPerUnit;
                $profitFromThisSell = $sellValue - $costOfSoldUnits;
                
                echo "      📊 Avg cost: €" . number_format($avgCostPerUnit, 6) . "\n";
                echo "      📊 Cost of sold: €" . number_format($costOfSoldUnits, 2) . "\n";
                echo "      💵 Profit: €" . number_format($profitFromThisSell, 2) . "\n";
                
                $totalRealizedProfit += $profitFromThisSell;
            }
        }
    }
    
    // Final calculations
    $totalProfit = $totalRealizedProfit;
    $totalInvested = $buyTrades->sum(function($trade) {
        return $trade->total_value + $trade->fee;
    });
    
    if ($totalInvested > 0) {
        $profitPercentage = (($totalProfit / $totalInvested) * 100);
    } else {
        $profitPercentage = 0;
    }
    
    // Get current USDT balance
    $usdtWallet = $user11->wallets()->where('currency', 'USDT')->first();
    $totalCurrentValue = $usdtWallet ? $usdtWallet->balance : 0;
    
    // Validation
    $totalInvested = is_numeric($totalInvested) ? (float)$totalInvested : 0;
    $totalProfit = is_numeric($totalProfit) && is_finite($totalProfit) ? (float)$totalProfit : 0;
    $profitPercentage = is_numeric($profitPercentage) && is_finite($profitPercentage) ? (float)$profitPercentage : 0;
    
    echo "\n🎯 FINAL DASHBOARD VALUES:\n";
    echo "   💰 Total Profit: €" . number_format($totalProfit, 2) . "\n";
    echo "   📈 Profit %: " . number_format($profitPercentage, 2) . "%\n";
    echo "   💵 Investment: €" . number_format($totalInvested, 2) . "\n";
    echo "   🏦 Current Value: €" . number_format($totalCurrentValue, 2) . "\n";
    
    // Check for issues
    if (abs($totalProfit) > 100000) {
        echo "   ⚠️  WARNING: MASSIVE PROFIT DETECTED!\n";
        echo "   🔍 Checking for data corruption...\n";
        
        // Check individual trade values
        foreach ($userTrades as $trade) {
            if ($trade->total_value > 100000 || $trade->amount > 1000000 || $trade->price > 1000000) {
                echo "   ❌ CORRUPTED TRADE FOUND:\n";
                echo "      Trade ID: " . $trade->id . "\n";
                echo "      Total Value: €" . number_format($trade->total_value, 2) . "\n";
                echo "      Amount: " . $trade->amount . "\n";
                echo "      Price: €" . number_format($trade->price, 2) . "\n";
            }
        }
    }
} else {
    echo "   ℹ️  No trades - should show €0.00\n";
}

echo "\n✅ Investigation complete!\n";
