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
        'user_type_id',
        'email',
        'first_name',
        'last_name',
        'password',
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
        'email_verified_at' => 'datetime',
    ];

    //a single user belongs to only one usertype (can belong to either [1, admin], [2, professor], or [3, student])
    public function userType() {
        return $this->belongsTo(UserType::class);
    }

    //a single user can only have one of either (can either be admin/student/prof)
    public function admin() {
        return $this->hasOne(Admin::class);
    }
    public function student() {
        return $this->hasOne(Student::class);
    }
    public function professor() {
        return $this->hasOne(Professor::class);
    }

}
