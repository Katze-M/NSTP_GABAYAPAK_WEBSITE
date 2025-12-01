<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Approval;
use App\Models\User;

$approvals = Approval::where('type','staff')->where('status','pending')->with('user')->orderBy('created_at','desc')->get();
$legacy = User::where('user_Type','staff')->whereRaw('(approved IS NULL OR approved = 0)')->whereDoesntHave('approvals')->with('staff')->get();

echo "Approvals (pending): " . $approvals->count() . PHP_EOL;
foreach($approvals as $a){
    $u = $a->user;
    echo "A|{$a->id}|{$u->user_id}|{$u->user_Name}|{$u->user_Email}|{$u->created_at}\n";
}

echo "Legacy (no approval record): " . $legacy->count() . PHP_EOL;
foreach($legacy as $u){
    echo "L|{$u->user_id}|{$u->user_Name}|{$u->user_Email}|{$u->created_at}\n";
}

$all = $approvals->merge($legacy)->sortByDesc(function($item){ return optional($item->created_at); });

echo "Merged total: " . $all->count() . PHP_EOL;
foreach($all as $it){
    if ($it instanceof Approval) {
        $u = $it->user; echo "M-A|{$it->id}|{$u->user_id}|{$u->user_Name}|{$u->user_Email}|{$it->created_at}\n";
    } else {
        // legacy user object
        $u = $it; echo "M-L|{$u->user_id}|{$u->user_Name}|{$u->user_Email}|{$u->created_at}\n";
    }
}
