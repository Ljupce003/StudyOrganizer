<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmissionPolicy
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
    public function view(User $user, Submission $submission): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::STUDENT);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Submission $submission): bool
    {
        return $user->hasRole(UserRole::STUDENT) && $submission->student_id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Submission $submission): bool
    {
        return $user->hasRole(UserRole::STUDENT) && $submission->student_id == $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Submission $submission): bool
    {
        return $user->hasRole(UserRole::STUDENT) && $submission->student_id == $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Submission $submission): bool
    {
        return $user->hasRole(UserRole::STUDENT) && $submission->student_id == $user->id;
    }

    public function grade(User $user, Submission $submission): bool
    {
        return $user->hasRole(UserRole::PROFESSOR) && (!$submission->isGraded() || $submission->graded_by == $user->id);
    }
    public function un_grade(User $user, Submission $submission): bool
    {
        if($user->hasRole(UserRole::STUDENT)) return false;


        if($submission->isGraded() && $submission->graded_by === $user->id) {
            return true;
        }
        return false;
    }

}
