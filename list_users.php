<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserWallet;
use App\Models\Deposit;

echo "=== ALL USERS ===\n";

$users = User::all();
foreach($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Main Balance: $" . number_format($user->wallet_balance ?? 0, 2) . "\n";
    
    // Show USDT wallet if exists
    $usdtWallet = UserWallet::where('user_id', $user->id)->where('currency', 'USDT')->first();
    if ($usdtWallet) {
        echo "  USDT Wallet: " . number_format($usdtWallet->balance, 2) . " USDT\n";
    }
    
    // Show recent deposits
    $recentDeposit = Deposit::where('user_id', $user->id)->latest()->first();
    if ($recentDeposit) {
        echo "  Latest Deposit: $" . number_format($recentDeposit->amount, 2) . " ({$recentDeposit->status}) on {$recentDeposit->created_at}\n";
    }
    echo "\n";
}

?>
