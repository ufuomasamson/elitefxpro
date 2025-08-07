<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\SystemLog;

class SetupUserWallets extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'users:setup-wallets';

    /**
     * The console command description.
     */
    protected $description = 'Setup USDT wallets for existing users who don\'t have them (with 0 balance - admin funding required)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up USDT wallets for users (0 balance - admin funding required)...');
        
        // Get users without USDT wallets
        $usersWithoutUSDT = User::whereDoesntHave('wallets', function ($query) {
            $query->where('currency', 'USDT');
        })->get();
        
        $this->info("Found {$usersWithoutUSDT->count()} users without USDT wallets");
        
        $progressBar = $this->output->createProgressBar($usersWithoutUSDT->count());
        $progressBar->start();
        
        foreach ($usersWithoutUSDT as $user) {
            // Create USDT wallet with 0 balance - admin will fund manually
            UserWallet::create([
                'user_id' => $user->id,
                'currency' => 'USDT',
                'currency_name' => 'Tether USD',
                'balance' => 0, // Start with 0 - admin funding required
                'locked_balance' => 0,
                'balance_usd' => 0
            ]);
            
            // Log the action
            SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'usdt_wallet_setup',
                'message' => "USDT wallet created for user {$user->name} - awaiting admin funding",
                'user_id' => $user->id,
            ]);
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine();
        $this->info("Successfully created USDT wallets for {$usersWithoutUSDT->count()} users - all awaiting admin funding");
        
        return 0;
    }
}
