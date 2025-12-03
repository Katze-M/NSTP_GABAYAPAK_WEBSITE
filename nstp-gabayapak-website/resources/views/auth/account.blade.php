@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="mb-6 md:mb-8">
    <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 mb-2">My Account</h1>
    <p class="text-gray-600 text-sm md:text-base">Manage your personal information and NSTP details</p>
</div>

<!-- Account Content -->
<div class="space-y-6 md:space-y-8">
    
    <!-- Profile Overview Card -->
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-red-500 to-red-600 p-6 md:p-8">
            <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
                <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white bg-white flex items-center justify-center overflow-hidden">
                    @if(Auth::user()->staff && Auth::user()->staff->staff_formal_picture)
                        <img src="{{ asset('storage/' . Auth::user()->staff->staff_formal_picture) }}" alt="{{ Auth::user()->user_Name }}" class="w-full h-full object-cover">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.5" class="w-12 h-12 md:w-14 md:h-14">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21a7.5 7.5 0 0115 0v.75H4.5V21z" />
                        </svg>
                    @endif
                </div>
                <div class="text-center md:text-left text-white">
                    <h2 class="text-xl md:text-2xl font-bold">{{ Auth::user()->user_Name }}</h2>
                    @if(Auth::user()->student)
                        <p class="text-red-100 text-sm md:text-base">{{ Auth::user()->user_Email }}</p>
                        <p class="text-red-100 text-sm">{{ Auth::user()->student->student_course ?? 'N/A' }}</p>
                    @elseif(Auth::user()->staff)
                        <p class="text-red-100 text-sm md:text-base">{{ Auth::user()->user_Email }}</p>
                        <p class="text-red-100 text-sm">{{ Auth::user()->user_role }}</p>
                    @endif
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Personal Information -->
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Personal Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @php
                        $editableRoles = ['SACSI Director', 'NSTP Coordinator', 'NSTP Program Officer'];
                        $user = Auth::user();
                    @endphp

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        @if($user->isStaff() && in_array($user->user_role, $editableRoles))
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-gray-800 font-medium">{{ $user->user_Name }}</p>
                            </div>
                        @else
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-gray-800">{{ $user->user_Name }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="p-3 bg-gray-50 rounded-lg border">
                            <p class="text-gray-800">{{ Auth::user()->user_Email }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @if(Auth::user()->student)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                            <div class="p-3 bg-gray-50 rounded-lg border">
                                <p class="text-gray-800">{{ Auth::user()->student->student_contact_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Academic Information -->
    @if(Auth::user()->student)
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Academic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Course</label>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-gray-800">{{ Auth::user()->student->student_course ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Year Level</label>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-gray-800">{{ Auth::user()->student->student_year ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NSTP Section</label>
                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-gray-800">{{ Auth::user()->student->student_section ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- NSTP Information -->
    @if(Auth::user()->student)
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                NSTP Information
            </h3>
            
            <div class="max-w-md">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">NSTP Component</label>
                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-gray-800 font-medium">{{ Auth::user()->student->student_component ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- SACSI Information (Staff only) -->
    @if(Auth::user()->staff)
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.66 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                SACSI Information
            </h3>

            <div class="max-w-md">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Staff Position</label>
                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-gray-800 font-medium">{{ Auth::user()->user_role ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Account Actions -->
    <section class="bg-white rounded-2xl shadow-subtle overflow-hidden border border-gray-100">
        <div class="p-6 md:p-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Account Actions
            </h3>
            
            <div class="max-w-sm flex items-stretch gap-3 account-actions">
                <div class="flex-1">
                    <button type="button" id="changePasswordBtn" class="w-full flex items-center justify-center gap-3 p-4 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 font-medium whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Change Password
                    </button>
                </div>
                @if(Auth::user()->isStaff() && in_array(Auth::user()->user_role, ['SACSI Director','NSTP Coordinator','NSTP Program Officer']))
                    <div class="flex-1">
                        <button type="button" id="openEditInfoBtn" class="w-full flex items-center justify-center gap-3 p-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 font-medium whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M11 9h6M11 13h6M5 5h.01M5 9h.01M5 13h.01M5 17h14" />
                            </svg>
                            Edit Information
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal container -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Change Password
                        </h3>
                        
                        <!-- Display validation errors -->
                        @if ($errors->has('current_password') || $errors->has('new_password'))
                            <div class="mt-4">
                                <div class="font-medium text-red-600">Whoops! Something went wrong.</div>
                                <ul class="mt-3 text-sm text-red-600 list-disc list-inside">
                                    @if ($errors->has('current_password'))
                                        @foreach ($errors->get('current_password') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    @endif
                                    @if ($errors->has('new_password'))
                                        @foreach ($errors->get('new_password') as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Display success message -->
                        @if (session('status'))
                            <div class="mt-4 hidden">
                                <div class="font-medium text-green-600">
                                    {{ session('status') }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <form method="POST" action="{{ route('account.password') }}" id="changePasswordForm">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                    <input type="password" name="current_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" name="new_password" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Update Password
                                    </button>
                                    <button type="button" id="cancelModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Information Modal -->
<div id="editInfoModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="edit-modal-title">Edit Information</h3>

                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div class="mt-4">
                                <div class="font-medium text-red-600">Whoops! Something went wrong.</div>
                                <ul class="mt-3 text-sm text-red-600 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-4">
                            <form method="POST" action="{{ route('account.update') }}" id="editInfoForm" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="user_Name" value="{{ old('user_Name', Auth::user()->user_Name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Formal Picture (Official ID photo)</label>
                                    <input type="file" name="staff_formal_picture" accept="image/*" class="w-full">
                                    @if(Auth::user()->staff && Auth::user()->staff->staff_formal_picture)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . Auth::user()->staff->staff_formal_picture) }}" alt="Formal Picture" class="w-24 h-24 object-cover rounded">
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Update Information</button>
                                    <button type="button" id="cancelEditModalBtn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('status'))
    <div id="globalStatus" class="hidden">{{ session('status') }}</div>
@endif

<style>
    /* Desktop/large screens keep horizontal layout by default. */
    /* On small screens (mobile 360x600) stack the buttons vertically. */
    @media (max-width: 600px) {
        .account-actions {
            flex-direction: column !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Change Password modal elements
        const passwordModal = document.getElementById('changePasswordModal');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const cancelPasswordBtn = document.getElementById('cancelModalBtn');

        // Edit Info modal elements
        const editModal = document.getElementById('editInfoModal');
        const openEditInfoBtn = document.getElementById('openEditInfoBtn');
        const cancelEditModalBtn = document.getElementById('cancelEditModalBtn');

        // Open password modal
        if (changePasswordBtn && passwordModal) {
            changePasswordBtn.addEventListener('click', function() {
                passwordModal.classList.remove('hidden');
            });
        }

        // Close password modal
        if (cancelPasswordBtn && passwordModal) {
            cancelPasswordBtn.addEventListener('click', function() {
                passwordModal.classList.add('hidden');
            });
        }

        // Open edit info modal
        if (openEditInfoBtn && editModal) {
            openEditInfoBtn.addEventListener('click', function() {
                editModal.classList.remove('hidden');
            });
        }

        // Close edit info modal
        if (cancelEditModalBtn && editModal) {
            cancelEditModalBtn.addEventListener('click', function() {
                editModal.classList.add('hidden');
            });
        }

        // Hide modals when clicking outside of them
        [passwordModal, editModal].forEach(function(m) {
            if (m) {
                m.addEventListener('click', function(event) {
                    if (event.target === m) {
                        m.classList.add('hidden');
                    }
                });
            }
        });

        // Hide modals when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (passwordModal && !passwordModal.classList.contains('hidden')) passwordModal.classList.add('hidden');
                if (editModal && !editModal.classList.contains('hidden')) editModal.classList.add('hidden');
            }
        });

        // Show SweetAlert2 confirmation if any status message exists
        const globalStatus = document.getElementById('globalStatus');
        if (globalStatus && globalStatus.textContent.trim() !== '') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: globalStatus.textContent.trim(),
                confirmButtonColor: '#3085d6'
            });
        }
    });
</script>
@endsection