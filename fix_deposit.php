<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\Deposit;

echo "🔧 FIXING DEPOSIT APPROVAL FIELDS\n";
echo "===============================\n\n";

// Find the deposit
$deposit = Deposit::find(4);

if ($deposit) {
    echo "📦 Found deposit #{$deposit->id}:\n";
    echo "   User ID: {$deposit->user_id}\n";
    echo "   Amount: {$deposit->amount}\n";
    echo "   Status: {$deposit->status}\n";
    echo "   Processed at: " . ($deposit->processed_at ?? 'null') . "\n";
    echo "   Approved at: " . ($deposit->approved_at ?? 'null') . "\n";
    echo "   Processed by: " . ($deposit->processed_by ?? 'null') . "\n";
    echo "   Approved by: " . ($deposit->approved_by ?? 'null') . "\n\n";
    
    // Update the fields
    $deposit->processed_at = now();
    $deposit->approved_at = now();
    $deposit->processed_by = 1; // Assuming admin user ID 1
    $deposit->approved_by = 1;
    $deposit->save();
    
    echo "✅ Deposit approval fields updated successfully!\n\n";
    
    // Verify the update
    $deposit->refresh();
    echo "📋 Updated deposit #{$deposit->id}:\n";
    echo "   Processed at: {$deposit->processed_at}\n";
    echo "   Approved at: {$deposit->approved_at}\n";
    echo "   Processed by: {$deposit->processed_by}\n";
    echo "   Approved by: {$deposit->approved_by}\n";
} else {
    echo "❌ Deposit not found!\n";
}
