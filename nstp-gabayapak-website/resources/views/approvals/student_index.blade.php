@extends('layouts.app')

@section('title', 'Student Registrations Approval')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Student Registration Approvals</h1>

    {{-- session status removed; approvals use SweetAlert2 confirmations --}}

    <form method="GET" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="q" placeholder="Search name, email, component, section or course" value="{{ old('q', $q ?? request('q')) }}" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">Search</button>
            <a href="{{ url()->current() }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Reset</a>
        </div>
    </form>

    @if($pending->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <p class="text-gray-600">No pending student registrations.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse text-sm">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <th class="px-3 py-3 text-left font-semibold">Name</th>
                            <th class="px-3 py-3 text-left font-semibold">Email</th>
                            <th class="px-3 py-3 text-left font-semibold">Contact</th>
                            <th class="px-3 py-3 text-left font-semibold">Component</th>
                            <th class="px-3 py-3 text-left font-semibold">Section</th>
                            <th class="px-3 py-3 text-left font-semibold">Course</th>
                            <th class="px-3 py-3 text-left font-semibold">Year</th>
                            <th class="px-3 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pending as $a)
                            @php 
                                $u = $a->user; 
                                $stu = $u->student ?? null;
                                $component = $stu->student_component ?? '—';
                                $componentClass = '';
                                if (strtoupper($component) === 'ROTC') {
                                    $componentClass = 'bg-blue-100 text-blue-800';
                                } elseif (strtoupper($component) === 'LTS') {
                                    $componentClass = 'bg-yellow-100 text-yellow-800';
                                } elseif (strtoupper($component) === 'CWTS') {
                                    $componentClass = 'bg-red-100 text-red-800';
                                } else {
                                    $componentClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $u->user_Name }}</td>
                                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $u->user_Email }}</td>
                                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $stu->student_contact_number ?? '—' }}</td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $componentClass }}">
                                        {{ $component }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $stu->student_section ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $stu->student_course ?? '—' }}</td>
                                <td class="px-3 py-3 text-gray-600 text-center whitespace-nowrap">{{ $stu->student_year ?? '—' }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex gap-1 justify-center">
                                        <button type="button" class="px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-sm text-sm open-confirm-modal" data-id="{{ $a->id }}" data-type="approve" data-base-url="{{ url('/approvals/students') }}">Approve</button>
                                        <button type="button" class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-sm text-sm open-remark-button" data-id="{{ $a->id }}" data-base-url="{{ url('/approvals/students') }}">Reject</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-600">Showing {{ $pending->firstItem() ?? 0 }} to {{ $pending->lastItem() ?? 0 }} of {{ $pending->total() }} entries</div>
            <div class="flex items-center gap-4">
                {{ $pending->links() }}
                <a href="{{ route('approvals.students.history') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    View History
                </a>
            </div>
        </div>
        @endif

        <!-- include SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            (function(){
                // CSRF token for dynamic form submits
                var csrfToken = '{{ csrf_token() }}';

                // Approve button: confirm then POST
                document.querySelectorAll('.open-confirm-modal').forEach(function(btn){
                    btn.addEventListener('click', function(e){
                        var id = btn.getAttribute('data-id');
                        var type = btn.getAttribute('data-type');
                        var baseUrl = btn.getAttribute('data-base-url');

                                if(type === 'approve'){
                                    Swal.fire({
                                        title: 'Confirm Approval',
                                        text: 'Are you sure you want to approve this registration?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'Approve',
                                        cancelButtonText: 'Cancel',
                                    }).then(function(result){
                                        if(result.isConfirmed){
                                            // show immediate success feedback, then submit
                                            Swal.fire({
                                                title: 'Registration successfully approved',
                                                icon: 'success',
                                                showConfirmButton: false,
                                                timer: 900
                                            }).then(function(){
                                                var form = document.createElement('form');
                                                form.method = 'POST';
                                                form.action = baseUrl + '/' + id + '/approve';
                                                var token = document.createElement('input'); token.type='hidden'; token.name='_token'; token.value=csrfToken; form.appendChild(token);
                                                document.body.appendChild(form);
                                                form.submit();
                                            });
                                        }
                                    });
                                }
                    });
                });

                // Reject button: open SweetAlert2 textarea for remarks, require input, then POST
                document.querySelectorAll('.open-remark-button').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        var id = btn.getAttribute('data-id');
                        var baseUrl = btn.getAttribute('data-base-url');

                        // First open remarks textarea
                        Swal.fire({
                            title: 'Reject Registration',
                            input: 'textarea',
                            inputLabel: 'Remarks',
                            inputPlaceholder: 'Add remarks explaining the rejection...',
                            inputAttributes: { 'aria-label': 'Remarks' },
                            showCancelButton: true,
                            confirmButtonText: 'Next',
                            cancelButtonText: 'Cancel',
                            preConfirm: (value) => {
                                if(!value || !value.trim()){
                                    Swal.showValidationMessage('Please provide a remark to explain the rejection');
                                }
                                return value;
                            }
                        }).then(function(remarkResult){
                            if(!remarkResult.isConfirmed) return;

                            var remarkText = remarkResult.value || '';
                            function escapeHtml(str){
                                return String(str)
                                    .replace(/&/g, '&amp;')
                                    .replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/"/g, '&quot;')
                                    .replace(/'/g, '&#039;');
                            }

                            // Then show confirmation including the remark; note this cannot be undone
                            Swal.fire({
                                title: 'Confirm Rejection',
                                html: '<p>Are you sure you want to reject this registration? <strong>This action cannot be undone.</strong></p>' +
                                      '<div class="mt-3 text-left"><strong>Remark:</strong><div style="white-space:pre-wrap;margin-top:6px;">' + escapeHtml(remarkText) + '</div></div>',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes',
                                cancelButtonText: 'Cancel',
                                width: 600
                            }).then(function(confirmResult){
                                if(!confirmResult.isConfirmed) return;

                                // show rejection feedback then submit
                                Swal.fire({
                                    title: 'Registration rejected',
                                    icon: 'error',
                                    showConfirmButton: false,
                                    timer: 900
                                }).then(function(){
                                    var form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = baseUrl + '/' + id + '/reject';
                                    var token = document.createElement('input'); token.type='hidden'; token.name='_token'; token.value=csrfToken; form.appendChild(token);
                                    var remarkInput = document.createElement('input'); remarkInput.type='hidden'; remarkInput.name='remarks'; remarkInput.value = remarkText; form.appendChild(remarkInput);
                                    document.body.appendChild(form);
                                    form.submit();
                                });
                            });
                        });
                    });
                });
            })();
        </script>
        {{-- session status popups are handled inline on this page; no modal popup needed here --}}
</div>
@endsection
