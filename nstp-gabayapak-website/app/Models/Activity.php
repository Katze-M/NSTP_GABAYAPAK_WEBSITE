<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'Activity_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Specific_Activity',
        'Stage',
        'Time_Frame',
        'Point_Persons',
        'status',
        'project_id',
    ];

    /**
     * Get the project that owns the activity.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'Project_ID');
    }

    /**
     * Get the budget for the activity.
     */
    public function budget()
    {
        return $this->hasOne(Budget::class, 'activity_id', 'Activity_ID');
    }
}