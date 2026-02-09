<?php

namespace App\Http\Controllers;

use App\Actions\Submissions\GradeSubmission;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionGradingController extends Controller
{
    //

    public function edit(Course $course, Assignment $assignment, Submission $submission)
    {
        $this->assertCourseAssignmentMatch($course, $assignment);
        $this->assertSubmissionBelongs($assignment, $submission);

        $submission->load(['student', 'grader']);

        return view('submissions.grade', compact('course', 'assignment', 'submission'));
    }

    public function update(
        Request $request,
        Course $course,
        Assignment $assignment,
        Submission $submission,
        GradeSubmission $action
    ) {

        $this->assertCourseAssignmentMatch($course, $assignment);
        $this->assertSubmissionBelongs($assignment, $submission);

        // Make sure it was actually submitted (optional but sensible)
        if (is_null($submission->submitted_at)) {
            abort(403, 'Cannot grade a submission that was not submitted.');
        }

        $validated = $request->validate([
            'grade' => ['required', 'integer', 'min:0', 'max:' . (int) $assignment->max_points],
            'feedback' => ['nullable', 'string', 'max:20000'],
        ]);

        $action->execute(
            $submission,
            auth()->id(),
            (int) $validated['grade'],
            $validated['feedback'] ?? null
        );

        return redirect()->route('course.assignments.show', [$course, $assignment]);
    }



    private function assertCourseAssignmentMatch(Course $course, Assignment $assignment): void
    {
        if ((int) $course->id !== (int) $assignment->course_id) abort(403);
    }

    private function assertSubmissionBelongs(Assignment $assignment, Submission $submission): void
    {
        if ((int) $assignment->id !== (int) $submission->assignment_id) abort(403);
    }


}
