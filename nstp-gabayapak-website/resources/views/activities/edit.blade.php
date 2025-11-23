@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Activity</h1>
                    <p class="text-gray-600">Update the status and upload proof for this activity</p>
                </div>

                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">{{ $activity->Stage }}</h3>
                    <p class="text-gray-700">{{ $activity->Specific_Activity }}</p>
                    <p class="text-gray-600 mt-2">Time Frame: {{ $activity->Time_Frame }}</p>
                    <p class="text-gray-600">Point Persons: {{ $activity->Point_Persons }}</p>
                </div>

                <form action="{{ route('activities.update', $activity) }}" method="POST" enctype="multipart/form-data" id="activityForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        @php $curStatus = strtolower((string)($activity->status ?? '')) @endphp
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Status <span class="text-red-500">*</span></label>
                        <select id="statusSelect" name="status" data-initial-status="{{ $curStatus }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" @if($curStatus === 'completed') disabled @endif>
                            @if($curStatus === 'planned')
                                {{-- Keep the current value but hide it so the form still submits the existing status if unchanged --}}
                                <option value="planned" selected hidden>Planned (current)</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            @else
                                <option value="planned" {{ $curStatus == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="ongoing" {{ $curStatus == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ $curStatus == 'completed' ? 'selected' : '' }}>Completed</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        @php
                            $hasExistingProof = $activity->proof_picture;
                        @endphp
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proof Picture <span class="text-red-500">*</span></label>
                        
                        {{-- Upload Box Container: h-64 ensures space, overflow-hidden contains the placeholder/preview --}}
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-8 border-2 border-gray-300 border-dashed rounded-md relative h-64 overflow-hidden">
                            
                            {{-- Default upload content (icon/text) --}}
                            <div class="space-y-1 text-center flex flex-col items-center justify-center w-full h-full">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="proof_picture" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                        <span>Upload a file</span>
                                        <input id="proof_picture" name="proof_picture" type="file" class="sr-only" accept="image/*" @if(!$hasExistingProof) required @endif @if($curStatus === 'completed') disabled @endif>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                <p id="proofRequiredNote" class="text-xs text-red-600 mt-1" style="display: none;">A proof picture is required when you change the activity status.</p>
                            </div>
                            
                            <div id="imagePreview" class="absolute inset-0 flex items-center justify-center hidden bg-white rounded-md p-4">
                                <div class="relative w-full h-full flex items-center justify-center">
                                    {{-- **FIX: Added w-full and h-full to force the image to use the full dimension of the container.** --}}
                                    <img src="" alt="Preview" class="w-full h-full object-contain rounded-md transform scale-125 origin-center">
                                    <button type="button" id="removeImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hidden">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('proof_picture')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    @if($activity->proof_picture)
                    <div class="mb-6 overflow-hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Proof Picture</label>
                        {{-- Increased size: changed from w-40 to w-48 for a slightly larger display --}}
                        <img src="{{ asset('storage/' . $activity->proof_picture) }}" alt="Proof" class="block w-48 h-auto rounded-lg">
                    </div>
                    @endif
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('projects.show', $activity->project) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Cancel
                        </a>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg" @if($curStatus === 'completed') disabled @endif>
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
    const fileInput = document.getElementById('proof_picture');
    const statusSelect = document.getElementById('statusSelect');
    const initialStatus = statusSelect ? (statusSelect.dataset.initialStatus || '') : '';
    const hasExistingProof = @json($hasExistingProof ? true : false);
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = imagePreview.querySelector('img');
    const uploadIcon = document.querySelector('.space-y-1.text-center');
    const removeImageBtn = document.getElementById('removeImage');
    const proofRequiredNote = document.getElementById('proofRequiredNote');
    // If the activity was already completed, inform the user and ensure status select stays disabled
    if ((initialStatus || '').toLowerCase() === 'completed') {
        try {
            Swal.fire({
                title: 'Activity Completed',
                text: 'This activity is already marked as completed. Status and proof picture cannot be changed anymore.',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } catch (e) {
            // ignore if Swal is not available
        }
        if (statusSelect) {
            statusSelect.disabled = true;
        }
    }
    
    // Handle file input change for preview
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.classList.remove('hidden');
                uploadIcon.classList.add('hidden');
                removeImageBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
            uploadIcon.classList.remove('hidden');
            removeImageBtn.classList.add('hidden');
        }
    });
    
    // Handle remove image button click
    removeImageBtn.addEventListener('click', function() {
        fileInput.value = '';
        previewImage.src = '';
        imagePreview.classList.add('hidden');
        uploadIcon.classList.remove('hidden');
        removeImageBtn.classList.add('hidden');
    });
    
    // Toggle required attribute on file input when status changes
    if (statusSelect) {
        // Ensure initial required state
        if (!hasExistingProof) {
            fileInput.required = true;
        }

        statusSelect.addEventListener('change', function() {
            const newStatus = (statusSelect.value || '').toLowerCase();
            const changed = newStatus !== (initialStatus || '').toLowerCase();
            if (changed) {
                fileInput.required = true;
                if (proofRequiredNote) proofRequiredNote.style.display = 'block';
            } else {
                fileInput.required = !hasExistingProof;
                if (proofRequiredNote) proofRequiredNote.style.display = 'none';
            }
        });
    }
    
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