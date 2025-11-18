<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'Project_ID';
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'Project_ID';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Project_Name',
        'Project_Team_Name',
        'Project_Logo',
        'Project_Component',
        'Project_Solution',
        'Project_Goals',
        'Project_Target_Community',
        'Project_Expected_Outcomes',
        'Project_Problems',
        'Project_Status',
        'Project_Section',
        'student_id',
        'student_ids',
        'Project_Rejection_Reason',
        'Project_Rejected_By',
    ];

    /**
     * Get the student that owns the project.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the activities for the project.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'project_id', 'Project_ID');
    }

    /**
     * Staff user who rejected the project (if any).
     */
    public function rejectedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'Project_Rejected_By', 'user_id');
    }

    /**
     * Get the budgets for the project.
     */
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'project_id', 'Project_ID');
    }
    
    /**
     * Get the team members for the project.
     */
    /**
     * Alias for teamMembers() to match Blade usage.
     */
    public function members()
    {
        // Map Student models to simple arrays expected by the form (name, role, email, contact)
        $students = $this->teamMembers();
        $members = [];
        foreach ($students as $student) {
            $user = $student->user ?? null;
            $members[] = [
                'name' => $user->user_Name ?? '',
                'role' => '', // role is not persisted separately; left empty for now
                'email' => $user->user_Email ?? '',
                'contact' => $student->student_contact_number ?? '',
                'student_id' => $student->id ?? null,
            ];
        }

        // Ensure at least one blank member entry (owner) when none found
        if (empty($members)) {
            $owner = $this->student()->with('user')->first();
            $user = $owner?->user ?? null;
            $members[] = [
                'name' => $user->user_Name ?? '',
                'role' => '',
                'email' => $user->user_Email ?? '',
                'contact' => $owner->student_contact_number ?? '',
                'student_id' => $owner->id ?? null,
            ];
        }

        return $members;
    }

    public function teamMembers()
    {
        if ($this->student_ids) {
            $studentIds = json_decode($this->student_ids, true);
            if (is_array($studentIds) && !empty($studentIds)) {
                return Student::whereIn('id', $studentIds)->get();
            }
        }
        // If no student_ids are stored, return only the project owner
        return Student::where('id', $this->student_id)->get();
    }

    /**
     * Return budgets as simple arrays for form rendering.
     * Keys: activity, resources, partners, amount
     *
     * @return array
     */
    public function budgetsArray()
    {
        // Use the existing relationship to fetch Budget models
        $budgets = $this->budgets()->get();
        $result = [];
        foreach ($budgets as $b) {
            $result[] = [
                'activity' => $b->Specific_Activity ?? '',
                'resources' => $b->Resources_Needed ?? '',
                'partners' => $b->Partner_Agencies ?? '',
                'amount' => $b->Amount ?? 0,
            ];
        }

        // If no budgets exist, return a single empty row so form shows one entry
        if (empty($result)) {
            $result[] = [
                'activity' => '',
                'resources' => '',
                'partners' => '',
                'amount' => '',
            ];
        }

        return $result;
    }

    /**
     * Calculate the total budget for the project.
     *
     * @return float
     */
    public function getTotalBudgetAttribute()
    {
        return $this->budgets()->sum('Amount');
    }
}