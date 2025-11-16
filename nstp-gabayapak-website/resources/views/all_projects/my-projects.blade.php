@extends('layouts.app')

@section('title', 'My Projects')

@section('content')
<!-- Main Content -->
<main id="content" class="flex-1 p-6 transition-all duration-300 bg-white min-h-screen">
    <x-all-projects :section="'My Projects'" :projects="$projects" />
</main>
@endsection