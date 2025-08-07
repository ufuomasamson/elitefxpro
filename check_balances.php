<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USER BALANCE CHECK ===" . PHP_EOL;

$users = App\Models\User::all(['id', 'name', 'email', 'wallet_balance']);

foreach($users as $user) {
    echo "ID: {$user->id}" . PHP_EOL;
    echo "Email: {$user->email}" . PHP_EOL;
    echo "Balance: " . ($user->wallet_balance ?? 'NULL') . PHP_EOL;
    echo "---" . PHP_EOL;
}

echo "=== CHECKING DONWILLIAM44@GMAIL.COM ===" . PHP_EOL;

$specificUser = App\Models\User::where('email', 'donwilliam44@gmail.com')->first();
if ($specificUser) {
    echo "Found user: {$specificUser->name}" . PHP_EOL;
    echo "Balance: " . ($specificUser->wallet_balance ?? 'NULL') . PHP_EOL;
    
    // Check user wallets
    $wallets = $specificUser->wallets;
    echo "Number of wallets: " . $wallets->count() . PHP_EOL;
    
    foreach($wallets as $wallet) {
        echo "  - {$wallet->currency}: {$wallet->balance}" . PHP_EOL;
    }
    
    // Check total portfolio value
    try {
        $portfolioValue = $specificUser->getTotalPortfolioValue();
        echo "Portfolio Value: {$portfolioValue}" . PHP_EOL;
    } catch (Exception $e) {
        echo "Error calculating portfolio: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "User not found!" . PHP_EOL;
}

echo "=== SYSTEM SETTINGS ===" . PHP_EOL;
$settings = App\Models\SystemSetting::all(['key', 'value']);
foreach($settings as $setting) {
    echo "{$setting->key}: {$setting->value}" . PHP_EOL;
}
