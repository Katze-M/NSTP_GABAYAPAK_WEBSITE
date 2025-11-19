@extends('layouts.app')

@section('title', 'Edit Draft Project Proposal')

@section('content')
<!-- Main Heading -->
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Edit Project Proposal Draft</h1>
</div>

<!-- This is the edit draft blade. Copy of edit.blade.php, but can be customized for draft-specific logic. -->
<div class="page-container">
  @include('projects.partials.edit-form', ['project' => $project, 'isDraft' => true])
</div>

<!-- Include the JavaScript for form functionality -->
@include('projects.partials.edit-form-scripts')
@endsection