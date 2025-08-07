<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserWallet;
use App\Models\Deposit;

echo "=== BALANCE DEBUG REPORT ===\n\n";

// Get the main user (assuming user ID 1)
$user = User::find(1);

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Main Wallet Balance: $" . number_format($user->wallet_balance ?? 0, 2) . "\n\n";

// Get all user wallets
$wallets = UserWallet::where('user_id', $user->id)->get();

echo "=== CRYPTO WALLETS ===\n";
$totalCryptoValue = 0;
foreach ($wallets as $wallet) {
    $balance = $wallet->balance ?? 0;
    $balanceUsd = $wallet->balance_usd ?? $balance;
    $totalCryptoValue += $balanceUsd;
    
    echo "{$wallet->currency}: {$balance} (USD: $" . number_format($balanceUsd, 2) . ")\n";
}

echo "\nTotal Crypto Value: $" . number_format($totalCryptoValue, 2) . "\n";
echo "Combined Balance (Main + Crypto): $" . number_format(($user->wallet_balance ?? 0) + $totalCryptoValue, 2) . "\n\n";

// Check recent deposits
echo "=== RECENT DEPOSITS ===\n";
$deposits = Deposit::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

foreach ($deposits as $deposit) {
    echo "ID: {$deposit->id}, Amount: $" . number_format($deposit->amount, 2) . ", Status: {$deposit->status}, Symbol: {$deposit->crypto_symbol}, Created: {$deposit->created_at}\n";
}

// Calculate what each page should show
echo "\n=== EXPECTED VALUES ===\n";

try {
    // Dashboard calculation (from routes/web.php)
    $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
    $usdtBalance = $usdtWallet ? $usdtWallet->balance : 0;
    $mainWalletBalance = $user->wallet_balance ?? 0;
    $totalCurrentValue = $usdtBalance + $mainWalletBalance;
    
    echo "Dashboard Overview Card: $" . number_format($totalCurrentValue, 2) . " (Main: $" . number_format($mainWalletBalance, 2) . " + USDT: $" . number_format($usdtBalance, 2) . ")\n";
    
    // Wallet page calculation
    $portfolioValue = $totalCryptoValue; // This should be getTotalPortfolioValue() but simplified here
    $walletPageTotal = $portfolioValue + $mainWalletBalance;
    
    echo "Wallet Page Total: $" . number_format($walletPageTotal, 2) . " (Portfolio: $" . number_format($portfolioValue, 2) . " + Main: $" . number_format($mainWalletBalance, 2) . ")\n";
    
    // Trading page USDT available
    $tradingAvailable = $usdtWallet ? $usdtWallet->balance : 0;
    echo "Trading Available (USDT): $" . number_format($tradingAvailable, 2) . "\n";
    
} catch (Exception $e) {
    echo "Error calculating expected values: " . $e->getMessage() . "\n";
}

echo "\n=== PROBLEM ANALYSIS ===\n";
echo "If there's a discrepancy, it's likely because:\n";
echo "1. Deposit approval is adding to BOTH main wallet AND crypto wallet\n";
echo "2. Different pages are counting balances differently\n";
echo "3. Portfolio calculation may be double-counting\n";

?>
