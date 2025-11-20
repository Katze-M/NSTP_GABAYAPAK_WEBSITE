@extends('layouts.app')

@section('title', 'Edit Draft Project Proposal')

@section('content')
<!-- Project Proposal -->
<section id="upload-project" class="space-y-6 md:space-y-8 page-container max-w-screen-lg mx-auto">
  <!-- Main Heading -->
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-4">
      <!-- Back Button -->
      <x-back-button />
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">
        @if(isset($isResubmission) && $isResubmission)
          Resubmit Project
        @else
          Edit Project Draft
        @endif
      </h1>
    </div>
  </div>

  <!-- Show rejection reason if this is a rejected project -->
  @if($project->Project_Status === 'rejected' && $project->Project_Rejection_Reason)
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
      <h3 class="font-semibold text-red-800 mb-2">Rejection Reason:</h3>
      <p class="text-red-700">{{ $project->Project_Rejection_Reason }}</p>
      
      @if($project->previous_rejection_reasons)
        @php
          $previousReasons = json_decode($project->previous_rejection_reasons, true);
        @endphp
        @if(!empty($previousReasons))
          <div class="mt-4">
            <h4 class="font-semibold text-red-800 mb-2">Previous Rejection History:</h4>
            @foreach($previousReasons as $index => $reason)
              <div class="bg-red-100 border border-red-300 rounded p-3 mb-2">
                <p class="text-red-700 text-sm"><strong>Rejection #{{ count($previousReasons) - $index }}:</strong> {{ $reason['reason'] }}</p>
                <p class="text-red-600 text-xs mt-1">Rejected on: {{ $reason['rejected_at'] }}</p>
              </div>
            @endforeach
          </div>
        @endif
      @endif
    </div>
  @endif
 
  <form id="projectForm" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
    @method('PUT')
    
    @include('projects.partials.edit-form-body', ['project' => $project, 'isDraft' => $isDraft ?? true, 'isResubmission' => $isResubmission ?? false])
  </form>
</section>

<!-- Include the JavaScript for form functionality -->
@include('projects.partials.edit-form-scripts')
@endsection