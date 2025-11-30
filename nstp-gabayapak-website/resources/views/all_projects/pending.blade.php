@extends('layouts.app')

@section('title', 'Pending Projects')

@section('content')
    <style>
        @media (max-width: 400px) {
            .pending-header {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
            .pending-header h1 {
                font-size: 1rem !important;
                flex-shrink: 0 !important;
            }
            .pending-header div {
                flex-shrink: 0 !important;
            }
            .pending-header a {
                font-size: 0.7rem !important;
                padding: 0.4rem 0.6rem !important;
                white-space: nowrap !important;
            }
        }
    </style>
    <div class="flex items-center justify-between mb-4 pending-header">
        <h1 class="text-2xl font-bold">Pending Projects</h1>
        <div>
            <a href="{{ route('projects.rejected') }}" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow transition">View Rejected Projects</a>
        </div>
    </div>

    <x-all-projects :section="'Pending Projects'" :projects="$projects" :hide-header="true" />
@endsection