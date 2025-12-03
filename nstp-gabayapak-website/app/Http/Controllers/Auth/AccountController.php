<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Show the user account page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('auth.account');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->user_Password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        // Update the password
        $user->changePassword($request->new_password);

        return back()->with('status', 'Password updated successfully!');
    }

    /**
     * Update the user's account information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $rules = [
            'user_Name' => 'required|string|max:255',
        ];
        
        // For students, validate student-specific fields
        if ($user->isStudent()) {
            $rules['student_contact_number'] = 'required|string|max:255';
            $rules['student_course'] = 'required|string|max:255';
            $rules['student_year'] = 'required|integer|min:1|max:4';
            $rules['student_section'] = 'required|string|max:255';
            $rules['student_component'] = 'required|string|in:ROTC,LTS,CWTS';
        }
        
        $request->validate($rules);
        
        // Update user information
        // If staff and in privileged roles, allow updating name and formal picture only
        $privilegedRoles = ['SACSI Director', 'NSTP Coordinator', 'NSTP Program Officer'];

        if ($user->isStaff() && in_array($user->user_role, $privilegedRoles)) {
            // Validate picture if provided
            $request->validate([
                'staff_formal_picture' => 'nullable|image|max:2048',
            ]);

            $user->update([
                'user_Name' => $request->user_Name,
            ]);

            // Handle file upload
            if ($request->hasFile('staff_formal_picture')) {
                $file = $request->file('staff_formal_picture');
                // store new file first
                $path = $file->store('staff_formal_pictures', 'public');

                // delete old file if present
                $old = $user->staff->staff_formal_picture ?? null;

                if ($user->staff) {
                    $user->staff->update(['staff_formal_picture' => $path]);
                } else {
                    $user->staff()->create(['staff_formal_picture' => $path]);
                }

                if (!empty($old) && $old !== $path) {
                    try {
                        Storage::disk('public')->delete($old);
                    } catch (\Throwable $e) {
                        logger()->warning('Failed deleting old staff formal picture: ' . $e->getMessage(), ['old' => $old]);
                    }
                }
            }

            return back()->with('status', 'Account updated successfully!');
        }

        // Default behavior: update user and student profile if applicable
        $user->update([
            'user_Name' => $request->user_Name,
        ]);
        
        // Update profile based on user type
        if ($user->isStudent()) {
            $user->student->update([
                'student_contact_number' => $request->student_contact_number,
                'student_course' => $request->student_course,
                'student_year' => $request->student_year,
                'student_section' => $request->student_section,
                'student_component' => $request->student_component,
            ]);
        }
        
        return back()->with('status', 'Account updated successfully!');
    }

    /**
     * Update the NSTP formators list (for staff with proper role).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFormators(Request $request)
    {
        // In a real application, this would update the formators in the database
        // For now, we'll just redirect back with a success message
        return back()->with('status', 'NSTP Formators updated successfully!');
    }
}