<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Budget;
use App\Models\Student;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    /* -----------------------------------------------------------------
     | Standard resource endpoints delegate to the draft/submit flows
     | ----------------------------------------------------------------- */

    public function store(Request $request)
    {
        // Decide which flow based on submitted flag
        if ($request->input('submit_project')) {
            return $this->storeSubmit($request);
        } else {
            return $this->storeDraft($request);
        }
    }

    public function update(Request $request, Project $project)
    {
        // Decide which flow based on submitted flag
        if ($request->input('submit_project')) {
            return $this->updateSubmit($request, $project);
        }

        // If staff saved via staff-edit flow, treat as a proper staff UPDATE (not a draft save)
        if (Auth::user() && Auth::user()->isStaff() && $request->input('staff_save')) {
            return $this->updateStaff($request, $project);
        }

        return $this->updateDraft($request, $project);
    }

    /* -----------------------------------------------------------------
     | Create flows
     | ----------------------------------------------------------------- */

    /**
     * Store a project as draft.
     * Minimal required fields: owner (student), Project_Name, Project_Team_Name
     */
    public function storeDraft(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->student) {
            return redirect()->back()->with('error', 'Only authenticated students can create projects.');
        }

        $rules = $this->validateDraftRules();
        $messages = $this->validationMessages();

        // If the request did not include the full form payload (e.g. submit-from-show button
        // posts only Project_Status), build a validation payload from the existing project
        // data so we validate the current stored values rather than the empty request.
        if (!$request->has('Project_Name')) {
            $data = $request->all();
            // Basic project fields
            $data['Project_Name'] = $project->Project_Name;
            $data['Project_Team_Name'] = $project->Project_Team_Name;
            $data['Project_Component'] = $project->Project_Component;
            $data['nstp_section'] = $project->Project_Section;
            $data['Project_Solution'] = $project->Project_Solution;
            $data['Project_Goals'] = $project->Project_Goals;
            $data['Project_Target_Community'] = $project->Project_Target_Community;
            $data['Project_Expected_Outcomes'] = $project->Project_Expected_Outcomes;
            $data['Project_Problems'] = $project->Project_Problems;

            // Members
            $members = is_callable([$project, 'members']) ? $project->members() : [];
            $data['member_student_id'] = [];
            $data['member_role'] = [];
            foreach ($members as $m) {
                $data['member_student_id'][] = $m['student_id'] ?? null;
                $data['member_role'][] = $m['role'] ?? null;
            }

            // Activities
            $data['stage'] = [];
            $data['activities'] = [];
            $data['timeframe'] = [];
            $data['implementation_date'] = [];
            $data['point_person'] = [];
            $data['status'] = [];
            foreach ($project->activities as $act) {
                $data['stage'][] = $act->Stage ?? '';
                $data['activities'][] = $act->Specific_Activity ?? '';
                $data['timeframe'][] = $act->Time_Frame ?? '';
                $data['implementation_date'][] = $act->Implementation_Date ?? '';
                $data['point_person'][] = $act->Point_Persons ?? '';
                $data['status'][] = $act->status ?? 'Planned';
            }

            // Budgets
            $data['budget_activity'] = [];
            $data['budget_resources'] = [];
            $data['budget_partners'] = [];
            $data['budget_amount'] = [];
            foreach ($project->budgets as $b) {
                $data['budget_activity'][] = $b->Specific_Activity ?? '';
                $data['budget_resources'][] = $b->Resources_Needed ?? '';
                $data['budget_partners'][] = $b->Partner_Agencies ?? '';
                $data['budget_amount'][] = $b->Amount ?? '';
            }

            $validator = Validator::make($data, $rules, $messages);
            $validated = $validator->validate();
        } else {
            $validated = $request->validate($rules, $messages);
        }

        // file upload if provided (optional for draft)
        if ($request->hasFile('Project_Logo')) {
            $validated['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }

        $validated['student_id'] = $user->student->id;
        // For staff saving via staff edit, preserve the existing project status; students saving drafts set 'draft'
        if ($user->isStaff()) {
            $validated['Project_Status'] = $project->Project_Status;
        } else {
            $validated['Project_Status'] = 'draft';
        }
        // Build members arrays (minimal handling for draft)
        $memberResult = $this->buildMemberArraysForCreate($request, $validated);
        $validated['student_ids'] = $memberResult['student_ids'];
        $validated['member_roles'] = $memberResult['member_roles'] ?? [];

        DB::transaction(function() use ($validated, $request, &$project) {
            $project = Project::create([
                'Project_Name' => $validated['Project_Name'],
                'Project_Team_Name' => $validated['Project_Team_Name'],
                'Project_Logo' => $validated['Project_Logo'] ?? null,
                'Project_Component' => $validated['Project_Component'] ?? '',
                'Project_Solution' => $validated['Project_Solution'] ?? '',
                'Project_Goals' => $validated['Project_Goals'] ?? '',
                'Project_Target_Community' => $validated['Project_Target_Community'] ?? '',
                'Project_Expected_Outcomes' => $validated['Project_Expected_Outcomes'] ?? '',
                'Project_Problems' => $validated['Project_Problems'] ?? '',
                'Project_Status' => 'draft',
                'student_id' => $validated['student_id'],
                'student_ids' => $validated['student_ids'],
                'member_roles' => $validated['member_roles'],
                'Project_Section' => $validated['nstp_section'] ?? '',
            ]);

            // sync activities/budgets for a draft: allow incomplete but skip fully blank rows
            $this->syncActivities($project, $request, true);
            $this->syncBudgets($project, $request, true);
            // Persist computed completed state if all activities are completed
            $this->maybePersistCompleted($project);
        });

        return redirect()->route('projects.my')->with('success', 'Project saved as draft!');
    }

    /**
     * Store a submitted project (full validation).
     */
    public function storeSubmit(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->student) {
            return redirect()->back()->with('error', 'Only authenticated students can submit projects.');
        }

        $rules = $this->validateSubmitRules();
        $messages = $this->validationMessages();

        // storeSubmit is for new submissions — do not relax logo requirement here.

        $validated = $request->validate($rules, $messages);

        // file upload (required in rules if project has no existing logo)
        if ($request->hasFile('Project_Logo')) {
            $validated['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }

        $validated['student_id'] = $user->student->id;
        $validated['Project_Status'] = 'pending';

        // Build members arrays (stronger processing)
        $memberResult = $this->buildMemberArraysForCreate($request, $validated);
        $validated['student_ids'] = $memberResult['student_ids'];
        $validated['member_roles'] = $memberResult['member_roles'] ?? [];

        // Ensure the student doesn't already have another pending project
        $existingPending = Project::where('student_id', $validated['student_id'])
            ->where('Project_Status', 'pending')
            ->first();
        if ($existingPending) {
            return redirect()->back()->with('error', 'You already have a project pending for review. You can only have one pending project at a time.');
        }

        $project = null;
        DB::transaction(function() use ($validated, $request, &$project) {
            $project = Project::create([
                'Project_Name' => $validated['Project_Name'],
                'Project_Team_Name' => $validated['Project_Team_Name'],
                'Project_Logo' => $validated['Project_Logo'] ?? null,
                'Project_Component' => $validated['Project_Component'] ?? '',
                'Project_Solution' => $validated['Project_Solution'] ?? '',
                'Project_Goals' => $validated['Project_Goals'] ?? '',
                'Project_Target_Community' => $validated['Project_Target_Community'] ?? '',
                'Project_Expected_Outcomes' => $validated['Project_Expected_Outcomes'] ?? '',
                'Project_Problems' => $validated['Project_Problems'] ?? '',
                'Project_Status' => 'pending',
                'student_id' => $validated['student_id'],
                'student_ids' => $validated['student_ids'],
                'member_roles' => $validated['member_roles'],
                'Project_Section' => $validated['nstp_section'] ?? '',
            ]);

            // sync activities and budgets - strict because this is a submission
            $this->syncActivities($project, $request, false);
            $this->syncBudgets($project, $request, false);
            // Persist computed completed state if all activities are completed
            $this->maybePersistCompleted($project);
        });

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Failed to create project.');
        }

        return redirect()->route('projects.show', $project)->with('success', 'Project submitted successfully for review!');
    }

    /* -----------------------------------------------------------------
     | Update flows
     | ----------------------------------------------------------------- */

    /**
     * Update (save changes) for student/staff but treated as a draft update.
     * Students: save as draft (Project_Status becomes 'draft').
     * Staff: staff are not allowed to "save draft" for student projects — staff edits are "save changes" and keep status.
     */
    public function updateDraft(Request $request, Project $project)
    {
        $user = Auth::user();
        // Authorization: students can edit their own draft/rejected projects.
        // Staff edits are handled by the dedicated `updateStaff` method; this draft
        // handler is intended for student draft saves.

        // Ensure the student owns the project (skip this check for staff saving via staff edit flow)
        if (!$user->isStaff()) {
            if (!$user->isStudent() || $user->student->id !== $project->student_id) {
                abort(403, 'Unauthorized action.');
            }

            // Students can edit drafts and rejected projects only (not pending)
            if ($project->Project_Status === 'pending') {
                return redirect()->route('projects.show', $project)->with('error', 'Pending projects cannot be edited here.');
            }
        }

        $rules = $this->validateDraftRules();
        $messages = $this->validationMessages();
        $validated = $request->validate($rules, $messages);

        // Optional debug dump to help with diagnosing client payloads
        if (config('app.debug') || $request->input('debug_dump')) {
            try {
                $dump = [
                    'time' => now()->toDateTimeString(),
                    'route' => 'updateDraft',
                    'project' => $project->Project_ID ?? null,
                    'request' => $request->all(),
                    'validated' => $validated,
                ];
                Storage::put('debug/update-draft-' . ($project->Project_ID ?? 'new') . '-' . time() . '.json', json_encode($dump, JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
                Log::warning('Failed writing debug dump: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('Project_Logo')) {
            $validated['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }

        // Determine resulting status:
        // - Staff using the staff-edit flow (with hidden `staff_save`) should preserve the existing status,
        //   but if the existing status is 'approved' we convert it to 'current' so staff edits don't keep 'approved'.
        // - Students saving should normally set to 'draft', EXCEPT when the project is already 'rejected':
        //   students editing a rejected project should be able to save draft edits without changing its
        //   status from 'rejected'. This preserves rejection state while allowing updates.
        if ($user->isStaff() && $request->input('staff_save')) {
            // Preserve existing status when staff saves; do not convert 'approved' -> 'current'.
            $projectStatus = $project->Project_Status;
        } else {
            if (!$user->isStaff() && $project->Project_Status === 'rejected') {
                // Preserve rejected status for student edits
                $projectStatus = 'rejected';
            } else {
                $projectStatus = 'draft';
            }
        }

        // Build member arrays
        $memberResult = $this->buildMemberArraysForUpdate($request, $project);
        $studentIds = $memberResult['student_ids'];
        $memberRoles = $memberResult['member_roles'];

        // Debug incoming activity/budget ids to help track why existing rows may not be updated
        Log::debug('updateDraft payload ids', [
            'project' => $project->Project_ID ?? null,
            'activity_id' => $request->input('activity_id'),
            'budget_id' => $request->input('budget_id'),
            'computed_project_status' => $projectStatus,
        ]);

        DB::transaction(function() use ($project, $validated, $request, $studentIds, $memberRoles, $projectStatus, $user) {
            $project->update([
                'Project_Name' => $validated['Project_Name'],
                'Project_Team_Name' => $validated['Project_Team_Name'],
                'Project_Logo' => $validated['Project_Logo'] ?? $project->Project_Logo,
                'Project_Component' => $validated['Project_Component'] ?? $project->Project_Component,
                'Project_Solution' => $validated['Project_Solution'] ?? $project->Project_Solution,
                'Project_Goals' => $validated['Project_Goals'] ?? $project->Project_Goals,
                'Project_Target_Community' => $validated['Project_Target_Community'] ?? $project->Project_Target_Community,
                'Project_Expected_Outcomes' => $validated['Project_Expected_Outcomes'] ?? $project->Project_Expected_Outcomes,
                'Project_Problems' => $validated['Project_Problems'] ?? $project->Project_Problems,
                'Project_Status' => $projectStatus,
                'student_ids' => $studentIds,
                'member_roles' => $memberRoles,
                'Project_Section' => $request->input('nstp_section') ?? $project->Project_Section,
            ]);

            $this->syncActivities($project, $request, true);
            $this->syncBudgets($project, $request, true);
            // Persist computed completed state if all activities are completed
            $this->maybePersistCompleted($project);
        });

        // If this was a staff-save, return a neutral 'updated' message instead of 'Draft saved'
        if ($user->isStaff() && $request->input('staff_save')) {
            return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
        }

        return redirect()->route('projects.show', $project)->with('success', 'Draft saved successfully!');
    }

    /**
     * Update a project for submission (student submits final) or staff changes.
     * Students: transition draft/rejected -> pending when submitting.
     * Staff: if editing, preserve status (unless staff explicitly sets Project_Status)
     */
    public function updateSubmit(Request $request, Project $project)
    {
        $user = Auth::user();

        // Authorization: students can submit their own projects; staff can edit any project
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if (!$user->isStaff()) {
            // Ensure student owns the project
            if (!$user->isStudent() || $user->student->id !== $project->student_id) {
                abort(403, 'Unauthorized action.');
            }
            // Students cannot edit pending projects here
            if ($project->Project_Status === 'pending') {
                return redirect()->route('projects.show', $project)->with('error', 'Pending projects cannot be edited.');
            }
        } else {
            // staff editing; staff should be allowed but staff edits should not create drafts.
        }

        $rules = $this->validateSubmitRules();
        $messages = $this->validationMessages();

        // If updating (resubmission) and the project already has a saved logo,
        // allow omitting the file input so the existing logo is retained.
        if (!$request->hasFile('Project_Logo') && !empty($project->Project_Logo)) {
            $rules['Project_Logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $validated = $request->validate($rules, $messages);

        // Optional debug dump to help with diagnosing client payloads
        if (config('app.debug') || $request->input('debug_dump')) {
            try {
                $dump = [
                    'time' => now()->toDateTimeString(),
                    'route' => 'updateSubmit',
                    'project' => $project->Project_ID ?? null,
                    'request' => $request->all(),
                    'validated' => $validated,
                ];
                Storage::put('debug/update-submit-' . ($project->Project_ID ?? 'new') . '-' . time() . '.json', json_encode($dump, JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
                Log::warning('Failed writing debug dump: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('Project_Logo')) {
            $validated['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }

        // Compute new status
        if ($user->isStaff()) {
            // Staff editing: preserve unless they explicitly set to pending
            if ($request->input('Project_Status') === 'pending' || $request->input('submit_project')) {
                $projectStatus = 'pending';
            } else {
                $projectStatus = $project->Project_Status;
            }
        } else {
            // Student submitting: change to pending
            $projectStatus = 'pending';
        }

        // Preserve 'approved' as approved; do not normalize to 'current'.

        // Build member arrays
        $memberResult = $this->buildMemberArraysForUpdate($request, $project);
        $studentIds = $memberResult['student_ids'];
        $memberRoles = $memberResult['member_roles'];

        // Resubmission handling
        $isResubmission = $project->Project_Status === 'rejected' && $projectStatus === 'pending';

        DB::transaction(function() use ($project, $validated, $request, $studentIds, $memberRoles, $projectStatus, $isResubmission) {
            // Debug incoming activity/budget ids for submit flow
            Log::debug('updateSubmit payload ids', [
                'project' => $project->Project_ID ?? null,
                'activity_id' => $request->input('activity_id'),
                'budget_id' => $request->input('budget_id'),
            ]);

            $update = [
                'Project_Name' => $validated['Project_Name'],
                'Project_Team_Name' => $validated['Project_Team_Name'],
                'Project_Logo' => $validated['Project_Logo'] ?? $project->Project_Logo,
                'Project_Solution' => $validated['Project_Solution'] ?? $project->Project_Solution,
                'Project_Goals' => $validated['Project_Goals'] ?? $project->Project_Goals,
                'Project_Target_Community' => $validated['Project_Target_Community'] ?? $project->Project_Target_Community,
                'Project_Expected_Outcomes' => $validated['Project_Expected_Outcomes'] ?? $project->Project_Expected_Outcomes,
                'Project_Problems' => $validated['Project_Problems'] ?? $project->Project_Problems,
                'Project_Status' => $projectStatus,
                'student_ids' => $studentIds,
                'member_roles' => $memberRoles,
                'Project_Section' => $request->input('nstp_section') ?? $project->Project_Section,
            ];

            if ($isResubmission) {
                $update['is_resubmission'] = true;
                $update['resubmission_count'] = ($project->resubmission_count ?? 0) + 1;
            }

            $project->update($update);

            $this->syncActivities($project, $request, false);
            $this->syncBudgets($project, $request, false);
            // Persist computed completed state if all activities are completed
            $this->maybePersistCompleted($project);
        });

        $message = $projectStatus === 'pending'
            ? ($isResubmission ? 'Project resubmitted successfully for review!' : 'Project submitted successfully for review!')
            : 'Project updated successfully!';

        return redirect()->route('projects.show', $project)->with('success', $message);
    }

    /**
     * Handle staff edits as a proper UPDATE (CRUD) operation.
     * Staff may edit any project; preserve existing status unless staff explicitly sets it.
     */
    public function updateStaff(Request $request, Project $project)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Unauthorized action.');
        }

        // Use draft rules for flexible editing by staff; messages are reused
        $rules = $this->validateDraftRules();
        $messages = $this->validationMessages();
        $validated = $request->validate($rules, $messages);

        // Optional debug dump to capture raw incoming arrays for reproduction runs
        if (config('app.debug') || $request->input('debug_dump')) {
            try {
                $dump = [
                    'time' => now()->toDateTimeString(),
                    'route' => 'updateStaff',
                    'project' => $project->Project_ID ?? null,
                    'request' => $request->all(),
                    'validated' => $validated,
                ];
                Storage::put('debug/update-staff-' . ($project->Project_ID ?? 'new') . '-' . time() . '.json', json_encode($dump, JSON_PRETTY_PRINT));
            } catch (\Exception $e) {
                Log::warning('Failed writing debug dump (updateStaff): ' . $e->getMessage());
            }
        }

        if ($request->hasFile('Project_Logo')) {
            $validated['Project_Logo'] = $request->file('Project_Logo')->store('project_logos', 'public');
        }

        // Preserve existing status unless staff explicitly sets a new status
        $projectStatus = $request->input('Project_Status') ?? $project->Project_Status;

        // Build member arrays
        $memberResult = $this->buildMemberArraysForUpdate($request, $project);
        $studentIds = $memberResult['student_ids'];
        $memberRoles = $memberResult['member_roles'];

        // Log incoming arrays to help debug any remaining alignment issues
        Log::debug('updateStaff payload ids', [
            'project' => $project->Project_ID ?? null,
            'activity_id' => $request->input('activity_id'),
            'budget_id' => $request->input('budget_id'),
        ]);

        DB::transaction(function() use ($project, $validated, $request, $studentIds, $memberRoles, $projectStatus) {
            $project->update([
                'Project_Name' => $validated['Project_Name'],
                'Project_Team_Name' => $validated['Project_Team_Name'],
                'Project_Logo' => $validated['Project_Logo'] ?? $project->Project_Logo,
                'Project_Component' => $validated['Project_Component'] ?? $project->Project_Component,
                'Project_Solution' => $validated['Project_Solution'] ?? $project->Project_Solution,
                'Project_Goals' => $validated['Project_Goals'] ?? $project->Project_Goals,
                'Project_Target_Community' => $validated['Project_Target_Community'] ?? $project->Project_Target_Community,
                'Project_Expected_Outcomes' => $validated['Project_Expected_Outcomes'] ?? $project->Project_Expected_Outcomes,
                'Project_Problems' => $validated['Project_Problems'] ?? $project->Project_Problems,
                'Project_Status' => $projectStatus,
                'student_ids' => $studentIds,
                'member_roles' => $memberRoles,
                'Project_Section' => $request->input('nstp_section') ?? $project->Project_Section,
            ]);

            // Staff edits: allow partial rows (don't require full submission fields)
            $this->syncActivities($project, $request, true);
            $this->syncBudgets($project, $request, true);
            $this->maybePersistCompleted($project);
        });

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
    }

    /* -----------------------------------------------------------------
     | Helpers: validation rules and messages
     | ----------------------------------------------------------------- */

    protected function validateDraftRules()
    {
        // For drafts: require only the owner (handled elsewhere), project title and team name.
        return [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            // Allow students to save project detail fields when saving drafts
            'Project_Component' => 'nullable|string|max:50',
            'Project_Problems' => 'nullable|string',
            'Project_Solution' => 'nullable|string',
            'Project_Goals' => 'nullable|string',
            'Project_Target_Community' => 'nullable|string',
            'Project_Expected_Outcomes' => 'nullable|string',
            // Basic arrays allowed but not required to have content
            'stage' => 'nullable|array',
            'activities' => 'nullable|array',
            'timeframe' => 'nullable|array',
            'point_person' => 'nullable|array',
            'status' => 'nullable|array',
            'budget_activity' => 'nullable|array',
            'budget_resources' => 'nullable|array',
            'budget_partners' => 'nullable|array',
            'budget_amount' => 'nullable|array',
            'member_email' => 'nullable|array',
            'member_student_id' => 'nullable|array',
            'member_role' => 'nullable|array',
            'Project_Logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nstp_section' => 'nullable|string',
        ];
    }

    protected function validateSubmitRules()
    {
        // Strict validation for submissions
        $rules = [
            'Project_Name' => 'required|string|max:255',
            'Project_Team_Name' => 'required|string|max:255',
            'Project_Logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'Project_Component' => 'required|string',
            'nstp_section' => 'required|string',
            'Project_Solution' => 'required|string',
            'Project_Goals' => 'required|string',
            'Project_Target_Community' => 'required|string',
            'Project_Expected_Outcomes' => 'required|string',
            'Project_Problems' => 'required|string',
            // Member fields (at least owner present)
            'member_student_id' => 'nullable|array',
            'member_student_id.*' => 'nullable|integer|exists:students,id',
            'member_role' => 'nullable|array',
            'member_role.*' => 'nullable|string|max:255',
            'member_email' => 'nullable|array',
            'member_email.*' => 'nullable|email|max:255',
            // Activity data - require at least one valid row
            'stage' => 'required|array|min:1',
            'stage.*' => 'required|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*' => 'required|string',
            'timeframe' => 'required|array|min:1',
            'timeframe.*' => 'required|string|max:255',
            'point_person' => 'required|array|min:1',
            'point_person.*' => 'required|string|max:255',
            'status' => 'required|array|min:1',
            'status.*' => ['required', Rule::in(['Planned','Ongoing','Completed'])],
            // Budgets: custom per-row enforcement handled below
            'budget_activity' => 'nullable|array',
            'budget_resources' => 'nullable|array',
            'budget_partners' => 'nullable|array',
            'budget_amount' => 'nullable|array',
        ];

        // Dynamically add budget row required rules if any field present in a row
        // We'll not enumerate them here; sync functions will check and we also add messages
        return $rules;
    }

    protected function validationMessages()
    {
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
            'Project_Logo.required' => 'The project logo is required for submission.',
        ];

        for ($i = 0; $i < 40; $i++) {
            $messages["budget_activity.$i.required"] = 'The activity field is required when budget information is provided.';
            $messages["budget_resources.$i.required"] = 'The resources needed field is required when budget information is provided.';
            $messages["budget_partners.$i.required"] = 'The partner agencies field is required when budget information is provided.';
            $messages["budget_amount.$i.required"] = 'The amount field is required when budget information is provided.';
        }

        return $messages;
    }

    /* -----------------------------------------------------------------
     | Helpers: sync activities, budgets and members
     | ----------------------------------------------------------------- */

    /**
     * Sync activities from the request into the project.
     * @param Project $project
     * @param Request $request
     * @param bool $allowPartialRows - true for draft, false for submission (strict)
     */
    protected function syncActivities(Project $project, Request $request, bool $allowPartialRows = true)
    {
        // Normalize inputs to arrays to avoid undefined-index warnings
        $stages = is_array($request->input('stage', [])) ? $request->input('stage', []) : [];
        $activities = is_array($request->input('activities', [])) ? $request->input('activities', []) : [];
        $timeframes = is_array($request->input('timeframe', [])) ? $request->input('timeframe', []) : [];
        $implDates = is_array($request->input('implementation_date', [])) ? $request->input('implementation_date', []) : [];
        $points = is_array($request->input('point_person', [])) ? $request->input('point_person', []) : [];
        $statuses = is_array($request->input('status', [])) ? $request->input('status', []) : [];
        $activityIds = is_array($request->input('activity_id', [])) ? $request->input('activity_id', []) : [];
        $rowKeys = is_array($request->input('activity_row_key', [])) ? $request->input('activity_row_key', []) : [];
        $deletedActivityIds = is_array($request->input('deleted_activity_id', [])) ? $request->input('deleted_activity_id', []) : [];
        $deletedActivityRowKeys = is_array($request->input('deleted_activity_row_key', [])) ? $request->input('deleted_activity_row_key', []) : [];

        // Log incoming activity arrays for debugging
        Log::debug('syncActivities input', [
            'project' => $project->Project_ID ?? null,
            'stages_count' => count($stages),
            'activities_count' => count($activities),
            'timeframes_count' => count($timeframes),
            'points_count' => count($points),
            'implDates_count' => count($implDates),
            'allowPartialRows' => $allowPartialRows,
        ]);

        // Defensive: if everything is empty, nothing to do
        if (empty($stages) && empty($activities) && empty($timeframes) && empty($points) && empty($implDates)) {
            Log::debug('syncActivities: nothing to sync (all arrays empty)', ['project' => $project->Project_ID ?? null]);
            return;
        }

        // existing IDs to track deletions
        $existingIds = $project->activities()->pluck('Activity_ID')->toArray();
        $processed = [];
        $processedIds = [];
        $processedRowKeys = [];

        $seen = [];

        $max = max(count($stages), count($activities), count($timeframes), count($implDates), count($points), count($statuses), count($activityIds));
        for ($i = 0; $i < $max; $i++) {
            $stage = trim($stages[$i] ?? '');
            $act = trim($activities[$i] ?? '');
            $tf = trim($timeframes[$i] ?? '');
            $impl = $implDates[$i] ?? null;
            $pp = trim($points[$i] ?? '');
            $st = $statuses[$i] ?? 'Planned';
            $id = $activityIds[$i] ?? null;
            $rowKey = $rowKeys[$i] ?? null;

            // If rowKey encodes an id like 'act-123', prefer that id when id not provided
            if (empty($id) && !empty($rowKey) && preg_match('/^act-(\d+)$/', $rowKey, $m)) {
                $id = intval($m[1]);
            }

            // Skip duplicate rowKeys (desktop+mobile duplication)
            if (!empty($rowKey) && in_array($rowKey, $processedRowKeys)) {
                Log::debug('syncActivities: skipping duplicate rowKey', ['project' => $project->Project_ID ?? null, 'row_key' => $rowKey]);
                continue;
            }

            // determine if this row has any content
            $hasAny = ($stage !== '' || $act !== '' || $tf !== '' || $pp !== '' || !empty($impl));

            if (!$hasAny && !$allowPartialRows) {
                // for submissions skip completely-empty rows
                continue;
            }

            // For submissions, require stage & activity & point person (validation already enforced)
            if (!$allowPartialRows && ($stage === '' && $act === '')) {
                // skip invalid row (should have been caught by validator); but be safe
                continue;
            }

            // Deduplication key
            $key = strtolower(implode('|', [$stage, $act, $tf, $pp]));

            if ($key !== '' && isset($seen[$key])) {
                // duplicate - skip
                continue;
            }
            if ($key !== '') $seen[$key] = true;

            $data = [
                'Stage' => $stage,
                'Specific_Activity' => $act,
                'Time_Frame' => $tf,
                'Implementation_Date' => $impl,
                'Point_Persons' => $pp,
                'status' => $st,
                'project_id' => $project->Project_ID,
            ];

            if (!empty($id) && is_numeric($id)) {
                $existing = Activity::where('Activity_ID', $id)->where('project_id', $project->Project_ID)->first();
                if ($existing) {
                    // If this row is completely empty and we're in draft mode, skip updating existing
                    if (!$hasAny && $allowPartialRows) {
                        continue;
                    }
                    // Build update payload carefully so we don't clear existing Implementation_Date
                    $updateData = $data;
                    // If implementation date is empty in the incoming request, preserve existing value
                    if (empty($impl) && !is_null($existing->Implementation_Date)) {
                        unset($updateData['Implementation_Date']);
                    }

                    // Prevent staff from changing the status if the activity was already marked completed by students
                    $existingStatus = strtolower(trim((string)($existing->status ?? '')));
                    $incomingStatus = strtolower(trim((string)($st ?? '')));
                    if ($existingStatus === 'completed' && $incomingStatus !== 'completed') {
                        // keep the existing status
                        $updateData['status'] = $existing->status;
                    }

                    $existing->update($updateData);
                    $processed[] = $existing->Activity_ID;
                    $processedIds[] = $existing->Activity_ID;
                    if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                    continue;
                }
            }

            // create new only if the row has any content (avoid creating empty rows on draft save)
            if ($hasAny) {
                // First, attempt a content-based lookup to update an existing row when ids/row-keys are missing
                try {
                    $lookup = Activity::where('project_id', $project->Project_ID)
                        ->whereRaw('LOWER(TRIM(COALESCE(Specific_Activity, ?))) = LOWER(TRIM(?))', ['', $act])
                        ->whereRaw('LOWER(TRIM(COALESCE(Point_Persons, ?))) = LOWER(TRIM(?))', ['', $pp])
                        ->whereRaw('LOWER(TRIM(COALESCE(Time_Frame, ?))) = LOWER(TRIM(?))', ['', $tf])
                        ->first();
                    if ($lookup) {
                        if (in_array($lookup->Activity_ID, $processedIds)) {
                            Log::debug('syncActivities: content-lookup matched id already processed; skipping', ['project' => $project->Project_ID ?? null, 'activity_id' => $lookup->Activity_ID]);
                        } else {
                            // Update the found record instead of creating a duplicate
                            $updateData = $data;
                            if (empty($impl) && !is_null($lookup->Implementation_Date)) {
                                unset($updateData['Implementation_Date']);
                            }
                            $lookup->update($updateData);
                            $processed[] = $lookup->Activity_ID;
                            $processedIds[] = $lookup->Activity_ID;
                            if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                            Log::debug('syncActivities: applied content-lookup update', ['project' => $project->Project_ID ?? null, 'activity_id' => $lookup->Activity_ID]);
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('syncActivities: content lookup failed', ['project' => $project->Project_ID ?? null, 'error' => $e->getMessage()]);
                }

                try {
                    // Temporary debug: log payload attempted for creation
                    Log::debug('syncActivities: create payload', array_merge(['project' => $project->Project_ID ?? null], $data));
                    $created = Activity::create($data);
                    $processed[] = $created->Activity_ID;
                    $processedIds[] = $created->Activity_ID;
                    if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                    Log::debug('syncActivities: created activity', ['project' => $project->Project_ID ?? null, 'activity_id' => $created->Activity_ID]);
                } catch (\Exception $e) {
                    Log::error('syncActivities: failed creating activity', ['project' => $project->Project_ID ?? null, 'error' => $e->getMessage(), 'data' => $data]);
                }
            }
        }

        // delete omitted existing activities (existing minus processedIds)
        $toDelete = array_diff($existingIds, $processedIds);
        // include explicitly deleted ids
        if (!empty($deletedActivityIds)) {
            $toDelete = array_unique(array_merge($toDelete, array_map('intval', $deletedActivityIds)));
        }
        // include deleted row keys that encode ids like 'act-<id>'
        if (!empty($deletedActivityRowKeys)) {
            foreach ($deletedActivityRowKeys as $k) {
                if (preg_match('/^act-(\d+)$/', $k, $m)) {
                    $toDelete[] = intval($m[1]);
                }
            }
            $toDelete = array_unique($toDelete);
        }
        if (!empty($toDelete)) {
            Activity::whereIn('Activity_ID', $toDelete)->where('project_id', $project->Project_ID)->delete();
        }
    }

    /**
     * Sync budgets from the request into the project.
     * Budgets may be associated with activities or standalone (as in your existing schema).
     */
    protected function syncBudgets(Project $project, Request $request, bool $allowPartialRows = true)
    {
        $bActivities = is_array($request->input('budget_activity', [])) ? $request->input('budget_activity', []) : [];
        $bResources = is_array($request->input('budget_resources', [])) ? $request->input('budget_resources', []) : [];
        $bPartners = is_array($request->input('budget_partners', [])) ? $request->input('budget_partners', []) : [];
        $bAmounts = is_array($request->input('budget_amount', [])) ? $request->input('budget_amount', []) : [];
        // Normalize amount inputs: accept formats like "15,000", "15,000.00", "15000.00",
        // currency-prefixed values like "₱15,000" or "$1,500.00", and parentheses for negatives
        foreach ($bAmounts as $k => $v) {
            if (is_string($v)) {
                $val = trim($v);

                // Convert (1,234.56) -> -1234.56
                if (preg_match('/^\s*\((.*)\)\s*$/', $val, $m)) {
                    $val = '-' . $m[1];
                }

                // Remove common currency symbols, thousands separators and normal whitespace
                $val = str_replace(['₱', '$', ',', ' '], '', $val);

                // Remove any character except digits, dot and minus
                $val = preg_replace('/[^\d\.\-]/', '', $val);

                // If there are multiple dots, keep the first and remove the rest
                if (substr_count($val, '.') > 1) {
                    $parts = explode('.', $val);
                    $val = $parts[0] . '.' . implode('', array_slice($parts, 1));
                }

                // Normalize empty-like values
                if ($val === '' || $val === '-' || $val === '.') {
                    $bAmounts[$k] = '';
                } else {
                    // Cast to float to normalize numeric representation, then store as string
                    // This removes grouping separators while keeping decimal value.
                    $num = (float) $val;
                    $bAmounts[$k] = (string) $num;
                }
            }
        }
        $bIds = is_array($request->input('budget_id', [])) ? $request->input('budget_id', []) : [];
        $bRowKeys = is_array($request->input('budget_row_key', [])) ? $request->input('budget_row_key', []) : [];
        $deletedBudgetIds = is_array($request->input('deleted_budget_id', [])) ? $request->input('deleted_budget_id', []) : [];
        $deletedBudgetRowKeys = is_array($request->input('deleted_budget_row_key', [])) ? $request->input('deleted_budget_row_key', []) : [];

        Log::debug('syncBudgets input', [
            'project' => $project->Project_ID ?? null,
            'bActivities_count' => count($bActivities),
            'bResources_count' => count($bResources),
            'bPartners_count' => count($bPartners),
            'bAmounts_count' => count($bAmounts),
            'allowPartialRows' => $allowPartialRows,
        ]);

        if (empty($bActivities) && empty($bResources) && empty($bPartners) && empty($bAmounts)) {
            Log::debug('syncBudgets: nothing to sync (all arrays empty)', ['project' => $project->Project_ID ?? null]);
            return;
        }

        $existingIds = $project->budgets()->pluck('Budget_ID')->toArray();
        $processed = [];
        $processedIds = [];
        $processedRowKeys = [];
        $seen = [];

        $max = max(count($bActivities), count($bResources), count($bPartners), count($bAmounts), count($bIds));
        for ($i = 0; $i < $max; $i++) {
            $act = trim($bActivities[$i] ?? '');
            $res = trim($bResources[$i] ?? '');
            $par = trim($bPartners[$i] ?? '');
            $amt = $bAmounts[$i] ?? '';
            // normalize amount for checks and storage
            $amtNormalized = is_string($amt) ? trim($amt) : $amt;
            $id  = $bIds[$i] ?? null;
            $rowKey = $bRowKeys[$i] ?? null;

            // If rowKey encodes an id like 'bud-123', prefer that id when id not provided
            if (empty($id) && !empty($rowKey) && preg_match('/^bud-(\d+)$/', $rowKey, $m)) {
                $id = intval($m[1]);
            }

            // Skip duplicate rowKeys submitted by duplicate DOM representations
            if (!empty($rowKey) && in_array($rowKey, $processedRowKeys)) {
                Log::debug('syncBudgets: skipping duplicate rowKey', ['project' => $project->Project_ID ?? null, 'row_key' => $rowKey]);
                continue;
            }

            $hasAny = ($act !== '' || $res !== '' || $par !== '' || ($amtNormalized !== '' && floatval($amtNormalized) != 0));

            if (!$hasAny && !$allowPartialRows) {
                // For submissions: skip empty budget rows
                continue;
            }

            // If any field present in this row but not all and we are in strict mode, enforce all fields
            if (!$allowPartialRows) {
                if ($hasAny && ($act === '' || $res === '' || $par === '' || $amtNormalized === '' || !is_numeric($amtNormalized))) {
                    // fail-safe: validator should have prevented this; skip this row to avoid incomplete DB entries
                    continue;
                }
            }

            // Dedupe key
            $key = strtolower(implode('|', [$act, $res, $par, (string)$amtNormalized]));
            if ($key !== '' && isset($seen[$key])) {
                continue;
            }
            if ($key !== '') $seen[$key] = true;

                $data = [
                'project_id' => $project->Project_ID,
                'Specific_Activity' => $act,
                'Resources_Needed' => $res,
                'Partner_Agencies' => $par,
                    'Amount' => ($amtNormalized !== '' && is_numeric($amtNormalized)) ? (float)$amtNormalized : 0,
            ];

            if (!empty($id) && is_numeric($id)) {
                $existing = Budget::where('Budget_ID', $id)->where('project_id', $project->Project_ID)->first();
                if ($existing) {
                    // If this row is completely empty and we're in draft mode, skip updating existing
                    if (!$hasAny && $allowPartialRows) {
                        continue;
                    }
                    $existing->update($data);
                    $processed[] = $existing->Budget_ID;
                    $processedIds[] = $existing->Budget_ID;
                    if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                    continue;
                }
            }

            // If an explicit id was not provided or didn't match, try content-based lookup
            // to avoid creating duplicate budget rows when client ids are misaligned.
            if (empty($id) || !is_numeric($id) || empty($existing)) {
                try {
                    $lookup = Budget::where('project_id', $project->Project_ID)
                        ->whereRaw('LOWER(COALESCE(Specific_Activity, ?)) = LOWER(COALESCE(?, ?))', ['', $act, ''])
                        ->whereRaw('LOWER(COALESCE(Resources_Needed, ?)) = LOWER(COALESCE(?, ?))', ['', $res, ''])
                        ->whereRaw('LOWER(COALESCE(Partner_Agencies, ?)) = LOWER(COALESCE(?, ?))', ['', $par, ''])
                        ->where('Amount', ($amtNormalized !== '' && is_numeric($amtNormalized)) ? (float)$amtNormalized : 0)
                        ->first();

                    if ($lookup) {
                        // Avoid re-processing same lookup record
                        if (in_array($lookup->Budget_ID, $processedIds)) {
                            Log::debug('syncBudgets: content-lookup matched id already processed; skipping', ['project' => $project->Project_ID ?? null, 'budget_id' => $lookup->Budget_ID]);
                            continue;
                        }
                        $lookup->update($data);
                        $processed[] = $lookup->Budget_ID;
                        $processedIds[] = $lookup->Budget_ID;
                        if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                        continue;
                    }
                } catch (\Exception $e) {
                    Log::warning('syncBudgets: content lookup failed', ['project' => $project->Project_ID ?? null, 'error' => $e->getMessage()]);
                }
            }

            // create new only if the row has any content (avoid creating empty rows on draft save)
            // Require Specific_Activity and a numeric amount to create new budget rows
            $shouldCreate = $hasAny && trim($act) !== '' && $amtNormalized !== '' && is_numeric($amtNormalized);
                    if ($shouldCreate) {
                try {
                    // Temporary debug: log payload attempted for creation
                    Log::debug('syncBudgets: create payload', array_merge(['project' => $project->Project_ID ?? null], $data));
                    // Final fallback: try a looser lookup on Specific_Activity (and amount when provided)
                    try {
                        if (trim($act) !== '') {
                            $qb = Budget::where('project_id', $project->Project_ID)
                                ->whereRaw('LOWER(TRIM(COALESCE(Specific_Activity, ?))) = LOWER(TRIM(?))', ['', $act]);
                            if ($amtNormalized !== '' && is_numeric($amtNormalized)) {
                                $qb = $qb->where('Amount', (float)$amtNormalized);
                            }
                            $fallback = $qb->first();
                            if ($fallback && !in_array($fallback->Budget_ID, $processedIds)) {
                                $fallback->update($data);
                                $processed[] = $fallback->Budget_ID;
                                $processedIds[] = $fallback->Budget_ID;
                                if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                                Log::debug('syncBudgets: applied fallback update instead of create', ['project' => $project->Project_ID ?? null, 'budget_id' => $fallback->Budget_ID]);
                                continue;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('syncBudgets: fallback lookup failed', ['project' => $project->Project_ID ?? null, 'error' => $e->getMessage()]);
                    }
                    $created = Budget::create($data);
                    $processed[] = $created->Budget_ID;
                    $processedIds[] = $created->Budget_ID;
                    if (!empty($rowKey)) $processedRowKeys[] = $rowKey;
                    Log::debug('syncBudgets: created budget', ['project' => $project->Project_ID ?? null, 'budget_id' => $created->Budget_ID]);
                } catch (\Exception $e) {
                    Log::error('syncBudgets: failed creating budget', ['project' => $project->Project_ID ?? null, 'error' => $e->getMessage(), 'data' => $data]);
                }
            }
        }

        // delete omitted existing budgets (existing minus processedIds)
        $toDelete = array_diff($existingIds, $processedIds);
        if (!empty($deletedBudgetIds)) {
            $toDelete = array_unique(array_merge($toDelete, array_map('intval', $deletedBudgetIds)));
        }
        if (!empty($deletedBudgetRowKeys)) {
            foreach ($deletedBudgetRowKeys as $k) {
                if (preg_match('/^bud-(\d+)$/', $k, $m)) {
                    $toDelete[] = intval($m[1]);
                }
            }
            $toDelete = array_unique($toDelete);
        }
        if (!empty($toDelete)) {
            Budget::whereIn('Budget_ID', $toDelete)->where('project_id', $project->Project_ID)->delete();
        }
    }

    /**
     * Build student_ids and member_roles during creation (store flows).
     * Minimal mapping: owner included; map emails to students if possible.
     */
    protected function buildMemberArraysForCreate(Request $request, array $validated)
    {
        $studentIds = [];
        $memberRoles = [];

        $ownerId = $validated['student_id'] ?? null;
        if ($ownerId) $studentIds[] = $ownerId;

        // Map member_student_id if supplied (explicit IDs)
        if ($request->has('member_student_id')) {
            foreach ($request->input('member_student_id', []) as $idx => $sid) {
                if (is_numeric($sid) && !in_array($sid, $studentIds)) {
                    $studentIds[] = $sid;
                    if ($role = $request->input("member_role.$idx")) {
                        $memberRoles[$sid] = $role;
                    }
                }
            }
        }

        // Map by email -> student if provided (prefer explicit ids)
        $emails = array_filter($request->input('member_email', []));
        $roles  = $request->input('member_role', []);
        if (!empty($emails)) {
            $students = Student::whereHas('user', function($q) use ($emails) {
                $q->whereIn('user_Email', $emails);
            })->with('user')->get();

            foreach ($students as $s) {
                if (!in_array($s->id, $studentIds)) {
                    $studentIds[] = $s->id;
                }
                $email = $s->user->user_Email ?? null;
                if ($email) {
                    // Find first matching index in request
                    $idx = array_search($email, $emails);
                    if ($idx !== false && isset($roles[$idx]) && $roles[$idx] !== '') {
                        $memberRoles[$s->id] = $roles[$idx];
                    }
                }
            }
        }

        return [
            'student_ids' => $studentIds,
            'member_roles' => $memberRoles
        ];
    }

    /**
     * Build student_ids and member_roles during update flows.
     * Preserves existing owner and existing student_ids where appropriate.
     */
    protected function buildMemberArraysForUpdate(Request $request, Project $project)
    {
        // Start with the owner and existing project members
        $existing = $project->student_ids ?? [];
        if (!is_array($existing)) {
            $existing = json_decode($existing, true) ?: [];
        }

        $studentIds = is_array($existing) && count($existing) ? $existing : [$project->student_id];
        $memberRoles = $project->member_roles ?? [];

        // Handle explicit member_student_id
        if ($request->has('member_student_id')) {
            foreach ($request->input('member_student_id', []) as $idx => $sid) {
                if (is_numeric($sid) && !in_array($sid, $studentIds)) {
                    $studentIds[] = $sid;
                }
                if (isset($request->input('member_role', [])[$idx]) && $request->input('member_role')[$idx] !== '') {
                    $memberRoles[$sid] = $request->input('member_role')[$idx];
                }
            }
        }

        // Map by email as well
        $emails = array_filter($request->input('member_email', []));
        $roles  = $request->input('member_role', []);
        if (!empty($emails)) {
            $students = Student::whereHas('user', function($q) use ($emails) {
                $q->whereIn('user_Email', $emails);
            })->with('user')->get();

            foreach ($students as $s) {
                if (!in_array($s->id, $studentIds)) {
                    $studentIds[] = $s->id;
                }
                $email = $s->user->user_Email ?? null;
                if ($email) {
                    $idx = array_search($email, $emails);
                    if ($idx !== false && isset($roles[$idx]) && $roles[$idx] !== '') {
                        $memberRoles[$s->id] = $roles[$idx];
                    }
                }
            }
        }

        return [
            'student_ids' => $studentIds,
            'member_roles' => $memberRoles
        ];
    }

    /* -----------------------------------------------------------------
     | Minimal stubs for show/edit/destroy so controller remains usable
     | ----------------------------------------------------------------- */

    public function show(Project $project)
    {
        // Allow staff to view any project. Students may view their own projects.
        // Additionally, allow students to view public/current projects on the listing
        // pages (including 'current', 'approved', 'completed'). This enables students
        // to open project details from the Current Projects page.
        $user = Auth::user();
        if ($user && $user->isStudent()) {
            if ($user->student->id === $project->student_id) {
                // owner - allowed
            } else {
                // allow viewing if project is public (current/approved/completed)
                $publicStatuses = ['current', 'approved', 'completed'];
                if (!in_array(strtolower((string)$project->Project_Status), $publicStatuses)) {
                    abort(403, 'Unauthorized action.');
                }
            }
        }

        // Load relations used on the page
        $project->load(['activities', 'budgets']);

        // Build a validation payload from saved project data so we can determine
        // whether the project is ready for submission without relying on client JS.
        $data = [];
        $data['Project_Name'] = $project->Project_Name ?? null;
        $data['Project_Team_Name'] = $project->Project_Team_Name ?? null;
        $data['Project_Component'] = $project->Project_Component ?? null;
        $data['nstp_section'] = $project->Project_Section ?? null;
        $data['Project_Solution'] = $project->Project_Solution ?? null;
        $data['Project_Goals'] = $project->Project_Goals ?? null;
        $data['Project_Target_Community'] = $project->Project_Target_Community ?? null;
        $data['Project_Expected_Outcomes'] = $project->Project_Expected_Outcomes ?? null;
        $data['Project_Problems'] = $project->Project_Problems ?? null;
        $data['Project_Logo'] = $project->Project_Logo ?? null;

        // Members
        $members = is_callable([$project, 'members']) ? $project->members() : [];
        $data['member_student_id'] = [];
        $data['member_role'] = [];
        $data['member_email'] = [];
        foreach ($members as $m) {
            $data['member_student_id'][] = $m['student_id'] ?? null;
            $data['member_role'][] = $m['role'] ?? null;
            $data['member_email'][] = $m['email'] ?? null;
        }

        // Activities
        $data['stage'] = [];
        $data['activities'] = [];
        $data['timeframe'] = [];
        $data['implementation_date'] = [];
        $data['point_person'] = [];
        $data['status'] = [];
        foreach ($project->activities as $act) {
            $data['stage'][] = $act->Stage ?? '';
            $data['activities'][] = $act->Specific_Activity ?? '';
            $data['timeframe'][] = $act->Time_Frame ?? '';
            $data['implementation_date'][] = $act->Implementation_Date ?? '';
            $data['point_person'][] = $act->Point_Persons ?? '';
            $data['status'][] = $act->status ?? 'Planned';
        }

        // Budgets
        $data['budget_activity'] = [];
        $data['budget_resources'] = [];
        $data['budget_partners'] = [];
        $data['budget_amount'] = [];
        foreach ($project->budgets as $b) {
            $data['budget_activity'][] = $b->Specific_Activity ?? '';
            $data['budget_resources'][] = $b->Resources_Needed ?? '';
            $data['budget_partners'][] = $b->Partner_Agencies ?? '';
            $data['budget_amount'][] = $b->Amount ?? '';
        }

        // Prepare rules for validation. If the project already has a saved logo,
        // allow omitting the uploaded file when checking readiness (mirror updateSubmit logic).
        $rules = $this->validateSubmitRules();
        if (!empty($project->Project_Logo)) {
            $rules['Project_Logo'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        // Run submit validation rules against the saved project payload
        $validator = Validator::make($data, $rules, $this->validationMessages());
        $readyForSubmission = !$validator->fails();
        $submissionErrors = $validator->fails() ? $validator->errors()->all() : [];

        // Determine if this pending view represents a resubmission so the
        // show blade can surface the rejection history accordingly.
        $isResubmission = ($project->is_resubmission ?? false)
            || (($project->resubmission_count ?? 0) > 0)
            || !empty($project->previous_rejection_reasons);

        return view('projects.show', [
            'project' => $project,
            'readyForSubmission' => $readyForSubmission,
            'submissionErrors' => $submissionErrors,
            'readinessData' => $data,
            'isResubmission' => $isResubmission,
        ]);
    }

    public function edit(Project $project)
    {
        $isStaff = Auth::user()->isStaff();
        if (!$isStaff) {
            // Students may edit drafts or rejected; not pending
            if (!Auth::user()->isStudent() || Auth::user()->student->id !== $project->student_id) {
                abort(403, 'Unauthorized action.');
            }
            if ($project->Project_Status === 'pending') {
                return redirect()->route('projects.show', $project)->with('error', 'Pending projects cannot be edited.');
            }
            // student edit view - ensure related data is loaded for the edit form
            $project->load(['activities', 'budgets']);
            return view('projects.edit-draft', ['project' => $project]);
        } else {
            // staff edit view
            // load relations so the staff edit JS can populate activities, budgets, and members
            $project->load(['activities', 'budgets']);

            // Ensure student_ids and member_roles are present for the frontend script.
            // If student_ids is empty, fall back to the project owner or teamMembers() list.
            $studentIds = $project->student_ids;
            if (empty($studentIds) || !is_array($studentIds) || count($studentIds) === 0) {
                $students = $project->teamMembers();
                $studentIds = $students->pluck('id')->map(function($v){ return (int) $v; })->toArray();
                if (empty($studentIds)) {
                    // at least include project owner student_id
                    $studentIds = [$project->student_id];
                }
            }

            // Ensure member_roles is always an array (may be null in older records)
            $memberRoles = $project->member_roles;
            if (empty($memberRoles) || !is_array($memberRoles)) {
                $memberRoles = [];
            }

            // Inject these into the model instance so Blade @json($project) contains them
            $project->student_ids = $studentIds;
            $project->member_roles = $memberRoles;

            return view('projects.staff-edit', ['project' => $project]);
        }
    }

    public function destroy(Project $project)
    {
        if (Auth::user()->isStaff()) {
            $project->delete();
            return redirect()->back()->with('success', 'Project deleted successfully.');
        }
        if (!Auth::user()->isStudent() || Auth::user()->student->id !== $project->student_id) {
            abort(403, 'Unauthorized action.');
        }
        if ($project->Project_Status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft projects can be deleted by students.');
        }
        $project->delete();
        return redirect()->route('projects.my')->with('success', 'Draft project deleted successfully.');
    }

    /**
     * List / index pages
     */
    public function index()
    {
        return $this->current();
    }

    public function current()
    {
        // Include completed projects so that unarchived projects marked 'completed'
        // also appear on the Current Projects listing.
        $projects = Project::whereIn('Project_Status', ['current', 'approved', 'completed'])
            ->orderByDesc('created_at')
            ->get();
        $projectCount = $projects->count();
        // Render the projects listing view so the passed $projects (including 'completed') are shown.
        return view('all_projects.current', compact('projects', 'projectCount'));
    }

    public function pending()
    {
        if (!Auth::user()->isStaff()) abort(403);
                // Only include projects whose status is 'pending'. Rejected projects
                // should be shown in the dedicated rejected list instead.
                $projects = Project::where('Project_Status', 'pending')
                        ->orderByDesc('created_at')
                        ->get();
        return view('all_projects.pending', compact('projects'));
    }

    public function archived()
    {
        if (!Auth::user()->isStaff()) abort(403);
        $projects = Project::where('Project_Status', 'archived')
            ->orderByDesc('created_at')
            ->get();
        return view('all_projects.archived', compact('projects'));
    }

    /**
     * Show rejected projects (staff view)
     */
    public function rejected()
    {
        if (!Auth::user()->isStaff()) abort(403);
        $projects = Project::where('Project_Status', 'rejected')
            ->orderByDesc('updated_at')
            ->get();
        return view('all_projects.rejected', compact('projects'));
    }

    /**
     * Staff actions: approve/reject/archive/unarchive
     */
    public function approve(Request $request, Project $project)
    {
        if (!Auth::user()->isStaff()) abort(403);
        // Keep 'approved' as the canonical approved state
        $project->update(['Project_Status' => 'approved']);
        return redirect()->back()->with('success', 'Project approved successfully.');
    }

    public function reject(Request $request, Project $project)
    {
        if (!Auth::user()->isStaff()) abort(403);
        $data = $request->validate(['reason' => 'nullable|string|max:2000']);
        // Preserve previous rejection reasons history
        $previous = [];
        if (!empty($project->previous_rejection_reasons)) {
            try {
                $previous = json_decode($project->previous_rejection_reasons, true) ?: [];
            } catch (\Exception $e) {
                $previous = [];
            }
        }
        // If there is an existing rejection reason, push it into history
        if (!empty($project->Project_Rejection_Reason)) {
            $previous[] = [
                'reason' => $project->Project_Rejection_Reason,
                'rejected_at' => optional($project->updated_at)->toDateTimeString() ?? now()->toDateTimeString(),
                'rejected_by' => $project->Project_Rejected_By ?? null,
            ];
        }

        $project->update([
            'Project_Status' => 'rejected',
            'Project_Rejection_Reason' => $data['reason'] ?? null,
            'Project_Rejected_By' => Auth::user()->user_id ?? null,
            'previous_rejection_reasons' => !empty($previous) ? json_encode($previous) : null,
        ]);
        return redirect()->back()->with('success', 'Project rejected.');
    }

    public function archive(Request $request, Project $project)
    {
        if (!Auth::user()->isStaff()) abort(403);
        // Prevent archiving if the project's activities are not all completed
        $acts = $project->activities ?? collect();
        $allCompleted = false;
        try {
            if ($acts instanceof \Illuminate\Support\Collection) {
                $allCompleted = $acts->isNotEmpty() && $acts->filter(function($a){ return strtolower(trim((string)($a->status ?? ''))) !== 'completed'; })->count() === 0;
            }
        } catch (\Exception $e) { $allCompleted = false; }

        if (! $allCompleted) {
            return redirect()->back()->with('error', 'Project is still in progress and cannot be archived until all activities are completed.');
        }

        $project->update(['Project_Status' => 'archived']);
        return redirect()->back()->with('success', 'Project archived.');
    }

    public function unarchive(Request $request, Project $project)
    {
        if (!Auth::user()->isStaff()) abort(403);
        // When unarchiving, mark project as completed (staff action).
        // Completed projects are the only ones that can be archived, so when
        // we unarchive we preserve that completed state.
        $project->update(['Project_Status' => 'completed']);
        // Redirect staff to the Current Projects page so the unarchived project
        // is visible in the listing immediately (Current includes 'completed').
        return redirect()->route('projects.current')->with('success', 'Project unarchived and marked as completed.');
    }

    /**
     * Section views (rotc, lts, cwts)
     */
    public function rotc($section = null)
    {
        $component = 'ROTC';
        $letter = $section ?? request()->input('section');

        $q = Project::where('Project_Component', $component);
        if (!empty($letter)) {
            // Accept both 'Section X' and legacy 'X' values
            $q->where(function($sub) use ($letter) {
                $sub->where('Project_Section', 'Section ' . $letter)
                    ->orWhere('Project_Section', $letter);
            });
        }

        $projects = $q->whereIn('Project_Status', ['current', 'approved', 'completed'])
            ->with(['activities', 'budgets'])
            ->orderByDesc('created_at')
            ->get();
        $currentSection = $letter ?? null;
        return view('projects.rotc', compact('projects', 'currentSection'));
    }

    public function lts($section = null)
    {
        $component = 'LTS';
        $letter = $section ?? request()->input('section') ?? 'A';

        $projects = Project::where('Project_Component', $component)
            ->whereIn('Project_Status', ['current', 'approved', 'completed'])
            ->where(function($sub) use ($letter) {
                $sub->where('Project_Section', 'Section ' . $letter)
                    ->orWhere('Project_Section', $letter);
            })
            ->with(['activities', 'budgets'])
            ->orderByDesc('created_at')
            ->get();

        $currentSection = $letter;
        return view('projects.lts', compact('projects', 'currentSection'));
    }

    public function cwts($section = null)
    {
        $component = 'CWTS';
        $letter = $section ?? request()->input('section') ?? 'A';

        $projects = Project::where('Project_Component', $component)
            ->whereIn('Project_Status', ['current', 'approved', 'completed'])
            ->where(function($sub) use ($letter) {
                $sub->where('Project_Section', 'Section ' . $letter)
                    ->orWhere('Project_Section', $letter);
            })
            ->with(['activities', 'budgets'])
            ->orderByDesc('created_at')
            ->get();

        $currentSection = $letter;
        return view('projects.cwts', compact('projects', 'currentSection'));
    }

    /**
     * Student-specific helpers
     */
    public function myProjects()
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) abort(403);
        $sid = $user->student->id;
        $projects = Project::where(function($q) use ($sid) {
            $q->where('student_id', $sid)->orWhereJsonContains('student_ids', $sid);
        })->orderByDesc('created_at')->get();
        return view('all_projects.my-projects', compact('projects'));
    }

    public function myProjectDetails($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) abort(403);
        $project = Project::where('Project_ID', $id)->firstOrFail();
        if ($project->student_id !== $user->student->id) abort(403);
        $project->load(['activities', 'budgets']);
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function details($id)
    {
        $project = Project::where('Project_ID', $id)->with(['activities', 'budgets'])->firstOrFail();
        return view('projects.details', compact('project'));
    }

    /**
     * Student lookup APIs used by forms
     */
    public function getStudentsBySectionAndComponent(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) abort(403);

        // Default to current student's section/component unless explicitly provided
        $section = $request->input('section', $user->student->student_section ?? null);
        $component = $request->input('component', $user->student->student_component ?? null);

        $q = Student::with('user');
        if ($section) $q->where('student_section', $section);
        if ($component) $q->where('student_component', $component);

        // Exclude the current student (owner) from the selectable list
        if (!empty($user->student->id)) {
            $q->where('id', '!=', $user->student->id);
        }

        $students = $q->get()->filter(function($s) {
            // Ensure linked user exists
            return !empty($s->user) && !empty($s->user->user_Name);
        })->sortBy(function($s) {
            return $s->user->user_Name ?? '';
        })->values()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->user_Name ?? null,
                'email' => $s->user->user_Email ?? null,
                // include contact number from DB column (both keys for compatibility)
                'contact' => $s->student_contact_number ?? null,
                'contact_number' => $s->student_contact_number ?? null,
            ];
        });

        return response()->json($students);
    }

    public function getStudentsForStaff(Request $request)
    {
        if (!Auth::user()->isStaff()) abort(403);
        $section = $request->input('section');
        $component = $request->input('component');
        $q = Student::with('user');
        if ($section) $q->where('student_section', $section);
        if ($component) $q->where('student_component', $component);
        $students = $q->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->user_Name ?? null,
                'email' => $s->user->user_Email ?? null,
                'contact' => $s->student_contact_number ?? null,
                'contact_number' => $s->student_contact_number ?? null,
            ];
        });
        return response()->json($students);
    }

    public function getUserPendingCount()
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) abort(403);
        $sid = $user->student->id;
        $count = Project::where(function($q) use ($sid) {
            $q->where('student_id', $sid)->orWhereJsonContains('student_ids', $sid);
        })->where('Project_Status', 'pending')->count();
        return response()->json(['count' => $count]);
    }

    public function getStudentDetails(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStudent()) abort(403);
        $ids = $request->input('ids', []);
        $students = Student::with('user')->whereIn('id', $ids)->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->user_Name ?? null,
                'email' => $s->user->user_Email ?? null,
                'contact' => $s->student_contact_number ?? null,
                'contact_number' => $s->student_contact_number ?? null,
            ];
        });
        return response()->json($students);
    }

    public function getStudentDetailsForStaff(Request $request)
    {
        if (!Auth::user()->isStaff()) abort(403);
        $ids = $request->input('ids', []);
        $students = Student::with('user')->whereIn('id', $ids)->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->user_Name ?? null,
                'email' => $s->user->user_Email ?? null,
                'contact' => $s->student_contact_number ?? null,
                'contact_number' => $s->student_contact_number ?? null,
            ];
        });
        return response()->json($students);
    }

    /**
     * If a project has at least one activity and all activities' status are 'completed',
     * persist `Project_Status = 'completed'` to the database.
     * This ensures computed state is persisted when activities are updated via project flows.
     */
    protected function maybePersistCompleted(Project $project)
    {
        try {
            $project->load('activities');
            $acts = $project->activities ?? collect();
            if (!($acts instanceof \Illuminate\Support\Collection)) return;
            if ($acts->isEmpty()) return;

            $notCompleted = $acts->filter(function($a){ return strtolower(trim((string)($a->status ?? ''))) !== 'completed'; })->count();
            if ($notCompleted === 0) {
                // All activities completed — persist status
                if ($project->Project_Status !== 'completed') {
                    $project->update(['Project_Status' => 'completed']);
                }
            }
        } catch (\Exception $e) {
            // swallow errors — this is a best-effort persistence
            Log::warning('maybePersistCompleted failed for project ' . ($project->Project_ID ?? 'unknown') . ': ' . $e->getMessage());
        }
    }
}
