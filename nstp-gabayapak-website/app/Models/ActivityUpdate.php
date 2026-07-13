<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function pictures()
    {
        return $this->hasMany(ActivityUpdatePicture::class);
    }

    protected static function booted()
    {
        static::deleting(function (ActivityUpdate $update) {
            try {
                foreach ($update->pictures()->get() as $pic) {
                    $pic->delete();
                }
            } catch (\Throwable $e) {
                Log::warning('Failed deleting activity update pictures: ' . $e->getMessage(), ['update_id' => $update->id ?? null]);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
