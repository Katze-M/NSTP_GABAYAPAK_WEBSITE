@extends('layouts.app')

@section('title', 'Project Details - Military Training Program')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Back Button -->
                <div class="mb-6">
                    <a href="javascript:history.back()" 
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
                    </a>
                </div>

                <!-- Project Title with Logo -->
                <div class="bg-gradient-to-r from-gray-50 to-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <div class="flex items-center gap-4">
                        <!-- Team Logo -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl flex flex-col items-center justify-center shadow-lg border-4 border-white">
                                <span class="text-lg md:text-xl mb-1">🎖️</span>
                                <span class="text-xs md:text-sm font-bold text-white">A</span>
                            </div>
                        </div>
                        
                        <!-- Project Info -->
                        <div class="flex-1 min-w-0">
                            <h1 class="text-xl md:text-4xl font-bold text-black mb-1 md:mb-2">Military Training Program</h1>
                            <p class="text-sm md:text-lg text-gray-700 mb-1">by Team Alpha</p>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    ROTC
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Section A
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Current
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Ongoing
                                </span>
                            </div>
                        </div>
                        
                        <!-- Edit Status Button for Students -->
                        @if(Auth::user()->isStudent())
                        <div class="flex-shrink-0">
                            <button id="editStatusBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                                Edit Status
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Project Details Container -->
                <div class="bg-gradient-to-br from-pink-50 to-pink-100 p-4 md:p-6 rounded-2xl space-y-6 md:space-y-8 shadow-md">
                    <!-- TEAM INFORMATION -->
                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                👥
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-500 bg-clip-text text-transparent">Team Information</h2>
                                <p class="text-sm text-gray-600">Project team details and composition</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Project Name</label>
                                <p class="text-base md:text-lg text-gray-900 mt-1">Military Training Program</p>
                            </div>
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Team Name</label>
                                <p class="text-base md:text-lg text-gray-900 mt-1">Team Alpha</p>
                            </div>
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Component</label>
                                <p class="text-base md:text-lg text-gray-900 mt-1">ROTC</p>
                            </div>
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Section</label>
                                <p class="text-base md:text-lg text-gray-900 mt-1">Section A</p>
                            </div>
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Submitted Date</label>
                                <p class="text-base md:text-lg text-gray-900 mt-1">2024-01-15</p>
                            </div>
                            <div>
                                <label class="block text-base md:text-lg font-medium text-gray-700">Status</label>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-green-100 text-green-800">
                                        Current
                                    </span>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 text-blue-800">
                                        Ongoing
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MEMBER PROFILE -->
                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                👤
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-teal-600 to-green-500 bg-clip-text text-transparent">Member Profile</h2>
                                <p class="text-sm text-gray-600">Team members and their roles</p>
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left bg-white rounded-lg shadow-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-base font-semibold">Name</th>
                                        <th class="px-4 py-3 text-base font-semibold">Role/s</th>
                                        <th class="px-4 py-3 text-base font-semibold">School Email</th>
                                        <th class="px-4 py-3 text-base font-semibold">Contact Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t">
                                        <td class="px-4 py-3 text-base">John Smith</td>
                                        <td class="px-4 py-3 text-base">Cadet Commander</td>
                                        <td class="px-4 py-3 text-base">john.smith@student.edu.ph</td>
                                        <td class="px-4 py-3 text-base">09123456789</td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="px-4 py-3 text-base">Sarah Johnson</td>
                                        <td class="px-4 py-3 text-base">Training Officer</td>
                                        <td class="px-4 py-3 text-base">sarah.johnson@student.edu.ph</td>
                                        <td class="px-4 py-3 text-base">09123456790</td>
                                    </tr>
                                    <tr class="border-t">
                                        <td class="px-4 py-3 text-base">Mike Wilson</td>
                                        <td class="px-4 py-3 text-base">Logistics Officer</td>
                                        <td class="px-4 py-3 text-base">mike.wilson@student.edu.ph</td>
                                        <td class="px-4 py-3 text-base">09123456791</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-4">
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        JS
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">John Smith</h3>
                                        <p class="text-xs text-gray-500">Team Member #1</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-blue-500">🎯</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Role</label>
                                            <p class="text-sm font-medium">Cadet Commander</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-500">📧</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Email</label>
                                            <p class="text-sm">john.smith@student.edu.ph</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-purple-500">📱</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Contact</label>
                                            <p class="text-sm">09123456789</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        SJ
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Sarah Johnson</h3>
                                        <p class="text-xs text-gray-500">Team Member #2</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-blue-500">🎯</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Role</label>
                                            <p class="text-sm font-medium">Training Officer</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-500">📧</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Email</label>
                                            <p class="text-sm">sarah.johnson@student.edu.ph</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-purple-500">📱</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Contact</label>
                                            <p class="text-sm">09123456790</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        MW
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Mike Wilson</h3>
                                        <p class="text-xs text-gray-500">Team Member #3</p>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-blue-500">🎯</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Role</label>
                                            <p class="text-sm font-medium">Logistics Officer</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-green-500">📧</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Email</label>
                                            <p class="text-sm">mike.wilson@student.edu.ph</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-purple-500">📱</span>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600">Contact</label>
                                            <p class="text-sm">09123456791</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PROJECT DETAILS -->
                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                🎯
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-red-600 to-orange-500 bg-clip-text text-transparent">Project Details</h2>
                                <p class="text-sm text-gray-600">Comprehensive project information and objectives</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-red-500">⚠️</span>
                                    <label class="text-base md:text-lg font-semibold text-gray-800">Issues/Problems</label>
                                </div>
                                <p class="text-gray-700 text-sm md:text-base leading-relaxed">The community lacks proper military training and discipline programs for youth development. Many young people need structured leadership training and physical fitness programs.</p>
                            </div>
                            
                            <div class="bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-green-500">🎯</span>
                                    <label class="text-base md:text-lg font-semibold text-gray-800">Goals/Objectives</label>
                                </div>
                                <p class="text-gray-700 text-sm md:text-base leading-relaxed">To provide comprehensive military training and leadership development programs for community youth, promoting discipline, physical fitness, and civic responsibility.</p>
                            </div>
                            
                            <div class="bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-blue-500">👥</span>
                                    <label class="text-base md:text-lg font-semibold text-gray-800">Target Community</label>
                                </div>
                                <p class="text-gray-700 text-sm md:text-base leading-relaxed">Youth aged 16-25 years old in the local community, particularly those interested in military service or leadership development.</p>
                            </div>
                            
                            <div class="bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-purple-500">⚡</span>
                                    <label class="text-base md:text-lg font-semibold text-gray-800">Solutions/Activities</label>
                                </div>
                                <p class="text-gray-700 text-sm md:text-base leading-relaxed">Conduct regular military training sessions, leadership workshops, physical fitness programs, and community service activities.</p>
                            </div>
                            
                            <div class="lg:col-span-2 bg-white p-4 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-yellow-500">🌟</span>
                                    <label class="text-base md:text-lg font-semibold text-gray-800">Expected Outcomes</label>
                                </div>
                                <p class="text-gray-700 text-sm md:text-base leading-relaxed">Improved discipline among youth, enhanced leadership skills, better physical fitness, increased civic responsibility, and potential military service candidates.</p>
                            </div>
                        </div>
                    </div>

                    <!-- PROJECT ACTIVITIES -->
                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                📅
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent">Project Activities</h2>
                                <p class="text-sm text-gray-600">Timeline and progress tracking</p>
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left bg-white rounded-xl shadow-sm border border-gray-100">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Stage</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Activities</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Timeline</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Point Person</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Status</th>
                                        @if(Auth::user()->isStudent())
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Recruitment</td>
                                        <td class="px-4 py-3 text-base">Recruit participants and conduct orientation</td>
                                        <td class="px-4 py-3 text-base">Week 1-2</td>
                                        <td class="px-4 py-3 text-base">John Smith</td>
                                        <td class="px-4 py-3 text-base">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                                Completed
                                            </span>
                                        </td>
                                        @if(Auth::user()->isStudent())
                                        <td class="px-4 py-3 text-base">
                                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="1">
                                                Edit
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Training</td>
                                        <td class="px-4 py-3 text-base">Conduct military drills and leadership training</td>
                                        <td class="px-4 py-3 text-base">Week 3-8</td>
                                        <td class="px-4 py-3 text-base">Sarah Johnson</td>
                                        <td class="px-4 py-3 text-base">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                                Ongoing
                                            </span>
                                        </td>
                                        @if(Auth::user()->isStudent())
                                        <td class="px-4 py-3 text-base">
                                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="2">
                                                Edit
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Evaluation</td>
                                        <td class="px-4 py-3 text-base">Assess training effectiveness and graduation ceremony</td>
                                        <td class="px-4 py-3 text-base">Week 9-10</td>
                                        <td class="px-4 py-3 text-base">Mike Wilson</td>
                                        <td class="px-4 py-3 text-base">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                                Planned
                                            </span>
                                        </td>
                                        @if(Auth::user()->isStudent())
                                        <td class="px-4 py-3 text-base">
                                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="3">
                                                Edit
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="lg:hidden space-y-4">
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                            1
                                        </div>
                                        <h3 class="font-semibold text-gray-900">Recruitment</h3>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Completed
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Activities</label>
                                        <p class="text-sm text-gray-800">Recruit participants and conduct orientation</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Timeline</label>
                                            <p class="text-sm text-gray-800">Week 1-2</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Point Person</label>
                                            <p class="text-sm text-gray-800">John Smith</p>
                                        </div>
                                    </div>
                                    @if(Auth::user()->isStudent())
                                    <div class="pt-2">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="1">
                                            Edit & Upload Proof
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                            2
                                        </div>
                                        <h3 class="font-semibold text-gray-900">Training</h3>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Ongoing
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Activities</label>
                                        <p class="text-sm text-gray-800">Conduct military drills and leadership training</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Timeline</label>
                                            <p class="text-sm text-gray-800">Week 3-8</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Point Person</label>
                                            <p class="text-sm text-gray-800">Sarah Johnson</p>
                                        </div>
                                    </div>
                                    @if(Auth::user()->isStudent())
                                    <div class="pt-2">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="2">
                                            Edit & Upload Proof
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                                            3
                                        </div>
                                        <h3 class="font-semibold text-gray-900">Evaluation</h3>
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        Planned
                                    </span>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Activities</label>
                                        <p class="text-sm text-gray-800">Assess training effectiveness and graduation ceremony</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Timeline</label>
                                            <p class="text-sm text-gray-800">Week 9-10</p>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Point Person</label>
                                            <p class="text-sm text-gray-800">Mike Wilson</p>
                                        </div>
                                    </div>
                                    @if(Auth::user()->isStudent())
                                    <div class="pt-2">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm edit-activity-btn" data-activity="3">
                                            Edit & Upload Proof
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BUDGET -->
                    <div class="bg-gradient-to-br from-white to-gray-50 p-4 md:p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg">
                                💰
                            </div>
                            <div>
                                <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-500 bg-clip-text text-transparent">Budget Breakdown</h2>
                                <p class="text-sm text-gray-600">Financial planning and resource allocation</p>
                            </div>
                        </div>

                        <!-- Desktop Table View -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full text-left bg-white rounded-xl shadow-sm border border-gray-100">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Activity</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Resources</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Partners</th>
                                        <th class="px-4 py-3 text-base font-semibold text-gray-700">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Training Equipment</td>
                                        <td class="px-4 py-3 text-base">Military uniforms, training materials, equipment</td>
                                        <td class="px-4 py-3 text-base">Military supply store, ROTC unit</td>
                                        <td class="px-4 py-3 text-base font-bold text-green-600">₱ 8,000</td>
                                    </tr>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Transportation</td>
                                        <td class="px-4 py-3 text-base">Vehicle rental for field exercises</td>
                                        <td class="px-4 py-3 text-base">Local transport service</td>
                                        <td class="px-4 py-3 text-base font-bold text-green-600">₱ 3,000</td>
                                    </tr>
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-base font-medium">Meals and Refreshments</td>
                                        <td class="px-4 py-3 text-base">Food and drinks during training</td>
                                        <td class="px-4 py-3 text-base">Local catering service</td>
                                        <td class="px-4 py-3 text-base font-bold text-green-600">₱ 4,000</td>
                                    </tr>
                                    <!-- Total Row for Desktop -->
                                    <tr class="border-t-2 border-green-200 bg-gradient-to-r from-green-50 to-emerald-50">
                                        <td class="px-4 py-4 text-base font-bold text-gray-800" colspan="3">Total Project Budget</td>
                                        <td class="px-4 py-4 text-xl font-bold text-green-600">₱ 15,000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="lg:hidden space-y-4">
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3 gap-3">
                                    <h3 class="font-semibold text-gray-900 flex-1 min-w-0">Training Equipment</h3>
                                    <span class="text-lg font-bold text-green-600 whitespace-nowrap flex-shrink-0">₱ 8,000</span>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Resources Needed</label>
                                        <p class="text-sm text-gray-800">Military uniforms, training materials, equipment</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Partner Agencies</label>
                                        <p class="text-sm text-gray-800">Military supply store, ROTC unit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3 gap-3">
                                    <h3 class="font-semibold text-gray-900 flex-1 min-w-0">Transportation</h3>
                                    <span class="text-lg font-bold text-green-600 whitespace-nowrap flex-shrink-0">₱ 3,000</span>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Resources Needed</label>
                                        <p class="text-sm text-gray-800">Vehicle rental for field exercises</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Partner Agencies</label>
                                        <p class="text-sm text-gray-800">Local transport service</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-md border border-gray-100 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-3 gap-3">
                                    <h3 class="font-semibold text-gray-900 flex-1 min-w-0">Meals and Refreshments</h3>
                                    <span class="text-lg font-bold text-green-600 whitespace-nowrap flex-shrink-0">₱ 4,000</span>
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Resources Needed</label>
                                        <p class="text-sm text-gray-800">Food and drinks during training</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Partner Agencies</label>
                                        <p class="text-sm text-gray-800">Local catering service</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Total Budget Card -->
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-xl text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold">Total Project Budget</h3>
                                        <p class="text-sm opacity-90">Complete financial allocation</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-bold whitespace-nowrap">₱ 15,000</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
@if(Auth::user()->isStudent())
<div id="editActivityModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Activity & Upload Proof</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editActivityForm">
            <input type="hidden" id="activityId" name="activity_id">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Activity Status</label>
                <select id="activityStatus" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="Planned">Planned</option>
                    <option value="Ongoing">Ongoing</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Proof/Documentation</label>
                <input type="file" id="activityProof" name="proof" accept="image/*,.pdf,.doc,.docx" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Upload images, PDFs, or documents as proof of activity completion</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelEdit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Status Modal -->
<div id="editStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Edit Project Status</h3>
            <button id="closeStatusModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editStatusForm">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Status</label>
                <select id="projectStatus" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="Draft">Draft</option>
                    <option value="Submitted">Submitted</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelStatusEdit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
@if(Auth::user()->isStudent())
document.addEventListener('DOMContentLoaded', function() {
    // Edit activity buttons
    const editButtons = document.querySelectorAll('.edit-activity-btn');
    const modal = document.getElementById('editActivityModal');
    const closeModal = document.getElementById('closeModal');
    const cancelEdit = document.getElementById('cancelEdit');
    const editForm = document.getElementById('editActivityForm');
    
    // Edit status button
    const editStatusBtn = document.getElementById('editStatusBtn');
    const statusModal = document.getElementById('editStatusModal');
    const closeStatusModal = document.getElementById('closeStatusModal');
    const cancelStatusEdit = document.getElementById('cancelStatusEdit');
    const editStatusForm = document.getElementById('editStatusForm');
    
    // Open activity edit modal
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const activityId = this.getAttribute('data-activity');
            document.getElementById('activityId').value = activityId;
            modal.classList.remove('hidden');
        });
    });
    
    // Close activity modal
    function closeActivityModal() {
        modal.classList.add('hidden');
        editForm.reset();
    }
    
    closeModal.addEventListener('click', closeActivityModal);
    cancelEdit.addEventListener('click', closeActivityModal);
    
    // Close status modal
    function closeStatusModalFunc() {
        statusModal.classList.add('hidden');
        editStatusForm.reset();
    }
    
    closeStatusModal.addEventListener('click', closeStatusModalFunc);
    cancelStatusEdit.addEventListener('click', closeStatusModalFunc);
    
    // Open status edit modal
    if (editStatusBtn) {
        editStatusBtn.addEventListener('click', function() {
            statusModal.classList.remove('hidden');
        });
    }
    
    // Handle form submissions (you would implement actual form handling here)
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // In a real implementation, you would send the form data to the server
        alert('Activity updated successfully! In a real application, this would save to the database.');
        closeActivityModal();
    });
    
    editStatusForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // In a real implementation, you would send the form data to the server
        alert('Project status updated successfully! In a real application, this would save to the database.');
        closeStatusModalFunc();
    });
    
    // Close modals when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeActivityModal();
        }
        if (e.target === statusModal) {
            closeStatusModalFunc();
        }
    });
});
@endif
</script>
@endsection