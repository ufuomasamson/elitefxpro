<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\User;

$user = User::find(11);
echo "Main Wallet: $" . ($user->wallet_balance ?? 0) . "\n";

$usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
echo "USDT Wallet: " . ($usdtWallet ? $usdtWallet->balance : 0) . "\n";

$total = ($user->wallet_balance ?? 0) + ($usdtWallet ? $usdtWallet->balance : 0);
echo "Total Dashboard Value: $" . $total . "\n";
