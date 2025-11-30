@extends('layouts.app')

@section('title', 'Registration Status')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-8">
    @php
        $stateClass = '';
        $displayMessage = $message ?? '';
        if(isset($message)) {
            $lower = strtolower($message);
            if(str_contains($lower,'approved')) {
                $stateClass = 'bg-green-50 border-green-300 text-green-800';
            } elseif(str_contains($lower,'reject')) {
                $stateClass = 'bg-red-50 border-red-300 text-red-800';
                // Replace "Please register again" with a clickable link
                $displayMessage = preg_replace(
                    '/Please register again\.?/i',
                    '<a href="' . route('register') . '" class="text-red-700 underline hover:text-red-900 font-semibold">Please register again</a>.',
                    $message
                );
            } elseif(str_contains($lower,'review')) {
                $stateClass = 'bg-yellow-50 border-yellow-300 text-yellow-800';
            } else {
                $stateClass = 'bg-blue-50 border-blue-300 text-blue-800';
            }
        }
    @endphp
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
            <img src="{{ asset('assets/GabaYapak_Logo.png') }}" alt="GABAYAPAK Logo" class="w-28 mx-auto mb-6 drop-shadow-md">
            <h1 class="text-center text-2xl font-semibold tracking-wide text-gray-800 mb-6">Registration Status</h1>


            @if(isset($message))
                <div class="mb-6 px-4 py-3 rounded-lg border {{ $stateClass }} text-sm font-medium shadow-inner" role="status" aria-live="polite">{!! $displayMessage !!}
                    @if(isset($remarks) && $status === 'rejected' && $remarks)
                        <div class="mt-3 text-red-700 text-base font-normal bg-red-50 border border-red-200 rounded p-3">
                            <strong>Rejection Remarks:</strong><br>
                            <span style="white-space:pre-line">{{ $remarks }}</span>
                        </div>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('registration.status.post') }}" class="space-y-4">
                @csrf
                <label class="block text-sm font-medium text-gray-700" for="user_Email">Enter your email</label>
                <input id="user_Email" type="email" name="user_Email" required class="w-full px-3 py-2 rounded-lg bg-gray-50 border border-gray-300 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition" placeholder="you@adzu.edu.ph or you@gmail.com" value="{{ request('user_Email', old('user_Email')) }}">
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-blue-800 hover:bg-blue-900 text-white font-semibold tracking-wide shadow-md shadow-blue-900/30 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent">Check Status</button>
            </form>

            <div class="mt-6 text-sm leading-relaxed text-gray-600 italic">
                <p>If your registration is <span class="text-green-600 font-semibold">approved</span>, you will see: "Your account registration has been approved!"</p>
                <p class="mt-1">If it is still <span class="text-yellow-600 font-semibold">under review</span>, you will be informed it is currently under review.</p>
                <p class="mt-1">If it was <span class="text-red-600 font-semibold">rejected</span>, please <strong> re-register using the same email and update your information </strong>.</p>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-800 hover:text-blue-900 underline font-medium">← Back to Login</a>
            </div>
    </div>
</div>
@endsection
