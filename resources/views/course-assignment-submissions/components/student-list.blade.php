@if($submissions->isNotEmpty())
    <div class="p-6 space-y-4">
        @foreach($submissions as $submission)
            @include('course-assignment-submissions.components.submission-card', [
                                    'course' => $course,
                                    'assignment' => $assignment,
                                    'submission' => $submission,
                                    ])
        @endforeach
    </div>
@else
    <div class="p-6">
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
            <p class="text-gray-600 font-medium">No submissions yet</p>
            <p class="text-sm text-gray-500 mt-1">
                When you submit, your submission history will appear here.
            </p>
        </div>
    </div>
@endif
