<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Letter;
use App\Models\User;
use App\Models\UserLetterView;

echo "=== Checking Badge Status ===\n\n";

// Get all users
$users = User::all();
echo "Available users:\n";
foreach ($users as $user) {
    echo "  {$user->id}. {$user->name} (role: {$user->role})\n";
}

echo "\n";

// Get active letters
$letters = Letter::with(['creator', 'viewedByUsers'])
    ->active()
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($letters->count() == 0) {
    echo "No active letters found!\n";
    exit;
}

echo "Checking each letter for each user:\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($letters as $letter) {
    echo "Letter: {$letter->letter_number}\n";
    echo "  Created at: {$letter->created_at}\n";
    echo "  Created by: User #{$letter->created_by} ({$letter->creator->name})\n";
    
    // Check isNewFor for each user
    foreach ($users as $user) {
        $isNew = $letter->isNewFor($user->id);
        $status = $isNew ? "✓ WILL SHOW BADGE" : "✗ No badge";
        
        echo "  User #{$user->id} ({$user->name}): {$status}\n";
        
        if ($user->id != $letter->created_by && !$isNew) {
            // Debug why it's not showing
            $isRecent = $letter->created_at >= now()->subHours(24);
            $isViewed = $letter->isViewedBy($user->id);
            
            if (!$isRecent) {
                echo "    → Reason: Letter is older than 24 hours\n";
            }
            if ($isViewed) {
                echo "    → Reason: Already viewed by this user\n";
                $view = UserLetterView::where('letter_id', $letter->id)
                    ->where('user_id', $user->id)
                    ->first();
                if ($view) {
                    echo "       Viewed at: {$view->viewed_at}\n";
                }
            }
        }
    }
    echo "\n";
}

echo str_repeat("=", 80) . "\n";
echo "\nSUMMARY: Login ke aplikasi dengan salah satu user yang menampilkan '✓ WILL SHOW BADGE'\n";
echo "Kemudian buka halaman /letters untuk melihat badge NEW.\n";
