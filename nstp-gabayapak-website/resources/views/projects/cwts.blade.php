@extends('layouts.app')

@section('title', 'CWTS Projects')

@section('content')
<!-- Main Content -->
<main id="content" class="flex-1 p-6 transition-all duration-300 bg-white min-h-screen">
    <x-all-projects :section="'CWTS'" :current-section="$section ?? 'A'" :projects="$projects" />
</main>
@endsection