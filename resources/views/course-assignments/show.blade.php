<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            {{-- Course short name above title --}}
            <p class="text-sm text-gray-500">
                {{ $course->short_name ?? $course->code }}
            </p>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $assignment->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Assignment details card --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 space-y-6">


                    {{-- Meta grid --}}
                    <div class="rounded-lg border border-gray-200 p-2 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">

                            {{-- Created by --}}
                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Created by</div>
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    {{ $assignment->creator->name ?? 'Unknown' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $assignment->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>

                            {{-- Due --}}
                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Due</div>
                                @if($assignment->due_at)
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $assignment->due_at->format('Y-m-d H:i') }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        No due date
                                    </div>
                                @endif
                            </div>

                            {{-- Max points --}}
                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Max points</div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $assignment->max_points }}
                                </div>
                            </div>

                            {{-- Attempts --}}
                            <div class="rounded-md bg-white border border-gray-200 p-3">
                                <div class="text-xs text-gray-500">Attempts</div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $assignment->number_attempts ?? 'Unlimited' }}
                                </div>
                            </div>

                        </div>
                    </div>


                    <p class="text-gray-500 text-sm"
                       style="margin-top: 0.25rem !important;">{{ $course->short_name }}</p>

                    {{-- Title --}}
                    <div style="margin-top: 0 !important;">
                        {{--                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Title</h3>--}}
                        <div class="prose max-w-none text-gray-800 text-xl">
                            {!! nl2br(e($assignment->title)) !!}
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        {{--                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Description</h3>--}}
                        <div class="prose max-w-none text-gray-800 text-sm">
                            {!! nl2br(e($assignment->description)) !!}
                        </div>
                    </div>

                    {{-- Submit button --}}
                    <div class="flex justify-end">
                        <a
                            {{--                            href="{{ route('course-submissions.create', [$course, $assignment]) }}"--}}
                            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
                        >
                            Add submission
                        </a>
                    </div>

                </div>
            </div>

            {{-- Submissions section --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Your submissions
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Submissions you’ve made for this assignment.
                    </p>
                </div>

                @if($submissions && $submissions->count()>=1)
                    <div class="p-6 space-y-4">
                        @foreach($submissions as $submission)
                            <div class="border border-gray-200 rounded-lg p-5 hover:bg-gray-50 transition">
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                                    {{-- Main content --}}
                                    <div class="lg:col-span-2 space-y-4">

                                        {{-- Top meta (mobile-first) --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div class="text-sm text-gray-500">
                                                Submitted
                                                <span class="text-gray-800 font-medium">{{ $submission->submitted_at?->format('Y-m-d H:i') ?? $submission->created_at?->format('Y-m-d H:i') ?? '' }}</span>
                                            </div>

                                            {{-- Status badge --}}
                                            @if(!is_null($submission->grade))
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-900 text-white">Graded</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Not graded</span>
                                            @endif
                                        </div>

                                        {{-- Submission content --}}
                                        <div>
                                            <div class="text-xs text-gray-500 mb-1">Submission</div>
                                            <div
                                                class="rounded-md border border-gray-200 p-3 bg-white text-sm text-gray-800">
                                                {!! nl2br(e($submission->content)) !!}
                                            </div>
                                        </div>

                                        {{-- Optional feedback --}}
                                        @if(!empty($submission->feedback))
                                            <div>
                                                <div class="text-xs text-gray-500 mb-1">Feedback</div>
                                                <div
                                                    class="rounded-md border border-gray-200 p-3 bg-white text-sm text-gray-800">
                                                    {!! nl2br(e($submission->feedback)) !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Grade sidebar --}}
                                    <aside class="lg:col-span-1">
                                        <div class="rounded-lg border border-gray-200 bg-white p-4 h-full">
                                            @if(!is_null($submission->grade))
                                                <div class="text-xs text-gray-500">Grade</div>
                                                <div class="text-2xl font-semibold text-gray-900">
                                                    {{ $submission->grade }}
                                                    <span
                                                        class="text-sm font-medium text-gray-500">/ {{ $assignment->max_points }}</span>
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
            </div>

        </div>
    </div>
</x-layouts.app-with-sidebar>
