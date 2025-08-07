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
            $table->dropColumn(['api_settings', 'api_key', 'api_secret']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('api_settings')->nullable()->after('notification_settings');
            $table->string('api_key')->nullable()->unique()->after('api_settings');
            $table->string('api_secret')->nullable()->after('api_key');
        });
    }
};
