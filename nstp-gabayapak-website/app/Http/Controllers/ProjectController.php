<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Budget;
use App\Models\Student;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('all_projects.index');
    }

    /**
     * Approve a pending project (staff only).
     */
    public function approve(Project $project)
    {
        if (!Auth::user() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow approving pending projects
        if ($project->Project_Status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending projects can be approved.');
        }

        $project->Project_Status = 'current';
        $project->Project_Rejection_Reason = null;
        // Clear rejected-by when approving
        if (isset($project->Project_Rejected_By)) {
            $project->Project_Rejected_By = null;
        }
        $project->save();

        return redirect()->back()->with('success', 'Project approved successfully.');
    }

    /**
     * Reject a pending project with a reason (staff only).
     */
    public function reject(Request $request, Project $project)
    {
        if (!Auth::user() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        if ($project->Project_Status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending projects can be rejected.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $project->Project_Status = 'rejected';
        $project->Project_Rejection_Reason = $data['reason'];
        // Record which staff rejected the project (prefer user_id if present)
        $staffId = Auth::user()->user_id ?? Auth::id();
        $project->Project_Rejected_By = $staffId;
        $project->save();

        return redirect()->back()->with('success', 'Project rejected successfully.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Only students can create projects
        if (!Auth::user()->isStudent() || !Auth::user()->student) {
            abort(403, 'Unauthorized action.');
        }
        
        // Determine if this is a draft or submission
        $isDraft = !$request->input('submit_project', false);
        
        // Check if student already has a draft (only one draft allowed per student)
        if ($isDraft) {
            $existingDraft = Project::where('student_id', Auth::user()->student->id)
                ->where('Project_Status', 'draft')
                ->first();
            
            if ($existingDraft && !$request->route()->parameter('project')) {
                return redirect()->back()->with('error', 'You already have a draft project. Please edit or submit your existing draft before creating a new one.');
            }
        }

        // If this is a submission, ensure the student doesn't already have a pending project
        if (!$isDraft) {
            $existingSubmitted = Project::where('student_id', Auth::user()->student->id)
                ->where('Project_Status', 'pending')
                ->first();

            if ($existingSubmitted) {
                return redirect()->back()->with('error', 'You already have a project pending for review. You can only have one pending project at a time.');
            }
        }
        
        // Define validation rules based on draft vs submission
        $rules = [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            'Project_Logo' => $isDraft ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Budget data - not required in either case
            'budget_activity' => 'nullable|array',
            'budget_activity.*' => 'nullable|string',
            'budget_resources' => 'nullable|array',
            'budget_resources.*' => 'nullable|string',
            'budget_partners' => 'nullable|array',
            'budget_partners.*' => 'nullable|string',
            'budget_amount' => 'nullable|array',
            'budget_amount.*' => 'nullable|numeric|min:0',
        ];
        
        // For submissions, require all fields
        // For drafts, only require minimum counts but not individual field validation
        if (!$isDraft) {
            // Strict validation for submissions
            $rules = array_merge($rules, [
                'Project_Component' => 'required|string',
                'nstp_section' => 'required|string',
                'Project_Solution' => 'required|string',
                'Project_Goals' => 'required|string',
                'Project_Target_Community' => 'required|string',
                'Project_Expected_Outcomes' => 'required|string',
                'Project_Problems' => 'required|string',
                // Member data - at least one member required with all fields
                'member_name' => 'required|array|min:1',
                'member_name.*' => 'required|string|max:255',
                'member_role' => 'required|array|min:1',
                'member_role.*' => 'required|string|max:255',
                'member_email' => 'required|array|min:1',
                'member_email.*' => 'required|email|max:255',
                'member_contact' => 'required|array|min:1',
                'member_contact.*' => 'required|string|max:20',
                // Activity data - at least one activity required with all fields
                'stage' => 'required|array|min:1',
                'stage.*' => 'required|string|max:255',
                'activities' => 'required|array|min:1',
                'activities.*' => 'required|string',
                'timeframe' => 'required|array|min:1',
                'timeframe.*' => 'required|string|max:255',
                'implementation_date' => 'nullable|array',
                'implementation_date.*' => 'nullable|date',
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'required|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'required|string|in:Planned,Ongoing,Completed',
            ]);

            // Custom validation: If any budget row is partially filled, require all fields for that row
            $requestBudgetActivity = $request->input('budget_activity', []);
            $requestBudgetResources = $request->input('budget_resources', []);
            $requestBudgetPartners = $request->input('budget_partners', []);
            $requestBudgetAmount = $request->input('budget_amount', []);
            $budgetCount = max(
                count($requestBudgetActivity),
                count($requestBudgetResources),
                count($requestBudgetPartners),
                count($requestBudgetAmount)
            );
            for ($i = 0; $i < $budgetCount; $i++) {
                $hasActivity = !empty($requestBudgetActivity[$i]);
                $hasResources = !empty($requestBudgetResources[$i]);
                $hasPartners = !empty($requestBudgetPartners[$i]);
                $hasAmount = !empty($requestBudgetAmount[$i]) && $requestBudgetAmount[$i] !== '0';
                
                // If any field in this budget row is filled, require all fields
                if ($hasActivity || $hasResources || $hasPartners || $hasAmount) {
                    $rules["budget_activity.$i"] = 'required|string';
                    $rules["budget_resources.$i"] = 'required|string';
                    $rules["budget_partners.$i"] = 'required|string';
                    $rules["budget_amount.$i"] = 'required|numeric|min:0';
                }
            }
        } else {
            // Lenient validation for drafts - only require arrays to exist and have minimum counts
            $rules = array_merge($rules, [
                'Project_Component' => 'nullable|string',
                'nstp_section' => 'nullable|string',
                // Project details - not required for drafts
                'Project_Solution' => 'nullable|string',
                'Project_Goals' => 'nullable|string',
                'Project_Target_Community' => 'nullable|string',
                'Project_Expected_Outcomes' => 'nullable|string',
                'Project_Problems' => 'nullable|string',
                // Member data - arrays required with minimum count, but individual fields can be empty
                'member_name' => 'nullable|array',
                'member_name.*' => 'nullable|string|max:255',
                'member_role' => 'nullable|array',
                'member_role.*' => 'nullable|string|max:255',
                'member_email' => 'nullable|array',
                'member_email.*' => 'nullable|email|max:255',
                'member_contact' => 'nullable|array',
                'member_contact.*' => 'nullable|string|max:20',
                // Activity data - arrays required with minimum count, but individual fields can be empty
                'stage' => 'required|array|min:1',
                'stage.*' => 'nullable|string|max:255',
                'activities' => 'required|array|min:1',
                'activities.*' => 'nullable|string',
                'timeframe' => 'required|array|min:1',
                'timeframe.*' => 'nullable|string|max:255',
                'implementation_date' => 'nullable|array',
                'implementation_date.*' => 'nullable|date',
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'nullable|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'nullable|string|in:Planned,Ongoing,Completed',
            ]);
        }
        
        // Custom validation messages
        $messages = [
            'Project_Name.required' => 'The project name field is required.',
            'Project_Team_Name.required' => 'The project team name field is required.',
            'Project_Component.required' => 'The project component field is required.',
            'nstp_section.required' => 'The NSTP section field is required.',
            'stage.*.required' => 'The stage field is required.',
            'activities.*.required' => 'The activities field is required.',
            'timeframe.*.required' => 'The timeframe field is required.',
            'point_person.*.required' => 'The point person field is required.',
            'status.*.required' => 'The status field is required.',
        ];
        
        // Add budget validation messages dynamically
        for ($i = 0; $i < 20; $i++) {
            $messages["budget_activity.$i.required"] = 'The activity field is required when budget information is provided.';
            $messages["budget_resources.$i.required"] = 'The resources needed field is required when budget information is provided.';
            $messages["budget_partners.$i.required"] = 'The partner agencies field is required when budget information is provided.';
            $messages["budget_amount.$i.required"] = 'The amount field is required when budget information is provided.';
        }
        
        // Validate the request
        $validatedData = $request->validate($rules, $messages);
        
        // Handle file upload
        if ($request->hasFile('Project_Logo')) {
            $validatedData['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }
        
        // Add student_id to the validated data
        $validatedData['student_id'] = Auth::user()->student->id;
        
        // Build member_roles mapping (maps student ID to role based on email order)
        $memberRoles = [];
        $studentIds = [$validatedData['student_id']]; // Always include the project owner
        
        // Map member emails to their roles
        $emailToRole = [];
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            foreach ($validatedData['member_email'] as $i => $email) {
                if (!empty($email) && isset($validatedData['member_role'][$i])) {
                    $emailToRole[$email] = $validatedData['member_role'][$i];
                }
            }
        }
        
        // Add the owner's role (first member in the form)
        $ownerEmail = Auth::user()->user_Email ?? '';
        if (isset($validatedData['member_email'][0]) && $validatedData['member_email'][0] === $ownerEmail) {
            $memberRoles[$validatedData['student_id']] = $validatedData['member_role'][0] ?? '';
        }
        
        // Process student IDs from member data
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            // Get student IDs for the member emails
            $memberEmails = array_filter($validatedData['member_email']);
            if (!empty($memberEmails)) {
                $students = \App\Models\Student::whereHas('user', function($query) use ($memberEmails) {
                    $query->whereIn('user_Email', $memberEmails);
                })->with('user')->get();
                
                foreach ($students as $student) {
                    if (!in_array($student->id, $studentIds)) {
                        $studentIds[] = $student->id;
                    }
                    // Map the role based on email
                    $userEmail = $student->user->user_Email ?? '';
                    if (isset($emailToRole[$userEmail])) {
                        $memberRoles[$student->id] = $emailToRole[$userEmail];
                    }
                }
            }
        }
        
        // Also check for member_student_id (from edit form)
        if (isset($validatedData['member_student_id']) && is_array($validatedData['member_student_id'])) {
            foreach ($validatedData['member_student_id'] as $idx => $studentId) {
                if (!in_array($studentId, $studentIds) && is_numeric($studentId)) {
                    $studentIds[] = $studentId;
                    // Map role by index
                    if (isset($validatedData['member_role'][$idx])) {
                        $memberRoles[$studentId] = $validatedData['member_role'][$idx];
                    }
                }
            }
        }
        
        $validatedData['student_ids'] = json_encode($studentIds);
        $memberRolesJson = json_encode($memberRoles);

        // Set status based on submission type
        $validatedData['Project_Status'] = $request->input('submit_project') ? 'pending' : 'draft';
        $validatedData['Project_Section'] = $request->input('nstp_section');
        
        // Create the project
        $project = Project::create([
            'Project_Name' => $validatedData['Project_Name'],
            'Project_Team_Name' => $validatedData['Project_Team_Name'],
            'Project_Logo' => $validatedData['Project_Logo'] ?? null,
            'Project_Component' => $validatedData['Project_Component'] ?? '',
            'Project_Solution' => $validatedData['Project_Solution'] ?? '',
            'Project_Goals' => $validatedData['Project_Goals'] ?? '',
            'Project_Target_Community' => $validatedData['Project_Target_Community'] ?? '',
            'Project_Expected_Outcomes' => $validatedData['Project_Expected_Outcomes'] ?? '',
            'Project_Problems' => $validatedData['Project_Problems'] ?? '',
            'Project_Status' => $validatedData['Project_Status'],
            'student_id' => $validatedData['student_id'],
            'student_ids' => $validatedData['student_ids'],
            'member_roles' => $memberRolesJson,
            'Project_Section' => $validatedData['Project_Section'] ?? '',
        ]);
        
        // Create activities if provided
        if (!empty($validatedData['stage'])) {
            for ($i = 0; $i < count($validatedData['stage']); $i++) {
                // For drafts, we still create activities even if some fields are empty
                // For submissions, we only create activities with non-empty stages
                $isDraft = $validatedData['Project_Status'] === 'draft';
                $stageValue = $validatedData['stage'][$i] ?? '';
                
                if (!empty($stageValue) || $isDraft) {
                    $activity = new Activity([
                        'Stage' => $stageValue,
                        'Specific_Activity' => $validatedData['activities'][$i] ?? '',
                        'Time_Frame' => $validatedData['timeframe'][$i] ?? '',
                        'Implementation_Date' => $validatedData['implementation_date'][$i] ?? null,
                        'Point_Persons' => $validatedData['point_person'][$i] ?? '',
                        'status' => $validatedData['status'][$i] ?? 'Planned',
                        'project_id' => $project->Project_ID,
                    ]);
                    $activity->save();
                    
                    // Create budget for this activity if provided
                    // Check if this budget row has any content (any field filled)
                    if (!empty($validatedData['budget_activity'][$i] ?? '') || 
                        !empty($validatedData['budget_resources'][$i] ?? '') || 
                        !empty($validatedData['budget_partners'][$i] ?? '') || 
                        !empty($validatedData['budget_amount'][$i] ?? '')) {
                        
                        // Create budget directly associated with the project instead of through activity
                        Budget::create([
                            'project_id' => $project->Project_ID,
                            'Specific_Activity' => $validatedData['budget_activity'][$i] ?? '',
                            'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                            'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                            'Amount' => !empty($validatedData['budget_amount'][$i]) ? $validatedData['budget_amount'][$i] : 0,
                        ]);
                    }
                }
            }
        }
        
        // Create remaining budgets (in case there are more budget rows than activities)
        if (isset($validatedData['budget_activity']) && is_array($validatedData['budget_activity'])) {
            $activityCount = isset($validatedData['stage']) ? count($validatedData['stage']) : 0;
            
            for ($i = $activityCount; $i < count($validatedData['budget_activity']); $i++) {
                // Check if this budget row has any content
                if (!empty($validatedData['budget_activity'][$i]) || 
                    !empty($validatedData['budget_resources'][$i] ?? '') || 
                    !empty($validatedData['budget_partners'][$i] ?? '') || 
                    !empty($validatedData['budget_amount'][$i] ?? '')) {
                    
                    Budget::create([
                        'project_id' => $project->Project_ID,
                        'Specific_Activity' => $validatedData['budget_activity'][$i] ?? '',
                        'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                        'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                        'Amount' => !empty($validatedData['budget_amount'][$i]) ? $validatedData['budget_amount'][$i] : 0,
                    ]);
                }
            }
        }
        
        // Redirect with appropriate message
        $message = $validatedData['Project_Status'] === 'pending' 
            ? 'Project submitted successfully for review!' 
            : 'Project saved as draft!';
            
        return redirect()->route('projects.show', $project)->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        // Only the project owner or staff can view the project
        if (Auth::user()->isStudent() && Auth::user()->student->id !== $project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load the project with its relationships
        $project->load(['activities', 'budgets']);
        
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        // Allow staff to edit any project. Students may edit only their own projects and not submitted ones.
        if (Auth::user()->isStaff()) {
            // staff may edit projects
        } else {
            // must be student owner
            if (!Auth::user()->isStudent() || !Auth::user()->student || Auth::user()->student->id !== $project->student_id) {
                abort(403, 'Unauthorized action.');
            }

            // Prevent editing of pending projects by student owner
            if ($project->Project_Status === 'pending') {
                return redirect()->route('projects.show', $project)->with('error', 'Pending projects cannot be edited. You can only update activity status and upload proof for pending projects.');
            }
        }

        // Load the project with its relationships
        $project->load(['activities', 'budgets']);

        // Debug: Check if project is loaded correctly
        if (!$project || !$project->Project_ID) {
            abort(404, 'Project not found.');
        }

        // Choose the correct edit blade based on project status
        if ($project->Project_Status === 'draft') {
            return view('projects.edit-draft', ['project' => $project, 'isDraft' => true]);
        } elseif ($project->Project_Status === 'pending') {
            return view('projects.edit-submitted', ['project' => $project, 'isDraft' => false]);
        } else {
            // Fallback to original edit view for other statuses
            return view('projects.edit', ['project' => $project, 'isDraft' => false]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        // Allow staff to update any project. Students may update only their own projects and not submitted ones.
        if (Auth::user()->isStaff()) {
            // staff allowed
        } else {
            if (!Auth::user()->isStudent() || !Auth::user()->student || Auth::user()->student->id !== $project->student_id) {
                abort(403, 'Unauthorized action.');
            }

            // Prevent updating pending projects by student owner
            if ($project->Project_Status === 'pending') {
                return redirect()->route('projects.show', $project)->with('error', 'Pending projects cannot be edited. You can only update activity status and upload proof for pending projects.');
            }
        }

        // Debug: Check if project is loaded correctly
        if (!$project || !$project->Project_ID) {
            abort(404, 'Project not found.');
        }
        
        // Determine if this is a draft or submission
        $isDraft = !$request->input('submit_project', false);
        
        // Check if student already has a draft (only one draft allowed per student)
        if ($isDraft) {
            $existingDraft = Project::where('student_id', Auth::user()->student->id)
                ->where('Project_Status', 'draft')
                ->where('Project_ID', '!=', $project->Project_ID)
                ->first();
            
            if ($existingDraft) {
                return redirect()->back()->with('error', 'You already have a draft project. Please edit or submit your existing draft before creating a new one.');
            }
        }

        // If this request is transitioning to a submission, ensure the student doesn't already have another pending project
        if (!$isDraft) {
            $existingSubmittedOther = Project::where('student_id', Auth::user()->student->id)
                ->where('Project_Status', 'pending')
                ->where('Project_ID', '!=', $project->Project_ID)
                ->first();

            if ($existingSubmittedOther) {
                return redirect()->back()->with('error', 'You already have another project pending for review. You can only have one pending project at a time.');
            }
        }
        
        // Handle simple status updates from show.blade.php (when only Project_Status is sent)
        $isStatusOnlyUpdate = $request->has('Project_Status') && 
                             !$request->has('Project_Name') && 
                             !$request->has('submit_project');
        
        if ($isStatusOnlyUpdate && $request->input('Project_Status') === 'pending') {
            // Simple status update - no need for full validation
            $project->update(['Project_Status' => 'pending']);
            return redirect()->route('projects.show', $project)->with('success', 'Project submitted successfully for review!');
        }
        
        // Define validation rules based on draft vs submission
        // Determine logo validation: if saving as draft, logo not required; if submitting, require logo when project has no existing logo
        if ($isDraft) {
            $logoRule = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } else {
            $logoRule = $project->Project_Logo ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $rules = [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            'Project_Logo' => $logoRule,
            // Budget data - not required in either case
            'budget_activity' => 'nullable|array',
            'budget_activity.*' => 'nullable|string',
            'budget_resources' => 'nullable|array',
            'budget_resources.*' => 'nullable|string',
            'budget_partners' => 'nullable|array',
            'budget_partners.*' => 'nullable|string',
            'budget_amount' => 'nullable|array',
            'budget_amount.*' => 'nullable|numeric|min:0',
        ];
        
        // For submissions, require all fields
        // For drafts, only require minimum counts but not individual field validation
        if (!$isDraft) {
            // Strict validation for submissions
            $rules = array_merge($rules, [
                'Project_Component' => 'required|string',
                'nstp_section' => 'required|string',
                'Project_Solution' => 'required|string',
                'Project_Goals' => 'required|string',
                'Project_Target_Community' => 'required|string',
                'Project_Expected_Outcomes' => 'required|string',
                'Project_Problems' => 'required|string',
                // Member data - at least one member required with all fields
                'member_name' => 'required|array|min:1',
                'member_name.*' => 'required|string|max:255',
                'member_role' => 'required|array|min:1',
                'member_role.*' => 'required|string|max:255',
                'member_email' => 'required|array|min:1',
                'member_email.*' => 'required|email|max:255',
                'member_contact' => 'required|array|min:1',
                'member_contact.*' => 'required|string|max:20',
                // Activity data - at least one activity required with all fields
                'stage' => 'required|array|min:1',
                'stage.*' => 'required|string|max:255',
                'activities' => 'required|array|min:1',
                'activities.*' => 'required|string',
                'timeframe' => 'required|array|min:1',
                'timeframe.*' => 'required|string|max:255',
                'implementation_date' => 'nullable|array',
                'implementation_date.*' => 'nullable|date',
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'required|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'required|string|in:Planned,Ongoing,Completed',
            ]);

            // Custom validation: If any budget row is partially filled, require all fields for that row
            $requestBudgetActivity = $request->input('budget_activity', []);
            $requestBudgetResources = $request->input('budget_resources', []);
            $requestBudgetPartners = $request->input('budget_partners', []);
            $requestBudgetAmount = $request->input('budget_amount', []);
            $budgetCount = max(
                count($requestBudgetActivity),
                count($requestBudgetResources),
                count($requestBudgetPartners),
                count($requestBudgetAmount)
            );
            for ($i = 0; $i < $budgetCount; $i++) {
                $hasActivity = !empty($requestBudgetActivity[$i]);
                $hasResources = !empty($requestBudgetResources[$i]);
                $hasPartners = !empty($requestBudgetPartners[$i]);
                $hasAmount = !empty($requestBudgetAmount[$i]) && $requestBudgetAmount[$i] !== '0';
                
                // If any field in this budget row is filled, require all fields
                if ($hasActivity || $hasResources || $hasPartners || $hasAmount) {
                    $rules["budget_activity.$i"] = 'required|string';
                    $rules["budget_resources.$i"] = 'required|string';
                    $rules["budget_partners.$i"] = 'required|string';
                    $rules["budget_amount.$i"] = 'required|numeric|min:0';
                }
            }
        } else {
            // Lenient validation for drafts - only require arrays to exist and have minimum counts
            $rules = array_merge($rules, [
                'Project_Component' => 'nullable|string',
                'nstp_section' => 'nullable|string',
                // Project details - not required for drafts
                'Project_Solution' => 'nullable|string',
                'Project_Goals' => 'nullable|string',
                'Project_Target_Community' => 'nullable|string',
                'Project_Expected_Outcomes' => 'nullable|string',
                'Project_Problems' => 'nullable|string',
                // Member data - arrays required with minimum count, but individual fields can be empty
                'member_name' => 'nullable|array',
                'member_name.*' => 'nullable|string|max:255',
                'member_role' => 'nullable|array',
                'member_role.*' => 'nullable|string|max:255',
                'member_email' => 'nullable|array',
                'member_email.*' => 'nullable|email|max:255',
                'member_contact' => 'nullable|array',
                'member_contact.*' => 'nullable|string|max:20',
                // Activity data - arrays required with minimum count, but individual fields can be empty
                'stage' => 'required|array|min:1',
                'stage.*' => 'nullable|string|max:255',
                'activities' => 'required|array|min:1',
                'activities.*' => 'nullable|string',
                'timeframe' => 'required|array|min:1',
                'timeframe.*' => 'nullable|string|max:255',
                'implementation_date' => 'nullable|array',
                'implementation_date.*' => 'nullable|date',
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'nullable|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'nullable|string|in:Planned,Ongoing,Completed',
            ]);
        }
        
        // Custom validation messages
        $messages = [
            'Project_Name.required' => 'The project name field is required.',
            'Project_Team_Name.required' => 'The project team name field is required.',
            'Project_Component.required' => 'The project component field is required.',
            'nstp_section.required' => 'The NSTP section field is required.',
            'stage.*.required' => 'The stage field is required.',
            'activities.*.required' => 'The activities field is required.',
            'timeframe.*.required' => 'The timeframe field is required.',
            'point_person.*.required' => 'The point person field is required.',
            'status.*.required' => 'The status field is required.',
        ];
        
        // Add budget validation messages dynamically
        for ($i = 0; $i < 20; $i++) {
            $messages["budget_activity.$i.required"] = 'The activity field is required when budget information is provided.';
            $messages["budget_resources.$i.required"] = 'The resources needed field is required when budget information is provided.';
            $messages["budget_partners.$i.required"] = 'The partner agencies field is required when budget information is provided.';
            $messages["budget_amount.$i.required"] = 'The amount field is required when budget information is provided.';
        }
        
        // Validate the request
        $validatedData = $request->validate($rules, $messages);
        
        // Handle file upload
        if ($request->hasFile('Project_Logo')) {
            $validatedData['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }
        
        // Set status based on submission type
        // Check both submit_project parameter (from edit forms) and Project_Status parameter (from show page)
        $projectStatus = $request->input('submit_project') || $request->input('Project_Status') === 'pending' ? 'pending' : 'draft';
        
        // Build member_roles mapping (maps student ID to role based on email order)
        $memberRoles = [];
        $studentIds = [$project->student_id]; // Always include the project owner
        
        // Map member emails to their roles
        $emailToRole = [];
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            foreach ($validatedData['member_email'] as $i => $email) {
                if (!empty($email) && isset($validatedData['member_role'][$i])) {
                    $emailToRole[$email] = $validatedData['member_role'][$i];
                }
            }
        }
        
        // Add the owner's role (first member in the form)
        $ownerEmail = Auth::user()->user_Email ?? '';
        if (isset($validatedData['member_email'][0]) && $validatedData['member_email'][0] === $ownerEmail) {
            $memberRoles[$project->student_id] = $validatedData['member_role'][0] ?? '';
        }
        
        // Process student IDs from member data
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            // Get student IDs for the member emails
            $memberEmails = array_filter($validatedData['member_email']);
            if (!empty($memberEmails)) {
                $students = \App\Models\Student::whereHas('user', function($query) use ($memberEmails) {
                    $query->whereIn('user_Email', $memberEmails);
                })->with('user')->get();
                
                foreach ($students as $student) {
                    if (!in_array($student->id, $studentIds)) {
                        $studentIds[] = $student->id;
                    }
                    // Map the role based on email
                    $userEmail = $student->user->user_Email ?? '';
                    if (isset($emailToRole[$userEmail])) {
                        $memberRoles[$student->id] = $emailToRole[$userEmail];
                    }
                }
            }
        }
        
        // Also check for member_student_id (from edit form)
        if (isset($validatedData['member_student_id']) && is_array($validatedData['member_student_id'])) {
            foreach ($validatedData['member_student_id'] as $idx => $studentId) {
                if (!in_array($studentId, $studentIds) && is_numeric($studentId)) {
                    $studentIds[] = $studentId;
                    // Map role by index
                    if (isset($validatedData['member_role'][$idx])) {
                        $memberRoles[$studentId] = $validatedData['member_role'][$idx];
                    }
                }
            }
        }
        
        $studentIdsJson = json_encode($studentIds);
        $memberRolesJson = json_encode($memberRoles);
        
        // Update the project
        $project->update([
            'Project_Name' => $validatedData['Project_Name'],
            'Project_Team_Name' => $validatedData['Project_Team_Name'],
            'Project_Logo' => $validatedData['Project_Logo'] ?? $project->Project_Logo,
            'Project_Component' => $validatedData['Project_Component'] ?? $project->Project_Component,
            'Project_Solution' => $validatedData['Project_Solution'] ?? $project->Project_Solution,
            'Project_Goals' => $validatedData['Project_Goals'] ?? $project->Project_Goals,
            'Project_Target_Community' => $validatedData['Project_Target_Community'] ?? $project->Project_Target_Community,
            'Project_Expected_Outcomes' => $validatedData['Project_Expected_Outcomes'] ?? $project->Project_Expected_Outcomes,
            'Project_Problems' => $validatedData['Project_Problems'] ?? $project->Project_Problems,
            'Project_Status' => $projectStatus,
            'Project_Section' => $request->input('nstp_section') ?? $project->Project_Section,
            'student_ids' => $studentIdsJson,
            'member_roles' => $memberRolesJson,
        ]);
                
        // Note: Member data is collected in the form but not stored in a separate table
        // The member information is displayed in the form but not persisted separately
        // This is because we're using the students table directly as referenced in your earlier message
        
        // Update or create activities
        if (isset($validatedData['stage'])) {
            // Delete existing activities and budgets
            $project->activities()->delete();
            $project->budgets()->delete();
            
            // Create new activities
            for ($i = 0; $i < count($validatedData['stage']); $i++) {
                // For drafts, we still create activities even if some fields are empty
                // For submissions, we only create activities with non-empty stages
                $isDraft = $projectStatus === 'draft';
                $stageValue = $validatedData['stage'][$i] ?? '';
                
                if (!empty($stageValue) || $isDraft) {
                    $activity = $project->activities()->create([
                        'Stage' => $stageValue,
                        'Specific_Activity' => $validatedData['activities'][$i] ?? '',
                        'Time_Frame' => $validatedData['timeframe'][$i] ?? '',
                        'Implementation_Date' => $validatedData['implementation_date'][$i] ?? null,
                        'Point_Persons' => $validatedData['point_person'][$i] ?? '',
                        'status' => $validatedData['status'][$i] ?? 'Planned',
                    ]);
                    
                    // Create budget for this activity if provided
                    // Check if this budget row has any content (any field filled)
                    if (!empty($validatedData['budget_activity'][$i] ?? '') || 
                        !empty($validatedData['budget_resources'][$i] ?? '') || 
                        !empty($validatedData['budget_partners'][$i] ?? '') || 
                        !empty($validatedData['budget_amount'][$i] ?? '')) {
                        
                        // Create budget directly associated with the project instead of through activity
                        Budget::create([
                            'project_id' => $project->Project_ID,
                            'Specific_Activity' => $validatedData['budget_activity'][$i] ?? '',
                            'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                            'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                            'Amount' => !empty($validatedData['budget_amount'][$i]) ? $validatedData['budget_amount'][$i] : 0,
                        ]);
                    }
                }
            }
        }
        
        // Create remaining budgets (in case there are more budget rows than activities)
        if (isset($validatedData['budget_activity']) && is_array($validatedData['budget_activity'])) {
            $activityCount = isset($validatedData['stage']) ? count($validatedData['stage']) : 0;
            
            for ($i = $activityCount; $i < count($validatedData['budget_activity']); $i++) {
                // Check if this budget row has any content
                if (!empty($validatedData['budget_activity'][$i]) || 
                    !empty($validatedData['budget_resources'][$i] ?? '') || 
                    !empty($validatedData['budget_partners'][$i] ?? '') || 
                    !empty($validatedData['budget_amount'][$i] ?? '')) {
                    
                    Budget::create([
                        'project_id' => $project->Project_ID,
                        'Specific_Activity' => $validatedData['budget_activity'][$i] ?? '',
                        'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                        'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                        'Amount' => !empty($validatedData['budget_amount'][$i]) ? $validatedData['budget_amount'][$i] : 0,
                    ]);
                }
            }
        }
        
        // Redirect with appropriate message
        $message = $projectStatus === 'pending' 
            ? 'Project submitted successfully for review!' 
            : 'Project updated successfully!';
            
        return redirect()->route('projects.show', $project)->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        // Allow staff to delete any project. Students may delete only their own drafts.
        if (Auth::user()->isStaff()) {
            // staff may delete any project
            $project->delete();
            return redirect()->back()->with('success', 'Project deleted successfully.');
        }

        // Student owner deletion rules
        if (!Auth::user()->isStudent() || !Auth::user()->student || Auth::user()->student->id !== $project->student_id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion of draft projects for students
        if ($project->Project_Status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft projects can be deleted.');
        }

        // Delete the project
        $project->delete();

        return redirect()->route('projects.my')->with('success', 'Draft project deleted successfully.');
    }

    /**
     * Display current projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function current()
    {
        // Get current projects that are not drafts
        $projects = Project::where('Project_Status', 'current')
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('all_projects.current', compact('projects'));
    }

    /**
     * Display pending projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function pending()
    {
        // Get projects that are pending review (include both 'pending' and newly 'submitted')
        $projects = Project::whereIn('Project_Status', ['pending', 'submitted'])
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('all_projects.pending', compact('projects'));
    }

    /**
     * Display archived projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function archived()
    {
        // Get archived projects that are not drafts
        $projects = Project::where('Project_Status', 'archived')
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('all_projects.archived', compact('projects'));
    }

    /**
     * Archive a project (staff only).
     */
    public function archive(Project $project)
    {
        if (!Auth::user() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        $project->Project_Status = 'archived';
        $project->save();

        return redirect()->back()->with('success', 'Project archived successfully.');
    }

    /**
     * Unarchive a project (staff only) - move it back to pending for review.
     */
    public function unarchive(Project $project)
    {
        if (!Auth::user() || !Auth::user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow unarchiving archived projects
        if ($project->Project_Status !== 'archived') {
            return redirect()->back()->with('error', 'Only archived projects can be unarchived.');
        }

        // Move unarchived projects back to current (active)
        $project->Project_Status = 'current';
        $project->save();

        return redirect()->back()->with('success', 'Project unarchived successfully and moved to pending.');
    }

    /**
     * Display ROTC projects.
     *
     * @param  string|null  $section
     * @return \Illuminate\Http\Response
     */
    public function rotc($section = null)
    {
        // ROTC only has Section A
        $section = 'A';
        
        // Get ROTC projects that are approved/current
        // match either 'A' or 'Section A' if stored with prefix
        $sectionVal = $section;
        $sectionPrefixed = 'Section ' . $section;
        $projects = Project::where('Project_Component', 'ROTC')
            ->where('Project_Status', 'current')
            ->where(function($q) use ($sectionVal, $sectionPrefixed) {
                $q->where('Project_Section', $sectionVal)
                  ->orWhere('Project_Section', $sectionPrefixed);
            })
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('projects.rotc', compact('section', 'projects'));
    }

    /**
     * Display LTS projects.
     *
     * @param  string|null  $section
     * @return \Illuminate\Http\Response
     */
    public function lts($section = null)
    {
        // If no section provided, default to 'A'
        $section = $section ?? 'A';
        $sectionVal = $section;
        $sectionPrefixed = 'Section ' . $section;
        
        // Get LTS projects that are approved/current matching either raw or prefixed section
        $projects = Project::where('Project_Component', 'LTS')
            ->where('Project_Status', 'current')
            ->where(function($q) use ($sectionVal, $sectionPrefixed) {
                $q->where('Project_Section', $sectionVal)
                  ->orWhere('Project_Section', $sectionPrefixed);
            })
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('projects.lts', compact('section', 'projects'));
    }

    /**
     * Display CWTS projects.
     *
     * @param  string|null  $section
     * @return \Illuminate\Http\Response
     */
    public function cwts($section = null)
    {
        // If no section provided, default to 'A'
        $section = $section ?? 'A';
        $sectionVal = $section;
        $sectionPrefixed = 'Section ' . $section;

        // Get CWTS projects that are approved/current matching either raw or prefixed section
        $projects = Project::where('Project_Component', 'CWTS')
            ->where('Project_Status', 'current')
            ->where(function($q) use ($sectionVal, $sectionPrefixed) {
                $q->where('Project_Section', $sectionVal)
                  ->orWhere('Project_Section', $sectionPrefixed);
            })
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('projects.cwts', compact('section', 'projects'));
    }

    /**
     * Display my projects.
     *
     * @return \Illuminate\Http\Response
     */
    public function myProjects()
    {
        // Get projects for the authenticated student
        $student = Auth::user()->student;
        
        $projects = $student->projects()
            ->orderBy('created_at', 'desc')
            ->with('rejectedBy')
            ->get();
        
        return view('all_projects.my-projects', compact('projects'));
    }

    /**
     * Display my project details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function myProjectDetails($id)
    {
        // Only students can access this
        if (!Auth::user()->isStudent() || !Auth::user()->student) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get the project for the authenticated student
        $project = Project::where('Project_ID', $id)
            ->where('student_id', Auth::user()->student->id)
            ->with(['activities', 'budgets']) // Load activities and budgets directly
            ->firstOrFail();
        
        return view('projects.show', compact('project'));
    }

    /**
     * Display project details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function details($id)
    {
        // This would fetch the specific project details
        return view('projects.details');
    }

    /**
     * Get students from the same section and component as the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentsBySectionAndComponent(Request $request)
    {
        // Only students can access this
        if (!Auth::user()->isStudent() || !Auth::user()->student) {
            abort(403, 'Unauthorized action.');
        }
        
        // Get the authenticated student's section and component
        $student = Auth::user()->student;
        $section = $student->student_section;
        $component = $student->student_component;
        
        // Get existing member emails from the request to exclude them
        $existingMemberEmails = $request->input('existing_members', []);
        
        // Get all students from the same section and component (excluding the current user and existing members)
        $students = Student::where('student_section', $section)
            ->where('student_component', $component)
            ->where('user_id', '!=', $student->user_id)
            ->whereDoesntHave('user', function ($query) use ($existingMemberEmails) {
                $query->whereIn('user_Email', $existingMemberEmails);
            })
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->user_id,
                    'name' => $student->user->user_Name,
                    'email' => $student->user->user_Email,
                    'contact_number' => $student->student_contact_number,
                ];
            });
        
        return response()->json($students);
    }

    /**
     * Get student details by IDs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentDetails(Request $request)
    {
        $studentIds = $request->input('student_ids', []);
        
        if (empty($studentIds)) {
            return response()->json([]);
        }
        
        $students = Student::whereIn('id', $studentIds)
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->user_Name ?? '',
                    'email' => $student->user->user_Email ?? '',
                    'contact_number' => $student->student_contact_number ?? '',
                ];
            });
        
        return response()->json($students);
    }
}