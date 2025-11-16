@extends('layouts.app')


@section('title', 'Edit Project Proposal')


@section('content')
<!-- Project Proposal -->
<section id="upload-project" class="space-y-6 md:space-y-8">
  <!-- Main Heading -->
  <div class="flex items-center justify-between">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">Edit Project Proposal</h1>
    <!-- Back Button -->
    <x-back-button />
  </div>
 
  <form id="projectForm" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
    @method('PUT')
   
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
          <input type="file" name="Project_Logo" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" required>
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
            @foreach (range('A', 'Z') as $letter):
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
            <tbody class="divide-y divide-gray-400">
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Enter full name" required value="{{ Auth::user()->user_Name }}" readonly>
                </td>
                <td class="px-6 py-4">
                  <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" required>
                </td>
                <td class="px-6 py-4">
                  <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" required value="{{ Auth::user()->user_Email }}" readonly>
                </td>
                <td class="px-6 py-4">
                  <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required value="{{ Auth::user()->student->student_contact_number ?? '' }}" readonly>
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
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
            <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
            <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" required value="{{ Auth::user()->user_Email }}" readonly>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="{{ Auth::user()->student->student_contact_number ?? '' }}" readonly>
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
    <div class="rounded-2xl bg-gray-100 p-4 md:p-6 shadow-subtle">
      <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
        <span class="text-2xl md:text-3xl">📅</span> Project Activities
      </h2>


      <!-- Desktop Table View -->
      <div class="hidden md:block">
        <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400">
          <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b-2 border-gray-400 px-6 py-4">
            <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
              <div>Stage <span class="text-red-500">*</span></div>
              <div>Specific Activities <span class="text-red-500">*</span></div>
              <div>Time Frame <span class="text-red-500">*</span></div>
              <div>Point Person/s <span class="text-red-500">*</span></div>
              <div>Status</div>
              <div>Action</div>
            </div>
          </div>
          <div id="activitiesContainer" class="divide-y divide-gray-400">
            <div class="activity-row hover:bg-gray-50 transition-colors px-6 py-4">
              <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 items-start">
                <input name="stage[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Planning" required>
                <textarea name="activities[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
                <input name="timeframe[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Week 1-2" required>
                <textarea name="point_person[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
                <select name="status[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm">
                  <option>Planned</option>
                  <option>Ongoing</option>
                </select>
                <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Mobile Card View -->
      <div class="md:hidden space-y-3">
        <div id="activitiesContainerMobile" class="space-y-3">
          <div class="activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm">
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
          </div>
        </div>
      </div>


      <button type="button" id="addActivityRow" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Activity</button>
    </div>


    <!-- BUDGET -->
    <div class="rounded-2xl bg-gray-100 p-4 md:p-6 shadow-subtle">
      <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
        <span class="text-2xl md:text-3xl">💰</span> Budget
      </h2>


      <!-- Desktop Table View -->
      <div class="hidden md:block">
        <div class="bg-white rounded-xl shadow-subtle overflow-hidden border-2 border-gray-400">
          <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-b-2 border-gray-400 px-6 py-4">
            <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
              <div>Activity</div>
              <div>Resources Needed</div>
              <div>Partner Agencies</div>
              <div>Amount</div>
              <div>Action</div>
            </div>
          </div>
          <div id="budgetContainer" class="divide-y divide-gray-400">
            <div class="budget-row hover:bg-gray-50 transition-colors px-6 py-4">
              <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
                <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity..."></textarea>
                <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed..."></textarea>
                <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations..."></textarea>
                <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="₱ 0.00">
                <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
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


      <button type="button" id="addBudgetRow" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Budget Item</button>
    </div>


    <!-- Hidden input to track if it's a draft or submission -->
    <input type="hidden" name="save_draft" id="saveDraftInput" value="0">
    <input type="hidden" name="submit_project" id="submitProjectInput" value="0">


    <!-- SUBMIT and SAVE BUTTONS -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
      <button type="button" id="saveDraftBtn" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors">Save as Draft</button>
      <button type="button" id="submitProjectBtn" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Submit Project</button>
    </div>
  </form>
</section>


<!-- Member Selection Modal -->
<div id="memberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[80vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg font-bold">Select Team Members</h3>
      <button id="closeMemberModal" class="text-gray-500 hover:text-gray-700">
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
      <button id="cancelMemberSelection" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
        Cancel
      </button>
      <button id="addSelectedMembers" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
        Add Selected Members
      </button>
    </div>
  </div>
</div>





<script>
  // helper: remove row when button is clicked with SweetAlert2 confirmation
  function attachRemoveButtons() {
    document.querySelectorAll('.removeRow').forEach(btn => {
      btn.onclick = function() {
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
            btn.closest('tr, .grid, .activity-row, .budget-row, .member-card').remove();
            Swal.fire(
              'Removed!',
              'The item has been removed.',
              'success'
            )
          }
        })
      };
    });
  }


  // Add Row for Activities
  document.getElementById('addActivityRow').addEventListener('click', () => {
    // Desktop table view
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'activity-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <input name="stage[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Planning" required>
          <textarea name="activities[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
          <input name="timeframe[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Week 1-2" required>
          <textarea name="point_person[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
          <select name="status[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }


    // Mobile card view
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
   
    attachRemoveButtons();
  });


  // Add Row for Budget
  document.getElementById('addBudgetRow').addEventListener('click', () => {
    // Desktop table view
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity..."></textarea>
          <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed..."></textarea>
          <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations..."></textarea>
          <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="₱ 0.00">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }


    // Mobile card view
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
   
    attachRemoveButtons();
  });


  // Auto-expand textarea
  document.addEventListener("input", function (e) {
    if (e.target.classList.contains("auto-expand")) {
      e.target.style.height = "auto";
      e.target.style.height = e.target.scrollHeight + "px";
    }
  });


  // Handle Save as Draft
  document.getElementById('saveDraftBtn').addEventListener('click', function() {
    // For save as draft, we don't require all fields to be filled
    // Just show a simple confirmation and save
    Swal.fire({
      title: 'Save as Draft?',
      text: "Your project will be saved as a draft and can be edited later.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, save as draft!'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('saveDraftInput').value = '1';
        document.getElementById('submitProjectInput').value = '0';
        document.getElementById('projectForm').submit();
      }
    })
  });


  // Handle Submit Project with confirmation
  document.getElementById('submitProjectBtn').addEventListener('click', function() {
    // Validate minimum requirements before submitting
    if (!validateFormRequirements()) return;
    
    // Show confirmation modal with project summary
    showConfirmationModal();
  });


  // Show confirmation modal with detailed project information
  function showConfirmationModal() {
    // Get form data
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);
    
    // Team Information
    const projectName = formData.get('Project_Name') || 'N/A';
    const teamName = formData.get('Project_Team_Name') || 'N/A';
    const component = formData.get('Project_Component') || 'N/A';
    const section = formData.get('nstp_section') || 'N/A';
    
    // Get team logo file
    const teamLogoFile = formData.get('Project_Logo');
    let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
    if (teamLogoFile && teamLogoFile.size > 0) {
      teamLogoHTML = `<div class="text-sm text-gray-600">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>`;
    }
    
    // Project Details
    const problems = formData.get('Project_Problems') || 'N/A';
    const goals = formData.get('Project_Goals') || 'N/A';
    const targetCommunity = formData.get('Project_Target_Community') || 'N/A';
    const solution = formData.get('Project_Solution') || 'N/A';
    const outcomes = formData.get('Project_Expected_Outcomes') || 'N/A';
    
    // Members - Filter out empty entries and avoid desktop/mobile duplicates
    const allMemberNames = formData.getAll('member_name[]');
    const allMemberRoles = formData.getAll('member_role[]');
    const allMemberEmails = formData.getAll('member_email[]');
    const allMemberContacts = formData.getAll('member_contact[]');
    
    // Only collect unique members - desktop and mobile views have the same data
    // We'll take only the first half to avoid duplicates
    const uniqueMemberCount = Math.ceil(allMemberNames.length / 2);
    
    // Only get non-empty members from the first half (unique members)
    const memberNames = [];
    const memberRoles = [];
    const memberEmails = [];
    const memberContacts = [];
    
    for (let idx = 0; idx < uniqueMemberCount; idx++) {
      const name = allMemberNames[idx];
      if (name && name.trim() !== '') {
        memberNames.push(name);
        memberRoles.push(allMemberRoles[idx] || '');
        memberEmails.push(allMemberEmails[idx] || '');
        memberContacts.push(allMemberContacts[idx] || '');
      }
    }
    
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
    
    // Activities - Filter out empty entries from hidden mobile/desktop duplicates
    const allStages = formData.getAll('stage[]');
    const allActivities = formData.getAll('activities[]');
    const allTimeframes = formData.getAll('timeframe[]');
    const allPointPersons = formData.getAll('point_person[]');
    const allStatuses = formData.getAll('status[]');
    
    // Only get non-empty activities
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
    
    // Budget - Filter out empty entries from hidden mobile/desktop duplicates
    const allBudgetActivities = formData.getAll('budget_activity[]');
    const allBudgetResources = formData.getAll('budget_resources[]');
    const allBudgetPartners = formData.getAll('budget_partners[]');
    const allBudgetAmounts = formData.getAll('budget_amount[]');
    
    // Only get non-empty budget items
    const budgetActivities = [];
    const budgetResources = [];
    const budgetPartners = [];
    const budgetAmounts = [];
    
    allBudgetActivities.forEach((activity, idx) => {
      // Include if at least one field has data
      if ((activity && activity.trim() !== '') || 
          (allBudgetResources[idx] && allBudgetResources[idx].trim() !== '') || 
          (allBudgetPartners[idx] && allBudgetPartners[idx].trim() !== '') || 
          (allBudgetAmounts[idx] && allBudgetAmounts[idx].trim() !== '')) {
        budgetActivities.push(activity || '');
        budgetResources.push(allBudgetResources[idx] || '');
        budgetPartners.push(allBudgetPartners[idx] || '');
        budgetAmounts.push(allBudgetAmounts[idx] || '');
      }
    });
    
    let budgetHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
    let totalBudget = 0;
    
    budgetActivities.forEach((activity, idx) => {
      if (activity || budgetResources[idx] || budgetPartners[idx] || budgetAmounts[idx]) {
        // Extract numeric value from amount (remove peso sign and commas)
        let amountValue = budgetAmounts[idx] || '0';
        amountValue = amountValue.replace(/[₱,]/g, '').trim();
        const numericAmount = parseFloat(amountValue) || 0;
        totalBudget += numericAmount;
        
        // Format display amount with peso sign
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
    
    // Add total budget at the bottom if there are budget items
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
            // Set the submit flag and submit the form
            document.getElementById('saveDraftInput').value = '0';
            document.getElementById('submitProjectInput').value = '1';
            form.submit();
          }
        });
      }
    });
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
 
  document.getElementById('closeMemberModal').addEventListener('click', function() {
    document.getElementById('memberModal').classList.add('hidden');
  });
 
  document.getElementById('cancelMemberSelection').addEventListener('click', function() {
    document.getElementById('memberModal').classList.add('hidden');
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
          html += `
            <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
              <div class="flex items-center">
                <input type="checkbox" id="member${student.id}" name="available_members[]" value="${student.id}" class="mr-2" data-name="${student.name}" data-email="${student.email}">
                <label for="member${student.id}" class="text-sm">
                  <span class="font-medium">${student.name}</span> -
                  <span class="text-gray-600">${student.email}</span>
                </label>
              </div>
              <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Same Section</span>
            </div>
          `;
        });
       
        memberList.innerHTML = html;
      })
      .catch(error => {
        console.error('Error fetching students:', error);
        memberList.innerHTML = '<p class="text-center text-red-500">Error loading students. Please try again.</p>';
      });
  }


  // Add selected members to the form
  document.getElementById('addSelectedMembers').addEventListener('click', function() {
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
   
    selectedMembers.forEach(checkbox => {
      const memberId = checkbox.value;
      const memberName = checkbox.dataset.name;
      const memberEmail = checkbox.dataset.email;
     
      // Add to desktop table
      const desktopTable = document.querySelector('#memberTable tbody');
      if (desktopTable) {
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50 transition-colors';
        newRow.innerHTML = `
          <td class="px-6 py-4">
            <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberName}" readonly>
          </td>
          <td class="px-6 py-4">
            <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required>
          </td>
          <td class="px-6 py-4">
            <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberEmail}" readonly>
          </td>
          <td class="px-6 py-4">
            <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required>
          </td>
          <td class="px-6 py-4 text-center">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
              Remove
            </button>
          </td>
        `;
        desktopTable.appendChild(newRow);
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
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
          </div>
        `;
        mobileContainer.appendChild(newCard);
      }
    });
   
    // Reattach remove button handlers
    attachRemoveButtons();
   
    // Close modal
    document.getElementById('memberModal').classList.add('hidden');
  });


  // initial remove buttons
  attachRemoveButtons();


  // Populate existing data when the page loads
  document.addEventListener('DOMContentLoaded', function() {
    // Add existing activities
    @if(isset($project->activities) && $project->activities->count() > 0)
      @foreach($project->activities as $activity)
        addActivityRow('{{ addslashes($activity->Stage) }}', '{{ addslashes($activity->Specific_Activity) }}', '{{ addslashes($activity->Time_Frame) }}', '{{ addslashes($activity->Point_Persons) }}', '{{ addslashes($activity->status) }}');
      @endforeach
    @else
      // Add one blank activity row
      addBlankActivityRow();
    @endif
    
    // Add existing budget items
    @if(isset($project->activities))
      @foreach($project->activities as $activity)
        @if($activity->budget)
          addBudgetRow('{{ addslashes($activity->budget->Activity) }}', '{{ addslashes($activity->budget->Resources_Needed) }}', '{{ addslashes($activity->budget->Partner_Agencies) }}', '{{ addslashes($activity->budget->Amount) }}');
        @endif
      @endforeach
    @else
      // Add one blank budget row
      addBlankBudgetRow();
    @endif
    
    // Reattach remove button handlers
    attachRemoveButtons();
  });

  // Validate minimum requirements for form submission
  function validateFormRequirements() {
    // Check minimum one member (count both desktop table rows and mobile cards)
    const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
    const memberCardRows = document.querySelectorAll('.member-card').length;
    const totalMemberRows = memberTableRows + memberCardRows;
    
    if (totalMemberRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one team member is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    // Check minimum one activity
    const activityRows = document.querySelectorAll('.activity-row').length;
    if (activityRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one activity is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    return true;
  }
</script>
@endsection

