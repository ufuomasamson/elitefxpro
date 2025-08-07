<?php
// PURE TEST - No Laravel dependencies
echo "<!DOCTYPE html>";
echo "<html><head><title>Direct Test</title></head><body>";
echo "<h1>🔧 Direct Registration Test</h1>";

if ($_POST) {
    // Handle registration directly
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=pppzoxkc_tradetrustdb', 'pppzoxkc_ttuser', 'TT2025!secure');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($name && $email && $password) {
            // Check if user exists
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                echo "<div style='color: red;'>❌ Email already exists</div>";
            } else {
                // Create user
                $stmt = $pdo->prepare('
                    INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at) 
                    VALUES (?, ?, ?, NOW(), NOW(), NOW())
                ');
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    echo "<div style='color: green;'>✅ User created successfully!</div>";
                    echo "<p><a href='/login'>→ Go to Login</a></p>";
                } else {
                    echo "<div style='color: red;'>❌ Failed to create user</div>";
                }
            }
        } else {
            echo "<div style='color: red;'>❌ All fields required</div>";
        }
    } catch (Exception $e) {
        echo "<div style='color: red;'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

echo '
<style>
    body { font-family: Arial; max-width: 400px; margin: 50px auto; padding: 20px; }
    input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
    button { width: 100%; padding: 12px; background: #007cba; color: white; border: none; border-radius: 4px; }
    div { margin: 10px 0; padding: 10px; border-radius: 4px; }
</style>

<h2>Create Account (Direct)</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>

<p>
    <a href="/">← Back to Homepage</a> | 
    <a href="quick-diagnostic.php">Run Diagnostic</a>
</p>
';

echo "</body></html>";
?>
