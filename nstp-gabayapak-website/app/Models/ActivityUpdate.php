<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'Activity_ID');
    }

    public function pictures()
    {
        return $this->hasMany(ActivityUpdatePicture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
