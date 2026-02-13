<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\SubmissionAttachment;
use Gate;
use Illuminate\Http\Request;

class SubmissionAttachmentController extends Controller
{
    //

    public function fetch(
        Course $course,
        Assignment $assignment,
        Submission $submission,
        SubmissionAttachment $attachment
    ) {

        Gate::authorize('view', $attachment);

        return $attachment->attachmentResponse();

    }

    public function destroy(
        Course $course,
        Assignment $assignment,
        Submission $submission,
        SubmissionAttachment $attachment
    )
    {


        Gate::authorize('delete', [$attachment,$submission]);

        $attachment->storage()->delete($attachment->storage_path);

        $attachment->delete();

        return back()->with('status','Attachment removed.');

    }
}
