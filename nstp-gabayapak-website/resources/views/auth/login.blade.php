<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gabayapak</title>
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
    
    <div class="absolute inset-0 bg-blue-900/80 -z-10"></div>

    <div class="bg-blue-900/95 p-8 rounded-2xl w-full max-w-md relative">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('assets/GabaYapak_Logo.png') }}" alt="GabaYapak Logo" class="h-16 w-16 object-contain">
            </div>
            <h1 class="text-3xl font-bold mb-2">NSTP GabaYapak</h1>
            <p class="text-gray-300">Sign in to your account</p>
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

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            
            <div>
                <label class="block mb-2 text-sm font-medium">Email Address</label>
                <input type="email" name="user_Email" value="{{ old('user_Email') }}" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your email">
            </div>
            
            <div>
                <label class="block mb-2 text-sm font-medium">Password</label>
                <input type="password" name="user_Password" required class="w-full px-4 py-3 rounded-lg bg-white text-black focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your password">
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-yellow-400 focus:ring-yellow-400 border-white/20 rounded bg-white/10">
                    <label for="remember" class="ml-2 block text-sm">Remember me</label>
                </div>
                
                <!-- <a href="#" class="text-sm font-medium hover:underline">Forgot password?</a> -->
            </div>
            
            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-3 rounded-lg transition-colors">Sign In</button>
        </form>
        
        <p class="mt-6 text-center">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-semibold hover:underline">Register here</a>
        </p>
    </div>

    <!-- Scroll to Top Button -->
    <button id="scrollToTop" title="Go to top">↑</button>

    <script>
        // Scroll to top button functionality
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                document.getElementById("scrollToTop").style.display = "block";
            } else {
                document.getElementById("scrollToTop").style.display = "none";
            }
        });

        document.getElementById("scrollToTop").addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>