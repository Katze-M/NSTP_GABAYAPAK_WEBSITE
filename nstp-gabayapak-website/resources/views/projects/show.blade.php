@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button and Edit Button for Staff -->
        <div class="mb-4 flex justify-between">
            <button onclick="history.back()" 
                    class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 
                           text-gray-700 font-medium rounded-lg shadow transition">
                <svg class="h-5 w-5 mr-2 text-gray-700" xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 122.88 122.88" xml:space="preserve" fill="currentColor">
                    <g>
                        <path d="M84.93,4.66C77.69,1.66,69.75,0,61.44,0C44.48,0,29.11,6.88,18,18C12.34,23.65,7.77,30.42,4.66,37.95 
                        C1.66,45.19,0,53.13,0,61.44c0,16.96,6.88,32.33,18,43.44c5.66,5.66,12.43,10.22,19.95,13.34c7.24,3,15.18,4.66,23.49,4.66 
                        c8.31,0,16.25-1.66,23.49-4.66c7.53-3.12,14.29-7.68,19.95-13.34c5.66-5.66,10.22-12.43,13.34-19.95c3-7.24,4.66-15.18,4.66-23.49 
                        c0-8.31-1.66-16.25-4.66-23.49c-3.12-7.53-7.68-14.29-13.34-19.95C99.22,12.34,92.46,7.77,84.93,4.66L84.93,4.66z 
                        M65.85,47.13c2.48-2.52,2.45-6.58-0.08-9.05s-6.58-2.45-9.05,0.08L38.05,57.13c-2.45,2.5-2.45,6.49,0,8.98l18.32,18.62 
                        c2.48,2.52,6.53,2.55,9.05,0.08c2.52-2.48,2.55-6.53,0.08-9.05l-7.73-7.85l22-0.13c3.54-0.03,6.38-2.92,6.35-6.46 
                        c-0.03-3.54-2.92-6.38-6.46-6.35l-21.63,0.13L65.85,47.13L65.85,47.13z M80.02,16.55c5.93,2.46,11.28,6.07,15.76,10.55 
                        c4.48,4.48,8.09,9.83,10.55,15.76c2.37,5.71,3.67,11.99,3.67,18.58c0,6.59-1.31,12.86-3.67,18.58 
                        c-2.46,5.93-6.07,11.28-10.55,15.76c-4.48,4.48-9.83,8.09-15.76,10.55C74.3,108.69,68.03,110,61.44,110s-12.86-1.31-18.58-3.67 
                        c-5.93-2.46-11.28-6.07-15.76-10.55c-4.48-4.48-8.09-9.82-10.55-15.76c-2.37-5.71-3.67-11.99-3.67-18.58 
                        c0-6.59,1.31-12.86,3.67-18.58c2.46-5.93,6.07-11.28,10.55-15.76c4.48-4.48,9.83-8.09,15.76-10.55c5.71-2.37,11.99-3.67,18.58-3.67 
                        C68.03,12.88,74.3,14.19,80.02,16.55L80.02,16.55z"/>
                    </g>
                </svg>
                Back
            </button>
            
            @if(Auth::user() && Auth::user()->staff)
                <a href="{{ route('projects.edit', $project) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 
                          text-white font-medium rounded-lg shadow transition">
                    <svg class="h-5 w-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Project
                </a>
            @endif
        </div>
        
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Project Header -->
                <div class="flex flex-col items-center text-center mb-8">
                    <!-- Project Logo -->
                    <div class="mb-6">
                        @if($project->Project_Logo)
                            <img src="{{ asset('storage/' . $project->Project_Logo) }}" alt="{{ $project->Project_Name }} Logo" class="w-32 h-32 object-contain rounded-lg border border-gray-200 p-2 mx-auto">
                        @else
                            <div class="w-32 h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 mx-auto">
                                <span class="text-gray-500 text-base">No Logo</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Project Name and Team Name -->
                    <div class="mb-4">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $project->Project_Name }}</h1>
                        <p class="text-gray-600 text-lg">Team: {{ $project->Project_Team_Name }}</p>
                    </div>
                    
                    <!-- Project Status and Component -->
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                            {{ $project->Project_Component ?? 'No Component' }}
                        </span>
                        @php $section = $project->Project_Section ?? null; @endphp
                        <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded-full">
                            @if($section)
                                {{ \Illuminate\Support\Str::startsWith($section, 'Section ') ? $section : 'Section ' . $section }}
                            @else
                                Section N/A
                            @endif
                        </span>
                        @php
                            $status = $project->Project_Status ?? 'draft';
                            $statusLabel = ucfirst($status);
                            $badgeClasses = 'bg-gray-100 text-gray-800';
                            $badgeStyle = 'background-color:#E5E7EB;color:#1F2937;';
                            if ($status === 'draft') { $badgeClasses = 'bg-yellow-500 text-white'; $badgeStyle = 'background-color:#F59E0B;color:#ffffff;'; }
                            elseif ($status === 'pending') { $badgeClasses = 'bg-indigo-100 text-indigo-800'; $badgeStyle = 'background-color:#E0E7FF;color:#3730A3;'; }
                            elseif ($status === 'pending') { $badgeClasses = 'bg-purple-100 text-purple-800'; $badgeStyle = 'background-color:#F3E8FF;color:#6D28D9;'; }
                            elseif ($status === 'current') { $badgeClasses = 'bg-green-600 text-white'; $badgeStyle = 'background-color:#16A34A;color:#ffffff;'; }
                            elseif ($status === 'rejected') { $badgeClasses = 'bg-red-600 text-white'; $badgeStyle = 'background-color:#DC2626;color:#ffffff;'; }
                            elseif ($status === 'archived') { $badgeClasses = 'bg-slate-400 text-white'; $badgeStyle = 'background-color:#94A3B8;color:#ffffff;'; }
                        @endphp

                        <span class="{{ $badgeClasses }} text-sm font-medium px-3 py-1 rounded-full" style="{{ $badgeStyle }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                
                <!-- Project Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Project Problems</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Problems ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Project Goals</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Goals ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Target Community</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Target_Community ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Solutions</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Solution ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h3 class="text-lg font-semibold mb-2">Expected Outcomes</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Expected_Outcomes ?? 'Not specified' }}</p>
                    </div>
                </div>

                <!-- Team Members -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Team Members</h2>
                    <div class="space-y-4">
                        @foreach($project->members() as $member)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold">{{ $member['name'] }}</h3>
                                        @if(!empty($member['role']))
                                            <p class="text-gray-600 font-medium">{{ $member['role'] }}</p>
                                        @endif
                                        <p class="text-gray-600">{{ $member['email'] }}</p>
                                        <p class="text-gray-600">{{ $member['contact'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Activities -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Project Activities</h2>
                    <div class="space-y-4">
                        @forelse($project->activities as $activity)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold">{{ $activity->Stage ?? 'Unspecified Stage' }}</h3>
                                        <p class="text-gray-700 mt-2 whitespace-pre-line">{{ $activity->Specific_Activity ?? 'No activity details provided' }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <!-- Activity Status Badge -->
                                        @php
                                            $statusColors = [
                                                'planned' => 'bg-gray-100 text-gray-800',
                                                'ongoing' => 'bg-yellow-100 text-yellow-800',
                                                'completed' => 'bg-green-100 text-green-800'
                                            ];
                                            $statusText = [
                                                'planned' => 'Planned',
                                                'ongoing' => 'Ongoing',
                                                'completed' => 'Completed'
                                            ];
                                        @endphp
                                        <span class="{{ $statusColors[strtolower($activity->status)] ?? $statusColors['planned'] }} text-sm font-medium px-2.5 py-0.5 rounded">
                                            {{ $statusText[strtolower($activity->status)] ?? $statusText['planned'] }}
                                        </span>
                                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                            {{ $activity->Time_Frame ?? 'No timeframe' }}
                                        </span>
                                    </div>
                                </div>
                                <p class="text-gray-600 mt-2">Point Persons: {{ $activity->Point_Persons ?? 'Not specified' }}</p>
                                
                                <!-- Implementation Date -->
                                @if($activity->Implementation_Date)
                                    <p class="text-gray-600 mt-2">Implementation Date: {{ \Carbon\Carbon::parse($activity->Implementation_Date)->format('F j, Y') }}</p>
                                @endif
                                
                                <!-- Proof Picture (if exists) - Only when project is current or archived -->
                                @if(($project->Project_Status === 'current' || $project->Project_Status === 'archived') && $activity->proof_picture)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500">Proof of Activity</p>
                                        <img src="{{ asset('storage/' . $activity->proof_picture) }}" alt="Proof" class="max-w-xs h-auto rounded-lg mt-2">
                                    </div>
                                @endif
                                
                                <!-- Edit Activity Button (for project owner) - Only when project is current -->
                                @if(Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === $project->student_id && $project->Project_Status === 'current')
                                    <div class="mt-3">
                                        <a href="{{ route('activities.edit', $activity) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Edit Status & Proof
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500">No activities have been added to this project yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Budget Items (Separated from Activities) -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Budget Items</h2>
                    <div class="space-y-4">
                        @forelse($project->budgets as $budget)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-semibold mb-2">{{ $budget->Specific_Activity ?? 'Unspecified Activity' }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Resources Needed</p>
                                        <p class="text-gray-700">{{ $budget->Resources_Needed ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Partner Agencies</p>
                                        <p class="text-gray-700">{{ $budget->Partner_Agencies ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Amount</p>
                                        <p class="text-gray-700">₱{{ number_format($budget->Amount ?? 0, 2) }}</p>
                                    </div>
                                </div>

                                <!-- Proof pictures are now displayed in the activities section -->
                            </div>
                        @empty
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500">No budget items have been added to this project yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Total Budget -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <h3 class="text-xl font-bold text-blue-800">Total Budget</h3>
                        <p class="text-2xl font-bold text-green-600">₱{{ number_format($project->total_budget ?? 0, 2) }}</p>
                    </div>
                </div>
                
                @if($project->Project_Status === 'rejected')
                    <div class="mt-6 bg-red-50 p-4 rounded-lg border border-red-200">
                        <h3 class="text-lg font-semibold text-red-700">Rejection Reason</h3>
                        <p class="text-gray-800 whitespace-pre-line">{{ $project->Project_Rejection_Reason ?? 'No reason provided' }}</p>
                        @if($project->rejectedBy)
                            <p class="mt-2 text-sm text-gray-600">Rejected by: {{ $project->rejectedBy->user_Name ?? 'Staff' }} @if(!empty($project->rejectedBy->user_role)) ({{ $project->rejectedBy->user_role }}) @endif</p>
                        @endif
                    </div>
                @elseif($project->Project_Status === 'pending' && $project->is_resubmission)
                    <div class="mt-6 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center mb-3">
                            <h3 class="text-lg font-semibold text-yellow-700">Resubmission #{{ $project->resubmission_count ?? 1 }}</h3>
                            <span class="ml-3 bg-yellow-200 text-yellow-800 text-sm font-medium px-2 py-1 rounded-full">Under Review</span>
                        </div>
                        
                        @if($project->previous_rejection_reasons)
                            @php
                                $previousReasons = json_decode($project->previous_rejection_reasons, true);
                            @endphp
                            @if(!empty($previousReasons))
                                <div class="mb-4">
                                    <h4 class="font-semibold text-yellow-800 mb-2">Previous Rejection History:</h4>
                                    @foreach($previousReasons as $index => $reason)
                                        <div class="bg-yellow-100 border border-yellow-300 rounded p-3 mb-2">
                                            <p class="text-yellow-700 text-sm"><strong>Rejection #{{ count($previousReasons) - $index }}:</strong> {{ $reason['reason'] }}</p>
                                            <p class="text-yellow-600 text-xs mt-1">Rejected on: {{ $reason['rejected_at'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
                
                <!-- Edit and Submit Buttons -->
                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @if(Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === $project->student_id)
                        @if($project->Project_Status === 'draft')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Edit Project
                            </a>
                            <form action="{{ route('projects.update', $project) }}" method="POST" class="inline-block" id="submitForm">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="Project_Status" value="pending">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    Submit for Review
                                </button>
                            </form>
                        @elseif($project->Project_Status === 'rejected')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                                Edit Project Details
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate project data before submission
    function validateProjectData() {
        const errors = [];
        
        // Check required project fields
        const projectName = @json($project->Project_Name ?? null);
        const teamName = @json($project->Project_Team_Name ?? null);
        const component = @json($project->Project_Component ?? null);
        const section = @json($project->Project_Section ?? null);
        const problems = @json($project->Project_Problems ?? null);
        const goals = @json($project->Project_Goals ?? null);
        const targetCommunity = @json($project->Project_Target_Community ?? null);
        const solution = @json($project->Project_Solution ?? null);
        const expectedOutcomes = @json($project->Project_Expected_Outcomes ?? null);
        const logo = @json($project->Project_Logo ?? null);
        
        if (!projectName || !projectName.trim()) {
            errors.push('The Project Name field is required.');
        }
        if (!teamName || !teamName.trim()) {
            errors.push('The Team Name field is required.');
        }
        if (!component || !component.trim()) {
            errors.push('The Component field is required.');
        }
        if (!section || !section.trim()) {
            errors.push('The NSTP Section field is required.');
        }
        if (!problems || !problems.trim()) {
            errors.push('The Project Problems field is required.');
        }
        if (!goals || !goals.trim()) {
            errors.push('The Project Goals field is required.');
        }
        if (!targetCommunity || !targetCommunity.trim()) {
            errors.push('The Target Community field is required.');
        }
        if (!solution || !solution.trim()) {
            errors.push('The Project Solution field is required.');
        }
        if (!expectedOutcomes || !expectedOutcomes.trim()) {
            errors.push('The Expected Outcomes field is required.');
        }
        if (!logo || !logo.trim()) {
            errors.push('A team logo is required for project submission.');
        }
        
        // Check team members
        const members = @json($project->members() ?? []);
        let validMembers = 0;
        
        members.forEach((member, index) => {
            const email = member.email ? member.email.trim() : '';
            const name = member.name ? member.name.trim() : '';
            const role = member.role ? member.role.trim() : '';
            const contact = member.contact ? member.contact.trim() : '';
            
            if (email || name || role || contact) {
                if (!email) errors.push(`Team member ${index + 1}: Email is required.`);
                if (!name) errors.push(`Team member ${index + 1}: Name is required.`);
                if (!role) errors.push(`Team member role is required.`);
                if (!contact) errors.push(`Team member ${index + 1}: Contact is required.`);
                if (email && name && role && contact) validMembers++;
            }
        });
        
        if (validMembers === 0) {
            errors.push('At least one complete team member info is required.');
        }
        
        // Check activities
        const activities = @json($project->activities ?? []);
        let validActivities = 0;
        
        if (activities.length === 0) {
            errors.push('At least one activity is required.');
        } else {
            activities.forEach((activity, index) => {
                const stage = activity.Stage ? activity.Stage.toString().trim() : '';
                const specificActivity = activity.Specific_Activity ? activity.Specific_Activity.toString().trim() : '';
                const timeframe = activity.Time_Frame ? activity.Time_Frame.toString().trim() : '';
                const implementationDate = activity.Implementation_Date ? activity.Implementation_Date.toString().trim() : '';
                const pointPersons = activity.Point_Persons ? activity.Point_Persons.toString().trim() : '';
                
                // Check if any activity field has content (indicating this row is being used)
                if (stage || specificActivity || timeframe || implementationDate || pointPersons) {
                    const missingFields = [];
                    if (!stage) missingFields.push('Stage');
                    if (!specificActivity) missingFields.push('Specific Activities');
                    if (!timeframe) missingFields.push('Time Frame');
                    if (!implementationDate) missingFields.push('Implementation Date');
                    if (!pointPersons) missingFields.push('Point Persons');
                    
                    if (missingFields.length > 0) {
                        errors.push(`Activity ${index + 1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
                    } else {
                        validActivities++;
                    }
                } else {
                    // Completely empty activity - this is an error since activities should have data
                    errors.push(`Activity ${index + 1}: All activity fields are required.`);
                }
            });
        }
        
        // Check budget rows (optional but if filled, must be complete)
        const budgets = @json($project->budgets ?? []);
        
        budgets.forEach((budget, index) => {
            const budgetActivity = budget.Specific_Activity ? budget.Specific_Activity.toString().trim() : '';
            const resources = budget.Resources_Needed ? budget.Resources_Needed.toString().trim() : '';
            const partners = budget.Partner_Agencies ? budget.Partner_Agencies.toString().trim() : '';
            const amount = budget.Amount ? budget.Amount.toString().trim() : '';
            
            // If any field in this row is filled, all must be filled
            if (budgetActivity || resources || partners || amount) {
                const missingFields = [];
                if (!budgetActivity) missingFields.push('Activity');
                if (!resources) missingFields.push('Resources needed');
                if (!partners) missingFields.push('Partner agencies');
                if (!amount) missingFields.push('Amount');
                
                if (missingFields.length > 0) {
                    errors.push(`Budget row ${index + 1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
                }
            }
        });
        
        return errors;
    }

    // Add SweetAlert2 confirmation to the submit for review button
    const submitForm = document.getElementById('submitForm');
    if (submitForm) {
        submitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // First check if user has any pending projects
            fetch('{{ route("projects.user.pending-count") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pending Project Exists',
                            html: `You already have <strong>${data.count}</strong> project${data.count > 1 ? 's' : ''} awaiting approval.<br><br>Please wait for approval before submitting another project.`,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Understood'
                        });
                        return;
                    }
                    
                    // Validate project data
                    const errors = validateProjectData();
                    
                    if (errors.length > 0) {
                        const errorList = errors.join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            html: `<div class="text-center">${errorList}</div>`,
                            confirmButtonColor: '#3085d6',
                            width: '600px'
                        });
                        return;
                    }
                    
                    // Show detailed review modal (similar to edit form)
                    showProjectReviewModal();
                })
                .catch(error => {
                    console.error('Error checking pending projects:', error);
                    // Continue with submission if check fails
                    
                    // Validate project data
                    const errors = validateProjectData();
                    
                    if (errors.length > 0) {
                        const errorList = errors.join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            html: `<div class="text-center">${errorList}</div>`,
                            confirmButtonColor: '#3085d6',
                            width: '600px'
                        });
                        return;
                    }
                    
                    showProjectReviewModal();
                });
        });
    }
    
    // Show detailed project review modal
    function showProjectReviewModal() {
        // Team Information
        const projectName = '{{ $project->Project_Name }}';
        const teamName = '{{ $project->Project_Team_Name }}';
        const component = '{{ $project->Project_Component }}';
        const section = @json($project->Project_Section ?? null);
        const formattedSection = section ? (section.startsWith('Section ') ? section : 'Section ' + section) : 'N/A';

        // Project Details
        const problems = `{{ Str::limit($project->Project_Problems, 500) }}`;
        const goals = `{{ Str::limit($project->Project_Goals, 500) }}`;
        const targetCommunity = `{{ Str::limit($project->Project_Target_Community, 300) }}`;
        const solution = `{{ Str::limit($project->Project_Solution, 500) }}`;
        const outcomes = `{{ Str::limit($project->Project_Expected_Outcomes, 500) }}`;

        // Get team logo
        const logoPath = '{{ $project->Project_Logo }}';
        let teamLogoHTML = '<div class="text-sm text-gray-600">No logo uploaded</div>';
        if (logoPath && logoPath.trim() !== '') {
            teamLogoHTML = `<div class="text-sm text-green-600">✓ Logo image is uploaded</div>`;
        }

        // Members - get from project data
        const members = @json($project->members());
        let membersHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
        members.forEach((member, idx) => {
            membersHTML += `
                <div class="mb-3 pb-3 ${idx < members.length - 1 ? 'border-b border-gray-300' : ''}">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">${idx + 1}</span>
                        <strong class="text-gray-800">${member.name || 'N/A'}</strong>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">${member.role || 'N/A'}</span>
                    </div>
                    <div class="ml-8 text-xs text-gray-600">
                        <div class="flex items-center gap-2 mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                            ${member.email || 'N/A'}
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
                            ${member.contact || 'N/A'}
                        </div>
                    </div>
                </div>`;
        });
        membersHTML += '</div>';

        // Activities - get from project data
        const activities = @json($project->activities ?? []);
        let activitiesHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
        activities.forEach((activity, idx) => {
            const statusColors = {
                'Planned': 'bg-yellow-100 text-yellow-800',
                'Ongoing': 'bg-blue-100 text-blue-800',
                'Completed': 'bg-green-100 text-green-800'
            };
            const status = activity.status || activity.Status || 'Planned';
            const statusColor = statusColors[status] || 'bg-gray-100 text-gray-800';
            activitiesHTML += `
                <div class="mb-3 pb-3 ${idx < activities.length - 1 ? 'border-b border-gray-300' : ''}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <div class="font-bold text-gray-800 mb-1">${activity.Stage || 'N/A'}</div>
                            <div class="text-sm text-gray-700 whitespace-pre-wrap">${activity.Specific_Activity || 'N/A'}</div>
                        </div>
                        <span class="text-xs ${statusColor} px-2 py-1 rounded font-medium ml-2">${status}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                        <div class="bg-white p-2 rounded">
                            <span class="text-gray-500">⏱️ Timeframe:</span>
                            <span class="font-medium text-gray-800 whitespace-pre-wrap">${activity.Time_Frame || 'N/A'}</span>
                        </div>
                        <div class="bg-white p-2 rounded">
                            <span class="text-gray-500">👤 Person:</span>
                            <span class="font-medium text-gray-800 whitespace-pre-wrap">${activity.Point_Persons || 'N/A'}</span>
                        </div>
                    </div>
                </div>`;
        });
        activitiesHTML += '</div>';

        // Budget - get from project data
        const budgets = @json($project->budgets ?? []);
        let budgetHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
        let totalBudget = 0;

        budgets.forEach((budget, idx) => {
            const numericAmount = parseFloat(budget.Amount) || 0;
            totalBudget += numericAmount;
            const displayAmount = numericAmount > 0 ? `₱ ${numericAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱ 0.00';

            budgetHTML += `
                <div class="mb-3 pb-3 ${idx < budgets.length - 1 ? 'border-b border-gray-300' : ''}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="font-bold text-gray-800">${budget.Specific_Activity || 'Activity ' + (idx + 1)}</div>
                        <div class="bg-green-100 text-green-800 px-3 py-1 rounded-lg font-bold text-sm">${displayAmount}</div>
                    </div>
                    <div class="space-y-1 text-xs">
                        <div class="flex items-start gap-2">
                            <span class="text-gray-500 font-medium min-w-[80px]">📦 Resources:</span>
                            <span class="text-gray-700 whitespace-pre-wrap">${budget.Resources_Needed || 'N/A'}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-gray-500 font-medium min-w-[80px]">🤝 Partners:</span>
                            <span class="text-gray-700 whitespace-pre-wrap">${budget.Partner_Agencies || 'N/A'}</span>
                        </div>
                    </div>
                </div>`;
        });

        if (budgets.length > 0 && totalBudget > 0) {
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

        // Show detailed review modal
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
                                    <div class="text-sm font-medium text-gray-800">${formattedSection}</div>
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
                            <span class="text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded-full">${members.length} members</span>
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
                            <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">${activities.length} activities</span>
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
                // Final confirmation
                Swal.fire({
                    title: 'Submit for Review?',
                    text: "Are you sure you want to submit this project for review? This action cannot be undone and you won't be able to edit the project after submission.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2b50ff',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        submitForm.submit();
                    }
                });
            }
        });
    }
});
</script>
@endsection
