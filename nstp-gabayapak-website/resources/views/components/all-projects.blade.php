<div class="flex-1 p-6">
    <!-- Header and Back button -->
    @unless(isset($hideHeader) && $hideHeader)
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            @php
                $hideBackButton = isset($section) && in_array($section, ['My Projects', 'Pending Projects', 'Archived Projects']);
            @endphp
            @unless($hideBackButton)
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
            @endunless
            <h1 class="text-2xl font-bold">
                {{ $section ?? 'Projects' }} @if(isset($currentSection) && (isset($section) && $section !== 'ROTC')) - Section {{ $currentSection }} @endif
            </h1>
        </div>
    </div>
    @endunless

    <!-- Section Selection for LTS and CWTS (ROTC shows projects directly) -->
    @if(isset($section) && in_array($section, ['LTS', 'CWTS']))
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Sections:</h3>
        <div class="flex flex-wrap gap-2">
            @foreach (range('A', 'Z') as $letter)
                <a href="{{ route('projects.' . strtolower($section), $letter) }}" 
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                   @if(($currentSection ?? 'A') === $letter)
                       bg-blue-600 text-white
                   @else
                       bg-gray-200 text-gray-700 hover:bg-gray-300
                   @endif">
                    {{ $letter }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @if(isset($projects) && $projects->isNotEmpty())
            <!-- Debug: Projects count: {{ $projects->count() }} -->
            @foreach($projects as $project)
                <div class="bg-white p-4 pt-10 rounded-lg shadow-md text-center relative">
                    {{-- Status badge row above the title (right-aligned) to avoid overlap --}}
                    @php
                        // Determine if all project activities are completed. If so, show 'completed' badge.
                        $acts = $project->activities ?? collect();
                        $allActivitiesCompleted = false;
                        try {
                            if ($acts instanceof \Illuminate\Support\Collection) {
                                $allActivitiesCompleted = $acts->isNotEmpty() && $acts->filter(function($a){ return strtolower(trim((string)($a->status ?? ''))) !== 'completed'; })->count() === 0;
                            }
                        } catch (\Exception $e) {
                            $allActivitiesCompleted = false;
                        }
                    @endphp
                    <div class="w-full flex justify-end mb-2">
                        @if($project->Project_Status === 'draft')
                            @include('components.status-badge', ['status' => 'draft'])
                        @elseif($project->Project_Status === 'rejected')
                            @include('components.status-badge', ['status' => 'rejected'])
                        @elseif(in_array(strtolower(trim((string)($project->Project_Status ?? ''))), ['pending','submitted','under review']))
                            @include('components.status-badge', ['status' => 'pending', 'extraClass' => 'bg-orange-500 text-white'])
                        @elseif($project->Project_Status === 'completed')
                            @include('components.status-badge', ['status' => 'completed'])
                        @elseif($allActivitiesCompleted)
                            @include('components.status-badge', ['status' => 'completed'])
                        @elseif($project->Project_Status === 'approved' || $project->Project_Status === 'current')
                            @include('components.status-badge', ['status' => 'current'])
                        @elseif($project->Project_Status === 'archived')
                            @include('components.status-badge', ['status' => 'archived'])
                        @endif
                    </div>
                    @php
                        // compute resub status once so we can show the count beside the title
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
                    <div class="flex flex-col items-center mb-2 min-w-0">
                        <h2 class="text-lg font-semibold truncate text-center" title="{{ $project->Project_Name }}">{{ $project->Project_Name }}</h2>

                        {{-- Resubmission count removed from card list (kept in show view) --}}
                    </div>
                    <div class="w-16 h-16 mx-auto my-4">
                        @if($project->Project_Logo)
                            <img src="{{ asset('storage/' . $project->Project_Logo) }}" alt="{{ $project->Project_Name }} Logo" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full border-2 border-black rounded-full flex items-center justify-center">
                                <span class="text-xs text-gray-500">No Logo</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-gray-600">{{ $project->Project_Team_Name }}</p>
                    @if($project->Project_Status === 'rejected')
                        <p class="text-sm text-red-600 mt-1">Reason: {{ \Illuminate\Support\Str::limit($project->Project_Rejection_Reason ?? 'No reason provided', 80) }}</p>
                        @if(isset($project->Project_Rejected_By) && $project->rejectedBy)
                            <p class="text-xs text-gray-600">Rejected by: {{ $project->rejectedBy->user_Name ?? 'Staff' }}</p>
                        @endif
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-2 mt-4">
                                                <a href="@if(($section ?? '') === 'My Projects') {{ route('my-projects.details', $project->Project_ID) }} @else {{ route('projects.show', $project->Project_ID) }} @endif" 
                                                     class="view-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200"
                                                     style="background-color:#2563eb;color:#ffffff;">
                                                    View Project
                                                </a>
                        
                        @if(($section ?? '') === 'My Projects' && $project->Project_Status === 'draft')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors duration-200">
                                Edit Project
                            </a>
                            <!-- Delete Button for Draft Projects -->
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors duration-200">
                                    Delete
                                </button>
                            </form>
                        @endif
                        
                        @php
                            $isStaff = false;
                            if (Auth::check()) {
                                $user = Auth::user();
                                $isStaff = (isset($user->user_Type) && $user->user_Type === 'staff') || (method_exists($user, 'isStaff') && $user->isStaff());
                            }
                        @endphp
                        @if($isStaff && in_array($project->Project_Status, ['submitted','pending']))
                            <!-- Approve / Reject for Staff -->
                            <form action="{{ route('projects.approve', $project) }}" method="POST" class="approve-form">
                                @csrf
                                <button type="button" class="approve-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#16a34a;color:#ffffff;">
                                    Approve
                                </button>
                            </form>

                            <form action="{{ route('projects.reject', $project) }}" method="POST" class="reject-form">
                                @csrf
                                <input type="hidden" name="reason" class="reject-reason-input" value="">
                                <button type="button" class="reject-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#dc2626;color:#ffffff;">
                                    Reject
                                </button>
                            </form>
                        @endif
                        
                        @if($isStaff && in_array($project->Project_Status, ['current','approved','completed']))
                            <a href="{{ route('projects.edit', $project) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition-colors duration-200 view-btn" style="background-color:#4f46e5;color:#ffffff;">Edit</a>

                            <form action="{{ route('projects.archive', $project) }}" method="POST" class="inline-block archive-form">
                                @csrf
                                <button type="button" class="archive-btn bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#f59e0b;color:#ffffff;">Archive</button>
                            </form>

                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form-staff inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn-staff bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#dc2626;color:#ffffff;">Delete</button>
                            </form>
                        @endif
                        @if($project->Project_Status === 'completed')
                            <p class="text-xs text-gray-600 mt-2">Note: Project marked <strong>Completed</strong>. Activities and proofs are preserved.</p>
                        @endif
                        
                        @if($isStaff && ($section ?? '') === 'Archived Projects')
                            {{-- Unarchive, Edit, Delete for archived projects --}}
                            <form action="{{ route('projects.unarchive', $project) }}" method="POST" class="inline-block unarchive-form">
                                @csrf
                                <button type="button" class="unarchive-btn bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#f97316;color:#ffffff;">Unarchive</button>
                            </form>

                            <a href="{{ route('projects.edit', $project) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition-colors duration-200 view-btn" style="background-color:#4f46e5;color:#ffffff;">Edit</a>

                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form-staff inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn-staff bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors duration-200" style="background-color:#dc2626;color:#ffffff;">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty state message -->
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No projects found @if(!isset($section) || $section !== 'ROTC') in this section @endif.</p>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add SweetAlert2 confirmation to delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('.delete-form');
            
            Swal.fire({
                title: 'Delete Draft Project?',
                text: "Are you sure you want to delete this draft project? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
    
    // Approve and Reject handlers for staff
    document.addEventListener('DOMContentLoaded', function() {
        // Approve
        document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.approve-form');

                Swal.fire({
                    title: 'Approve Project?',
                    text: "Are you sure you want to approve this project?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Reject with reason
        document.querySelectorAll('.reject-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.reject-form');
                const reasonInput = form.querySelector('.reject-reason-input');

                Swal.fire({
                    title: 'Reject Project',
                    input: 'textarea',
                    inputLabel: 'Reason for rejection',
                    inputPlaceholder: 'Type the reason for rejection here...',
                    inputAttributes: {
                        'aria-label': 'Type the reason for rejection here'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (!value || !value.trim()) {
                            Swal.showValidationMessage('A rejection reason is required');
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        reasonInput.value = result.value;
                        form.submit();
                    }
                });
            });
        });
    });

    // Archive and staff-delete handlers
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.archive-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.archive-form');

                Swal.fire({
                    title: 'Archive Project?',
                    text: "Are you sure you want to archive this project?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, archive'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Unarchive handler for staff on archived projects
        document.querySelectorAll('.unarchive-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.unarchive-form');

                Swal.fire({
                    title: 'Unarchive Project?',
                    text: "Are you sure you want to unarchive this project and move it back to pending?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, unarchive'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        document.querySelectorAll('.delete-btn-staff').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.delete-form-staff');

                Swal.fire({
                    title: 'Delete Project?',
                    text: "This will permanently delete the project.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection

<style>
    /* Fallback styles for approve/reject buttons when Tailwind is not compiled */
    .approve-btn, .reject-btn {
        cursor: pointer !important;
        transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease;
        outline: none;
    }

    .approve-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.16);
        opacity: 0.98;
    }

    .reject-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(239, 68, 68, 0.16);
        opacity: 0.98;
    }

    .approve-btn:active, .reject-btn:active {
        transform: translateY(0);
        box-shadow: none;
    }
    
    /* View button fallback styles */
    .view-btn {
        cursor: pointer !important;
        transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease;
        display: inline-block;
        text-decoration: none;
    }

    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.12);
        opacity: 0.98;
    }

    .view-btn:active {
        transform: translateY(0);
        box-shadow: none;
    }

    /* Unified action button fallback (uniform height, padding, hover) */
    .approve-btn, .reject-btn, .view-btn, .delete-btn, .archive-btn, .unarchive-btn, .delete-btn-staff, .delete-btn-staff {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding-left: 12px !important;
        padding-right: 12px !important;
        font-size: 0.95rem;
        border-radius: 0.5rem;
        cursor: pointer !important;
        transition: transform 0.12s ease, box-shadow 0.12s ease, opacity 0.12s ease;
    }

    .approve-btn:hover, .reject-btn:hover, .view-btn:hover, .delete-btn:hover, .archive-btn:hover, .unarchive-btn:hover, .delete-btn-staff:hover, .delete-btn-staff:hover {
        transform: translateY(-3px);
        opacity: 0.98;
    }

    .approve-btn:active, .reject-btn:active, .view-btn:active, .delete-btn:active, .archive-btn:active, .unarchive-btn:active, .delete-btn-staff:active, .delete-btn-staff:active {
        transform: translateY(0);
        box-shadow: none;
    }
</style>