<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Approval;

$legacy = User::where('user_Type', 'staff')
    ->whereRaw('(approved IS NULL OR approved = 0)')
    ->whereDoesntHave('approvals')
    ->get();

if ($legacy->isEmpty()) {
    echo "No legacy staff users found.\n";
    exit(0);
}

echo "Found " . $legacy->count() . " legacy staff users. Creating approval rows...\n";

foreach ($legacy as $user) {
    $approval = Approval::create([
        'user_id' => $user->user_id,
        'approver_id' => null,
        'approver_role' => null,
        'type' => 'staff',
        'status' => 'pending',
        'remarks' => null,
    ]);

    // Set created_at to the user's created_at to preserve ordering (optional)
    $approval->created_at = $user->created_at;
    $approval->save();

    echo "Created approval id={$approval->id} for user_id={$user->user_id} ({$user->user_Email})\n";
}

echo "Done.\n";
