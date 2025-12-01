@extends('layouts.app')

@section('title', 'All Approved Projects')

@section('content')
<main id="content" class="flex-1 p-6 transition-all duration-300 bg-white min-h-screen">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl md:text-4xl font-bold text-black">All Approved Projects</h1>
            <div>
                <a href="{{ route('projects.current') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow transition">Back to Current Projects</a>
            </div>
        </div>
        <p class="text-base md:text-lg text-gray-700">
            Showing all completed, archived, and approved projects
        </p>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" id="searchInput" placeholder="Search by project name, owner, component, or section..." class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button id="clearSearch" class="absolute right-3 top-3.5 h-5 w-5 text-gray-400 hover:text-gray-600 cursor-pointer hidden">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Projects Table -->
    @if(isset($projects) && $projects->count())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Component
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Section
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project Owner
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Created
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Endorsed By
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Approved By
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marked as Completed By
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                            @php
                                // Get project owner
                                $owner = $project->student()->with('user')->first();
                                $ownerName = $owner?->user?->user_Name ?? 'N/A';
                            @endphp
                            <tr class="hover:bg-gray-50 searchable-row" 
                                data-project-name="{{ strtolower($project->Project_Name ?? '') }}"
                                data-owner="{{ strtolower($ownerName) }}"
                                data-component="{{ strtolower($project->Project_Component ?? '') }}"
                                data-section="{{ strtolower($project->Project_Section ?? '') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900 underline">
                                        {{ $project->Project_Name ?? 'Untitled' }}
                                    </a>
                                    <div class="text-sm text-gray-500">
                                        {{ $project->Project_Team_Name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $project->Project_Component ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $project->Project_Section ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $ownerName }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $project->created_at ? $project->created_at->format('M d, Y') : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $project->endorsedBy?->user_Name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $project->approvedBy?->user_Name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $project->completedBy?->user_Name ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @include('components.status-badge', ['status' => $project->Project_Status])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg p-6 border-2 border-gray-200 text-center text-gray-600">
            No approved projects found.
        </div>
    @endif
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const clearButton = document.getElementById('clearSearch');
        const rows = document.querySelectorAll('.searchable-row');

        // Show/hide clear button based on input
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Toggle clear button visibility
            if (this.value.length > 0) {
                clearButton.classList.remove('hidden');
            } else {
                clearButton.classList.add('hidden');
            }

            // Filter rows
            rows.forEach(row => {
                const projectName = row.dataset.projectName || '';
                const owner = row.dataset.owner || '';
                const component = row.dataset.component || '';
                const section = row.dataset.section || '';

                const matches = projectName.includes(searchTerm) || 
                               owner.includes(searchTerm) || 
                               component.includes(searchTerm) || 
                               section.includes(searchTerm);

                row.style.display = matches ? '' : 'none';
            });
        });

        // Clear search when X button is clicked
        clearButton.addEventListener('click', function() {
            searchInput.value = '';
            clearButton.classList.add('hidden');
            // Show all rows
            rows.forEach(row => {
                row.style.display = '';
            });
            searchInput.focus();
        });
    });
</script>
@endsection
