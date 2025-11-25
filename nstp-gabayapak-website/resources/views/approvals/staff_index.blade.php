@extends('layouts.app')

@section('title', 'Staff Registrations Approval')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center mb-6 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">&larr; Back to Dashboard</a>
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Staff Registration Approvals</h1>

    {{-- session status removed; approvals use SweetAlert2 confirmations --}}

    <form method="GET" class="mb-6">
        <div class="flex gap-2">
            <input type="text" name="q" placeholder="Search name, email or role" value="{{ old('q', $q ?? request('q')) }}" class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">Search</button>
            <a href="{{ url()->current() }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">Reset</a>
        </div>
    </form>

    @if($pending->isEmpty())
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
            <p class="text-gray-600">No pending staff registrations.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                            <th class="px-6 py-4 text-left font-semibold">Picture</th>
                            <th class="px-6 py-4 text-left font-semibold">Name</th>
                            <th class="px-6 py-4 text-left font-semibold">Email</th>
                            <th class="px-6 py-4 text-left font-semibold">Role</th>
                            <th class="px-6 py-4 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($pending as $a)
                            @php 
                                $u = $a->user; 
                                $staff = $u->staff ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if($staff && $staff->staff_formal_picture)
                                        <img src="{{ asset('storage/' . $staff->staff_formal_picture) }}" 
                                             alt="{{ $u->user_Name }}" 
                                             class="w-16 h-16 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity shadow-sm"
                                             onclick="openImageModal('{{ asset('storage/' . $staff->staff_formal_picture) }}', '{{ $u->user_Name }}')">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $u->user_Name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $u->user_Email }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $u->user_role ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2 justify-center">
                                        <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium shadow-sm open-confirm-modal" data-id="{{ $a->id }}" data-type="approve" data-base-url="{{ url('/approvals/staff') }}">Approve</button>
                                        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-sm open-remark-button" data-id="{{ $a->id }}" data-base-url="{{ url('/approvals/staff') }}">Reject</button>
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
            <div>
                {{ $pending->links() }}
            </div>
        </div>

        <!-- Image Modal -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
            <div class="relative max-w-4xl max-h-full" onclick="event.stopPropagation()">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img id="modalImage" src="" alt="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
                <p id="modalCaption" class="text-white text-center mt-4 text-lg font-medium"></p>
            </div>
        </div>

        <!-- include SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Image Modal Functions
            function openImageModal(imageSrc, name) {
                document.getElementById('imageModal').classList.remove('hidden');
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('modalCaption').textContent = name;
                document.body.style.overflow = 'hidden';
            }

            function closeImageModal() {
                document.getElementById('imageModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            // Close modal on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                }
            });

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

