{{-- Shared edit form for both draft and submitted project editing --}}
<form id="projectForm" action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
    @csrf
    @method('PUT')
    
    {{-- The rest of the form is copied from edit.blade.php, but you can use $isDraft to conditionally render fields/buttons --}}
    @include('projects.partials.edit-form-body', ['project' => $project, 'isDraft' => $isDraft])
</form>
