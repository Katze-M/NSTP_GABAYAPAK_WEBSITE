@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-4">
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
                            elseif ($status === 'submitted') { $badgeClasses = 'bg-indigo-100 text-indigo-800'; $badgeStyle = 'background-color:#E0E7FF;color:#3730A3;'; }
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
                        @foreach($project->teamMembers() as $member)
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
                                        <h3 class="text-lg font-semibold">{{ $member->user->user_Name }}</h3>
                                        <p class="text-gray-600">{{ $member->user->user_Email }}</p>
                                        <p class="text-gray-600">{{ $member->student_contact_number }}</p>
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
                                
                                <!-- Edit Activity Button (for project owner) - Only when project is submitted -->
                                @if(Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === $project->student_id && $project->Project_Status !== 'draft')
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

                                <!-- Proof Picture (if exists) - Only when project is submitted -->
                                @if($project->Project_Status !== 'draft' && $budget->proof_picture)
                                    <div class="mt-3">
                                        <p class="text-sm text-gray-500">Proof of Activity</p>
                                        <img src="{{ asset('storage/' . $budget->proof_picture) }}" alt="Proof" class="max-w-xs h-auto rounded-lg mt-2">
                                    </div>
                                @endif
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
                                <input type="hidden" name="Project_Status" value="submitted">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                                    Submit for Review
                                </button>
                            </form>
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
    // Add SweetAlert2 confirmation to the submit for review button
    const submitForm = document.getElementById('submitForm');
    if (submitForm) {
        submitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Submit for Review?',
                text: "Are you sure you want to submit this project for review? This action cannot be undone and you won't be able to edit the project after submission.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm.submit();
                }
            });
        });
    }
});
</script>
@endsection
