<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ReportsController;
use App\Models\User;
use App\Models\Staff;

// Public routes
Route::get('/', function () {
    // Check if user is authenticated
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    // Fetch only staff members with the "NSTP Formator" role from the database
    // Only include those who are approved (or have an approved approval record)
    $nstpFormators = User::where('user_Type', 'staff')
        ->where('user_role', 'NSTP Formator')
        ->where(function($q) {
            $q->where('approved', true)
              ->orWhereHas('approvals', function($a) {
                  $a->where('status', 'approved');
              });
        })
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
    
    // Fetch only approved staff members who could be formators
    $allStaff = User::where('user_Type', 'staff')
        ->where(function($q) {
            $q->where('approved', true)
              ->orWhereHas('approvals', function($a) {
                  $a->where('status', 'approved');
              });
        })
        ->with('staff')
        ->get();
    
    // Fetch current NSTP Formators (only among approved staff)
    $currentFormators = User::where('user_Type', 'staff')
        ->where('user_role', 'NSTP Formator')
        ->where(function($q) {
            $q->where('approved', true)
              ->orWhereHas('approvals', function($a) {
                  $a->where('status', 'approved');
              });
        })
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
    
    // Only consider approved staff for role changes
    $staffUsers = User::where('user_Type', 'staff')
        ->where(function($q) {
            $q->where('approved', true)
              ->orWhereHas('approvals', function($a) {
                  $a->where('status', 'approved');
              });
        })
        ->get();

    $selected = $request->formators ?? [];
    
    // Update roles based on selection (only for approved staff)
    foreach ($staffUsers as $user) {
        if (in_array($user->user_id, $selected)) {
            $user->update(['user_role' => 'NSTP Formator']);
        } else {
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
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // Reports route
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    
    // All Projects routes (Staff only)
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/current', [ProjectController::class, 'current'])->name('projects.current');
    Route::get('/projects/pending', [ProjectController::class, 'pending'])->name('projects.pending')->middleware('staff');
    Route::get('/projects/rejected', [ProjectController::class, 'rejected'])->name('projects.rejected')->middleware('staff');
    Route::get('/projects/archived', [ProjectController::class, 'archived'])->name('projects.archived')->middleware('staff');
    // Approve / Reject actions for staff
    Route::post('/projects/{project}/approve', [ProjectController::class, 'approve'])->name('projects.approve')->middleware('staff');
    Route::post('/projects/{project}/reject', [ProjectController::class, 'reject'])->name('projects.reject')->middleware('staff');
    Route::post('/projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive')->middleware('staff');
    Route::post('/projects/{project}/unarchive', [ProjectController::class, 'unarchive'])->name('projects.unarchive')->middleware('staff');
    Route::get('/projects/rotc/{section?}', [ProjectController::class, 'rotc'])->name('projects.rotc');
    Route::get('/projects/lts/{section?}', [ProjectController::class, 'lts'])->name('projects.lts');
    Route::get('/projects/cwts/{section?}', [ProjectController::class, 'cwts'])->name('projects.cwts');
    
    // My Projects (Student only)
    Route::get('/my-projects', [ProjectController::class, 'myProjects'])->name('projects.my')->middleware('student');
    Route::get('/my-projects/details/{id}', [ProjectController::class, 'myProjectDetails'])->name('my-projects.details')->middleware('student');
    
    // Project CRUD routes (Student only)
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('student');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('student');
    // Allow controller to enforce access (owner or staff). Do not restrict to 'student' middleware here.
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    // Allow controller to enforce whether a user is allowed to edit/update/delete (owner student or staff)
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    
    // Project Details
    Route::get('/projects/details/{id}', [ProjectController::class, 'details'])->name('projects.details');
    
    // Get students by section and component (Student only)
    Route::get('/projects/students/same-section', [ProjectController::class, 'getStudentsBySectionAndComponent'])->name('projects.students.same-section')->middleware('student');
    Route::get('/projects/students/for-staff', [ProjectController::class, 'getStudentsForStaff'])->name('projects.students.for-staff')->middleware('staff');
    
    // Get user's pending project count (Student only)
    Route::get('/projects/user/pending-count', [ProjectController::class, 'getUserPendingCount'])->name('projects.user.pending-count')->middleware('student');
    
    // Get student details by IDs (Student only)
    Route::post('/api/students/details', [ProjectController::class, 'getStudentDetails'])->name('api.students.details')->middleware('student');
    Route::post('/api/students/details-staff', [ProjectController::class, 'getStudentDetailsForStaff'])->name('api.students.details-staff')->middleware('staff');
    
    // Activity routes
    Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');

});

// Approval routes
// Staff approvals - only SACSI Director can access
Route::middleware(['auth', 'role:SACSI Director'])->group(function () {
    Route::get('/approvals/staff', [\App\Http\Controllers\StaffApprovalController::class, 'index'])->name('approvals.staff');
    Route::get('/approvals/staff/history', [\App\Http\Controllers\StaffApprovalController::class, 'history'])->name('approvals.staff.history');
    Route::post('/approvals/staff/{id}/approve', [\App\Http\Controllers\StaffApprovalController::class, 'approve'])->name('approvals.staff.approve');
    Route::post('/approvals/staff/{id}/reject', [\App\Http\Controllers\StaffApprovalController::class, 'reject'])->name('approvals.staff.reject');
});

// Student approvals - only NSTP Program Officer can access
Route::middleware(['auth', 'role:NSTP Program Officer'])->group(function () {
    Route::get('/approvals/students', [\App\Http\Controllers\StudentApprovalController::class, 'index'])->name('approvals.students');
    Route::get('/approvals/students/history', [\App\Http\Controllers\StudentApprovalController::class, 'history'])->name('approvals.students.history');
    Route::post('/approvals/students/{id}/approve', [\App\Http\Controllers\StudentApprovalController::class, 'approve'])->name('approvals.students.approve');
    Route::post('/approvals/students/{id}/reject', [\App\Http\Controllers\StudentApprovalController::class, 'reject'])->name('approvals.students.reject');
});

// Registration status check (public form shown on login page)
Route::get('/registration-status', function () { return view('registration.status'); })->name('registration.status');
Route::post('/registration-status', function (\Illuminate\Http\Request $request) {
    $request->validate(['user_Email' => 'required|email']);
    $user = App\Models\User::where('user_Email', $request->user_Email)->first();
    $status = null;
    $message = 'No registration found for that email.';
    if ($user) {
        $approval = App\Models\Approval::where('user_id', $user->user_id)->latest()->first();
        if ($user->approved || ($approval && $approval->status === 'approved')) {
            $status = 'approved';
            $message = 'Your account registration has been approved!';
        } elseif ($approval && $approval->status === 'pending') {
            $status = 'pending';
            $message = 'Your registration is currently under review.';
        } elseif ($approval && $approval->status === 'rejected') {
            $status = 'rejected';
            $message = 'Your registration was rejected. Please register again.';
        } else {
            $message = 'No approval record found. Please contact admin.';
        }
    }
    return view('registration.status', compact('message','status'));
})->name('registration.status.post');
