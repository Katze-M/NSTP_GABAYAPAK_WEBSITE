@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Activity</h1>
                    <p class="text-gray-600">Update the status and upload proof for this activity</p>
                </div>

                <!-- Activity Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">{{ $activity->Stage }}</h3>
                    <p class="text-gray-700">{{ $activity->Specific_Activity }}</p>
                    <p class="text-gray-600 mt-2">Time Frame: {{ $activity->Time_Frame }}</p>
                    <p class="text-gray-600">Point Persons: {{ $activity->Point_Persons }}</p>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('activities.update', $activity) }}" method="POST" enctype="multipart/form-data" id="activityForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Status Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                            <option value="planned" {{ $activity->status == 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="ongoing" {{ $activity->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $activity->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    
                    <!-- Proof Picture Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proof Picture <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="proof_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span>Upload a file</span>
                                        <input id="proof_picture" name="proof_picture" type="file" class="sr-only" accept="image/*" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('proof_picture')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Current Proof Picture (if exists) -->
                    @if($activity->budget && $activity->budget->proof_picture)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Proof Picture</label>
                        <img src="{{ asset('storage/' . $activity->budget->proof_picture) }}" alt="Proof" class="max-w-xs h-auto rounded-lg">
                    </div>
                    @endif
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('projects.show', $activity->project) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Cancel
                        </a>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                            Update Activity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// SweetAlert2 confirmation on form submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('activityForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update this activity?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection