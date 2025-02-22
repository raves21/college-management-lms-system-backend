<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name'
    ];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function courseContents() {
        return $this->hasMany(CourseContent::class);
    }

}
