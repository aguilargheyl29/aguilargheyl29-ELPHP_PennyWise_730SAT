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

    protected $primaryKey = 'userID'; 

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
        'email_verified_at',  // Adding email_verified_at here
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
     * Automatically hash the password whenever it is set or updated.
     *
     * @param string $value
     */
    public function setUserPasswordAttribute($value)
    {
        $this->attributes['userPassword'] = bcrypt($value);
    }

    /**
     * Override the default getAuthPassword method to use `userPassword`.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->userPassword;
    }
}
