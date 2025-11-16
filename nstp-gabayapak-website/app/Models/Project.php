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
     * Get the budgets for the project through activities.
     */
    public function budgets()
    {
        return $this->hasManyThrough(Budget::class, Activity::class, 'project_id', 'activity_id', 'Project_ID', 'Activity_ID');
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