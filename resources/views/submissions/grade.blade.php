@php use App\Models\SubmissionAttachment; @endphp
<x-layouts.app-with-sidebar :course="$course">
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <p class="text-sm text-gray-500">{{ $course->short_name ?? $course->code }}</p>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Grade submission
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="text-sm text-gray-500">Student</div>
                <div class="text-lg font-semibold text-gray-900">
                    {{ $submission->student->name ?? 'Unknown' }}
                </div>

                <div class="mt-2 text-sm text-gray-500">
                    Submitted:
                    <span class="text-gray-800 font-medium">
                        {{ $submission->submitted_at?->format('Y-m-d H:i') ?? $submission->created_at->format('Y-m-d H:i') }}
                    </span>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div class="text-xs text-gray-500 mb-1">Submission</div>
                <div class="rounded-md border border-gray-200 p-3 bg-white text-sm text-gray-800">
                    <x-markdown :text="$submission->content" class="prose-sm"/>
                    {{--                    {!! nl2br(e($submission->content ?? '')) !!}--}}
                </div>
            </div>

            {{-- Attachments --}}
            @if($submission->attachments?->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs text-gray-500">Attachments</div>
                            <div class="text-base font-semibold text-gray-900 mt-3">
                                Files
                                <span class="text-sm font-medium text-gray-500">
                    ({{ $submission->attachments?->count() ?? 0 }})
                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-1">
                        {{--                    @if($submission->attachments?->isNotEmpty())--}}
                        <div class="flex flex-col gap-2">
                            @foreach($submission->attachments as $attachment)
                                <div
                                    class="flex items-center justify-between gap-3 border border-gray-200 rounded-md px-3 py-2 bg-white">
                                    <a
                                        href="{{ route('course.assignments.submissions.attachments.fetch',
                                [$course, $assignment, $submission, $attachment, $attachment->original_filename]) }}"
                                        class="text-sm text-gray-800 hover:underline truncate"
                                        title="{{ $attachment->original_filename }}"
                                    >
                                        {{ $attachment->original_filename }}
                                    </a>

                                    <div class="shrink-0 flex items-center gap-2">
                                        @if($attachment->size_bytes)
                                            <span class="text-xs text-gray-500">
                                    {{ number_format($attachment->size_bytes / 1024, 1) }} KB
                                </span>
                                        @endif

                                        @if($attachment->mime_type)
                                            @php
                                                $typeLabel = SubmissionAttachment::typeLabel($attachment);
                                            @endphp
                                            <span
                                                class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700">
                                    {{ $typeLabel }}
                                </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-3 text-xs text-gray-500">
                            Click a file to open/preview.
                        </p>
                        {{--                    @else--}}
                        {{--                        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center">--}}
                        {{--                            <p class="text-gray-600 font-medium">No attachments</p>--}}
                        {{--                            <p class="text-sm text-gray-500 mt-1">--}}
                        {{--                                This submission has no uploaded files.--}}
                        {{--                            </p>--}}
                        {{--                        </div>--}}
                        {{--                    @endif--}}
                    </div>
                </div>
            @endif



            <form
                action="{{ route('course.assignments.submissions.grade.update', [$course, $assignment, $submission]) }}"
                method="POST"
                class="bg-white shadow-sm sm:rounded-lg"
            >
                @csrf
                @method('PUT')

                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500">Grade & feedback</div>

                    <div class="flex items-center gap-2">
                        <a
                            href="{{ route('course.assignments.show', [$course, $assignment]) }}"
                            class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center px-3 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-700"
                        >
                            Save grade
                        </button>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Grade (0–{{ $assignment->max_points }})
                        </label>
                        <input
                            type="number"
                            name="grade"
                            min="0"
                            max="{{ $assignment->max_points }}"
                            value="{{ old('grade', $submission->grade) }}"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                            required
                        >
                        @error('grade') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea
                            id="editor-content"
                            name="feedback"
                            rows="8"
                            class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-500 focus:ring-gray-500"
                        >{{ old('feedback', $submission->feedback) }}</textarea>
                        @error('feedback') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </form>

        </div>
    </div>

</x-layouts.app-with-sidebar>
