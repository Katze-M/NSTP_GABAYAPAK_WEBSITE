<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Staff;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Student accounts are created by staff via bulk import, not self-registration.
        $roles = ['NSTP Formator', 'NSTP Program Officer', 'SACSI Director', 'NSTP Coordinator'];

        //Prefill support: if ?email=... is present, load existing user/profile data for editing when rejected
        $prefill = [];
        $email = request()->query('email');
        if ($email) {
            $u = User::where('user_Email', $email)->first();
            if ($u) {
                $prefill['user_Name'] = $u->user_Name;
                $prefill['user_Email'] = $u->user_Email;
                $prefill['user_role'] = $u->user_role;
            }
        }

        /* count existing approved staff roles to inform the view and enforce limits. Max Limit: SACSI Director = 1, NSTP Program Officer = 1, NSTP Coordinator = 3
            accounts will be passed down if new staff assumes those roles*/
        $sacsiCount = User::where('user_role', 'SACSI Director')->where('approved', true)->count();
        $programOfficerCount = User::where('user_role', 'NSTP Program Officer')->where('approved', true)->count();
        $coordinatorCount = User::where('user_role', 'NSTP Coordinator')->where('approved', true)->count();

        return view('auth.register', compact('roles', 'prefill', 'sacsiCount', 'programOfficerCount', 'coordinatorCount'));
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
            'user_Password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'user_role' => 'required|string|max:255',
            // Staff can use ADZU email or Gmail
            'user_Email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@(adzu\.edu\.ph|gmail\.com)$/',
            ],
        ];

        $messages = [
            'user_Email.regex' => 'Staff must use either a valid ADZU email address (@adzu.edu.ph) or Gmail address (@gmail.com)',
            'user_Email.unique' => 'This email address is already registered.',
            'user_Email.email' => 'Please enter a valid email address.',
            'user_Email.max' => 'The email address must not exceed 255 characters.',
            'user_Email.required' => 'Email address is required.',
        ];

        $request->validate($rules, $messages);

        // Check if email already exists
        $existing = User::where('user_Email', $request->user_Email)->first();

        // Enforce role limits: only 1 SACSI Director, 1 NSTP Program Officer, max 3 NSTP Coordinator
        $role = $request->user_role;
        /* count only approved users for role limits (unapproved/pending users don't reserve a slot)
        this is just for demo purposes we still kept the roles in Staff Position selection and disabled selection if max limit is reached
        each staff position will be given their own accounts in reality EXCEPT nstp formators*/
        $sacsiCount = User::where('user_role', 'SACSI Director')->where('approved', true)->count();
        $programOfficerCount = User::where('user_role', 'NSTP Program Officer')->where('approved', true)->count();
        $coordinatorCount = User::where('user_role', 'NSTP Coordinator')->where('approved', true)->count();

        $existingHasRole = $existing && $existing->user_role === $role;

        if ($role === 'SACSI Director' && $sacsiCount >= 1 && !$existingHasRole) {
            return back()->withErrors(['user_role' => 'A SACSI Director account already exists.'])->withInput();
        }

        if ($role === 'NSTP Program Officer' && $programOfficerCount >= 1 && !$existingHasRole) {
            return back()->withErrors(['user_role' => 'An NSTP Program Officer account already exists.'])->withInput();
        }

        if ($role === 'NSTP Coordinator' && $coordinatorCount >= 3 && !$existingHasRole) {
            return back()->withErrors(['user_role' => 'There are already 3 NSTP Coordinator accounts.'])->withInput();
        }

        $user = DB::transaction(function () use ($request, $existing) {
            if ($existing) {
                // If existing user has a rejected approval, allow updating their record and create a new pending approval.
                $lastApproval = Approval::where('user_id', $existing->id)->latest()->first();
                if ($lastApproval && $lastApproval->status === 'rejected') {
                    // update the existing user
                    $existing->update([
                        'user_Name' => $request->user_Name,
                        'user_Password' => Hash::make($request->user_Password),
                        'user_Type' => 'staff',
                        'user_role' => $request->user_role,
                    ]);

                    $user = $existing;
                } else {
                    throw ValidationException::withMessages(['user_Email' => 'This email address is already registered.']);
                }
            } else {
                // Create the user
                $user = User::create([
                    'user_Name' => $request->user_Name,
                    'user_Email' => $request->user_Email,
                    'user_Password' => Hash::make($request->user_Password),
                    'user_Type' => 'staff',
                    'user_role' => $request->user_role,
                ]);
            }

            // Validate and handle formal picture upload
            $request->validate([
                'staff_formal_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $existingStaff = Staff::where('user_id', $user->id)->first();
            $picturePath = null;
            if ($request->hasFile('staff_formal_picture')) {
                $picturePath = $request->file('staff_formal_picture')->store('staff_pictures', config('filesystems.default', 's3'));
            } elseif ($existingStaff && $existingStaff->staff_formal_picture) {
                $picturePath = $existingStaff->staff_formal_picture;
            }

            if (!$picturePath) {
                throw ValidationException::withMessages(['staff_formal_picture' => 'Formal picture is required for staff registration.']);
            }

            // Create or update staff profile
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                ['staff_formal_picture' => $picturePath]
            );

            // Create approval record if necessary
            // SACSI Director is treated as super admin and does not require approval
            if ($user->isSACSIDirector()) {
                $user->approved = true;
                $user->save();
            } else {
                // Create an approval entry (pending)
                Approval::create([
                    'user_id' => $user->id,
                    'type' => 'staff',
                    'status' => 'pending',
                ]);
                $user->approved = false;
                $user->save();
            }

            return $user;
        });

        // Only the SACSI Director is automatically logged in after registration (auto-approved).
        // Everyone else sees the pending page/registration success page until a reviewer approves them.
        if (!$user->approved) {
            return view('auth.registration_pending', ['userType' => $user->user_Type ?? null]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}