@extends('layouts.app')

@section('title', 'Staff Registrations Approval')

@section('content')
<div class="p-6">
    <a href="{{ route('dashboard') }}" class="inline-block mb-4 px-3 py-1 bg-gray-200 rounded">&larr; Back to Dashboard</a>
    <h1 class="text-2xl font-bold mb-4">Staff Registration Approvals</h1>

    {{-- session status removed; approvals use SweetAlert2 confirmations --}}

    <form method="GET" class="mb-4">
        <div class="flex gap-2">
            <input type="text" name="q" placeholder="Search name, email or role" value="{{ old('q', $q ?? request('q')) }}" class="border rounded px-2 py-1 w-full">
            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Search</button>
            <a href="{{ url()->current() }}" class="px-3 py-1 border rounded">Reset</a>
        </div>
    </form>

    @if($pending->isEmpty())
        <p>No pending staff registrations.</p>
    @else
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Name</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Role</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $a)
                    @php $u = $a->user; @endphp
                    <tr class="border-t">
                        <td class="p-2">{{ $u->user_Name }}</td>
                        <td class="p-2">{{ $u->user_Email }}</td>
                        <td class="p-2">{{ $u->user_role ?? '—' }}</td>
                        <td class="p-2">
                            <button type="button" class="px-3 py-1 bg-green-600 text-white rounded open-confirm-modal" data-id="{{ $a->id }}" data-type="approve" data-base-url="{{ url('/approvals/staff') }}">Approve</button>

                            <button type="button" class="px-3 py-1 bg-red-600 text-white rounded open-remark-button" style="margin-left:8px;" data-id="{{ $a->id }}" data-base-url="{{ url('/approvals/staff') }}">Reject</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">Showing {{ $pending->firstItem() ?? 0 }} to {{ $pending->lastItem() ?? 0 }} of {{ $pending->total() }} entries</div>
            <div>
                {{ $pending->links() }}
            </div>
        </div>

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

                // Reject button: open remarks textarea first, then confirm, then submit
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
    @endif
</div>
@endsection

