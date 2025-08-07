<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\UserWallet;
use App\Models\Deposit;

echo "=== FIXING DOUBLE-COUNTED BALANCES ===\n\n";

// Find users who have approved deposits and might have double-counted balances
$problematicUsers = User::whereHas('deposits', function($query) {
    $query->where('status', 'approved');
})->get();

foreach ($problematicUsers as $user) {
    echo "Checking User: {$user->name} (ID: {$user->id})\n";
    echo "Current Main Wallet: $" . number_format($user->wallet_balance ?? 0, 2) . "\n";
    
    // Get approved deposits
    $approvedDeposits = Deposit::where('user_id', $user->id)
        ->where('status', 'approved')
        ->get();
    
    $totalApprovedDeposits = $approvedDeposits->sum('amount');
    echo "Total Approved Deposits: $" . number_format($totalApprovedDeposits, 2) . "\n";
    
    // Get USDT wallet
    $usdtWallet = UserWallet::where('user_id', $user->id)->where('currency', 'USDT')->first();
    if ($usdtWallet) {
        echo "USDT Wallet Balance: " . number_format($usdtWallet->balance, 2) . " USDT\n";
        
        // Check if main wallet might be double-counting deposits
        // If main wallet balance equals or is close to approved deposits amount, it's likely double-counted
        $mainBalance = $user->wallet_balance ?? 0;
        
        // Simple heuristic: if main wallet balance is suspicious (equal to deposits or very close)
        if ($mainBalance > 0 && abs($mainBalance - $totalApprovedDeposits) < 100) {
            echo "*** SUSPICIOUS: Main wallet balance might include deposits that are already in USDT wallet\n";
            echo "*** Recommendation: Reset main wallet to 0 or original amount before deposits\n";
            
            // Find user's deposits to understand original balance
            $firstDeposit = $approvedDeposits->sortBy('created_at')->first();
            if ($firstDeposit) {
                echo "*** First approved deposit was on: {$firstDeposit->created_at}\n";
            }
        }
    }
    
    echo "---\n\n";
}

echo "=== CORRECTION RECOMMENDATION ===\n";
echo "For users with double-counted balances:\n";
echo "1. Keep the crypto wallet (USDT) balance as is - this is correct\n";
echo "2. Reset main wallet balance to original amount (usually 0 for new users)\n";
echo "3. Or subtract the approved deposit amounts from main wallet\n\n";

echo "To auto-fix, we need to know what the original main wallet balance should be.\n";
echo "For most users, this would be 0 unless they were manually funded separately.\n";

?>
