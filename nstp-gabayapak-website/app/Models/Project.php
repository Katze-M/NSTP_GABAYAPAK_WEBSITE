<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        'member_roles',
        'Project_Rejection_Reason',
        'Project_Rejected_By',
        'Project_Approved_By',
        'is_resubmission',
        'previous_rejection_reasons',
        'resubmission_count',
            'endorsed_by',
            'mark_as_completed_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'student_ids' => 'array',
        'member_roles' => 'array',
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
     * Staff user who approved the project (if any).
     */
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'Project_Approved_By', 'user_id');
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
     * This returns a proper relationship instance.
     */
    public function teamMembersRelation()
    {
        if ($this->student_ids && is_array($this->student_ids) && !empty($this->student_ids)) {
            return $this->hasManyThrough(Student::class, 'students', 'id', 'id', 'Project_ID', 'id')
                       ->whereIn('students.id', $this->student_ids);
        }
        // If no student_ids are stored, return only the project owner
        return $this->hasOne(Student::class, 'id', 'student_id');
    }

    /**
     * Get team members as a collection (for backward compatibility).
     */
    public function getTeamMembersAttribute()
    {
        if ($this->student_ids && is_array($this->student_ids) && !empty($this->student_ids)) {
            // Get students with their user relationship loaded
            $students = Student::with('user')->whereIn('id', $this->student_ids)->get();
            
            // If no students found, fall back to project owner
            if ($students->isEmpty()) {
                return Student::with('user')->where('id', $this->student_id)->get();
            }
            
            return $students;
        }
        // If no student_ids are stored, return only the project owner
        return Student::with('user')->where('id', $this->student_id)->get();
    }

    /**
     * Alias for teamMembers() to match Blade usage.
     */
    public function members()
    {
        // Map Student models to simple arrays expected by the form (name, role, email, contact)
        $students = $this->teamMembers;
        $members = [];
        
        // Get stored member roles if they exist
        $memberRoles = $this->member_roles && is_array($this->member_roles) ? $this->member_roles : [];
        
        foreach ($students as $student) {
            $user = $student->user ?? null;
            // Use stored role if it exists, otherwise use empty string
            $role = $memberRoles[$student->id] ?? '';
            $members[] = [
                'name' => $user->user_Name ?? '',
                'role' => $role,
                'email' => $user->user_Email ?? '',
                'contact' => $student->student_contact_number ?? '',
                'student_id' => $student->id ?? null,
            ];
        }

        // Ensure at least one blank member entry (owner) when none found
        if (empty($members)) {
            $owner = $this->student()->with('user')->first();
            $user = $owner?->user ?? null;
            // Use stored role for owner if it exists, otherwise use empty string
            $ownerRole = $memberRoles[$owner->id] ?? '';
            $members[] = [
                'name' => $user->user_Name ?? '',
                'role' => $ownerRole,
                'email' => $user->user_Email ?? '',
                'contact' => $owner->student_contact_number ?? '',
                'student_id' => $owner->id ?? null,
            ];
        }

        // Sort members to put Leaders/Project Leaders first
        usort($members, function($a, $b) {
            $aRole = strtolower(trim($a['role'] ?? ''));
            $bRole = strtolower(trim($b['role'] ?? ''));
            
            // Check if role contains "leader" or "project leader"
            $aIsLeader = strpos($aRole, 'leader') !== false || strpos($aRole, 'project leader') !== false;
            $bIsLeader = strpos($bRole, 'leader') !== false || strpos($bRole, 'project leader') !== false;
            
            // If both are leaders or both are not leaders, maintain original order
            if ($aIsLeader === $bIsLeader) {
                return 0;
            }
            
            // Put leaders first
            return $aIsLeader ? -1 : 1;
        });

        return $members;
    }

    /**
     * Get team members as collection (deprecated - use teamMembers attribute instead).
     */
    public function teamMembers()
    {
        if ($this->student_ids && is_array($this->student_ids) && !empty($this->student_ids)) {
            return Student::whereIn('id', $this->student_ids)->get();
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

    /**
     * Get the user who endorsed the project.
     */
    public function endorsedBy()
    {
        return $this->belongsTo(User::class, 'endorsed_by', 'user_id');
    }

    /**
     * Get the user who marked the project as completed.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'mark_as_completed_by', 'user_id');
    }

    /**
     * Model events: clean up Project_Logo when changed/deleted and cascade-delete activities.
     */
    protected static function booted()
    {
        // When Project_Logo changes during update, delete the previous file
        static::updating(function (Project $project) {
            if ($project->isDirty('Project_Logo')) {
                $old = $project->getOriginal('Project_Logo');
                $new = $project->Project_Logo;
                if (!empty($old) && $old !== $new) {
                    try {
                        Storage::disk('public')->delete($old);
                        Log::info('Deleted old project logo', ['path' => $old, 'project' => $project->Project_ID ?? null]);
                    } catch (\Throwable $e) {
                        Log::warning('Failed deleting old project logo: ' . $e->getMessage(), ['path' => $old]);
                    }
                }
            }
        });

        // On project deletion, remove logo and cascade delete activities (and their media)
        static::deleting(function (Project $project) {
            try {
                if (!empty($project->Project_Logo)) {
                    Storage::disk('public')->delete($project->Project_Logo);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed deleting project logo: ' . $e->getMessage(), ['project' => $project->Project_ID ?? null]);
            }

            try {
                // Delete related activities to ensure their updates/pictures are removed via model events
                foreach ($project->activities()->get() as $activity) {
                    $activity->delete();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed deleting project activities on project delete: ' . $e->getMessage(), ['project' => $project->Project_ID ?? null]);
            }
        });
    }
}