@extends('layouts.app')

@section('title', 'Rejected Projects')

@section('content')
    <style>
        @media (max-width: 400px) {
            .rejected-header {
                margin-bottom: 1rem !important;
            }
            .rejected-header a {
                font-size: 0.85rem !important;
                padding: 0.5rem 0.75rem !important;
            }
            .rejected-header svg {
                width: 1rem !important;
                height: 1rem !important;
            }
        }
    </style>
    <div class="mb-6 flex items-center justify-between rejected-header">
        <div>
            <a href="{{ route('projects.pending') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-medium rounded-lg shadow transition">Back to Pending</a>
        </div>
    </div>

    <x-all-projects :projects="$projects" :hide-header="true" />

@endsection
