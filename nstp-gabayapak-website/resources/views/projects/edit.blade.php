@extends('layouts.app')

@section('title', 'Edit Project Proposal')

@section('content')
{{-- Simple fallback edit view - most editing should use dedicated views --}}

<section class="space-y-6 md:space-y-8 page-container max-w-5xl mx-auto">
  <!-- Main Heading -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <x-back-button />
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">Edit Project Proposal</h1>
    </div>
  </div>

  <!-- Notice Banner -->
  <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
    <div class="flex">
      <div class="shrink-0">
        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <p class="text-sm text-blue-700">
          <strong>Notice:</strong> This is a basic edit view. For the full editing experience, please use the dedicated edit interface for your user type and project status.
        </p>
      </div>
    </div>
  </div>
 
  <form id="projectForm" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
    @method('PUT')
    
    {{-- Include the standardized form body --}}
    @include('projects.partials.edit-form-body', ['project' => $project, 'isDraft' => true])

    <!-- EDIT BUTTONS -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
      <a href="{{ route('projects.show', $project) }}" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors text-center text-gray-700">Cancel</a>
      <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Save Changes</button>
    </div>
  </form>
</section>

{{-- Include basic student scripts as fallback --}}
@include('projects.partials.edit-form-scripts', ['project' => $project])

@endsection