<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\SystemLog;

class FundUserAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fund 
                            {email : The email of the user to fund}
                            {amount : The amount of USDT to add to their account}
                            {--note= : Optional note for the funding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fund a user account with USDT for trading';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $amount = floatval($this->argument('amount'));
        $note = $this->option('note') ?? 'Admin funding';

        if ($amount <= 0) {
            $this->error('Amount must be greater than 0');
            return Command::FAILURE;
        }

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email '{$email}' not found");
            return Command::FAILURE;
        }

        $this->info("Found user: {$user->name} ({$user->email})");

        // Get or create USDT wallet
        $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
        if (!$usdtWallet) {
            $usdtWallet = UserWallet::create([
                'user_id' => $user->id,
                'currency' => 'USDT',
                'currency_name' => 'Tether USD',
                'balance' => 0,
                'locked_balance' => 0,
                'balance_usd' => 0
            ]);
            $this->info('Created new USDT wallet for user');
        }

        $oldBalance = $usdtWallet->balance;
        $newBalance = $oldBalance + $amount;

        // Update wallet balance
        $usdtWallet->update([
            'balance' => $newBalance,
            'balance_usd' => $newBalance
        ]);

        // Log the funding
        SystemLog::create([
            'level' => 'info',
            'type' => 'admin',
            'action' => 'user_funding',
            'message' => "Admin funded user {$user->name} with {$amount} USDT. Balance: {$oldBalance} -> {$newBalance}. Note: {$note}",
            'user_id' => $user->id,
            'data' => [
                'old_balance' => $oldBalance,
                'amount_added' => $amount,
                'new_balance' => $newBalance,
                'note' => $note,
                'funded_at' => now()->toDateTimeString()
            ]
        ]);

        $this->info("✅ Successfully funded user account!");
        $this->table(
            ['Field', 'Value'],
            [
                ['User', $user->name],
                ['Email', $user->email],
                ['Amount Added', number_format($amount, 2) . ' USDT'],
                ['Previous Balance', number_format($oldBalance, 2) . ' USDT'],
                ['New Balance', number_format($newBalance, 2) . ' USDT'],
                ['Note', $note]
            ]
        );

        return Command::SUCCESS;
    }
}
