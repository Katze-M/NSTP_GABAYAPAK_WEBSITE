<style>
@media (max-width: 400px) {
  /* Make back button smaller and tighter */
  .back-btn {
    font-size: 0.9rem !important;
    padding: 0.4rem 0.8rem !important;
    border-radius: 0.7rem !important;
  }
  .back-btn svg {
    width: 1rem !important;
    height: 1rem !important;
    margin-right: 0.3rem !important;
  }
  /* Heading adjustments */
  .section-heading {
    font-size: 1.2rem !important;
    margin-top: 0.5rem !important;
    margin-bottom: 0.7rem !important;
    line-height: 1.2 !important;
  }
  /* Section grid: tighter spacing, smaller buttons */
  .section-grid {
    grid-gap: 0.4rem !important;
    gap: 0.4rem !important;
    margin-top: 0.5rem !important;
  }
  .section-grid a {
    font-size: 0.85rem !important;
    padding: 0.5rem 0.7rem !important;
    min-width: 2.2rem !important;
    border-radius: 0.7rem !important;
  }
  /* Project list spacing */
  .all-projects-container {
    padding: 0.75rem !important;
  }
}
</style>
<div class="flex-1 p-6 all-projects-container">
    <!-- Header and Back button -->
    @unless(isset($hideHeader) && $hideHeader)
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            @php
                $hideBackButton = isset($section) && in_array($section, ['My Projects', 'Pending Projects', 'Archived Projects']);
            @endphp
            @unless($hideBackButton)
            <button onclick="history.back()" 
                    class="back-btn inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 
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
            <h1 class="text-2xl font-bold section-heading">
                {{ $section ?? 'Projects' }} @if(isset($currentSection) && (isset($section) && $section !== 'ROTC')) - Section {{ $currentSection }} @endif
            </h1>
        </div>
    </div>
    @endunless

    <!-- Section Selection for LTS and CWTS (ROTC shows projects directly) -->
    @if(isset($section) && in_array($section, ['LTS', 'CWTS']))
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Sections:</h3>
        <div class="flex flex-wrap gap-2 section-grid">
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
            @php $u = Auth::user(); @endphp

            {{-- Special view for NSTP Program Officer on Pending Projects: show two lists (to endorse / to approve) with toggles --}}
            @php
                // Detect SACSI Director explicitly so we can give a Program Officer-like
                // listing while restricting actions for the SACSI Director.
                $isSACSI = false;
                try {
                    if ($u && (method_exists($u, 'isSACSIDirector') && $u->isSACSIDirector())) {
                        $isSACSI = true;
                    } elseif ($u && isset($u->user_role) && $u->user_role === 'SACSI Director') {
                        $isSACSI = true;
                    }
                } catch (\Exception $e) { $isSACSI = false; }
            @endphp

            @if((($section ?? '') === 'Pending Projects') && $u && ((method_exists($u, 'isProgramOfficer') && $u->isProgramOfficer()) || (isset($u->user_role) && trim($u->user_role) === 'SACSI Director') || (method_exists($u, 'isSACSIDirector') && $u->isSACSIDirector())))
                <div class="col-span-full">
                @php
                    // Normalize statuses and try to pick up projects that need formator endorsement
                    $toEndorse = $projects->filter(function($p){
                        $s = strtolower(trim((string)($p->Project_Status ?? '')));
                        return in_array($s, ['submitted','pending']);
                    });

                    // Projects that have been endorsed and are waiting coordinator approval
                    $toApprove = $projects->filter(function($p){
                        $s = strtolower(trim((string)($p->Project_Status ?? '')));
                        return $s === 'endorsed';
                    });

                    // Fallback: if the collection passed to this component doesn't include
                    // the expected pending/endorsed projects (for example when the
                    // controller provided a filtered set), query the Project model
                    // to populate the lists. This keeps the Program Officer view useful
                    // even when `$projects` is empty or filtered by a different scope.
                    try {
                        if (($toEndorse->isEmpty() || $projects->isEmpty()) && class_exists(\App\Models\Project::class)) {
                            $fallback = \App\Models\Project::whereIn('Project_Status', ['pending', 'submitted'])->get();
                            // merge unique by primary key if necessary
                            $toEndorse = $toEndorse->merge($fallback)->unique(function($p){ return $p->Project_ID ?? $p->id ?? null; })->values();
                        }

                        if (($toApprove->isEmpty() || $projects->isEmpty()) && class_exists(\App\Models\Project::class)) {
                            $fallbackA = \App\Models\Project::where('Project_Status', 'endorsed')->get();
                            $toApprove = $toApprove->merge($fallbackA)->unique(function($p){ return $p->Project_ID ?? $p->id ?? null; })->values();
                        }
                    } catch (\Exception $e) {
                        // If querying fails (e.g., running in a context where DB isn't available), keep empty collections
                        $toEndorse = $toEndorse ?? collect();
                        $toApprove = $toApprove ?? collect();
                    }
                @endphp

                <!-- Endorse section with toggle -->
                <div class="po-section mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold mb-2">Projects to be endorsed by the NSTP Formators:</h3>
                        <button type="button" class="po-toggle-btn inline-flex items-center px-3 py-1 bg-white border rounded text-sm" data-target="po-endorse" aria-expanded="false">
                            <span class="po-toggle-label">Show</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="po-toggle-icon h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <div id="po-endorse" class="po-collapse overflow-hidden">
                        @if($toEndorse->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                                @foreach($toEndorse as $project)
                                    <div class="bg-white p-4 pt-10 rounded-lg shadow-md text-center relative">
                                        <div class="w-full flex justify-end mb-2">
                                            @include('components.status-badge', ['status' => strtolower((string)($project->Project_Status ?? 'pending'))])
                                        </div>
                                        <h2 class="text-lg font-semibold text-center break-words" title="{{ $project->Project_Name }}">{{ $project->Project_Name }}</h2>
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
                                        <div class="flex justify-center mt-4">
                                            <div class="relative group">
                                                <a href="{{ route('projects.show', $project->Project_ID) }}" class="view-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200 flex items-center justify-center" style="background-color:#2563eb;color:#ffffff;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">View Project</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 mb-6">No projects to be endorsed are found in this section.</p>
                        @endif
                    </div>
                </div>

                <!-- Approve section with toggle -->
                <div class="po-section mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold mb-2">Projects to be approved by NSTP Coordinators:</h3>
                        <button type="button" class="po-toggle-btn inline-flex items-center px-3 py-1 bg-white border rounded text-sm" data-target="po-approve" aria-expanded="false">
                            <span class="po-toggle-label">Show</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="po-toggle-icon h-4 w-4 ml-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                    <div id="po-approve" class="po-collapse overflow-hidden">
                        @if($toApprove->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                                @foreach($toApprove as $project)
                                    <div class="bg-white p-4 pt-10 rounded-lg shadow-md text-center relative">
                                        <div class="w-full flex justify-end mb-2">
                                            {{-- These projects are in the "to approve" list and are 'endorsed' in DB -- show endorsed badge --}}
                                            @include('components.status-badge', ['status' => 'endorsed'])
                                        </div>
                                        <h2 class="text-lg font-semibold text-center break-words" title="{{ $project->Project_Name }}">{{ $project->Project_Name }}</h2>
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
                                        <div class="flex justify-center mt-4">
                                            <div class="relative group">
                                                <a href="{{ route('projects.show', $project->Project_ID) }}" class="view-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200 flex items-center justify-center" style="background-color:#2563eb;color:#ffffff;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">View Project</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 mb-6">No projects found in this section.</p>
                        @endif
                    </div>
                </div>

                </div>
            @else
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
                        @elseif($project->Project_Status === 'endorsed')
                            {{-- Show purple endorsed badge on project cards/lists --}}
                            @include('components.status-badge', ['status' => 'endorsed'])
                        {{-- Removed automatic badge when activities are all completed; badge displays only when project status is completed. --}}
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
                        <h2 class="text-lg font-semibold text-center break-words" title="{{ $project->Project_Name }}">{{ $project->Project_Name }}</h2>

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
                        <!-- View Button with Eye Icon and Custom Tooltip -->
                            <div class="relative group">
                                <a href="@if((($section ?? '') === 'My Projects') && (Auth::check() && Auth::user()->isStudent() && Auth::user()->student && Auth::user()->student->id === ($project->student_id ?? null))) {{ route('my-projects.details', $project->Project_ID) }} @else {{ route('projects.show', $project->Project_ID) }} @endif" 
                                   class="view-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200 flex items-center justify-center"
                                   style="background-color:#2563eb;color:#ffffff;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">View Project</span>
                            </div>
                        
                        @if(($section ?? '') === 'My Projects' && $project->Project_Status === 'draft')
                            @php
                                $currentUserIsOwner = false;
                                if (Auth::check() && Auth::user()->isStudent() && Auth::user()->student) {
                                    $currentUserIsOwner = (Auth::user()->student->id === ($project->student_id ?? null));
                                }
                            @endphp
                            @if($currentUserIsOwner || (Auth::check() && Auth::user()->isStaff()))
                                <!-- Edit Button: yellow, pencil icon, same rounded sizing as view-btn -->
                                <div class="relative group">
                                    <a href="{{ route('projects.edit', $project) }}" class="view-btn bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center" style="background-color:#f59e0b;color:#ffffff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Edit</span>
                                </div>

                                <!-- Delete Button for Draft Projects: red rounded icon button; keep `.delete-btn` for confirmation JS -->
                                <div class="relative group">
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-btn view-btn bg-red-600 hover:bg-red-700 text-white flex items-center justify-center" style="background-color:#dc2626;color:#ffffff;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3" />
                                            </svg>
                                        </button>
                                    </form>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Delete</span>
                                </div>
                            @endif
                        @endif
                        
                        @php
                            $isStaff = false;
                            $user = null;
                            if (Auth::check()) {
                                $user = Auth::user();
                                $isStaff = (isset($user->user_Type) && $user->user_Type === 'staff') || (method_exists($user, 'isStaff') && $user->isStaff());
                            }
                        @endphp
                        @if($isStaff && (in_array($project->Project_Status, ['submitted','pending','endorsed']) && !(isset($user) && method_exists($user, 'isProgramOfficer') && $user->isProgramOfficer() && $project->Project_Status === 'approved')))
                            @php
                                $showApprove = false;
                                $showReject = false;
                                $roleLabel = '';
                                $roleTooltip = '';
                                // Fix: Ensure $user is set and role detection works for Coordinator
                                if ($user) {
                                    if (method_exists($user, 'isFormator') && $user->isFormator()) {
                                        $roleLabel = 'Endorse';
                                        $roleTooltip = 'Endorse Project';
                                        $showApprove = true;
                                        $showReject = true;
                                    } elseif (method_exists($user, 'isCoordinator') && $user->isCoordinator()) {
                                        $roleLabel = 'Approve';
                                        $roleTooltip = 'Approve Project';
                                        $showApprove = true;
                                        $showReject = true;
                                    } elseif (method_exists($user, 'isProgramOfficer') && $user->isProgramOfficer()) {
                                        $roleLabel = 'Mark as Completed';
                                        $roleTooltip = 'Mark as Completed';
                                        $showApprove = true;
                                        $showReject = false;
                                    }
                                }
                            @endphp
                            @if($showApprove)
                            <div class="relative group">
                                <form action="{{ route('projects.approve', $project) }}" method="POST" class="approve-form">
                                    @csrf
                                    <button type="button" class="approve-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors duration-200 flex items-center justify-center" style="background-color:#16a34a;color:#ffffff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </form>
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">{{ $roleTooltip }}</span>
                            </div>
                            @endif
                            @if($showReject)
                            <div class="relative group">
                                <form action="{{ route('projects.reject', $project) }}" method="POST" class="reject-form">
                                    @csrf
                                    <input type="hidden" name="reason" class="reject-reason-input" value="">
                                    <button type="button" class="reject-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors duration-200 flex items-center justify-center" style="background-color:#dc2626;color:#ffffff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </form>
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Reject Project</span>
                            </div>
                            @endif
                        @endif
                        
                        @if($isStaff && in_array($project->Project_Status, ['current','approved','completed']))
                                                        @php
                                                            $showMarkCompleted = false;
                                                            // Use the already-computed activity completion indicator
                                                            $activities = $project->activities ?? collect();
                                                            $allActivitiesCompleted = false;
                                                            try {
                                                                if ($activities instanceof \Illuminate\Support\Collection) {
                                                                    $allActivitiesCompleted = $activities->isNotEmpty() && $activities->filter(function($a){ return strtolower(trim((string)($a->status ?? ''))) !== 'completed'; })->count() === 0;
                                                                }
                                                            } catch (\Exception $e) { $allActivitiesCompleted = false; }

                                                            if (isset($user) && method_exists($user, 'isProgramOfficer') && $user->isProgramOfficer()
                                                                && in_array($project->Project_Status, ['approved','current'])
                                                                && $allActivitiesCompleted
                                                                // don't show when project was already marked completed or has been recorded as completed
                                                                && empty($project->mark_as_completed_by)
                                                                && strtolower(trim((string)$project->Project_Status)) !== 'completed') {
                                                                $showMarkCompleted = true;
                                                            }
                                                        @endphp
                                                        @if($showMarkCompleted)
                                                        <!-- Mark as Completed Button for Program Officer (only shown when all activities are completed) -->
                                                        <div class="relative group">
                                                            <form action="{{ route('projects.approve', $project) }}" method="POST" class="inline-block complete-form">
                                                                @csrf
                                                                <button type="button" class="approve-btn bg-green-600 hover:bg-green-700 text-white flex items-center justify-center" style="background-color:#16a34a;color:#ffffff;">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                            <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Mark as Completed</span>
                                                        </div>
                                                        @endif
                            @php
                                // If the current user is SACSI Director, only show Edit action here.
                                $showOnlyEditForSACSI = isset($isSACSI) && $isSACSI;
                            @endphp
                            @if($showOnlyEditForSACSI)
                                <!-- SACSI Director: only Edit -->
                                <div class="relative group">
                                    <a href="{{ route('projects.edit', $project) }}" class="view-btn bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center" style="background-color:#4f46e5;color:#ffffff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-2.97-2.97L5 17v3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                        </svg>
                                    </a>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Edit</span>
                                </div>
                            @else
                                <!-- Edit Button with Pencil Icon and Tooltip -->
                                <div class="relative group">
                                    <a href="{{ route('projects.edit', $project) }}" class="view-btn bg-indigo-600 hover:bg-indigo-700 text-white flex items-center justify-center" style="background-color:#4f46e5;color:#ffffff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-2.97-2.97L5 17v3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                        </svg>
                                    </a>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Edit</span>
                                </div>

                                <!-- Archive Button with Archive Icon and Tooltip -->
                                <div class="relative group">
                                    <form action="{{ route('projects.archive', $project) }}" method="POST" class="inline-block archive-form">
                                        @csrf
                                        <button type="button" class="archive-btn view-btn bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center" style="background-color:#f59e0b;color:#ffffff;">
                                            <!-- New Archive Icon: Box with Down Arrow -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                                <path d="M8 12l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                                <path d="M12 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Archive</span>
                                </div>
                                <!-- Delete Button with Trash Icon and Tooltip -->
                                <div class="relative group">
                                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form-staff inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="delete-btn-staff view-btn bg-red-600 hover:bg-red-700 text-white flex items-center justify-center" style="background-color:#dc2626;color:#ffffff;">
                                            <!-- New Delete Icon: Trash Can with Lid -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <rect x="5" y="7" width="14" height="12" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                <rect x="9" y="3" width="6" height="3" rx="1.5" stroke="currentColor" stroke-width="2" fill="none"/>
                                            </svg>
                                        </button>
                                    </form>
                                    <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Delete</span>
                                </div>
                            @endif
                        @endif
                        @if($project->Project_Status === 'completed')
                            <p class="text-xs text-gray-600 mt-2">Note: Project marked <strong>Completed</strong>. Activities and proofs are preserved.</p>
                        @endif
                        @if(Auth::check() && Auth::user()->isStudent())
                            @php
                                $currentSid = Auth::user()->student->id ?? null;
                                $isOwner = $currentSid && $currentSid === ($project->student_id ?? null);
                                $isMember = false;
                                try {
                                    $sids = $project->student_ids ?? [];
                                    if (!is_array($sids)) {
                                        $sids = json_decode($sids, true) ?: [];
                                    }
                                    foreach ($sids as $sid) {
                                        if ((string)$sid === (string)$currentSid) { $isMember = true; break; }
                                    }
                                } catch (\Exception $e) { $isMember = false; }
                            @endphp
                            @php
                                $statusNorm = strtolower(trim((string)($project->Project_Status ?? '')));
                            @endphp
                            @if($isMember && !$isOwner && !in_array($statusNorm, ['completed', 'archived']))
                                <p class="text-xs text-gray-600 mt-2">Note: As a <strong> team member </strong>, you cannot edit, submit, or resubmit the project. <strong> Only the project leader (owner) can perform those actions.</strong></p>
                            @endif
                        @endif
                        
                        @if($isStaff && ($section ?? '') === 'Archived Projects')
                            {{-- Unarchive (icon), Delete (icon) for archived projects. Edit removed. --}}
                            <form action="{{ route('projects.unarchive', $project) }}" method="POST" class="inline-block unarchive-form relative group">
                                @csrf
                                <button type="button" class="unarchive-btn view-btn bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center" style="background-color:#f97316;color:#ffffff;">
                                    <!-- Unarchive Icon: box with up arrow -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 8v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <polyline points="3 8 12 3 21 8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 15V7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <polyline points="9 12 12 9 15 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Unarchive</span>
                            </form>

                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form-staff inline-block relative group">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn-staff view-btn bg-red-600 hover:bg-red-700 text-white flex items-center justify-center" style="background-color:#dc2626;color:#ffffff;">
                                    <!-- Trash Icon: same as other delete buttons -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M3 6h18" stroke-width="2" stroke-linecap="round"/>
                                        <rect x="5" y="7" width="14" height="12" rx="2" stroke-width="2" fill="none"/>
                                        <path d="M10 11v6M14 11v6" stroke-width="2" stroke-linecap="round"/>
                                        <rect x="9" y="3" width="6" height="3" rx="1.5" stroke-width="2" fill="none"/>
                                    </svg>
                                </button>
                                <span class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 px-2 py-1 rounded bg-gray-800 text-white text-xs opacity-0 group-hover:opacity-100 transition pointer-events-none whitespace-nowrap z-10">Delete</span>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
            @endif
        @else
            <!-- Empty state message -->
            <div class="col-span-full text-center py-12">
                @php
                    $u = Auth::user();
                    $isFormator = false;
                    try {
                        if ($u && method_exists($u, 'isFormator') && $u->isFormator()) {
                            $isFormator = true;
                        }
                    } catch (\Exception $e) {
                        $isFormator = false;
                    }
                @endphp

                @if(($section ?? '') === 'Pending Projects' && $isFormator)
                    <p class="text-gray-500 text-lg">No projects to endorse are found in this section.</p>
                @else
                    <p class="text-gray-500 text-lg">No projects found in this section.</p>
                @endif
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
        // Approve / Endorse / Complete handlers - dynamic modal text based on tooltip
                document.querySelectorAll('.approve-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');

                // Try to find a nearby tooltip label to derive an action verb (e.g., 'Approve', 'Endorse', 'Mark as Completed')
                let actionLabel = 'approve this project';
                try {
                    const group = this.closest('.group');
                    if (group) {
                        const tip = group.querySelector('span');
                        if (tip && tip.textContent && tip.textContent.trim().length > 0) {
                            actionLabel = tip.textContent.trim();
                        }
                    }
                } catch (err) {
                    // ignore and fallback
                }

                const title = actionLabel.endsWith('?') ? actionLabel : (actionLabel + '?');
                const lower = actionLabel.toLowerCase();
                const text = 'Are you sure you want to ' + lower + '?';
                const confirmText = 'Yes, ' + lower.replace(/[^a-z0-9 ]/gi, '');

                // Insert an action-specific note between the title and the confirmation text
                let noteHtml = '';
                if (/endorse/i.test(actionLabel)) {
                    noteHtml = '<p style="text-align:center;margin-bottom:0.5rem"><strong>Note:</strong> Endorsing this project means the project details are complete and has been reviewed by the NSTP Formator</p>';
                } else if (/^approve/i.test(actionLabel) || /approve/i.test(actionLabel)) {
                    noteHtml = '<p style="text-align:center;margin-bottom:0.5rem"><strong>Note:</strong> Approving this project means the project was successfully defended by the group.</p>';
                } else if (/mark as completed/i.test(actionLabel) || /completed/i.test(actionLabel)) {
                    noteHtml = '<p style="text-align:center;margin-bottom:0.5rem"><strong>Note:</strong> Marking this project as completed means the project was fully implemented.</p>';
                }

                Swal.fire({
                    title: title,
                    html: (noteHtml ? noteHtml : '') + '<p style="text-align:center;margin-top:0.25rem">' + text + '</p>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: confirmText
                }).then((result) => {
                    if (result.isConfirmed && form) {
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

    // Program Officer toggles: show/hide collapsible sections
    document.querySelectorAll('.po-toggle-btn').forEach(btn => {
        const targetId = btn.getAttribute('data-target');
        const target = document.getElementById(targetId);
        if (!target) return;

        // initialize collapsed
        target.style.maxHeight = '0px';

        btn.addEventListener('click', function() {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            if (expanded) {
                // collapse
                // set explicit maxHeight so transition works from current height
                target.style.maxHeight = target.scrollHeight + 'px';
                requestAnimationFrame(() => { target.style.maxHeight = '0px'; });
                btn.setAttribute('aria-expanded', 'false');
                const label = btn.querySelector('.po-toggle-label'); if (label) label.textContent = 'Show';
                const icon = btn.querySelector('.po-toggle-icon'); if (icon) icon.classList.remove('rotate');
                target.classList.remove('open');
            } else {
                // expand
                target.style.maxHeight = target.scrollHeight + 'px';
                btn.setAttribute('aria-expanded', 'true');
                const label = btn.querySelector('.po-toggle-label'); if (label) label.textContent = 'Hide';
                const icon = btn.querySelector('.po-toggle-icon'); if (icon) icon.classList.add('rotate');
                target.classList.add('open');

                // after transition, allow natural height
                const onEnd = function() {
                    target.style.maxHeight = 'none';
                    target.removeEventListener('transitionend', onEnd);
                };
                target.addEventListener('transitionend', onEnd);
            }
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
        .approve-btn, .reject-btn, .view-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
            padding: 0 !important;
            font-size: 1rem;
            border-radius: 0.85rem;
            cursor: pointer !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            transition: transform 0.18s cubic-bezier(.4,2,.3,1), box-shadow 0.18s cubic-bezier(.4,2,.3,1), opacity 0.18s;
            background: linear-gradient(180deg, rgba(255,255,255,0.12) 0%, rgba(0,0,0,0.04) 100%);
        }

        .approve-btn:hover, .reject-btn:hover, .view-btn:hover {
            transform: scale(1.08) translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.18);
            opacity: 0.98;
            z-index: 2;
        }

        .approve-btn:active, .reject-btn:active, .view-btn:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }

    
    /* PO toggle styles */
    .po-collapse {
        max-height: 0;
        transition: max-height 280ms ease;
        overflow: hidden;
    }

    .po-collapse.open {
        /* large enough to cover probable content; JS will set 'none' after transition */
        max-height: 2000px;
    }

    .po-toggle-icon {
        transition: transform 180ms ease;
        transform-origin: center;
    }

    .po-toggle-icon.rotate {
        transform: rotate(180deg);
    }
</style>