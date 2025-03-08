<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'logo',
        'description'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function professors()
    {
        return $this->hasMany(Professor::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
