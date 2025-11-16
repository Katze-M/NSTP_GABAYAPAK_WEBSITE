<?php
require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Project;

// Try to get all users
$users = User::all();
echo "Total users: " . $users->count() . "\n";

foreach ($users as $user) {
    echo "User ID: " . $user->user_id . " - Name: " . $user->user_Name . " - Type: " . $user->user_Type . "\n";
    
    // Try to get the student profile
    $student = $user->student;
    if ($student) {
        echo "  Student profile found: ID " . $student->id . "\n";
        
        // Try to get projects through the relationship
        $projects = $student->projects;
        echo "  Projects through relationship: " . $projects->count() . "\n";
        
        foreach ($projects as $project) {
            echo "    Project: " . $project->Project_Name . " - Status: " . $project->Project_Status . "\n";
        }
    } else {
        echo "  No student profile found for this user\n";
    }
}

// Check specifically for student users
$studentUsers = User::where('user_Type', 'student')->get();
echo "\nStudent users: " . $studentUsers->count() . "\n";

foreach ($studentUsers as $user) {
    echo "Student User ID: " . $user->user_id . " - Name: " . $user->user_Name . "\n";
    
    // Try to get the student profile
    $student = $user->student;
    if ($student) {
        echo "  Student profile found: ID " . $student->id . "\n";
        
        // Try to get projects through the relationship
        $projects = $student->projects;
        echo "  Projects through relationship: " . $projects->count() . "\n";
        
        foreach ($projects as $project) {
            echo "    Project: " . $project->Project_Name . " - Status: " . $project->Project_Status . "\n";
        }
    } else {
        echo "  No student profile found for this user\n";
    }
}