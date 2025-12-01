<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fade { 
            0%{opacity:0;} 
            10%{opacity:1;} 
            30%{opacity:1;} 
            40%{opacity:0;} 
            100%{opacity:0;} 
        }
        
        .fade-bg img { 
            position:absolute; 
            top:0; left:0; 
            width:100%; 
            height:100%; 
            object-fit:cover; 
            opacity:0; 
            animation:fade 20s infinite; 
            animation-timing-function:ease-in-out; 
            animation-fill-mode:both; 
        }
        
        .fade-bg img:nth-child(1){animation-delay:0s;}
        .fade-bg img:nth-child(2){animation-delay:5s;}
        .fade-bg img:nth-child(3){animation-delay:10s;}
        .fade-bg img:nth-child(4){animation-delay:15s;}
        .fade-bg img:nth-child(5){animation-delay:20s;}
        
        #formContainer::-webkit-scrollbar{width:8px;}
        #formContainer::-webkit-scrollbar-track{background:transparent;}
        #formContainer::-webkit-scrollbar-thumb{background-color: rgba(255,255,255,0.5); border-radius:4px;}
        #formContainer{scrollbar-gutter:stable; padding-right:12px;}
        
        /* Scroll to top button */
        #scrollToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #f59e0b;
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            display: none;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        #scrollToTop:hover {
            background-color: #d97706;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative text-white p-4">
    <div class="fade-bg absolute inset-0 -z-20">
        <img src="{{ asset('assets/1000036076.jpg') }}" alt="bg1">
        <img src="{{ asset('assets/1000036077.jpg') }}" alt="bg2">
        <img src="{{ asset('assets/1000036078.jpg') }}" alt="bg3">
        <img src="{{ asset('assets/1000036079.jpg') }}" alt="bg4">
        <img src="{{ asset('assets/1000041348.jpg') }}" alt="bg5">
    </div>
    
    <div class="absolute inset-0 bg-white/75 -z-10"></div>

    <div id="formContainer" class="bg-blue-900/95 p-8 rounded-2xl w-full max-w-md relative">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2">Account Registration</h1>
            <p class="text-gray-300">Create your account</p>
        </div>

        <!-- Display validation errors -->
        @if ($errors->any())
            <div class="mb-6">
                <div class="font-medium text-red-400">Whoops! Something went wrong.</div>
                <ul class="mt-3 text-sm text-red-400 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Role Selection -->
        <div class="flex justify-center space-x-6 mb-6">
            <label class="flex items-center space-x-2 text-base">
                <input type="radio" name="role" value="student" class="accent-yellow-400" checked>
                <span>Student</span>
            </label>
            <label class="flex items-center space-x-2 text-base">
                <input type="radio" name="role" value="staff" class="accent-yellow-400">
                <span>Staff</span>
            </label>
        </div>

        <!-- Student Form -->
        <form id="studentForm" class="space-y-6" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_Type" value="student" id="studentRole">
            
            <div>
                <label class="block mb-2 text-sm font-medium">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="user_Name" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400" value="{{ old('user_Name', $prefill['user_Name'] ?? '') }}">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">ADZU Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="user_Email" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400" value="{{ old('user_Email', $prefill['user_Email'] ?? '') }}" placeholder="e.g., co230143@adzu.edu.ph">
                <p class="text-sm text-gray-300 mt-1">Must be a valid ADZU email address</p>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Contact Number <span class="text-red-500">*</span></label>
                <input type="tel" name="student_contact_number" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="e.g., 09123456789" pattern="[0-9]{11}" value="{{ old('student_contact_number', $prefill['student_contact_number'] ?? '') }}">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium">Course <span class="text-red-500">*</span></label>
                    <select name="student_course" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="" disabled selected>Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course }}" {{ (old('student_course', $prefill['student_course'] ?? '') == $course) ? 'selected' : '' }}>
                                {{ $course }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2 text-sm font-medium">Year <span class="text-red-500">*</span></label>
                    <select name="student_year" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="" disabled selected>Select Year</option>
                        @for($i=1;$i<=4;$i++)
                            <option value="{{ $i }}" {{ (old('student_year', $prefill['student_year'] ?? '') == $i) ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium">Section <span class="text-red-500">*</span></label>
                    <select name="student_section" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="" disabled selected>Select Section</option>
                        @foreach (range('A', 'Z') as $letter)
                            <option value="Section {{ $letter }}" {{ (old('student_section', $prefill['student_section'] ?? '') == "Section $letter") ? 'selected' : '' }}>
                                Section {{ $letter }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2 text-sm font-medium">Component <span class="text-red-500">*</span></label>
                    <select name="student_component" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                        <option value="" disabled selected>Select Component</option>
                        <option value="ROTC" {{ (old('student_component', $prefill['student_component'] ?? '') == 'ROTC') ? 'selected' : '' }}>ROTC</option>
                        <option value="LTS" {{ (old('student_component', $prefill['student_component'] ?? '') == 'LTS') ? 'selected' : '' }}>LTS</option>
                        <option value="CWTS" {{ (old('student_component', $prefill['student_component'] ?? '') == 'CWTS') ? 'selected' : '' }}>CWTS</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Password <span class="text-red-500">*</span></label>
                <input type="password" name="user_Password" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            
            <div>
                <label class="block mb-2 text-sm font-medium">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="user_Password_confirmation" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 rounded-lg transition-colors">Register as Student</button>
        </form>

        <!-- Staff Form -->
        <form id="staffForm" class="space-y-6 hidden" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_Type" value="staff" id="staffRole">

            <div>
                <label class="block mb-2 text-sm font-medium">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="user_Name" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400" value="{{ old('user_Name', $prefill['user_Name'] ?? '') }}">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="user_Email" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400" value="{{ old('user_Email', $prefill['user_Email'] ?? '') }}" placeholder="e.g., username@adzu.edu.ph or username@gmail.com">
                <p class="text-sm text-gray-300 mt-1">Must be a valid ADZU email or Gmail address</p>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Staff Position <span class="text-red-500">*</span></label>
                <select name="user_role" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <option value="" disabled selected>Select Position</option>
                    @foreach($roles as $role)
                        @if($role != 'Student')
                            <option value="{{ $role }}" {{ (old('user_role', $prefill['user_role'] ?? '') == $role) ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Formal Picture <span class="text-red-500">*</span></label>
                <input type="file" name="staff_formal_picture" {{ empty($prefill['user_role']) ? 'required' : '' }} class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <p class="text-sm text-gray-300 mt-1">Please upload a formal headshot picture</p>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Password <span class="text-red-500">*</span></label>
                <input type="password" name="user_Password" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            
            <div>
                <label class="block mb-2 text-sm font-medium">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="user_Password_confirmation" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 rounded-lg transition-colors">Register as Staff</button>
        </form>

        <p class="mt-6 text-center">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-semibold hover:underline">Sign in here</a>
        </p>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" title="Go to top">↑</button>

    <script>
        const studentForm = document.getElementById("studentForm");
        const staffForm = document.getElementById("staffForm");
        const studentRoleInput = document.getElementById("studentRole");
        const staffRoleInput = document.getElementById("staffRole");
        const formContainer = document.getElementById("formContainer");
        const scrollToTopBtn = document.getElementById("scrollToTop");

        // Role switching
        const roleRadios = document.querySelectorAll('input[name="role"]');
        roleRadios.forEach(radio => {
            radio.addEventListener('change', function(){
                if(this.value==='student'){
                    studentForm.classList.remove('hidden');
                    staffForm.classList.add('hidden');
                    studentRoleInput.value='student';
                    staffForm.reset();
                } else {
                    staffForm.classList.remove('hidden');
                    studentForm.classList.add('hidden');
                    staffRoleInput.value='staff';
                    studentForm.reset();
                }
            });
        });

        // Scroll to top button functionality
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.display = "block";
            } else {
                scrollToTopBtn.style.display = "none";
            }
        });

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Auto-scroll to top when form changes
        roleRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>