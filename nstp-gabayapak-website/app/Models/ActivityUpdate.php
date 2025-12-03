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
        // Your `users` table uses `user_id` as the primary key, not the default `id`.
        // Specify the owner key so Eloquent can resolve the relation correctly.
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
