<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegularUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DISABLED: This seeder creates users with fake wallet balances
        // For production, users should register normally with zero balances
        
        $this->command->info("RegularUsersSeeder is disabled to prevent fake user data creation.");
        $this->command->info("Users should register through the normal registration process.");
        
        return;
        
        /* COMMENTED OUT TO PREVENT FAKE DATA CREATION
        // Create sample regular users for testing
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_active' => true,
                'wallet_balance' => 250.00,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_active' => true,
                'wallet_balance' => 500.75,
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_active' => false,
                'wallet_balance' => 75.25,
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_active' => true,
                'wallet_balance' => 1200.00,
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'is_active' => true,
                'wallet_balance' => 0.00,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('Regular users seeded successfully!');
        */
    }
}
