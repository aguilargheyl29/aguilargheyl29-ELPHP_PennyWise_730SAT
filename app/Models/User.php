<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'userEmail',
        'userPassword',
        'userFullName',
        'userImage',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'userPassword',
        'remember_token', // Default hidden field for Laravel Auth
    ];

    /**
     * Set password mutator to ensure hashing when creating or updating user passwords.
     *
     * @param string $value
     */
    public function setUserPasswordAttribute($value)
    {
        $this->attributes['userPassword'] = bcrypt($value);
    }

    public function getAuthPassword()
    {
        return $this->userPassword;
    }
}
