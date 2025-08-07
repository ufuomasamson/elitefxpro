<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserWallet;

class UserWalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DISABLED: This seeder creates fake wallet balances for users
        // Only run this seeder in development if you specifically need test data
        
        $this->command->info("UserWalletsSeeder is disabled to prevent fake balance creation.");
        $this->command->info("For production, users should start with zero balances.");
        
        return;
        
        /* COMMENTED OUT TO PREVENT FAKE DATA CREATION
        $users = User::all();
        
        foreach ($users as $user) {
            if ($user->wallets()->count() == 0) {
                $cryptos = [
                    'BTC' => 'Bitcoin',
                    'ETH' => 'Ethereum', 
                    'USDT' => 'Tether',
                    'ADA' => 'Cardano',
                    'DOT' => 'Polkadot'
                ];
                
                foreach ($cryptos as $symbol => $name) {
                    UserWallet::create([
                        'user_id' => $user->id,
                        'currency' => $symbol,
                        'currency_name' => $name,
                        'balance' => rand(0, 500) / 100, // Random balance 0-5
                        'locked_balance' => 0,
                        'balance_usd' => 0
                    ]);
                }
                
                $this->command->info("Created wallets for user: {$user->email}");
            }
        }
        */
    }
}
