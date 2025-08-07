<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoWalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wallets = [
            [
                'currency' => 'BTC',
                'currency_name' => 'Bitcoin',
                'wallet_address' => '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
                'network' => 'Bitcoin Mainnet',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'ETH',
                'currency_name' => 'Ethereum',
                'wallet_address' => '0x742d35Cc6634C0532925a3b8D5c9E3A84d7D7E2a',
                'network' => 'Ethereum Mainnet',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'USDT',
                'currency_name' => 'Tether',
                'wallet_address' => 'TQn9Y2khEsLJW1ChVWFMSMeRDow5KcbLSE',
                'network' => 'TRC20 (Tron)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'BNB',
                'currency_name' => 'Binance Coin',
                'wallet_address' => 'bnb1grpf0955h0ykzq3ar5nmum7y6gdfl6lxfn46h2',
                'network' => 'Binance Smart Chain',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'currency' => 'ADA',
                'currency_name' => 'Cardano',
                'wallet_address' => 'addr1qx2fxv2umyhttkxyxp8x0dlpdt3k6cwng5pxj3jhsydzer3jcu5d8ps7zex2k2xt3uqxgjqnnj83ws8lhrn648jjxtwq2ytjqp',
                'network' => 'Cardano Mainnet',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \DB::table('crypto_wallets')->insert($wallets);
    }
}
