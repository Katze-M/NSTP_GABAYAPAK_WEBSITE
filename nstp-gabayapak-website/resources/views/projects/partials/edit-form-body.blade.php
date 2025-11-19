@php
    $isDraftMode = isset($isDraft) && $isDraft;
@endphp

<!-- TEAM INFORMATION -->
    <div class="rounded-2xl bg-gray-100 p-6 shadow-subtle space-y-4">
      <h2 class="text-2xl font-bold flex items-center gap-2">
        <span class="text-3xl">🖼️</span> Team Information
      </h2>


      <div class="space-y-3">
        <div>
          <label class="block text-lg font-medium">Project Name<span class="text-red-500">*</span></label>
          <input name="Project_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Project" @if(!$isDraftMode) required @endif value="{{ old('Project_Name', $project->Project_Name) }}">
        </div>
        <div>
          <label class="block text-lg font-medium">Team Name<span class="text-red-500">*</span></label>
          <input name="Project_Team_Name" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors" placeholder="Name of Team" @if(!$isDraftMode) required @endif value="{{ old('Project_Team_Name', $project->Project_Team_Name) }}">
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
          <input type="file" name="Project_Logo" class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
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
			<select name="nstp_section" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-400 bg-white text-black relative z-10 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors">
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
						<th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Name <span class="text-red-500">*</span></th>
						<th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Role/s <span class="text-red-500">*</span></th>
						<th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">School Email <span class="text-red-500">*</span></th>
						<th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">Contact Number <span class="text-red-500">*</span></th>
						<th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider text-center">Action</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-400">
					@foreach($project->members() as $i => $member)
						<tr class="hover:bg-gray-50 transition-colors">
							<td class="px-6 py-4">
								<input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Enter full name" @if(!$isDraftMode) required @endif value="{{ old('member_name.' . $i, $member['name']) }}">
								@if(isset($member['student_id']))
									<input type="hidden" name="member_student_id[]" value="{{ $member['student_id'] }}">
								@endif
							</td>
							<td class="px-6 py-4">
								<input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" @if(!$isDraftMode) required @endif value="{{ old('member_role.' . $i, $member['role']) }}">
							</td>
							<td class="px-6 py-4">
								<input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" @if(!$isDraftMode) required @endif value="{{ old('member_email.' . $i, $member['email']) }}">
							</td>
							<td class="px-6 py-4">
								<input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" @if(!$isDraftMode) required @endif value="{{ old('member_contact.' . $i, $member['contact']) }}">
							</td>
							<td class="px-6 py-4 text-center">
								<button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm" {{ $i === 0 ? 'disabled' : '' }}>Remove</button>
							</td>
						</tr>
					@endforeach
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


	<div id="memberContainer" class="md:hidden mt-4 space-y-3">
		@foreach($project->members() as $i => $member)
			<div class="member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3">
				<div class="space-y-1">
					<label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
					<input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" @if(!$isDraftMode) required @endif value="{{ old('member_name.' . $i, $member['name']) }}">
				</div>
				<div class="space-y-1">
					<label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
					<input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" @if(!$isDraftMode) required @endif value="{{ old('member_role.' . $i, $member['role']) }}">
				</div>
				<div class="space-y-1">
					<label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
					<input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" @if(!$isDraftMode) required @endif value="{{ old('member_email.' . $i, $member['email']) }}">
				</div>
				<div class="space-y-1">
					<label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
					<input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" @if(!$isDraftMode) required @endif value="{{ old('member_contact.' . $i, $member['contact']) }}">
				</div>
				<div class="flex justify-end">
					<button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs" {{ $i === 0 ? 'disabled' : '' }}>Remove</button>
				</div>
			</div>
		@endforeach
		<div class="mt-4">
			<button type="button" id="openMemberModalMobile" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors shadow-sm">+ Add Member from Same Section/Component</button>
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
			<textarea name="Project_Problems" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" @if(!$isDraftMode) required @endif>{{ old('Project_Problems', $project->Project_Problems) }}</textarea>
		</div>
		<div>
			<label class="block text-lg font-medium">Goal/Objectives<span class="text-red-500">*</span></label>
			<textarea name="Project_Goals" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" @if(!$isDraftMode) required @endif>{{ old('Project_Goals', $project->Project_Goals) }}</textarea>
		</div>
		<div>
			<label class="block text-lg font-medium">Target Community<span class="text-red-500">*</span></label>
			<textarea name="Project_Target_Community" rows="2" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" @if(!$isDraftMode) required @endif>{{ old('Project_Target_Community', $project->Project_Target_Community) }}</textarea>
		</div>
		<div>
			<label class="block text-lg font-medium">Solutions/Activities to be implemented<span class="text-red-500">*</span></label>
			<textarea name="Project_Solution" rows="4" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" @if(!$isDraftMode) required @endif>{{ old('Project_Solution', $project->Project_Solution) }}</textarea>
		</div>
		<div>
			<label class="block text-lg font-medium">Expected Outcomes<span class="text-red-500">*</span></label>
			<textarea name="Project_Expected_Outcomes" rows="5" class="mt-1 w-full rounded-lg border-2 border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-colors auto-expand" @if(!$isDraftMode) required @endif>{{ old('Project_Expected_Outcomes', $project->Project_Expected_Outcomes) }}</textarea>
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
        @foreach($project->activities as $i => $activity)
        <div class="proposal-table-row activity-row flex items-center gap-4 w-full">
          <div class="w-20 flex-none">
            <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" @if(!$isDraftMode) required @endif value="{{ old('stage.' . $i, $activity->Stage)}}">
          </div>
          <div class="flex-1 px-2">
            <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." @if(!$isDraftMode) required @endif>{{ old('activities.' . $i, $activity->Specific_Activity) }}</textarea>
          </div>
          <div class="w-36 px-2 flex-none">
            <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" @if(!$isDraftMode) required @endif value="{{ old('timeframe.' . $i, $activity->Time_Frame) }}">
          </div>
          <div class="w-44 px-2 flex-none">
            <input type="date" name="implementation_date[]" class="proposal-input w-full" @if(!$isDraftMode) required @endif value="{{ old('implementation_date.' . $i, $activity->Implementation_Date) }}">
          </div>
          <div class="flex-1 px-2">
            <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" @if(!$isDraftMode) required @endif>{{ old('point_person.' . $i, $activity->Point_Persons) }}</textarea>
          </div>
          <div class="w-30 py-3 flex-none">
            <select name="status[]" class="proposal-select w-full">
              <option {{ old('status.' . $i, $activity->status) == 'Planned' ? 'selected' : '' }}>Planned</option>
              <option {{ old('status.' . $i, $activity->status) == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
            </select>
          </div>
          <div class="w-20 py-3 flex-none">
            <button type="button" class="proposal-remove-btn removeRow">Remove</button>
          </div>
        </div>
        @endforeach
        @if($project->activities->isEmpty())
        <div class="proposal-table-row activity-row flex items-center gap-4 w-full">
          <div class="w-20 flex-none">
            <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" @if(!$isDraftMode) required @endif>
          </div>
          <div class="flex-1 px-2">
            <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." @if(!$isDraftMode) required @endif></textarea>
          </div>
          <div class="w-36 px-2 flex-none">
            <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" @if(!$isDraftMode) required @endif>
          </div>
          <div class="w-44 px-2 flex-none">
            <input type="date" name="implementation_date[]" class="proposal-input w-full" @if(!$isDraftMode) required @endif>
          </div>
          <div class="flex-1 px-2">
            <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" @if(!$isDraftMode) required @endif></textarea>
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
        @endif
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
          placeholder="Stage" @if(!$isDraftMode) required @endif>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          rows="2" placeholder="Specific Activities" @if(!$isDraftMode) required @endif></textarea>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          placeholder="Time Frame" @if(!$isDraftMode) required @endif>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 
          px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          @if(!$isDraftMode) required @endif>
      </div>

      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm 
          focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" 
          rows="2" placeholder="Point Person/s" @if(!$isDraftMode) required @endif></textarea>
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
                @foreach($project->budgetsArray() as $i => $budget)
              <div class="proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start w-full">
                <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity...">{{ old('budget_activity.' . $i, $budget['activity']) }}</textarea>
                <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed...">{{ old('budget_resources.' . $i, $budget['resources']) }}</textarea>
                <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations...">{{ old('budget_partners.' . $i, $budget['partners']) }}</textarea>
                <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00" value="{{ old('budget_amount.' . $i, $budget['amount']) }}">
                <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
              </div>
                @endforeach
                @if(empty($project->budgetsArray()))
              <div class="proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start w-full">
                <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity..."></textarea>
                <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed..."></textarea>
                <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations..."></textarea>
                <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00">
                <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
              </div>
                @endif
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
	@if($isDraft)
		<button type="button" id="cancelEditBtn" class="rounded-lg bg-red-500 hover:bg-red-600 text-white px-6 py-2 text-sm md:text-base transition-colors flex items-center justify-center font-medium">Cancel Edit</button>
		<button type="submit" name="action" value="save_draft" id="saveDraftBtn" class="rounded-lg bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 text-sm md:text-base transition-colors font-medium">Save as Draft</button>
		<button type="submit" name="action" value="submit_project" id="submitProjectBtn" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 text-sm md:text-base transition-colors font-medium">Submit Project</button>
	@else
		<button type="submit" name="action" value="save_project" id="saveProjectBtn" data-current-status="{{ $project->Project_Status }}" class="rounded-lg bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Save Project</button>
		<a href="{{ route('projects.show', $project) }}" class="rounded-lg bg-gray-300 hover:bg-gray-400 px-4 py-2 text-sm md:text-base text-gray-800 transition-colors flex items-center justify-center">Cancel Edit</a>
	@endif
</div>


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

<!-- Refine 'Save as Draft' functionality to enforce confirmation and prevent blank rows -->


