<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
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
        $roles = ['NSTP Formator', 'NSTP Program Officer', 'SACSI Director', 'SACSI Admin Staff'];
        
        return view('auth.register', compact('courses', 'roles'));
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

        // Email validation based on user type
        if ($request->user_Type === 'student') {
            // Students must use ADZU email
            $rules['user_Email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,user_Email',
                'regex:/^[a-zA-Z0-9._%+-]+@adzu\.edu\.ph$/'
            ];
        } else {
            // Staff can use ADZU email or Gmail
            $rules['user_Email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,user_Email',
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

        // Create the user
        $user = User::create([
            'user_Name' => $request->user_Name,
            'user_Email' => $request->user_Email,
            'user_Password' => Hash::make($request->user_Password),
            'user_Type' => $request->user_Type,
            'user_role' => $request->user_Type === 'student' ? 'Student' : $request->user_role,
        ]);

        // Create profile based on user type
        if ($request->user_Type === 'student') {
            $request->validate([
                'student_contact_number' => 'required|string|max:255',
                'student_course' => 'required|string|max:255',
                'student_year' => 'required|integer|min:1|max:4',
                'student_section' => 'required|string|max:255',
                'student_component' => 'required|string|in:ROTC,LTS,CWTS',
            ]);

            Student::create([
                'user_id' => $user->user_id,
                'student_contact_number' => $request->student_contact_number,
                'student_course' => $request->student_course,
                'student_year' => $request->student_year,
                'student_section' => $request->student_section,
                'student_component' => $request->student_component,
            ]);
        } else {
            // For staff, validate and handle formal picture upload
            $request->validate([
                'staff_formal_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Handle file upload
            $picturePath = $request->file('staff_formal_picture')->store('staff_pictures', 'public');

            // Create staff record
            Staff::create([
                'user_id' => $user->user_id,
                'staff_formal_picture' => $picturePath,
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Redirect based on user type
        if ($user->isStudent()) {
            return redirect()->route('home');
        } else {
            return redirect()->route('dashboard');
        }
    }
}