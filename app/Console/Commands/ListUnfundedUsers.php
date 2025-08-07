<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserWallet;

class ListUnfundedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list-unfunded 
                            {--all : Show all users including funded ones}
                            {--limit=20 : Limit number of results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List users who need funding or show all user balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showAll = $this->option('all');
        $limit = intval($this->option('limit'));

        $this->info($showAll ? 'All Users and Their Balances:' : 'Users Awaiting Funding:');
        $this->line('');

        // Get users with their USDT wallets
        $users = User::with(['wallets' => function($query) {
            $query->where('currency', 'USDT');
        }])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();

        $tableData = [];
        $unfundedCount = 0;
        $totalUsers = $users->count();

        foreach ($users as $user) {
            $usdtWallet = $user->wallets->where('currency', 'USDT')->first();
            $balance = $usdtWallet ? $usdtWallet->balance : 0;
            $status = $balance > 0 ? '✅ Funded' : '❌ Unfunded';
            
            if ($balance <= 0) {
                $unfundedCount++;
            }

            // Only show unfunded users unless --all is specified
            if (!$showAll && $balance > 0) {
                continue;
            }

            $tableData[] = [
                $user->id,
                $user->name,
                $user->email,
                number_format($balance, 2) . ' USDT',
                $status,
                $user->created_at->format('M j, Y H:i')
            ];
        }

        if (empty($tableData)) {
            if ($showAll) {
                $this->info('No users found.');
            } else {
                $this->info('🎉 All users have been funded!');
            }
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'USDT Balance', 'Status', 'Registered'],
            $tableData
        );

        $this->line('');
        $this->info("📊 Summary:");
        $this->line("• Total users checked: {$totalUsers}");
        $this->line("• Unfunded users: {$unfundedCount}");
        $this->line("• Funded users: " . ($totalUsers - $unfundedCount));

        if ($unfundedCount > 0 && !$showAll) {
            $this->line('');
            $this->comment('💡 To fund a user account, use:');
            $this->line('php artisan users:fund user@example.com 1000 --note="Initial funding"');
        }

        return Command::SUCCESS;
    }
}
