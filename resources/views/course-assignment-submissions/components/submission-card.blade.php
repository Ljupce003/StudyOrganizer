@php
    // expects: $course, $assignment, $submission
    $submittedAt = $submission->submitted_at ?? $submission->created_at;
    $isGraded = !is_null($submission->grade);
@endphp

<div class="border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

        {{-- Top meta (left) --}}
        <div class="text-sm text-gray-500">
            Submitted
            <span class="text-gray-800 font-medium">
                {{ $submittedAt?->format('Y-m-d H:i') ?? '' }}
            </span>
        </div>
        @if($submission->updated_at && $submission->updated_at->ne($submittedAt))

            <div class="text-sm text-gray-500">
                Updated
                <span class="text-gray-800 font-medium">
                {{ $submission->updated_at?->format('Y-m-d H:i') ?? '' }}
            </span>
            </div>
        @endif

        {{-- Actions + status (right) --}}
        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
            @if($isGraded)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-900 text-white">
                    Graded
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    Not graded
                </span>
            @endif

            {{-- Edit --}}
            <a
                href="{{ route('course.assignments.submissions.edit', [$course, $assignment, $submission]) }}"
                class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Edit
            </a>

            {{-- Delete --}}
            <form
                action="{{ route('course.assignments.submissions.destroy', [$course, $assignment, $submission]) }}"
                method="POST"
                onsubmit="return confirm('Delete this submission? This cannot be undone.')"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="inline-flex items-center px-3 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50"
                >
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Submission content --}}
            <div>
                <div class="text-xs text-gray-500 mb-1">Submission</div>
                <div class="rounded-md border border-gray-200 p-3 bg-white text-sm text-gray-800">
                    <x-markdown :text="$submission->content" class="prose-sm" />
{{--                    {!! nl2br(e($submission->content ?? '')) !!}--}}
                </div>
            </div>

            {{-- Optional feedback --}}
            @if(!empty($submission->feedback))
                <div>
                    <div class="text-xs text-gray-500 mb-1">Feedback</div>
                    <div class="rounded-md border border-gray-200 p-3 bg-white text-sm text-gray-800">
                        <x-markdown :text="$submission->feedback" class="prose-sm" />
{{--                        {!! nl2br(e($submission->feedback)) !!}--}}
                    </div>
                </div>
            @endif
        </div>

        {{-- Grade sidebar --}}
        <aside class="lg:col-span-1">
            <div class="rounded-lg border border-gray-200 bg-white p-4 h-full">
                @if($isGraded)
                    <div class="text-xs text-gray-500">Grade</div>
                    <div class="text-2xl font-semibold text-gray-900">
                        {{ $submission->grade }}
                        <span class="text-sm font-medium text-gray-500">/ {{ $assignment->max_points }}</span>
                    </div>

                    <div class="mt-3 text-xs text-gray-500 space-y-1">
                        <div>
                            Graded by
                            <span class="text-gray-800 font-medium">
                                {{ $submission->grader->name ?? 'Unknown' }}
                            </span>
                        </div>

                        @if($submission->graded_at)
                            <div>
                                {{ $submission->graded_at->format('Y-m-d H:i') }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-xs text-gray-500">Status</div>
                    <div class="text-sm font-medium text-gray-900 mt-1">
                        Not graded yet
                    </div>

                    <div class="text-xs text-gray-500 mt-3">
                        Your submission is awaiting review.
                    </div>
                @endif
            </div>
        </aside>
    </div>
</div>
