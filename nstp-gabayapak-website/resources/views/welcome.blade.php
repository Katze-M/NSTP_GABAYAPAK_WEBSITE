@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to NSTP GabaYapak</h1>
        <p class="text-lg text-gray-600 mb-8">Project Management and Monitoring System</p>
        
        <div class="space-x-4">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                    Login
                </a>
                <a href="{{ route('register') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Register
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection