<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ENSURING ALL USERS HAVE EUR WALLETS ===" . PHP_EOL;

$users = App\Models\User::where('wallet_balance', '>', 0)->get();

foreach ($users as $user) {
    echo "Processing user: {$user->email} (Balance: {$user->wallet_balance})" . PHP_EOL;
    
    // Check if user has EUR wallet
    $eurWallet = $user->wallets()->where('currency', 'EUR')->first();
    
    if (!$eurWallet && $user->wallet_balance > 0) {
        // Create EUR wallet for main balance
        $eurWallet = App\Models\UserWallet::create([
            'user_id' => $user->id,
            'currency' => 'EUR',
            'currency_name' => 'Euro',
            'balance' => $user->wallet_balance,
            'locked_balance' => 0,
            'balance_usd' => $user->wallet_balance // Assuming EUR ≈ USD for now
        ]);
        
        echo "  ✓ Created EUR wallet with balance: {$user->wallet_balance}" . PHP_EOL;
        
        // Optionally reset main wallet balance since it's now in EUR wallet
        // $user->wallet_balance = 0;
        // $user->save();
    } else if ($eurWallet) {
        echo "  ✓ EUR wallet already exists with balance: {$eurWallet->balance}" . PHP_EOL;
    } else {
        echo "  - User has no main balance to transfer" . PHP_EOL;
    }
}

echo PHP_EOL . "=== TESTING DONWILLIAM44@GMAIL.COM WALLETS ===" . PHP_EOL;

$user = App\Models\User::where('email', 'donwilliam44@gmail.com')->first();
if ($user) {
    echo "User: {$user->name}" . PHP_EOL;
    echo "Main wallet_balance: {$user->wallet_balance}" . PHP_EOL;
    
    $wallets = $user->wallets;
    echo "Number of crypto wallets: " . $wallets->count() . PHP_EOL;
    
    foreach ($wallets as $wallet) {
        echo "  - {$wallet->currency} ({$wallet->currency_name}): {$wallet->balance}" . PHP_EOL;
    }
    
    $totalPortfolio = $user->getTotalPortfolioValue();
    $grandTotal = $totalPortfolio + $user->wallet_balance;
    echo "Portfolio Value: {$totalPortfolio}" . PHP_EOL;
    echo "Grand Total: {$grandTotal}" . PHP_EOL;
}
