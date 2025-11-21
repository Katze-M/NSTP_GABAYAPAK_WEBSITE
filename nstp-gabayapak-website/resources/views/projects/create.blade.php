@extends('layouts.app')


@section('title', 'Create Project Proposal')


@section('content')
<!-- Project Proposal -->
<section id="upload-project" class="space-y-6 md:space-y-8 page-container w-full lg:max-w-5xl mx-auto px-2 md:px-6">
  <!-- Main Heading -->
  <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">Project Proposal</h1>
 
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
        <div>
          <label class="block text-lg font-medium">Team Name<span class="text-red-500">*</span></label>
          <input name="Project_Team_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Team" required>
        </div>
        <div>
          <label class="block text-lg font-medium">Team Logo<span class="text-red-500">*</span></label>
          <input type="file" name="Project_Logo" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
          <p class="text-sm text-gray-600 mt-1">Note: Logo is required when submitting a project, but optional when saving as draft.</p>
        </div>
        <!-- Component Dropdown -->
        <div class="relative">
          <label class="block text-lg font-medium">Component<span class="text-red-500">*</span></label>
          <select name="Project_Component" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white relative z-10 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" required>
            <option value="">Select Component</option>
            <option value="LTS" {{ (Auth::user()->student->student_component ?? '') === 'LTS' ? 'selected' : '' }}>Literacy Training Service (LTS)</option>
            <option value="CWTS" {{ (Auth::user()->student->student_component ?? '') === 'CWTS' ? 'selected' : '' }}>Civic Welfare Training Service (CWTS)</option>
            <option value="ROTC" {{ (Auth::user()->student->student_component ?? '') === 'ROTC' ? 'selected' : '' }}>Reserve Officers' Training Corps (ROTC)</option>
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
              <option value="{{ $value }}" {{ (Auth::user()->student->student_section ?? '') === $value ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
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
            <input type="hidden" name="member_student_id[]" value="{{ Auth::user()->student->id }}">
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
    <input type="hidden" name="save_draft" id="saveDraftInput" value="0">
    <input type="hidden" name="submit_project" id="submitProjectInput" value="0">


    <!-- SUBMIT and SAVE BUTTONS -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
      <button type="button" id="saveDraftBtn" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors">Save as Draft</button>
      <button type="button" id="submitProjectBtn" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Submit Project</button>
    </div>
  </form>
  </div>
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
// Track added member emails to prevent duplicates
let addedMemberEmails = new Set();

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

/* --------------------
   prepareFormForSubmit: disable inputs that are not visible (so only visible ones get sent)
   -------------------- */
function prepareFormForSubmit(form) {
  // Enable everything first
  form.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
  // Then disable hidden elements except hidden inputs / csrf / method / member inputs
  form.querySelectorAll('input, textarea, select').forEach(el => {
    try {
      const t = (el.type || '').toLowerCase();
      if (t === 'hidden') return;
      if (el.name && (el.name === '_token' || el.name === '_method')) return;
      // Don't disable member inputs - we need both desktop and mobile views to submit
      if (el.name && el.name.startsWith('member_')) return;
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
          // If removing member, remove email from tracking set
          if (row.querySelector && row.querySelector('input[name="member_email[]"]')) {
            const emailInput = row.querySelector('input[name="member_email[]"]');
            if (emailInput && emailInput.value) {
              addedMemberEmails.delete(emailInput.value);
            }
          }
          row.remove();
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

        // Debug: Log budget data being submitted for draft
        console.log('Draft - Budget Activities:', form.querySelectorAll('input[name="budget_activity[]"]:not([disabled])').length);
        console.log('Draft - Budget Amounts:', form.querySelectorAll('input[name="budget_amount[]"]:not([disabled])').length);
        console.log('Draft - Member names:', form.querySelectorAll('input[name="member_name[]"]:not([disabled])').length);
        console.log('Draft - Member emails:', form.querySelectorAll('input[name="member_email[]"]:not([disabled])').length);
        console.log('Draft - Member student IDs:', form.querySelectorAll('input[name="member_student_id[]"]:not([disabled])').length);

        const saveDraftInput = document.getElementById('saveDraftInput');
        const submitProjectInput = document.getElementById('submitProjectInput');
        if (saveDraftInput) saveDraftInput.value = '1';
        if (submitProjectInput) submitProjectInput.value = '0';

        // final submit
        form.submit();
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

    // Members - take unique (avoid duplicates from mobile/desktop)
    const allMemberNames = formData.getAll('member_name[]');
    const allMemberRoles = formData.getAll('member_role[]');
    const allMemberEmails = formData.getAll('member_email[]');
    const allMemberContacts = formData.getAll('member_contact[]');
    const uniqueMemberCount = Math.ceil(allMemberNames.length / 2);

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

            // Debug: Log budget data being submitted
            console.log('Budget Activities:', formData.getAll('budget_activity[]'));
            console.log('Budget Amounts:', formData.getAll('budget_amount[]'));

            // submit
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
          // Skip if this member is already added
          if (addedMemberEmails.has(student.email)) {
            return;
          }
          
          html += `
            <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
              <div class="flex items-center">
                <input type="checkbox" id="member${student.id}" name="available_members[]" value="${student.id}" class="mr-2" data-name="${student.name}" data-email="${student.email}" data-contact="${student.contact_number || ''}">
                <label for="member${student.id}" class="text-sm">
                  <span class="font-medium">${student.name}</span> -
                  <span class="text-gray-600">${student.email}</span>
                  <span class="text-gray-500 text-xs block">${student.contact_number || 'No contact number'}</span>
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
  document.getElementById('addSelectedMembers').addEventListener('click', function() {
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
   
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
            <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required>
          </td>
          <td class="px-6 py-4 text-center">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
              Remove
            </button>
          </td>
        `;
        // Add new member at the end of the table
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
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
          </div>
        `;
        // Add new member card at the end of the container
        mobileContainer.appendChild(newCard);
      }
    });
   
   
    // Close modal
    document.getElementById('memberModal').classList.add('hidden');
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
    const allMemberNames = formData.getAll('member_name[]');
    const allMemberRoles = formData.getAll('member_role[]');
    const allMemberEmails = formData.getAll('member_email[]');
    const allMemberContacts = formData.getAll('member_contact[]');

    // Filter out duplicates and empty entries (from desktop/mobile views)
    const uniqueMembers = [];
    const processedEmails = new Set();
    
    for (let i = 0; i < allMemberNames.length; i++) {
      const name = allMemberNames[i]?.trim();
      const role = allMemberRoles[i]?.trim();
      const email = allMemberEmails[i]?.trim();
      const contact = allMemberContacts[i]?.trim();
      
      // Only process if there's actual data and email hasn't been processed
      if ((name || role || email || contact) && (!email || !processedEmails.has(email))) {
        if (email) processedEmails.add(email);
        uniqueMembers.push({ name, role, email, contact, index: uniqueMembers.length + 1 });
      }
    }

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

