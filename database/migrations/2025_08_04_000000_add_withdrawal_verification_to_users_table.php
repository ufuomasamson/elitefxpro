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
            // Withdrawal verification status
            $table->enum('withdrawal_status', [
                'active', 
                'aml_kyc_verification', 
                'aml_security_check', 
                'regulatory_compliance'
            ])->default('active')->after('wallet_balance');
            
            // Verification codes set by admin
            $table->string('aml_verification_code')->nullable()->after('withdrawal_status');
            $table->string('fwac_verification_code')->nullable()->after('aml_verification_code');
            $table->string('tsc_verification_code')->nullable()->after('fwac_verification_code');
            
            // Track if user has used the codes
            $table->boolean('aml_code_used')->default(false)->after('tsc_verification_code');
            $table->boolean('fwac_code_used')->default(false)->after('aml_code_used');
            $table->boolean('tsc_code_used')->default(false)->after('fwac_code_used');
            
            // Admin notes for withdrawal restrictions
            $table->text('withdrawal_restriction_notes')->nullable()->after('tsc_code_used');
            
            // Timestamps for when codes were used
            $table->timestamp('aml_code_used_at')->nullable()->after('withdrawal_restriction_notes');
            $table->timestamp('fwac_code_used_at')->nullable()->after('aml_code_used_at');
            $table->timestamp('tsc_code_used_at')->nullable()->after('fwac_code_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'withdrawal_status',
                'aml_verification_code',
                'fwac_verification_code', 
                'tsc_verification_code',
                'aml_code_used',
                'fwac_code_used',
                'tsc_code_used',
                'withdrawal_restriction_notes',
                'aml_code_used_at',
                'fwac_code_used_at',
                'tsc_code_used_at'
            ]);
        });
    }
};
