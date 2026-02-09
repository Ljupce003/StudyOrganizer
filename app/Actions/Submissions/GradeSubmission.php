<?php

namespace App\Actions\Submissions;

use App\Models\Submission;

class GradeSubmission
{
    public function execute(Submission $submission, int $graderId, int $grade, ?string $feedback): Submission {
        $submission->grade = $grade;
        $submission->feedback = $feedback;
        $submission->graded_by = $graderId;
        $submission->graded_at = now();

        $submission->save();

        return $submission;
    }
}
