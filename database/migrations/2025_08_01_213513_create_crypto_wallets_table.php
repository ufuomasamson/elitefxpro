<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 10); // BTC, ETH, etc.
            $table->string('currency_name', 50); // Bitcoin, Ethereum, etc.
            $table->string('wallet_address'); // Wallet address for deposits
            $table->string('network', 100)->nullable(); // Network type (mainnet, testnet, etc.)
            $table->boolean('is_active')->default(true); // Whether this wallet is active for deposits
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
            
            // Ensure one wallet per currency
            $table->unique('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_wallets');
    }
};
