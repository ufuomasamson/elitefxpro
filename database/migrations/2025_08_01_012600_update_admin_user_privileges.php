<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update admin user to ensure they have admin privileges
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->update(['is_admin' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally remove admin privileges
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->update(['is_admin' => false]);
        }
    }
};
