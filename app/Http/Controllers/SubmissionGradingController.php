<?php

namespace App\Http\Controllers;

use App\Actions\Submissions\GradeSubmission;
use App\Actions\Submissions\UnGradeSubmission;
use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Gate;
use Illuminate\Http\Request;

class SubmissionGradingController extends Controller
{
    //

    public function edit(Course $course, Assignment $assignment, Submission $submission)
    {

        Gate::authorize('grade', $submission);

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


        Gate::authorize('grade', $submission);

        // Make sure it was actually submitted (optional but sensible)
//        if (is_null($submission->submitted_at)) {
//            abort(403, 'Cannot grade a submission that was not submitted.');
//        }

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

    public function destroy(
        Course $course,
        Assignment $assignment,
        Submission $submission,
        UnGradeSubmission $action)
    {

        Gate::authorize("un_grade", $submission);

        $action->execute($submission,auth()->user());

        return redirect()
            ->route('course.assignments.show', [$course, $assignment])
            ->with('status', 'Grade removed.');
    }



}
