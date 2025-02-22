<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    //a single user type can be associated to many users (an admin can be either user 1,3,5. student can either be user 43,12,55 etc.)
    public function users() {
        return $this->hasMany(User::class);
    }
}
