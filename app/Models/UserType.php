<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    public const ADMIN = 1;
    public const PROFESSOR = 2;
    public const STUDENT = 3;

    //a single user type can be associated to many users (an admin can be either user 1,3,5. student can either be user 43,12,55 etc.)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
