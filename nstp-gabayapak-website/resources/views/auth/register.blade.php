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

        .password-field { position: relative; }
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #4b5563;
            padding: 0.25rem;
        }
        .password-toggle:hover { color: #111827; }

        .pw-requirement { color: #d1d5db; transition: color 150ms ease; }
        .pw-requirement.met { color: #4ade80; }
        .pw-requirement svg { display: inline; width: 1rem; height: 1rem; vertical-align: -2px; margin-right: 0.25rem; }
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
            <h1 class="text-3xl font-bold mb-2">Staff Registration</h1>
            <p class="text-gray-300">Student accounts are created by staff, not through self-registration.</p>
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

        <form id="staffForm" class="space-y-6" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

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
                        @php
                            $currentSelection = old('user_role', $prefill['user_role'] ?? '');
                            $disabled = false;
                            $title = '';
                            if(isset($sacsiCount) && $role === 'SACSI Director' && $sacsiCount > 0 && $currentSelection !== $role) {
                                $disabled = true;
                                $title = 'A SACSI Director account already exists';
                            }
                            if(isset($programOfficerCount) && $role === 'NSTP Program Officer' && $programOfficerCount > 0 && $currentSelection !== $role) {
                                $disabled = true;
                                $title = 'An NSTP Program Officer account already exists';
                            }
                            if(isset($coordinatorCount) && $role === 'NSTP Coordinator' && $coordinatorCount >= 3 && $currentSelection !== $role) {
                                $disabled = true;
                                $title = 'There are already 3 NSTP Coordinator accounts';
                            }
                        @endphp
                        <option value="{{ $role }}" {{ ($currentSelection == $role) ? 'selected' : '' }} @if($disabled) disabled title="{{ $title }}" @endif>
                            {{ $role }} @if($disabled) (unavailable) @endif
                        </option>
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
                <div class="password-field">
                    <input type="password" name="user_Password" id="passwordInput" required minlength="8" class="w-full px-4 py-3 pr-11 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <button type="button" class="password-toggle" data-target="passwordInput" aria-label="Show password">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.6 21.6 0 015.17-6.17M9.9 4.24A10.4 10.4 0 0112 4c7 0 11 8 11 8a21.6 21.6 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                <ul class="mt-2 text-xs space-y-1">
                    <li class="pw-requirement" data-rule="length"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L8.3 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>At least 8 characters</li>
                    <li class="pw-requirement" data-rule="case"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L8.3 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>Upper and lowercase letters</li>
                    <li class="pw-requirement" data-rule="number"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L8.3 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>At least one number</li>
                    <li class="pw-requirement" data-rule="symbol"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 111.4-1.4L8.3 11.6l6.3-6.3a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>At least one symbol (e.g. ! @ # $)</li>
                </ul>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium">Confirm Password <span class="text-red-500">*</span></label>
                <div class="password-field">
                    <input type="password" name="user_Password_confirmation" id="passwordConfirmInput" required class="w-full px-4 py-3 pr-11 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <button type="button" class="password-toggle" data-target="passwordConfirmInput" aria-label="Show password">
                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.6 21.6 0 015.17-6.17M9.9 4.24A10.4 10.4 0 0112 4c7 0 11 8 11 8a21.6 21.6 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
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
        // Password show/hide toggles
        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById(btn.dataset.target);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                btn.querySelector('.eye-open').classList.toggle('hidden', isHidden);
                btn.querySelector('.eye-closed').classList.toggle('hidden', !isHidden);
                btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            });
        });

        // Live password requirement checklist
        const passwordInput = document.getElementById('passwordInput');
        const requirements = {
            length: v => v.length >= 8,
            case: v => /[a-z]/.test(v) && /[A-Z]/.test(v),
            number: v => /[0-9]/.test(v),
            symbol: v => /[^a-zA-Z0-9]/.test(v),
        };
        passwordInput.addEventListener('input', function () {
            const value = passwordInput.value;
            Object.keys(requirements).forEach(function (rule) {
                document.querySelector('.pw-requirement[data-rule="' + rule + '"]')
                    .classList.toggle('met', requirements[rule](value));
            });
        });

        // Scroll to top button functionality
        const scrollToTopBtn = document.getElementById('scrollToTop');
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
    </script>
</body>
</html>
