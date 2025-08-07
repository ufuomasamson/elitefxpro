<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserWallet;

echo "=== WITHDRAWAL PAGE WALLET DEBUG ===\n\n";

// Test with Sarah John (user ID 11) who we know has balances
$user = User::find(11);

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Email: {$user->email}\n\n";

echo "=== ALL USER WALLETS ===\n";
$allWallets = $user->wallets()->get();

foreach ($allWallets as $wallet) {
    echo "Currency: {$wallet->currency}\n";
    echo "Balance: {$wallet->balance}\n";
    echo "Locked Balance: " . ($wallet->locked_balance ?? 0) . "\n";
    echo "Available Balance: " . $wallet->available_balance . "\n";
    echo "Balance USD: " . ($wallet->balance_usd ?? 0) . "\n";
    echo "Has positive balance: " . ($wallet->balance > 0 ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

echo "\n=== WITHDRAWAL PAGE QUERY SIMULATION ===\n";
// This is the exact query from the withdrawal route
$availableWallets = $user->wallets()
    ->where('balance', '>', 0)
    ->orderBy('balance_usd', 'desc')
    ->get();

echo "Query: wallets()->where('balance', '>', 0)->orderBy('balance_usd', 'desc')->get()\n";
echo "Result count: " . $availableWallets->count() . "\n\n";

if ($availableWallets->count() > 0) {
    echo "Available wallets for withdrawal:\n";
    foreach ($availableWallets as $wallet) {
        echo "• {$wallet->currency}: " . number_format($wallet->balance, 8) . " (USD: " . number_format($wallet->balance_usd, 2) . ")\n";
    }
} else {
    echo "❌ No wallets found with positive balance!\n";
    echo "This explains why the withdrawal page is showing empty wallets.\n";
}

echo "\n=== DEBUGGING BALANCE VALUES ===\n";
foreach ($allWallets as $wallet) {
    echo "Wallet: {$wallet->currency}\n";
    echo "  Raw balance value: '" . $wallet->balance . "'\n";
    echo "  Balance type: " . gettype($wallet->balance) . "\n";
    echo "  Is numeric: " . (is_numeric($wallet->balance) ? 'YES' : 'NO') . "\n";
    echo "  Greater than 0: " . ($wallet->balance > 0 ? 'YES' : 'NO') . "\n";
    echo "  Greater than '0': " . ($wallet->balance > '0' ? 'YES' : 'NO') . "\n";
    echo "  Float value: " . (float)$wallet->balance . "\n";
    echo "---\n";
}

echo "\n=== ALTERNATIVE QUERY TEST ===\n";
// Try different query approaches
$altQuery1 = $user->wallets()
    ->where('balance', '>', 0.0)
    ->get();
echo "Using 0.0 instead of 0: " . $altQuery1->count() . " results\n";

$altQuery2 = $user->wallets()
    ->whereRaw('CAST(balance AS DECIMAL(15,8)) > 0')
    ->get();
echo "Using CAST to DECIMAL: " . $altQuery2->count() . " results\n";

$altQuery3 = $user->wallets()
    ->get()
    ->filter(function ($wallet) {
        return (float)$wallet->balance > 0;
    });
echo "Using collection filter: " . $altQuery3->count() . " results\n";

if ($altQuery3->count() > 0) {
    echo "Filtered wallets:\n";
    foreach ($altQuery3 as $wallet) {
        echo "• {$wallet->currency}: " . number_format($wallet->balance, 8) . "\n";
    }
}

?>
