<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 INVESTIGATING TRADE PAGE PORTFOLIO VALUE ISSUE\n";
echo "=================================================\n\n";

use App\Models\User;

try {
    // Get the user with ID 9
    $user = User::find(9);
    
    if (!$user) {
        echo "❌ User with ID 9 not found\n";
        exit(1);
    }
    
    echo "👤 User: {$user->name} (ID: {$user->id})\n";
    echo "📧 Email: {$user->email}\n\n";
    
    // Get user's wallets (same way as trade route)
    $userWallets = $user->wallets()->get();
    echo "💰 User has " . $userWallets->count() . " wallets:\n";
    
    foreach ($userWallets as $wallet) {
        echo "  - {$wallet->currency}: Balance={$wallet->balance}, Available={$wallet->available_balance}, USD Value={$wallet->balance_usd}\n";
    }
    
    // Get USDT balance (same way as trade route)
    $usdtWallet = $userWallets->where('currency', 'USDT')->first();
    $usdtBalance = $usdtWallet ? $usdtWallet->available_balance : 0;
    
    echo "\n💵 USDT Balance (from available_balance): €{$usdtBalance}\n";
    
    // Calculate the PROBLEMATIC way (current trade page calculation)
    $problematicTotal = $usdtBalance + $userWallets->where('balance', '>', 0)->sum('balance_usd');
    echo "\n❌ PROBLEMATIC Trade Page Calculation:\n";
    echo "   USDT Balance: €{$usdtBalance}\n";
    echo "   + Sum of balance_usd: €" . $userWallets->where('balance', '>', 0)->sum('balance_usd') . "\n";
    echo "   = TOTAL: €{$problematicTotal}\n";
    
    // Calculate the CORRECT way (dashboard/wallet page calculation)
    $correctTotal = 0;
    $usdtFromWallet = 0;
    $cryptoTotal = 0;
    
    foreach ($userWallets as $wallet) {
        if ($wallet->currency === 'USDT') {
            $usdtFromWallet = $wallet->available_balance;
            $correctTotal += $usdtFromWallet;
        } else if ($wallet->balance > 0) {
            $cryptoValue = $wallet->balance_usd ?? 0;
            $cryptoTotal += $cryptoValue;
            $correctTotal += $cryptoValue;
        }
    }
    
    echo "\n✅ CORRECT Dashboard/Wallet Calculation:\n";
    echo "   USDT (available_balance): €{$usdtFromWallet}\n";
    echo "   + Crypto values: €{$cryptoTotal}\n";
    echo "   = TOTAL: €{$correctTotal}\n";
    
    // Show the difference
    $difference = $problematicTotal - $correctTotal;
    echo "\n🔍 ANALYSIS:\n";
    echo "   Trade Page shows: €{$problematicTotal}\n";
    echo "   Dashboard shows: €{$correctTotal}\n";
    echo "   Difference: €{$difference}\n";
    
    if ($difference > 0) {
        echo "\n🚨 ISSUE IDENTIFIED: Trade page is double-counting USDT!\n";
        echo "   The trade page adds both:\n";
        echo "   1. \$usdtBalance (from available_balance): €{$usdtBalance}\n";
        echo "   2. USDT wallet balance_usd: €" . ($usdtWallet ? $usdtWallet->balance_usd : 0) . "\n";
        echo "   This creates a double-count of €" . min($usdtBalance, ($usdtWallet ? $usdtWallet->balance_usd : 0)) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
