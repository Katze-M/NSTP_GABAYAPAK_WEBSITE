<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_Email' => 'required|email',
            'user_Password' => 'required|string',
        ]);

        // Manually authenticate the user
        $user = User::where('user_Email', $request->user_Email)->first();

        if ($user && Hash::check($request->user_Password, $user->user_Password)) {
            Auth::login($user);
            
            // Redirect to homepage after successful login
            return redirect()->route('home');
        }

        // Authentication failed
        throw ValidationException::withMessages([
            'user_Email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        // Redirect to login page after logout
        return redirect()->route('login');
    }
}