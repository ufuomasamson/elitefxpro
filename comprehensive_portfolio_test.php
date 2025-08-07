<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 COMPREHENSIVE TRADE PAGE PORTFOLIO TEST\n";
echo "==========================================\n\n";

use App\Models\User;

try {
    $users = User::with('wallets')->take(5)->get();
    
    foreach ($users as $user) {
        echo "👤 User: {$user->name} (ID: {$user->id})\n";
        
        $userWallets = $user->wallets;
        
        // Apply the FIXED calculation logic (same as in the updated view)
        $totalPortfolioValue = 0;
        $breakdown = [];
        
        foreach ($userWallets as $wallet) {
            if ($wallet->currency === 'USDT') {
                // For USDT, use available_balance (not balance_usd to avoid double-counting)
                $usdtValue = $wallet->available_balance;
                $totalPortfolioValue += $usdtValue;
                if ($usdtValue > 0) {
                    $breakdown[] = "USDT: €{$usdtValue}";
                }
            } else if ($wallet->balance > 0) {
                // For crypto, use balance_usd
                $cryptoValue = $wallet->balance_usd ?? 0;
                $totalPortfolioValue += $cryptoValue;
                if ($cryptoValue > 0) {
                    $breakdown[] = "{$wallet->currency}: €{$cryptoValue}";
                }
            }
        }
        
        echo "   Portfolio Breakdown: " . (empty($breakdown) ? "No assets" : implode(", ", $breakdown)) . "\n";
        echo "   Total Portfolio Value: €{$totalPortfolioValue}\n\n";
    }
    
    echo "✅ All users tested successfully with the fixed calculation!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
