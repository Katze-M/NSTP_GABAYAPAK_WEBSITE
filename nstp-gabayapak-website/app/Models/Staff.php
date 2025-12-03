<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Staff extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'staff_formal_picture',
    ];

    /**
     * Get the user that owns the staff record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Boot model events to clean up files when staff records are deleted.
     */
    protected static function booted()
    {
        static::deleting(function (Staff $staff) {
            if ($staff->staff_formal_picture) {
                try {
                    Storage::disk('public')->delete($staff->staff_formal_picture);
                } catch (\Throwable $e) {
                    // don't break deletion if filesystem fails; just log when available
                    logger()->warning('Failed deleting staff_formal_picture: ' . $e->getMessage(), ['path' => $staff->staff_formal_picture]);
                }
            }
        });
    }
}