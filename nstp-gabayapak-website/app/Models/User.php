<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_Name',
        'user_Email',
        'user_Password',
        'user_Type',
        'user_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_Password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_Password' => 'hashed',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_Password;
    }

    /**
     * Get the email address for the user.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->user_Email;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Change the password for the user.
     *
     * @param string $newPassword
     * @return bool
     */
    public function changePassword($newPassword)
    {
        $this->user_Password = bcrypt($newPassword);
        return $this->save();
    }

    /**
     * Get the student profile associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the staff profile associated with the user.
     */
    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id', 'user_id');
    }

    /**
     * Check if user is a student.
     *
     * @return bool
     */
    public function isStudent()
    {
        return $this->user_Type === 'student';
    }

    /**
     * Check if user is staff.
     *
     * @return bool
     */
    public function isStaff()
    {
        return $this->user_Type === 'staff';
    }

    /**
     * Check if user is an NSTP Formator.
     *
     * @return bool
     */
    public function isNstpFormator()
    {
        return $this->user_role === 'NSTP Formator';
    }
}