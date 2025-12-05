<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Letter;
use App\Models\User;

echo "=== Testing auth()->id() in Blade context ===\n\n";

// Simulate login as user 2 (Operator)
$user = User::find(2);
if (!$user) {
    echo "User 2 not found\n";
    exit;
}

// Manually set auth
auth()->login($user);

echo "Logged in as: {$user->name} (ID: " . auth()->id() . ")\n\n";

// Get letters like in the controller
$letters = Letter::with(['signatory', 'classification', 'letterType', 'creator', 'viewedByUsers'])
    ->active()
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

echo "Testing letters:\n";
foreach ($letters as $letter) {
    $authId = auth()->id();
    $isNew = $letter->isNewFor($authId);
    
    echo "- {$letter->letter_number}\n";
    echo "  auth()->id() returns: {$authId}\n";
    echo "  \$letter->isNewFor(auth()->id()) returns: " . ($isNew ? 'true' : 'false') . "\n";
    echo "  Badge should " . ($isNew ? 'SHOW' : 'NOT show') . "\n\n";
}
