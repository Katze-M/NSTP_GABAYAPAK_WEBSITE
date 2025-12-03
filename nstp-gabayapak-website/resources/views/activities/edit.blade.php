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
                {{-- The append-to-latest-update checkbox is moved inside the form below so it will be submitted. --}}

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
                        {{-- Append-to-latest-update removed: every status change creates a new history entry. --}}
                        @php
                            $curStatus = strtolower((string)($activity->status ?? ''));
                            $projectStatus = strtolower((string)($activity->project->Project_Status ?? ''));
                        @endphp
                        <label class="block text-sm font-medium text-gray-700 mb-2">Activity Status <span class="text-red-500">*</span></label>
                        {{-- Allow editing only when the parent project's status is 'approved' or 'current' and the activity is not already completed --}}
                        <select id="statusSelect" name="status" data-initial-status="{{ $curStatus }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500" @if($curStatus === 'completed' || !in_array($projectStatus, ['approved','current'])) disabled @endif>
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
                            $hasExistingProof = false;
                            try {
                                if ($activity->updates && $activity->updates->isNotEmpty()) {
                                    foreach ($activity->updates as $u) {
                                        if ($u->pictures && $u->pictures->isNotEmpty()) {
                                            $hasExistingProof = true;
                                            break;
                                        }
                                    }
                                }
                            } catch (\Throwable $e) {
                                $hasExistingProof = false;
                            }
                        @endphp
                        @if(!empty($disablePlanned) && $disablePlanned)
                            <p class="text-xs text-gray-600 mt-1">Students cannot change an <span class="font-semibold">Ongoing</span> activity back to <span class="font-semibold">Planned</span>.</p>
                        @endif
                        <label class="block text-sm font-medium text-gray-700 mb-2">Proof Picture <span class="text-red-500">*</span> <span class="text-xs text-gray-500 ml-2">(Up to 5 images)</span></label>
                        
                        {{-- Upload Box Container: h-64 ensures space, overflow-hidden contains the placeholder/preview --}}
                        <div id="dropZone" class="mt-1 flex justify-center px-6 pt-5 pb-8 border-2 border-gray-300 border-dashed rounded-md relative h-64 overflow-hidden">
                            
                            {{-- Default upload content (icon/text) --}}
                            <div class="space-y-1 text-center flex flex-col items-center justify-center w-full h-full">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="proof_pictures" class="relative cursor-pointer bg-white rounded-md font-medium text-red-600 hover:text-red-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-red-500">
                                            <span>Upload files (max 5)</span>
                                            <input id="proof_pictures" name="proof_pictures[]" type="file" class="sr-only" accept="image/*" multiple @if(!$hasExistingProof) required @endif @if($curStatus === 'completed' || !in_array($projectStatus, ['approved','current'])) disabled @endif>
                                        </label>
                                            <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP up to 5MB each — up to 5 files</p>
                                <p id="proofRequiredNote" class="text-xs text-red-600 mt-1" style="display: none;">A proof picture is required when you change the activity status.</p>
                                @if(!in_array($projectStatus, ['approved','current']) && $curStatus !== 'completed')
                                    <p class="mt-2 text-sm text-yellow-700 bg-yellow-50 border border-yellow-100 p-2 rounded">Project is not approved — students cannot edit activity status or upload proof until the project is approved.</p>
                                @endif
                            </div>
                            
                            <div id="imagePreview" class="absolute inset-0 flex items-center justify-center hidden bg-white rounded-md p-4 overflow-auto">
                                <div class="relative w-full">
                                    <div id="thumbnails" class="flex items-center space-x-2"></div>
                                    {{-- per-thumbnail remove buttons exist; clear-all handled by a small control below thumbnails --}}
                                    <div class="absolute top-2 right-2 flex space-x-2">
                                        <button type="button" id="removeImageBtn" class="hidden inline-flex items-center px-2 py-1 bg-white text-red-600 rounded-md border">Clear</button>
                                    </div>
                                </div>
                            </div>
                            {{-- Always-visible Add More button inside the drop zone so it doesn't disappear under previews --}}
                            <button type="button" id="addMoreBtn" class="absolute bottom-3 right-3 z-20 inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-md">Add more images</button>
                        </div>
                        @if($errors->has('proof_pictures'))
                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('proof_pictures') }}</p>
                        @elseif($errors->has('proof_pictures.0'))
                            <p class="mt-2 text-sm text-red-600">{{ $errors->first('proof_pictures.0') }}</p>
                        @endif
                    </div>
                    
                    @php
                        $latestUpdate = null;
                        try {
                            if ($activity->updates && $activity->updates->isNotEmpty()) {
                                $latestUpdate = $activity->updates->sortByDesc('created_at')->first();
                            }
                        } catch (\Throwable $e) {
                            $latestUpdate = null;
                        }
                    @endphp

                    @if($latestUpdate && $latestUpdate->pictures && $latestUpdate->pictures->isNotEmpty())
                        <div class="mb-6 overflow-hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Proof Picture(s)</label>
                            <div class="flex items-start space-x-3">
                                @foreach($latestUpdate->pictures as $pic)
                                    <a href="{{ asset('storage/' . $pic->path) }}" target="_blank" class="block w-48 rounded-lg overflow-hidden border">
                                        <img src="{{ asset('storage/' . $pic->path) }}" alt="Proof" class="w-48 h-32 object-cover rounded">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    {{-- Legacy `activities.proof_picture` removed; current proof pictures are shown above from the latest update. --}}
                    @endif
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('projects.show', $activity->project) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                            Cancel
                        </a>
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg" @if($curStatus === 'completed' || !in_array($projectStatus, ['approved','current'])) disabled @endif>
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
    const fileInput = document.getElementById('proof_pictures');
    const addMoreBtn = document.getElementById('addMoreBtn');
    const statusSelect = document.getElementById('statusSelect');
    const initialStatus = statusSelect ? (statusSelect.dataset.initialStatus || '') : '';
    const hasExistingProof = @json($hasExistingProof ? true : false);
    const imagePreview = document.getElementById('imagePreview');
    const thumbnailsContainer = document.getElementById('thumbnails');
    const removeImageBtn = document.getElementById('removeImageBtn');
    const uploadIcon = document.querySelector('.space-y-1.text-center');
    const dropZone = document.getElementById('dropZone');
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
    
    // Maintain a mutable list of selected files so we can remove individual items
    let selectedFiles = [];

    // Initial visibility: hide Add More until user picks at least one image
    if (addMoreBtn) {
        addMoreBtn.classList.add('hidden');
        try { addMoreBtn.style.display = 'none'; } catch (e) {}
    }

    function renderThumbnails() {
        thumbnailsContainer.innerHTML = '';
        if (!selectedFiles.length) {
            imagePreview.classList.add('hidden');
            if (uploadIcon) uploadIcon.classList.remove('hidden');
            if (removeImageBtn) removeImageBtn.classList.add('hidden');
            if (uploadIcon && uploadIcon.querySelector('p')) uploadIcon.querySelector('p').textContent = 'or drag and drop';
            if (addMoreBtn) addMoreBtn.classList.add('hidden');
            // clear the actual file input
            try {
                fileInput.value = '';
            } catch (e) {}
            return;
        }

        selectedFiles.forEach((file, idx) => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            const wrapper = document.createElement('div');
            wrapper.className = 'relative w-24 h-24 overflow-hidden rounded-md border';

            // remove button for each thumbnail
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'absolute top-1 right-1 bg-white text-red-600 rounded-full p-0.5 z-10';
            removeBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            removeBtn.addEventListener('click', function() {
                // remove this file from selectedFiles and update input
                selectedFiles.splice(idx, 1);
                updateFileInputFromSelected();
                renderThumbnails();
            });

            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            wrapper.appendChild(removeBtn);
            wrapper.appendChild(img);
            thumbnailsContainer.appendChild(wrapper);

            reader.onload = function(evt) {
                img.src = evt.target.result;
            };
            reader.readAsDataURL(file);
        });

        // update UI
        imagePreview.classList.remove('hidden');
        if (uploadIcon) uploadIcon.classList.add('hidden');
        if (removeImageBtn) removeImageBtn.classList.remove('hidden');
        if (selectedFiles.length > 1 && uploadIcon && uploadIcon.querySelector('p')) {
            uploadIcon.querySelector('p').textContent = selectedFiles.length + ' files selected';
        }
        // Show Add More only when there is at least 1 and less than 5 selected
        if (addMoreBtn) {
            if (selectedFiles.length >= 1 && selectedFiles.length < 5) {
                addMoreBtn.classList.remove('hidden');
                try { addMoreBtn.style.display = 'inline-flex'; } catch (e) {}
            } else {
                addMoreBtn.classList.add('hidden');
                try { addMoreBtn.style.display = 'none'; } catch (e) {}
            }
        }
    }

    function updateFileInputFromSelected() {
        // create a DataTransfer to populate fileInput.files
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(f => dataTransfer.items.add(f));
        fileInput.files = dataTransfer.files;
    }

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files || []);
            console.debug('fileInput.change', files.length);
            // append new files to selectedFiles (but ensure total <= 5)
            const combined = selectedFiles.concat(files);
            if (combined.length > 5) {
                alert('You can upload up to 5 images at once. Please select fewer files.');
                return;
            }
            selectedFiles = combined;
            updateFileInputFromSelected();
            renderThumbnails();
        });
    }

    // Drag and drop support on the dropZone
    if (dropZone) {
        ;['dragenter','dragover'].forEach(evtName => {
            dropZone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('bg-gray-100');
            });
        });
        ;['dragleave','drop'].forEach(evtName => {
            dropZone.addEventListener(evtName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('bg-gray-100');
            });
        });

        dropZone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = Array.from(dt.files || []);
            console.debug('dropZone.drop', files.length, 'ctrlKey=', e.ctrlKey, 'shiftKey=', e.shiftKey);
            if (!files.length) return;

            // Default behavior: APPEND dropped files to the current selection
            // If user holds Ctrl or Shift while dropping, treat as REPLACE instead
            if (e.ctrlKey || e.shiftKey) {
                // Replace selection
                if (files.length > 5) {
                    alert('You can upload up to 5 images at once. Please select fewer files.');
                    return;
                }
                selectedFiles = files;
            } else {
                const combined = selectedFiles.concat(files);
                if (combined.length > 5) {
                    alert('You can upload up to 5 images at once. Please select fewer files.');
                    return;
                }
                selectedFiles = combined;
            }

            updateFileInputFromSelected();
            renderThumbnails();
        });
    }

    // Wire the Add more images button to open the file chooser
    if (addMoreBtn && fileInput) {
        addMoreBtn.addEventListener('click', function() {
            // If input is disabled, do nothing
            if (fileInput.disabled) return;
            fileInput.click();
        });
    }
    
    // Handle remove image button click (clears selected files and previews)
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            fileInput.value = '';
            thumbnailsContainer.innerHTML = '';
            selectedFiles = [];
            if (imagePreview) imagePreview.classList.add('hidden');
            if (uploadIcon) uploadIcon.classList.remove('hidden');
            removeImageBtn.classList.add('hidden');
            if (uploadIcon && uploadIcon.querySelector('p')) uploadIcon.querySelector('p').textContent = 'or drag and drop';
            // update add-more visibility after clear
            if (addMoreBtn) addMoreBtn.classList.add('hidden');
        });
    }
    
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