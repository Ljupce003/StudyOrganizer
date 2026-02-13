<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CourseMaterial;
use App\Models\User;
use DB;
use Illuminate\Auth\Access\Response;

class CourseMaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseMaterial $courseMaterial): bool
    {
        if ($user->hasRole(UserRole::STUDENT)) {

            return (bool) $courseMaterial->is_published;
        }

        return $user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseMaterial $courseMaterial): bool
    {
        return ($user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseMaterial $courseMaterial): bool
    {
        return ($user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseMaterial $courseMaterial): bool
    {
        return ($user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseMaterial $courseMaterial): bool
    {
        return ($user->hasRole(UserRole::PROFESSOR) || $user->hasRole(UserRole::ADMIN));
    }

}
