<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserWallet;
use App\Models\Deposit;
use App\Models\SystemLog;

echo "=== CORRECTING DOUBLE-COUNTED BALANCES ===\n\n";

// Get the specific users who have the double-counting issue
$usersToFix = [8, 11]; // Daniel Den and Sarah John

foreach ($usersToFix as $userId) {
    $user = User::find($userId);
    if (!$user) {
        echo "User ID {$userId} not found!\n";
        continue;
    }
    
    echo "Fixing User: {$user->name} (ID: {$user->id})\n";
    echo "Current Main Wallet: $" . number_format($user->wallet_balance ?? 0, 2) . "\n";
    
    // Get approved deposits total
    $totalApprovedDeposits = Deposit::where('user_id', $user->id)
        ->where('status', 'approved')
        ->sum('amount');
    
    echo "Total Approved Deposits: $" . number_format($totalApprovedDeposits, 2) . "\n";
    
    // Get USDT wallet
    $usdtWallet = UserWallet::where('user_id', $user->id)->where('currency', 'USDT')->first();
    if ($usdtWallet) {
        echo "USDT Wallet Balance: " . number_format($usdtWallet->balance, 2) . " USDT\n";
    }
    
    // For new users (registered recently), reset main wallet to 0
    // Since the deposits are already properly recorded in USDT wallet
    $oldBalance = $user->wallet_balance ?? 0;
    
    // Reset main wallet to 0 since deposits should only be in crypto wallets
    $user->wallet_balance = 0;
    $user->save();
    
    echo "✅ Fixed: Main wallet reset from $" . number_format($oldBalance, 2) . " to $0.00\n";
    echo "   Crypto wallets remain unchanged (correct)\n";
    
    // Log the correction
    SystemLog::create([
        'level' => 'info',
        'type' => 'admin',
        'action' => 'balance_correction',
        'message' => "Fixed double-counting for user {$user->name}: Main wallet reset from $" . number_format($oldBalance, 2) . " to $0.00",
        'user_id' => 1, // Admin user
    ]);
    
    echo "---\n\n";
}

echo "=== VERIFICATION ===\n";
echo "After the fix, let's verify the balances:\n\n";

foreach ($usersToFix as $userId) {
    $user = User::find($userId);
    if (!$user) continue;
    
    echo "User: {$user->name}\n";
    echo "Main Wallet: $" . number_format($user->wallet_balance ?? 0, 2) . "\n";
    
    $usdtWallet = UserWallet::where('user_id', $user->id)->where('currency', 'USDT')->first();
    if ($usdtWallet) {
        echo "USDT Wallet: " . number_format($usdtWallet->balance, 2) . " USDT\n";
        echo "Trading Available: " . number_format($usdtWallet->available_balance, 2) . " USDT\n";
    }
    
    // Calculate what each page should now show
    $usdtBalance = $usdtWallet ? $usdtWallet->balance : 0;
    $mainBalance = $user->wallet_balance ?? 0;
    
    echo "Dashboard Overview Card: $" . number_format($usdtBalance + $mainBalance, 2) . "\n";
    echo "Wallet Page Total: $" . number_format($usdtBalance + $mainBalance, 2) . "\n";
    echo "Trading Available: $" . number_format($usdtBalance, 2) . "\n";
    echo "---\n\n";
}

?>
