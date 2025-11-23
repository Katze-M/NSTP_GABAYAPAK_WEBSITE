@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="p-6 text-gray-900">
          <div class="flex items-center justify-between mb-4">
          <div>
            <h1 class="text-3xl md:text-4xl font-bold">Dashboard</h1>
              <p class="text-xl md:text-2xl text-gray-800">Welcome,
                <span class="text-blue-800 font-semibold px-1 py-0.5 rounded">{{ Auth::user()->user_Name }}</span>!
              </p>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('projects.current') }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg font-sans font-medium tracking-wide text-base md:text-base hover:bg-blue-700">Current Projects</a>
            <a href="{{ route('projects.pending') }}" class="px-3 py-2 bg-yellow-400 text-black rounded-lg font-sans font-medium tracking-wide text-base md:text-base hover:bg-yellow-300">Pending Projects</a>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="mt-4 md:mt-6 grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
          <div class="rounded-xl  bg-slate-50 p-3 md:p-4 shadow-lg text-center">
            <p class="text-sm md:text-base text-black font-semibold">Submitted Projects</p>
            <p class="text-xl md:text-3xl font-bold text-blue-600">{{ $total_projects }}</p>
          </div>
          <div class="rounded-xl  bg-slate-50 p-3 md:p-4 shadow-lg text-center">
            <p class="text-sm md:text-base text-black font-semibold">Pending Projects</p>
            <p class="text-xl md:text-3xl font-bold text-yellow-600">{{ $project_status_counts['pending'] }}</p>
          </div>
          <div class="rounded-xl  bg-slate-50 p-3 md:p-4 shadow-lg text-center">
            <p class="text-sm md:text-base text-black font-semibold">Current Projects</p>
            <p class="text-xl md:text-3xl font-bold text-green-600">{{ $project_status_counts['approved'] }}</p>
          </div>
          <div class="rounded-xl  bg-slate-50 p-3 md:p-4 shadow-lg text-center">
            <p class="text-sm md:text-base text-black font-semibold">Rejected Projects</p>
            <p class="text-xl md:text-3xl font-bold text-red-600">{{ $project_status_counts['rejected'] }}</p>
          </div>
          <div class="col-span-2 md:col-span-1 rounded-xl bg-slate-50 p-3 md:p-4 shadow-lg text-center">
            <p class="text-sm md:text-base text-black font-semibold">Total Student Users</p>
            <p class="text-xl md:text-3xl font-bold text-red-800">{{ $total_students }}</p>
          </div>
        </div>

        <!-- Upcoming Activities + Filters -->
        <div class="mt-6 md:mt-10 rounded-xl border bg-white p-4 md:p-6 shadow-md">
          <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-xl md:text-2xl font-bold">Upcoming Activities</h3>
          </div>

          <!-- Filters -->
          <form id="dashboard-filters" method="GET" action="{{ route('dashboard') }}" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-2">
            <div class="md:col-span-2">
              <input type="search" name="q" value="{{ request('q') }}" placeholder="Search activities, projects, teams..." class="w-full rounded-lg border px-3 py-2" />
            </div>
            <div class="md:col-span-1">
              <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-lg border px-3 py-2" />
            </div>
            <div class="md:col-span-1 flex gap-2">
              <select id="filter-section" name="section" class="rounded-lg border px-3 py-2 w-1/2">
                <option value="">All Sections</option>
                @foreach($sections ?? collect() as $s)
                  {{-- Store values in the same format as DB (e.g. 'Section A') but show the short letter to users --}}
                  <option value="Section {{ $s }}" @if(request('section') == 'Section ' . $s) selected @endif>{{ $s }}</option>
                @endforeach
              </select>
              <select name="component" class="rounded-lg border px-3 py-2 w-1/2">
                <option value="">All Components</option>
                @foreach($components ?? collect() as $c)
                  <option value="{{ $c }}" @if(request('component') == $c) selected @endif>{{ $c }}</option>
                @endforeach
              </select>
            </div>
            <div class="md:col-span-1 flex items-center justify-end">
              <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg border text-sm">Clear</a>
            </div>
          </form>

          <script>
            (function(){
              var form = document.getElementById('dashboard-filters');
              var comp = document.querySelector('select[name="component"]');
              var sect = document.getElementById('filter-section');
              var date = document.querySelector('input[name="date"]');
              if (!form) return;

              // Helper to safely submit
              function trySubmit(){
                try { form.submit(); } catch(e) { /* ignore */ }
              }

              if (comp && sect) {
                // When component selection changes, if ROTC selected, set section to 'A'
                comp.addEventListener('change', function(){
                  try {
                    if (this.value && this.value.toUpperCase() === 'ROTC') {
                      sect.value = 'Section A';
                    }
                  } catch(e) {
                    // ignore
                  }
                  trySubmit();
                });

                // On load, if component is already ROTC, ensure section shows 'A'
                try {
                  if (comp.value && comp.value.toUpperCase() === 'ROTC') {
                    sect.value = 'Section A';
                  }
                } catch(e) {}
              }

              // Auto-submit when section changes
              if (sect) {
                sect.addEventListener('change', trySubmit);
              }

              // Auto-submit when date changes
              if (date) {
                date.addEventListener('change', trySubmit);
              }
            })();
          </script>

          @php $hasFilters = request('q') || request('date') || request('section') || request('component'); @endphp

          @if($hasFilters)
            <h4 class="text-lg font-semibold mb-2">Filtered Activities ({{ $filtered_activities->count() }})</h4>
              @if($filtered_activities->isNotEmpty())
              <ul class="divide-y divide-gray-200 mb-4">
                @foreach($filtered_activities as $activity)
                  <li class="relative border border-gray-200 rounded-lg p-3 md:p-4 pr-12 flex items-center sm:items-center justify-between gap-4 bg-white mb-4">
                    @php
                      $implBadge = null;
                      if(!empty($activity['date'])){
                        try {
                          $implDate = \Carbon\Carbon::parse($activity['date']);
                          $now = \Carbon\Carbon::now();
                          if ($implDate->isToday()) {
                            $implBadge = ['text' => 'Today', 'class' => 'bg-green-600 text-white'];
                          } elseif ($implDate->isFuture()) {
                            $days = (int) $now->diffInDays($implDate);
                            if ($days <= 7) { $implBadge = ['text' => 'In ' . $days . 'd', 'class' => 'bg-yellow-400 text-black']; }
                            else { $implBadge = ['text' => 'In ' . $days . 'd', 'class' => 'bg-blue-600 text-white']; }
                          } else {
                            $days = (int) $implDate->diffInDays($now);
                            $implBadge = ['text' => $days . 'd ago', 'class' => 'bg-orange-400 text-black'];
                          }
                        } catch (\Exception $e) {
                          $implBadge = null;
                        }
                      }
                    @endphp
                    @if($implBadge)
                      <div class="absolute top-3 right-3 z-10">
                        <span class="inline-block text-sm px-3 py-1 rounded {{ $implBadge['class'] }}">{{ $implBadge['text'] }}</span>
                      </div>
                    @endif
                      <div class="flex items-center gap-4 flex-1">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center border border-gray-200 shrink-0 overflow-hidden">
                          @if(!empty($activity['project_logo']))
                            <img src="{{ $activity['project_logo'] }}" alt="{{ $activity['project_name'] }} logo" class="w-full h-full object-cover" />
                          @else
                            <!-- Clipboard icon fallback -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 2h6a2 2 0 012 2v1h1a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h1V4a2 2 0 012-2z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6" />
                            </svg>
                          @endif
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-xl md:text-2xl font-semibold text-gray-800 leading-tight">{{ $activity['title'] }}</p>
                          <p class="text-sm font-bold italic text-gray-700 mt-1">{{ $activity['project_name'] }}</p>

                          <!-- Team badge below project name -->
                          @if(!empty($activity['team']) || !empty($activity['point_persons']))
                            <div class="mt-2">
                              <span class="inline-flex items-center text-sm text-gray-800 bg-gray-100 px-2 py-0.5 rounded font-medium">{{ $activity['team'] ?? $activity['point_persons'] }}</span>
                            </div>

                            @if(!empty($activity['date']))
                              @php
                                $status = strtolower($activity['status'] ?? '');
                                  $statusClass = 'bg-slate-100 text-gray-800';
                                  // Use only the activity table's `status` value.
                                  // Map exactly: planned -> yellow, ongoing -> blue, completed -> red
                                  if ($status === 'planned') { $statusClass = 'bg-yellow-100 text-yellow-800'; }
                                  elseif ($status === 'ongoing') { $statusClass = 'bg-blue-100 text-blue-800'; }
                                  elseif ($status === 'completed') { $statusClass = 'bg-red-100 text-red-800'; }
                              @endphp
                              <p class="mt-1 text-sm md:text-base">
                                <span class="inline-flex items-center bg-yellow-100 text-gray-800 px-2 py-0.5 rounded font-bold">{{ $activity['date'] }}</span>
                                @if(!empty($activity['status']))
                                  <span class="ml-2 inline-flex items-center text-xs px-2 py-0.5 rounded {{ $statusClass }}">{{ ucfirst($activity['status']) }}</span>
                                @endif
                              </p>
                            @endif
                          @endif

                          <!-- Badges: component, section, timeframe (with clock) -->
                          <div class="mt-3 flex flex-wrap items-center gap-2">
                            @php
                              // component is normalized in controller; use it for logic, display the original label if available
                              $compRaw = $activity['component'] ?? '';
                              // slightly darker component badges with white text for contrast
                              $compClass = 'bg-gray-200 text-gray-800';
                              if ($compRaw === 'ROTC') { $compClass = 'bg-blue-500 text-white'; }
                              elseif ($compRaw === 'LTS') { $compClass = 'bg-yellow-500 text-white'; }
                              elseif ($compRaw === 'CWTS') { $compClass = 'bg-red-500 text-white'; }
                              $sect = $activity['section'] ?? $activity['project_section'] ?? $activity['Project_Section'] ?? null;
                            @endphp

                            @if(!empty($activity['component_label']))
                              <span class="inline-flex items-center text-xs px-2 py-0.5 rounded {{ $compClass }}">{{ $activity['component_label'] }}</span>
                            @endif
                            @if($sect)
                              <span class="inline-flex items-center text-xs text-white bg-green-600 px-2 py-0.5 rounded">{{ $sect }}</span>
                            @endif
                            @if(!empty($activity['timeframe']))
                              <span class="inline-flex items-center text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                <!-- clock icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $activity['timeframe'] }}
                              </span>
                            @endif
                              @if(!empty($activity['point_persons']))
                                <span class="inline-flex items-center text-xs text-gray-600 bg-gray-50 px-2 py-0.5 rounded">Point Person(s): <span class="ml-1 font-medium text-gray-800">{{ $activity['point_persons'] }}</span></span>
                              @endif
                            
                            
                          </div>
                          <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if(!empty($activity['implementation']) || !empty($activity['implementation_date']))
                              <span class="inline-flex items-center text-sm text-gray-700 bg-slate-100 px-3 py-1 rounded">Implementation: <span class="ml-1 font-medium">{{ $activity['implementation'] ?? $activity['implementation_date'] }}</span></span>
                            @endif
                            
                          </div>
                        </div>
                      </div>

                      <div class="shrink-0">
                        <a href="{{ $activity['project_id'] ? route('projects.show', $activity['project_id']) . (request('component') ? '?component=' . urlencode(request('component')) : ( $activity['component'] ? '?component=' . urlencode($activity['component']) : '')) : '#' }}" class="px-3 py-1.5 text-xs md:text-sm text-blue-600 border border-blue-600 rounded-full hover:bg-blue-600 hover:text-white transition-colors">
                          View
                        </a>
                      </div>
                    </li>
                @endforeach
              </ul>
            @else
              <div class="mt-2 rounded-xl border bg-white p-4 shadow-md text-center">
                <p class="text-gray-500">No activities match your filters</p>
              </div>
            @endif
          @else
            @if($upcoming_activities->isNotEmpty())
              <ul class="divide-y divide-gray-200">
                @foreach($upcoming_activities as $activity)
                <li class="relative border border-gray-200 rounded-lg p-3 md:p-4 pr-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white mb-4">
                  @php
                    $implBadge = null;
                    if(!empty($activity['date'])){
                      try {
                        $implDate = \Carbon\Carbon::parse($activity['date']);
                        $now = \Carbon\Carbon::now();
                        if ($implDate->isToday()) {
                          $implBadge = ['text' => 'Today', 'class' => 'bg-green-600 text-white'];
                        } elseif ($implDate->isFuture()) {
                          $days = (int) $now->diffInDays($implDate);
                          if ($days <= 7) { $implBadge = ['text' => 'In ' . $days . 'd', 'class' => 'bg-yellow-400 text-black']; }
                          else { $implBadge = ['text' => 'In ' . $days . 'd', 'class' => 'bg-blue-600 text-white']; }
                        } else {
                          $days = (int) $implDate->diffInDays($now);
                          $implBadge = ['text' => $days . 'd ago', 'class' => 'bg-orange-400 text-black'];
                        }
                      } catch (\Exception $e) {
                        $implBadge = null;
                      }
                    }
                  @endphp
                  @if($implBadge)
                    <div class="absolute top-3 right-3 z-10">
                      <span class="inline-block text-sm px-3 py-1 rounded {{ $implBadge['class'] }}">{{ $implBadge['text'] }}</span>
                    </div>
                  @endif
                  <div class="flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center border border-gray-200 shrink-0 overflow-hidden">
                      @if(!empty($activity['project_logo']))
                        <img src="{{ $activity['project_logo'] }}" alt="{{ $activity['project_name'] }} logo" class="w-full h-full object-cover" />
                      @else
                        <!-- Clipboard icon fallback -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 2h6a2 2 0 012 2v1h1a2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h1V4a2 2 0 012-2z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6" />
                        </svg>
                      @endif
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-xl md:text-2xl font-semibold text-gray-800 leading-tight">{{ $activity['title'] }}</p>
                      <p class="text-sm font-bold italic text-gray-700 mt-1">{{ $activity['project_name'] }}</p>

                      <!-- Team badge below project name -->
                      @if(!empty($activity['team']) || !empty($activity['point_persons']))
                        <div class="mt-2">
                          <span class="inline-flex items-center text-sm text-gray-800 bg-gray-100 px-2 py-0.5 rounded font-medium">{{ $activity['team'] ?? $activity['point_persons'] }}</span>
                        </div>

                        @if(!empty($activity['date']))
                            @php
                            $status = strtolower($activity['status'] ?? '');
                            $statusClass = 'bg-slate-100 text-gray-800';
                            // Use only the activity table's `status` value here as well
                            if ($status === 'planned') { $statusClass = 'bg-yellow-100 text-yellow-800'; }
                            elseif ($status === 'ongoing') { $statusClass = 'bg-blue-100 text-blue-800'; }
                            elseif ($status === 'completed') { $statusClass = 'bg-red-100 text-red-800'; }
                          @endphp
                              <p class="mt-1 text-sm md:text-base">
                                <span class="inline-flex items-center bg-yellow-100 text-gray-800 px-3 py-1 rounded font-bold">{{ $activity['date'] }}</span>
                                @if(!empty($activity['status']))
                                  <span class="ml-2 inline-flex items-center text-sm px-3 py-1 rounded {{ $statusClass }}">{{ ucfirst($activity['status']) }}</span>
                                @endif
                              </p>
                        @endif
                      @endif

                      <!-- Badges: component, section, timeframe (with clock) -->
                      <div class="mt-3 flex flex-wrap items-center gap-2">
                        @php
                          $compRaw2 = $activity['component'] ?? '';
                          // slightly darker component badges with white text for contrast
                          $compClass2 = 'bg-gray-200 text-gray-800';
                          if ($compRaw2 === 'ROTC') { $compClass2 = 'bg-blue-500 text-white'; }
                          elseif ($compRaw2 === 'LTS') { $compClass2 = 'bg-yellow-500 text-white'; }
                          elseif ($compRaw2 === 'CWTS') { $compClass2 = 'bg-red-500 text-white'; }
                          $sect = $activity['section'] ?? $activity['project_section'] ?? $activity['Project_Section'] ?? null;
                        @endphp

                        @if(!empty($activity['component_label']))
                          <span class="inline-flex items-center text-xs px-2 py-0.5 rounded {{ $compClass2 }}">{{ $activity['component_label'] }}</span>
                        @endif
                        @if($sect)
                          <span class="inline-flex items-center text-xs text-white bg-green-600 px-2 py-0.5 rounded">{{ $sect }}</span>
                        @endif
                        @if(!empty($activity['timeframe']))
                          <span class="inline-flex items-center text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                            <!-- clock icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $activity['timeframe'] }}
                          </span>
                        @endif
                        @if(!empty($activity['point_persons']))
                          <span class="inline-flex items-center text-xs text-gray-600 bg-gray-50 px-2 py-0.5 rounded">Point Person(s): <span class="ml-1 font-medium text-gray-800">{{ $activity['point_persons'] }}</span></span>
                        @endif
                      </div>

                      <div class="flex flex-wrap items-center gap-2 mt-2">
                        @if(!empty($activity['implementation']) || !empty($activity['implementation_date']))
                          <span class="inline-flex items-center text-sm text-gray-700 bg-slate-100 px-3 py-1 rounded">Implementation: <span class="ml-1 font-medium">{{ $activity['implementation'] ?? $activity['implementation_date'] }}</span></span>
                        @endif
                      </div>
                    </div>
                  </div>

                  <a href="{{ $activity['project_id'] ? route('projects.show', $activity['project_id']) . (request('component') ? '?component=' . urlencode(request('component')) : ( $activity['component'] ? '?component=' . urlencode($activity['component']) : '')) : '#' }}"
                     class="px-3 py-1 text-xs md:text-sm text-blue-600 border border-blue-600 rounded-md hover:bg-blue-600 hover:text-white transition-colors text-center sm:text-left shrink-0">
                    View
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            <div class="mt-6 rounded-xl border bg-white p-6 shadow-md text-center">
              <p class="text-gray-500">No upcoming activities match your filters</p>
              <a href="/activities/add_activity.php" class="mt-3 inline-block rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2">Add Activity</a>
            </div>
          @endif
        @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
