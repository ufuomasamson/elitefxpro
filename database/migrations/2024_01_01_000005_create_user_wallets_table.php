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
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency'); // BTC, ETH, USDT, etc.
            $table->string('currency_name')->nullable(); // Bitcoin, Ethereum, Tether, etc.
            $table->decimal('balance', 15, 8)->default(0);
            $table->decimal('locked_balance', 15, 8)->default(0); // for pending trades
            $table->decimal('balance_usd', 15, 2)->default(0); // USD value of balance
            $table->timestamps();
            
            $table->unique(['user_id', 'currency']);
            $table->index(['user_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};
