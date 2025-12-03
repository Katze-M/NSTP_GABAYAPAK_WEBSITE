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
        'Implementation_Date',
        'Point_Persons',
        'status',
        'project_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Implementation_Date' => 'date',
    ];

    /**
     * Get the project that owns the activity.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'Project_ID');
    }

    /**
     * Activity update history (status changes and attached proof pictures)
     */
    public function updates()
    {
        return $this->hasMany(ActivityUpdate::class, 'activity_id', 'Activity_ID');
    }


}