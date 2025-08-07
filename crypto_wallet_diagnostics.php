<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use App\Models\CryptoWallet;

echo "🔍 CRYPTO WALLET QR CODE UPLOAD DIAGNOSTICS\n";
echo "===========================================\n\n";

echo "📂 Storage Configuration:\n";
echo "   Storage link exists: " . (is_link(public_path('storage')) ? '✅ YES' : '❌ NO') . "\n";
echo "   QR codes directory: " . (is_dir(storage_path('app/public/qr_codes')) ? '✅ EXISTS' : '❌ MISSING') . "\n";
echo "   QR codes writable: " . (is_writable(storage_path('app/public/qr_codes')) ? '✅ YES' : '❌ NO') . "\n\n";

echo "📊 Current Crypto Wallets:\n";
$wallets = CryptoWallet::all();
foreach ($wallets as $wallet) {
    echo "   💰 " . $wallet->currency . " (" . $wallet->currency_name . "):\n";
    echo "      Address: " . substr($wallet->wallet_address, 0, 20) . "...\n";
    echo "      QR Code: " . ($wallet->qr_code_image ? '✅ ' . $wallet->qr_code_image : '❌ No QR code') . "\n";
    
    if ($wallet->qr_code_image) {
        $qrPath = storage_path('app/public/' . $wallet->qr_code_image);
        $publicPath = public_path('storage/' . $wallet->qr_code_image);
        
        echo "      QR File exists in storage: " . (file_exists($qrPath) ? '✅ YES' : '❌ NO') . "\n";
        echo "      QR File accessible via web: " . (file_exists($publicPath) ? '✅ YES' : '❌ NO') . "\n";
        
        if (file_exists($qrPath)) {
            echo "      File size: " . number_format(filesize($qrPath)) . " bytes\n";
        }
    }
    echo "      Active: " . ($wallet->is_active ? '✅ YES' : '❌ NO') . "\n";
    echo "      Created: " . $wallet->created_at . "\n\n";
}

echo "🔧 Common Issues and Solutions:\n";
echo "   1. Form enctype: Make sure form has enctype='multipart/form-data'\n";
echo "   2. File input: Ensure input type='file' with accept='image/*'\n";
echo "   3. Validation: Check max file size (current limit: 2MB)\n";
echo "   4. Storage permissions: Ensure storage/app/public is writable\n";
echo "   5. JavaScript: Check if file preview is working\n\n";

echo "📝 Form Validation Rules:\n";
echo "   • currency: required|string|max:10|unique:crypto_wallets,currency\n";
echo "   • wallet_address: required|string|max:255\n";
echo "   • network: required|string|max:100\n";
echo "   • qr_code_image: nullable|image|max:2048 (2MB)\n";
echo "   • is_active: boolean\n\n";

echo "🎯 Test Steps:\n";
echo "   1. Go to Admin > Crypto Wallets\n";
echo "   2. Click '+ Add Crypto Wallet'\n";
echo "   3. Fill in required fields\n";
echo "   4. Select an image file (PNG/JPG, under 2MB)\n";
echo "   5. Check if preview shows\n";
echo "   6. Submit form\n";
echo "   7. Check if QR code appears in table\n\n";

echo "✅ Diagnostics complete!\n";
