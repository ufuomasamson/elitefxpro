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

echo "🔍 CHECKING ALL TRADES FOR MASSIVE VALUES\n";
echo "=========================================\n\n";

// Check for any trades with massive values across all users
$massiveTrades = Trade::where(function($query) {
    $query->where('total_value', '>', 100000)
          ->orWhere('amount', '>', 1000000)  
          ->orWhere('price_at_time', '>', 1000000);
})->get();

echo "🚨 TRADES WITH MASSIVE VALUES:\n";
if ($massiveTrades->count() > 0) {
    foreach ($massiveTrades as $trade) {
        echo "   ❌ TRADE ID " . $trade->id . " (User " . $trade->user_id . "):\n";
        echo "      Direction: " . $trade->direction . "\n";
        echo "      Crypto: " . $trade->crypto_symbol . "\n";
        echo "      Amount: " . number_format($trade->amount, 8) . "\n";
        echo "      Price: €" . number_format($trade->price_at_time, 2) . "\n";
        echo "      Total Value: €" . number_format($trade->total_value, 2) . "\n";
        echo "      Fee: €" . number_format($trade->fee, 2) . "\n";
        echo "      Created: " . $trade->created_at . "\n\n";
    }
} else {
    echo "   ✅ No trades with massive values found\n\n";
}

// Check for wallets with massive USD values
$massiveWallets = UserWallet::where('balance_usd', '>', 100000)->get();

echo "🚨 WALLETS WITH MASSIVE USD VALUES:\n";
if ($massiveWallets->count() > 0) {
    foreach ($massiveWallets as $wallet) {
        echo "   ❌ WALLET (User " . $wallet->user_id . "):\n";
        echo "      Currency: " . $wallet->currency . "\n";
        echo "      Balance: " . number_format($wallet->balance, 8) . "\n";
        echo "      Available: " . number_format($wallet->available_balance, 8) . "\n";
        echo "      Balance USD: €" . number_format($wallet->balance_usd, 2) . "\n";
        echo "      Updated: " . $wallet->updated_at . "\n\n";
    }
} else {
    echo "   ✅ No wallets with massive USD values found\n\n";
}

// Check if there's something in the format_currency function or database values
echo "🔍 CHECKING RECENT TRADE EXECUTION...\n";

// Get the most recent trade for user 11
$recentTrade = Trade::where('user_id', 11)->latest()->first();
if ($recentTrade) {
    echo "📊 Most recent trade for User 11:\n";
    echo "   Raw total_value from DB: " . var_export($recentTrade->total_value, true) . "\n";
    echo "   Raw amount from DB: " . var_export($recentTrade->amount, true) . "\n";
    echo "   Raw price_at_time from DB: " . var_export($recentTrade->price_at_time, true) . "\n";
    echo "   Raw fee from DB: " . var_export($recentTrade->fee, true) . "\n";
    
    // Check if these are causing calculation issues
    $testCalc = $recentTrade->amount * $recentTrade->price_at_time;
    echo "   Test calculation (amount * price): " . var_export($testCalc, true) . "\n";
}

echo "\n🔍 CHECKING ALL USERS' CALCULATED PROFITS:\n";
$allUsers = User::whereHas('trades')->get();

foreach ($allUsers as $user) {
    $userTrades = Trade::where('user_id', $user->id)->get();
    $hasActiveTrades = $userTrades->count() > 0;
    
    if ($hasActiveTrades) {
        $totalRealizedProfit = 0;
        $sellTrades = $userTrades->where('direction', 'sell');
        $buyTrades = $userTrades->where('direction', 'buy');
        
        foreach ($sellTrades as $sellTrade) {
            $cryptoSymbol = $sellTrade->crypto_symbol;
            $sellAmount = $sellTrade->amount;
            $sellValue = $sellTrade->total_value - $sellTrade->fee;
            
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
                    
                    $totalRealizedProfit += $profitFromThisSell;
                }
            }
        }
        
        $totalProfit = is_numeric($totalRealizedProfit) && is_finite($totalRealizedProfit) ? (float)$totalRealizedProfit : 0;
        
        echo "👤 User " . $user->id . " (" . $user->name . "): €" . number_format($totalProfit, 2) . "\n";
        
        if (abs($totalProfit) > 100000) {
            echo "   ⚠️  MASSIVE PROFIT DETECTED FOR USER " . $user->id . "!\n";
        }
    }
}

echo "\n✅ Analysis complete!\n";
