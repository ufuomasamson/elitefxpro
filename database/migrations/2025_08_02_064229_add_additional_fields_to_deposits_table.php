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
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('crypto_symbol')->nullable()->after('method');
            $table->text('rejection_reason')->nullable()->after('admin_notes');
            $table->unsignedBigInteger('processed_by')->nullable()->after('approved_by');
            $table->timestamp('processed_at')->nullable()->after('approved_at');
            $table->string('transaction_id')->nullable()->after('transaction_hash');
            
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn([
                'crypto_symbol',
                'rejection_reason', 
                'processed_by',
                'processed_at',
                'transaction_id'
            ]);
        });
    }
};
