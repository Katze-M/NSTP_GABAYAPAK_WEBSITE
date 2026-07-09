@extends('layouts.app')

@section('title', 'Bulk Student Registration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Bulk Student Registration</h1>
            <p class="text-gray-600">Upload an Excel or CSV file to register multiple students at once</p>
        </div>

        <!-- Error/Success Messages -->
        @if($errors && count($errors) > 0)
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="font-semibold text-red-800 mb-2">Validation Error</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="font-semibold text-red-800 mb-2">Error</h3>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white">Upload Student Data</h2>
            </div>

            <div class="p-6 md:p-8">
                <form action="{{ route('bulk-import.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- File Upload -->
                    <div>
                        <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                            Select File <span class="text-red-600">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50"
                             id="dropzone"
                             data-max-size="5242880">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-7" />
                                </svg>
                                <p class="text-gray-700 font-medium">Drag and drop your file here</p>
                                <p class="text-gray-500 text-sm mt-1">or click below to browse</p>
                                <input type="file" id="file" name="file" class="hidden" accept=".csv,.xlsx,.xls" required>
                                <button type="button" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" onclick="document.getElementById('file').click()">
                                    Choose File
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Supported formats: CSV (recommended), XLSX, XLS (Max 5MB)</p>
                        <div id="fileInfo" class="mt-3 p-3 bg-blue-50 rounded hidden">
                            <p class="text-sm text-blue-700"><strong>Selected:</strong> <span id="fileName"></span></p>
                            <p class="text-xs text-blue-600 mt-1"><span id="fileSize"></span></p>
                        </div>
                    </div>

                    <!-- Template Download -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-2">📋 Need Help?</h3>
                        <p class="text-sm text-blue-800 mb-3">
                            Download our template CSV file to see the correct format and required columns.
                        </p>
                        <a href="{{ route('bulk-import.template') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template (CSV)
                        </a>
                    </div>

                    <!-- Required Columns Info -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Required Columns</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Email</p>
                                    <p class="text-xs text-gray-600">ADZU Student ID format: <span class="font-mono bg-gray-100 px-1 rounded">co230123@adzu.edu.ph</span></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Full Name</p>
                                    <p class="text-xs text-gray-600">Student's full name</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Contact Number</p>
                                    <p class="text-xs text-gray-600">Mobile number</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Course</p>
                                    <p class="text-xs text-gray-600">E.g., BS Computer Science</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Year</p>
                                    <p class="text-xs text-gray-600">Numeric value (1-4)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Section</p>
                                    <p class="text-xs text-gray-600">E.g., A, B, C</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="text-green-600 font-bold mr-2">✓</span>
                                <div>
                                    <p class="font-medium text-sm text-gray-800">Component</p>
                                    <p class="text-xs text-gray-600">ROTC, LTS, or CWTS</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="font-semibold text-yellow-900 mb-2">⚠️ Important Notes</h3>
                        <ul class="text-sm text-yellow-800 space-y-1">
                            <li>• Email must follow ADZU student ID format: <span class="font-mono bg-yellow-200 px-2 py-0.5 rounded">co230123@adzu.edu.ph</span></li>
                            <li>• Each student will be automatically approved after import</li>
                            <li>• Email addresses must be unique (duplicates will be rejected)</li>
                            <li>• All students will have the default password: <span class="font-mono bg-yellow-200 px-2 py-0.5 rounded">Student@NSTP123</span></li>
                            <li>• All required columns must be present in the file</li>
                            <li>• You must communicate the default password to students so they can login</li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M13 13l-4 4m0 0l-4-4m4 4V5m0 10l4 4m0 0l-4-4m4 4V5" />
                            </svg>
                            Upload & Register Students
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold rounded-lg hover:bg-gray-400 transition-colors">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Dropzone functionality -->
<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('file');
const fileInfo = document.getElementById('fileInfo');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Highlight drop area when item is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.add('border-blue-500', 'bg-blue-50');
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropzone.addEventListener(eventName, () => {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    });
});

// Handle dropped files
dropzone.addEventListener('drop', handleDrop);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    updateFileInfo();
}

// Handle file selection
fileInput.addEventListener('change', updateFileInfo);

function updateFileInfo() {
    const file = fileInput.files[0];
    if (file) {
        const maxSize = dropzone.dataset.maxSize;
        
        if (file.size > maxSize) {
            alert('File size exceeds 5MB limit');
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            return;
        }
        
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
        fileInfo.classList.remove('hidden');
    }
}
</script>
@endsection
