<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityUpdatePicture extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_update_id',
        'path',
    ];

    // Relationship accessor: the ActivityUpdate this picture belongs to
    public function activityUpdate()
    {
        return $this->belongsTo(ActivityUpdate::class, 'activity_update_id');
    }
}
