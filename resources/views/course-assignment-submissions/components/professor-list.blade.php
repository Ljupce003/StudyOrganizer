@if($submissions->isNotEmpty())
    <div class="p-6 space-y-5">

        @foreach($submissions as $studentId => $studentSubs)
            @php
                $student = $studentSubs->first()->student;
                $total = $studentSubs->count();
            @endphp

            <div class="border border-gray-200 rounded-lg p-5 bg-white">
                {{-- Student header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">
                            {{ $student->name ?? 'Unknown student' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $student->email ?? '' }}
                            <span class="mx-1">·</span>
                            {{ $total }} submission{{ $total === 1 ? '' : 's' }}
                        </div>
                    </div>

                    {{-- (Optional) overall status badge --}}
                    @php
                        $latest = $studentSubs->sortByDesc(fn($s) => $s->submitted_at ?? $s->created_at)->first();
                        $latestGraded = $latest && (!is_null($latest->grade) || !is_null($latest->graded_at));
                    @endphp

                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $latestGraded ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700' }}">
                        Latest: {{ $latestGraded ? 'Graded' : 'Not graded' }}
                    </span>
                </div>

                {{-- Submissions list --}}
                <div class="mt-4 space-y-2">
                    @php
                        $total_attempts = $studentSubs->count()
                    @endphp

                    @foreach($studentSubs->values() as $idx => $sub)
                        @php
                            // $attempt = $idx + 1;
                            $attempt = $total_attempts - $idx;
                            $submittedAt = $sub->submitted_at ?? $sub->created_at;
                            $isGraded = !is_null($sub->grade) || !is_null($sub->graded_at);
                        @endphp

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

                                <div class="text-sm text-gray-700">
                                    <span class="font-medium text-gray-900">Attempt {{ $attempt }}</span>
                                    <span class="text-gray-400 mx-2">·</span>
                                    <span class="text-gray-600">
                                        {{ $submittedAt?->format('Y-m-d H:i') ?? '' }}
                                    </span>

                                    <span class="text-gray-400 mx-2">·</span>

                                    @if($isGraded)
                                        <span class="text-gray-900 font-medium">
                                            {{ $sub->grade }} / {{ $assignment->max_points }}
                                        </span>
                                    @else
                                        <span class="text-gray-600">Not graded</span>
                                    @endif

                                    @if($isGraded && $sub->grader)
                                        <span class="text-gray-400 mx-2">·</span>
                                        <span class="text-gray-600 text-xs">
                                            by {{ $sub->grader->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 sm:justify-end">
                                    <a
                                        href="{{ route('course.assignments.submissions.grade.edit', [$course, $assignment, $sub]) }}"
                                        class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        {{ $isGraded ? 'Edit grade' : 'Grade' }}
                                    </a>
                                </div>
                            </div>

                            {{-- Feedback preview (optional, small) --}}
                            @if(!empty($sub->feedback))
                                <div class="mt-2 text-xs text-gray-600">
                                    <span class="text-gray-500">Feedback:</span>
                                    <x-markdown :text="\Illuminate\Support\Str::limit(strip_tags($sub->feedback), 140)" class="prose prose-sm max-w-none" />
{{--                                    {{ \Illuminate\Support\Str::limit(strip_tags($sub->feedback), 140) }}--}}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>
@else
    <div class="p-6">
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">
            <p class="text-gray-600 font-medium">No submissions yet</p>
            <p class="text-sm text-gray-500 mt-1">
                When students submit, they’ll appear here grouped by student.
            </p>
        </div>
    </div>
@endif
