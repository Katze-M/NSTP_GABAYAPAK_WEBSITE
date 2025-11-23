@extends('layouts.app')

@section('title', 'Rejected Projects')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('projects.pending') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-medium rounded-lg shadow transition">Back to Pending</a>
        </div>
    </div>

    <x-all-projects :projects="$projects" :hide-header="true" />

@endsection
