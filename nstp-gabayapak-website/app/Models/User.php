<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Approval;
use App\Models\Project;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_Name',
        'user_Email',
        'user_Password',
        'user_Type',
        'user_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_Password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_Password' => 'hashed',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_Password;
    }

    /**
     * Get the email address for the user.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->user_Email;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Change the password for the user.
     *
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($newPassword)
    {
        $this->user_Password = bcrypt($newPassword);
        return $this->save();
    }

    /**
     * Get the student profile associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the staff profile associated with the user.
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'user_id');
    }

    /**
     * Check if user is a student.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->user_Type === 'student';
    }

    /**
     * Check if user is staff.
     *
     * @return bool
     */
    public function isStaff()
    {
        return $this->user_Type === 'staff';
    }

    /**
     * Check if user is NSTP Formator
     */
    public function isFormator()
    {
        return $this->user_role === 'NSTP Formator';
    }

    /**
     * Check if user is NSTP Coordinator
     */
    public function isCoordinator()
    {
        return $this->user_role === 'NSTP Coordinator';
    }

    /**
     * Check if user is NSTP Program Officer
     */
    public function isProgramOfficer()
    {
        return $this->user_role === 'NSTP Program Officer';
    }

    /**
     * Check if user is SACSI Director
     */
    public function isSACSIDirector()
    {
        return $this->user_role === 'SACSI Director';
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'user_id', 'user_id');
    }

    /**
     * Return an active project that the user owns or is a member of.
     * Blocking statuses: draft, pending, endorsed, approved
     * Returns the first matching Project or null.
     *
     * @return \App\Models\Project|null
     */
    public function activeProject()
    {
        // Only students can have student projects
        if (!$this->isStudent() || empty($this->student) || empty($this->student->id)) {
            return null;
        }

        $sid = $this->student->id;
                // Component-aware rules:
                // - ROTC students: allowed to be tied to multiple projects (do not block creation)
                // - LTS/CWTS students: any project association (owner or member) is blocking
                // - Others: only certain 'active' statuses block creation
                $component = strtoupper(trim($this->student->student_component ?? ''));

                // ROTC students are exempt from the single-project rule: return null so
                // create page won't be blocked by existing associations.
                if ($component === 'ROTC') {
                    return null;
                }

                // For LTS/CWTS students: any project association (owner or member) should
                // be considered blocking — they may only ever be tied to one project
                // regardless of the project's status (including completed/archived).
                if (in_array($component, ['LTS', 'CWTS'])) {
                    return Project::where(function($q) use ($sid) {
                        $q->where('student_id', $sid)
                            // JSON columns may contain numbers or strings depending on how they were written.
                            // Check both numeric and string forms to be robust for legacy data.
                            ->orWhereJsonContains('student_ids', $sid)
                            ->orWhereJsonContains('student_ids', (string) $sid);
                    })->orderByDesc('created_at')->first();
                }

                // Default behavior for other components: only certain 'active' statuses block
                $blocking = ['draft', 'pending', 'endorsed', 'approved'];

                return Project::where(function($q) use ($sid) {
                    $q->where('student_id', $sid)
                        ->orWhereJsonContains('student_ids', $sid)
                        ->orWhereJsonContains('student_ids', (string) $sid);
                })->whereIn('Project_Status', $blocking)
                    ->orderByDesc('created_at')
                    ->first();
    }
}