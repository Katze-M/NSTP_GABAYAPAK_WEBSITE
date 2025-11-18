@extends('layouts.app')

@section('title', 'Edit Submitted Project Proposal')

@section('content')
<!-- This is the edit submitted blade. Copy of edit.blade.php, but can be customized for submitted-specific logic. -->
@include('projects.partials.edit-form', ['project' => $project, 'isDraft' => false])
@endsection
