@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <button onclick="history.back()" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-medium rounded-lg shadow transition">Back</button>
                    </div>
                    @if(Auth::user() && Auth::user()->staff)
                        <div>
                            <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow">Edit Project</a>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col items-center text-center mb-6">
                    @if($project->Project_Logo)
                        <img src="{{ asset('storage/' . $project->Project_Logo) }}" alt="{{ $project->Project_Name }} Logo" class="w-32 h-32 object-contain rounded-lg border border-gray-200 p-2 mx-auto">
                    @else
                        <div class="w-32 h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200 mx-auto">
                            <span class="text-gray-500 text-base">No Logo</span>
                        </div>
                    @endif

                    <h1 class="text-3xl font-extrabold text-gray-800 mt-4">{{ $project->Project_Name }}</h1>
                    <p class="text-gray-600 mt-1">Team: <span class="font-semibold text-gray-800">{{ $project->Project_Team_Name }}</span></p>

                    @if($project->endorsed_by && $project->endorsedBy)
                        <p class="text-sm text-blue-600 mt-2">
                            <span class="font-semibold">Endorsed by:</span> {{ $project->endorsedBy->user_Name ?? 'N/A' }}
                            <span class="text-gray-500">({{ $project->updated_at->format('M d, Y') }})</span>
                        </p>
                    @endif
                    @if($project->Project_Approved_By && $project->approvedBy)
                        <p class="text-sm text-green-600 mt-2">
                            <span class="font-semibold">Approved by:</span> {{ $project->approvedBy->user_Name ?? 'N/A' }}
                            <span class="text-gray-500">({{ $project->updated_at->format('M d, Y') }})</span>
                        </p>
                    @endif
                    @if($project->mark_as_completed_by && $project->completedBy)
                        <p class="text-sm text-purple-600 mt-2">
                            <span class="font-semibold">Marked as Completed by:</span> {{ $project->completedBy->user_Name ?? 'N/A' }}
                            <span class="text-gray-500">({{ $project->updated_at->format('M d, Y') }})</span>
                        </p>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
                        @php
                            $componentRaw = strtoupper(trim((string)($project->Project_Component ?? '')));
                            switch($componentRaw) {
                                case 'LTS':
                                    $compClasses = 'bg-yellow-50 text-yellow-800'; break;
                                case 'CWTS':
                                    $compClasses = 'bg-red-50 text-red-800'; break;
                                case 'ROTC':
                                    $compClasses = 'bg-blue-50 text-blue-800'; break;
                                default:
                                    $compClasses = 'bg-indigo-50 text-indigo-700'; break;
                            }
                        @endphp
                        <span class="{{ $compClasses }} text-sm font-semibold px-3 py-1 rounded-full">{{ $project->Project_Component ?? 'No Component' }}</span>
                        <span class="bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">{{ $project->Project_Section ?? 'N/A' }}</span>
                        @php
                            $acts = $project->activities ?? collect();
                            $allActivitiesCompleted = false;
                            try {
                                if ($acts instanceof \Illuminate\Support\Collection) {
                                    $allActivitiesCompleted = $acts->isNotEmpty() && $acts->filter(function($a){ return strtolower(trim((string)($a->status ?? ''))) !== 'completed'; })->count() === 0;
                                }
                            } catch (\Exception $e) { $allActivitiesCompleted = false; }

                            $st = strtolower(trim((string)($project->Project_Status ?? '')));
                        @endphp
                        @if($allActivitiesCompleted)
                            @include('components.status-badge', ['status' => 'completed', 'size' => 'large'])
                        @elseif(in_array($st, ['pending','submitted','under review']))
                            @include('components.status-badge', ['status' => $project->Project_Status, 'size' => 'large', 'extraClass' => 'bg-orange-500 text-white'])
                        @else
                            @include('components.status-badge', ['status' => $project->Project_Status, 'size' => 'large'])
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                        <h3 class="text-lg font-bold mb-2 text-gray-800">Project Problems</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Problems ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                        <h3 class="text-lg font-bold mb-2 text-gray-800">Project Goals</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Goals ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                        <h3 class="text-lg font-bold mb-2 text-gray-800">Target Community</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Target_Community ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-gray-50 p-5 rounded-lg shadow-sm">
                        <h3 class="text-lg font-bold mb-2 text-gray-800">Solutions</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Solution ?? 'Not specified' }}</p>
                    </div>
                    <div class="bg-blue-50 p-5 rounded-lg shadow-sm md:col-span-2">
                        <h3 class="text-lg font-bold mb-2 text-blue-800">Expected Outcomes</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $project->Project_Expected_Outcomes ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Team Members</h2>
                    <div class="space-y-4">
                        @foreach($project->members() as $index => $member)
                            <div class="bg-white p-4 rounded-lg border-l-4 border-indigo-200 shadow-md relative">
                                @php
                                    // Use a consistent blue badge for member roles but do NOT default
                                    // empty/blank values to 'Member'. If role is empty, do not render.
                                    $rawRole = $member['role'] ?? ($member->role ?? null);
                                    $roleLabel = trim((string)($rawRole ?? ''));
                                    $roleClass = 'bg-blue-600 text-white';
                                    // Check if this is the project owner - first member or contains "owner" or "leader" and is first
                                    $isOwner = $index === 0 || stripos($roleLabel, 'owner') !== false || (stripos($roleLabel, 'leader') !== false && $index === 0);
                                @endphp
                                
                                {{-- Project Owner badge in top right --}}
                                @if($isOwner)
                                    <div class="absolute top-2 right-2">
                                        <span class="text-xs px-2 py-1 bg-blue-600 text-white rounded-full font-semibold">Project Owner</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-lg font-semibold text-gray-800">{{ $member['name'] ?? ($member->name ?? 'N/A') }}</div>
                                        @if(!empty($roleLabel))
                                            <div class="mt-1">
                                                <span class="inline-flex items-center {{ $roleClass }} text-sm font-semibold px-3 py-0.5 rounded-full shadow-sm" aria-label="role">{{ $roleLabel }}</span>
                                            </div>
                                        @endif

                                        @php
                                            // Try several possible locations for a contact number
                                            // Accept common keys including student-specific field names
                                            $phone = $member['contact'] ?? $member['contact_number'] ?? $member['contact_no'] ?? $member['phone'] ?? $member['mobile'] ?? $member['student_contact_number'] ?? null;
                                            if (empty($phone)) {
                                                // If member is an Eloquent model, try related user or student records
                                                try {
                                                    if (!empty($member->contact)) {
                                                        $phone = $member->contact;
                                                    } elseif (!empty($member->contact_number)) {
                                                        $phone = $member->contact_number;
                                                    } elseif (!empty($member->student_contact_number)) {
                                                        $phone = $member->student_contact_number;
                                                    } elseif (!empty($member->contact_no)) {
                                                        $phone = $member->contact_no;
                                                    } elseif (!empty($member->phone)) {
                                                        $phone = $member->phone;
                                                    } elseif (!empty($member->mobile)) {
                                                        $phone = $member->mobile;
                                                    } elseif (!empty($member->user) && !empty($member->user->contact_number)) {
                                                        $phone = $member->user->contact_number;
                                                    } elseif (!empty($member->user) && !empty($member->user->phone)) {
                                                        $phone = $member->user->phone;
                                                    } elseif (!empty($member->student) && !empty($member->student->student_contact_number)) {
                                                        $phone = $member->student->student_contact_number;
                                                    }
                                                } catch (\Exception $e) {
                                                    // ignore access errors and keep $phone null
                                                }
                                            }
                                        @endphp
                                        @if(!empty($phone))
                                            <div class="text-sm text-gray-600 mt-2"><span class="text-gray-800">{{ $phone }}</span></div>
                                        @endif

                                        <div class="text-xs text-gray-500 mt-1">{{ $member['email'] ?? ($member->email ?? '') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Project Activities</h2>
                    <div class="space-y-4">
                        @php $projStatus = strtolower((string)($project->Project_Status ?? '')) @endphp
                        @forelse($project->activities as $activity)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="font-semibold">{{ $activity->Specific_Activity ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-600">Stage: <span class="font-medium text-gray-800">{{ $activity->Stage ?? 'N/A' }}</span></div>
                                    </div>
                                    @php
                                        $aStatus = strtolower(trim((string)($activity->status ?? $activity->Status ?? 'planned')));
                                        if(in_array($aStatus, ['approved','completed','done'])) {
                                                $aClass = 'bg-green-100 text-green-800';
                                        } elseif(in_array($aStatus, ['in progress','ongoing','started'])) {
                                                $aClass = 'bg-blue-100 text-blue-800';
                                        } elseif(in_array($aStatus, ['rejected','cancelled'])) {
                                                $aClass = 'bg-red-100 text-red-800';
                                        } else {
                                                $aClass = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <div class="flex items-center">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $aClass }}">{{ ucfirst($aStatus) }}</span>
                                    </div>
                                </div>
                                <p class="text-gray-600 mt-2">Point Persons: <span class="font-medium text-gray-800">{{ $activity->Point_Persons ?? 'Not specified' }}</span></p>
                                @if($activity->Implementation_Date)
                                    <p class="text-gray-600 mt-2">Implementation Date: <span class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($activity->Implementation_Date)->format('F j, Y') }}</span></p>
                                @endif
                                @if(!empty($activity->Time_Frame))
                                    <p class="text-gray-600 mt-2">Time Frame:
                                        <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 text-gray-800 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M12 7v5l3 2"></path>
                                            </svg>
                                            <span class="font-medium text-gray-800">{{ $activity->Time_Frame }}</span>
                                        </span>
                                    </p>
                                @endif

                                {{-- Show Edit button for staff or the project owner when project is submitted/current --}}
                                @if(Auth::user() && (Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === $project->student_id) && in_array($projStatus, ['submitted','approved','current','completed']))
                                    <div class="mt-3 flex justify-end">
                                        @php
                                            // If the activity itself is completed (or similar states), do not navigate to edit — show modal-only button
                                            $activityCompleted = in_array($aStatus, ['approved','completed','done']);
                                        @endphp
                                        @if($activityCompleted || $projStatus === 'completed')
                                            {{-- Activity or project completed: show a button that prompts user that edits are no longer allowed --}}
                                            <button type="button" class="inline-flex items-center justify-center bg-red-500 text-white px-3 py-1 rounded-lg text-sm shadow opacity-75 cursor-not-allowed activity-edit-disabled" data-project-status="completed" aria-disabled="true">Update Status / Upload Proof</button>
                                        @else
                                            <a href="{{ route('activities.edit', $activity) }}" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm">Update Status / Upload Proof</a>
                                        @endif
                                    </div>
                                @endif

                                {{-- Activity update & proof history (visible for approved/current and completed projects) --}}
                                @if(in_array($projStatus, ['approved','current','completed']))
                                    <div class="mt-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Update Status and Proof</h4>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            <li><strong>Created:</strong> {{ $activity->created_at ? $activity->created_at->format('F j, Y g:i A') : 'N/A' }}</li>
                                            <li><strong>Last updated:</strong> {{ $activity->updated_at ? $activity->updated_at->format('F j, Y g:i A') : 'N/A' }}</li>
                                            <li><strong>Current status:</strong> {{ ucfirst($aStatus) }} @if($activity->updated_at)<span class="text-xs text-gray-500">({{ $activity->updated_at->diffForHumans() }})</span>@endif</li>
                                            @if($activity->proof_picture)
                                                <li class="mt-2">
                                                    <strong>Proof:</strong>
                                                    <div class="mt-1 flex items-center space-x-3">
                                                        <a href="{{ asset('storage/' . $activity->proof_picture) }}" target="_blank" class="block w-24 h-auto rounded border overflow-hidden">
                                                            <img src="{{ asset('storage/' . $activity->proof_picture) }}" alt="Proof" class="w-24 h-24 object-cover rounded">
                                                        </a>
                                                        <div class="text-xs text-gray-500">Uploaded: {{ $activity->updated_at ? $activity->updated_at->format('F j, Y g:i A') : 'Unknown' }}</div>
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
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

                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Budget Items</h2>
                    <div class="space-y-4">
                        @forelse($project->budgets as $budget)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                                <h3 class="text-lg font-semibold mb-2">{{ $budget->Specific_Activity ?? 'Unspecified Activity' }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-700">
                                    <div><strong class="text-gray-800">Resources:</strong> {{ $budget->Resources_Needed ?? 'N/A' }}</div>
                                    <div><strong class="text-gray-800">Partners:</strong> {{ $budget->Partner_Agencies ?? 'N/A' }}</div>
                                    <div class="text-right">
                                        <span class="inline-block bg-green-50 text-green-800 px-3 py-1 rounded-full font-semibold">₱{{ number_format($budget->Amount ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500">No budget items have been added to this project yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-blue-800">Total Budget</h3>
                        <p class="text-2xl font-bold text-green-600">₱{{ number_format($project->total_budget ?? 0, 2) }}</p>
                    </div>
                    @if($project->student_id && $project->student && $project->student->user)
                        <div class="mt-3 pt-3 border-t border-blue-200">
                            <p class="text-sm text-blue-700"><span class="font-medium">Submitted by:</span> {{ $project->student->user->user_Name }}</p>
                        </div>
                    @endif
                </div>

                {{-- Rejection history: visible to project owner, project members, and staff --}}
                @php
                    $isMember = false;
                    try {
                        $sidToCheck = null;
                        if (auth()->check() && auth()->user()->isStudent() && auth()->user()->student) {
                            $sidToCheck = (string) auth()->user()->student->id;
                        }
                        if ($sidToCheck) {
                            // First try stored student_ids JSON/array
                            $sids = $project->student_ids ?? [];
                            if (!is_array($sids)) {
                                $sids = json_decode($sids, true) ?: [];
                            }
                            foreach ($sids as $sidVal) {
                                if ((string) $sidVal === $sidToCheck) {
                                    $isMember = true;
                                    break;
                                }
                            }

                            // Fallback: check the project's members() helper which may return
                            // team members even when student_ids is not populated (legacy records)
                            if (!$isMember) {
                                try {
                                    $members = is_callable([$project, 'members']) ? $project->members() : [];
                                    foreach ($members as $m) {
                                        $mid = null;
                                        if (is_array($m)) {
                                            $mid = isset($m['student_id']) ? (string)$m['student_id'] : null;
                                        } else {
                                            // Eloquent model
                                            $mid = isset($m->id) ? (string)$m->id : null;
                                        }
                                        if ($mid && $mid === $sidToCheck) {
                                            $isMember = true;
                                            break;
                                        }
                                    }
                                } catch (\Throwable $e) {
                                    // ignore fallback errors
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        $isMember = false;
                    }
                @endphp

                @if(auth()->check() && (auth()->user()->isStaff() || (auth()->user()->isStudent() && auth()->user()->student && (auth()->user()->student->id === $project->student_id || $isMember))))
                    @php
                        // Show rejection history whenever there is any rejection metadata
                        // (most recent rejection, previous rejection history, or resubmission count)
                        $hasRejectionMetadata = false;
                        try {
                            $hasRejectionMetadata = (
                                !empty($project->Project_Rejection_Reason) ||
                                !empty($project->previous_rejection_reasons) ||
                                (($project->resubmission_count ?? 0) > 0) ||
                                (isset($isResubmission) && $isResubmission)
                            );
                        } catch (\Throwable $e) {
                            $hasRejectionMetadata = false;
                        }
                    @endphp
                    @if($hasRejectionMetadata)
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="font-semibold text-red-800 mb-2">Rejection History</h3>

                        @php
                            // determine if this project is a resubmission and compute count
                            $isResub = ($project->is_resubmission ?? false) || (($project->resubmission_count ?? 0) > 0) || !empty($project->previous_rejection_reasons);
                            $resubCount = $project->resubmission_count ?? 0;
                            if (!$resubCount && !empty($project->previous_rejection_reasons)) {
                                try {
                                    $decoded = json_decode($project->previous_rejection_reasons, true) ?: [];
                                    $resubCount = count($decoded);
                                } catch (\Exception $e) {
                                    $resubCount = $resubCount ?? 0;
                                }
                            }
                        @endphp

                        @if(!empty($isResub))
                            @if($resubCount > 0)
                                <p class="text-sm text-orange-600 mb-2">Resubmissions: <span class="bg-yellow-100 text-orange-700 px-2 py-0.5 rounded font-bold">{{ $resubCount }}</span></p>
                            @else
                                <p class="text-sm text-orange-600 mb-2">Resubmission</p>
                            @endif
                        @endif

                        @if($project->Project_Rejection_Reason)
                            <div class="bg-red-100 border border-red-300 rounded p-3 mb-3">
                                <p class="text-red-700"><strong>Most recent rejection:</strong> {{ $project->Project_Rejection_Reason }}</p>
                                @if($project->Project_Rejected_By)
                                    @php
                                        $rej = $project->rejectedBy;
                                        $rejName = optional($rej)->user_Name ?? null;
                                        $rejRole = optional($rej)->user_role ?? null;
                                        $rejDisplay = null;
                                        if ($rejName && $rejRole) {
                                            $rejDisplay = $rejName . ' (' . $rejRole . ')';
                                        } elseif ($rejName) {
                                            $rejDisplay = $rejName;
                                        } else {
                                            $rejDisplay = $project->Project_Rejected_By;
                                        }
                                    @endphp
                                    <p class="text-red-600 text-xs mt-1">Rejected by: {{ $rejDisplay }}</p>
                                @endif
                                <p class="text-red-600 text-xs mt-1">On: {{ optional($project->updated_at)->toDateTimeString() }}</p>
                            </div>
                        @endif

                        @if($project->previous_rejection_reasons)
                            @php
                                $previousReasons = json_decode($project->previous_rejection_reasons, true) ?: [];
                            @endphp
                            @if(!empty($previousReasons))
                                <div class="mt-2">
                                    <h4 class="font-semibold text-red-800 mb-2">Previous Rejection History:</h4>
                                    @foreach($previousReasons as $index => $reason)
                                        <div class="bg-red-100 border border-red-300 rounded p-3 mb-2">
                                            <p class="text-red-700 text-sm"><strong>Rejection #{{ $index + 1 }}:</strong> {{ $reason['reason'] ?? 'No reason provided' }}</p>
                                            <p class="text-red-600 text-xs mt-1">Rejected on: {{ $reason['rejected_at'] ?? 'Unknown' }}</p>
                                            @php
                                                // Reason entries may include a human-friendly 'rejected_by' display
                                                // (e.g., "Jane Doe (NSTP Coordinator)") or an id in older rows.
                                                $rejectedByLabel = null;
                                                if (!empty($reason['rejected_by'])) {
                                                    $rejectedByLabel = $reason['rejected_by'];
                                                } elseif (!empty($reason['rejected_by_id'])) {
                                                    try {
                                                        $u = \App\Models\User::find($reason['rejected_by_id']);
                                                        if ($u) {
                                                            $rejectedByLabel = $u->user_Name . ($u->user_role ? ' (' . $u->user_role . ')' : '');
                                                        } else {
                                                            $rejectedByLabel = $reason['rejected_by_id'];
                                                        }
                                                    } catch (\Throwable $e) {
                                                        $rejectedByLabel = $reason['rejected_by_id'] ?? null;
                                                    }
                                                }
                                            @endphp
                                            @if(!empty($rejectedByLabel))
                                                <p class="text-red-600 text-xs">Rejected by: {{ $rejectedByLabel }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
                @endif

                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    @if(Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === $project->student_id)
                        @if($project->Project_Status === 'draft')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">Edit Project</a>
                        @elseif($project->Project_Status === 'rejected')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">Edit Project Details</a>
                        @endif
                    @endif
                </div>

            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show a modal when users attempt to edit activities that are completed (or when the project is completed)
            document.querySelectorAll('.activity-edit-disabled').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Update Not Allowed',
                        text: 'Status and proof picture cannot be changed anymore for this activity.',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true
                    });
                });
            });
        });
    </script>
</div>
@endsection
