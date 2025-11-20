@extends('layouts.app')

@section('title', 'Edit Pending Project Proposal')

@section('content')
<!-- This is the edit pending blade. Copy of edit.blade.php, but can be customized for pending-specific logic. -->
@include('projects.partials.edit-form', ['project' => $project, 'isDraft' => false])
@endsection

@section('scripts')
@include('projects.partials.edit-form-scripts', ['isDraft' => false])
@endsection
