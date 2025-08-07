<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'wallet_balance' => 0,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create user wallets for admin
        UserWallet::create([
            'user_id' => $adminUser->id,
            'currency' => 'BTC',
            'currency_name' => 'Bitcoin',
            'balance' => 1.0,
            'balance_usd' => 43000,
        ]);

        UserWallet::create([
            'user_id' => $adminUser->id,
            'currency' => 'ETH',
            'currency_name' => 'Ethereum',
            'balance' => 10.0,
            'balance_usd' => 23000,
        ]);

        UserWallet::create([
            'user_id' => $adminUser->id,
            'currency' => 'USDT',
            'currency_name' => 'Tether',
            'balance' => 50000.0,
            'balance_usd' => 50000,
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'wallet_balance' => 0,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create user wallets for regular user
        UserWallet::create([
            'user_id' => $user->id,
            'currency' => 'BTC',
            'currency_name' => 'Bitcoin',
            'balance' => 0.5,
            'balance_usd' => 21500,
        ]);

        UserWallet::create([
            'user_id' => $user->id,
            'currency' => 'ETH',
            'currency_name' => 'Ethereum',
            'balance' => 5.0,
            'balance_usd' => 11500,
        ]);

        UserWallet::create([
            'user_id' => $user->id,
            'currency' => 'USDT',
            'currency_name' => 'Tether',
            'balance' => 10000.0,
            'balance_usd' => 10000,
        ]);
    }
}
