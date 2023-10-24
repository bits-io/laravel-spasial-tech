<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $with = ['role', 'user_detail'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function user_detail()
    {
        return $this->hasOne(UserDetail::class, 'user_id', 'id');
    }

    // public function getImgKtpAttribute($value)
    // {
    //     if ($value) {
    //         // Add the prefix to the image URL
    //         return 'http://localhost:8000/public/' . $value;
    //     }
    //     return null;
    // }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin()
    {
        if($this->role_id == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isManager()
    {
        if($this->role_id == 2)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function isUser()
    {
        if($this->role_id == 3)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
