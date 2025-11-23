@php
    $isDraftMode = isset($isDraft) && $isDraft;
    $isStaffEdit = true; // Flag to indicate this is staff editing mode
@endphp

<!-- TEAM INFORMATION -->
<div class="rounded-2xl bg-gray-100 p-6 shadow-subtle space-y-4">
  <h2 class="text-2xl font-bold flex items-center gap-2">
    <span class="text-3xl">🖼️</span> Team Information
  </h2>

  <div class="space-y-3">
    <div>
      <label class="block text-lg font-medium">Project Name<span class="text-red-500">*</span></label>
      <input name="Project_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Project" required value="{{ old('Project_Name', $project->Project_Name) }}">
    </div>
    <div>
      <label class="block text-lg font-medium">Team Name<span class="text-red-500">*</span></label>
      <input name="Project_Team_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Team" required value="{{ old('Project_Team_Name', $project->Project_Team_Name) }}">
    </div>
    <div>
      <label class="block text-lg font-medium">Team Logo<span class="text-red-500">*</span></label>
      @if($project->Project_Logo)
        <div class="mb-2">
          <p class="text-sm text-gray-600">Current Logo:</p>
          <img src="{{ asset('storage/' . $project->Project_Logo) }}" alt="Current Logo" class="w-32 h-32 object-contain rounded-lg border border-gray-200 p-2">
        </div>
        <p class="text-sm text-gray-600 mb-2">Upload a new logo to replace the current one (optional if logo exists)</p>
      @endif
      <input type="file" name="Project_Logo" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" {{ $project->Project_Logo ? '' : 'required' }}>
      <p class="text-sm text-gray-600 mt-1">Note: Logo is required when submitting a project, but optional when saving as draft.</p>
    </div>
    <!-- Component Dropdown -->
    <div class="relative">
      <label class="block text-lg font-medium">Component<span class="text-red-500">*</span></label>
      <select name="Project_Component" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white relative z-10 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" required>
        <option value="">Select Component</option>
        <option value="LTS" {{ old('Project_Component', $project->Project_Component) === 'LTS' ? 'selected' : '' }}>Literacy Training Service (LTS)</option>
        <option value="CWTS" {{ old('Project_Component', $project->Project_Component) === 'CWTS' ? 'selected' : '' }}>Civic Welfare Training Service (CWTS)</option>
        <option value="ROTC" {{ old('Project_Component', $project->Project_Component) === 'ROTC' ? 'selected' : '' }}>Reserve Officers' Training Corps (ROTC)</option>
      </select>
    </div>
    <!-- Section Dropdown -->
    <div class="relative">
      <label class="block text-lg font-medium">Section<span class="text-red-500">*</span></label>
      <select name="nstp_section" required
              class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white text-black relative z-10 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
        <option value="" disabled>Select Section</option>
        @foreach (range('A', 'Z') as $letter)
          @php $value = "Section $letter"; @endphp
          <option value="{{ $value }}" {{ old('nstp_section', $project->Project_Section) === $value ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
      </select>
    </div>
  </div>

  
</div>

<!-- MEMBER PROFILE -->
<div class="rounded-2xl bg-gray-100 p-4 md:p-6 shadow-subtle">
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
        <tbody id="memberTableBody" class="divide-y divide-gray-400">
          <!-- Members will be loaded here dynamically -->
        </tbody>
      </table>
    </div>
   
    <!-- Add Member Button -->
    <div class="mt-4">
      <button type="button" id="openMemberModal" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
        + Add Member from Project Section/Component
      </button>
      <p class="text-xs text-gray-600 mt-1">Members will be filtered by project's original section: <strong>{{ $project->Project_Component }} - {{ $project->Project_Section }}</strong></p>
    </div>
  </div>

  <!-- Mobile Card View -->
  <div id="memberContainer" class="md:hidden mt-4 space-y-3">
    <!-- Members will be loaded here dynamically -->
   
    <!-- Add Member Button -->
    <div class="mt-4">
      <button type="button" id="openMemberModalMobile" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors shadow-sm">
        + Add Member from Project Section/Component
      </button>
      <p class="text-xs text-gray-600 mt-1">Members: <strong>{{ $project->Project_Component }} - {{ $project->Project_Section }}</strong></p>
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
      <textarea name="Project_Problems" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required>{{ old('Project_Problems', $project->Project_Problems) }}</textarea>
    </div>
    <div>
      <label class="block text-lg font-medium">Goal/Objectives<span class="text-red-500">*</span></label>
      <textarea name="Project_Goals" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required>{{ old('Project_Goals', $project->Project_Goals) }}</textarea>
    </div>
    <div>
      <label class="block text-lg font-medium">Target Community<span class="text-red-500">*</span></label>
      <textarea name="Project_Target_Community" rows="2" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required>{{ old('Project_Target_Community', $project->Project_Target_Community) }}</textarea>
    </div>
    <div>
      <label class="block text-lg font-medium">Solutions/Activities to be implemented<span class="text-red-500">*</span></label>
      <textarea name="Project_Solution" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required>{{ old('Project_Solution', $project->Project_Solution) }}</textarea>
    </div>
    <div>
      <label class="block text-lg font-medium">Expected Outcomes<span class="text-red-500">*</span></label>
      <textarea name="Project_Expected_Outcomes" rows="5" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" required>{{ old('Project_Expected_Outcomes', $project->Project_Expected_Outcomes) }}</textarea>
    </div>
  </div>
</div>

<!-- PROJECT ACTIVITIES -->
<div class="proposal-section section-gap">
  <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
    <span class="text-2xl md:text-3xl">📅</span> Project Activities
  </h2>

  <!-- Desktop Table View -->
  <div class="hidden md:block">
    <div class="overflow-x-auto w-full" style="max-width:100%;">
      <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400 min-w-[900px]">
        <!-- Header -->
        <div class="bg-linear-to-r from_green-50 to-emerald-50 border-b-2 border-gray-400 px-6 py-3 min-w-[900px]">
          <div class="flex items-center gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
            <div class="w-16 px-1 flex-none">Stage <span class="text-red-500">*</span></div>
            <div class="flex-1 px-2">Specific Activities <span class="text-red-500">*</span></div>
            <div class="w-32 px-2 flex-none">Time Frame <span class="text-red-500">*</span></div>
            <div class="w-32 flex-none">Implementation Date <span class="text-red-500">*</span></div>
            <div class="flex-1 px-2">Point Person/s <span class="text-red-500">*</span></div>
            <div class="w-28 flex-none">Status</div>
            <div class="w-20 px-2 flex-none">Action</div>
          </div>
        </div>

        <div id="activitiesContainer" class="divide-y divide-gray-400 min-w-0">
          <!-- Activities will be loaded here dynamically -->
        </div>
      </div>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div id="activitiesContainerMobile" class="space-y-3">
        <!-- Mobile activities will be loaded here dynamically -->
      </div>
    </div>

    <!-- Add Activity Button (single button for both desktop and mobile) -->
    <div class="mt-4">
      <button type="button" id="addActivityRow" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Activity</button>
    </div>
  </div>
</div>

<!-- BUDGET -->
<div class="proposal-section section-gap">
  <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
    <span class="text-2xl md:text-3xl">💰</span> Budget
  </h2>

  <!-- Desktop Table View -->
  <div class="hidden md:block">
    <div class="overflow-x-auto w-full" style="max-width:100%;">
      <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400 min-w-0">
        <div class="bg-linear-to-r from-yellow-50 to-emerald-50 border-b-2 border-gray-400 px-6 py-4 min-w-0">
          <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
            <div>Activity</div>
            <div>Resources Needed</div>
            <div>Partner Agencies</div>
            <div>Amount</div>
            <div>Action</div>
          </div>
        </div>
        <div id="budgetContainer" class="divide-y divide-gray-400 min-w-0">
          <!-- Budget items will be loaded here dynamically -->
        </div>
      </div>
    </div>
  </div>

  <!-- Mobile Card View -->
  <div class="md:hidden space-y-3">
    <div id="budgetContainerMobile" class="space-y-3">
      <!-- Mobile budget items will be loaded here dynamically -->
    </div>
  </div>

  <button type="button" id="addBudgetRow" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Budget Item</button>
</div>

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
      <p class="text-sm text-gray-600">Selecting students from the project's original section and component:</p>
      <p class="text-sm font-medium">{{ $project->Project_Component }} - {{ $project->Project_Section }}</p>
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
  // Preload project members into a global so the staff edit script can use them
  window.preloadedProjectMembers = @json($project->members());
</script>
{{-- Server-side hidden inputs to ensure existing activity/budget IDs post even if JS fails --}}
@if(isset($project) && $project->activities && $project->activities->count() > 0)
  @foreach($project->activities as $act)
    <input type="hidden" name="activity_id[]" value="{{ $act->Activity_ID ?? $act->id }}">
  @endforeach
@endif

@if(isset($project) && $project->budgets && $project->budgets->count() > 0)
  @foreach($project->budgets as $bud)
    <input type="hidden" name="budget_id[]" value="{{ $bud->Budget_ID ?? $bud->id }}">
  @endforeach
@endif
@include('projects.partials.staff-edit-form-scripts', ['project' => $project])