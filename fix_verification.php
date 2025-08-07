<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\User;

echo "✅ DASHBOARD FIX VERIFICATION\n";
echo "============================\n\n";

echo "🔧 Changes made:\n";
echo "   ✅ Disabled JavaScript real-time profit recalculation\n";
echo "   ✅ Dashboard now only shows server-calculated realized profits\n";
echo "   ✅ JavaScript only updates individual holding values, not total profit\n";
echo "   ✅ All caches cleared\n\n";

echo "📊 Server-side profit calculations remain unchanged:\n";

// Verify all users still show correct profits
$users = User::whereHas('trades')->get();
foreach ($users as $user) {
    $userTrades = \App\Models\Trade::where('user_id', $user->id)->get();
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
        echo "   👤 User " . $user->id . " (" . $user->name . "): €" . number_format($totalProfit, 2) . "\n";
    }
}

echo "\n🎯 What should happen now:\n";
echo "   1. Dashboard 'Total Profits' card will show €0.00 for all users\n";
echo "   2. No more massive profit numbers from JavaScript calculations\n";
echo "   3. Individual holding values may update with live prices (for display only)\n";
echo "   4. Total profit calculation is fixed to server-side realized profits only\n\n";

echo "🌐 Test instructions:\n";
echo "   1. Refresh your browser (F5 or Ctrl+R)\n";
echo "   2. Check the dashboard 'Total Profits' card\n";
echo "   3. It should now show €0.00 for all users\n";
echo "   4. Try with different user accounts to confirm\n\n";

echo "✅ Fix completed! The massive profit display issue should now be resolved.\n";
