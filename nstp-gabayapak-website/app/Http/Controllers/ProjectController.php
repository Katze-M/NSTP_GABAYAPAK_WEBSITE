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
        if (Auth::user()->isStaff()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Determine if this is a draft or submission
        $isDraft = !$request->input('submit_project', false);
        
        // Define validation rules based on draft vs submission
        $rules = [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            'Project_Logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Budget data - not required in either case
            'budget_activity' => 'nullable|array',
            'budget_activity.*' => 'nullable|string',
            'budget_resources' => 'nullable|array',
            'budget_resources.*' => 'nullable|string',
            'budget_partners' => 'nullable|array',
            'budget_partners.*' => 'nullable|string',
            'budget_amount' => 'nullable|array',
            'budget_amount.*' => 'nullable|string',
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
                // Member data - make member fields optional since we're not storing them
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
                    if (isset($validatedData['budget_activity'][$i]) && !empty($validatedData['budget_activity'][$i])) {
                        $budget = new Budget([
                            'Activity' => $validatedData['budget_activity'][$i],
                            'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                            'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                            'Amount' => $validatedData['budget_amount'][$i] ?? '',
                            'activity_id' => $activity->Activity_ID,
                        ]);
                        $budget->save();
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
        // Only the project owner can edit the project
        if (Auth::user()->student->id !== $project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Prevent editing of submitted projects
        if ($project->Project_Status === 'submitted') {
            return redirect()->route('projects.show', $project)->with('error', 'Submitted projects cannot be edited. You can only update activity status and upload proof for submitted projects.');
        }
        
        // Load the project with its relationships
        $project->load(['activities.budget']);
        
        // Debug: Check if project is loaded correctly
        if (!$project || !$project->Project_ID) {
            abort(404, 'Project not found.');
        }
        
        return view('projects.edit', compact('project'));
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
        // Only the project owner can update the project
        if (Auth::user()->student->id !== $project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Debug: Check if project is loaded correctly
        if (!$project || !$project->Project_ID) {
            abort(404, 'Project not found.');
        }
        
        // Prevent updating submitted projects
        if ($project->Project_Status === 'submitted') {
            return redirect()->route('projects.show', $project)->with('error', 'Submitted projects cannot be edited. You can only update activity status and upload proof for submitted projects.');
        }
        
        // Determine if this is a draft or submission
        $isDraft = !$request->input('submit_project', false);
        
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
            'budget_amount.*' => 'nullable|string',
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
                // Member data - make member fields optional since we're not storing them
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
                    if (isset($validatedData['budget_activity'][$i]) && !empty($validatedData['budget_activity'][$i])) {
                        $activity->budget()->create([
                            'Activity' => $validatedData['budget_activity'][$i],
                            'Resources_Needed' => $validatedData['budget_resources'][$i] ?? '',
                            'Partner_Agencies' => $validatedData['budget_partners'][$i] ?? '',
                            'Amount' => $validatedData['budget_amount'][$i] ?? '',
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
        //
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
        // Get pending projects that are not drafts
        $projects = Project::where('Project_Status', 'pending')
            ->orderBy('created_at', 'desc')
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
            ->get();
        
        return view('all_projects.archived', compact('projects'));
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
        
        // Get ROTC projects that are not drafts
        $projects = Project::where('Project_Component', 'ROTC')
            ->where('Project_Status', '!=', 'draft')
            ->where('Project_Section', $section)
            ->orderBy('created_at', 'desc')
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
        
        // Get LTS projects that are not drafts
        $projects = Project::where('Project_Component', 'LTS')
            ->where('Project_Status', '!=', 'draft')
            ->where('Project_Section', $section)
            ->orderBy('created_at', 'desc')
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
        
        // Get CWTS projects that are not drafts
        $projects = Project::where('Project_Component', 'CWTS')
            ->where('Project_Status', '!=', 'draft')
            ->where('Project_Section', $section)
            ->orderBy('created_at', 'desc')
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
        // Get the project for the authenticated student
        $project = Project::where('Project_ID', $id)
            ->where('student_id', Auth::user()->student->id)
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
        if (Auth::user()->isStaff()) {
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
}