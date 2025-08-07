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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->string('country')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('country');
            $table->string('timezone')->nullable()->after('bio');
            $table->timestamp('last_login_at')->nullable()->after('timezone');
            $table->boolean('two_factor_enabled')->default(false)->after('last_login_at');
            $table->json('trading_settings')->nullable()->after('two_factor_enabled');
            $table->json('notification_settings')->nullable()->after('trading_settings');
            $table->json('api_settings')->nullable()->after('notification_settings');
            $table->string('api_key')->nullable()->unique()->after('api_settings');
            $table->string('api_secret')->nullable()->after('api_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone',
                'country',
                'bio',
                'timezone',
                'last_login_at',
                'two_factor_enabled',
                'trading_settings',
                'notification_settings',
                'api_settings',
                'api_key',
                'api_secret'
            ]);
        });
    }
};
