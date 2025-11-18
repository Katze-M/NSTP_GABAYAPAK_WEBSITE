@extends('layouts.app')

@section('title', 'Edit Draft Project Proposal')

@section('content')
<!-- This is the edit draft blade. Copy of edit.blade.php, but can be customized for draft-specific logic. -->
@include('projects.partials.edit-form', ['project' => $project, 'isDraft' => true])

<!-- Include the JavaScript for form functionality -->
@include('projects.partials.edit-form-scripts')
@endsection