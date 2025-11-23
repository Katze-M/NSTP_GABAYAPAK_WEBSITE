@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="p-6 text-gray-900">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <p class="text-sm text-gray-600">Welcome, {{ Auth::user()->user_Name }}!</p>
          </div>
          <div class="flex gap-2">
            <a href="{{ route('projects.current') }}" class="px-3 py-2 bg-nstpBlue text-white rounded-lg text-sm">Current Projects</a>
            <a href="{{ route('projects.pending') }}" class="px-3 py-2 bg-nstpYellow text-black rounded-lg text-sm">Pending Projects</a>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="mt-4 md:mt-6 grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
          <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
            <p class="text-xs md:text-sm text-gray-500">Submitted Projects</p>
            <p class="text-xl md:text-3xl font-bold text-nstpBlue">{{ $total_projects }}</p>
          </div>
          <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
            <p class="text-xs md:text-sm text-gray-500">Pending Projects</p>
            <p class="text-xl md:text-3xl font-bold text-yellow-600">{{ $project_status_counts['pending'] }}</p>
          </div>
          <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
            <p class="text-xs md:text-sm text-gray-500">Current Projects</p>
            <p class="text-xl md:text-3xl font-bold text-green-600">{{ $project_status_counts['approved'] }}</p>
          </div>
          <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
            <p class="text-xs md:text-sm text-gray-500">Rejected Projects</p>
            <p class="text-xl md:text-3xl font-bold text-red-600">{{ $project_status_counts['rejected'] }}</p>
          </div>
          <div class="col-span-2 md:col-span-1 rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
            <p class="text-xs md:text-sm text-gray-500">Total Student Users</p>
            <p class="text-xl md:text-3xl font-bold text-nstpMaroon">{{ $total_students }}</p>
          </div>
        </div>

        <!-- Upcoming Activities + Filters -->
        <div class="mt-6 md:mt-10 rounded-xl border bg-white p-4 md:p-6 shadow-subtle">
          <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-xl md:text-2xl font-bold">Upcoming Activities</h3>
          </div>

          <!-- Filters -->
          <form method="GET" action="{{ route('dashboard') }}" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2">
            <div class="md:col-span-2">
              <input type="search" name="q" value="{{ request('q') }}" placeholder="Search activities, projects, teams..." class="w-full rounded-lg border px-3 py-2" />
            </div>
            <div>
              <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-lg border px-3 py-2" />
            </div>
            <div class="flex gap-2">
              <select name="section" class="rounded-lg border px-3 py-2 w-1/2">
                <option value="">All Sections</option>
                @foreach($sections ?? collect() as $s)
                  <option value="{{ $s }}" @if(request('section') == $s) selected @endif>{{ $s }}</option>
                @endforeach
              </select>
              <select name="component" class="rounded-lg border px-3 py-2 w-1/2">
                <option value="">All Components</option>
                @foreach($components ?? collect() as $c)
                  <option value="{{ $c }}" @if(request('component') == $c) selected @endif>{{ $c }}</option>
                @endforeach
              </select>
            </div>
            <div class="md:col-span-4 flex gap-2 justify-end">
              <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg border text-sm">Clear</a>
            </div>
          </form>

          @if($upcoming_activities->isNotEmpty())
            <ul class="divide-y divide-gray-200">
              @foreach($upcoming_activities as $activity)
                <li class="py-3 md:py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                  <div class="flex items-start sm:items-center gap-3 md:gap-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-info flex items-center justify-center border border-gray-300 shrink-0">
                      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 md:w-6 md:h-6 text-nstpMaroon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6 1a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-semibold text-gray-800 text-sm md:text-base">{{ $activity['title'] }}</p>
                      <p class="text-xs md:text-sm text-gray-500">
                        {{ $activity['project_name'] }}<br>
                        {{ $activity['team'] }} — {{ $activity['component'] }}<br>
                        <span class="text-xs text-gray-400"><strong class="bg-yellow-100 text-gray-800 px-2 py-0.5 rounded">{{ $activity['date'] }}</strong> | {{ $activity['location'] }}</span>
                      </p>
                    </div>
                  </div>

                  <a href="{{ $activity['project_id'] ? route('projects.show', $activity['project_id']) . (request('component') ? '?component=' . urlencode(request('component')) : ( $activity['component'] ? '?component=' . urlencode($activity['component']) : '')) : '#' }}"
                     class="px-3 py-1 text-xs md:text-sm text-nstpBlue border border-nstpBlue rounded-md hover:bg-nstpBlue hover:text-white transition text-center sm:text-left shrink-0">
                    View
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            <div class="mt-6 rounded-xl border bg-white p-6 shadow-subtle text-center">
              <p class="text-gray-500">No upcoming activities match your filters</p>
              <a href="/activities/add_activity.php" class="mt-3 inline-block rounded-lg bg-nstpBlue text-white px-4 py-2">Add Activity</a>
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
