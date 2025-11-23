@extends('layouts.app')

@section('title', 'Current Projects')

@section('content')
<section class="page-container w-full lg:max-w-6xl mx-auto px-2 md:px-6">
  <h1 class="text-2xl font-bold mb-4">Current Projects</h1>

  @if(isset($projects) && $projects->count())
    <div class="grid gap-4">
      @foreach($projects as $project)
        <div class="bg-white rounded-lg p-4 border-2 border-gray-200 shadow-sm flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold">{{ $project->Project_Name ?? 'Untitled' }}</h2>
            <p class="text-sm text-gray-600">Team: {{ $project->Project_Team_Name ?? '-' }} • Status: @include('components.status-badge', ['status' => $project->Project_Status])</p>
          </div>
          <div class="flex items-center gap-2">
            <a href="{{ route('projects.show', $project) }}" class="inline-block bg-blue-500 text-white px-3 py-1 rounded">View</a>
            @if(Auth::user()->isStaff())
              <a href="{{ route('projects.edit', $project) }}" class="inline-block ml-2 bg-gray-200 px-3 py-1 rounded">Edit</a>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="bg-white rounded-lg p-6 border-2 border-gray-200 text-center text-gray-600">No current projects found.</div>
  @endif
</section>
@endsection
