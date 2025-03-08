<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Professor;
use App\Models\Student;
use App\Policies\CoursePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Course::class => CoursePolicy::class,
        Department::class => DepartmentPolicy::class,
        Professor::class => ProfessorPolicy::class,
        Student::class => StudentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
