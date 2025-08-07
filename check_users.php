<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Available users:\n";
$users = User::take(10)->get();
foreach($users as $user) {
    echo "ID: {$user->id} - {$user->name} ({$user->email})\n";
}

?>
