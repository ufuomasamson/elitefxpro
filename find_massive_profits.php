<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 FINDING THE €554,149,137.73 'TOTAL PROFITS' SOURCE\n";
echo "====================================================\n\n";

use App\Models\User;

try {
    // Check all users to find who has massive profits
    $users = User::with(['trades', 'wallets'])->get();
    
    foreach ($users as $user) {
        echo "👤 Checking User: {$user->name} (ID: {$user->id})\n";
        
        $userTrades = $user->trades;
        
        if ($userTrades->count() > 0) {
            // Apply the EXACT dashboard calculation logic
            $totalRealizedProfit = 0;
            
            $sellTrades = $userTrades->where('direction', 'sell');
            $buyTrades = $userTrades->where('direction', 'buy');
            
            echo "  📈 Trades: {$buyTrades->count()} buys, {$sellTrades->count()} sells\n";
            
            // For each sell trade, calculate profit
            foreach ($sellTrades as $sellTrade) {
                $cryptoSymbol = $sellTrade->crypto_symbol;
                $sellAmount = $sellTrade->amount;
                $sellValue = $sellTrade->total_value - $sellTrade->fee;
                
                echo "    🔴 SELL: {$sellAmount} {$cryptoSymbol} for €{$sellValue} (total:{$sellTrade->total_value}, fee:{$sellTrade->fee})\n";
                
                $buysForThisCrypto = $buyTrades->where('crypto_symbol', $cryptoSymbol);
                
                if ($buysForThisCrypto->count() > 0) {
                    $totalBoughtAmount = $buysForThisCrypto->sum('amount');
                    $totalBoughtCost = $buysForThisCrypto->sum(function($trade) {
                        return $trade->total_value + $trade->fee;
                    });
                    
                    echo "      📊 Buy data: {$totalBoughtAmount} {$cryptoSymbol} for €{$totalBoughtCost}\n";
                    
                    if ($totalBoughtAmount > 0) {
                        $avgCostPerUnit = $totalBoughtCost / $totalBoughtAmount;
                        $costOfSoldUnits = $sellAmount * $avgCostPerUnit;
                        $profitFromThisSell = $sellValue - $costOfSoldUnits;
                        
                        echo "      💰 Avg cost: €{$avgCostPerUnit}, Cost of sold: €{$costOfSoldUnits}, Profit: €{$profitFromThisSell}\n";
                        
                        $totalRealizedProfit += $profitFromThisSell;
                        
                        if (abs($profitFromThisSell) > 100000000) {
                            echo "      🚨 MASSIVE PROFIT FOUND HERE! €{$profitFromThisSell}\n";
                            echo "         Sell details: Amount={$sellTrade->amount}, Price={$sellTrade->price_at_time}, Total={$sellTrade->total_value}\n";
                            echo "         Buy details: TotalAmount={$totalBoughtAmount}, TotalCost={$totalBoughtCost}, AvgCost={$avgCostPerUnit}\n";
                        }
                    }
                }
            }
            
            echo "  💸 TOTAL REALIZED PROFIT: €{$totalRealizedProfit}\n";
            
            if (abs($totalRealizedProfit) > 500000000) {
                echo "  🚨🚨🚨 FOUND THE SOURCE! This user has the massive profit amount!\n";
                
                // Show all trades for this user
                echo "\n  📋 ALL TRADES FOR THIS USER:\n";
                foreach ($userTrades->sortBy('created_at') as $trade) {
                    echo "    {$trade->created_at}: {$trade->direction} {$trade->amount} {$trade->crypto_symbol} @ €{$trade->price_at_time} = €{$trade->total_value} (fee: €{$trade->fee})\n";
                }
            }
        } else {
            echo "  No trades\n";
        }
        
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
