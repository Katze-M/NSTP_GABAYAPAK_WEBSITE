<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Project;
$sections = Project::select('Project_Section')->distinct()->pluck('Project_Section')->toArray();
print_r($sections);
