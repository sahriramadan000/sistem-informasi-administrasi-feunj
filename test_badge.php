<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Letter;
use App\Models\User;

echo "=== Testing Badge NEW Feature ===\n\n";

// Get latest letter
$letter = Letter::with('creator')->latest()->first();

if (!$letter) {
    echo "No letters found in database\n";
    exit;
}

echo "Latest Letter:\n";
echo "  ID: {$letter->id}\n";
echo "  Letter Number: {$letter->letter_number}\n";
echo "  Created At: {$letter->created_at}\n";
echo "  Created By: {$letter->created_by}\n";
echo "  Creator Name: " . ($letter->creator ? $letter->creator->name : 'N/A') . "\n\n";

// Get first user (not creator)
$testUser = User::where('id', '!=', $letter->created_by)->first();

if (!$testUser) {
    echo "No test user found\n";
    $testUser = User::first();
}

echo "Test User:\n";
echo "  ID: {$testUser->id}\n";
echo "  Name: {$testUser->name}\n\n";

echo "Checking isNewFor() conditions:\n";

// Check 1: Is recent (within 24 hours)
$isRecent = $letter->created_at >= now()->subHours(24);
echo "  1. Is recent (within 24 hours)? " . ($isRecent ? 'YES' : 'NO') . "\n";
echo "     Created: {$letter->created_at}\n";
echo "     24h ago: " . now()->subHours(24) . "\n";

// Check 2: Not viewed by user
$notViewed = !$letter->isViewedBy($testUser->id);
echo "  2. Not viewed by user? " . ($notViewed ? 'YES' : 'NO') . "\n";

// Check 3: Not created by user
$notCreatedByUser = $letter->created_by != $testUser->id;
echo "  3. Not created by user? " . ($notCreatedByUser ? 'YES' : 'NO') . "\n";

// Final result
$isNew = $letter->isNewFor($testUser->id);
echo "\nFinal Result - isNewFor({$testUser->id})? " . ($isNew ? 'YES (badge should show)' : 'NO (badge will not show)') . "\n";

// Check UserLetterView records
echo "\nUserLetterView records for this letter:\n";
$views = \App\Models\UserLetterView::where('letter_id', $letter->id)->get();
if ($views->count() > 0) {
    foreach ($views as $view) {
        $user = User::find($view->user_id);
        echo "  - User {$view->user_id} ({$user->name}) viewed at {$view->viewed_at}\n";
    }
} else {
    echo "  No views recorded\n";
}
