<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Approval;
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
            // SACSI Director is auto-approved and should always be able to log in
            if ($user->isStaff() && $user->isSACSIDirector()) {
                Auth::login($user);
                // SACSI Director is staff — send to dashboard
                return redirect()->route('dashboard');
            }

            // Check approval status for other users
            $approval = Approval::where('user_id', $user->user_id)->latest()->first();

            // Consider user approved if `approved` flag is true or latest approval is approved
            $isApproved = $user->approved || ($approval && $approval->status === 'approved');

            if ($isApproved) {
                Auth::login($user);
                // Approved staff will be redirected to dashboard; approved students will be redirected to upload project page
                if ($user->isStaff()) {
                    return redirect()->route('dashboard');
                }

                if ($user->isStudent()) {
                    return redirect()->route('projects.create');
                }

                //about page (as fallback)
                return redirect()->route('about');
            }

            //If approval is pending, inform user that their registration is under review
            if ($approval && $approval->status === 'pending') {
                throw ValidationException::withMessages([
                    'user_Email' => ['Your registration is currently under review.'],
                ]);
            }

            //If approval is rejected, send back with a link to re-register. Users must register using the same email
            if ($approval && $approval->status === 'rejected') {
                return redirect()->route('login')
                    ->with('rejected_email', $user->user_Email)
                    ->with('rejection_remarks', $approval->remarks ?? null)
                    ->withErrors([
                        'user_Email' => 'Your registration was rejected. Please update and re-register.'
                    ]);
            }

            // No approval record or unknown state will be treated as pending
            throw ValidationException::withMessages([
                'user_Email' => ['Your registration is currently under review.'],
            ]);
        }

        //Authentication failed (wrong credentials)
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
        
        //Redirect to login page after logout
        return redirect()->route('login');
    }
}