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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, array, json
            $table->text('description')->nullable();
            $table->string('group')->default('general'); // general, trading, currency, etc.
            $table->timestamps();
        });

        // Insert default settings
        \DB::table('system_settings')->insert([
            [
                'key' => 'default_currency',
                'value' => 'EUR',
                'type' => 'string',
                'description' => 'Default currency for the platform',
                'group' => 'currency',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'currency_symbol',
                'value' => '€',
                'type' => 'string',
                'description' => 'Currency symbol to display',
                'group' => 'currency',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Enable/disable maintenance mode',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimum_deposit',
                'value' => '10',
                'type' => 'number',
                'description' => 'Minimum deposit amount',
                'group' => 'trading',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'minimum_withdrawal',
                'value' => '0.001',
                'type' => 'number',
                'description' => 'Minimum withdrawal amount',
                'group' => 'trading',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'withdrawal_fee',
                'value' => '0.0005',
                'type' => 'number',
                'description' => 'Withdrawal fee percentage',
                'group' => 'trading',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
