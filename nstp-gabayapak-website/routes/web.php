<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Models\User;
use App\Models\Staff;

// Public routes
Route::get('/', function () {
    // Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    // Fetch only staff members with the "NSTP Formator" role from the database
    $nstpFormators = User::where('user_Type', 'staff')
        ->where('user_role', 'NSTP Formator')
        ->with('staff')
        ->get();
    
    // Format the data for the view
    $formators = [];
    foreach ($nstpFormators as $formator) {
        $formators[] = [
            'name' => $formator->user_Name,
            'image' => $formator->staff && $formator->staff->staff_formal_picture ? asset('storage/' . $formator->staff->staff_formal_picture) : null
        ];
    }
    
    return view('homepage', compact('formators'));
})->name('home');

// Route for managing NSTP Formators (staff only)
Route::get('/formators/manage', function () {
    // Check if user is authenticated and is staff
    if (!auth()->check() || !auth()->user()->isStaff()) {
        return redirect()->route('home');
    }
    
    // Fetch all staff members who could be formators
    $allStaff = User::where('user_Type', 'staff')
        ->with('staff')
        ->get();
    
    // Fetch current NSTP Formators
    $currentFormators = User::where('user_Type', 'staff')
        ->where('user_role', 'NSTP Formator')
        ->pluck('user_id')
        ->toArray();
    
    return view('formators.manage', compact('allStaff', 'currentFormators'));
})->name('formators.manage')->middleware('auth', 'staff');

Route::post('/formators/update', function (\Illuminate\Http\Request $request) {
    // Check if user is authenticated and is staff
    if (!auth()->check() || !auth()->user()->isStaff()) {
        return redirect()->route('home');
    }
    
    // Validate the request
    $request->validate([
        'formators' => 'array',
        'formators.*' => 'exists:users,user_id'
    ]);
    
    // Get all staff users
    $staffUsers = User::where('user_Type', 'staff')->get();
    
    // Update roles based on selection
    foreach ($staffUsers as $user) {
        if (in_array($user->user_id, $request->formators ?? [])) {
            $user->update(['user_role' => 'NSTP Formator']);
        } else {
            // If they were formators but are no longer selected, change their role
            if ($user->user_role === 'NSTP Formator') {
                $user->update(['user_role' => 'Staff']);
            }
        }
    }
    
    return redirect()->route('home')->with('status', 'NSTP Formators updated successfully!');
})->name('formators.update')->middleware('auth', 'staff');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Account routes
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password');
    
    // Project routes
    Route::get('/dashboard', [ProjectController::class, 'index'])->name('dashboard');
    
    // All Projects routes (Staff only)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/current', [ProjectController::class, 'current'])->name('projects.current');
    Route::get('/projects/pending', [ProjectController::class, 'pending'])->name('projects.pending')->middleware('staff');
    Route::get('/projects/archived', [ProjectController::class, 'archived'])->name('projects.archived')->middleware('staff');
    Route::get('/projects/rotc/{section?}', [ProjectController::class, 'rotc'])->name('projects.rotc');
    Route::get('/projects/lts/{section?}', [ProjectController::class, 'lts'])->name('projects.lts');
    Route::get('/projects/cwts/{section?}', [ProjectController::class, 'cwts'])->name('projects.cwts');
    
    // My Projects (Student only)
    Route::get('/my-projects', [ProjectController::class, 'myProjects'])->name('projects.my')->middleware('student');
    Route::get('/my-projects/details/{id}', [ProjectController::class, 'myProjectDetails'])->name('my-projects.details')->middleware('student');
    
    // Project CRUD routes (Student only)
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('student');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('student');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show')->middleware('student');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('student');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('student');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('student');
    
    // Project Details
    Route::get('/projects/details/{id}', [ProjectController::class, 'details'])->name('projects.details');
    
    // Get students by section and component (Student only)
    Route::get('/projects/students/same-section', [ProjectController::class, 'getStudentsBySectionAndComponent'])->name('projects.students.same-section')->middleware('student');
    
    // Activity routes
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
});