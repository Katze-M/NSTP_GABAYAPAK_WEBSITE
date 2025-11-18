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
        if ($project->Project_Status !== 'pending' && $project->Project_Status !== 'submitted') {
            return redirect()->back()->with('error', 'Only pending/submitted projects can be approved.');
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

        if ($project->Project_Status !== 'pending' && $project->Project_Status !== 'submitted') {
            return redirect()->back()->with('error', 'Only pending/submitted projects can be rejected.');
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

        // If this is a submission, ensure the student doesn't already have a submitted/pending project
        if (!$isDraft) {
            $existingSubmitted = Project::where('student_id', Auth::user()->student->id)
                ->whereIn('Project_Status', ['submitted', 'pending'])
                ->first();

            if ($existingSubmitted) {
                return redirect()->back()->with('error', 'You already have a project submitted for review. You can only have one submitted project at a time.');
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
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'required|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'required|string|in:Planned,Ongoing,Completed',
            ]);
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
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'nullable|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'nullable|string|in:Planned,Ongoing,Completed',
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate($rules);
        
        // Handle file upload
        if ($request->hasFile('Project_Logo')) {
            $validatedData['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }
        
        // Add student_id to the validated data
        $validatedData['student_id'] = Auth::user()->student->id;
        
        // Process student IDs from member data
        $studentIds = [$validatedData['student_id']]; // Always include the project owner
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            // Get student IDs for the member emails
            $memberEmails = array_filter($validatedData['member_email']);
            if (!empty($memberEmails)) {
                $students = \App\Models\Student::whereHas('user', function($query) use ($memberEmails) {
                    $query->whereIn('user_Email', $memberEmails);
                })->get();
                
                foreach ($students as $student) {
                    if (!in_array($student->id, $studentIds)) {
                        $studentIds[] = $student->id;
                    }
                }
            }
        }
        
        // Also check for member_student_id (from edit form)
        if (isset($validatedData['member_student_id']) && is_array($validatedData['member_student_id'])) {
            foreach ($validatedData['member_student_id'] as $studentId) {
                if (!in_array($studentId, $studentIds) && is_numeric($studentId)) {
                    $studentIds[] = $studentId;
                }
            }
        }
        
        $validatedData['student_ids'] = json_encode($studentIds);

        // Set status based on submission type
        $validatedData['Project_Status'] = $request->input('submit_project') ? 'submitted' : 'draft';
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
                        'Point_Persons' => $validatedData['point_person'][$i] ?? '',
                        'status' => $validatedData['status'][$i] ?? 'Planned',
                        'project_id' => $project->Project_ID,
                    ]);
                    $activity->save();
                    
                    // Create budget for this activity if provided
                    // Fix: Check if budget data exists at this index and has at least one non-empty field
                    if (isset($validatedData['budget_activity']) && 
                        is_array($validatedData['budget_activity']) && 
                        isset($validatedData['budget_activity'][$i]) && 
                        (!empty($validatedData['budget_activity'][$i]) || 
                         !empty($validatedData['budget_resources'][$i] ?? '') || 
                         !empty($validatedData['budget_partners'][$i] ?? '') || 
                         !empty($validatedData['budget_amount'][$i] ?? ''))) {
                        
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
        
        // Redirect with appropriate message
        $message = $validatedData['Project_Status'] === 'submitted' 
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

            // Prevent editing of submitted projects by student owner
            if ($project->Project_Status === 'submitted') {
                return redirect()->route('projects.show', $project)->with('error', 'Submitted projects cannot be edited. You can only update activity status and upload proof for submitted projects.');
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
        } elseif ($project->Project_Status === 'submitted') {
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

            // Prevent updating submitted projects by student owner
            if ($project->Project_Status === 'submitted') {
                return redirect()->route('projects.show', $project)->with('error', 'Submitted projects cannot be edited. You can only update activity status and upload proof for submitted projects.');
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

        // If this request is transitioning to a submission, ensure the student doesn't already have another submitted/pending project
        if (!$isDraft) {
            $existingSubmittedOther = Project::where('student_id', Auth::user()->student->id)
                ->whereIn('Project_Status', ['submitted', 'pending'])
                ->where('Project_ID', '!=', $project->Project_ID)
                ->first();

            if ($existingSubmittedOther) {
                return redirect()->back()->with('error', 'You already have another project submitted for review. You can only have one submitted project at a time.');
            }
        }
        
        // Define validation rules based on draft vs submission
        $rules = [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            'Project_Logo' => $project->Project_Logo ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'required|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'required|string|in:Planned,Ongoing,Completed',
            ]);
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
                'point_person' => 'required|array|min:1',
                'point_person.*' => 'nullable|string|max:255',
                'status' => 'required|array|min:1',
                'status.*' => 'nullable|string|in:Planned,Ongoing,Completed',
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate($rules);
        
        // Handle file upload
        if ($request->hasFile('Project_Logo')) {
            $validatedData['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }
        
        // Set status based on submission type
        $projectStatus = $request->input('submit_project') ? 'submitted' : 'draft';
        
        // Process student IDs from member data
        $studentIds = [$project->student_id]; // Always include the project owner
        if (isset($validatedData['member_email']) && is_array($validatedData['member_email'])) {
            // Get student IDs for the member emails
            $memberEmails = array_filter($validatedData['member_email']);
            if (!empty($memberEmails)) {
                $students = \App\Models\Student::whereHas('user', function($query) use ($memberEmails) {
                    $query->whereIn('user_Email', $memberEmails);
                })->get();
                
                foreach ($students as $student) {
                    if (!in_array($student->id, $studentIds)) {
                        $studentIds[] = $student->id;
                    }
                }
            }
        }
        
        // Also check for member_student_id (from edit form)
        if (isset($validatedData['member_student_id']) && is_array($validatedData['member_student_id'])) {
            foreach ($validatedData['member_student_id'] as $studentId) {
                if (!in_array($studentId, $studentIds) && is_numeric($studentId)) {
                    $studentIds[] = $studentId;
                }
            }
        }
        
        $studentIdsJson = json_encode($studentIds);
        
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
        ]);
                
        // Note: Member data is collected in the form but not stored in a separate table
        // The member information is displayed in the form but not persisted separately
        // This is because we're using the students table directly as referenced in your earlier message
        
        // Update or create activities
        if (isset($validatedData['stage'])) {
            // Delete existing activities and their budget
            $project->activities()->delete();
            
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
                        'Point_Persons' => $validatedData['point_person'][$i] ?? '',
                        'status' => $validatedData['status'][$i] ?? 'Planned',
                    ]);
                    
                    // Create budget for this activity if provided
                    // Fix: Check if budget data exists at this index and has at least one non-empty field
                    if (isset($validatedData['budget_activity']) && 
                        is_array($validatedData['budget_activity']) && 
                        isset($validatedData['budget_activity'][$i]) && 
                        (!empty($validatedData['budget_activity'][$i]) || 
                         !empty($validatedData['budget_resources'][$i] ?? '') || 
                         !empty($validatedData['budget_partners'][$i] ?? '') || 
                         !empty($validatedData['budget_amount'][$i] ?? ''))) {
                        
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
        
        // Redirect with appropriate message
        $message = $projectStatus === 'submitted' 
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