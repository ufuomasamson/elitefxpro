<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DEBUGGING TRADE PAGE 'TOTAL PROFITS' ISSUE\n";
echo "==============================================\n\n";

use Illuminate\Support\Facades\Auth;
use App\Models\User;

try {
    // Test with user ID 9 (Faith Marco who just placed a trade)
    $user = User::find(9);
    
    if (!$user) {
        echo "❌ User with ID 9 not found\n";
        exit(1);
    }
    
    echo "👤 Testing with user: {$user->name} (ID: {$user->id})\n\n";
    
    // Simulate the exact trade route logic
    $userWallets = $user->wallets()->get();
    $usdtWallet = $userWallets->where('currency', 'USDT')->first();
    $usdtBalance = $usdtWallet ? $usdtWallet->available_balance : 0;
    
    echo "💰 User Wallets:\n";
    foreach ($userWallets as $wallet) {
        echo "  - {$wallet->currency}: Balance={$wallet->balance}, Available={$wallet->available_balance}, USD Value={$wallet->balance_usd}\n";
    }
    echo "\n💵 USDT Balance: €{$usdtBalance}\n\n";
    
    // Check what the trade page SHOULD show for Total Portfolio Value
    $totalPortfolioValue = 0;
    foreach ($userWallets as $wallet) {
        if ($wallet->currency === 'USDT') {
            $totalPortfolioValue += $wallet->available_balance;
        } else if ($wallet->balance > 0) {
            $totalPortfolioValue += $wallet->balance_usd ?? 0;
        }
    }
    
    echo "📊 Trade Page Portfolio Value (CORRECTED): €{$totalPortfolioValue}\n\n";
    
    // Check what the old PROBLEMATIC calculation would show
    $problematicTotal = $usdtBalance + $userWallets->where('balance', '>', 0)->sum('balance_usd');
    echo "❌ Old Problematic Calculation Would Show: €{$problematicTotal}\n\n";
    
    // Check if there's any profit calculation happening
    echo "🔍 CHECKING FOR PROFIT CALCULATIONS:\n";
    
    // Get user's recent trades
    $userTrades = $user->trades()->latest()->take(10)->get();
    echo "📈 User has " . $userTrades->count() . " trades\n";
    
    if ($userTrades->count() > 0) {
        echo "Recent trades:\n";
        foreach ($userTrades as $trade) {
            echo "  - {$trade->direction} {$trade->amount} {$trade->crypto_symbol} @ €{$trade->price_at_time} = €{$trade->total_value}\n";
        }
    }
    
    // Check if user is actually looking at a different page
    echo "\n🤔 POSSIBLE CAUSES:\n";
    echo "1. Browser cache - Try hard refresh (Ctrl+Shift+R)\n";
    echo "2. Looking at Dashboard instead of Trade page\n";
    echo "3. JavaScript calculating values dynamically\n";
    echo "4. Included component with old calculation\n\n";
    
    echo "✅ TRADE PAGE SHOULD SHOW:\n";
    echo "   Total Portfolio Value: €{$totalPortfolioValue}\n";
    echo "   Today's P&L: +€0.00 (hardcoded)\n";
    echo "   If you're seeing €554149137.73, it's NOT from the trade page view!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
