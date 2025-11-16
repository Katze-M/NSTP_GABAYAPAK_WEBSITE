@extends('layouts.app')

@section('title', 'Archived Projects')

@section('content')
<div class="flex bg-gray-100">
    <!-- Include Sidebar -->
    <x-sidebar />
    
    <!-- Main Content -->
    <main id="content" class="flex-1 ml-64 p-6 transition-all duration-300 bg-white min-h-screen">
        <x-all-projects :section="'Archived'" :projects="$projects" />
    </main>
</div>
@endsection