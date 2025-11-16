@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
                <p class="mb-4">Welcome, {{ Auth::user()->user_Name }}!</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Your Profile</h2>
                        <p><strong>Name:</strong> {{ Auth::user()->user_Name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->user_Email }}</p>
                        <p><strong>Role:</strong> {{ Auth::user()->user_role }}</p>
                        <p><strong>Type:</strong> {{ Auth::user()->user_Type }}</p>
                        <a href="{{ route('account.show') }}" class="mt-3 inline-block bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">View Profile</a>
                    </div>
                    
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Quick Actions</h2>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-blue-600 hover:underline">View Projects</a></li>
                            <li><a href="#" class="text-blue-600 hover:underline">Submit Report</a></li>
                            <li><a href="#" class="text-blue-600 hover:underline">Check Announcements</a></li>
                        </ul>
                    </div>
                    
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">Recent Activity</h2>
                        <ul class="space-y-2">
                            <li class="text-sm">No recent activity</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection