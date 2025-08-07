<?php

require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 CHECKING TRADE DATA\n";
echo "====================\n\n";

try {
    $trades = \App\Models\Trade::latest()->take(10)->get();
    
    echo "Found " . $trades->count() . " recent trades:\n\n";
    
    foreach ($trades as $trade) {
        echo "Trade ID: {$trade->id}\n";
        echo "  User ID: {$trade->user_id}\n";
        echo "  Type: [{$trade->type}]\n";
        echo "  Symbol: {$trade->crypto_symbol}\n";
        echo "  Amount: {$trade->amount}\n";
        echo "  Price EUR: €{$trade->price_eur}\n";
        echo "  Created: {$trade->created_at}\n";
        echo "  Raw attributes: " . json_encode($trade->getAttributes()) . "\n";
        echo "  ----------------\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

?>
