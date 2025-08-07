<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Set verification codes for test user
echo "🔧 Setting up verification codes for test user\n";
echo "=============================================\n\n";

// Connect to database
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Find test user
    $user = User::find(8);
    
    if (!$user) {
        echo "❌ Test user not found\n";
        exit;
    }
    
    echo "👤 Setting verification codes for: {$user->name}\n\n";
    
    // Set verification codes
    $user->aml_verification_code = 'AML123';
    $user->fwac_verification_code = 'FWAC456';
    $user->tsc_verification_code = 'TSC789';
    $user->aml_code_used = false;
    $user->fwac_code_used = false;
    $user->tsc_code_used = false;
    $user->save();
    
    echo "✅ Verification codes set:\n";
    echo "   - AML Code: AML123\n";
    echo "   - FWAC Code: FWAC456\n";
    echo "   - TSC Code: TSC789\n";
    echo "   - All codes marked as unused\n\n";
    
    // Verify the setup
    $needsVerification = $user->needsWithdrawalVerification();
    echo "🔐 Now needs verification: " . ($needsVerification ? "YES" : "NO") . "\n";
    
    if ($needsVerification) {
        $verificationStep = $user->getNextVerificationStep();
        echo "📋 First verification step: {$verificationStep['title']}\n";
        echo "💬 Message: {$verificationStep['message']}\n";
    }
    
    echo "\n🎯 Test user is now ready for verification workflow testing!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
