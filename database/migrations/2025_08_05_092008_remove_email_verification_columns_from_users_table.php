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
            // Remove email verification columns
            $table->dropColumn([
                'verification_code',
                'verification_code_expires_at', 
                'verification_attempts'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-add columns if needed to rollback
            $table->string('verification_code', 6)->nullable()->after('email_verified_at');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            $table->integer('verification_attempts')->default(0)->after('verification_code_expires_at');
        });
    }
};
