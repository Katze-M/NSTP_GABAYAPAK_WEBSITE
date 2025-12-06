@props(['active'])


<aside id="sidebar" class="w-64 bg-[#EF3333] text-white shadow-lg h-screen fixed flex flex-col transition-all duration-300 z-50 -translate-x-full md:translate-x-0">
    <!-- Collapse button -->
    <div class="p-4 flex items-center justify-end border-b border-red-400">
        <button id="collapseBtn" class="p-2 rounded hover:bg-red-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>


    <!-- Sidebar header -->
    <div class="px-4 py-3 flex items-center gap-2 border-b border-red-400 bg-white">
        <div class="w-8 h-8 flex items-center justify-center">
            <img src="{{ asset('assets/GabaYapak_Logo.png') }}" alt="Logo" class="w-full h-full object-contain rounded" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center text-red-500 font-bold text-xs" style="display:none;">L</div>
        </div>
        <span class="font-bold text-lg sidebar-text text-black">NSTP GabaYapak</span>
    </div>


    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">

        @if(Auth::user()->isStaff())
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z"/>
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>
        @endif


        <!-- For Students: Current Projects only (no dropdown) -->
        @if(Auth::user()->isStudent())
        <a href="{{ route('projects.current') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="projects.current">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h5l2 2h11v10H3V7z"/>
            </svg>
            <span class="sidebar-text">Current Projects</span>
        </a>
        @else
        <!-- For Staff: All Projects dropdown -->
        <div class="all-projects-dropdown">
            <div class="flex items-center gap-3 p-2 rounded hover:bg-red-500 cursor-pointer all-projects-toggle" data-parent="all_projects.php">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h5l2 2h11v10H3V7z"/>
                </svg>
                <span class="sidebar-text">All Projects</span>
                <svg class="h-4 w-4 ml-auto transition-transform duration-200 dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="pl-8 mt-2 space-y-2 hidden dropdown-menu">
                <a href="{{ route('projects.current') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500 dropdown-item" data-route="projects.current" data-parent="all_projects.php">
                    <span class="sidebar-text">Current Projects</span>
                </a>
                <a href="{{ route('projects.pending') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500 dropdown-item" data-route="projects.pending" data-parent="all_projects.php">
                    <span class="sidebar-text">Pending Projects</span>
                </a>
                <a href="{{ route('projects.archived') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500 dropdown-item" data-route="projects.archived" data-parent="all_projects.php">
                    <span class="sidebar-text">Archived Projects</span>
                </a>
            </div>
        </div>
        @endif


        @if(Auth::user()->isStudent())
        <a href="{{ route('projects.create') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="projects.create">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4-4m0 0l-4 4m4-4v12"/>
            </svg>
            <span class="sidebar-text">Upload Project</span>
        </a>
        @endif


        @if(Auth::user()->isStaff())
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="reports">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="3" ry="3" />
                <line x1="3" y1="9" x2="21" y2="9" />
                <line x1="9" y1="3" x2="9" y2="21" />
            </svg>
            <span class="sidebar-text">Reports</span>
        </a>
        @endif


        @if(Auth::user()->isStudent())
        <a href="{{ route('projects.my') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="projects.my">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6m-7 4h8m-9 4h10m-11 4h12M9 2h6a2 2 0 012 2h2a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2h2a2 2 0 012-2z"/>
            </svg>
            <span class="sidebar-text">My Projects</span>
        </a>
        @endif


        <!-- Homepage is converted to About page and is placed above the Profile page nav -->
        <a href="{{ route('about') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="about">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9.75l9-7.5 9 7.5M4.5 10.5v9.75A1.5 1.5 0 006 21.75h12a1.5 1.5 0 001.5-1.5V10.5"/>
            </svg>
            <span class="sidebar-text">About</span>
        </a>

        <a href="{{ route('account.show') }}" class="flex items-center gap-3 p-2 rounded hover:bg-red-500" data-route="account.show">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="sidebar-text">Profile</span>
        </a>
    </nav>


    <!-- Logout -->
    <div class="p-4 border-t border-red-400">
        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="button" onclick="confirmLogout()" class="flex items-center gap-3 p-2 rounded hover:bg-red-500 text-white w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</aside>


<script>
function confirmLogout() {
    Swal.fire({
        title: 'Logout?',
        text: "Are you sure you want to logout?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, logout',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logoutForm').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const collapseBtn = document.getElementById('collapseBtn');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebarText = document.querySelectorAll('.sidebar-text');
    const dropdownMenu = document.querySelector('.dropdown-menu');
    const allProjectsToggle = document.querySelector('.all-projects-toggle');
    const dropdownArrow = document.querySelector('.dropdown-arrow');
    const currentPath = window.location.pathname;


    // Add active class based on current route
    const navLinks = document.querySelectorAll('nav a[data-route]');
    navLinks.forEach(link => {
        const route = link.getAttribute('data-route');
        if (route) {
            // Convert route name to path pattern
            let routePath = '';
            switch(route) {
                case 'about':
                    routePath = '/';
                    break;
                case 'dashboard':
                    routePath = '/dashboard';
                    break;
                case 'projects.current':
                    routePath = '/projects/current';
                    break;
                case 'projects.pending':
                    routePath = '/projects/pending';
                    break;
                case 'projects.archived':
                    routePath = '/projects/archived';
                    break;
                case 'projects.create':
                    routePath = '/projects/create';
                    break;
                case 'projects.my':
                    routePath = '/my-projects';
                    break;
                case 'account.show':
                    routePath = '/account';
                    break;
                default:
                    routePath = '/' + route.replace(/\./g, '/');
            }
           
            // Check if current path matches route path
            if (currentPath === routePath ||
                (routePath === '/' && currentPath === '/') ||
                (routePath !== '/' && currentPath.startsWith(routePath))) {
                link.classList.add('active-link');
            }
        }
    });


    // Collapse sidebar (desktop)
    const collapseSidebar = () => {
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-20');
        sidebarText.forEach(text => text.classList.add('hidden'));
        if (content) {
            content.classList.remove('ml-64');
            content.classList.add('ml-20');
            content.style.marginLeft = '5rem';
        }
        if (dropdownArrow) {
            dropdownArrow.classList.add('hidden');
        }
        if (dropdownMenu) {
            dropdownMenu.classList.add('hidden');
        }
    };


    // Expand sidebar (desktop)
    const expandSidebar = () => {
        sidebar.classList.remove('w-20');
        sidebar.classList.add('w-64');
        sidebarText.forEach(text => text.classList.remove('hidden'));
        if (content) {
            content.classList.remove('ml-20');
            content.classList.add('ml-64');
            content.style.marginLeft = '16rem';
        }
        if (dropdownArrow) {
            dropdownArrow.classList.remove('hidden');
        }
    };


    // Mobile toggle
    const toggleMobileSidebar = () => {
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    };


    // Collapse button click
    if (collapseBtn) {
        collapseBtn.addEventListener('click', () => {
            if (window.innerWidth >= 768) {
                // Desktop collapse/expand
                if (sidebar.classList.contains('w-64')) {
                    collapseSidebar();
                } else {
                    expandSidebar();
                }
            } else {
                // Mobile -> toggle sidebar
                toggleMobileSidebar();
            }
        });
    }


    // Dropdown toggle
    if (allProjectsToggle && dropdownMenu) {
        allProjectsToggle.addEventListener('click', () => {
            if (window.innerWidth >= 768 && sidebar.classList.contains('w-20')) {
                expandSidebar();
            }
            dropdownMenu.classList.toggle('hidden');
            if (dropdownArrow) {
                dropdownArrow.classList.toggle('rotate');
            }
           
            // Check if current route is one of the dropdown items and show dropdown if needed
            if (currentPath.includes('/projects/current') ||
                currentPath.includes('/projects/pending') ||
                currentPath.includes('/projects/archived')) {
                dropdownMenu.classList.remove('hidden');
                if (dropdownArrow) {
                    dropdownArrow.classList.add('rotate');
                }
            }
        });
    }


    // Check if current route is one of the dropdown items and show dropdown if needed
    if (dropdownMenu && (currentPath.includes('/projects/current') ||
        currentPath.includes('/projects/pending') ||
        currentPath.includes('/projects/archived'))) {
        dropdownMenu.classList.remove('hidden');
        if (dropdownArrow) {
            dropdownArrow.classList.add('rotate');
        }
    }


    // Initial layout
    const setInitialLayout = () => {
        if (window.innerWidth >= 768) {
            // Desktop: ensure sidebar is visible and expanded
            sidebar.classList.remove('w-20', '-translate-x-full');
            sidebar.classList.add('w-64', 'translate-x-0');
            sidebarText.forEach(text => text.classList.remove('hidden'));
            if (content) {
                content.classList.remove('ml-20');
                content.classList.add('md:ml-64');
            }
        } else {
            // Mobile: ensure sidebar is hidden by default
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    };
    window.addEventListener('resize', setInitialLayout);
    setInitialLayout();
});
</script>
