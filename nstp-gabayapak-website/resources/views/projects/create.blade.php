@extends('layouts.app')


@section('title', 'Create Project Proposal')


@section('content')
<!-- Project Proposal -->
<section id="upload-project" class="space-y-6 md:space-y-8 page-container w-full lg:max-w-5xl mx-auto px-2 md:px-6">
  <!-- Main Heading -->
  <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">Project Proposal</h1>
 
  @php
    use App\Models\Project;
    $student = Auth::user()->student ?? null;
    $existingDraft = null;
    $hasPending = false;
    $hasRejected = false;
    $existingRejected = null;
    $existingPending = null;
    if ($student) {
        $existingDraft = Project::where('student_id', $student->id)->where('Project_Status', 'draft')->first();
        $hasPending = Project::where('student_id', $student->id)->where('Project_Status', 'pending')->exists();
        $hasRejected = Project::where('student_id', $student->id)->where('Project_Status', 'rejected')->exists();
      $existingRejected = Project::where('student_id', $student->id)->where('Project_Status', 'rejected')->first();
      $existingPending = Project::where('student_id', $student->id)->where('Project_Status', 'pending')->first();
    }
    // Default disables based on draft/pending/rejected owned projects
    $disableDraftBtn = ($hasPending || $hasRejected);
    $disableSubmit = (bool) ($existingDraft || $hasPending || $hasRejected);

    // If controller passed an existingProject (student is a member of a project),
    // disable save/submit for non-owners and show a banner only for non-owners.
    $existingProject = $existingProject ?? null; // may be passed from controller
    $isOwnerOfExisting = false;
    if ($existingProject && $student) {
        $isOwnerOfExisting = ($existingProject->student_id === $student->id);
        if (!$isOwnerOfExisting) {
            // Student is a member (but not owner): prevent creating/submitting another project
            $disableDraftBtn = true;
            $disableSubmit = true;
        }
    }

    // Tooltip messages for disabled buttons
    $draftTooltip = '';
    $submitTooltip = '';
    if ($disableDraftBtn) {
      if (!$isOwnerOfExisting && $existingProject) {
        $draftTooltip = 'You cannot save a draft while you are already a member of another project. Open the project to view details.';
      } elseif ($hasPending || $hasRejected) {
        $draftTooltip = 'You cannot save a draft while you have a pending or rejected project.';
      } else {
        $draftTooltip = 'Saving is disabled.';
      }
    }
    if (!$disableSubmit) {
      $submitTooltip = '';
    } else {
      if (!$isOwnerOfExisting && $existingProject) {
        $submitTooltip = 'You cannot submit a project while you are a member of another project. Open that project to view details.';
      } elseif ($existingDraft) {
        $submitTooltip = 'Please open and submit your saved draft or delete it to create a new project.';
      } elseif ($hasPending || $hasRejected) {
        $submitTooltip = 'You cannot submit a new project while you have a pending or rejected project.';
      } else {
        $submitTooltip = 'Submitting is disabled.';
      }
    }
  @endphp

@if($existingDraft)
<div class="mb-4 rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4">
    <div class="flex items-center justify-between">
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-blue-800">You already have a saved draft project</p>
            <p class="text-sm text-blue-700">Continue editing your draft or submit it when ready.</p>
        </div>

        <a href="{{ route('projects.edit', $existingDraft) }}"
           class="inline-block bg-blue-600 text-white px-3 py-2 rounded-lg">
            Open Draft
        </a>
    </div>
</div>
@endif


  @php $showExistingBanner = false; @endphp
  @if(!empty($existingProject))
    {{-- Show banner for members OR owners, but if the student is the owner and the project's status is 'draft', prefer the draft banner above --}}
    @php
      $showExistingBanner = true;
      if (!empty($isOwnerOfExisting) && ($existingProject->Project_Status === 'draft')) {
        $showExistingBanner = false;
      }
    @endphp
  @endif

  @if($showExistingBanner)
  <div class="mb-4 rounded-lg border-l-4 border-emerald-500 bg-emerald-50 p-4">
    <div class="flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            @if(!empty($isOwnerOfExisting))
              <p class="font-semibold text-emerald-800">You already own a project</p>
            @else
              <p class="font-semibold text-emerald-800">You are already a member of a project</p>
            @endif
            @php $comp = strtoupper(trim($student->student_component ?? '')) ?? ''; @endphp
            @if(in_array($comp, ['LTS','CWTS']))
              <p class="text-sm text-emerald-700">
                As a <strong>{{ $student->student_component }}</strong> student, policy allows only one project association per student (owner or member). This applies even if the project is completed or archived. You are currently attached to "<strong>{{ $existingProject->Project_Name ?? 'a project' }}</strong>", so you cannot create another project.
              </p>
            @else
              @if(!empty($isOwnerOfExisting))
                <p class="text-sm text-emerald-700">
                  You currently own "<strong>{{ $existingProject->Project_Name ?? 'a project' }}</strong>". You may still fill out the form below, but you cannot create another project while this one exists.
                </p>
              @else
                <p class="text-sm text-emerald-700">
                  You are currently attached to "<strong>{{ $existingProject->Project_Name ?? 'a project' }}</strong>".
                  You may still fill out the form below, but you cannot create multiple active projects while attached to one.
                </p>
                @if(empty($isOwnerOfExisting))
                  <p class="text-sm text-emerald-700 mt-2">
                    <strong>Note:</strong> As a team member (not the project leader), you cannot edit the project's details, submit or resubmit the project, or delete it. Only the project leader (owner) can perform those actions.
                  </p>
                @endif
              @endif
            @endif
        </div>
        <div class="flex-none">
            <a href="{{ route('projects.show', $existingProject) }}"
               class="inline-block whitespace-nowrap bg-emerald-600 text-white px-3 py-2 rounded-lg">
                View Project
            </a>
        </div>

    </div>
  </div>
  @endif


  @if(!$existingDraft && ($hasPending || $hasRejected))
    <div class="mb-4 rounded-lg border-l-4 border-yellow-500 bg-yellow-50 p-4">
      <p class="font-semibold text-yellow-800">Notice</p>
      <p class="text-sm text-yellow-700">You already have a {{ $hasPending ? 'pending' : '' }}{{ $hasPending && $hasRejected ? ' and ' : '' }}{{ $hasRejected ? 'rejected' : '' }} project. You cannot create a new draft while that project exists.</p>
      @if($hasRejected && $existingRejected)
        <p class="text-sm mt-2">If your project was rejected, you may <a href="{{ route('projects.edit', $existingRejected) }}" class="text-blue-600 underline">edit the rejected project to resubmit</a>.</p>
      @endif
      @if($hasPending && $existingPending)
        <p class="text-sm mt-2">You have a pending project under review. Check <a href="{{ route('projects.my') }}" class="text-blue-600 underline">My Projects</a> for details.</p>
      @endif
    </div>
  @endif

  <form id="projectForm" action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
   
    <!-- TEAM INFORMATION -->
    <div class="rounded-2xl bg-gray-100 p-6 shadow-subtle space-y-4">
      <h2 class="text-2xl font-bold flex items-center gap-2">
        <span class="text-3xl">🖼️</span> Team Information
      </h2>


      <div class="space-y-3">
        <div>
          <label class="block text-lg font-medium">Project Name<span class="text-red-500">*</span></label>
          <input name="Project_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Project" required>
        </div>
        @php
          // If the existing-project banner is being shown, disable save/submit buttons
          if (!empty($showExistingBanner) && $showExistingBanner) {
            $disableDraftBtn = true;
            $disableSubmit = true;
            // Provide clear tooltips if not already set
            if (empty($draftTooltip)) {
              $draftTooltip = 'You cannot save a draft while you are already attached to another project.';
            }
            if (empty($submitTooltip)) {
              $submitTooltip = 'You cannot submit a project while you are already attached to another project.';
            }
          }
        @endphp

          <label class="block text-lg font-medium">Team Name<span class="text-red-500">*</span></label>
          <input name="Project_Team_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Team" required>
        </div>
        <div>
          <label class="block text-lg font-medium">Team Logo<span class="text-red-500">*</span></label>
          <input type="file" name="Project_Logo" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
          <p class="text-sm text-gray-600 mt-1">Note: Logo is required when submitting a project, but optional when saving as draft.</p>
        </div>
        <!-- Component Dropdown (readonly) -->
        <div class="relative">
          <label class="block text-lg font-medium">Component<span class="text-red-500">*</span></label>
          {{-- show as disabled/select so students cannot change; include hidden input so value is submitted --}}
          <select disabled title="This value comes from your student profile and cannot be changed here." class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-gray-100 relative z-10 focus:outline-none transition-colors">
            <option value="">Select Component</option>
            <option value="LTS" {{ (Auth::user()->student->student_component ?? '') === 'LTS' ? 'selected' : '' }}>Literacy Training Service (LTS)</option>
            <option value="CWTS" {{ (Auth::user()->student->student_component ?? '') === 'CWTS' ? 'selected' : '' }}>Civic Welfare Training Service (CWTS)</option>
            <option value="ROTC" {{ (Auth::user()->student->student_component ?? '') === 'ROTC' ? 'selected' : '' }}>Reserve Officers' Training Corps (ROTC)</option>
          </select>
          <input type="hidden" name="Project_Component" value="{{ Auth::user()->student->student_component ?? '' }}">
        </div>
        <!-- Section Dropdown -->
        <div class="relative">
          <label class="block text-lg font-medium">Section<span class="text-red-500">*</span></label>
          {{-- readonly section: disabled select with hidden input to preserve value on submit --}}
          <select disabled title="This value comes from your student profile and cannot be changed here." class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-gray-100 text-black relative z-10 focus:outline-none transition-colors">
            <option value="" disabled>Select Section</option>
            @foreach (range('A', 'Z') as $letter):
              @php $value = "Section $letter"; @endphp
              <option value="{{ $value }}" {{ (Auth::user()->student->student_section ?? '') === $value ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
            <input type="hidden" name="nstp_section" value="{{ Auth::user()->student->student_section ?? '' }}" />
        </div>
      </div>
    </div>


    <!-- MEMBER PROFILE -->
    <div class="proposal-section mb-8 w-full">
      <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
        <span class="text-2xl md:text-3xl">👥</span> Member Profile
      </h2>


      <!-- Desktop Table View -->
      <div class="hidden md:block mt-4">
        <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400">
          <table id="memberTable" class="w-full text-left">
            <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b-2 border-gray-400">
              <tr>
                <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Name <span class="text-red-500">*</span>
                </th>
                <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Role/s <span class="text-red-500">*</span>
                </th>
                <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  School Email <span class="text-red-500">*</span>
                </th>
                <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                  Contact Number <span class="text-red-500">*</span>
                </th>
                <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider text-center">
                  Action
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-400">
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Enter full name" required value="{{ Auth::user()->user_Name }}" readonly>
                  <input type="hidden" name="member_student_id[]" value="{{ Auth::user()->student->id }}">
                </td>
                <td class="px-6 py-4">
                  <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" required>
                </td>
                <td class="px-6 py-4">
                  <input type="email" name="member_email[]" title="This email comes from your student profile and cannot be changed here." class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" required value="{{ Auth::user()->user_Email }}" readonly>
                </td>
                <td class="px-6 py-4">
                  <input type="tel" name="member_contact[]" title="This contact number comes from your student profile and cannot be changed here." class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required value="{{ Auth::user()->student->student_contact_number ?? '' }}" readonly>
                </td>
                <td class="px-6 py-4 text-center">
                  <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm" disabled>
                    Remove
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
       
        <!-- Add Member Button -->
        <div class="mt-4">
          <button type="button" id="openMemberModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            + Add Member from Same Section/Component
          </button>
        </div>
      </div>


      <!-- Mobile Card View -->
      <div id="memberContainer" class="md:hidden mt-4 space-y-3">
        <div class="member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3">
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
            <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="{{ Auth::user()->user_Name }}" readonly>
            <input type="hidden" name="member_student_id[]" value="{{ Auth::user()->student->id }}">
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
            <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
            <input type="email" name="member_email[]" title="This email comes from your student profile and cannot be changed here." class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" required value="{{ Auth::user()->user_Email }}" readonly>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
            <input type="tel" name="member_contact[]" title="This contact number comes from your student profile and cannot be changed here." class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="{{ Auth::user()->student->student_contact_number ?? '' }}" readonly>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs" disabled>Remove</button>
          </div>
        </div>
       
        <!-- Add Member Button -->
        <div class="mt-4">
          <button type="button" id="openMemberModalMobile" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors shadow-sm">
            + Add Member from Same Section/Component
          </button>
        </div>
      </div>
    </div>


    <!-- PROJECT DETAILS -->
    <div class="rounded-2xl bg-gray-100 p-6 shadow-subtle space-y-4">
      <h2 class="text-2xl font-bold flex items-center gap-2">
        <span class="text-3xl">🎯</span> Project Details
      </h2>


      <div class="space-y-3">
        <div>
          <label class="block text-lg font-medium">Issues/Problem being addressed<span class="text-red-500">*</span></label>
          <textarea name="Project_Problems" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required></textarea>
        </div>
        <div>
          <label class="block text-lg font-medium">Goal/Objectives<span class="text-red-500">*</span></label>
          <textarea name="Project_Goals" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required></textarea>
        </div>
        <div>
          <label class="block text-lg font-medium">Target Community<span class="text-red-500">*</span></label>
          <textarea name="Project_Target_Community" rows="2" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required></textarea>
        </div>
        <div>
          <label class="block text-lg font-medium">Solutions/Activities to be implemented<span class="text-red-500">*</span></label>
          <textarea name="Project_Solution" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required></textarea>
        </div>
        <div>
          <label class="block text-lg font-medium">Expected Outcomes<span class="text-red-500">*</span></label>
          <textarea name="Project_Expected_Outcomes" rows="5" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required></textarea>
        </div>
      </div>
    </div>


    <!-- PROJECT ACTIVITIES -->
    <div class="proposal-section section-gap">
      <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
        <span class="text-2xl md:text-3xl">📅</span> Project Activities
      </h2>


<!-- Desktop Table View -->
<div class="hidden md:block w-full">
  <div class="overflow-x-auto w-full min-w-0">
    <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400 w-full min-w-0">
      <div class="bg-linear-to-r from-green-50 to-emerald-50 border-b-2 border-gray-400 px-6 py-3 w-full min-w-[900px]">
        <div class="flex items-center gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider w-full min-w-[900px] py-2">
            <div class="w-16 px-1 flex-none">Stage <span class="text-red-500">*</span></div>
            <div class="flex-1 px-2">Specific Activities <span class="text-red-500">*</span></div>
            <div class="w-32 px-1 flex-none">Time Frame <span class="text-red-500">*</span></div>
            <div class="w-40 px-2 flex-1">Implementation Date <span class="text-red-500">*</span></div>
            <div class="flex-1 px-2">Point Person/s <span class="text-red-500">*</span></div>
            <div class="w-28 flex-none">Status</div>
            <div class="w-20 px-2 flex-none">Action</div>
        </div>
      </div>
      <div id="activitiesContainer" class="divide-y divide-gray-400 w-full min-w-0">
        <div class="proposal-table-row activity-row flex items-center gap-4 w-full">
          <div class="w-20 flex-none">
            <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" required>
          </div>
          <div class="flex-1 px-2">
            <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
          </div>
          <div class="w-36 px-2 flex-none">
            <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" required>
          </div>
          <div class="w-44 px-2 flex-none">
            <input type="date" name="implementation_date[]" class="proposal-input w-full" required>
          </div>
          <div class="flex-1 px-2">
            <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
          </div>
          <div class="w-30 py-3 flex-none">
            <select name="status[]" class="proposal-select w-full">
              <option>Planned</option>
              <option>Ongoing</option>
            </select>
          </div>
          <div class="w-20 py-3 flex-none">
            <button type="button" class="proposal-remove-btn removeRow">Remove</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>




      <button type="button" id="addActivityRow" class="proposal-add-btn">+ Add Activity</button>
    </div>

    <!-- Mobile Card View -->
<div class="md:hidden space-y-3">
  <div id="activitiesContainerMobile" class="space-y-3">
    <div class="activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm">

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
        <input name="stage[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          placeholder="Stage" required>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          rows="2" placeholder="Specific Activities" required></textarea>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          placeholder="Time Frame" required>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 
          px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          required>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          rows="2" placeholder="Point Person/s" required></textarea>
      </div>

      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Status</label>
          <select name="status[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
            focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
        </div>

        <button type="button" 
          class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 
          text-xs whitespace-nowrap">
          Remove
        </button>
      </div>
    </div>
  </div>
</div>

    <!-- BUDGET -->
    <div class="proposal-section section-gap mb-8 w-full">
      <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
        <span class="text-2xl md:text-3xl">💰</span> Budget
      </h2>


      <!-- Desktop Table View -->
      <div class="hidden md:block w-full">
        <div class="overflow-x-auto w-full min-w-0">
          <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400 w-full min-w-0">
            <div class="bg-yellow-50 border-b-2 border-gray-400 px-6 py-4 grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider w-full min-w-[900px]">
              <div>Activity</div>
              <div>Resources Needed</div>
              <div>Partner Agencies</div>
              <div>Amount</div>
              <div>Action</div>
            </div>
            <div id="budgetContainer" class="w-full min-w-0">
              <div class="proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start w-full">
                <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity..."></textarea>
                <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed..."></textarea>
                <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations..."></textarea>
                <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00">
                <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Mobile Card View -->
      <div class="md:hidden space-y-3">
        <div id="budgetContainerMobile" class="space-y-3">
          <div class="budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm">
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Activity</label>
              <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Activity"></textarea>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
              <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources Needed"></textarea>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
              <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partner Agencies"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-2">
              <div class="space-y-1 flex-1">
                <label class="block text-xs font-medium text-gray-600">Amount</label>
                <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="₱ 0.00">
              </div>
              <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
            </div>
          </div>
        </div>
      </div>


      <button type="button" id="addBudgetRow" class="proposal-add-btn">+ Add Budget Item</button>
    </div>


    <!-- Hidden input to track if it's a draft or submission -->
    <!-- Canonical member payload: authoritative hidden inputs for member arrays (prevents duplicate/hidden DOM issues) -->
    <div id="memberPayload" style="display:none"></div>
    <input type="hidden" name="save_draft" id="saveDraftInput" value="0">
    <input type="hidden" name="submit_project" id="submitProjectInput" value="0">


    <!-- SUBMIT and SAVE BUTTONS -->
      <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
      @if($existingDraft)
        <a href="{{ route('projects.edit', $existingDraft) }}" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors">Open Draft</a>
      @else
        <button type="button" id="saveDraftBtn" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors @if($disableDraftBtn) cursor-not-allowed opacity-60 @endif" @if($disableDraftBtn) disabled title="{{ $draftTooltip }}" @endif>Save as Draft</button>
      @endif
      @if(!$disableSubmit)
        <button type="button" id="submitProjectBtn" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Submit Project</button>
      @else
        <button type="button" id="submitProjectBtn" class="rounded-lg bg-blue-600/60 text-white px-4 py-2 text-sm md:text-base transition-colors cursor-not-allowed" disabled title="{{ $submitTooltip }}">Submit Project</button>
      @endif
    </div>
  </form>
  </div>
</section>


<!-- Member Selection Modal -->
<div id="memberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold">Select Team Members</h3>
      <button type="button" id="closeMemberModal" class="text-gray-500 hover:text-gray-700">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
    <div class="mb-4">
      <p class="text-sm text-gray-600">Select students from your section and component:</p>
      <p class="text-sm font-medium">{{ Auth::user()->student->student_component ?? 'N/A' }} - {{ Auth::user()->student->student_section ?? 'N/A' }}</p>
    </div>
    <div id="memberList" class="space-y-2 mb-4 max-h-60 overflow-y-auto">
      <!-- Members will be loaded here dynamically -->
    </div>
    <div class="flex justify-end space-x-3">
      <button type="button" id="cancelMemberSelection" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
        Cancel
      </button>
      <button type="button" id="addSelectedMembers" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
        Add Selected Members
      </button>
    </div>
  </div>
</div>





<script>
// Track added member emails to prevent duplicates
let addedMemberEmails = new Set();
// Owner SID (server-side rendered) - used to ensure cloned payload keeps owner first
const OWNER_SID = '{{ Auth::user()->student->id }}';

/* Helper: safeAddListener */
function safeAddListener(id, event, handler) {
  const el = document.getElementById(id);
  if (el) el.addEventListener(event, handler);
}

/* --------------------
   Deduplication helpers - keep at most one empty activity/budget row
   (Safe to call multiple times)
   -------------------- */
function dedupeEmptyActivityRows() {
  try {
    const desktopContainer = document.getElementById('activitiesContainer');
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    function isRowEmpty(row) {
      if (!row) return true;
      const inputs = row.querySelectorAll('input, textarea, select');
      for (let el of inputs) {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') continue;
        if (el.name && (el.name === '_token' || el.name === '_method')) continue;
        if (el.value && el.value.toString().trim() !== '') return false;
      }
      return true;
    }

    if (desktopContainer) {
      const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .activity-row'));
      let emptyFound = false;
      rows.forEach(r => {
        if (isRowEmpty(r)) {
          if (!emptyFound) emptyFound = true; else r.remove();
        }
      });
    }

    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.activity-row'));
      let emptyFound = false;
      cards.forEach(c => {
        if (isRowEmpty(c)) {
          if (!emptyFound) emptyFound = true; else c.remove();
        }
      });
    }
  } catch (e) {
    console.error('dedupeEmptyActivityRows error', e);
  }
}

function dedupeEmptyBudgetRows() {
  try {
    const desktopContainer = document.getElementById('budgetContainer');
    const mobileContainer = document.getElementById('budgetContainerMobile');

    function isBudgetRowEmpty(row) {
      if (!row) return true;
      const inputs = row.querySelectorAll('input, textarea, select');
      for (let el of inputs) {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') continue;
        if (el.name && (el.name === '_token' || el.name === '_method')) continue;
        if (el.value && el.value.toString().trim() !== '') return false;
      }
      return true;
    }

    if (desktopContainer) {
      const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .budget-row'));
      const emptyRows = rows.filter(isBudgetRowEmpty);
      if (emptyRows.length > 1) emptyRows.slice(0, -1).forEach(r => r.remove());
    }

    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
      const emptyRows = cards.filter(isBudgetRowEmpty);
      if (emptyRows.length > 1) emptyRows.slice(0, -1).forEach(c => c.remove());
    }
  } catch (e) {
    console.error('dedupeEmptyBudgetRows error', e);
  }
}

/* removeAllEmptyBudgetRows: for final submit cleanup */
function removeAllEmptyBudgetRows() {
  const desktopContainer = document.getElementById('budgetContainer');
  const mobileContainer = document.getElementById('budgetContainerMobile');
  function isBudgetRowEmpty(row) {
    if (!row) return true;
    const inputs = row.querySelectorAll('input, textarea, select');
    for (let el of inputs) {
      const t = (el.type || '').toLowerCase();
      if (t === 'hidden') continue;
      if (el.name && (el.name === '_token' || el.name === '_method')) continue;
      if (el.value && el.value.toString().trim() !== '') return false;
    }
    return true;
  }
  if (desktopContainer) {
    const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .budget-row'));
    rows.forEach(r => { if (isBudgetRowEmpty(r)) r.remove(); });
  }
  if (mobileContainer) {
    const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
    cards.forEach(c => { if (isBudgetRowEmpty(c)) c.remove(); });
  }
}

// Ensure visible member inputs are enabled (override any prior disabling)
function enableVisibleMemberInputs(form) {
  try {
    // Find visible member rows (desktop table rows OR mobile cards)
    const visibleMemberRows = Array.from(document.querySelectorAll('#memberTable tbody tr, .member-card')).filter(r => r && r.offsetParent !== null);
    if (visibleMemberRows.length > 0) {
      visibleMemberRows.forEach(row => {
        try {
          row.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(i => { try { i.disabled = false; } catch(e){} });
        } catch (e) { /* ignore malformed row */ }
      });
    } else {
      // Fallback: enable desktop member inputs
      const desktopMemberTable = document.getElementById('memberTable');
      if (desktopMemberTable) desktopMemberTable.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(i => { try { i.disabled = false; } catch(e){} });
    }
  } catch (e) { /* ignore */ }
}

// Debug toggle: if true, show a preview modal of FormData member fields before submit
const ENABLE_FORMDATA_PREVIEW = true;
// If true, skip showing the FormData preview for Save-as-Draft (user requested)
const SKIP_DRAFT_PREVIEW = true;

/* --------------------
   prepareFormForSubmit: disable inputs that are not visible (so only visible ones get sent)
   -------------------- */
function prepareFormForSubmit(form) {
  // Sanitize budget amount inputs before submit: temporarily replace display values like "15,000" or "₱15,000.00"
  // with normalized numeric strings (e.g. "15000.00") so server receives a consistent format.
  function sanitizeBudgetAmountsForSubmit(formEl) {
    const inputs = Array.from(formEl.querySelectorAll('input[name="budget_amount[]"]'));
    inputs.forEach(input => {
      try {
        const orig = input.value || '';
        input.dataset._orig = orig;
        // Remove currency symbols, spaces and commas, keep digits, dot and minus
        let cleaned = orig.replace(/[₱\s,]/g, '');
        // Strip any other non-numeric except dot and minus
        cleaned = cleaned.replace(/[^0-9.\-]/g, '');
        // If multiple dots, join extras
        const parts = cleaned.split('.');
        if (parts.length > 2) {
          cleaned = parts.shift() + '.' + parts.join('');
        }
        if (cleaned !== '' && !isNaN(Number(cleaned))) {
          // Force two decimal places for consistency
          cleaned = Number(cleaned).toFixed(2);
        }
        input.value = cleaned;
      } catch (e) { /* ignore */ }
    });
    // Restore originals shortly after submit attempt in case the page doesn't navigate
    setTimeout(() => restoreBudgetAmounts(formEl), 1500);
  }

  function restoreBudgetAmounts(formEl) {
    const inputs = Array.from(formEl.querySelectorAll('input[name="budget_amount[]"]'));
    inputs.forEach(input => {
      try {
        if (input.dataset && input.dataset._orig !== undefined) {
          input.value = input.dataset._orig;
          delete input.dataset._orig;
        }
      } catch (e) { /* ignore */ }
    });
  }
  // Sanitize budget amounts before disabling hidden inputs so the values posted are normalized
  try { sanitizeBudgetAmountsForSubmit(form); } catch (e) { /* ignore */ }

  // Enable everything first
  form.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
  
  // Handle member inputs specially - only keep visible view's inputs
  const desktopMemberTable = document.getElementById('memberTable');
  const mobileContainer = document.getElementById('memberContainer');
  
  // Check which view is visible
  const desktopVisible = desktopMemberTable && desktopMemberTable.offsetParent !== null;
  const mobileVisible = mobileContainer && mobileContainer.offsetParent !== null;
  
  // Disable member inputs from the hidden view
  if (desktopVisible && !mobileVisible) {
    // Desktop is visible, disable all member inputs in the mobile view (including hidden ids)
    // Desktop initial row includes a hidden `member_student_id[]` so owner id will be present.
    if (mobileContainer) {
      mobileContainer.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(el => {
        el.disabled = true;
      });
    }
  } else if (mobileVisible && !desktopVisible) {
    // Mobile is visible, disable all member inputs in the desktop table (including hidden ids)
    if (desktopMemberTable) {
      desktopMemberTable.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(el => {
        el.disabled = true;
      });
    }
  }
  
  // Then disable other hidden elements except hidden inputs / csrf / method
  form.querySelectorAll('input, textarea, select').forEach(el => {
    try {
      // Never disable inputs that were manually added by the user (they are authoritative)
      if (el.dataset && (el.dataset.manuallyAdded === '1' || el.dataset.manuallyAdded === 'true')) return;
      const t = (el.type || '').toLowerCase();
      if (t === 'hidden') return;
      if (el.name && (el.name === '_token' || el.name === '_method')) return;
      if (el.disabled) return; // Already disabled by member input logic above
      // offsetParent === null indicates hidden by CSS (not in DOM flow)
      if (el.offsetParent === null) el.disabled = true;
    } catch (e) { /* ignore */ }
  });
}

/* --------------------
   relaxRequiredForDraft: remove required attributes for draft saving
   -------------------- */
function relaxRequiredForDraft(form) {
  const selectors = [
    'input[name^="member_"]',
    'input[name^="stage"]',
    'textarea[name^="activities"]',
    'input[name^="timeframe"]',
    'textarea[name^="point_person"]',
    'select[name^="status"]',
    'textarea[name^="budget_"]',
    'input[name^="budget_amount[]"]',
    'input[name="Project_Logo"]'
  ];
  selectors.forEach(sel => {
    form.querySelectorAll(sel).forEach(el => {
      if (el.hasAttribute && el.hasAttribute('required')) el.removeAttribute('required');
    });
  });
}

// Initialize addedMemberEmails from existing inputs
document.querySelectorAll('input[name="member_email[]"]').forEach(input => {
  if (input.value && input.value.trim() !== '') {
    addedMemberEmails.add(input.value.trim());
  }
});

// Use event delegation for remove buttons (works for static and dynamic rows)
document.addEventListener('click', function(e) {
  if (e.target.classList.contains('removeRow')) {
    const btn = e.target;
    // Check if this is an attempt to remove the last member
    if (btn.closest('#memberTable tbody tr') || btn.closest('.member-card')) {
      const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
      const memberCardRows = document.querySelectorAll('.member-card').length;
      const totalMemberRows = memberTableRows + memberCardRows;
      if (totalMemberRows <= 1) {
        Swal.fire({
          icon: 'error',
          title: 'Cannot Remove',
          text: 'At least one team member is required.',
          confirmButtonColor: '#3085d6'
        });
        return;
      }
    }
    // Check if this is an attempt to remove the last activity
    if (btn.closest('.activity-row')) {
      const activityRows = document.querySelectorAll('.activity-row').length;
      if (activityRows <= 1) {
        Swal.fire({
          icon: 'error',
          title: 'Cannot Remove',
          text: 'At least one activity is required.',
          confirmButtonColor: '#3085d6'
        });
        return;
      }
    }
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
      if (result.isConfirmed) {
        const row = btn.closest('tr, .proposal-table-row, .activity-row, .budget-row, .member-card');
        if (row) {
          // If removing member, remove email/student id from tracking set and
          // also remove any duplicate DOM representation (desktop vs mobile)
          // so validation does not see an orphaned duplicate.
          let emailVal = null;
          let sidVal = null;
          if (row.querySelector) {
            const emailInput = row.querySelector('input[name="member_email[]"]');
            if (emailInput && emailInput.value) {
              emailVal = emailInput.value.trim();
              addedMemberEmails.delete(emailVal);
            }
            const sidInput = row.querySelector('input[name="member_student_id[]"]');
            if (sidInput && sidInput.value) {
              sidVal = sidInput.value.trim();
            }
          }

          // Remove any other rows/cards that reference the same student id or email
          if (sidVal) {
            // find hidden inputs with same sid and remove their closest row/card
              document.querySelectorAll('input[name="member_student_id[]"]').forEach(el => {
                try {
                  if ((el.value || '').toString().trim() === sidVal) {
                    const other = el.closest('tr, .member-card, .proposal-table-row');
                    if (other && other !== row) other.remove();
                  }
                } catch (e) { /* ignore */ }
              });
              // Also remove canonical payload inputs matching this sid
              try {
                const payload = document.getElementById('memberPayload');
                if (payload) payload.querySelectorAll('[data-manual-sid="' + sidVal + '"]').forEach(i => i.remove());
              } catch (e) { /* ignore */ }
          }
          if (emailVal) {
            document.querySelectorAll('input[name="member_email[]"]').forEach(el => {
              try {
                if ((el.value || '').toString().trim() === emailVal) {
                  const other = el.closest('tr, .member-card, .proposal-table-row');
                  if (other && other !== row) other.remove();
                }
              } catch (e) { /* ignore */ }
            });
              // Also remove canonical payload inputs matching this email
              try {
                const payload = document.getElementById('memberPayload');
                if (payload) payload.querySelectorAll('[data-manual-email="' + emailVal + '"]').forEach(i => i.remove());
              } catch (e) { /* ignore */ }
          }

          // Finally remove the clicked row itself
          // Also remove any canonical payload clones that may have been cloned into the form
          try {
            const form = document.getElementById('projectForm');
            if (form) {
              form.querySelectorAll('input[data-cloned-from="memberPayload"]').forEach(i => {
                try {
                  if (sidVal && (i.dataset.manualSid === sidVal || i.value === sidVal)) i.remove();
                  if (emailVal && (i.dataset.manualEmail === emailVal || i.value === emailVal)) i.remove();
                } catch (e) { /* ignore per-input errors */ }
              });
            }
          } catch (e) { /* ignore */ }

          row.remove();
          // Ensure canonical payload reflects the current visible rows
          try { syncCanonicalPayloadWithVisible(); } catch(e) { /* ignore */ }
        }
        Swal.fire(
          'Removed!',
          'The item has been removed.',
          'success'
        )
      }
    });
  }
});

  // Add Row for Activities
  document.getElementById('addActivityRow').addEventListener('click', () => {
    // Desktop table view - create markup identical to static row
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row activity-row flex items-center gap-4 w-full';
      newRow.innerHTML = `
        <div class="w-20 flex-none">
          <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
        </div>
        <div class="w-36 px-2 flex-none">
          <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" required>
        </div>
        <div class="w-44 px-2 flex-none">
          <input type="date" name="implementation_date[]" class="proposal-input w-full" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
        </div>
        <div class="w-30 py-3 flex-none">
          <select name="status[]" class="proposal-select w-full">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
        </div>
        <div class="w-20 py-3 flex-none">
          <button type="button" class="proposal-remove-btn removeRow">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }


    // Mobile card view - keep existing card layout but ensure remove button has removeRow
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
      newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
        <input name="stage[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Stage" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Specific Activities" required></textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Time Frame" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Point Person/s" required></textarea>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Status</label>
          <select name="status[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
        </div>

        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
      `;
      mobileContainer.appendChild(newCard);
    }
   
  });


  // Add Row for Budget
  document.getElementById('addBudgetRow').addEventListener('click', () => {
    // Desktop table view - create markup identical to static budget row
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start';
      newRow.innerHTML = `
        <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity..."></textarea>
        <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed..."></textarea>
        <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations..."></textarea>
        <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00">
        <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
      `;
      desktopContainer.appendChild(newRow);
    }

    // Mobile card view - mirror existing mobile layout and ensure remove button has removeRow
    const mobileContainer = document.getElementById('budgetContainerMobile');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Activity</label>
          <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Activity"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
          <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources Needed"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
          <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partner Agencies"></textarea>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <div class="space-y-1 flex-1">
            <label class="block text-xs font-medium text-gray-600">Amount</label>
            <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="₱ 0.00">
          </div>
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }

  });


  // Auto-expand textarea
  document.addEventListener("input", function (e) {
    if (e.target.classList.contains("auto-expand")) {
      e.target.style.height = "auto";
      e.target.style.height = e.target.scrollHeight + "px";
    }
  });


  // Enhanced Save as Draft with proper handling
  (function() {
    let isSavingDraft = false;
    safeAddListener('saveDraftBtn', 'click', function (e) {
      e.preventDefault();
      if (isSavingDraft) return;
      const form = document.getElementById('projectForm');
      if (!form) return;

      Swal.fire({
        title: 'Save as Draft?',
        text: "Your project will be saved as a draft and can be edited later.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, save as draft!'
      }).then((result) => {
        if (!result.isConfirmed) return;
        isSavingDraft = true;

        // Relax required attributes (so draft can be saved without required fields)
        relaxRequiredForDraft(form);

        // Only remove completely empty rows, but preserve partial budget data for drafts
        dedupeEmptyActivityRows();
        dedupeEmptyBudgetRows(); // This only removes completely empty rows

        // DO NOT remove budget rows with partial data in draft mode
        // removeAllEmptyBudgetRows(); // Commented out for draft saving

        
        //prepare visible fields only
        prepareFormForSubmit(form);
        // Ensure visible member inputs are enabled (fix for draft saves missing added members)
        try { enableVisibleMemberInputs(form); } catch (e) { /* ignore */ }

        // Debug: Log budget and member arrays being submitted for draft (detailed values)
        const draftBudgetActivities = Array.from(form.querySelectorAll('input[name="budget_activity[]"]:not([disabled])')).map(i => i.value || '');
        const draftBudgetAmounts = Array.from(form.querySelectorAll('input[name="budget_amount[]"]:not([disabled])')).map(i => i.value || '');
        const draftMemberNames = Array.from(form.querySelectorAll('input[name="member_name[]"]:not([disabled])')).map(i => i.value || '');
        const draftMemberEmails = Array.from(form.querySelectorAll('input[name="member_email[]"]:not([disabled])')).map(i => i.value || '');
        const draftMemberStudentIds = Array.from(form.querySelectorAll('input[name="member_student_id[]"]:not([disabled])')).map(i => i.value || '');
        const draftMemberRoles = Array.from(form.querySelectorAll('input[name="member_role[]"]:not([disabled])')).map(i => i.value || '');
        console.debug('Draft - Budget Activities values:', draftBudgetActivities);
        console.debug('Draft - Budget Amounts values:', draftBudgetAmounts);
        console.debug('Draft - Member names values:', draftMemberNames);
        console.debug('Draft - Member emails values:', draftMemberEmails);
        console.debug('Draft - Member student IDs values:', draftMemberStudentIds);
        console.debug('Draft - Member roles values:', draftMemberRoles);

        const saveDraftInput = document.getElementById('saveDraftInput');
        const submitProjectInput = document.getElementById('submitProjectInput');
        if (saveDraftInput) saveDraftInput.value = '1';
        if (submitProjectInput) submitProjectInput.value = '0';

        // final submit (with FormData preview if enabled)
        try {
          // Force-enable all member inputs to ensure they are included in FormData
          try { form.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(i => i.disabled = false); } catch(e) { /* ignore */ }
          // Ensure canonical payload inputs are present directly inside the form as hidden clones
          try { ensureCanonicalPayloadClones(form); } catch(e) { /* ignore */ }
          const fdPreview = new FormData(form);
          console.debug('Draft FormData member_student_id[]=', fdPreview.getAll('member_student_id[]'));
          console.debug('Draft FormData member_email[]=', fdPreview.getAll('member_email[]'));
          console.debug('Draft FormData member_role[]=', fdPreview.getAll('member_role[]'));

          if (ENABLE_FORMDATA_PREVIEW && !SKIP_DRAFT_PREVIEW) {
            const membersHtml = fdPreview.getAll('member_student_id[]').map((sid, idx) => {
              const email = fdPreview.getAll('member_email[]')[idx] || '';
              const role = fdPreview.getAll('member_role[]')[idx] || '';
              return `<div class="text-left"><strong>#${idx+1}</strong> SID: ${sid} <br/> Email: ${email} <br/> Role: ${role}</div><hr/>`;
            }).join('');
            Swal.fire({
              title: 'Draft FormData Preview',
              html: `<div class="max-h-64 overflow-auto text-sm">${membersHtml || '<em>No member fields detected</em>'}</div>`,
              width: 600,
              showCancelButton: true,
              confirmButtonText: 'Proceed',
              cancelButtonText: 'Cancel',
              confirmButtonColor: '#3085d6'
            }).then((res) => {
              if (res.isConfirmed) {
                try { Swal.close(); } catch(e) {}
                setTimeout(() => { try { form.submit(); } catch(e) {} }, 50);
              } else {
                // user cancelled preview - re-enable required and stop
                isSavingDraft = false;
                return;
              }
            });
          } else {
            // Skip preview for draft saves: close any open modals then submit immediately
            try { Swal.close(); } catch(e) {}
            try { ensureCanonicalPayloadClones(form); } catch(e) {}
            setTimeout(() => { try { form.submit(); } catch(e) {} }, 50);
          }
        } catch (e) {
          console.error('Error preparing FormData preview for draft', e);
          form.submit();
        }
        // reset flag after short delay to prevent double-click issues (form navigation will usually occur)
        setTimeout(() => { isSavingDraft = false; }, 2000);
      });
    });
  })();


  // Enhanced Submit Project with safeAddListener
  safeAddListener('submitProjectBtn', 'click', function (e) {
    e.preventDefault();
    // Validate minimum requirements
    if (!validateFormRequirements()) return;
    // Show review / confirmation modal
    showConfirmationModal();
  });


  // Show confirmation modal with detailed project information
  function showConfirmationModal() {
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);

    // Team Information
    const projectName = formData.get('Project_Name') || 'N/A';
    const teamName = formData.get('Project_Team_Name') || 'N/A';
    const component = formData.get('Project_Component') || 'N/A';
    const section = formData.get('nstp_section') || 'N/A';

    // Get team logo file and detect existing logo (if any)
    const teamLogoFile = formData.get('Project_Logo');
    const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
    let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
    if (teamLogoFile && teamLogoFile.size > 0) {
      teamLogoHTML = `<div class="text-sm text-gray-600">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>`;
    }

    // If submitting and no uploaded or existing logo, show friendly message and abort
    if (!hasExistingLogo && (!teamLogoFile || teamLogoFile.size === 0)) {
      Swal.fire({
        icon: 'warning',
        title: 'Logo Required',
        text: 'Submitting a project requires a team logo. Please upload a logo or save as draft.',
        confirmButtonColor: '#3085d6'
      });
      return;
    }

    // Project Details
    const problems = formData.get('Project_Problems') || 'N/A';
    const goals = formData.get('Project_Goals') || 'N/A';
    const targetCommunity = formData.get('Project_Target_Community') || 'N/A';
    const solution = formData.get('Project_Solution') || 'N/A';
    const outcomes = formData.get('Project_Expected_Outcomes') || 'N/A';

    // Members - extract from visible DOM rows (desktop table rows or mobile cards)
    try { enableVisibleMemberInputs(form); } catch (e) { /* ignore */ }
    const visibleMemberRows = Array.from(document.querySelectorAll('#memberTable tbody tr, .member-card')).filter(r => r && r.offsetParent !== null);
    const memberNames = [];
    const memberRoles = [];
    const memberEmails = [];
    const memberContacts = [];
    visibleMemberRows.forEach(row => {
      try {
        const name = (row.querySelector('input[name="member_name[]"]')?.value || '').trim();
        const role = (row.querySelector('input[name="member_role[]"]')?.value || '').trim();
        const email = (row.querySelector('input[name="member_email[]"]')?.value || '').trim();
        const contact = (row.querySelector('input[name="member_contact[]"]')?.value || '').trim();
        if (name || role || email || contact) {
          memberNames.push(name);
          memberRoles.push(role);
          memberEmails.push(email);
          memberContacts.push(contact);
        }
      } catch (e) { /* ignore */ }
    });

    // Build members HTML for modal display
    let membersHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
    memberNames.forEach((name, idx) => {
      membersHTML += `
        <div class="mb-3 pb-3 ${idx < memberNames.length - 1 ? 'border-b border-gray-300' : ''}">
          <div class="flex items-center gap-2 mb-1">
            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">${idx + 1}</span>
            <strong class="text-gray-800">${name}</strong>
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">${memberRoles[idx] || 'N/A'}</span>
          </div>
          <div class="ml-8 text-xs text-gray-600">
            <div class="flex items-center gap-2 mt-1">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
              ${memberEmails[idx] || 'N/A'}
            </div>
            <div class="flex items-center gap-2 mt-1">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
              ${memberContacts[idx] || 'N/A'}
            </div>
          </div>
        </div>`;
    });
    membersHTML += '</div>';

    // Activities - collect non-empty entries
    const allStages = formData.getAll('stage[]');
    const allActivities = formData.getAll('activities[]');
    const allTimeframes = formData.getAll('timeframe[]');
    const allPointPersons = formData.getAll('point_person[]');
    const allStatuses = formData.getAll('status[]');

    const stages = [];
    const activities = [];
    const timeframes = [];
    const pointPersons = [];
    const statuses = [];

    allStages.forEach((stage, idx) => {
      if (stage && stage.trim() !== '') {
        stages.push(stage);
        activities.push(allActivities[idx] || '');
        timeframes.push(allTimeframes[idx] || '');
        pointPersons.push(allPointPersons[idx] || '');
        statuses.push(allStatuses[idx] || 'Planned');
      }
    });

    // Build activities HTML
    let activitiesHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
    stages.forEach((stage, idx) => {
      const statusColors = {
        'Planned': 'bg-yellow-100 text-yellow-800',
        'Ongoing': 'bg-blue-100 text-blue-800',
        'Completed': 'bg-green-100 text-green-800'
      };
      const statusColor = statusColors[statuses[idx]] || 'bg-gray-100 text-gray-800';
      activitiesHTML += `
        <div class="mb-3 pb-3 ${idx < stages.length - 1 ? 'border-b border-gray-300' : ''}">
          <div class="flex items-start justify-between mb-2">
            <div class="flex-1">
              <div class="font-bold text-gray-800 mb-1">${stage}</div>
              <div class="text-sm text-gray-700 whitespace-pre-wrap">${activities[idx] || 'N/A'}</div>
            </div>
            <span class="text-xs ${statusColor} px-2 py-1 rounded font-medium ml-2">${statuses[idx]}</span>
          </div>
          <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
            <div class="bg-white p-2 rounded">
              <span class="text-gray-500">⏱️ Timeframe:</span>
              <span class="font-medium text-gray-800 whitespace-pre-wrap">${timeframes[idx] || 'N/A'}</span>
            </div>
            <div class="bg-white p-2 rounded">
              <span class="text-gray-500">👤 Person:</span>
              <span class="font-medium text-gray-800 whitespace-pre-wrap">${pointPersons[idx] || 'N/A'}</span>
            </div>
          </div>
        </div>`;
    });
    activitiesHTML += '</div>';

    // Budget processing
    const allBudgetActivities = formData.getAll('budget_activity[]');
    const allBudgetResources = formData.getAll('budget_resources[]');
    const allBudgetPartners = formData.getAll('budget_partners[]');
    const allBudgetAmounts = formData.getAll('budget_amount[]');

    const budgetActivities = [];
    const budgetResources = [];
    const budgetPartners = [];
    const budgetAmounts = [];

    allBudgetActivities.forEach((activity, idx) => {
      if ((activity && activity.trim() !== '') ||
          (allBudgetResources[idx] && allBudgetResources[idx].trim() !== '') ||
          (allBudgetPartners[idx] && allBudgetPartners[idx].trim() !== '') ||
          (allBudgetAmounts[idx] && allBudgetAmounts[idx].trim() !== '')) {
        // If only amount is filled, provide a default activity name
        const activityName = activity || (allBudgetAmounts[idx] && allBudgetAmounts[idx].trim() !== '' ? 'Budget Item' : '');
        budgetActivities.push(activityName);
        budgetResources.push(allBudgetResources[idx] || '');
        budgetPartners.push(allBudgetPartners[idx] || '');
        budgetAmounts.push(allBudgetAmounts[idx] || '');
      }
    });

    // Build budget HTML
    let budgetHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
    let totalBudget = 0;

    budgetActivities.forEach((activity, idx) => {
      if (activity || budgetResources[idx] || budgetPartners[idx] || budgetAmounts[idx]) {
        let amountValue = budgetAmounts[idx] || '0';
        amountValue = amountValue.replace(/[₱,]/g, '').trim();
        const numericAmount = parseFloat(amountValue) || 0;
        totalBudget += numericAmount;
        const displayAmount = numericAmount > 0 ? `₱ ${numericAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱ 0.00';

        budgetHTML += `
          <div class="mb-3 pb-3 ${idx < budgetActivities.length - 1 ? 'border-b border-gray-300' : ''}">
            <div class="flex items-start justify-between mb-2">
              <div class="font-bold text-gray-800">${activity || 'Activity ' + (idx + 1)}</div>
              <div class="bg-green-100 text-green-800 px-3 py-1 rounded-lg font-bold text-sm">${displayAmount}</div>
            </div>
            <div class="space-y-1 text-xs">
              <div class="flex items-start gap-2">
                <span class="text-gray-500 font-medium min-w-[80px]">📦 Resources:</span>
                <span class="text-gray-700 whitespace-pre-wrap">${budgetResources[idx] || 'N/A'}</span>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-gray-500 font-medium min-w-[80px]">🤝 Partners:</span>
                <span class="text-gray-700 whitespace-pre-wrap">${budgetPartners[idx] || 'N/A'}</span>
              </div>
            </div>
          </div>`;
      }
    });

    if (budgetActivities.length > 0 && totalBudget > 0) {
      const formattedTotal = `₱ ${totalBudget.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
      budgetHTML += `
        <div class="mt-3 pt-3 border-t-2 border-yellow-300">
          <div class="flex items-center justify-between bg-yellow-100 px-4 py-3 rounded-lg">
            <span class="text-base font-bold text-gray-800">Total Budget:</span>
            <span class="text-lg font-bold text-green-700">${formattedTotal}</span>
          </div>
        </div>`;
    }
    budgetHTML += '</div>';

    // First Modal: Review all details
    Swal.fire({
      title: '<div class="text-2xl font-bold text-gray-800">📋 Review Project Proposal</div>',
      html: `
        <div class="text-left space-y-4 max-h-[500px] overflow-y-auto px-3 py-2">
          <!-- Team Information -->
          <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <h3 class="font-bold text-blue-700 mb-3 text-lg flex items-center gap-2">
              <span>🖼️</span> Team Information
            </h3>
            <div class="space-y-2">
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Project Name</span>
                <div class="text-sm font-bold text-gray-800">${projectName}</div>
              </div>
              <div class="grid grid-cols-3 gap-2">
                <div class="bg-white p-2 rounded">
                  <span class="text-xs text-gray-500 uppercase font-semibold">Team</span>
                  <div class="text-sm font-medium text-gray-800">${teamName}</div>
                </div>
                <div class="bg-white p-2 rounded">
                  <span class="text-xs text-gray-500 uppercase font-semibold">Component</span>
                  <div class="text-sm font-medium text-gray-800">${component}</div>
                </div>
                <div class="bg-white p-2 rounded">
                  <span class="text-xs text-gray-500 uppercase font-semibold">Section</span>
                  <div class="text-sm font-medium text-gray-800">${section}</div>
                </div>
              </div>
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Team Logo</span>
                ${teamLogoHTML}
              </div>
            </div>
          </div>
          <!-- Members -->
          <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
            <h3 class="font-bold text-purple-700 mb-3 text-lg flex items-center gap-2">
              <span>👥</span> Team Members
              <span class="text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded-full">${memberNames.length} members</span>
            </h3>
            ${membersHTML}
          </div>
          <!-- Project Details -->
          <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
            <h3 class="font-bold text-green-700 mb-3 text-lg flex items-center gap-2">
              <span>🎯</span> Project Details
            </h3>
            <div class="space-y-3">
              <div class="bg-white p-3 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Issues/Problem</span>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${problems.substring(0, 500)}${problems.length > 500 ? '...' : ''}</div>
              </div>
              <div class="bg-white p-3 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Goal/Objectives</span>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${goals.substring(0, 500)}${goals.length > 500 ? '...' : ''}</div>
              </div>
              <div class="bg-white p-3 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Target Community</span>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${targetCommunity.substring(0, 300)}${targetCommunity.length > 300 ? '...' : ''}</div>
              </div>
              <div class="bg-white p-3 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Solutions/Activities to be implemented</span>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${solution.substring(0, 500)}${solution.length > 500 ? '...' : ''}</div>
              </div>
              <div class="bg-white p-3 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Expected Outcomes</span>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${outcomes.substring(0, 500)}${outcomes.length > 500 ? '...' : ''}</div>
              </div>
            </div>
          </div>
          <!-- Activities -->
          <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-orange-500">
            <h3 class="font-bold text-orange-700 mb-3 text-lg flex items-center gap-2">
              <span>📅</span> Project Activities
              <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">${stages.length} activities</span>
            </h3>
            ${activitiesHTML}
          </div>
          <!-- Budget -->
          <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
            <h3 class="font-bold text-yellow-700 mb-3 text-lg flex items-center gap-2">
              <span>💰</span> Budget Items
            </h3>
            ${budgetHTML}
          </div>
        </div>
      `,
      width: '700px',
      showCancelButton: true,
      confirmButtonColor: '#2b50ff',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '✓ Proceed to Submit',
      cancelButtonText: '✕ Cancel',
      reverseButtons: true,
      customClass: {
        container: 'review-modal',
        popup: 'rounded-2xl',
        confirmButton: 'font-bold px-6 py-3 rounded-lg',
        cancelButton: 'font-bold px-6 py-3 rounded-lg'
      }
    }).then((reviewResult) => {
      if (reviewResult.isConfirmed) {
        // Second Modal: Final confirmation
        Swal.fire({
          title: 'Submit Project?',
          text: 'Once submitted, your project will be sent for review.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#2b50ff',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, submit it!',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        }).then((confirmResult) => {
          if (confirmResult.isConfirmed) {
            // Final: set flags, clean up and submit
            const saveDraftInput = document.getElementById('saveDraftInput');
            const submitProjectInput = document.getElementById('submitProjectInput');
            if (saveDraftInput) saveDraftInput.value = '0';
            if (submitProjectInput) submitProjectInput.value = '1';

            // cleanup duplicate/empty rows
            dedupeEmptyActivityRows();
            dedupeEmptyBudgetRows();
            removeAllEmptyBudgetRows();

            // disable hidden inputs so only visible values are submitted
            prepareFormForSubmit(form);
            // Ensure visible member inputs are enabled (fix for submit/draft mismatch)
            try { enableVisibleMemberInputs(form); } catch (e) { /* ignore */ }
            // Final safety: force-enable all member inputs so none are omitted from FormData
            try { form.querySelectorAll('input[name^="member_"], select[name^="member_"], textarea[name^="member_"]').forEach(i => i.disabled = false); } catch(e) { /* ignore */ }
            // Ensure canonical payload inputs are present directly inside the form as hidden clones
            try { ensureCanonicalPayloadClones(form); } catch(e) { /* ignore */ }
            // Debug: Log budget data being submitted and preview FormData members
            try {
              const fdFinal = new FormData(form);
              console.debug('Final FormData member_student_id[]=', fdFinal.getAll('member_student_id[]'));
              console.debug('Final FormData member_email[]=', fdFinal.getAll('member_email[]'));
              console.debug('Final FormData member_role[]=', fdFinal.getAll('member_role[]'));

              if (ENABLE_FORMDATA_PREVIEW) {
                const membersHtml = fdFinal.getAll('member_student_id[]').map((sid, idx) => {
                  const email = fdFinal.getAll('member_email[]')[idx] || '';
                  const role = fdFinal.getAll('member_role[]')[idx] || '';
                  return `<div class="text-left"><strong>#${idx+1}</strong> SID: ${sid} <br/> Email: ${email} <br/> Role: ${role}</div><hr/>`;
                }).join('');
                Swal.fire({
                  title: 'Final FormData Preview',
                  html: `<div class="max-h-64 overflow-auto text-sm">${membersHtml || '<em>No member fields detected</em>'}</div>`,
                  width: 600,
                  showCancelButton: true,
                  confirmButtonText: 'Proceed',
                  cancelButtonText: 'Cancel',
                  confirmButtonColor: '#3085d6'
                }).then((res) => {
                  if (res.isConfirmed) {
                    try { Swal.close(); } catch(e) {}
                    setTimeout(() => { try { form.submit(); } catch(e) {} }, 50);
                  } else {
                    return;
                  }
                });
              } else {
                form.submit();
              }
            } catch (e) {
              console.error('Error preparing final FormData preview', e);
              form.submit();
            }
          }
        });
      }
    });
  }


    // Ensure canonical payload inputs are cloned into the form as hidden inputs
    function ensureCanonicalPayloadClones(form) {
      try {
        if (!form) return;
        const payload = document.getElementById('memberPayload');
        if (!payload) return;
        // Make sure payload reflects visible UI before cloning
        try { syncCanonicalPayloadWithVisible(); } catch(e) { /* ignore */ }
        // Remove any previous clones we added
        Array.from(form.querySelectorAll('input[data-cloned-from="memberPayload"], select[data-cloned-from="memberPayload"], textarea[data-cloned-from="memberPayload"]')).forEach(n => n.remove());
        // Build canonical records by student id from payload (group email/role by sid)
        const sidInputs = Array.from(payload.querySelectorAll('input[name="member_student_id[]"]'));
        const emailInputs = Array.from(payload.querySelectorAll('input[name="member_email[]"]'));
        const roleInputs = Array.from(payload.querySelectorAll('input[name="member_role[]"]'));
        const records = [];
        // Map by data-manual-sid when available
        const seenSids = new Set();
        sidInputs.forEach(sidEl => {
          const sid = (sidEl.value || '').toString();
          if (!sid) return;
          if (seenSids.has(sid)) return;
          seenSids.add(sid);
          // find matching email/role inputs by data-manual-sid
          const emailEl = payload.querySelector('input[name="member_email[]"][data-manual-sid="' + sid + '"]');
          const roleEl = payload.querySelector('input[name="member_role[]"][data-manual-sid="' + sid + '"]');
          const email = emailEl ? (emailEl.value || '') : '';
          const role = roleEl ? (roleEl.value || '') : '';
          records.push({ sid: sid, email: email, role: role });
        });

        // If payload had no explicit sid inputs but had email-only entries, map them
        if (records.length === 0 && emailInputs.length > 0) {
          emailInputs.forEach((eEl, idx) => {
            const email = (eEl.value || '').toString();
            if (!email) return;
            const roleEl = roleInputs[idx] || null;
            const role = roleEl ? (roleEl.value || '') : '';
            records.push({ sid: '', email: email, role: role });
          });
        }

        // Build ordered list: owner first if present among payload sids; otherwise keep payload order.
        const ordered = [];
        // If owner present in records, put first
        const ownerIndex = records.findIndex(r => r.sid && r.sid.toString() === OWNER_SID?.toString());
        if (ownerIndex !== -1) {
          ordered.push(records[ownerIndex]);
        }
        // Add remaining records skipping owner duplicate
        records.forEach(r => {
          if (r.sid && r.sid.toString() === OWNER_SID?.toString()) return;
          ordered.push(r);
        });

        // Append clones into the form in computed order, avoid duplicates
        const appended = new Set();
        ordered.forEach(rec => {
          try {
            const sidKey = rec.sid || rec.email || ('__' + Math.random().toString(36).slice(2,8));
            if (appended.has(sidKey)) return;
            appended.add(sidKey);

            if (rec.sid) {
              const hiddenSid = document.createElement('input');
              hiddenSid.type = 'hidden'; hiddenSid.name = 'member_student_id[]'; hiddenSid.value = rec.sid;
              hiddenSid.dataset.clonedFrom = 'memberPayload'; hiddenSid.dataset.manualSid = rec.sid;
              form.appendChild(hiddenSid);
            }

            const hiddenEmail = document.createElement('input');
            hiddenEmail.type = 'hidden'; hiddenEmail.name = 'member_email[]'; hiddenEmail.value = rec.email || '';
            if (rec.sid) hiddenEmail.dataset.manualSid = rec.sid;
            hiddenEmail.dataset.clonedFrom = 'memberPayload';
            form.appendChild(hiddenEmail);

            const hiddenRole = document.createElement('input');
            hiddenRole.type = 'hidden'; hiddenRole.name = 'member_role[]'; hiddenRole.value = rec.role || '';
            if (rec.sid) hiddenRole.dataset.manualSid = rec.sid;
            hiddenRole.dataset.clonedFrom = 'memberPayload';
            form.appendChild(hiddenRole);
          } catch (e) { /* ignore per-record errors */ }
        });
      } catch (e) { /* ignore */ }
    }

    // Sync memberPayload with currently visible member rows.
    // Ensures canonical payload contains only members visible in the UI (owner always kept).
    function syncCanonicalPayloadWithVisible() {
      try {
        const payload = document.getElementById('memberPayload');
        if (!payload) return;

        // Collect visible member emails (desktop OR mobile rows)
        const visibleRows = Array.from(document.querySelectorAll('#memberTable tbody tr, .member-card')).filter(r => r && r.offsetParent !== null);
        const visibleEmails = new Set();
        visibleRows.forEach(row => {
          try {
            const emailInput = row.querySelector('input[name="member_email[]"]');
            if (emailInput && emailInput.value) visibleEmails.add(emailInput.value.toString().trim());
          } catch (e) { /* ignore */ }
        });

        // Keep owner always (by manualSid matching OWNER_SID)
        const keepSids = new Set();
        if (typeof OWNER_SID !== 'undefined' && OWNER_SID) {
          keepSids.add(OWNER_SID.toString());
        }

        // Walk payload and remove any canonical inputs that do not match visible emails or owner
        // Group payload inputs by manualSid/manualEmail for safety
        const payloadInputs = Array.from(payload.querySelectorAll('input'));
        // Determine manualSids present in payload that match visible emails
        const payloadSidsToKeep = new Set();
        payloadInputs.forEach(pi => {
          try {
            const manualEmail = pi.dataset && pi.dataset.manualEmail ? pi.dataset.manualEmail.toString() : '';
            const manualSid = pi.dataset && pi.dataset.manualSid ? pi.dataset.manualSid.toString() : '';
            if (manualSid && keepSids.has(manualSid)) payloadSidsToKeep.add(manualSid);
            if (manualEmail && visibleEmails.has(manualEmail)) {
              if (manualSid) payloadSidsToKeep.add(manualSid);
            }
          } catch (e) { /* ignore */ }
        });

        // Remove payload children that are not in payloadSidsToKeep (and not owner)
        payload.querySelectorAll('input').forEach(pi => {
          try {
            const manualSid = pi.dataset && pi.dataset.manualSid ? pi.dataset.manualSid.toString() : '';
            const manualEmail = pi.dataset && pi.dataset.manualEmail ? pi.dataset.manualEmail.toString() : '';
            // If has a manualSid, keep only if in payloadSidsToKeep
            if (manualSid) {
              if (!payloadSidsToKeep.has(manualSid)) pi.remove();
            } else if (manualEmail) {
              // email-only entries: keep only if email present in visible
              if (!visibleEmails.has(manualEmail)) pi.remove();
            } else {
              // Unmarked inputs: safe to remove
              pi.remove();
            }
          } catch (e) { /* ignore */ }
        });
      } catch (e) { console.debug('syncCanonicalPayloadWithVisible error', e); }
    }



  // Member selection modal
  document.getElementById('openMemberModal').addEventListener('click', function() {
    loadMemberList();
    document.getElementById('memberModal').classList.remove('hidden');
  });
 
  document.getElementById('openMemberModalMobile').addEventListener('click', function() {
    loadMemberList();
    document.getElementById('memberModal').classList.remove('hidden');
  });
 
  document.getElementById('closeMemberModal').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('memberModal').classList.add('hidden');
  });
 
  document.getElementById('cancelMemberSelection').addEventListener('click', function(event) {
    event.preventDefault();
    document.getElementById('memberModal').classList.add('hidden');
    // Show cancel confirmation
    Swal.fire({
      icon: 'info',
      title: 'Cancelled',
      text: 'Member selection has been cancelled.',
      timer: 1500,
      showConfirmButton: false
    });
  });


  // Load member list from same section and component
  function loadMemberList() {
    const memberList = document.getElementById('memberList');
    memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
   
    // Fetch students from the same section and component
    fetch('{{ route("projects.students.same-section") }}')
      .then(response => response.json())
      .then(students => {
        if (students.length === 0) {
          memberList.innerHTML = '<p class="text-center text-gray-500">No students found in your section and component.</p>';
          return;
        }
       
        let html = '';
        students.forEach(student => {
          // Skip if this member is already added
          if (addedMemberEmails.has(student.email)) {
            return;
          }
          
          html += `
            <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
              <div class="flex items-center">
                <input type="checkbox" id="member${student.id}" name="available_members[]" value="${student.id}" class="mr-2" data-name="${student.name}" data-email="${student.email}" data-contact="${student.contact || ''}">
                <label for="member${student.id}" class="text-sm">
                  <span class="font-medium">${student.name}</span> -
                  <span class="text-gray-600">${student.email}</span>
                  <span class="text-gray-500 text-xs block">${student.contact || 'No contact number'}</span>
                </label>
              </div>
              <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Same Section</span>
            </div>
          `;
        });
       
        if (html === '') {
          memberList.innerHTML = '<p class="text-center text-gray-500">All students from your section are already added to the team.</p>';
        } else {
          memberList.innerHTML = html;
        }
      })
      .catch(error => {
        console.error('Error fetching students:', error);
        memberList.innerHTML = '<p class="text-center text-red-500">Error loading students. Please try again.</p>';
      });
  }


  // Add selected members to the form
  document.getElementById('addSelectedMembers').addEventListener('click', function(event) {
    event.preventDefault();
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
   
    // Check if any members are selected
    if (selectedMembers.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Members Selected',
        text: 'Please select at least one member to add to your team.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#3b82f6'
      });
      return;
    }
   
    selectedMembers.forEach(checkbox => {
      const memberId = checkbox.value;
      const memberName = checkbox.dataset.name;
      const memberEmail = checkbox.dataset.email;
      const memberContact = checkbox.dataset.contact || '';
      
      // Add to tracking set to prevent future duplicates
      if (memberEmail) {
        addedMemberEmails.add(memberEmail);
      }
      
      // Add to tracking set
      if (memberEmail) {
        addedMemberEmails.add(memberEmail);
      }
     
      // Add to desktop table
      const desktopTable = document.querySelector('#memberTable tbody');
      if (desktopTable) {
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50 transition-colors';
        newRow.innerHTML = `
          <td class="px-6 py-4">
            <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberName}" readonly>
            <input type="hidden" name="member_student_id[]" value="${memberId}">
          </td>
          <td class="px-6 py-4">
            <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required>
          </td>
          <td class="px-6 py-4">
            <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberEmail}" readonly>
          </td>
          <td class="px-6 py-4">
              <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required readonly>
          </td>
          <td class="px-6 py-4 text-center">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
              Remove
            </button>
          </td>
        `;
        // Mark as manually added and ensure inputs enabled
        newRow.querySelectorAll('input, select, textarea').forEach(i => { try { i.disabled = false; i.dataset.manuallyAdded = '1'; } catch(e){} });
        // Add new member at the end of the table
        desktopTable.appendChild(newRow);
        // Move the hidden student_id from the visible row into the canonical payload
          try {
            const payload = document.getElementById('memberPayload');
            const sidInput = newRow.querySelector('input[name="member_student_id[]"]');
            if (payload) {
              // Always remove the visible row's hidden sid to avoid duplicate inputs.
              if (sidInput) sidInput.remove();
              // If a canonical entry for this sid already exists, do not create duplicates.
              const existing = payload.querySelector('[data-manual-sid="' + memberId + '"]');
              if (!existing) {
                // Create a canonical hidden student id input (do NOT move the visible one)
                const hiddenSid = document.createElement('input');
                hiddenSid.type = 'hidden'; hiddenSid.name = 'member_student_id[]'; hiddenSid.value = memberId;
                hiddenSid.dataset.manuallyAdded = '1'; hiddenSid.dataset.manualSid = memberId;
                payload.appendChild(hiddenSid);

                // Create canonical hidden email and role inputs and attach dataset markers
                const hiddenEmail = document.createElement('input');
                hiddenEmail.type = 'hidden'; hiddenEmail.name = 'member_email[]'; hiddenEmail.value = memberEmail || '';
                hiddenEmail.dataset.manuallyAdded = '1'; hiddenEmail.dataset.manualSid = memberId; hiddenEmail.dataset.manualEmail = memberEmail || '';
                payload.appendChild(hiddenEmail);
                const hiddenRole = document.createElement('input');
                hiddenRole.type = 'hidden'; hiddenRole.name = 'member_role[]'; hiddenRole.value = '';
                hiddenRole.dataset.manuallyAdded = '1'; hiddenRole.dataset.manualSid = memberId; hiddenRole.dataset.manualEmail = memberEmail || '';
                payload.appendChild(hiddenRole);
                // Sync visible role input to hidden role
                const visibleRole = newRow.querySelector('input[name="member_role[]"]');
                if (visibleRole) {
                  visibleRole.addEventListener('input', function(e){ try { hiddenRole.value = e.target.value; } catch(err){} });
                }
              } else {
                // canonical entry exists: ensure visible role syncs to existing hidden role
                const visibleRole = newRow.querySelector('input[name="member_role[]"]');
                const existingHiddenRole = payload.querySelector('input[name="member_role[]"][data-manual-sid="' + memberId + '"]');
                if (visibleRole && existingHiddenRole) {
                  visibleRole.addEventListener('input', function(e){ try { existingHiddenRole.value = e.target.value; } catch(err){} });
                }
              }
            }
          } catch (e) { /* ignore payload append errors */ }
      }
     
      // Add to mobile view
      const mobileContainer = document.getElementById('memberContainer');
      if (mobileContainer) {
        const newCard = document.createElement('div');
        newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
        newCard.innerHTML = `
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
            <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberName}" readonly>
            <input type="hidden" name="member_student_id[]" value="${memberId}">
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
            <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
            <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberEmail}" readonly>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required readonly>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
          </div>
        `;
        // Mark as manually added and ensure inputs enabled
        newCard.querySelectorAll('input, select, textarea').forEach(i => { try { i.disabled = false; i.dataset.manuallyAdded = '1'; } catch(e){} });
        // Add new member card at the end of the container
        mobileContainer.appendChild(newCard);
        // Move hidden student id into memberPayload and create canonical hidden inputs
        try {
          const payload = document.getElementById('memberPayload');
          const sidInput = newCard.querySelector('input[name="member_student_id[]"]');
          if (payload) {
            // Always remove the visible row's hidden sid to avoid duplicate inputs.
            if (sidInput) sidInput.remove();
            const existing = payload.querySelector('[data-manual-sid="' + memberId + '"]');
            if (!existing) {
              const hiddenSid = document.createElement('input');
              hiddenSid.type = 'hidden'; hiddenSid.name = 'member_student_id[]'; hiddenSid.value = memberId;
              hiddenSid.dataset.manuallyAdded = '1'; hiddenSid.dataset.manualSid = memberId;
              payload.appendChild(hiddenSid);
              const hiddenEmail = document.createElement('input');
              hiddenEmail.type = 'hidden'; hiddenEmail.name = 'member_email[]'; hiddenEmail.value = memberEmail || '';
              hiddenEmail.dataset.manuallyAdded = '1'; hiddenEmail.dataset.manualSid = memberId; hiddenEmail.dataset.manualEmail = memberEmail || '';
              payload.appendChild(hiddenEmail);
              const hiddenRole = document.createElement('input');
              hiddenRole.type = 'hidden'; hiddenRole.name = 'member_role[]'; hiddenRole.value = '';
              hiddenRole.dataset.manuallyAdded = '1'; hiddenRole.dataset.manualSid = memberId; hiddenRole.dataset.manualEmail = memberEmail || '';
              payload.appendChild(hiddenRole);
              const visibleRole = newCard.querySelector('input[name="member_role[]"]');
              if (visibleRole) {
                visibleRole.addEventListener('input', function(e){ try { hiddenRole.value = e.target.value; } catch(err){} });
              }
            } else {
              const visibleRole = newCard.querySelector('input[name="member_role[]"]');
              const existingHiddenRole = payload.querySelector('input[name="member_role[]"][data-manual-sid="' + memberId + '"]');
              if (visibleRole && existingHiddenRole) {
                visibleRole.addEventListener('input', function(e){ try { existingHiddenRole.value = e.target.value; } catch(err){} });
              }
            }
          }
        } catch(e) { /* ignore */ }
      }
    });
   
   
    // Close modal
    document.getElementById('memberModal').classList.add('hidden');
    
    // Show success message
    Swal.fire({
      icon: 'success',
      title: 'Members Added Successfully!',
      text: `${selectedMembers.length} member(s) have been added to your team.`,
      timer: 2000,
      showConfirmButton: false
    });
    // Debug: after adding, print current member arrays in the DOM and persist last added set
    try {
      const names = Array.from(document.querySelectorAll('input[name="member_name[]"]')).map(i => i.value || '');
      const emails = Array.from(document.querySelectorAll('input[name="member_email[]"]')).map(i => i.value || '');
      const ids = Array.from(document.querySelectorAll('input[name="member_student_id[]"]')).map(i => i.value || '');
      // Ensure payload canonical entries are in sync with visible rows
      try { syncCanonicalPayloadWithVisible(); } catch(e) {}
      console.debug('After addSelectedMembers - member names:', names);
      console.debug('After addSelectedMembers - member emails:', emails);
      console.debug('After addSelectedMembers - member student ids:', ids);
      try { localStorage.setItem('lastAddedMembers', JSON.stringify({names, emails, ids})); } catch(e) {}
    } catch (e) { console.debug('Debug after addSelectedMembers failed', e); }
  });


  // initial remove buttons


  // Enhanced validation function with comprehensive error checking
  function validateFormRequirements() {
    const form = document.getElementById('projectForm');
    if (!form) {
      console.error('Project form not found');
      return false;
    }
    
    const formData = new FormData(form);
    const errors = [];
    
    // Validate basic project fields
    const projectName = formData.get('Project_Name')?.trim();
    const teamName = formData.get('Project_Team_Name')?.trim();
    const component = formData.get('Project_Component')?.trim();
    const section = formData.get('nstp_section')?.trim();
    const problems = formData.get('Project_Problems')?.trim();
    const goals = formData.get('Project_Goals')?.trim();
    const targetCommunity = formData.get('Project_Target_Community')?.trim();
    const solution = formData.get('Project_Solution')?.trim();
    const outcomes = formData.get('Project_Expected_Outcomes')?.trim();
    
    if (!projectName) errors.push('The Project Name field is required.');
    if (!teamName) errors.push('The Team Name field is required.');
    if (!component) errors.push('The Component field is required.');
    if (!section) errors.push('The NSTP Section field is required.');
    if (!problems) errors.push('The Project Problems field is required.');
    if (!goals) errors.push('The Project Goals field is required.');
    if (!targetCommunity) errors.push('The Target Community field is required.');
    if (!solution) errors.push('The Project Solution field is required.');
    if (!outcomes) errors.push('The Expected Outcomes field is required.');
    
    // Validate team logo
    const logoFile = formData.get('Project_Logo');
    const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
    if (!hasExistingLogo && (!logoFile || logoFile.size === 0)) {
      errors.push('A team logo is required for project submission.');
    }

    // Validate team members
    // Prefer extracting members from visible DOM rows (desktop table rows or mobile cards)
    try { enableVisibleMemberInputs(form); } catch (e) { /* ignore */ }
    const visibleMemberRows = Array.from(document.querySelectorAll('#memberTable tbody tr, .member-card')).filter(r => r && r.offsetParent !== null);
    // Filter out duplicates and empty entries; use student id when available, fallback to email.
    const uniqueMembers = [];
    const memberMap = new Map();
    visibleMemberRows.forEach((row, i) => {
      try {
        const name = (row.querySelector('input[name="member_name[]"]')?.value || '').trim();
        const role = (row.querySelector('input[name="member_role[]"]')?.value || '').trim();
        const email = (row.querySelector('input[name="member_email[]"]')?.value || '').trim();
        const contact = (row.querySelector('input[name="member_contact[]"]')?.value || '').trim();
        const sid = (row.querySelector('input[name="member_student_id[]"]')?.value || '').trim();

        if (!(name || role || email || contact || sid)) return;

        const key = sid || (email ? email.toLowerCase() : '__idx_' + i);
        const score = (name ? 1 : 0) + (role ? 4 : 0) + (email ? 1 : 0) + (contact ? 1 : 0);

        if (memberMap.has(key)) {
          const existing = memberMap.get(key);
          if (score > existing._score) {
            memberMap.set(key, { name, role, email, contact, _score: score });
          }
        } else {
          memberMap.set(key, { name, role, email, contact, _score: score });
        }
      } catch (e) { /* ignore malformed row */ }
    });

    // Re-index uniqueMembers in stable order
    Array.from(memberMap.values()).forEach((m, idx) => {
      uniqueMembers.push({ name: m.name, role: m.role, email: m.email, contact: m.contact, index: idx + 1 });
    });

    let validMembers = 0;
    uniqueMembers.forEach((member) => {
      const missingFields = [];
      if (!member.name) missingFields.push('Name');
      if (!member.role) missingFields.push('Role');
      if (!member.email) missingFields.push('Email');
      if (!member.contact) missingFields.push('Contact');

      if (missingFields.length > 0) {
        errors.push(`Team member ${member.index}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
      } else {
        validMembers++;
      }
    });
    if (validMembers === 0) errors.push('At least one complete team member info is required.');

    // Validate activities
    const allStages = formData.getAll('stage[]');
    const allActivities = formData.getAll('activities[]');
    const allTimeframes = formData.getAll('timeframe[]');
    const allImplementationDates = formData.getAll('implementation_date[]');
    const allPointPersons = formData.getAll('point_person[]');
    const allStatuses = formData.getAll('status[]');

    let validActivities = 0;
    for (let i = 0; i < allStages.length; i++) {
      const stage = allStages[i]?.trim();
      const activity = allActivities[i]?.trim();
      const timeframe = allTimeframes[i]?.trim();
      const implementationDate = allImplementationDates[i]?.trim();
      const person = allPointPersons[i]?.trim();
      const status = allStatuses[i]?.trim() || 'Planned'; // Default to 'Planned' if empty
      
      // Check if any activity field has content (indicating this row is being used)
      if (stage || activity || timeframe || implementationDate || person) {
        const missingFields = [];
        if (!stage) missingFields.push('Stage');
        if (!activity) missingFields.push('Specific Activities');
        if (!timeframe) missingFields.push('Time Frame');
        if (!implementationDate) missingFields.push('Implementation Date');
        if (!person) missingFields.push('Point Persons');
        // Note: Status is not included in missing fields since it defaults to 'Planned'
        
        if (missingFields.length > 0) {
          errors.push(`Activity ${i+1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
        } else {
          validActivities++;
        }
      }
    }
    if (validActivities === 0) errors.push('At least one complete activity is required.');

    // Validate budget rows (optional, but if partially filled must be complete)
    const allBudgetActivities = formData.getAll('budget_activity[]');
    const allBudgetResources = formData.getAll('budget_resources[]');
    const allBudgetPartners = formData.getAll('budget_partners[]');
    const allBudgetAmounts = formData.getAll('budget_amount[]');

    for (let i = 0; i < allBudgetActivities.length; i++) {
      const act = allBudgetActivities[i]?.trim();
      const res = allBudgetResources[i]?.trim();
      const part = allBudgetPartners[i]?.trim();
      const amt = allBudgetAmounts[i]?.trim();
      
      if (act || res || part || amt) {
        const missingFields = [];
        if (!act) missingFields.push('Activity');
        if (!res) missingFields.push('Resources needed');
        if (!part) missingFields.push('Partner agencies');
        if (!amt) missingFields.push('Amount');
        
        if (missingFields.length > 0) {
          errors.push(`Budget row ${i+1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
        }
      }
    }

    // Show errors if any
    if (errors.length > 0) {
      const errorList = errors.join('<br>');
      Swal.fire({
        icon: 'error',
        title: 'Validation Error!',
        html: `<div class="text-center">${errorList}</div>`,
        confirmButtonColor: '#3085d6',
        width: '600px'
      });
      return false;
    }
    
    return true;
  }

</script>
@endsection

