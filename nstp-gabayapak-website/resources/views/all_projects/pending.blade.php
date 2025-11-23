@extends('layouts.app')

@section('title', 'Pending Projects')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Pending Projects</h1>
        <div>
            <a href="{{ route('projects.rejected') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow transition">View Rejected Projects</a>
        </div>
    </div>

    <x-all-projects :section="'Pending Projects'" :projects="$projects" :hide-header="true" />
@endsection