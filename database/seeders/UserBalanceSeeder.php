<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Give all existing users some initial balance for testing
        $users = User::all();
        
        foreach ($users as $user) {
            if ($user->wallet_balance === null || $user->wallet_balance == 0) {
                $user->wallet_balance = 1000.00; // Give 1000 euros initial balance
                $user->save();
                
                echo "Updated user {$user->email} with balance: 1000.00\n";
            }
        }
        
        echo "Finished updating user balances.\n";
    }
}
