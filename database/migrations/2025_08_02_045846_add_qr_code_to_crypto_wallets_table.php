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
        Schema::table('crypto_wallets', function (Blueprint $table) {
            $table->string('qr_code_image')->nullable()->after('wallet_address'); // Path to QR code image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crypto_wallets', function (Blueprint $table) {
            $table->dropColumn('qr_code_image');
        });
    }
};
