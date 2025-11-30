@extends('layouts.app')


@section('title', 'Edit Project Proposal (Staff)')

@section('content')
<!-- Project Proposal - Staff Edit -->
<section id="upload-project" class="space-y-6 md:space-y-8 page-container max-w-screen-lg mx-auto">
  <!-- Main Heading -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">
        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-semibold mr-2">STAFF</span>
        Edit Project Proposal
      </h1>
    </div>
  </div>
  
  <!-- Staff Notice -->
  <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5 text-purple-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-purple-800">Staff Edit Mode</h3>
        <div class="mt-2 text-sm text-purple-700">
          <p>You are editing this project as staff. Member selection will be based on the project's original component (<strong>{{ $project->Project_Component }}</strong>) and section (<strong>{{ $project->Project_Section }}</strong>).</p>
        </div>
      </div>
    </div>
  </div>
 
  <form id="projectForm" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
    @method('PUT')
    <input type="hidden" name="staff_save" value="1">
   
    <!-- Include the form body -->
    @include('projects.partials.staff-edit-form-body', ['project' => $project, 'isDraft' => false])

    <!-- EDIT BUTTONS -->
    <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
      <button type="button" id="cancelEditBtn" data-redirect="{{ route('projects.show', $project) }}" class="rounded-lg bg-gray-200 hover:bg-gray-300 px-4 py-2 text-sm md:text-base transition-colors text-center text-gray-700">Cancel</button>
      <button type="submit" class="rounded-lg bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm md:text-base transition-colors">Save Changes</button>
    </div>
  </form>
</section>

@endsection


{{-- Staff scripts moved into the form body partial to ensure correct placement inside the DOM --}}