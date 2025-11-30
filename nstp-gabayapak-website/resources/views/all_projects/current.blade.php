@extends('layouts.app')

@section('title', 'All Current Projects')

@section('styles')
<style>
    /* Custom colors and background images */
    .light-pink-bg { background-color: #FFE4E1; }
    
    .rotc-bg {
        background-image: url('{{ asset('assets/NSTP_ROTC_IMAGE_3.jpg') }}');
        background-size: cover;
        background-position: center 35%;
        background-repeat: no-repeat;
    }
    
    .lts-bg {
        background-image: url('{{ asset('assets/NSTP_LTS_IMAGE_2.jpg') }}');
        background-size: cover;
        background-position: center 20%;
        background-repeat: no-repeat;
    }
    
    .cwts-bg {
        background-image: url('{{ asset('assets/NSTP_IMAGE_1.jpg') }}');
        background-size: cover;
        background-position: center 55%;
        background-repeat: no-repeat;
    }
    
    /* Overlay for better text readability */
    .project-overlay {
        background: rgba(0, 0, 0, 0.4);
        border-radius: 0.5rem;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .rotc-bg, .lts-bg, .cwts-bg {
            background-attachment: scroll;
            min-height: 120px;
        }
        
        .project-card {
            padding: 2rem 1rem !important;
            text-align: center !important;
        }
        
        .project-title {
            font-size: 1.5rem !important;
            text-align: center !important;
        }
    }
    
    @media (max-width: 480px) {
        .project-card {
            padding: 1.5rem 0.75rem !important;
            text-align: center !important;
        }
        
        .project-title {
            font-size: 1.25rem !important;
            text-align: center !important;
        }
    }
</style>
@endsection

@section('content')
<!-- Main Content -->
<main id="content" class="flex-1 p-6 transition-all duration-300 bg-white min-h-screen current-projects-container">
    <!-- Page Header -->
    <div class="mb-6 md:mb-8 current-projects-header">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl md:text-4xl font-bold text-black">All Current Projects</h1>
            @if(Auth::user()->isStaff())
                <div>
                    <a href="{{ route('projects.allApproved') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg shadow transition">All Approved Projects</a>
                </div>
            @endif
        </div>
        <p class="text-base md:text-lg text-gray-700">
            {{ $projectCount ?? 0 }} of {{ $projectCount ?? 0 }} projects organized
        </p>
    </div>

    <!-- Project Categories Container -->
    <div class="light-pink-bg p-6 rounded-lg">
        <div class="space-y-6">
            <h2 class="text-2xl font-semibold text-black mb-6">Recent Projects...</h2>
            
            <!-- ROTC Projects -->
            <div class="w-full">
                <a href="{{ route('projects.rotc') }}" class="block">
                    <div class="rotc-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
                        <div class="project-overlay absolute inset-0"></div>
                        <div class="relative z-10">
                            <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">ROTC Projects</h3>
                        </div>
                    </div>
                </a>
            </div>

            <!-- LTS Projects -->
            <div class="w-full">
                <a href="{{ route('projects.lts') }}" class="block">
                    <div class="lts-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
                        <div class="project-overlay absolute inset-0"></div>
                        <div class="relative z-10">
                            <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">LTS Projects</h3>
                        </div>
                    </div>
                </a>
            </div>

            <!-- CWTS Projects -->
            <div class="w-full">
                <a href="{{ route('projects.cwts') }}" class="block">
                    <div class="cwts-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
                        <div class="project-overlay absolute inset-0"></div>
                        <div class="relative z-10">
                            <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">CWTS Projects</h3>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</main>
@endsection