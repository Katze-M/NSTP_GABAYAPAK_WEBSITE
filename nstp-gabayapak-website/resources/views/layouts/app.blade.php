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
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @auth
            @include('components.sidebar')
        @endauth

        <!-- Page Content -->
        <main id="content" class="flex-1 @auth md:ml-64 @endauth p-4 md:p-6 transition-all duration-300 bg-white min-h-screen">
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#3085d6'
            });
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