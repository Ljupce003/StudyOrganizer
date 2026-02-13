<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\SubmissionAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmissionAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SubmissionAttachment $submissionAttachment): bool
    {
        if($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::PROFESSOR)){
            return true;
        }

        return $user->id === $submissionAttachment->uploaded_by;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SubmissionAttachment $submissionAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SubmissionAttachment $submissionAttachment,Submission $submission): bool
    {
        if($user->hasRole(UserRole::ADMIN) || $user->hasRole(UserRole::PROFESSOR)){
            return true;
        }

        return $user->id === $submissionAttachment->uploaded_by && !$submission->isGraded();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SubmissionAttachment $submissionAttachment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SubmissionAttachment $submissionAttachment): bool
    {
        return false;
    }
}
