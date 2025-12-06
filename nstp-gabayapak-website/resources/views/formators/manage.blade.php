@extends('layouts.app')

@section('title', 'Manage NSTP Formators')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 mb-2">Manage NSTP Formators</h1>
            <button type="button" class="text-sm text-gray-500" title="Only approved staff appear here. Approve staff via Staff Registration Approvals first.">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10A8 8 0 11.999 10 8 8 0 0118 10zm-8.93-4.588a.75.75 0 10-1.14.974l1.22 1.429A2.25 2.25 0 109.75 11h.75a.75.75 0 100-1.5h-.75a.75.75 0 01-.75-.75c0-.38.276-.702.652-.755l-.472-.916zM10 13.5a.75.75 0 100 1.5.75.75 0 000-1.5z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        <p class="text-gray-600 text-sm md:text-base">Select staff members to display as NSTP Formators on the homepage</p>
    </div>

    <!-- Success Message -->
    @if(session('status'))
        <div class="mb-6">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('status') }}
            </div>
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('formators.update') }}">
        @csrf
        <div class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
            <div class="p-6 md:p-8">
                <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Select NSTP Formators</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @php $shown = false; @endphp
                    @foreach($allStaff as $staff)
                        @if(trim(($staff->user_role ?? '') ) === 'NSTP Formator')
                            @php $shown = true; @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-start gap-3">
                                    <div class="flex items-center h-5">
                                        <input id="formator_{{ $staff->user_id }}" 
                                               name="formators[]" 
                                               type="checkbox" 
                                               value="{{ $staff->user_id }}"
                                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                               {{ in_array($staff->user_id, $currentFormators) ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <label for="formator_{{ $staff->user_id }}" class="block text-sm font-medium text-gray-700 cursor-pointer">
                                            <div class="flex items-center gap-3 mb-2">
                                                <div class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center bg-white overflow-hidden">
                                                    @if($staff->staff && $staff->staff->staff_formal_picture)
                                                        <img src="{{ asset('storage/' . $staff->staff->staff_formal_picture) }}" 
                                                             alt="{{ $staff->user_Name }}" 
                                                             class="w-full h-full object-cover">
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="1.5" class="w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21a7.5 7.5 0 0115 0v.75H4.5V21z" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 break-words whitespace-normal">{{ $staff->user_Name }}</p>
                                                    <p class="text-xs text-gray-500 whitespace-normal">{{ $staff->user_role }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @unless($shown)
                        <div class="col-span-full text-center py-8">
                            <p class="text-gray-600">No NSTP Formators found.</p>
                        </div>
                    @endunless
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 sm:px-8 sm:py-6 flex justify-end gap-3">
                <a href="{{ route('about') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Formators
                </button>
            </div>
        </div>
    </form>
</div>
@endsection