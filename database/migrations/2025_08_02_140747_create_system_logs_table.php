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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('level')->index(); // info, warning, error, critical
            $table->string('type')->index(); // admin, trading, wallet, security, system
            $table->string('action')->index(); // login, logout, trade, deposit, withdrawal, etc.
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_email')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('message');
            $table->json('context')->nullable(); // Additional data as JSON
            $table->string('file')->nullable(); // File where log originated
            $table->integer('line')->nullable(); // Line number
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['level', 'type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
