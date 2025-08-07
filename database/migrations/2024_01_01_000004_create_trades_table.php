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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('crypto_symbol'); // BTC, ETH, etc.
            $table->decimal('amount', 15, 8); // amount of crypto
            $table->enum('direction', ['buy', 'sell']);
            $table->decimal('price_at_time', 15, 8); // price when trade was made
            $table->decimal('total_value', 15, 8); // amount * price
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->decimal('fee', 15, 8)->default(0); // trading fee
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'crypto_symbol']);
            $table->index(['status', 'created_at']);
            $table->index(['crypto_symbol', 'direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
