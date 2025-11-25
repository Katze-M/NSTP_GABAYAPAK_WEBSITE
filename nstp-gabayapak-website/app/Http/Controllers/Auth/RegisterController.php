<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Sample data for form options
        $courses = [
            // SMA
            'BSN', 'BSAC', 'BSMA', 'BSAIS', 'BSBA', 'BSBA-ENTRE', 'BSBA-FM', 'BSBA-MM', 'BSOA', 'BSLM', 'BSIA',
            // SLA
            'BA ELS', 'BA COMM', 'BA INDIS', 'BA INTS', 'BA PHILO', 'BS PSYC',
            // CSITE
            'AEET', 'BSBIO', 'BSBME', 'BSCE', 'BSCpE', 'BSCS', 'BSECE', 'BSIT', 'BS MATH', 'BSNMCA', 'BS STAT',
            // SED
            'BSEd', 'BECEd', 'BEED', 'BPEd'
        ];
        sort($courses);
        $roles = ['NSTP Formator', 'NSTP Program Officer', 'SACSI Director', 'NSTP Coordinator'];
        
        // Prefill support: if ?email=... present, load existing user/profile data for editing when rejected
        $prefill = [];
        $email = request()->query('email');
        if ($email) {
            $u = User::where('user_Email', $email)->first();
            if ($u) {
                $prefill['user_Name'] = $u->user_Name;
                $prefill['user_Email'] = $u->user_Email;
                $prefill['user_Type'] = $u->user_Type;
                $prefill['user_role'] = $u->user_role;
                if ($u->isStudent() && $u->student) {
                    $prefill['student_contact_number'] = $u->student->student_contact_number;
                    $prefill['student_course'] = $u->student->student_course;
                    $prefill['student_year'] = $u->student->student_year;
                    $prefill['student_section'] = $u->student->student_section;
                    $prefill['student_component'] = $u->student->student_component;
                }
            }
        }

        return view('auth.register', compact('courses', 'roles', 'prefill'));
    }

    /**
     * Handle a registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Base validation rules
        $rules = [
            'user_Name' => 'required|string|max:255',
            'user_Password' => 'required|string|min:8|confirmed',
            'user_Type' => 'required|in:student,staff',
            'user_role' => 'required_if:user_Type,staff|string|max:255',
        ];

        // Email validation based on user type (we validate format here;
        // uniqueness will be handled manually so rejected users can re-register)
        if ($request->user_Type === 'student') {
            // Students must use ADZU email
            $rules['user_Email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@adzu\.edu\.ph$/'
            ];
        } else {
            // Staff can use ADZU email or Gmail
            $rules['user_Email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@(adzu\.edu\.ph|gmail\.com)$/'
            ];
        }

        $messages = [
            'user_Email.regex' => $request->user_Type === 'student' 
                ? 'The email must be a valid ADZU email address ending with @adzu.edu.ph'
                : 'Staff must use either a valid ADZU email address (@adzu.edu.ph) or Gmail address (@gmail.com)',
            'user_Email.unique' => 'This email address is already registered.',
            'user_Email.email' => 'Please enter a valid email address.',
            'user_Email.max' => 'The email address must not exceed 255 characters.',
            'user_Email.required' => 'Email address is required.'
        ];

        $request->validate($rules, $messages);

        // Check if email already exists
        $existing = User::where('user_Email', $request->user_Email)->first();

        if ($existing) {
            // If existing user has a rejected approval, allow updating their record and create a new pending approval.
            $lastApproval = Approval::where('user_id', $existing->user_id)->latest()->first();
            if ($lastApproval && $lastApproval->status === 'rejected') {
                // update the existing user
                $existing->update([
                    'user_Name' => $request->user_Name,
                    'user_Password' => Hash::make($request->user_Password),
                    'user_Type' => $request->user_Type,
                    'user_role' => $request->user_Type === 'student' ? 'Student' : $request->user_role,
                ]);

                $user = $existing;
            } else {
                return back()->withErrors(['user_Email' => 'This email address is already registered.'])->withInput();
            }
        } else {
            // Create the user
            $user = User::create([
                'user_Name' => $request->user_Name,
                'user_Email' => $request->user_Email,
                'user_Password' => Hash::make($request->user_Password),
                'user_Type' => $request->user_Type,
                'user_role' => $request->user_Type === 'student' ? 'Student' : $request->user_role,
            ]);
        }

        // Create profile based on user type
        if ($request->user_Type === 'student') {
            $request->validate([
                'student_contact_number' => 'required|string|max:255',
                'student_course' => 'required|string|max:255',
                'student_year' => 'required|integer|min:1|max:4',
                'student_section' => 'required|string|max:255',
                'student_component' => 'required|string|in:ROTC,LTS,CWTS',
            ]);

            // Create or update student profile
            Student::updateOrCreate(
                ['user_id' => $user->user_id],
                [
                    'student_contact_number' => $request->student_contact_number,
                    'student_course' => $request->student_course,
                    'student_year' => $request->student_year,
                    'student_section' => $request->student_section,
                    'student_component' => $request->student_component,
                ]
            );
        } else {
            // For staff, validate and handle formal picture upload
            $request->validate([
                'staff_formal_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $existingStaff = Staff::where('user_id', $user->user_id)->first();
            $picturePath = null;
            if ($request->hasFile('staff_formal_picture')) {
                $picturePath = $request->file('staff_formal_picture')->store('staff_pictures', 'public');
            } elseif ($existingStaff && $existingStaff->staff_formal_picture) {
                $picturePath = $existingStaff->staff_formal_picture;
            }

            if (!$picturePath) {
                return back()->withErrors(['staff_formal_picture' => 'Formal picture is required for staff registration.'])->withInput();
            }

            // Create or update staff profile
            Staff::updateOrCreate(
                ['user_id' => $user->user_id],
                ['staff_formal_picture' => $picturePath]
            );
        }

        // Create approval record if necessary
        // SACSI Director is treated as super admin and does not require approval
        if ($user->isStaff() && $user->user_role === 'SACSI Director') {
            $user->approved = true;
            $user->save();
        } else {
            // Create an approval entry (pending)
            Approval::create([
                'user_id' => $user->user_id,
                'type' => $user->isStudent() ? 'student' : 'staff',
                'status' => 'pending',
            ]);
            $user->approved = false;
            $user->save();
        }

        // Redirect / login behavior:
        // - Students: after registering they are NOT logged in; show a pending page informing them their account is under review.
        // - Staff: keep old behavior (log in approved staff or SACSI Director as before)

        // If the user is not approved, show the pending page (no login),
        // but allow SACSI Director through (they are auto-approved earlier).
        if (!$user->approved && !($user->isStaff() && $user->user_role === 'SACSI Director')) {
            return view('auth.registration_pending');
        }

        // For approved users (or SACSI Director), continue logging in
        Auth::login($user);

        if (!$user->approved) {
            session()->flash('registration_status', 'pending');
        }

        if ($user->isStudent()) {
            return redirect()->route('home');
        } else {
            return redirect()->route('dashboard');
        }
    }
}