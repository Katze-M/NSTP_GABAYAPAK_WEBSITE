<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $primaryKey = 'Budget_ID';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Amount' => 'decimal:2',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'Specific_Activity',
        'Resources_Needed',
        'Partner_Agencies',
        'Amount',
        'project_id',
    ];

    /**
     * Get the project that owns the budget.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'Project_ID');
    }


}