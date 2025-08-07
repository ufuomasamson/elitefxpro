<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CryptoWallet;

class CryptoWalletsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptoWallets = [
            [
                'currency' => 'BTC',
                'currency_name' => 'Bitcoin',
                'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'ETH',
                'currency_name' => 'Ethereum',
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'USDT',
                'currency_name' => 'Tether',
                'wallet_address' => 'TQrZ8tKfjpras94FpdaNcSk1jzGEhYG8nr',
                'network' => 'TRC20',
                'is_active' => true
            ],
            [
                'currency' => 'ADA',
                'currency_name' => 'Cardano',
                'wallet_address' => 'addr1qx2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'DOT',
                'currency_name' => 'Polkadot',
                'wallet_address' => '15oF4uVJwmo4TdGW7VfQxNLavjCXviqxT9S1MgbjMNHr6Sp5',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'LTC',
                'currency_name' => 'Litecoin',
                'wallet_address' => 'ltc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'XRP',
                'currency_name' => 'Ripple',
                'wallet_address' => 'rNLrqcj3LLNjcXjFYKk9YrsaWXzQP9uwV5',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'LINK',
                'currency_name' => 'Chainlink',
                'wallet_address' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F',
                'network' => 'mainnet',
                'is_active' => true
            ],
            [
                'currency' => 'BCH',
                'currency_name' => 'Bitcoin Cash',
                'wallet_address' => 'bitcoincash:qp3wjpa3tjlj042z2wv7hahsldgwhwy0rq9sywjpyy',
                'network' => 'mainnet',
                'is_active' => true
            ]
        ];

        foreach ($cryptoWallets as $wallet) {
            CryptoWallet::updateOrCreate(
                ['currency' => $wallet['currency']],
                $wallet
            );
        }

        $this->command->info('Crypto wallets seeded successfully!');
    }
}
