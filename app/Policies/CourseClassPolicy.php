<?php

namespace App\Policies;

use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseClassPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseClass  $courseClass
     * @return mixed
     */
    public function view(User $user, CourseClass $courseClass)
    {
        return $user->isCoordinator();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isCoordinator();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseClass  $courseClass
     * @return mixed
     */
    public function update(User $user, CourseClass $courseClass)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseClass  $courseClass
     * @return mixed
     */
    public function delete(User $user, CourseClass $courseClass)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseClass  $courseClass
     * @return mixed
     */
    public function restore(User $user, CourseClass $courseClass)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CourseClass  $courseClass
     * @return mixed
     */
    public function forceDelete(User $user, CourseClass $courseClass)
    {
        //
    }
}
