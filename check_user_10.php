<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 CHECKING USER 10 DATA\n";
echo "========================\n\n";

use App\Models\User;

try {
    // Check user 10
    $user = User::find(10);
    
    if (!$user) {
        echo "❌ User with ID 10 not found\n";
        
        // Show all users
        echo "\n📋 All users in database:\n";
        $users = User::all();
        foreach ($users as $u) {
            echo "  - ID: {$u->id}, Name: {$u->name}, Email: {$u->email}\n";
        }
        exit(1);
    }
    
    echo "👤 User: {$user->name} (ID: {$user->id})\n";
    echo "📧 Email: {$user->email}\n";
    echo "🕐 Created: {$user->created_at}\n\n";
    
    // Get user's wallets
    $userWallets = $user->wallets()->get();
    echo "💰 User has " . $userWallets->count() . " wallets:\n";
    
    foreach ($userWallets as $wallet) {
        echo "  - {$wallet->currency}:\n";
        echo "    * Balance: {$wallet->balance}\n";
        echo "    * Available: {$wallet->available_balance}\n";
        echo "    * USD Value: {$wallet->balance_usd}\n";
        echo "    * Created: {$wallet->created_at}\n";
    }
    
    // Get USDT balance
    $usdtWallet = $userWallets->where('currency', 'USDT')->first();
    $usdtBalance = $usdtWallet ? $usdtWallet->available_balance : 0;
    echo "\n💵 USDT Balance: €{$usdtBalance}\n";
    
    // Calculate portfolio values
    echo "\n📊 PORTFOLIO CALCULATIONS:\n";
    
    // 1. CORRECT calculation (from our fix)
    $correctTotal = 0;
    foreach ($userWallets as $wallet) {
        if ($wallet->currency === 'USDT') {
            $correctTotal += $wallet->available_balance;
        } else if ($wallet->balance > 0) {
            $correctTotal += $wallet->balance_usd ?? 0;
        }
    }
    echo "✅ CORRECT (Trade page fix): €{$correctTotal}\n";
    
    // 2. PROBLEMATIC calculation (old way)
    $problematicTotal = $usdtBalance + $userWallets->where('balance', '>', 0)->sum('balance_usd');
    echo "❌ PROBLEMATIC (old way): €{$problematicTotal}\n";
    
    // 3. Check if this matches the massive amount you saw
    if ($problematicTotal > 500000000) {
        echo "🚨 FOUND THE SOURCE! This matches the massive amount you saw!\n";
    }
    
    // Get user's trades
    $userTrades = $user->trades()->get();
    echo "\n📈 TRADES ANALYSIS:\n";
    echo "Total trades: " . $userTrades->count() . "\n";
    
    if ($userTrades->count() > 0) {
        echo "Recent trades:\n";
        foreach ($userTrades->take(10) as $trade) {
            echo "  - {$trade->direction} {$trade->amount} {$trade->crypto_symbol} @ €{$trade->price_at_time} = €{$trade->total_value} ({$trade->created_at})\n";
        }
        
        // Check for realized profits calculation
        echo "\n💰 REALIZED PROFITS CALCULATION:\n";
        $realizedProfit = 0;
        $holdings = [];
        
        foreach ($userTrades->sortBy('created_at') as $trade) {
            $symbol = $trade->crypto_symbol;
            
            if ($trade->direction === 'buy') {
                if (!isset($holdings[$symbol])) {
                    $holdings[$symbol] = ['amount' => 0, 'totalCost' => 0];
                }
                
                $holdings[$symbol]['amount'] += $trade->amount;
                $holdings[$symbol]['totalCost'] += $trade->amount * $trade->price_at_time;
            } elseif ($trade->direction === 'sell') {
                if (isset($holdings[$symbol]) && $holdings[$symbol]['amount'] > 0) {
                    $avgCost = $holdings[$symbol]['totalCost'] / $holdings[$symbol]['amount'];
                    $sellAmount = min($trade->amount, $holdings[$symbol]['amount']);
                    
                    $profit = ($trade->price_at_time - $avgCost) * $sellAmount;
                    $realizedProfit += $profit;
                    
                    // Update holdings
                    $costToRemove = $avgCost * $sellAmount;
                    $holdings[$symbol]['amount'] -= $sellAmount;
                    $holdings[$symbol]['totalCost'] -= $costToRemove;
                    
                    echo "  Sell profit: €{$profit} (sold {$sellAmount} at €{$trade->price_at_time} vs avg cost €{$avgCost})\n";
                }
            }
        }
        
        echo "TOTAL REALIZED PROFIT: €{$realizedProfit}\n";
        
        if ($realizedProfit > 500000000) {
            echo "🚨 FOUND IT! The massive profit comes from realized trading profits!\n";
        }
    } else {
        echo "No trades found.\n";
    }
    
    echo "\n🎯 SUMMARY:\n";
    echo "If you're seeing €554149137.73 as 'Total Profits', it's likely coming from:\n";
    echo "1. Realized profit calculation showing unrealistic gains\n";
    echo "2. Portfolio value double-counting\n";
    echo "3. Browser cache showing old values\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
