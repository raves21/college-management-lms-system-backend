<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code'
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }

    public function course_contents()
    {
        return $this->hasMany(CourseContent::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

    public function professors()
    {
        return $this->belongsToMany(Professor::class);
    }
}
