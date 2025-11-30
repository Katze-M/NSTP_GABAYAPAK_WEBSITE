<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title', 'NSTP GabaYapak')</title>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
   
    <!-- Custom Styles -->
    @yield('styles')
   
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Mobile menu button positioning */
        @media (max-width: 400px) {
            /* Full width content when sidebar is hidden */
            #content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            #mobileMenuBtn {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                padding: 0.6rem 1rem !important;
                border-radius: 0 !important;
                display: flex !important;
                justify-content: flex-start !important;
                align-items: center !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
                z-index: 1000 !important;
            }
            #mobileMenuBtn svg {
                width: 1.2rem !important;
                height: 1.2rem !important;
            }
            #content > div {
                padding-top: 3.5rem !important;
            }
            /* Adjust page headers to account for top button bar */
            .pending-header h1 {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @auth
            @include('components.sidebar')
        @endauth


        <!-- Page Content -->
        <main id="content" class="flex-1 @auth md:ml-64 @endauth transition-all duration-300 bg-white min-h-screen">
            <!-- Mobile Menu Button -->
            @auth
            <div class="md:hidden fixed top-4 left-4 z-40">
                <button id="mobileMenuBtn" class="p-2 bg-[#EF3333] text-white rounded-lg shadow-lg hover:bg-red-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            @endauth
           
            <div class="p-4 md:p-6">
                @yield('content')
            </div>
        </main>
    </div>


    <script>
        // Mobile menu button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
           
            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', () => {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                    }
                });
            }
        });
    </script>


    <!-- SweetAlert2 -->
    @if(session('success'))
        <script>
            // Prevent showing the same success message multiple times on back button navigation
            const successMessage = '{{ session('success') }}';
            const successKey = 'lastShownSuccess_' + btoa(successMessage).substring(0, 20);
            const lastShown = sessionStorage.getItem(successKey);
            const now = Date.now();
            
            // Only show if not shown in the last 5 seconds
            if (!lastShown || (now - parseInt(lastShown)) > 5000) {
                sessionStorage.setItem(successKey, now);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: successMessage,
                    confirmButtonColor: '#3085d6'
                });
            }
        </script>
    @endif


    @if(session('error'))
        <script>
            // Prevent showing the same error multiple times on back button navigation
            const errorMessage = '{{ session('error') }}';
            const errorKey = 'lastShownError_' + btoa(errorMessage).substring(0, 20);
            const lastShown = sessionStorage.getItem(errorKey);
            const now = Date.now();
            
            // Only show if not shown in the last 5 seconds
            if (!lastShown || (now - parseInt(lastShown)) > 5000) {
                sessionStorage.setItem(errorKey, now);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonColor: '#3085d6'
                });
            }
        </script>
    @endif


    @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Notice',
                text: '{{ session('warning') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif


    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error!',
                html: '<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif
   
    @yield('scripts')
</body>
</html>

