<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Trade;

class TestTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found!');
            return;
        }
        
        // Create test deposits
        if ($user->deposits()->count() == 0) {
            for ($i = 0; $i < 3; $i++) {
                Deposit::create([
                    'user_id' => $user->id,
                    'amount' => rand(100, 1000),
                    'crypto_symbol' => ['BTC', 'ETH', 'USDT'][rand(0, 2)],
                    'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
                    'transaction_id' => 'TXN' . uniqid(),
                    'method' => 'crypto',
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }
        
        // Create test withdrawals
        if ($user->withdrawals()->count() == 0) {
            for ($i = 0; $i < 2; $i++) {
                Withdrawal::create([
                    'user_id' => $user->id,
                    'amount' => rand(50, 500),
                    'withdrawal_address' => 'test_address_' . uniqid(),
                    'status' => ['pending', 'approved', 'rejected'][rand(0, 2)],
                    'reference' => 'WD-' . uniqid(),
                    'created_at' => now()->subDays(rand(1, 15))
                ]);
            }
        }
        
        // Create test trades
        if ($user->trades()->count() == 0) {
            for ($i = 0; $i < 5; $i++) {
                Trade::create([
                    'user_id' => $user->id,
                    'crypto_symbol' => ['BTC', 'ETH', 'ADA', 'DOT'][rand(0, 3)],
                    'amount' => rand(1, 10) / 10,
                    'direction' => ['buy', 'sell'][rand(0, 1)],
                    'price_at_time' => rand(20000, 70000),
                    'total_value' => rand(500, 5000),
                    'status' => 'completed',
                    'fee' => rand(1, 50),
                    'executed_at' => now()->subDays(rand(1, 10))
                ]);
            }
        }
        
        $this->command->info('Created test transactions for user: ' . $user->email);
    }
}
