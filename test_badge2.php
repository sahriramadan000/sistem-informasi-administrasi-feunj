<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Letter;
use App\Models\User;

echo "=== Simulating Index View ===\n\n";

// Simulate what happens in controller
$letters = Letter::with(['signatory', 'classification', 'letterType', 'creator', 'viewedByUsers'])
    ->active()
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

$currentUserId = 2; // Operator user
$currentUser = User::find($currentUserId);

echo "Current User: {$currentUser->name} (ID: {$currentUserId})\n\n";

echo "Letters:\n";
foreach ($letters as $index => $letter) {
    $isNew = $letter->isNewFor($currentUserId);
    
    echo ($index + 1) . ". {$letter->letter_number}\n";
    echo "   Created: {$letter->created_at}\n";
    echo "   Created By: {$letter->created_by} ({$letter->creator->name})\n";
    echo "   isNewFor({$currentUserId})? " . ($isNew ? 'YES - Badge SHOULD show' : 'NO - Badge will NOT show') . "\n";
    
    // Check if badge code will work
    if ($isNew) {
        echo "   ✓ Badge code will execute in Blade\n";
    }
    
    echo "\n";
}
