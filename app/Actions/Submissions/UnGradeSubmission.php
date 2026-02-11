<?php

namespace App\Actions\Submissions;

use App\Models\Submission;
use App\Models\User;

class UnGradeSubmission
{
    public function execute(Submission $submission,User $actor): Submission
    {
        $submission->grade = null;
        $submission->feedback = null;
        $submission->graded_by = null;
        $submission->graded_at = null;

        $submission->save();

        return $submission;

    }
}
