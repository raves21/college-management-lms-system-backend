<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Course $course)
    {
        switch ($user->user_type_id) {
            case UserType::ADMIN:
                return true;
                //can only view if user is part of the given course
            case UserType::PROFESSOR:
                return $course->professors()->where('user_id', $user->id)->exists();
            case UserType::STUDENT:
                return $course->students()->where('user_id', $user->id)->exists();
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    public function toggleCourseMembers(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    public function attachToDepartments(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user)
    {
        return $user->user_type_id === UserType::ADMIN;
    }
}
