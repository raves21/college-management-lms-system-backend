<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title'
    ];

    public function course() {
        return $this->belongsTo(Course::class);
    }

    public function courseContentFiles(){
        return $this->hasMany(CourseContentFile::class);
    }
}
