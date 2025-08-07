<?php
// Quick Laravel diagnostic for 500 errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 TradeTrust Error Diagnostic</h1>";

// Test 1: Basic PHP
echo "<h3>1. PHP Status</h3>";
echo "✅ PHP Version: " . PHP_VERSION . "<br>";
echo "✅ Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";

// Test 2: File permissions
echo "<h3>2. File Permissions</h3>";
echo "Storage writable: " . (is_writable(__DIR__ . '/storage') ? '✅ YES' : '❌ NO') . "<br>";
echo "Bootstrap cache writable: " . (is_writable(__DIR__ . '/bootstrap/cache') ? '✅ YES' : '❌ NO') . "<br>";

// Test 3: Environment file
echo "<h3>3. Environment</h3>";
echo ".env exists: " . (file_exists(__DIR__ . '/.env') ? '✅ YES' : '❌ NO') . "<br>";

// Test 4: Try to load Laravel
echo "<h3>4. Laravel Bootstrap Test</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ Autoloader loaded<br>";
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel app created<br>";
    
    // Try to create kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel created<br>";
    
    // Try to handle a simple request
    $request = Illuminate\Http\Request::capture();
    echo "✅ Request captured<br>";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 5: Database connection
echo "<h3>5. Database Test</h3>";
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pppzoxkc_tradetrustdb', 'pppzoxkc_ttuser', 'TT2025!secure');
    echo "✅ Database connected<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Laravel Route Test</h3>";
echo "<a href='/'>Test Homepage</a> | ";
echo "<a href='/login'>Test Login</a> | ";
echo "<a href='/register'>Test Register</a>";

?>
