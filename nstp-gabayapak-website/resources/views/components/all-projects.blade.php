<div class="flex-1 p-6">
    <!-- Header and Back button -->
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <button onclick="history.back()" 
                    class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-100 
                           text-gray-700 font-medium rounded-lg shadow transition">
                <svg class="h-5 w-5 mr-2 text-gray-700" xmlns="http://www.w3.org/2000/svg" 
                     viewBox="0 0 122.88 122.88" xml:space="preserve" fill="currentColor">
                    <g>
                        <path d="M84.93,4.66C77.69,1.66,69.75,0,61.44,0C44.48,0,29.11,6.88,18,18C12.34,23.65,7.77,30.42,4.66,37.95 
                        C1.66,45.19,0,53.13,0,61.44c0,16.96,6.88,32.33,18,43.44c5.66,5.66,12.43,10.22,19.95,13.34c7.24,3,15.18,4.66,23.49,4.66 
                        c8.31,0,16.25-1.66,23.49-4.66c7.53-3.12,14.29-7.68,19.95-13.34c5.66-5.66,10.22-12.43,13.34-19.95c3-7.24,4.66-15.18,4.66-23.49 
                        c0-8.31-1.66-16.25-4.66-23.49c-3.12-7.53-7.68-14.29-13.34-19.95C99.22,12.34,92.46,7.77,84.93,4.66L84.93,4.66z 
                        M65.85,47.13c2.48-2.52,2.45-6.58-0.08-9.05s-6.58-2.45-9.05,0.08L38.05,57.13c-2.45,2.5-2.45,6.49,0,8.98l18.32,18.62 
                        c2.48,2.52,6.53,2.55,9.05,0.08c2.52-2.48,2.55-6.53,0.08-9.05l-7.73-7.85l22-0.13c3.54-0.03,6.38-2.92,6.35-6.46 
                        c-0.03-3.54-2.92-6.38-6.46-6.35l-21.63,0.13L65.85,47.13L65.85,47.13z M80.02,16.55c5.93,2.46,11.28,6.07,15.76,10.55 
                        c4.48,4.48,8.09,9.83,10.55,15.76c2.37,5.71,3.67,11.99,3.67,18.58c0,6.59-1.31,12.86-3.67,18.58 
                        c-2.46,5.93-6.07,11.28-10.55,15.76c-4.48,4.48-9.83,8.09-15.76,10.55C74.3,108.69,68.03,110,61.44,110s-12.86-1.31-18.58-3.67 
                        c-5.93-2.46-11.28-6.07-15.76-10.55c-4.48-4.48-8.09-9.82-10.55-15.76c-2.37-5.71-3.67-11.99-3.67-18.58 
                        c0-6.59,1.31-12.86,3.67-18.58c2.46-5.93,6.07-11.28,10.55-15.76c4.48-4.48,9.83-8.09,15.76-10.55c5.71-2.37,11.99-3.67,18.58-3.67 
                        C68.03,12.88,74.3,14.19,80.02,16.55L80.02,16.55z"/>
                    </g>
                </svg>
                Back
            </button>
            <h1 class="text-2xl font-bold">
                {{ $section ?? 'Projects' }} @if(isset($currentSection) && $section !== 'ROTC') - Section {{ $currentSection }} @endif
            </h1>
        </div>
    </div>

    <!-- Section Selection for LTS and CWTS (ROTC shows projects directly) -->
    @if(in_array($section, ['LTS', 'CWTS']))
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Sections:</h3>
        <div class="flex flex-wrap gap-2">
            @foreach (range('A', 'Z') as $letter)
                <a href="{{ route('projects.' . strtolower($section), $letter) }}" 
                   class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                   @if(($currentSection ?? 'A') === $letter)
                       bg-blue-600 text-white
                   @else
                       bg-gray-200 text-gray-700 hover:bg-gray-300
                   @endif">
                    {{ $letter }}
                </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @if(isset($projects) && $projects->isNotEmpty())
            <!-- Debug: Projects count: {{ $projects->count() }} -->
            @foreach($projects as $project)
                <div class="bg-white p-4 rounded-lg shadow-md text-center relative">
                    @if($project->Project_Status === 'draft')
                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded">Draft</span>
                    @endif
                    <h2 class="text-lg font-semibold">{{ $project->Project_Name }}</h2>
                    <div class="w-16 h-16 mx-auto my-4">
                        @if($project->Project_Logo)
                            <img src="{{ asset('storage/' . $project->Project_Logo) }}" alt="{{ $project->Project_Name }} Logo" class="w-full h-full object-contain">
                        @else
                            <div class="w-full h-full border-2 border-black rounded-full flex items-center justify-center">
                                <span class="text-xs text-gray-500">No Logo</span>
                            </div>
                        @endif
                    </div>
                    <p class="text-gray-600">{{ $project->Project_Team_Name }}</p>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-wrap justify-center gap-2 mt-4">
                        <a href="@if(($section ?? '') === 'My Projects') {{ route('my-projects.details', $project->Project_ID) }} @else {{ route('projects.show', $project->Project_ID) }} @endif" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors duration-200">
                          View Project
                        </a>
                        
                        @if(($section ?? '') === 'My Projects' && $project->Project_Status === 'draft')
                            <a href="{{ route('projects.edit', $project) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors duration-200">
                                Edit Project
                            </a>
                            <!-- Delete Button for Draft Projects -->
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition-colors duration-200">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty state message -->
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500 text-lg">No projects found @if($section !== 'ROTC') in this section @endif.</p>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add SweetAlert2 confirmation to delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('.delete-form');
            
            Swal.fire({
                title: 'Delete Draft Project?',
                text: "Are you sure you want to delete this draft project? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection