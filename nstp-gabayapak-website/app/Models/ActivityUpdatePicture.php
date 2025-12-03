<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected static function booted()
    {
        static::deleting(function (ActivityUpdatePicture $pic) {
            if (!empty($pic->path)) {
                try {
                    Storage::disk('public')->delete($pic->path);
                    logger()->info('Deleted proof picture from storage', ['path' => $pic->path]);
                } catch (\Throwable $e) {
                    logger()->warning('Failed deleting proof picture: ' . $e->getMessage(), ['path' => $pic->path]);
                }
            }
        });
    }
}
